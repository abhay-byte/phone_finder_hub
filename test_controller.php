<?php

use Illuminate\Http\Request;
use App\Http\Controllers\FindController;

// Mock request
$request = Request::create('/find/chat', 'POST', [
    'message' => 'Are there any phones with Turnip drivers?',
]);

// Since the controller handles DB models directly, we can instantiate it and call chat()
$controller = app()->make(FindController::class);
$response = $controller->chat($request);

echo "Status Code: " . $response->getStatusCode() . "\n";
echo "Response Content:\n";
echo $response->getContent() . "\n";
