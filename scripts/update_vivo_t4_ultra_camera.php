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
    echo "❌ $phoneName not found! Searching...\n";
    $phone = Phone::where('name', 'LIKE', '%T4 Ultra%')->first();
    if (!$phone) {
        echo "❌ vivo T4 Ultra not found!\n";
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
$camera->main_camera_specs = "50 MP, f/1.9, 23mm (wide), 1/1.56\", PDAF, OIS\n" .
                             "50 MP, f/2.6, 85mm (periscope telephoto), 1/1.95\", 0.8µm, PDAF, OIS, 3x optical zoom\n" .
                             "8 MP, f/2.2, (ultrawide)";

$camera->telephoto_camera_specs = "50 MP, f/2.6, 85mm (periscope telephoto), 1/1.95\", 0.8µm, PDAF, OIS, 3x optical zoom";
$camera->ultrawide_camera_specs = "8 MP, f/2.2, (ultrawide)";

$camera->main_camera_features = "Ring-LED flash, panorama, HDR";
$camera->main_video_capabilities = "4K, 1080p, gyro-EIS";

$camera->selfie_camera_specs = "32 MP, f/2.5, (wide)";
$camera->selfie_camera_features = ""; 
$camera->selfie_video_features = "1080p"; // "Yes" usually implies standard recording

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
