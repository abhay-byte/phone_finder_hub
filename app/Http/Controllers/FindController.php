<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use App\Models\Chat;
use App\Models\ChatMessage;
use App\Services\SEO\SeoManager;
use App\Services\SEO\SEOData;

class FindController extends Controller
{
    public function index(SeoManager $seo)
    {
        $chats = [];
        if (auth()->check()) {
            $chats = Chat::where('user_id', auth()->id())->orderBy('updated_at', 'desc')->get();
        }

        $seo->set(new SEOData(
            title: 'AI Phone Finder | PhoneFinderHub',
            description: 'Find your perfect smartphone using our AI assistant. Get personalized recommendations based on our expert database.',
            url: route('find.index'),
        ));

        $allPhoneNames = \App\Models\Phone::pluck('name')->values()->toArray();

        return view('find.index', compact('chats', 'allPhoneNames'));
    }

    /**
     * Extract budget from the full conversation context (current message + recent history).
     * Returns INR amount or null if no budget mentioned.
     */
    protected function extractBudget(string $currentMessage, array $history): ?int
    {
        // Combine recent history + current message text for budget detection
        $recent = collect($history)->filter(fn($m) => ($m['role'] ?? '') === 'user')
            ->values()->last()['content'] ?? '';
        $text = strtolower($recent . ' ' . $currentMessage);

        // Remove commas for easier parsing
        $text = str_replace(',', '', $text);

        // Match patterns like: under 45000, under ₹45k, below 45000, budget 45k, ≤45000
        $patterns = [
            '/(?:under|below|within|max|upto|up to|≤|less than)\s*[₹rs.]?\s*(\d+)\s*k\b/u',
            '/(?:under|below|within|max|upto|up to|≤|less than)\s*[₹rs.]?\s*(\d{4,6})/u',
            '/[₹rs.]?\s*(\d+)\s*k\s*(?:budget|range|price|phone)/u',
            '/budget\s*(?:of\s*)?[₹rs.]?\s*(\d+)\s*k\b/u',
            '/budget\s*(?:of\s*)?[₹rs.]?\s*(\d{4,6})/u',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $text, $m)) {
                $val = (int) $m[1];
                // If the number looks like it's in "k" suffix form (e.g. 45 → 45000)
                if ($val < 1000) {
                    $val *= 1000;
                }
                return $val;
            }
        }

        return null;
    }

    /**
     * Detect user priority from the current message.
     * Returns one of: camera, gaming, battery, value, experience, overall
     */
    protected function detectPriority(string $message): string
    {
        $msg = strtolower($message);

        if (preg_match('/\b(camera|photo|photography|selfie|portrait|zoom|video)\b/', $msg)) return 'camera';
        if (preg_match('/\b(gaming|game|gpu|fps|gfx|graphic|3dmark|geekbench)\b/', $msg)) return 'gaming';
        if (preg_match('/\b(battery|endurance|backup|life|charging|mah)\b/', $msg)) return 'battery';
        if (preg_match('/\b(cheap|value|budget|bang|affordable|price)\b/', $msg)) return 'value';
        if (preg_match('/\b(experience|smooth|ui|performance|daily)\b/', $msg)) return 'experience';

        return 'overall';
    }

    public function chat(Request $request)
    {
        $request->validate([
            'message'  => 'required|string',
            'chat_id'  => 'nullable|exists:chats,id',
            'history'  => 'nullable|array',
        ]);

        $userMessage = $request->message;
        $chatId      = $request->chat_id;
        $history     = $request->history ?? [];

        $apiKey = config('services.nvidia.api_key');
        if (!$apiKey) {
            return response()->json(['error' => 'NVIDIA API Key is missing. Please configure NVIDIA_API_KEY in .env'], 500);
        }

        try {
            // ── Step 1: Detect budget from CURRENT message (not full history) ───
            $budget = $this->extractBudget($userMessage, $history);

            // ── Step 2: Detect priority from current message ─────────────────────
            $priority = $this->detectPriority($userMessage);

            // ── Step 3: Build query – apply server-side budget filter ────────────
            $query = \App\Models\Phone::with(['platform', 'battery', 'benchmarks', 'body', 'connectivity']);

            if ($budget) {
                // Allow a small overshoot (5%) so ₹42,450 phones show for ₹45k
                $query->where('price', '<=', $budget * 1.05);
            }

            // Sort by the detected priority score
            $orderMap = [
                'camera'     => 'cms_score',
                'gaming'     => 'gpx_score',
                'battery'    => 'endurance_score',
                'value'      => 'value_score',
                'experience' => 'ueps_score',
                'overall'    => 'overall_score',
            ];
            $query->orderBy($orderMap[$priority] ?? 'overall_score', 'desc');

            // Limit context to top 15 by priority to keep prompt focused
            $filteredPhones = $query->limit(15)->get();

            // ── Step 4: Build compact context ────────────────────────────────────
            $phoneLines = [];
            $cardLines  = [];

            foreach ($filteredPhones as $rank => $phone) {
                $imageUrl = trim($phone->image_url ?? '');
                if ($imageUrl && !str_starts_with($imageUrl, 'http') && !str_starts_with($imageUrl, '/')) {
                    $imageUrl = '/storage/' . ltrim($imageUrl, '/');
                }

                $chipset      = trim($phone->platform->chipset ?? '?');
                $batteryType  = trim($phone->battery->battery_type ?? '?');
                $ram          = trim($phone->platform->ram ?? '');
                $storage      = trim($phone->platform->internal_storage ?? '');
                $displaySize  = trim($phone->body->display_size ?? '');
                $displayType  = trim($phone->body->display_type ?? '');
                $wiredCharging = trim($phone->battery->charging_wired ?? '');
                $nfc          = trim($phone->connectivity->nfc ?? '');
                $jack         = trim($phone->connectivity->jack_3_5mm ?? '');
                $endurance    = $phone->endurance_score ?? 0;

                // Compact spec line: all relevant facts in one line
                $specLine = "{$chipset}";
                if ($ram)          $specLine .= " | RAM:{$ram}";
                if ($storage)      $specLine .= " | Storage:{$storage}";
                if ($displaySize)  $specLine .= " | Display:{$displaySize}";
                if ($displayType && strlen($displayType) < 60) $specLine .= " ({$displayType})";
                $specLine .= " | {$batteryType}";
                if ($wiredCharging) $specLine .= " {$wiredCharging}";
                if ($nfc && stripos($nfc, 'yes') !== false) $specLine .= " | NFC";
                if ($jack && stripos($jack, 'no') === false)  $specLine .= " | 3.5mm";

                $phoneLines[] = ($rank + 1) . ". {$phone->name} | ₹" . number_format($phone->price ?? 0) .
                    " | Overall:{$phone->overall_score} Expert:{$phone->expert_score} Value:{$phone->value_score}" .
                    " | UEPS:{$phone->ueps_score} CMS:{$phone->cms_score} GPX:{$phone->gpx_score} END:{$endurance}" .
                    "\n   SPECS: {$specLine}";

                $cardLines[] = "[CARD|" . trim($phone->name) . "|₹" . number_format($phone->price ?? 0, 0, '.', ',') .
                    "|" . $imageUrl . "|/phones/" . $phone->id .
                    "|" . $chipset . "|" . $batteryType .
                    "|" . trim($phone->amazon_url ?? '') . "|" . trim($phone->flipkart_url ?? '') . "]";
            }

            $phoneDatabase = implode("\n", $phoneLines);
            $cardLookup    = implode("\n", $cardLines);

            // ── Step 5: Build top ranklists ──────────────────────────────────────
            $budgetClause = $budget ? "where price <= " . intval($budget * 1.05) : "";
            $topCms  = DB::select("SELECT name FROM phones {$budgetClause} ORDER BY cms_score  DESC LIMIT 5");
            $topGpx  = DB::select("SELECT name FROM phones {$budgetClause} ORDER BY gpx_score  DESC LIMIT 5");
            $topUeps = DB::select("SELECT name FROM phones {$budgetClause} ORDER BY ueps_score DESC LIMIT 5");
            $topEnd  = DB::select("SELECT name FROM phones {$budgetClause} ORDER BY endurance_score DESC LIMIT 5");

            $fmt = fn($rows) => implode(', ', array_column($rows, 'name'));

            $budgetLabel = $budget ? " (within ₹" . number_format($budget) . " budget)" : " (no budget filter)";
            $ranklistContext = "TOP RANKINGS{$budgetLabel}:\n" .
                "- Best Camera (CMS): "   . $fmt($topCms)  . "\n" .
                "- Best Gaming (GPX): "   . $fmt($topGpx)  . "\n" .
                "- Best Experience(UEPS):" . $fmt($topUeps) . "\n" .
                "- Best Endurance: "       . $fmt($topEnd);

            // ── Step 6: Build system prompt ───────────────────────────────────────
            $priorityInstruction = match($priority) {
                'camera'     => "The user is asking about CAMERA. Rank primarily by Camera Mastery Score (CMS). The list below is already sorted by CMS descending.",
                'gaming'     => "The user is asking about GAMING. Rank primarily by Gaming Performance Index (GPX). The list below is already sorted by GPX descending.",
                'battery'    => "The user is asking about BATTERY/ENDURANCE. Rank primarily by Endurance score. The list below is already sorted by Endurance descending.",
                'value'      => "The user is asking about VALUE/BUDGET. Rank primarily by Value Score. The list below is already sorted by Value descending.",
                'experience' => "The user is asking about USER EXPERIENCE. Rank primarily by UEPS. The list below is already sorted by UEPS descending.",
                default      => "The user is asking a general question. Use Overall Score as primary ranking.",
            };

            $budgetInstruction = $budget
                ? "⚠️ STRICT BUDGET: The user specified a budget of ₹" . number_format($budget) . ". ALL phones below are already filtered within this budget. Do NOT recommend any phone with a higher price."
                : "No budget constraint detected. Recommend based on priority.";

            $systemPrompt = <<<PROMPT
You are PhoneFinder AI, a concise phone consultant for PhoneFinderHub.

⚠️ ABSOLUTE RULES:
- ONLY recommend phones from the PHONES list below. NEVER invent a phone.
- ONLY use spec data listed below for each phone. Do NOT invent specs like RAM, camera MP, display details, or any detail NOT listed.
- ONLY use the exact [CARD|...] strings from CARDS below. NEVER fabricate card strings.
- ALWAYS base your answer on the CURRENT user message and the sorted list below — do NOT let previous recommendations influence your current answer.

{$budgetInstruction}
{$priorityInstruction}

PHONES (sorted by {$priority} priority):
{$phoneDatabase}

CARDS (copy-paste exactly — never modify):
{$cardLookup}

SCORES GUIDE (use full names when speaking to user):
Overall Score (max ~60), Expert Score (max ~80), Value Score (bang-for-buck),
UEPS = User Experience Score (max 255), CMS = Camera Mastery Score (max ~1330),
GPX = Gaming Performance Index (max ~300), END = Endurance Score (max ~160).

{$ranklistContext}

KNOWLEDGE (mention ONLY if user asks):
- Turnip: Open-source GPU drivers for game emulation. ONLY works on Snapdragon/Adreno GPUs. Mediatek/Exynos = NO Turnip.
- Bootloader: OnePlus/Realme/Xiaomi/Motorola usually allow, Samsung/Apple block it.

RESPONSE RULES:
1. Output the [CARD|...] for each recommended phone. Never list phones as plain text.
2. Put each [CARD|...] on its own line with a blank line between them.
3. After all cards, write 2-4 natural sentences comparing the phones by the relevant priority.
4. NEVER dump raw score strings (e.g. never write "OS:17.8 ES:45.55..."). Instead say "It has a Camera Mastery Score of 853."
5. Use full score names (Camera Mastery Score, Gaming Performance Index, etc.), never abbreviations.
6. If user asks to compare specific phones, show their CARDs then compare specs/scores.
7. When asked about specs, quote ONLY the SPECS listed in PHONES above for that phone.
8. If info isn't listed, say "I don't have that detail in our database."
PROMPT;

            // ── Step 7: Build messages — strip [CARD|...] blobs from history ──────
            $messages = [];
            $messages[] = ['role' => 'system', 'content' => $systemPrompt];

            // Only send last 12 history messages (6 exchanges); strip card strings from assistant messages
            $historySlice = array_slice($history, -12);
            foreach ($historySlice as $msg) {
                if (!isset($msg['role'], $msg['content']) || empty(trim($msg['content']))) continue;

                $content = $msg['content'];
                // Strip [CARD|...] blobs from assistant messages so they don't pollute the context
                if ($msg['role'] === 'assistant') {
                    $content = preg_replace('/\[CARD\|[^\]]+\]/s', '[phone card shown to user]', $content);
                    $content = trim($content);
                    if (empty($content)) continue;
                }

                $messages[] = ['role' => $msg['role'], 'content' => $content];
            }

            $messages[] = ['role' => 'user', 'content' => $userMessage];

            \Log::info("FindController: priority={$priority} budget=" . ($budget ?? 'none') . " phones=" . $filteredPhones->count() . " history=" . count($historySlice));

            return response()->stream(function () use ($apiKey, $messages, $userMessage, $chatId) {
                $title = null;

                // Process Chat Title & DB Before Streaming
                if (auth()->check()) {
                    if (!$chatId) {
                        $titleResp = Http::timeout(30)->withHeaders([
                            'Authorization' => 'Bearer ' . $apiKey,
                            'Content-Type'  => 'application/json',
                        ])->post("https://integrate.api.nvidia.com/v1/chat/completions", [
                            'model'       => 'meta/llama-3.1-8b-instruct',
                            'messages'    => [
                                ['role' => 'user', 'content' => "Generate a short 3-5 word title for a chat that starts with this message. Return ONLY the title string, no quotes. Message: \"{$userMessage}\""]
                            ],
                            'temperature' => 0.3,
                            'max_tokens'  => 20,
                        ]);
                        $title = 'New Chat';
                        if ($titleResp->successful() && isset($titleResp->json()['choices'][0]['message']['content'])) {
                            $title = trim($titleResp->json()['choices'][0]['message']['content'], " \t\n\r\0\x0B\"");
                        }

                        $chat   = Chat::create(['user_id' => auth()->id(), 'title' => $title]);
                        $chatId = $chat->id;
                    } else {
                        $chat  = Chat::findOrFail($chatId);
                        $chat->touch();
                        $title = $chat->title;
                    }

                    ChatMessage::create(['chat_id' => $chatId, 'role' => 'user', 'content' => $userMessage]);
                }

                echo "data: " . json_encode(['type' => 'meta', 'chat_id' => $chatId, 'title' => $title]) . "\n\n";
                if (ob_get_level() > 0) ob_flush();
                flush();

                $client = new \GuzzleHttp\Client();

                $makeRequest = function (string $model) use ($client, $apiKey, $messages) {
                    return $client->request('POST', 'https://integrate.api.nvidia.com/v1/chat/completions', [
                        'headers' => [
                            'Authorization' => 'Bearer ' . $apiKey,
                            'Content-Type'  => 'application/json',
                        ],
                        'json' => [
                            'model'       => $model,
                            'messages'    => $messages,
                            'temperature' => 0.4,
                            'top_p'       => 0.9,
                            'max_tokens'  => 2048,
                            'stream'      => true,
                        ],
                        'stream' => true,
                    ]);
                };

                try {
                    // Primary: llama-3.1-70b — best quality, confirmed working on this NVIDIA account
                    // Fallback: llama-3.1-8b  — ultra-fast, also confirmed working
                    try {
                        $response = $makeRequest('meta/llama-3.1-70b-instruct');
                    } catch (\Exception $primaryEx) {
                        \Log::warning('Primary model failed, trying fallback: ' . $primaryEx->getMessage());
                        $response = $makeRequest('meta/llama-3.1-8b-instruct');
                    }

                    $body             = $response->getBody();
                    $assistantMessage = '';

                    while (!$body->eof()) {
                        $line = \GuzzleHttp\Psr7\Utils::readLine($body);
                        if (!str_starts_with($line, 'data: ')) continue;

                        $data = substr($line, 6);
                        if (trim($data) === '[DONE]') break;

                        $json = json_decode($data, true);
                        if (isset($json['choices'][0]['delta']['content'])) {
                            $token = $json['choices'][0]['delta']['content'];
                            $assistantMessage .= $token;
                            echo "data: " . json_encode(['type' => 'chunk', 'content' => $token]) . "\n\n";
                            if (ob_get_level() > 0) ob_flush();
                            flush();
                        }
                    }

                    if (auth()->check() && $chatId && !empty(trim($assistantMessage))) {
                        ChatMessage::create(['chat_id' => $chatId, 'role' => 'assistant', 'content' => $assistantMessage]);
                    }

                    echo "data: " . json_encode(['type' => 'done']) . "\n\n";
                    if (ob_get_level() > 0) ob_flush();
                    flush();

                } catch (\Exception $e) {
                    \Log::error('Stream Error: ' . $e->getMessage());
                    echo "data: " . json_encode(['type' => 'error', 'message' => 'AI service unavailable. Please try again.']) . "\n\n";
                    if (ob_get_level() > 0) ob_flush();
                    flush();
                }

            }, 200, [
                'Cache-Control'    => 'no-cache',
                'Content-Type'     => 'text/event-stream',
                'X-Accel-Buffering' => 'no',
            ]);

        } catch (\Exception $e) {
            \Log::error('Chat Exception: ' . $e->getMessage());
            return response()->json(['error' => 'An error occurred while processing your request.'], 500);
        }
    }

    public function show(Chat $chat)
    {
        if (auth()->id() !== $chat->user_id) {
            abort(403, 'Unauthorized access to chat.');
        }

        $messages = $chat->messages()->orderBy('created_at', 'asc')->get()->map(function ($msg) {
            return ['role' => $msg->role, 'content' => $msg->content];
        });

        return response()->json([
            'chat_id'  => $chat->id,
            'title'    => $chat->title,
            'messages' => $messages,
        ]);
    }

    public function destroy(Chat $chat)
    {
        if (auth()->id() !== $chat->user_id) {
            abort(403, 'Unauthorized access to chat.');
        }

        $chat->delete();
        return response()->json(['success' => true]);
    }
}
