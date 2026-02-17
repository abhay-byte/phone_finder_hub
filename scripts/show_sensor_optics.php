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
        echo "Total CMS Score: {$phone->cms_score}/1330\n\n";
        
        if ($phone->cms_details && isset($phone->cms_details['sensor_optics'])) {
            $so = $phone->cms_details['sensor_optics'];
            echo "🔬 SENSOR & OPTICS: {$so['score']}/{$so['max']} points\n";
            echo str_repeat('-', 70) . "\n";
            
            foreach ($so['details'] as $detail) {
                printf("%-35s %3d pts - %s\n", 
                    $detail['criterion'], 
                    $detail['points'], 
                    $detail['reason']
                );
            }
            
            echo "\n📊 Camera Specs:\n";
            echo str_repeat('-', 70) . "\n";
            if ($phone->camera) {
                echo "Main Camera: " . ($phone->camera->main_camera_specs ?? 'N/A') . "\n";
                echo "Sensors: " . ($phone->camera->main_camera_sensors ?? 'N/A') . "\n";
                echo "Apertures: " . ($phone->camera->main_camera_apertures ?? 'N/A') . "\n";
                echo "Features: " . substr($phone->camera->main_camera_features ?? 'N/A', 0, 100) . "...\n";
            }
        }
        echo "\n";
    }
}
