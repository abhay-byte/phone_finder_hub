<?php
require __DIR__.'/vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$apiKey = $_ENV['NVIDIA_API_KEY'];
$response = \Illuminate\Support\Facades\Http::withHeaders([
    'Authorization' => "Bearer $apiKey",
    'Content-Type' => 'application/json',
])->post("https://integrate.api.nvidia.com/v1/chat/completions", [
    'model' => 'deepseek-ai/deepseek-v3.2',
    'messages' => [['role' => 'user', 'content' => 'Hello']],
    'max_tokens' => 50,
]);
print_r($response->json());
