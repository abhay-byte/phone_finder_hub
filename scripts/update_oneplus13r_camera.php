<?php

require __DIR__ . '/../vendor/autoload.php';

use App\Models\Phone;
use App\Models\SpecCamera;

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "🔄 Updating OnePlus 13R camera data...\n\n";

$phone = Phone::where('name', 'OnePlus 13R')->first();

if (!$phone) {
    echo "❌ OnePlus 13R not found!\n";
    exit(1);
}

$camera = SpecCamera::where('phone_id', $phone->id)->first();

if (!$camera) {
    echo "❌ Camera record not found!\n";
    exit(1);
}

// Update camera specs with correct detailed data
$camera->main_camera_specs = "50 MP, f/1.8, 24mm (wide), 1/1.56\", 1.0µm, multi-directional PDAF, OIS\n50 MP, f/2.0, 47mm (telephoto), 1/2.75\", 0.64µm, PDAF, 2x optical zoom\n8 MP, f/2.2, 16mm, 112˚ (ultrawide), 1/4.0\", 1.12µm";

$camera->main_camera_features = "Color spectrum sensor, LED flash, HDR, panorama";

$camera->main_video_capabilities = "4K@30/60fps, 1080p@30/60/120/240fps, gyro-EIS, OIS";

// Update individual camera details
$camera->main_camera_sensors = "Main: 1/1.56\", Tele: 1/2.75\", UW: 1/4.0\"";
$camera->main_camera_apertures = "f/1.8 (main), f/2.0 (tele), f/2.2 (ultrawide)";

// Populate separate telephoto and ultrawide fields for granular scoring
$camera->telephoto_camera_specs = "50 MP, f/2.0, 47mm (telephoto), 1/2.75\", 0.64µm, PDAF, 2x optical zoom";
$camera->ultrawide_camera_specs = "8 MP, f/2.2, 16mm, 112˚ (ultrawide), 1/4.0\", 1.12µm";

$camera->save();

echo "✅ Updated OnePlus 13R camera data:\n";
echo "   - Main: 50MP f/1.8, 1/1.56\", multi-directional PDAF, OIS\n";
echo "   - Telephoto: 50MP f/2.0, 2x zoom, PDAF\n";
echo "   - Ultrawide: 8MP f/2.2, 112°\n";
echo "   - Features: Color spectrum sensor\n";
echo "   - Video: 4K@60fps with gyro-EIS + OIS\n\n";

// Recalculate CMS score
echo "🔄 Recalculating CMS score...\n";

$result = \App\Services\CmsScoringService::calculate($phone);
$phone->cms_score = $result['total_score'];
$phone->cms_details = $result['breakdown'];
$phone->save();

echo "✅ New CMS Score: {$phone->cms_score}/1370\n\n";

// Show detailed breakdown
if (isset($result['breakdown']['sensor_optics'])) {
    $so = $result['breakdown']['sensor_optics'];
    echo "🔬 Sensor & Optics: {$so['score']}/{$so['max']} points\n";
}

if (isset($result['breakdown']['focus_stability'])) {
    $fs = $result['breakdown']['focus_stability'];
    echo "🎯 Focus & Stability: {$fs['score']}/{$fs['max']} points\n";
    foreach ($fs['details'] as $detail) {
        echo "   - {$detail['criterion']}: {$detail['points']} pts ({$detail['reason']})\n";
    }
}

echo "\n✨ Update complete!\n";
