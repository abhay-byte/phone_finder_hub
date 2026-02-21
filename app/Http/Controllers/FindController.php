<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use App\Models\Chat;
use App\Models\ChatMessage;

class FindController extends Controller
{
    public function index()
    {
        $chats = [];
        if (auth()->check()) {
            $chats = Chat::where('user_id', auth()->id())->orderBy('updated_at', 'desc')->get();
        }
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
            // STEP 1: RAG Query Extraction
            // We use the cheaper/faster Flash model to extract search parameters
            $extractorContext = "";
            if (!empty($history)) {
                $lastMsg = end($history);
                if (isset($lastMsg['content'])) {
                    $extractorContext = " (Preceding conversation context: " . substr($lastMsg['content'], 0, 400) . ")";
                }
            }
            $extractorInstruction = "Extract search parameters from the user's message. Output ONLY a valid JSON object with these exact keys, using null if not specified: 'brand' (string), 'min_price' (integer), 'max_price' (integer), 'priority' (string enum: 'gaming', 'camera', 'battery', 'value', 'overall' - default is 'overall'). If the user refers to a previously mentioned phone, extract its brand." . $extractorContext;
            
            $extractionResponse = Http::timeout(60)->withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json',
            ])->post("https://api.groq.com/openai/v1/chat/completions", [
                'model' => 'llama-3.1-8b-instant',
                'messages' => [
                    ['role' => 'system', 'content' => $extractorInstruction],
                    ['role' => 'user', 'content' => $userMessage]
                ],
                'temperature' => 0.1,
                'response_format' => ['type' => 'json_object']
            ]);

            \Log::info("FindController: Response Status (Extraction): " . $extractionResponse->status());
            if ($extractionResponse->status() === 429) {
                return response()->json(['error' => 'AI usage limit reached for the day/minute. Please try again later.'], 429);
            }
            if (!$extractionResponse->successful()) {
                return response()->json(['error' => 'AI service is currently unavailable. Please try again later.'], 500);
            }

            $searchParams = ['brand' => null, 'min_price' => null, 'max_price' => null, 'priority' => 'overall'];
            if ($extractionResponse->successful() && isset($extractionResponse->json()['choices'][0]['message']['content'])) {
                $rawJson = $extractionResponse->json()['choices'][0]['message']['content'];
                $parsed = json_decode($rawJson, true);
                if (is_array($parsed)) {
                    $searchParams = array_merge($searchParams, $parsed);
                }
            }

            // STEP 2: Database Retrieval
            $query = \App\Models\Phone::with([
                'platform', 'camera', 'benchmarks', 'battery', 'connectivity', 'body'
            ]);

            if (!empty($searchParams['brand'])) {
                $query->where('brand', 'like', '%' . $searchParams['brand'] . '%');
            }
            if (!empty($searchParams['min_price'])) {
                $query->where('price', '>=', $searchParams['min_price']);
            }
            if (!empty($searchParams['max_price'])) {
                $query->where('price', '<=', $searchParams['max_price']);
            }
            
            // Order by relevance based on explicit priority or overall score
            $priority = $searchParams['priority'] ?? 'overall';
            if ($priority === 'gaming') {
                $query->orderBy('gpx_score', 'desc');
            } elseif ($priority === 'camera') {
                $query->orderBy('cms_score', 'desc');
            } elseif ($priority === 'battery') {
                $query->join('spec_batteries', 'phones.id', '=', 'spec_batteries.phone_id')
                      ->orderBy('spec_batteries.capacity_mah', 'desc')
                      ->select('phones.*');
            } elseif ($priority === 'value') {
                $query->orderBy('value_score', 'desc');
            } else {
                $query->orderBy('overall_score', 'desc');
            }

            // Fetch top 2 matches to pass as context (reduces heavy token load)
            $relevantPhones = $query->select('phones.*')->limit(2)->get();
            $simplifiedPhones = $relevantPhones->map(function (\App\Models\Phone $phone) {
                // Ensure image URLs output as valid absolute/storage paths for UI cards
                $imageUrl = trim($phone->image_url ?? '');
                if ($imageUrl && !str_starts_with($imageUrl, 'http') && !str_starts_with($imageUrl, '/')) {
                    $imageUrl = '/storage/' . ltrim($imageUrl, '/');
                }

                // Helper to strip massive nulls and metadata from payload objects to save TPM context size
                $cleanSpecs = function($relation) {
                    if (!$relation) return null;
                    $arr = $relation->toArray();
                    return array_filter($arr, function($val, $key) {
                        return $val !== null && $val !== '' && !in_array($key, ['id', 'phone_id', 'created_at', 'updated_at']);
                    }, ARRAY_FILTER_USE_BOTH);
                };

                return [
                    'name' => $phone->name,
                    'price' => $phone->price,
                    'overall_score' => $phone->overall_score,
                    'expert_score' => $phone->expert_score,
                    'value_score' => $phone->value_score,
                    'ueps' => $phone->ueps_score,
                    'cms' => $phone->cms_score,
                    'gpx' => $phone->gpx_score,
                    'endurance' => $phone->calculateEnduranceScore(),
                    'specs' => array_filter([
                        'platform' => $cleanSpecs($phone->platform),
                        'camera' => $cleanSpecs($phone->camera),
                        'battery' => $cleanSpecs($phone->battery),
                        'connectivity' => $cleanSpecs($phone->connectivity),
                        'body' => $cleanSpecs($phone->body),
                        'benchmarks' => $cleanSpecs($phone->benchmarks),
                    ]),
                    'card_string' => "[CARD|" . trim($phone->name) . "|₹" . number_format($phone->price ?? 0, 0, '.', ',') . "|" . $imageUrl . "|/phones/" . $phone->id . "|" . trim($phone->platform->chipset ?? 'Unknown SoC') . "|" . trim($phone->battery->battery_type ?? '') . "|" . trim($phone->amazon_url ?? '') . "|" . trim($phone->flipkart_url ?? '') . "]",
                ];
            });
            
            $contextString = "DATABASE CONTEXT:\n";
            $contextString .= json_encode($simplifiedPhones->toArray());

            // STEP 3: Final Generation
            $systemInstruction = "You are an expert AI Phone Consultant for Phone Finder Hub. Act like a helpful agent. Instead of dropping lists of specs, ALWAYS ask clarifying questions first if the user hasn't provided enough details. ALWAYS ask for their exact budget/price range and priorities (gaming, camera, endurance).\n\n" .
                                 "CRITICAL INSTRUCTION - ONE QUESTION AT A TIME:\n" .
                                 "You must ONLY ASK ONE QUESTION AT A TIME. DO NOT present multiple lists of different questions or button groups in a single response. Wait for the user to answer the first question before moving on. For example, either ask about their budget OR ask about their priorities, but NEVER both at the same time.\n\n" .
                                 "CRITICAL INSTRUCTION - FORMATTING OPTIONS:\n" .
                                 "When you ask the user for their preferences, DO NOT use standard markdown bullet points. You MUST format choices using the special exact tag `[BTN|Choice Text]`.\n" .
                                 "Example single-question output:\n" .
                                 "To narrow it down, could you let me know what matters most to you?\n" .
                                 "[BTN|Camera Quality (Photos & Video)]\n" .
                                 "[BTN|Heavy Gaming Performance]\n" .
                                 "[BTN|All-day Battery Life]\n\n" .
                                 "The user uses this website to look for phones. Answer queries based ONLY on general real-world knowledge AND the DATABASE CONTEXT provided. Note: Context now includes FULL detailed specs. If giving details about the value table, explain: UEPS = User Experience Performance Score, CMS = Camera Matrix Score, GPX = Gaming Performance Index, Endurance = Battery Life Rating. Note: 'Turnip' refers to custom open-source GPU drivers for Snapdragon chips that massively improve gaming/emulation performance. 'Bootloader unlock' means the phone allows flashing custom ROMs and rooting.\n\n" .
                                 "CRITICAL INSTRUCTION - PRODUCT CARDS:\n" .
                                 "When recommending a phone, you MUST output the EXACT \"card_string\" provided for that phone in the context, followed by a short summary of why it fits their needs, key specs, and performance scores. \n" .
                                 "Do NOT put the card_string in a code block or markdown formatting like ` or **. Output it exactly as raw plain text so the system can parse it. \n" .
                                 "For example:\n" .
                                 "[CARD|Oppo Find X9 Pro|₹50,000|/image/path.jpg|/phones/5|Snapdragon 8 Gen 3|5000 mAh|https://amazon.in/..|https://flipkart.com/..]\n" .
                                 "This phone features an excellent GPX score of 250 and great endurance, making it perfect for your needs.";

            // Format history
            $messages = [];
            $messages[] = ['role' => 'system', 'content' => $systemInstruction];
            foreach ($history as $msg) {
                $messages[] = [
                    'role' => $msg['role'],
                    'content' => $msg['content'],
                ];
            }

            // Add the current user message + RAG Context
            $finalUserMessage = "User Query: " . $userMessage . "\n\n" . $contextString;
            $messages[] = [
                'role' => 'user',
                'content' => $finalUserMessage,
            ];

            \Log::info("FindController: Starting Final API request to Groq via Stream");
            
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
                try {
                    $response = $client->request('POST', 'https://api.groq.com/openai/v1/chat/completions', [
                        'headers' => [
                            'Authorization' => 'Bearer ' . $apiKey,
                            'Content-Type' => 'application/json',
                        ],
                        'json' => [
                            'model' => 'llama-3.3-70b-versatile',
                            'messages' => $messages,
                            'temperature' => 0.7,
                            'max_completion_tokens' => 4096,
                            'stream' => true,
                        ],
                        'stream' => true,
                    ]);

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

                    if (auth()->check() && $chatId) {
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
                    echo "data: " . json_encode(['type' => 'error', 'message' => 'AI service is currently unavailable. Please try again later.']) . "\n\n";
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
