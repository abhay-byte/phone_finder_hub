<?php

require __DIR__ . '/../vendor/autoload.php';

use App\Models\Phone;

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$phones = ['OnePlus 13', 'Poco X6 Pro'];

foreach ($phones as $name) {
    $phone = Phone::where('name', $name)->with(['camera', 'benchmarks'])->first();
    
    if ($phone) {
        echo "\n" . str_repeat('=', 70) . "\n";
        echo "📱 {$phone->name}\n";
        echo str_repeat('=', 70) . "\n";
        echo "Total CMS Score: {$phone->cms_score}/1290\n\n";
        
        if ($phone->cms_details && isset($phone->cms_details['focus_stability'])) {
            $fs = $phone->cms_details['focus_stability'];
            echo "🎯 FOCUS & STABILITY: {$fs['score']}/{$fs['max']} points\n";
            echo str_repeat('-', 70) . "\n";
            
            foreach ($fs['details'] as $detail) {
                printf("%-40s %3d pts - %s\n", 
                    $detail['criterion'], 
                    $detail['points'], 
                    $detail['reason']
                );
            }
            
            echo "\n📊 Camera Focus & Stabilization Info:\n";
            echo str_repeat('-', 70) . "\n";
            if ($phone->camera) {
                echo "Features: " . substr($phone->camera->main_camera_features ?? 'N/A', 0, 200) . "...\n";
            }
        }
        echo "\n";
    }
}
