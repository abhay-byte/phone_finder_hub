<?php

require __DIR__ . '/../vendor/autoload.php';

use App\Models\Phone;

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$phones = ['OnePlus 13R', 'OnePlus 13', 'Poco X6 Pro'];

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
    echo "📊 RAW CAMERA DATA:\n";
    echo str_repeat('-', 80) . "\n";
    echo "Main Specs:\n" . ($camera->main_camera_specs ?? 'NULL') . "\n\n";
    echo "Telephoto Specs:\n" . ($camera->telephoto_camera_specs ?? 'NULL') . "\n\n";
    echo "Ultrawide Specs:\n" . ($camera->ultrawide_camera_specs ?? 'NULL') . "\n\n";
    echo "Front/Selfie Specs:\n" . ($camera->selfie_camera_specs ?? 'NULL') . "\n\n";
    echo "Features:\n" . ($camera->main_camera_features ?? 'NULL') . "\n\n";
    echo "Video Capabilities:\n" . ($camera->main_video_capabilities ?? 'NULL') . "\n\n";
    
    // Calculate and show detailed scoring
    echo "🎯 FOCUS & STABILITY SCORING BREAKDOWN (200 points max):\n";
    echo str_repeat('-', 80) . "\n\n";
    
    // Get CMS details
    if ($phone->cms_details && isset($phone->cms_details['focus_stability'])) {
        $fs = $phone->cms_details['focus_stability'];
        
        echo "TOTAL SCORE: {$fs['score']}/{$fs['max']} points\n\n";
        
        echo "DETAILED BREAKDOWN:\n";
        echo str_repeat('-', 80) . "\n";
        
        $autofocusTotal = 0;
        $stabilizationTotal = 0;
        
        foreach ($fs['details'] as $detail) {
            $criterion = str_pad($detail['criterion'], 30);
            $points = str_pad(number_format($detail['points'], 1), 6, ' ', STR_PAD_LEFT);
            $reason = $detail['reason'];
            
            echo "{$criterion} {$points} pts  →  {$reason}\n";
            
            // Categorize
            if (str_contains($detail['criterion'], 'AF') || str_contains($detail['criterion'], 'Autofocus')) {
                $autofocusTotal += $detail['points'];
            } else {
                $stabilizationTotal += $detail['points'];
            }
        }
        
        echo "\n" . str_repeat('-', 80) . "\n";
        echo "AUTOFOCUS SUBTOTAL:        " . number_format($autofocusTotal, 1) . "/100 points\n";
        echo "STABILIZATION SUBTOTAL:    " . number_format($stabilizationTotal, 1) . "/100 points\n";
        echo "TOTAL:                     " . number_format($fs['score'], 1) . "/200 points\n";
    }
    
    echo "\n" . str_repeat('=', 80) . "\n";
}

echo "\n\n";
echo "📖 SCORING LOGIC EXPLANATION:\n";
echo str_repeat('=', 80) . "\n\n";

echo "AUTOFOCUS SCORING (100 points total):\n";
echo "  • Main Camera AF (40 pts max):\n";
echo "      - Laser + Dual-Pixel: 40 pts (1.0 × 40)\n";
echo "      - Laser + Multi-PDAF: 36 pts (0.9 × 40)\n";
echo "      - Dual-Pixel AF:      32 pts (0.8 × 40)\n";
echo "      - Multi-PDAF:         28 pts (0.7 × 40)\n";
echo "      - PDAF:               20 pts (0.5 × 40)\n";
echo "      - Contrast AF:         8 pts (0.2 × 40)\n\n";

echo "  • Telephoto Camera AF (20 pts max):\n";
echo "      - Same ratios as main, but × 20\n";
echo "      - PDAF: 10 pts (0.5 × 20)\n";
echo "      - Contrast AF: 4 pts (0.2 × 20)\n\n";

echo "  • Ultrawide Camera AF (20 pts max):\n";
echo "      - Same ratios as main, but × 20\n\n";

echo "  • Front Camera AF (20 pts max):\n";
echo "      - Same ratios as main, but × 20\n\n";

echo "STABILIZATION SCORING (100 points total):\n";
echo "  • Main Camera OIS (40 pts):\n";
echo "      - Has OIS: 40 pts\n";
echo "      - No OIS: 0 pts\n\n";

echo "  • Telephoto Camera OIS (20 pts):\n";
echo "      - Has OIS: 20 pts\n";
echo "      - No OIS: 0 pts\n\n";

echo "  • Video Stabilization (20 pts):\n";
echo "      - Gyro-EIS: 20 pts\n";
echo "      - Standard EIS: 10 pts\n";
echo "      - None: 0 pts\n\n";

echo "  • Advanced Stabilization (20 pts):\n";
echo "      - Gimbal: 20 pts\n";
echo "      - Sensor-Shift OIS: 15 pts\n";
echo "      - Ultrawide OIS: 10 pts\n";
echo "      - None: 0 pts\n\n";

echo str_repeat('=', 80) . "\n";
