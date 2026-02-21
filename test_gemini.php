<?php

use Illuminate\Support\Facades\Http;

$apiKey = config('services.gemini.api_key');
$userMessage = "What is the best phone under 40000 INR?";
$systemInstruction = ['parts' => [['text' => "You are an AI assistant. Answer concisely."]]];
$contents = [['role' => 'user', 'parts' => [['text' => $userMessage]]]];

echo "Testing Gemini 3 Flash Preview with API Key...\n";

$response = Http::withHeaders([
    'Content-Type' => 'application/json',
])->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key={$apiKey}", [
    'systemInstruction' => $systemInstruction,
    'contents' => $contents,
    'generationConfig' => [
        'temperature' => 0.7,
        'maxOutputTokens' => 8192,
    ]
]);

if ($response->successful()) {
    echo "Success! Response:\n";
    echo print_r($response->json(), true);
} else {
    echo "Error! Status: " . $response->status() . "\n";
    echo "Body: " . $response->body() . "\n";
}
