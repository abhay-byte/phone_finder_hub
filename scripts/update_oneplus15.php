<?php

require __DIR__.'/../vendor/autoload.php';

use App\Models\Phone;
use App\Services\CmsScoringService;

$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$phoneName = 'OnePlus 15';
$phone = Phone::where('name', $phoneName)->first();

if (! $phone) {
    echo "❌ $phoneName not found!\n";
    exit;
}

echo "📱 Updating Camera Specs for {$phone->name}...\n";

$camera = $phone->camera;
if (! $camera) {
    echo "⚠️ No camera entry found, creating one...\n";
    $camera = new \App\Models\Camera;
    $camera->phone_id = $phone->id;
}

// Update Specs
$camera->main_camera_specs = "50 MP, f/1.8, 24mm (wide), 1/1.56\", 1.0µm, multi-directional PDAF, OIS\n".
                             "50 MP, f/2.8, 80mm (periscope telephoto), 1/2.76\", 0.64µm, 3.5x optical zoom, PDAF, OIS\n".
                             '50 MP, f/2.0, 16mm, 116˚ (ultrawide), 1/2.88", 0.61µm, PDAF';

$camera->telephoto_camera_specs = '50 MP, f/2.8, 80mm (periscope telephoto), 1/2.76", 0.64µm, 3.5x optical zoom, PDAF, OIS';
$camera->ultrawide_camera_specs = '50 MP, f/2.0, 16mm, 116˚ (ultrawide), 1/2.88", 0.61µm, PDAF';

$camera->main_camera_features = 'Laser focus, color spectrum sensor, LED flash, HDR, panorama, LUT preview';
$camera->main_video_capabilities = '8K@30fps, 4K@30/60/120fps, 1080p@30/60/240fps, Auto HDR, gyro-EIS, Dolby Vision, LUT';

$camera->selfie_camera_specs = '32 MP, f/2.4, 21mm (wide), 1/2.74", 0.64µm, AF';
$camera->selfie_camera_features = 'HDR, panorama';
$camera->selfie_video_features = '4K@30/60fps, 1080p@30/60fps, gyro-EIS, HDR';

$camera->save();

echo "✅ Specs updated!\n";

// Recalculate
echo "\n🔄 Recalculating CMS Score...\n";
$service = new CmsScoringService;
$score = $service->calculate($phone);

// Save Score
$phone->cms_score = $score['total_score'];
$phone->cms_details = $score['breakdown'];
$phone->save();

echo "🎉 New CMS Score: {$score['total_score']}/1330\n\n";

// Detailed Breakdown
foreach ($score['breakdown'] as $key => $section) {
    if (in_array($key, ['sensor_optics', 'resolution', 'focus_stability', 'video', 'fusion'])) {
        echo strtoupper(str_replace('_', ' ', $key)).": {$section['score']}/{$section['max']}\n";
        foreach ($section['details'] as $detail) {
            $pts = str_pad($detail['points'], 5, ' ', STR_PAD_LEFT);
            echo '  - '.str_pad($detail['criterion'], 25).": $pts pts ({$detail['reason']})\n";
        }
        echo "\n";
    }
}
