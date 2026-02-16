<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

// Test Phone ID 1
$phone = \App\Models\Phone::with(['body', 'platform', 'camera', 'connectivity', 'battery', 'benchmarks'])->find(1);

if ($phone) {
    echo "Phone: " . $phone->name . "\n";
    echo "CMS Score: " . $phone->cms_score . "\n\n";
    
    echo "cms_details structure:\n";
    print_r($phone->cms_details);
    
    echo "\n\nJSON serialization test:\n";
    $json = $phone->toArray();
    echo "Has cms_details in array: " . (isset($json['cms_details']) ? 'YES' : 'NO') . "\n";
    if (isset($json['cms_details'])) {
        echo "cms_details type: " . gettype($json['cms_details']) . "\n";
        echo "Has breakdown: " . (isset($json['cms_details']['breakdown']) ? 'YES' : 'NO') . "\n";
    }
} else {
    echo "Phone ID 1 not found.\n";
}
