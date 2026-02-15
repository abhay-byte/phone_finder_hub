<?php

use App\Models\Phone;

// ==========================================
// OnePlus 13 (Database ID: 2)
// Source: scripts/update_13_15r.php (mapped from ID 1 in script)
// ==========================================
$p13 = Phone::find(2);
if ($p13) {
    echo "Updating OnePlus 13 (ID 2)...\n";
    $p13->update([
        'release_date' => '2024-11-01',
        'announced_date' => '2024-10-31',
        // 'image_url' => '/storage/phones/oneplus-13.jpg', // Keeping existing if valid
    ]);

    $p13->body()->updateOrCreate([], [
        'dimensions' => '162.9 x 76.5 x 8.5 mm',
        'weight' => '210 g',
        'build_material' => 'Glass front (Ceramic Guard), glass back or silicone polymer back (eco leather), aluminum frame',
        'sim' => 'Nano-SIM + Nano-SIM + eSIM',
        'ip_rating' => 'IP68/IP69',
        'display_type' => 'LTPO 4.1 AMOLED, 1B colors, 120Hz, Dolby Vision, HDR10+, HDR Vivid',
        'display_size' => '6.82 inches',
        'display_resolution' => '1440 x 3168 pixels',
        'display_protection' => 'Ceramic Guard glass',
        'display_features' => '4500 nits (peak), 1600 nits (HBM), 800 nits (typ), 2160Hz PWM',
        'display_brightness' => '4500 nits (peak)',
        'pwm_dimming' => '2160Hz PWM',
        'measured_display_brightness' => '1204 nits',
        'screen_to_body_ratio' => '~90.7%',
        'pixel_density' => '~510 ppi',
        'screen_glass' => 'Ceramic Guard glass',
    ]);

    $p13->platform()->updateOrCreate([], [
        'os' => 'Android 15, OxygenOS 16',
        'chipset' => 'Snapdragon 8 Elite (3 nm)',
        'cpu' => 'Octa-core (2x4.32 GHz Oryon V2 Phoenix L + 6x3.53 GHz Oryon V2 Phoenix M)',
        'gpu' => 'Adreno 830',
        'memory_card_slot' => 'No',
        'internal_storage' => '256GB/512GB/1TB',
        'ram' => '12GB/16GB/24GB',
        'storage_type' => 'UFS 4.0',
        'os_details' => 'OxygenOS 16 (International), ColorOS 16 (China)',
    ]);

    $p13->camera()->updateOrCreate([], [
        'main_camera_specs' => "50 MP, f/1.6, 23mm (wide), 1/1.43\", 1.12µm, OIS\n50 MP, f/2.6, 73mm (periscope), 3x optical zoom, OIS\n50 MP, f/2.0, 15mm, 120˚ (ultrawide)",
        'main_camera_features' => 'Hasselblad Color Calibration, Dual-LED flash, HDR, panorama',
        'main_video_capabilities' => '8K@30fps, 4K@30/60fps, 1080p@30/60/240/480fps, Dolby Vision',
        'selfie_camera_specs' => '32 MP, f/2.4, 21mm (wide)',
        'selfie_camera_features' => 'HDR, panorama',
        'selfie_video_capabilities' => '4K@30/60fps, 1080p@30/60fps',
    ]);

    $p13->connectivity()->updateOrCreate([], [
        'wlan' => 'Wi-Fi 7, tri-band',
        'bluetooth' => '5.4, aptX HD, LHDC 5',
        'positioning' => 'GPS (L1+L5), GLONASS, BDS, GALILEO, QZSS, NavIC',
        'nfc' => 'Yes',
        'usb' => 'USB Type-C 3.2, OTG',
        'sensors' => 'Fingerprint (ultrasonic), accelerometer, gyro, proximity, compass, barometer',
        'loudspeaker' => 'Yes, stereo speakers',
        'jack_3_5mm' => 'No',
        'network_bands' => '5G / LTE / HSPA / GSM',
        'loudness_test_result' => '-23.5 LUFS (Very good)',
    ]);

    $p13->battery()->updateOrCreate([], [
        'battery_type' => 'Si/C Li-Ion 6000 mAh',
        'charging_wired' => '100W wired',
        'charging_wireless' => '50W wireless',
        'charging_reverse' => '10W reverse wireless',
        'charging_specs_detailed' => "100W wired (100% in 36 min)\n50W wireless\n10W reverse wireless\n5W reverse wired",
        'reverse_wired' => '5W',
        'reverse_wireless' => '10W',
    ]);

    $p13->benchmarks()->updateOrCreate([], [
        'antutu_score' => 2690491,
        'geekbench_single' => 0, 
        'geekbench_multi' => 9278,
        'dmark_wild_life_extreme' => 6615,
        'battery_endurance_hours' => 61.6, // 61:36
    ]);
}

// ==========================================
// OnePlus 15 (Database ID: 4)
// Source: scripts/add_oneplus15.php
// ==========================================
$p15 = Phone::find(4);
if ($p15) {
    echo "Updating OnePlus 15 (ID 4)...\n";
    $p15->update([
        'release_date' => '2025-10-28',
        'image_url' => '/storage/phones/oneplus-15.jpg', // Ensure this exists or use mapping
        // Note: keeping existing image logic if this file is missing
    ]);

    $p15->body()->updateOrCreate([], [
        'dimensions' => '161.4 x 76.7 x 8.1 mm',
        'weight' => '211 g',
        'build_material' => 'Glass front (Victus 2), aluminum frame, glass/fiber back',
        'sim' => 'Nano-SIM + Nano-SIM + eSIM',
        'ip_rating' => 'IP68/IP69K',
        'display_type' => 'LTPO AMOLED, 1B colors, 165Hz, Dolby Vision, HDR10+',
        'display_size' => '6.78 inches',
        'display_resolution' => '1272 x 2772 pixels',
        'display_protection' => 'Corning Gorilla Glass Victus 2',
        'display_features' => '1800 nits (HBM), Ultra HDR support',
        'display_brightness' => '1800 nits (HBM)',
        'pwm_dimming' => 'PWM Supported',
        'measured_display_brightness' => '1364 nits',
        'screen_to_body_ratio' => '~90.8%',
        'pixel_density' => '~450 ppi',
        'screen_glass' => 'Gorilla Glass 7i / Crystal Shield',
    ]);

    $p15->platform()->updateOrCreate([], [
        'os' => 'Android 16, OxygenOS 16',
        'chipset' => 'Snapdragon 8 Elite Gen 5 (3 nm)',
        'cpu' => 'Octa-core (2x4.6 GHz Oryon V3 + 6x3.62 GHz Oryon V3)',
        'gpu' => 'Adreno 840',
        'memory_card_slot' => 'No',
        'internal_storage' => '256GB/512GB/1TB',
        'ram' => '12GB/16GB',
        'storage_type' => 'UFS 4.1',
        'os_details' => 'OxygenOS 16 (Global), ColorOS 16 (China)',
    ]);

    $p15->camera()->updateOrCreate([], [
        'main_camera_specs' => "50 MP (Main) + 50 MP (3.5x Tele) + 50 MP (Ultrawide)",
        'main_camera_features' => 'Laser focus, Hasselblad, LUT preview',
        'main_video_capabilities' => '8K@30fps, 4K@120fps, Dolby Vision',
        'selfie_camera_specs' => '32 MP, f/2.4',
        'selfie_camera_features' => 'HDR',
        'selfie_video_capabilities' => '4K@60fps',
        'main_camera_sensors' => 'Main: 1/1.56", Tele: 1/2.76", UW: 1/2.88"',
        'main_camera_ois' => 'Yes (Main & Tele)',
        'main_camera_zoom' => '3.5x optical',
    ]);

    $p15->connectivity()->updateOrCreate([], [
        'wlan' => 'Wi-Fi 7',
        'bluetooth' => '6.0',
        'nfc' => 'Yes',
        'usb' => 'USB Type-C 3.2, OTG',
        'sensors' => 'Fingerprint (ultrasonic), sensors package',
        'loudspeaker' => 'Yes, stereo',
        'loudness_test_result' => '-24.8 LUFS',
        'sar_value' => '1.17 W/kg (head)',
    ]);

    $p15->battery()->updateOrCreate([], [
        'battery_type' => 'Si/C Li-Ion 7300 mAh',
        'charging_wired' => '120W wired',
        'charging_wireless' => '50W wireless',
        'charging_reverse' => '10W reverse wireless',
        'charging_specs_detailed' => '120W wired (50% in 15 min), 50W wireless, 10W reverse wireless',
        'reverse_wired' => '5W',
        'reverse_wireless' => '10W',
    ]);

    $p15->benchmarks()->updateOrCreate([], [
        'antutu_score' => 3688274,
        'geekbench_multi' => 11062,
        'dmark_wild_life_extreme' => 7370,
    ]);
}

// ==========================================
// Poco F7 (Database ID: 7)
// Source: docs/phone_evaluations/xiaomi_poco_f7_indian.md
// ==========================================
$pf7 = Phone::find(7);
if ($pf7) {
    echo "Updating Poco F7 (ID 7)...\n";
    $pf7->update([
        'release_date' => '2025-06-01',
        'brand' => 'Xiaomi', // Ensuring correct brand
        // image_url already fixed
    ]);

    $pf7->body()->updateOrCreate([], [
        'dimensions' => '162.9 x 76.5 x 8.5 mm', // Inferred
        'weight' => '215.7g',
        'build_material' => 'Glass front (Ceramic Guard), glass/eco leather back',
        'sim' => 'Nano-SIM + Nano-SIM',
        'ip_rating' => 'IP68',
        'display_type' => 'LTPO 4.1 AMOLED, 120Hz, 3200 nits',
        'display_size' => '6.83 inches',
        'display_resolution' => '1280x2772',
        'display_protection' => 'Ceramic Guard glass',
        'display_features' => '3200 nits peak brightness, 2160Hz PWM dimming',
        'display_brightness' => '3200 nits (peak)',
        'pwm_dimming' => '2160Hz',
    ]);

    $pf7->platform()->updateOrCreate([], [
        'os' => 'Android 15, HyperOS 2',
        'chipset' => 'Snapdragon 8s Gen 4 (4nm)',
        'cpu' => 'Octa-core (1x3.21 GHz Cortex-X4 + ...)',
        'gpu' => 'Adreno 825',
        'memory_card_slot' => 'No',
        'internal_storage' => '256GB/512GB',
        'ram' => '12GB',
        'storage_type' => 'UFS 4.1',
        'os_details' => 'HyperOS 2.0 (Heavily customized)',
        'turnip_support_level' => 'Full Support',
    ]);
    
    $pf7->battery()->updateOrCreate([], [
        'battery_type' => 'Si/C Li-Ion 7550 mAh',
        'charging_wired' => '90W wired',
        'charging_reverse' => '22.5W reverse wired', 
        'charging_specs_detailed' => '90W wired (80% in 30 min)',
        'reverse_wired' => '22.5W',
    ]);

    $pf7->benchmarks()->updateOrCreate([], [
        'antutu_score' => 2024751,
        'geekbench_multi' => 6402,
        'dmark_wild_life_extreme' => 4476,
        'battery_endurance_hours' => 64.05, // 64:03
    ]);
}

echo "Database population complete.\n";
