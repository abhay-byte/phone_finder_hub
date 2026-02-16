<?php

require __DIR__ . '/../vendor/autoload.php';

use App\Models\Phone;
use App\Services\CmsScoringService;

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$phoneName = 'vivo iQOO 15'; // Assuming name in DB is 'vivo iQOO 15' or 'iQOO 15'
$phone = Phone::where('name', $phoneName)->first();

if (!$phone) {
    echo "❌ $phoneName not found! Trying 'iQOO 15'...\n";
    $phone = Phone::where('name', 'iQOO 15')->first();
    if (!$phone) {
        echo "❌ iQOO 15 not found either!\n";
        exit;
    }
}

echo "📱 Updating Camera Specs for {$phone->name}...\n";

$camera = $phone->camera;
if (!$camera) {
    echo "⚠️ No camera entry found, creating one...\n";
    $camera = new \App\Models\Camera();
    $camera->phone_id = $phone->id;
}

// Update Specs
$camera->main_camera_specs = "50 MP, f/1.9, 24mm (wide), 1/1.56\", 1.0µm, PDAF, OIS\n" .
                             "50 MP, f/2.6, 85mm (periscope telephoto), 1/1.95\", 0.8µm, PDAF, OIS, 3x optical zoom\n" .
                             "50 MP, f/2.1, 15mm (ultrawide), 1/2.76\", 0.64µm, AF";

$camera->telephoto_camera_specs = "50 MP, f/2.6, 85mm (periscope telephoto), 1/1.95\", 0.8µm, PDAF, OIS, 3x optical zoom";
$camera->ultrawide_camera_specs = "50 MP, f/2.1, 15mm (ultrawide), 1/2.76\", 0.64µm, AF";

$camera->main_camera_features = "LED flash, HDR, panorama";
$camera->main_video_capabilities = "8K@30fps, 4K@24/30/60fps, 1080p@30/60/120/240fps, gyro-EIS";

$camera->selfie_camera_specs = "32 MP, f/2.2, 21mm (wide), 1/3.1\", 0.7µm";
$camera->selfie_camera_features = "HDR";
$camera->selfie_video_features = "4K@30/60fps, 1080p@30/60fps";

$camera->save();

echo "✅ Specs updated!\n";

// Recalculate
echo "\n🔄 Recalculating CMS Score...\n";
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
