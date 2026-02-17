<?php

require __DIR__ . '/../vendor/autoload.php';

use App\Models\Phone;
use App\Services\CmsScoringService;

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$phones = [
    'OnePlus 13',
    'vivo iQOO 15',
    'vivo T4 Ultra',
    'Nothing Phone (3)',
    'Poco F7',
    'OnePlus 13R',
    'Poco X6 Pro'
];

$service = new CmsScoringService();

echo "🔄 Updating Benchmarks for target list...\n";

foreach ($phones as $name) {
    echo "Processing $name... ";
    $phone = Phone::where('name', $name)->first();
    
    if (!$phone) {
        // Try fuzzy search if exact match fails
        $phone = Phone::where('name', 'LIKE', "%$name%")->first();
    }
    
    if (!$phone) {
        echo "❌ Not Found\n";
        continue;
    }

    $bench = $phone->benchmarks;
    if (!$bench) {
        $bench = new \App\Models\Benchmark();
        $bench->phone_id = $phone->id;
    }
    
    // Set default scores if null
    if ($bench->dxomark_score === null) $bench->dxomark_score = 0;
    if ($bench->phonearena_camera_score === null) $bench->phonearena_camera_score = 0;
    if ($bench->other_benchmark_score === null) $bench->other_benchmark_score = 0;
    
    // Maintain OnePlus 13 PA score
    if ($name === 'OnePlus 13') {
        $bench->phonearena_camera_score = 145;
    }

    $bench->save();
    
    // Recalculate
    $phone->refresh();
    $phone->load('benchmarks', 'camera');
    $score = $service->calculate($phone);
    
    $phone->cms_score = $score['total_score'];
    $phone->cms_details = $score['breakdown'];
    $phone->save();
    
    echo "✅ CMS: {$score['total_score']}\n";
}
