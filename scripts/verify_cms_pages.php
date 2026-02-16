<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

echo "Starting CMS Page Verification...\n";

// 1. Verify Phone Page
echo "\n1. Verifying Phone Page (ID: 1)...\n";
$phone = \App\Models\Phone::find(1);

if (!$phone) {
    echo "Phone ID 1 not found. Skipping phone page verification.\n";
} else {
    // Simulate rendering the view
    $html = view('phones.show', ['phone' => $phone])->render();

    // Check for CMS Score Card
    $checks = [
        'Camera Mastery Score' => str_contains($html, 'Camera Mastery Score (CMS-1330)'), // Tooltip text
        'CMS Score Value' => str_contains($html, $phone->cms_score),
        'Amber Theme' => str_contains($html, 'text-amber-300'),
        'Detailed Breakdown' => str_contains($html, 'Detailed Breakdown'),
        'CMS Breakdown Item' => str_contains($html, 'Camera Mastery (CMS)'),
    ];

    foreach ($checks as $check => $passed) {
        echo "   - {$check}: " . ($passed ? "PASS" : "FAIL") . "\n";
    }
}

// 2. Verify Comparison Page JS Logic (Indirectly via ensuring component exists)
echo "\n2. Verifying Comparison Page Asset references...\n";
$jsFile = file_get_contents(__DIR__ . '/../resources/js/components/comparison-page.js');

$checks = [
    'CMS in Specs' => str_contains($jsFile, "title: 'Camera (CMS-1330)'"),
    'CMS Score Key' => str_contains($jsFile, "key: 'cms_score'"),
    'CMS Max Score logic' => str_contains($jsFile, "max: 1330"),
    'Show CMS State' => str_contains($jsFile, "showCms: false"),
];

foreach ($checks as $check => $passed) {
    echo "   - {$check}: " . ($passed ? "PASS" : "FAIL") . "\n";
}

echo "\nVerification Complete.\n";
