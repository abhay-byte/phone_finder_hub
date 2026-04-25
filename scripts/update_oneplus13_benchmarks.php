<?php

require __DIR__.'/../vendor/autoload.php';

use App\Models\Phone;
use App\Services\CmsScoringService;

$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$phoneName = 'OnePlus 13';
$phone = Phone::where('name', $phoneName)->first();

if (! $phone) {
    echo "❌ $phoneName not found!\n";
    exit;
}

echo "📱 Updating Benchmarks for {$phone->name}...\n";

$bench = $phone->benchmarks;
if (! $bench) {
    echo "⚠️ No benchmarks found, creating...\n";
    $bench = new \App\Models\Benchmark;
    $bench->phone_id = $phone->id;
}

// Update Scores
$bench->dxomark_score = 0; // Not available yet
$bench->phonearena_camera_score = 145; // Source: PhoneArena (144.9 rounded)
$bench->save();

echo "✅ Benchmarks updated!\n";
echo "   - DxOMark: 0\n";
echo "   - PhoneArena: 145\n";

// Recalculate CMS
echo "\n🔄 Recalculating CMS Score...\n";
// Force refresh to ensure relationship is loaded
$phone->refresh();
$phone->load('benchmarks');  // Load the updated benchmarks

$service = new CmsScoringService;
$score = $service->calculate($phone);

// Save Score
$phone->cms_score = $score['total_score'];
$phone->cms_details = $score['breakdown'];
$phone->save();

echo "🎉 New CMS Score: {$score['total_score']}/1330\n\n";

// Detailed Breakdown Highlight for Benchmarks
foreach ($score['breakdown'] as $key => $section) {
    if ($key === 'benchmarks') {
        echo strtoupper($key).": {$section['score']}/{$section['max']}\n";
        foreach ($section['details'] as $detail) {
            $pts = str_pad($detail['points'], 5, ' ', STR_PAD_LEFT);
            echo '  - '.str_pad($detail['criterion'], 25).": $pts pts ({$detail['reason']})\n";
        }
    }
}
