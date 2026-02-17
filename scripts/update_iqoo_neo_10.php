<?php

require __DIR__ . '/../vendor/autoload.php';

use App\Models\Phone;
use App\Services\CmsScoringService;

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$phoneName = 'vivo iQOO Neo 10';
echo "📱 Finding phone: $phoneName...\n";
$phone = Phone::where('name', $phoneName)->first();

if (!$phone) {
    echo "❌ $phoneName not found! Trying 'iQOO Neo 10'...\n";
    $phone = Phone::where('name', 'iQOO Neo 10')->first();
}

if (!$phone) {
    echo "❌ Phone not found via fallback either. Exiting.\n";
    exit(1);
}

echo "✅ Found {$phone->name} (ID: {$phone->id})\n";
echo "📱 Updating Camera Specs...\n";

// Update Camera Specs
$camera = $phone->camera;
if (!$camera) {
    echo "⚠️ No camera entry found, creating one...\n";
    $camera = new \App\Models\Camera();
    $camera->phone_id = $phone->id;
}

// User provided:
// Dual 50 MP, f/1.8, (wide), 1/1.95", 0.8µm, multi-directional PDAF, OIS
// 8 MP, f/2.2, (ultrawide)
// Features LED flash, HDR, panorama
// Video 4K@30/60fps, 1080p, gyro-EIS, OIS

$camera->main_camera_specs = '50 MP, f/1.8, (wide), 1/1.95", 0.8µm, multi-directional PDAF, OIS';
$camera->ultrawide_camera_specs = '8 MP, f/2.2, (ultrawide)';
$camera->main_camera_features = 'LED flash, HDR, panorama';
$camera->main_video_capabilities = '4K@30/60fps, 1080p, gyro-EIS, OIS';

// Save
$camera->save();
echo "✅ Camera specs updated.\n";

// Recalculate Score
echo "\n🔄 Recalculating CMS Score...\n";
$service = new CmsScoringService();
$score = $service->calculate($phone);

// Save Score
$phone->cms_score = $score['total_score'];
$phone->cms_details = $score['breakdown'];
$phone->save();

echo "🎉 New CMS Score: {$score['total_score']}\n";

// Show Breakdown for verification
foreach ($score['breakdown'] as $key => $section) {
    if (in_array($key, ['sensor_optics', 'resolution', 'focus_stability', 'video', 'fusion'])) {
        echo strtoupper(str_replace('_', ' ', $key)) . ": {$section['score']}/{$section['max']}\n";
        foreach ($section['details'] as $detail) {
            $pts = str_pad($detail['points'], 5, ' ', STR_PAD_LEFT);
            echo "  - " . str_pad($detail['criterion'], 30) . ": $pts pts ({$detail['reason']})\n";
        }
        echo "\n";
    }
}
