<?php

use App\Models\Phone;

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Recalculating CMS Scores for all phones...\n";

$phones = Phone::all();

foreach ($phones as $phone) {
    echo "Processing: {$phone->name}...\n";
    try {
        $phone->updateScores();
        echo "  -> CMS: {$phone->cms_score}\n";
        echo "  -> GPX: {$phone->gpx_score}\n";
        echo "  -> UEPS: {$phone->ueps_score}\n";
        echo "  -> FPI: {$phone->overall_score}\n";
    } catch (\Exception $e) {
        echo "  ❌ Error: " . $e->getMessage() . "\n";
    }
}

echo "\nAll scores updated!\n";
