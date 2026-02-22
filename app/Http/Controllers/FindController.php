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

    public function chat(Request $request)
    {
        $request->validate([
            'message' => 'required|string',
            'chat_id' => 'nullable|exists:chats,id',
            'history' => 'nullable|array',
        ]);

        $userMessage = $request->message;
        $chatId = $request->chat_id;
        $history = $request->history ?? [];

        $apiKey = config('services.nvidia.api_key');
        if (!$apiKey) {
            return response()->json(['error' => 'NVIDIA API Key is missing. Please configure NVIDIA_API_KEY in .env'], 500);
        }

        try {
            // Fetch ALL phones directly to feed the entire database into Nemotron's large context window.
            // This relies on the AI organic reasoning to filter by budget/priority, bypassing fragile pre-extraction.
            $filteredPhones = \App\Models\Phone::with(['platform', 'battery', 'benchmarks'])
                ->orderBy('overall_score', 'desc')
                ->get();

            // STEP 3: Build compact context from ALL phones
            $phoneLines = [];
            $cardLines = [];
            foreach ($filteredPhones as $rank => $phone) {
                $imageUrl = trim($phone->image_url ?? '');
                if ($imageUrl && !str_starts_with($imageUrl, 'http') && !str_starts_with($imageUrl, '/')) {
                    $imageUrl = '/storage/' . ltrim($imageUrl, '/');
                }

                $chipset = trim($phone->platform->chipset ?? '?');
                $batteryType = trim($phone->battery->battery_type ?? '?');
                $endurance = $phone->calculateEnduranceScore();

                $phoneLines[] = ($rank + 1) . ". {$phone->name} | ₹" . number_format($phone->price ?? 0) .
                    " | OS:{$phone->overall_score} ES:{$phone->expert_score} VS:{$phone->value_score}" .
                    " | UEPS:{$phone->ueps_score} CMS:{$phone->cms_score} GPX:{$phone->gpx_score} END:{$endurance}" .
                    " | {$chipset} | {$batteryType}";

                $cardLines[] = "[CARD|" . trim($phone->name) . "|₹" . number_format($phone->price ?? 0, 0, '.', ',') .
                    "|" . $imageUrl . "|/phones/" . $phone->id .
                    "|" . $chipset . "|" . $batteryType .
                    "|" . trim($phone->amazon_url ?? '') . "|" . trim($phone->flipkart_url ?? '') . "]";
            }

            $phoneDatabase = implode("\n", $phoneLines);
            $cardLookup = implode("\n", $cardLines);


            // STEP 4: Build concise ranklists for context
            $topExpert = DB::table('phones')->orderBy('expert_score', 'desc')->limit(5)->pluck('name')->toArray();
            $topCms = DB::table('phones')->orderBy('cms_score', 'desc')->limit(5)->pluck('name')->toArray();
            $topGpx = DB::table('phones')->orderBy('gpx_score', 'desc')->limit(5)->pluck('name')->toArray();
            $topUeps = DB::table('phones')->orderBy('ueps_score', 'desc')->limit(5)->pluck('name')->toArray();
            $topEndurance = DB::table('phones')->orderBy('endurance_score', 'desc')->limit(5)->pluck('name')->toArray();

            $ranklistContext = "TOP RANKINGS:\n" .
                "- Expert Score: " . implode(', ', $topExpert) . "\n" .
                "- Camera (CMS): " . implode(', ', $topCms) . "\n" .
                "- Gaming (GPX): " . implode(', ', $topGpx) . "\n" .
                "- Experience (UEPS): " . implode(', ', $topUeps) . "\n" .
                "- Endurance: " . implode(', ', $topEndurance);

            // STEP 5: Build system prompt
            $systemPrompt = <<<PROMPT
You are PhoneFinder AI, a concise phone consultant for PhoneFinderHub.

⚠️ ABSOLUTE RULES:
- ONLY recommend phones from the list below. NEVER invent a phone.
- ONLY use data provided below. Do NOT make up specs like RAM, camera MP, display size, or any detail not listed.
- ONLY use the exact [CARD|...] strings below. NEVER fabricate card strings.

PHONES:
{$phoneDatabase}

CARDS (COPY-PASTE exactly — never modify or create new ones):
{$cardLookup}

SCORES (use full names when talking to user):
Overall Score (max ~60), Expert Score (max ~80), Value Score (bang-for-buck), UEPS/User Experience (max 255), Camera Mastery Score (max ~1330), Gaming Performance Index (max ~300), Endurance (max ~160).

{$ranklistContext}

KNOWLEDGE (mention ONLY if user asks):
- Turnip: Open-source GPU drivers for game emulation. ONLY works on Snapdragon phones with Adreno GPUs. Mediatek/Exynos phones do NOT support Turnip.
- Bootloader unlock: Allows custom ROMs. Availability varies by brand — OnePlus/Realme usually allow it, Samsung makes it harder, some brands block it entirely.

RULES:
1. ONLY state facts from the data above. If info isn't in the data, say "I don't have that detail."
2. Output the [CARD|...] string when recommending. Never list phones as plain text.
3. For multiple phones, put each [CARD|...] on its own line, then a brief comparison.
4. ABSOLUTELY NEVER copy-paste the raw score data strings (e.g. NEVER write "OS 17.8 ES 45.55 VS 11.7...").
5. INSTEAD, write 1-2 natural, concise sentences highlighting ONLY the most impressive or relevant specs/scores for the user's request. (e.g. "It has a massive 6500mAh battery and an excellent Camera Mastery Score of 853.")
6. Use full score names (e.g. "Camera Mastery Score" not "CMS") when talking about them.
7. Stay on topic. Don't switch phones unless asked.
8. If user asks about Turnip/bootloader, check the chipset — if it's Mediatek/Exynos, tell them Turnip is not supported.
PROMPT;

            // Build messages array
            $messages = [];
            $messages[] = ['role' => 'system', 'content' => $systemPrompt];

            // Include conversation history (last 10 exchanges max)
            $historySlice = array_slice($history, -20);
            foreach ($historySlice as $msg) {
                if (isset($msg['role']) && isset($msg['content']) && !empty(trim($msg['content']))) {
                    $messages[] = [
                        'role' => $msg['role'],
                        'content' => $msg['content'],
                    ];
                }
            }

            $messages[] = ['role' => 'user', 'content' => $userMessage];

            \Log::info("FindController: Sending " . count($messages) . " messages (" . $filteredPhones->count() . " phones in context)");

            return response()->stream(function () use ($apiKey, $messages, $userMessage, $chatId) {
                $title = null;

                // Process Chat Title & DB Before Streaming starts
                if (auth()->check()) {
                    if (!$chatId) {
                        $titleGenerationResponse = Http::timeout(30)->withHeaders([
                            'Authorization' => 'Bearer ' . $apiKey,
                            'Content-Type' => 'application/json',
                        ])->post("https://integrate.api.nvidia.com/v1/chat/completions", [
                            'model' => 'meta/llama-3.1-8b-instruct',
                            'messages' => [
                                ['role' => 'user', 'content' => "Generate a short 3-5 word title for a chat that starts with this message. Return ONLY the title string, no quotes. Message: \"{$userMessage}\""]
                            ],
                            'temperature' => 0.3,
                            'max_tokens' => 20
                        ]);
                        $title = 'New Chat';
                        if ($titleGenerationResponse->successful() && isset($titleGenerationResponse->json()['choices'][0]['message']['content'])) {
                            $title = trim($titleGenerationResponse->json()['choices'][0]['message']['content'], " \t\n\r\0\x0B\"");
                        }

                        $chat = Chat::create([
                            'user_id' => auth()->id(),
                            'title' => $title,
                        ]);
                        $chatId = $chat->id;
                    } else {
                        $chat = Chat::findOrFail($chatId);
                        $chat->touch();
                        $title = $chat->title;
                    }

                    ChatMessage::create([
                        'chat_id' => $chatId,
                        'role' => 'user',
                        'content' => $userMessage,
                    ]);
                }

                echo "data: " . json_encode(['type' => 'meta', 'chat_id' => $chatId, 'title' => $title]) . "\n\n";
                if (ob_get_level() > 0) ob_flush();
                flush();

                $client = new \GuzzleHttp\Client();
                $makeRequest = function($model) use ($client, $apiKey, $messages) {
                    return $client->request('POST', 'https://integrate.api.nvidia.com/v1/chat/completions', [
                        'headers' => [
                            'Authorization' => 'Bearer ' . $apiKey,
                            'Content-Type' => 'application/json',
                        ],
                        'json' => [
                            'model' => $model,
                            'messages' => $messages,
                            'temperature' => 0.5,
                            'top_p' => 0.9,
                            'max_tokens' => 2048,
                            'stream' => true,
                        ],
                        'stream' => true,
                    ]);
                };

                try {
                    // Primary: llama-3.1-70b — best quality, confirmed working on this NVIDIA account
                    // Fallback: llama-3.1-8b — ultra-fast, also confirmed working
                    try {
                        $response = $makeRequest('meta/llama-3.1-70b-instruct');
                    } catch (\Exception $primaryEx) {
                        \Log::warning('Primary model failed, trying fallback: ' . $primaryEx->getMessage());
                        $response = $makeRequest('meta/llama-3.1-8b-instruct');
                    }

                    $body = $response->getBody();
                    $assistantMessage = '';

                    while (!$body->eof()) {
                        $line = \GuzzleHttp\Psr7\Utils::readLine($body);
                        if (str_starts_with($line, 'data: ')) {
                            $data = substr($line, 6);
                            if (trim($data) === '[DONE]') {
                                break;
                            }
                            $json = json_decode($data, true);
                            if (isset($json['choices'][0]['delta'])) {
                                $delta = $json['choices'][0]['delta'];
                                
                                // Process reasoning (thinking) chunk if available
                                if (isset($delta['reasoning_content'])) {
                                    $reasoning = $delta['reasoning_content'];
                                    // Optionally stream reasoning to UI if needed
                                    // echo "data: " . json_encode(['type' => 'reasoning', 'content' => $reasoning]) . "\n\n";
                                }
                                
                                // Process actual content chunk
                                if (isset($delta['content'])) {
                                    $token = $delta['content'];
                                    $assistantMessage .= $token;
                                    echo "data: " . json_encode(['type' => 'chunk', 'content' => $token]) . "\n\n";
                                    if (ob_get_level() > 0) ob_flush();
                                    flush();
                                }
                            }
                        }
                    }

                    if (auth()->check() && $chatId && !empty(trim($assistantMessage))) {
                        ChatMessage::create([
                            'chat_id' => $chatId,
                            'role' => 'assistant',
                            'content' => $assistantMessage,
                        ]);
                    }

                    echo "data: " . json_encode(['type' => 'done']) . "\n\n";
                    if (ob_get_level() > 0) ob_flush();
                    flush();
                } catch (\Exception $e) {
                    \Log::error('Stream Error: ' . $e->getMessage());
                    echo "data: " . json_encode(['type' => 'error', 'message' => 'AI service is currently unavailable or hitting usage limits. Please try again later.']) . "\n\n";
                    if (ob_get_level() > 0) ob_flush();
                    flush();
                }
            }, 200, [
                'Cache-Control' => 'no-cache',
                'Content-Type' => 'text/event-stream',
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

        $messages = $chat->messages()->orderBy('created_at', 'asc')->get()->map(function($msg) {
            return [
                'role' => $msg->role,
                'content' => $msg->content,
            ];
        });

        return response()->json([
            'chat_id' => $chat->id,
            'title' => $chat->title,
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
