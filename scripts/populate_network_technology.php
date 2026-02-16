<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Phone;

echo "Checking for phones with missing network technology...\n";

$phones = Phone::with('connectivity')->get();
$targetValue = '5G / LTE / HSPA / GSM';
$updatedCount = 0;

foreach ($phones as $phone) {
    if (!$phone->connectivity) {
        $phone->connectivity()->create([
            'network_bands' => $targetValue
        ]);
        echo "Created connectivity record for {$phone->name}\n";
        $updatedCount++;
        continue;
    }

    if (empty($phone->connectivity->network_bands)) {
        $phone->connectivity->update([
            'network_bands' => $targetValue
        ]);
        echo "Updated {$phone->name}: Set to '{$targetValue}'\n";
        $updatedCount++;
    } else {
        echo "Skipped {$phone->name}: Already has '{$phone->connectivity->network_bands}'\n";
    }
}

echo "\nUpdate complete! Total phones updated: {$updatedCount}\n";
