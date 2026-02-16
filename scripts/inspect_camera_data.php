<?php

use App\Models\Phone;

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$phones = Phone::with('camera')->take(5)->get();

foreach ($phones as $phone) {
    echo "Phone: {$phone->name}\n";
    if ($phone->camera) {
        $cam = $phone->camera;
        echo "  Main Specs: " . ($cam->main_camera_specs ?? 'N/A') . "\n";
        echo "  Sensors: " . ($cam->main_camera_sensors ?? 'N/A') . "\n";
        echo "  Apertures: " . ($cam->main_camera_apertures ?? 'N/A') . "\n";
        echo "  Features: " . ($cam->main_camera_features ?? 'N/A') . "\n";
        echo "  Video: " . ($cam->main_video_capabilities ?? 'N/A') . "\n";
        echo "  OIS: " . ($cam->main_camera_ois ?? 'N/A') . "\n";
        echo "  PDAF: " . ($cam->main_camera_pdaf ?? 'N/A') . "\n";
    } else {
        echo "  No camera specs found.\n";
    }
    echo "--------------------------------------------------\n";
}
