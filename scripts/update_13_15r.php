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
    $p13->update([
        'release_date' => '2024-11-01',
        'announced_date' => '2024-10-31',
        'image_url' => '/storage/phones/oneplus-13.jpg', // Placeholder or keep existing
    ]);

    // Body
    SpecBody::updateOrCreate(['phone_id' => 1], [
        'dimensions' => '162.9 x 76.5 x 8.5 mm or 8.9 mm',
        'weight' => '210 g or 213 g (7.41 oz)',
        'build_material' => 'Glass front (Ceramic Guard), glass back or silicone polymer back (eco leather), aluminum frame',
        'sim' => 'Nano-SIM + Nano-SIM + eSIM (max 2 at a time) or Nano-SIM + Nano-SIM',
        'water_resistance' => 'IP68/IP69 dust tight and water resistant (high pressure water jets; immersible up to 1.5m for 30 min)',
        'display_type' => 'LTPO 4.1 AMOLED, 1B colors, 120Hz, 2160Hz PWM, Dolby Vision, HDR10+, HDR Vivid, 800 nits (typ), 1600 nits (HBM), 4500 nits (peak)',
        'display_size' => '6.82 inches, 113.0 cm2 (~90.7% screen-to-body ratio)',
        'display_resolution' => '1440 x 3168 pixels (~510 ppi density)',
        'display_protection' => 'Ceramic Guard glass, Mohs level 4',
        'measured_display_brightness' => '1204 nits max brightness (measured)',
    ]);

    // Platform
    SpecPlatform::updateOrCreate(['phone_id' => 1], [
        'os' => 'Android 15, up to 4 major Android upgrades, OxygenOS 16 (International), ColorOS 16 (China)',
        'chipset' => 'Qualcomm SM8750-AB Snapdragon 8 Elite (3 nm)',
        'cpu' => 'Octa-core (2x4.32 GHz Oryon V2 Phoenix L + 6x3.53 GHz Oryon V2 Phoenix M)',
        'gpu' => 'Adreno 830',
        'memory_slot' => 'No',
        'memory_internal' => '256GB 12GB RAM, 512GB 12GB RAM, 512GB 16GB RAM, 1TB 24GB RAM; UFS 4.0',
    ]);

    // Camera
    SpecCamera::updateOrCreate(['phone_id' => 1], [
        'main_camera_type' => 'Triple',
        'main_camera_spec' => "50 MP, f/1.6, 23mm (wide), 1/1.43\", 1.12µm, multi-directional PDAF, OIS\n50 MP, f/2.6, 73mm (periscope telephoto), 1/1.95\", 0.8µm, 3x optical zoom, PDAF, OIS\n50 MP, f/2.0, 15mm, 120˚ (ultrawide), 1/2.75\", 0.64µm, PDAF",
        'main_camera_features' => 'Laser focus, Hasselblad Color Calibration, color spectrum sensor, Dual-LED flash, HDR, panorama',
        'main_camera_video' => '8K@30fps, 4K@30/60fps, 1080p@30/60/240/480fps, Auto HDR, gyro-EIS, Dolby Vision',
        'selfie_camera_type' => 'Single',
        'selfie_camera_spec' => '32 MP, f/2.4, 21mm (wide), 1/2.74", 0.8µm',
        'selfie_camera_features' => 'HDR, panorama',
        'selfie_camera_video' => '4K@30/60fps, 1080p@30/60fps, gyro-EIS',
    ]);

    // Connectivity
    SpecConnectivity::updateOrCreate(['phone_id' => 1], [
        'wlan' => 'Wi-Fi 802.11 a/b/g/n/ac/6/7, dual or tri-band, Wi-Fi Direct',
        'bluetooth' => '5.4, A2DP, LE, aptX HD, LHDC 5',
        'positioning' => 'GPS (L1+L5), GLONASS (G1), BDS (B1I+B1c+B2a), GALILEO (E1+E5a), QZSS (L1+L5), NavIC',
        'nfc' => 'Yes',
        'radio' => 'No',
        'usb' => 'USB Type-C 3.2, OTG',
        'sensors' => 'Fingerprint (under display, ultrasonic), accelerometer, gyro, proximity, compass, barometer',
        'audio_quality' => '24-bit/192kHz Hi-Res audio',
        'loudspeaker' => 'Yes, with stereo speakers',
        'loudness_test_result' => '-23.5 LUFS (Very good)',
    ]);

    // Battery
    SpecBattery::updateOrCreate(['phone_id' => 1], [
        'battery_type' => 'Si/C Li-Ion 6000 mAh',
        'charging' => 'non-removable', // Assuming this field name based on previous errors/logs
        'charging_specs_detailed' => "100W wired, PD, QC, 50% in 13 min, 100% in 36 min\n50W wireless\n10W reverse wireless\n5W reverse wired",
    ]);

    // Benchmark
    Benchmark::updateOrCreate(['phone_id' => 1], [
        'antutu_score' => '2690491', // v10
        'geekbench_score' => '9278', // v6
        'dxomark_score' => null, 
        '3dmark_score' => '6615', // Wild Life Extreme
        'battery_endurance' => '61:36', // 61h 36m
        'battery_active_use_score' => '15:28', // 15h 28m
        'energy_label' => 'Class A',
        'free_fall_rating' => 'Class D (45 falls)',
        'repairability_score' => 'Class B', // Storing the class letter
    ]);
}

// OnePlus 15R (ID 2)
$p15r = Phone::find(2);
if ($p15r) {
    echo "Updating OnePlus 15R...\n";
    $p15r->update([
        'release_date' => '2025-12-22',
        'announced_date' => '2025-12-17',
        'image_url' => '/storage/phones/oneplus-15r.jpg', // Placeholder or keep existing
    ]);

    // Body
    SpecBody::updateOrCreate(['phone_id' => 2], [
        'dimensions' => '163.4 x 77 x 8.1 mm or 8.3 mm',
        'weight' => '213 g or 219 g (7.51 oz)',
        'build_material' => 'Glass front (Gorilla Glass 7i), aluminum alloy frame, glass back or fiber-reinforced plastic back',
        'sim' => 'Nano-SIM + Nano-SIM + eSIM (max 2 at a time) or Nano-SIM + Nano-SIM',
        'water_resistance' => 'IP68/IP69K dust tight and water resistant (high pressure water jets; immersible up to 1.5m for 30 min)',
        'display_type' => 'AMOLED, 1B colors, 165Hz, PWM, Dolby Vision, HDR10+, HDR Vivid, 1800 nits (HBM), 3600 nits (peak)',
        'display_size' => '6.83 inches, 113.3 cm2 (~90.1% screen-to-body ratio)',
        'display_resolution' => '1272 x 2800 pixels (~450 ppi density)',
        'display_protection' => 'Corning Gorilla Glass 7i, Mohs level 5',
        'measured_display_brightness' => '1204 nits max brightness (measured)',
    ]);

    // Platform
    SpecPlatform::updateOrCreate(['phone_id' => 2], [
        'os' => 'Android 16, OxygenOS 16',
        'chipset' => 'Qualcomm SM8845 Snapdragon 8 Gen 5 (3 nm)',
        'cpu' => 'Octa-core (2x3.8 GHz Oryon V3 Phoenix L + 6x3.32 GHz Oryon V3 Phoenix M)',
        'gpu' => 'Adreno 829',
        'memory_slot' => 'No',
        'memory_internal' => '256GB 12GB RAM, 512GB 12GB RAM; UFS 4.1',
    ]);

    // Camera
    SpecCamera::updateOrCreate(['phone_id' => 2], [
        'main_camera_type' => 'Dual',
        'main_camera_spec' => "50 MP, f/1.8, 24mm (wide), 1/1.56\", 1.0µm, PDAF, OIS\n8 MP, f/2.2, 16mm, 112˚ (ultrawide), 1/4.0\", 1.12µm",
        'main_camera_features' => 'Color spectrum sensor, LED flash, HDR, panorama',
        'main_camera_video' => '4K@30/60/120fps, 1080p@30/60/120/240fps, gyro-EIS, OIS',
        'selfie_camera_type' => 'Single',
        'selfie_camera_spec' => '32 MP, f/2.0, 25mm (wide), AF',
        'selfie_camera_features' => 'HDR, panorama',
        'selfie_camera_video' => '4K@30fps, 1080p@30fps, gyro-EIS, OIS',
    ]);

    // Connectivity
    SpecConnectivity::updateOrCreate(['phone_id' => 2], [
        'wlan' => 'Wi-Fi 802.11 a/b/g/n/ac/6/7, dual-band',
        'bluetooth' => '6.0, A2DP, LE, aptX HD, LHDC 5',
        'positioning' => 'GPS (L1+L5), GLONASS (G1), BDS (B1I+B1c+B2a), GALILEO (E1+E5a), QZSS (L1+L5), NavIC (L5)',
        'nfc' => 'Yes',
        'radio' => 'No',
        'usb' => 'USB Type-C 2.0, OTG',
        'sensors' => 'Fingerprint (under display, ultrasonic), accelerometer, gyro, proximity, compass',
        'loudspeaker' => 'Yes, with stereo speakers',
        'loudness_test_result' => '-25.7 LUFS (Very good)',
    ]);

    // Battery
    SpecBattery::updateOrCreate(['phone_id' => 2], [
        'battery_type' => 'Si/C Li-Ion 7400 mAh',
        'charging' => 'non-removable', 
        'charging_specs_detailed' => '80W wired',
    ]);

    // Benchmark
    Benchmark::updateOrCreate(['phone_id' => 2], [
        'antutu_score' => '1954910', // v10 (taking the lower one as safer bet or concatenate?) User provided 1954910 (v10), 2981677 (v11). I'll use v10.
        'geekbench_score' => '9369', // v6
        'dxomark_score' => null, 
        '3dmark_score' => '5016', // Wild Life Extreme
        'battery_endurance' => '77:51', // 77h 51m
        'battery_active_use_score' => '21:36', // 21h 36m
        'energy_label' => 'Class A',
        'free_fall_rating' => 'Class D (45 falls)',
        'repairability_score' => 'Class B',
    ]);
}

echo "Done updating OnePlus 13 and 15R.\n";
