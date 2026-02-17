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
        echo "Total CMS Score: {$phone->cms_score}/1370\n\n";
        
        if ($phone->cms_details && isset($phone->cms_details['sensor_optics'])) {
            $so = $phone->cms_details['sensor_optics'];
            echo "🔬 SENSOR & OPTICS: {$so['score']}/{$so['max']} points\n";
            echo str_repeat('-', 70) . "\n";
            
            foreach ($so['details'] as $detail) {
                printf("%-40s %3d pts - %s\n", 
                    $detail['criterion'], 
                    $detail['points'], 
                    $detail['reason']
                );
            }
            
            echo "\n📊 Camera Info:\n";
            echo str_repeat('-', 70) . "\n";
            if ($phone->camera) {
                echo "Rear Main: " . substr($phone->camera->main_camera_specs ?? 'N/A', 0, 80) . "...\n";
                echo "Front/Selfie: " . ($phone->camera->selfie_camera_specs ?? 'N/A') . "\n";
            }
        }
        echo "\n";
    }
}
