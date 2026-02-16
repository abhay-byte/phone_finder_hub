<?php

use App\Models\Phone;

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$names = ['vivo V60', 'vivo iQOO Neo 10'];

foreach ($names as $name) {
    $phone = Phone::where('name', $name)->first();
    if ($phone) {
        echo "============================================\n";
        echo "{$phone->name} (CMS: {$phone->cms_score})\n";
        echo "============================================\n";
        $details = $phone->cms_details;
        foreach ($details as $category => $data) {
            echo strtoupper($category) . " (Score: {$data['score']} / {$data['max']})\n";
            foreach ($data['details'] as $item) {
                echo "  - {$item['criterion']}: {$item['points']} ({$item['reason']})\n";
            }
            echo "\n";
        }
    } else {
        echo "Phone not found: $name\n";
    }
}
