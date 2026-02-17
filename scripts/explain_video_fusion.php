<?php

require __DIR__ . '/../vendor/autoload.php';

use App\Models\Phone;

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$phones = ['OnePlus 13R', 'OnePlus 13', 'Poco X6 Pro', 'vivo V60'];

foreach ($phones as $name) {
    $phone = Phone::where('name', $name)->with(['camera'])->first();
    
    if (!$phone) {
        echo "❌ $name not found!\n";
        continue;
    }
    
    echo "\n" . str_repeat('=', 80) . "\n";
    echo "📱 {$phone->name}\n";
    echo str_repeat('=', 80) . "\n\n";
    
    $camera = $phone->camera;
    
    // Display raw camera data
    echo "📊 RAW DATA:\n";
    echo str_repeat('-', 80) . "\n";
    echo "Video Specs:      " . ($camera->main_video_capabilities ?? 'NULL') . "\n";
    echo "Selfie Video:     " . ($camera->selfie_video_features ?? 'NULL') . "\n";
    echo "Main Specs:       " . ($camera->main_camera_specs ?? 'NULL') . "\n";
    echo "Telephoto Specs:  " . ($camera->telephoto_camera_specs ?? 'NULL') . "\n";
    echo "Ultrawide Specs:  " . ($camera->ultrawide_camera_specs ?? 'NULL') . "\n\n";
    
    // Get CMS details
    if ($phone->cms_details) {
        
        // VIDEO SYSTEM BREAKDOWN
        if (isset($phone->cms_details['video'])) {
            $video = $phone->cms_details['video'];
            echo "🎥 VIDEO SYSTEM (200 points max):\n";
            echo str_repeat('-', 80) . "\n";
            echo "TOTAL: {$video['score']}/{$video['max']} points\n\n";
            
            foreach ($video['details'] as $detail) {
                $criterion = str_pad($detail['criterion'], 30);
                $points = str_pad(number_format($detail['points'], 1), 6, ' ', STR_PAD_LEFT);
                $reason = $detail['reason'];
                echo "{$criterion} {$points} pts  →  {$reason}\n";
            }
            echo "\n";
        }
        
        // FUSION BREAKDOWN
        if (isset($phone->cms_details['fusion'])) {
            $fusion = $phone->cms_details['fusion'];
            echo "🔄 MULTI-CAMERA FUSION (200 points max):\n";
            echo str_repeat('-', 80) . "\n";
            echo "TOTAL: {$fusion['score']}/{$fusion['max']} points\n\n";
            
            foreach ($fusion['details'] as $detail) {
                $criterion = str_pad($detail['criterion'], 30);
                $points = str_pad(number_format($detail['points'], 1), 6, ' ', STR_PAD_LEFT);
                $reason = $detail['reason'];
                echo "{$criterion} {$points} pts  →  {$reason}\n";
            }
        }
    }
    
    echo "\n" . str_repeat('=', 80) . "\n";
}

echo "\n\n";
echo "📖 SCORING LOGIC EXPLANATION:\n";
echo str_repeat('=', 80) . "\n\n";

echo "🎥 VIDEO SYSTEM (200 pts):\n";
echo "  • Resolution & Frame Rate (Max 100pts):\n";
echo "      - 8K Video: 100 pts\n";
echo "      - 4K @ 60fps: 80 pts\n";
echo "      - 4K @ 30fps: 50 pts\n";
echo "      - 1080p @ 60fps: 30 pts\n";
echo "      - 1080p @ 30fps: 20 pts\n\n";

echo "  • Video Features (Max 60pts):\n";
echo "      - Dolby Vision / HDR10+: 30 pts\n";
echo "      - 10-bit color: 20 pts\n";
echo "      - LOG/RAW video: 20 pts\n";
echo "      - Cinematic Mode: 10 pts\n";
echo "      - Slow Motion (960fps+): 20 pts\n\n";

echo "  • Selfie Video (Max 40pts):\n";
echo "      - 4K Selfie Video: 40 pts\n";
echo "      - 1080p @ 60fps: 30 pts\n";
echo "      - 1080p @ 30fps: 10 pts\n\n";

echo "🔄 MULTI-CAMERA FUSION (200 pts):\n";
echo "  • Focal Length Coverage (Max 80pts):\n";
echo "      - Ultra-wide (<16mm) + Telephoto (>50mm): 80 pts\n";
echo "      - Ultra-wide + Main: 40 pts\n";
echo "      - Main Only: 10 pts\n\n";

echo "  • Zoom Capabilities (Max 80pts):\n";
echo "      - Periscope (5x+): 80 pts\n";
echo "      - Periscope (3x-4.9x) or High-Res Crop: 60 pts\n";
echo "      - Telephoto (2x-3x): 40 pts\n";
echo "      - Digital Zoom only: 0 pts\n\n";

echo "  • Macro Capabilities (Max 20pts):\n";
echo "      - Tele-Macro (High End): 20 pts\n";
echo "      - Ultrawide Macro (Mid Range): 15 pts\n";
echo "      - Dedicated 2MP/5MP Macro (Low End): 10 pts\n\n";

echo "  • Consistency & Color (Max 20pts):\n";
echo "      - Hasselblad/Leica/Zeiss Partnership: 20 pts\n";
echo "      - Color Spectrum Sensor: 15 pts\n";
echo "      - Standard: 0 pts\n\n";

echo str_repeat('=', 80) . "\n";
