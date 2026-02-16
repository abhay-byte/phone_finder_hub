<?php

use App\Models\Phone;

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$phone = Phone::where('name', 'vivo iQOO Neo 10')->first();
if ($phone) {
    echo "Processing: {$phone->name}...\n";
    $phone->updateScores();
    echo "New CMS: {$phone->cms_score}\n";
    
    // Show Fusion details
    foreach ($phone->cms_details['fusion']['details'] as $item) {
         echo "  - {$item['criterion']}: {$item['points']} ({$item['reason']})\n";
    }
}
