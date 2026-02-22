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

        return view('find.index', compact('chats'));
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

        $apiKey = config('services.groq.api_key');
        if (!$apiKey) {
            return response()->json(['error' => 'Groq API Key is missing. Please configure GROQ_API_KEY in .env'], 500);
        }

        try {
            // STEP 1: Extract search filters from the FULL conversation context
            // Build a conversation summary for the extractor so it understands ongoing context
            $conversationSummary = "";
            $recentHistory = array_slice($history, -10); // last 5 exchanges
            foreach ($recentHistory as $msg) {
                if (isset($msg['role']) && isset($msg['content'])) {
                    $role = $msg['role'] === 'user' ? 'User' : 'AI';
                    $conversationSummary .= "{$role}: " . substr($msg['content'], 0, 300) . "\n";
                }
            }
            $conversationSummary .= "User: {$userMessage}\n";

            $extractorInstruction = "Given this phone-finding conversation, extract search filters. Output ONLY a JSON object with: " .
                "'brand' (string|null - ONLY if user explicitly mentions a brand name like Samsung, OnePlus, etc.), " .
                "'min_price' (int|null), 'max_price' (int|null), " .
                "'priority' (string: 'gaming'|'camera'|'battery'|'value'|'overall', default 'overall'), " .
                "'phone_name' (string|null - exact phone name if user is asking about a specific phone already discussed). " .
                "IMPORTANT: Do NOT set brand unless user explicitly says a brand name. Look at the ENTIRE conversation to determine budget and preferences.";

            $extractionResponse = Http::timeout(15)->withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json',
            ])->post("https://api.groq.com/openai/v1/chat/completions", [
                'model' => 'llama-3.1-8b-instant',
                'messages' => [
                    ['role' => 'system', 'content' => $extractorInstruction],
                    ['role' => 'user', 'content' => $conversationSummary]
                ],
                'temperature' => 0.05,
                'max_completion_tokens' => 150,
                'response_format' => ['type' => 'json_object']
            ]);

            $searchParams = ['brand' => null, 'min_price' => null, 'max_price' => null, 'priority' => 'overall', 'phone_name' => null];
            if ($extractionResponse->successful() && isset($extractionResponse->json()['choices'][0]['message']['content'])) {
                $parsed = json_decode($extractionResponse->json()['choices'][0]['message']['content'], true);
                if (is_array($parsed)) {
                    $searchParams = array_merge($searchParams, $parsed);
                }
            } else {
                \Log::warning("FindController: Extraction failed (status " . $extractionResponse->status() . "), using defaults");
            }
            \Log::info("FindController: Extracted params", $searchParams);

            // STEP 2: Query filtered phones from DB
            $query = \App\Models\Phone::with(['platform', 'battery', 'benchmarks'])
                ->orderBy('overall_score', 'desc');

            if (!empty($searchParams['brand'])) {
                $query->where('brand', 'like', '%' . $searchParams['brand'] . '%');
            }
            if (!empty($searchParams['min_price'])) {
                $query->where('price', '>=', $searchParams['min_price']);
            }
            if (!empty($searchParams['max_price'])) {
                $query->where('price', '<=', $searchParams['max_price']);
            }

            // Order by priority
            $priority = $searchParams['priority'] ?? 'overall';
            if ($priority === 'gaming') {
                $query->reorder('gpx_score', 'desc');
            } elseif ($priority === 'camera') {
                $query->reorder('cms_score', 'desc');
            } elseif ($priority === 'battery') {
                // Join with spec_batteries and order by capacity extracted from battery_type
                $query->join('spec_batteries', 'phones.id', '=', 'spec_batteries.phone_id')
                      ->select('phones.*')
                      ->orderByRaw("CAST(SUBSTRING(spec_batteries.battery_type FROM '([0-9]+)') AS INTEGER) DESC NULLS LAST");
            } elseif ($priority === 'value') {
                $query->reorder('value_score', 'desc');
            }

            // Get top 8 filtered matches for better variety
            $filteredPhones = $query->limit(8)->get();

            // If user is asking about a specific phone by name, make sure it's included
            if (!empty($searchParams['phone_name'])) {
                $specificPhone = \App\Models\Phone::with(['platform', 'battery', 'benchmarks'])
                    ->where('name', 'like', '%' . $searchParams['phone_name'] . '%')
                    ->first();
                if ($specificPhone && !$filteredPhones->contains('id', $specificPhone->id)) {
                    $filteredPhones->prepend($specificPhone);
                }
            }

            // STEP 3: Build compact context from filtered phones
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

            // STEP 4: Build system prompt
            $systemPrompt = <<<PROMPT
You are PhoneFinder AI, a friendly expert phone consultant for PhoneFinderHub.

MATCHING PHONES FROM DATABASE:
{$phoneDatabase}

CARD STRINGS (you MUST copy-paste one of these EXACTLY when recommending a phone — no tables, no plain text lists):
{$cardLookup}

SCORE GUIDE (ALWAYS use the FULL NAME when talking to the user, never abbreviations):
- Overall Score — Our composite ranking combining all metrics. Max ~60.
- Expert Score — Professional reviewer consensus score. Max ~80.
- Value Score — Bang-for-buck rating. Higher = better deal.
- UEPS (User Experience Performance Score) — 40 criteria across 7 categories. Max 255.
- Camera Mastery Score (CMS) — 1330-point camera scoring covering hardware + imaging + benchmarks.
- Gaming Performance Index (GPX) — 300-point gaming benchmark. Thermals, emulation, Turnip support.
- Endurance — Battery life rating. Raw capacity (mAh) + active screen-on efficiency. Max ~160.

CRITICAL RULES:
1. STAY ON TOPIC. If discussing a phone, keep talking about THAT phone unless user asks otherwise.
2. You MUST output the [CARD|...] string when recommending ANY phone. NEVER list phones as plain text — always use the card. This is non-negotiable.
3. When recommending MULTIPLE phones, output each [CARD|...] string on its own line. Then write a brief comparison.
4. Use FULL score names (e.g. "Camera Mastery Score: 853" not "CMS: 853").
5. Ask ONE question at a time if you need budget or priority. Format choices as [BTN|Choice Text].
6. Be concise. Close the sale — the card has buy links.
7. For follow-up questions about a phone already discussed, just answer without showing a different card.
8. Recommend the BEST phones from the list — pick the ones with the highest relevant score for the user's priority.
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
                        ])->post("https://api.groq.com/openai/v1/chat/completions", [
                            'model' => 'llama-3.1-8b-instant',
                            'messages' => [
                                ['role' => 'user', 'content' => "Generate a short 3-5 word title for a chat that starts with this message. Return ONLY the title string, no quotes. Message: \"{$userMessage}\""]
                            ],
                            'temperature' => 0.3,
                            'max_completion_tokens' => 20
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
                    return $client->request('POST', 'https://api.groq.com/openai/v1/chat/completions', [
                        'headers' => [
                            'Authorization' => 'Bearer ' . $apiKey,
                            'Content-Type' => 'application/json',
                        ],
                        'json' => [
                            'model' => $model,
                            'messages' => $messages,
                            'temperature' => 0.6,
                            'max_completion_tokens' => 2048,
                            'stream' => true,
                        ],
                        'stream' => true,
                    ]);
                };

                try {
                    try {
                        $response = $makeRequest('llama-3.3-70b-versatile');
                    } catch (\GuzzleHttp\Exception\ClientException $e) {
                         if ($e->getResponse()->getStatusCode() === 429) {
                              \Log::warning("FindController: 70B hit 429 Limit. Falling back to 8B Instant.");
                              $response = $makeRequest('llama-3.1-8b-instant');
                         } else {
                              throw $e;
                         }
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
                            if (isset($json['choices'][0]['delta']['content'])) {
                                $token = $json['choices'][0]['delta']['content'];
                                $assistantMessage .= $token;
                                echo "data: " . json_encode(['type' => 'chunk', 'content' => $token]) . "\n\n";
                                if (ob_get_level() > 0) ob_flush();
                                flush();
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
