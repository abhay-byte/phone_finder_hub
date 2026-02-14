<?php

use App\Models\Phone;
use App\Models\SpecBody;
use App\Models\SpecPlatform;
use App\Models\SpecCamera;
use App\Models\SpecConnectivity;
use App\Models\SpecBattery;
use App\Models\Benchmark;

// OnePlus 15 (ID 3)
$p15 = Phone::find(3);
if ($p15) {
    echo "Updating OnePlus 15...\n";

    // Body - Granular Updates
    SpecBody::updateOrCreate(['phone_id' => 3], [
        'screen_area' => '112.4 cm²',
        'aspect_ratio' => '19.5:9',
        'glass_protection_level' => 'Mohs level 5',
    ]);

    // Camera - Granular Updates
    SpecCamera::updateOrCreate(['phone_id' => 3], [
        'main_camera_zoom' => '3.5x optical zoom',
        'main_camera_pdaf' => 'Multi-directional PDAF (main), PDAF (tele + ultrawide)',
        'selfie_video_features' => '4K@30/60fps, Gyro-EIS',
    ]);

    // Connectivity - Granular Updates
    SpecConnectivity::updateOrCreate(['phone_id' => 3], [
        'positioning_details' => 'GPS (L1+L5), GLONASS (G1), BDS (B1I+B1c+B2a), GALILEO (E1+E5a), QZSS (L1+L5), NavIC (L5)',
        'has_3_5mm_jack' => false,
    ]);
}

echo "Done updating granular specs for OnePlus 15.\n";
