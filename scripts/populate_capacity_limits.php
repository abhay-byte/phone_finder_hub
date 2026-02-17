<?php

use App\Models\Phone;
use Illuminate\Support\Facades\DB;

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "🚀 Starting capacity limits population...\n";

$phones = Phone::with('platform')->get();

foreach ($phones as $phone) {
    if (!$phone->platform) continue;

    echo "Processing {$phone->name}...\n";

    $ramString = $phone->platform->ram;
    $storageString = $phone->platform->internal_storage;

    $ramMin = null;
    $ramMax = null;
    $storageMin = null;
    $storageMax = null;

    // Parse RAM
    if ($ramString) {
        preg_match_all('/(\d+)\s*GB/i', $ramString, $matches);
        if (!empty($matches[1])) {
            $rams = array_map('intval', $matches[1]);
            $ramMin = min($rams);
            $ramMax = max($rams);
        }
    }

    // Parse Storage
    if ($storageString) {
        $storageValues = [];
        
        // Match GB
        preg_match_all('/(\d+)\s*GB/i', $storageString, $gbMatches);
        if (!empty($gbMatches[1])) {
            foreach ($gbMatches[1] as $val) {
                $storageValues[] = intval($val);
            }
        }

        // Match TB
        preg_match_all('/(\d+)\s*TB/i', $storageString, $tbMatches);
        if (!empty($tbMatches[1])) {
            foreach ($tbMatches[1] as $val) {
                $storageValues[] = intval($val) * 1024;
            }
        }

        if (!empty($storageValues)) {
            $storageMin = min($storageValues);
            $storageMax = max($storageValues);
        }
    }

    $phone->platform->update([
        'ram_min' => $ramMin,
        'ram_max' => $ramMax,
        'storage_min' => $storageMin,
        'storage_max' => $storageMax,
    ]);

    echo "   -> RAM: {$ramMin}-{$ramMax} GB | Storage: {$storageMin}-{$storageMax} GB\n";
}

echo "\n🎉 Capacity limits populated!\n";
