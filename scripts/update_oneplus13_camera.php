<?php

require __DIR__ . '/../vendor/autoload.php';

use App\Models\Phone;
use App\Models\SpecCamera;

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "🔄 Updating OnePlus 13 camera data...\n\n";

$phone = Phone::where('name', 'OnePlus 13')->first();

if (!$phone) {
    echo "❌ OnePlus 13 not found!\n";
    exit(1);
}

$camera = SpecCamera::where('phone_id', $phone->id)->first();

if (!$camera) {
    echo "❌ Camera record not found!\n";
    exit(1);
}

// Update camera specs with correct data
$camera->main_camera_specs = "50 MP, f/1.6, 23mm (wide), 1/1.43\", 1.12µm, multi-directional PDAF, OIS\n50 MP, f/2.6, 73mm (periscope telephoto), 1/1.95\", 0.8µm, 3x optical zoom, PDAF, OIS\n50 MP, f/2.0, 15mm, 120˚ (ultrawide), 1/2.75\", 0.64µm, PDAF";

$camera->main_camera_features = "Laser focus, Hasselblad Color Calibration, color spectrum sensor, Dual-LED flash, HDR, panorama";

$camera->main_video_capabilities = "8K@30fps, 4K@30/60fps, 1080p@30/60/240/480fps, Auto HDR, gyro-EIS, Dolby Vision";

// Update individual camera details
$camera->main_camera_sensors = "Main: 1/1.43\", Tele: 1/1.95\", UW: 1/2.75\"";
$camera->main_camera_apertures = "f/1.6 (main), f/2.6 (tele), f/2.0 (ultrawide)";

$camera->save();

echo "✅ Updated OnePlus 13 camera data:\n";
echo "   - Main specs: Multi-directional PDAF, OIS\n";
echo "   - Features: Laser focus, Hasselblad, Spectrum sensor\n";
echo "   - Video: 8K@30fps with gyro-EIS\n\n";

// Recalculate CMS score
echo "🔄 Recalculating CMS score...\n";

$result = \App\Services\CmsScoringService::calculate($phone);
$phone->cms_score = $result['total_score'];
$phone->cms_details = $result['breakdown'];
$phone->save();

echo "✅ New CMS Score: {$phone->cms_score}/1290\n\n";

// Show Focus & Stability breakdown
if (isset($result['breakdown']['focus_stability'])) {
    $fs = $result['breakdown']['focus_stability'];
    echo "🎯 Focus & Stability: {$fs['score']}/{$fs['max']} points\n";
    foreach ($fs['details'] as $detail) {
        echo "   - {$detail['criterion']}: {$detail['points']} pts ({$detail['reason']})\n";
    }
}

echo "\n✨ Update complete!\n";
