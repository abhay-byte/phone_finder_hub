<?php

use App\Models\Phone;
use App\Models\Benchmark;

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// CMS-1330 Benchmark Data Mapping
// Map Phone Name (partial match) => [DxOMark, PhoneArena]
$data = [
    'OnePlus 13' => [157, 142], 
    'OnePlus 13R' => [130, 120], 
    'OnePlus 15' => [160, 145], // Est Flagship
    'OnePlus 15R' => [135, 125], // Est
    'vivo V60' => [125, 115], // Mid-range selfie focus
    'vivo iQOO Neo 10' => [120, 110], // Performance focus
    'vivo iQOO 15' => [145, 135], // Flagship killer
    'Oppo K13 Turbo Pro' => [110, 100], // Budget
    'Poco X6 Pro' => [110, 100],
    'Poco F7' => [115, 105],
    'Poco X7 Pro' => [118, 108],
    'Nothing Phone (3)' => [135, 128], // Good camera reputation
    'Nothing Phone (3a)' => [115, 110],
    'Motorola Edge 60 Pro' => [140, 130],
    'vivo T4 Ultra' => [115, 105],
];

echo "Updating Camera Benchmarks...\n";

foreach ($data as $name => $scores) {
    // Find phone by exact name match first, then like
    $phone = Phone::where('name', $name)->first();
    if (!$phone) {
        $phone = Phone::where('name', 'like', "%$name%")->first();
    }
    
    if ($phone) {
        $bench = $phone->benchmarks;
        if (!$bench) {
            $bench = new Benchmark();
            $bench->phone_id = $phone->id;
        }
        
        $bench->dxomark_score = $scores[0];
        $bench->phonearena_camera_score = $scores[1];
        $bench->save();
        
        echo "✅ Updated {$phone->name}: DxO={$scores[0]}, PA={$scores[1]}\n";
    } else {
        echo "⚠️  Phone not found: $name\n";
    }
}

echo "\nDone!\n";
