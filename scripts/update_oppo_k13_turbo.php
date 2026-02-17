<?php

require __DIR__ . '/../vendor/autoload.php';

use App\Models\Phone;
use App\Services\CmsScoringService;

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$phoneName = 'Oppo K13 Turbo Pro';
echo "📱 Finding phone: $phoneName...\n";
$phone = Phone::where('name', $phoneName)->first();

if (!$phone) {
    echo "❌ $phoneName not found!\n";
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
// Dual 50 MP, f/1.8, 27mm (wide), PDAF, OIS 2 MP
// Features LED flash, HDR, panorama
// Video 4K@30/60fps, 1080p@30fps

$camera->main_camera_specs = '50 MP, f/1.8, 27mm (wide), PDAF, OIS';
// The 2MP is likely a depth/macro sensor. We'll store it in main_camera_specs as a second line for display, 
// but currently the code parses 'ultrawide' or 'telephoto' separately.
// The user said "Dual... 2 MP". This implies the second camera is 2MP.
// A 2MP camera is usually not an ultrawide or telephoto worth scoring high points (often depth).
// However, to keep it stored properly:
$camera->main_camera_specs .= "\n2 MP (Depth/Macro)"; 
// Or better, if we have a place for 'secondary_camera_specs'? We usually use ultrawide/telephoto columns.
// If it's not ultrawide/telephoto, it might go into features or main specs string.
// Let's stick to appending to main_camera_specs to ensure it is seen.
// Also clear ultrawide/telephoto if they were there and incorrect, but the user said "missed", so implies adding.
// I will explicitly clear ultrawide/telephoto if this phone is known to ONLY have the 2MP secondary.
// Assuming "Dual" means exactly 2 cameras.
$camera->ultrawide_camera_specs = null; // 2MP is rarely ultrawide (usually 8MP+).
$camera->telephoto_camera_specs = null;

$camera->main_camera_features = 'LED flash, HDR, panorama';
$camera->main_video_capabilities = '4K@30/60fps, 1080p@30fps';

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
