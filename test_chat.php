<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$request = Illuminate\Http\Request::create('/find/chat', 'POST', [
    'message' => 'What is the best phone under 40000 INR?',
]);

$response = app()->handle($request);

echo "Status Code: " . $response->getStatusCode() . "\n";
echo "Response Body:\n" . $response->getContent() . "\n";

