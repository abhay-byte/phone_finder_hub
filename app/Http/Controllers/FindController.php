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
    // ─── Helpers ──────────────────────────────────────────────────────────────

    /** Extract INR budget from current user message (e.g. "under 45k" → 45000) */
    protected function extractBudget(string $message): ?int
    {
        $text = strtolower(str_replace(',', '', $message));

        $patterns = [
            '/(?:under|below|within|max|upto|up to|less than)\s*[₹rs.]?\s*(\d+)\s*k\b/u',
            '/(?:under|below|within|max|upto|up to|less than)\s*[₹rs.]?\s*(\d{4,6})/u',
            '/[₹rs.]?\s*(\d+)\s*k\s*(?:budget|range|price)/u',
            '/budget\s*(?:of\s*)?[₹rs.]?\s*(\d+)\s*k\b/u',
            '/budget\s*(?:of\s*)?[₹rs.]?\s*(\d{4,6})/u',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $text, $m)) {
                $val = (int) $m[1];
                return $val < 1000 ? $val * 1000 : $val;
            }
        }
        return null;
    }

    /** Detect what dimension the user cares about most */
    protected function detectPriority(string $message): string
    {
        $msg = strtolower($message);
        if (preg_match('/\b(camera|photo|photography|selfie|portrait|zoom|dxo)\b/', $msg)) return 'camera';
        if (preg_match('/\b(gaming|game|gpu|fps|graphic|3dmark|geekbench)\b/', $msg))     return 'gaming';
        if (preg_match('/\b(battery|endurance|backup|life|mah)\b/', $msg))                return 'battery';
        if (preg_match('/\b(cheap|value|bang|affordable)\b/', $msg))                      return 'value';
        if (preg_match('/\b(experience|smooth|ui|daily)\b/', $msg))                       return 'experience';
        return 'overall';
    }

    /** Sort-column map for each priority */
    protected function priorityColumn(string $priority): string
    {
        return match ($priority) {
            'camera'     => 'cms_score',
            'gaming'     => 'gpx_score',
            'battery'    => 'endurance_score',
            'value'      => 'value_score',
            'experience' => 'ueps_score',
            default      => 'overall_score',
        };
    }

    // ─── Pages ────────────────────────────────────────────────────────────────

    public function index(SeoManager $seo)
    {
        $chats = auth()->check()
            ? Chat::where('user_id', auth()->id())->orderBy('updated_at', 'desc')->get()
            : [];

        $seo->set(new SEOData(
            title: 'AI Phone Finder | PhoneFinderHub',
            description: 'Find your perfect smartphone using our AI assistant.',
            url: route('find.index'),
        ));

        $allPhoneNames = \App\Models\Phone::pluck('name')->values()->toArray();
        return view('find.index', compact('chats', 'allPhoneNames'));
    }

    // ─── Chat ─────────────────────────────────────────────────────────────────

    public function chat(Request $request)
    {
        $request->validate([
            'message' => 'required|string',
            'chat_id' => 'nullable|exists:chats,id',
            'history' => 'nullable|array',
        ]);

        $userMessage = $request->message;
        $chatId      = $request->chat_id;
        $history     = $request->history ?? [];

        $apiKey = config('services.nvidia.api_key');
        if (!$apiKey) {
            return response()->json(['error' => 'NVIDIA API key not configured.'], 500);
        }

        try {
            // 1. Detect intent from CURRENT message only
            $budget   = $this->extractBudget($userMessage);
            $priority = $this->detectPriority($userMessage);
            $sortCol  = $this->priorityColumn($priority);

            // 2. Query top 15 phones sorted by priority, filtered by budget
            $query = \App\Models\Phone::with(['platform', 'battery', 'benchmarks', 'body', 'connectivity'])
                ->orderBy($sortCol, 'desc');

            if ($budget) {
                $query->where('price', '<=', $budget * 1.05); // 5% overshoot tolerance
            }

            $phones = $query->limit(15)->get();

            // 3. Build phone lines and card strings
            $phoneLines = [];
            $cardLines  = [];

            foreach ($phones as $i => $phone) {
                $imageUrl = trim($phone->image_url ?? '');
                if ($imageUrl && !str_starts_with($imageUrl, 'http') && !str_starts_with($imageUrl, '/')) {
                    $imageUrl = '/storage/' . ltrim($imageUrl, '/');
                }

                $chipset  = trim($phone->platform->chipset ?? '?');
                $battery  = trim($phone->battery->battery_type ?? '?');
                $charging = trim($phone->battery->charging_wired ?? '');
                $ram      = trim($phone->platform->ram ?? '');
                $display  = trim($phone->body->display_size ?? '');
                $nfc      = trim($phone->connectivity->nfc ?? '');
                $jack     = trim($phone->connectivity->jack_3_5mm ?? '');

                // Compact spec summary
                $specs = $chipset;
                if ($ram)      $specs .= " | {$ram} RAM";
                if ($display)  $specs .= " | {$display}";
                $specs .= " | {$battery}";
                if ($charging) $specs .= " {$charging}";
                if ($nfc && stripos($nfc, 'yes') !== false) $specs .= " | NFC";
                if ($jack && stripos($jack, 'no') === false) $specs .= " | 3.5mm";

                $endurance = $phone->endurance_score ?? 0;

                $phoneLines[] = ($i + 1) . ". {$phone->name} | ₹" . number_format($phone->price ?? 0)
                    . " | Overall:{$phone->overall_score} Expert:{$phone->expert_score} Value:{$phone->value_score}"
                    . " | UEPS:{$phone->ueps_score} CMS:{$phone->cms_score} GPX:{$phone->gpx_score} END:{$endurance}"
                    . "\n   SPECS: {$specs}";

                $cardLines[] = "[CARD|{$phone->name}|₹" . number_format($phone->price ?? 0, 0, '.', ',')
                    . "|{$imageUrl}|/phones/{$phone->id}"
                    . "|{$chipset}|{$battery}"
                    . "|" . trim($phone->amazon_url  ?? '')
                    . "|" . trim($phone->flipkart_url ?? '') . "]";
            }

            // 4. Build top-5 ranklists (budget-aware)
            $bc = $budget ? "WHERE price <= " . intval($budget * 1.05) : "";
            $fmt = fn($rows) => implode(', ', array_column($rows, 'name'));
            $ranklist =
                "Best Camera (CMS): "   . $fmt(DB::select("SELECT name FROM phones {$bc} ORDER BY cms_score       DESC LIMIT 5")) . "\n" .
                "Best Gaming (GPX): "   . $fmt(DB::select("SELECT name FROM phones {$bc} ORDER BY gpx_score       DESC LIMIT 5")) . "\n" .
                "Best Experience(UEPS):" . $fmt(DB::select("SELECT name FROM phones {$bc} ORDER BY ueps_score     DESC LIMIT 5")) . "\n" .
                "Best Endurance: "      . $fmt(DB::select("SELECT name FROM phones {$bc} ORDER BY endurance_score DESC LIMIT 5"));

            // 5. Fill prompt from resource file
            $promptTemplate = file_get_contents(resource_path('ai/find_prompt.txt'));

            $budgetLabel = $budget ? "(within ₹" . number_format($budget) . ")" : "(no budget filter)";
            $budgetRule  = $budget
                ? "HARD BUDGET: ALL phones below are already filtered to ≤₹" . number_format(intval($budget * 1.05)) . ". Do NOT recommend any phone above this price."
                : "No budget constraint. Recommend by priority score.";

            $systemPrompt = str_replace(
                ['{PHONES}', '{CARDS}', '{PRIORITY}', '{BUDGET_LABEL}', '{BUDGET_RULE}', '{RANKLIST}'],
                [implode("\n", $phoneLines), implode("\n", $cardLines), strtoupper($priority), $budgetLabel, $budgetRule, $ranklist],
                $promptTemplate
            );

            // 6. Build messages — strip [CARD|...] blobs from history
            $messages   = [['role' => 'system', 'content' => $systemPrompt]];
            $historySlice = array_slice($history, -12);

            foreach ($historySlice as $msg) {
                if (empty($msg['role']) || empty(trim($msg['content'] ?? ''))) continue;
                $content = $msg['role'] === 'assistant'
                    ? preg_replace('/\[CARD\|[^\]]+\]/s', '[phone card shown]', $msg['content'])
                    : $msg['content'];
                if (!empty(trim($content))) {
                    $messages[] = ['role' => $msg['role'], 'content' => $content];
                }
            }

            $messages[] = ['role' => 'user', 'content' => $userMessage];

            \Log::info("FindAI: priority={$priority} budget=" . ($budget ?? 'none') . " phones={$phones->count()} history=" . count($historySlice));

            // 7. Stream response
            return response()->stream(function () use ($apiKey, $messages, $userMessage, $chatId) {
                $title = null;

                if (auth()->check()) {
                    if (!$chatId) {
                        $tr = Http::timeout(30)->withHeaders([
                            'Authorization' => 'Bearer ' . $apiKey,
                            'Content-Type'  => 'application/json',
                        ])->post("https://integrate.api.nvidia.com/v1/chat/completions", [
                            'model'       => 'meta/llama-3.1-8b-instruct',
                            'messages'    => [['role' => 'user', 'content' => "3-5 word title for: \"{$userMessage}\". Return ONLY the title, no quotes."]],
                            'temperature' => 0.3,
                            'max_tokens'  => 20,
                        ]);
                        $title  = trim($tr->json()['choices'][0]['message']['content'] ?? 'New Chat', " \t\n\r\"");
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
                $call   = fn($model) => $client->request('POST', 'https://integrate.api.nvidia.com/v1/chat/completions', [
                    'headers' => ['Authorization' => 'Bearer ' . $apiKey, 'Content-Type' => 'application/json'],
                    'json'    => ['model' => $model, 'messages' => $messages, 'temperature' => 0.3, 'top_p' => 0.9, 'max_tokens' => 2048, 'stream' => true],
                    'stream'  => true,
                ]);

                try {
                    try {
                        $response = $call('meta/llama-3.1-70b-instruct');
                    } catch (\Exception $e) {
                        \Log::warning('Primary model error, using fallback: ' . $e->getMessage());
                        $response = $call('meta/llama-3.1-8b-instruct');
                    }

                    $body      = $response->getBody();
                    $assistant = '';

                    while (!$body->eof()) {
                        $line = \GuzzleHttp\Psr7\Utils::readLine($body);
                        if (!str_starts_with($line, 'data: ')) continue;
                        $data = substr($line, 6);
                        if (trim($data) === '[DONE]') break;
                        $json = json_decode($data, true);
                        if (isset($json['choices'][0]['delta']['content'])) {
                            $token = $json['choices'][0]['delta']['content'];
                            $assistant .= $token;
                            echo "data: " . json_encode(['type' => 'chunk', 'content' => $token]) . "\n\n";
                            if (ob_get_level() > 0) ob_flush();
                            flush();
                        }
                    }

                    if (auth()->check() && $chatId && !empty(trim($assistant))) {
                        ChatMessage::create(['chat_id' => $chatId, 'role' => 'assistant', 'content' => $assistant]);
                    }

                    echo "data: " . json_encode(['type' => 'done']) . "\n\n";
                    if (ob_get_level() > 0) ob_flush();
                    flush();

                } catch (\Exception $e) {
                    \Log::error('Stream error: ' . $e->getMessage());
                    echo "data: " . json_encode(['type' => 'error', 'message' => 'AI service unavailable. Please try again.']) . "\n\n";
                    if (ob_get_level() > 0) ob_flush();
                    flush();
                }
            }, 200, [
                'Cache-Control'     => 'no-cache',
                'Content-Type'      => 'text/event-stream',
                'X-Accel-Buffering' => 'no',
            ]);

        } catch (\Exception $e) {
            \Log::error('Chat exception: ' . $e->getMessage());
            return response()->json(['error' => 'An error occurred. Please try again.'], 500);
        }
    }

    // ─── Chat history ──────────────────────────────────────────────────────────

    public function show(Chat $chat)
    {
        abort_unless(auth()->id() === $chat->user_id, 403);
        return response()->json([
            'chat_id'  => $chat->id,
            'title'    => $chat->title,
            'messages' => $chat->messages()->orderBy('created_at')->get()->map(fn($m) => ['role' => $m->role, 'content' => $m->content]),
        ]);
    }

    public function destroy(Chat $chat)
    {
        abort_unless(auth()->id() === $chat->user_id, 403);
        $chat->delete();
        return response()->json(['success' => true]);
    }
}
