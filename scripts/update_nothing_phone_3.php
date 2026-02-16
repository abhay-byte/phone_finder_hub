<?php

require __DIR__ . '/../vendor/autoload.php';

use App\Models\Phone;
use App\Models\Camera; // Assuming Camera model exists or relationship is directly on phone
use App\Services\CmsScoringService;

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$phoneName = 'Nothing Phone (3)';
$phone = Phone::where('name', $phoneName)->first();

if (!$phone) {
    echo "❌ $phoneName not found!\n";
    exit;
}

echo "📱 Updating Camera Specs for {$phone->name}...\n";

// Update Camera Specs
$camera = $phone->camera;
if (!$camera) {
    echo "⚠️ No camera entry found, creating one...\n";
    $camera = new \App\Models\Camera();
    $camera->phone_id = $phone->id;
}

// Map the provided specs to the database fields
$camera->main_camera_specs = "50 MP, f/1.7, 24mm (wide), 1/1.3\", PDAF, OIS\n" .
                             "50 MP, f/2.7, (periscope telephoto), 1/2.75\", PDAF, 3x optical zoom, OIS\n" .
                             "50 MP, f/2.2, 114˚ (ultrawide), 1/2.76\"";

$camera->telephoto_camera_specs = "50 MP, f/2.7, (periscope telephoto), 1/2.75\", PDAF, 3x optical zoom, OIS";
$camera->ultrawide_camera_specs = "50 MP, f/2.2, 114˚ (ultrawide), 1/2.76\"";

$camera->main_camera_features = "LED flash, panorama, HDR";
$camera->main_video_capabilities = "4K@30/60fps, 1080p@30/60fps, gyro-EIS, OIS";

$camera->selfie_camera_specs = "50 MP, f/2.2, (wide), 1/2.76\"";
$camera->selfie_camera_features = "HDR";
$camera->selfie_video_features = "4K@60fps, 1080p@60fps";

$camera->save();

echo "✅ Camera specs updated successfully!\n";
echo "Main: " . str_replace("\n", " + ", $camera->main_camera_specs) . "\n";
echo "Selfie: {$camera->selfie_camera_specs}\n";

// Recalculate Score
echo "\n🔄 Recalculating CMS Score...\n";
$service = new CmsScoringService();
$score = $service->calculate($phone);

// Save Score
$phone->cms_score = $score['total_score'];
$phone->cms_details = $score['breakdown'];
$phone->save();

echo "🎉 New CMS Score: {$score['total_score']}/1330\n\n";

// Show Breakdown
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
