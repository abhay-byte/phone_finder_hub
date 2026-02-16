<?php

require __DIR__ . '/../vendor/autoload.php';

use App\Models\Phone;
use App\Services\CmsScoringService;

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$phoneName = 'Poco F7';
$phone = Phone::where('name', $phoneName)->first();

if (!$phone) {
    echo "❌ $phoneName not found!\n";
    exit;
}

echo "📱 Updating Camera Specs for {$phone->name}...\n";

// Update Camera Specs to correct Dual setup
$camera = $phone->camera;
if (!$camera) {
    echo "⚠️ No camera entry found, creating one...\n";
    $camera = new \App\Models\Camera();
    $camera->phone_id = $phone->id;
}

// Correct Dual Camera Specs
$camera->main_camera_specs = "50 MP, f/1.5, 26mm (wide), 1/1.95\", 0.8µm, PDAF, OIS\n" .
                             "8 MP, f/2.2, 15mm (ultrawide), 1/4.0\", 1.12µm";

// Clear Telephoto (It's a dual camera phone)
$camera->telephoto_camera_specs = null;

// Set Ultrawide explicitly
$camera->ultrawide_camera_specs = "8 MP, f/2.2, 15mm (ultrawide), 1/4.0\", 1.12µm";

$camera->main_camera_features = "LED flash, HDR, panorama";
$camera->main_video_capabilities = "4K@30/60fps, 1080p@30/60/120/240/960fps, gyro-EIS";

// Assuming standard selfie if not provided, or keep existing. 
// User didn't specify selfie, so I'll leave it as is or set a reasonable default if null.
// Let's print what it is currently.
echo "Current Selfie: {$camera->selfie_camera_specs}\n";

$camera->save();

echo "✅ Camera specs updated successfully!\n";
echo "Main: " . str_replace("\n", " + ", $camera->main_camera_specs) . "\n";

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
