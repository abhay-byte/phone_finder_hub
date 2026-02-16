<?php

require __DIR__ . '/../vendor/autoload.php';

use App\Models\Phone;
use App\Services\CmsScoringService;

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$phoneName = 'vivo T4 Ultra';
$phone = Phone::where('name', $phoneName)->first();

if (!$phone) {
    echo "❌ $phoneName not found!\n";
    exit;
}

echo "📱 Debugging {$phone->name}...\n";

// Force Refresh
$phone->refresh();
$phone->load('camera');

echo "Camera Entry Exists: " . ($phone->camera ? 'Yes' : 'No') . "\n";
echo "Main Specs: " . substr($phone->camera->main_camera_specs, 0, 50) . "...\n";
echo "Tele Specs: " . ($phone->camera->telephoto_camera_specs ? 'Present' : 'Empty') . "\n";
echo "UW Specs: " . ($phone->camera->ultrawide_camera_specs ? 'Present' : 'Empty') . "\n";

// Recalculate
echo "\n🔄 Recalculating CMS Score with Refreshed Data...\n";
$service = new CmsScoringService();
$score = $service->calculate($phone);

// Save Score
$phone->cms_score = $score['total_score'];
$phone->cms_details = $score['breakdown'];
$phone->save();

echo "🎉 New CMS Score: {$score['total_score']}/1330\n\n";

// Detailed Breakdown
foreach ($score['breakdown'] as $key => $section) {
    if (in_array($key, ['sensor_optics', 'resolution', 'focus_stability', 'video', 'fusion'])) {
        echo strtoupper(str_replace('_', ' ', $key)) . ": {$section['score']}/{$section['max']}\n";
        foreach ($section['details'] as $detail) {
            $pts = str_pad($detail['points'], 5, ' ', STR_PAD_LEFT);
            echo "  - " . str_pad($detail['criterion'], 25) . ": $pts pts ({$detail['reason']})\n";
        }
        echo "\n";
    }
}
