<?php

use App\Models\Phone;
use App\Models\SpecBody;
use App\Models\SpecPlatform;
use App\Models\SpecCamera;
use App\Models\SpecConnectivity;
use App\Models\SpecBattery;
use App\Models\Benchmark;

// OnePlus 13 (ID 1)
$p13 = Phone::find(1);
if ($p13) {
    echo "Updating OnePlus 13...\n";
    
    // Body - Granular Updates
    SpecBody::updateOrCreate(['phone_id' => 1], [
        'screen_area' => '113.0 cm²', 
        'aspect_ratio' => '20:9', // Assuming standard, check if specific
        'glass_protection_level' => 'Mohs level 4',
    ]);

    // Camera - Granular Updates
    SpecCamera::updateOrCreate(['phone_id' => 1], [
        'main_camera_zoom' => '3x optical zoom',
        'main_camera_pdaf' => 'Multi-directional PDAF (main), PDAF (tele + ultrawide)',
        'selfie_video_features' => '4K@30/60fps, 1080p@30/60fps, gyro-EIS',
    ]);

    // Connectivity - Granular Updates
    SpecConnectivity::updateOrCreate(['phone_id' => 1], [
        'positioning_details' => 'GPS (L1+L5), GLONASS (G1), BDS (B1I+B1c+B2a), GALILEO (E1+E5a), QZSS (L1+L5), NavIC',
        'has_3_5mm_jack' => false,
    ]);
}

// OnePlus 15R (ID 2)
$p15r = Phone::find(2);
if ($p15r) {
    echo "Updating OnePlus 15R...\n";
    
    // Body - Granular Updates
    SpecBody::updateOrCreate(['phone_id' => 2], [
        'screen_area' => '113.3 cm²',
        'aspect_ratio' => '20:9',
        'glass_protection_level' => 'Mohs level 5',
    ]);
    
    // Camera - Granular Updates
    SpecCamera::updateOrCreate(['phone_id' => 2], [
        'main_camera_zoom' => 'No telephoto', // Or leave null if n/a
        'main_camera_pdaf' => 'PDAF',
        'selfie_video_features' => '4K@30fps, 1080p@30fps, gyro-EIS, OIS',
    ]);

    // Connectivity - Granular Updates
    SpecConnectivity::updateOrCreate(['phone_id' => 2], [
        'positioning_details' => 'GPS (L1+L5), GLONASS (G1), BDS (B1I+B1c+B2a), GALILEO (E1+E5a), QZSS (L1+L5), NavIC (L5)',
        'has_3_5mm_jack' => false,
    ]);
}

echo "Done updating granular specs for OnePlus 13 and 15R.\n";
