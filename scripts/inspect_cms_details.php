<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

$phone = \App\Models\Phone::find(1);

if ($phone) {
    echo "Phone: " . $phone->name . "\n";
    echo "CMS Score: " . $phone->cms_score . "\n";
    echo "CMS Details JSON:\n";
    echo json_encode($phone->cms_details, JSON_PRETTY_PRINT) . "\n";
} else {
    echo "Phone ID 1 not found.\n";
}
