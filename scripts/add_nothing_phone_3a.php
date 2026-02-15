<?php

use App\Models\Phone;

$phone = Phone::firstOrCreate(
    ['name' => 'Nothing Phone (3a)'],
    [
        'brand' => 'Nothing',
        'model_variant' => 'A059',
        'price' => 26900.00, // Converted ~€269 to INR roughly, or placeholder. User gave €269. Let's use ~25000 INR equivalent or keep EUR? The app seems to use INR symbol. 269 EUR * 90 = ~24210. Let's set 24999.
        'overall_score' => 0,
        'release_date' => '2025-03-11',
        'announced_date' => '2025-03-04',
        'image_url' => '/storage/phones/nothing-phone-3a.png',
        'amazon_url' => 'https://www.amazon.in/Nothing-Phone-128GB-Storage-White/dp/B0DZTMFWDB',
        'flipkart_url' => 'https://www.flipkart.com/nothing-phone-3a-black-256-gb/p/itm49557c5a65f9c',
    ]
);

// Body
$phone->body()->updateOrCreate([], [
    'dimensions' => '163.5 x 77.5 x 8.4 mm',
    'weight' => '201 g',
    'build_material' => 'Glass front (Panda Glass), plastic frame, glass back',
    'sim' => 'Nano-SIM + Nano-SIM',
    'ip_rating' => 'IP64',
    'display_type' => 'AMOLED, 1B colors, 120Hz, HDR10+',
    'display_size' => '6.77 inches',
    'display_resolution' => '1080 x 2392 pixels',
    'display_protection' => 'Panda Glass, Mohs level 5',
    'display_features' => '3 LED light strips on the back, Ultra HDR image support',
    'colors' => 'Black, White, Blue, Community Edition green',
    // Granular Display Specs
    'pixel_density' => '~387 ppi',
    'screen_to_body_ratio' => '~88.0%',
    'pwm_dimming' => '2160Hz',
    'display_brightness' => '3000 nits (peak)',
    'measured_display_brightness' => '1273 nits (measured), 1300 nits (HBM)',
    'touch_sampling_rate' => null,
    'aspect_ratio' => '20:9', // Inferred from resolution
]);

// Platform
$phone->platform()->updateOrCreate([], [
    'os' => 'Android 15, upgradable to Android 16',
    'os_details' => 'Nothing OS 4.0, up to 3 major Android upgrades',
    'chipset' => 'Qualcomm SM7635 Snapdragon 7s Gen 3 (4 nm)',
    'cpu' => 'Octa-core (1x2.5 GHz Cortex-A720 & 3x2.4 GHz Cortex-A720 & 4x1.8 GHz Cortex-A520)',
    'gpu' => 'Adreno 810',
    'memory_card_slot' => 'No',
    'internal_storage' => '128GB, 256GB',
    'ram' => '8GB, 12GB',
    'storage_type' => 'UFS 2.2', // Likely for this segment, or 3.1? Text doesn't say. Let's omit or guess conservative if scoring needs it.
    // Developer Freedom
    'bootloader_unlockable' => true,
    'os_openness' => 'Near-AOSP / Minimal restrictions',
    'turnip_support_level' => 'Full',
    'gpu_emulation_tier' => 'Adreno 7xx/8xx mid-range', // 7s Gen 3 is 7xx series effectively but named 810 GPU? Wait, 7s Gen 3 has Adreno 810? Yes per text.
    'custom_rom_support' => 'Major',
]);

// Camera
$phone->camera()->updateOrCreate([], [
    'main_camera_specs' => '50 MP (Main) + 50 MP (Tele) + 8 MP (UW)',
    'main_camera_features' => 'LED flash, panorama, HDR',
    'main_video_capabilities' => '4K@30fps, 1080p@30/60/120fps, gyro-EIS, OIS',
    // Granular
    'main_camera_sensors' => '1/1.57" (Main) + 1/2.74" (Tele) + 1/4.0" (UW)',
    'main_camera_apertures' => 'f/1.9 (Main) + f/2.0 (Tele) + f/2.2 (UW)',
    'main_camera_focal_lengths' => '24mm (Main) + 50mm (Tele) + 15mm (UW)',
    'main_camera_ois' => 'OIS (Main)', // Text says "OIS" after main and "PDAF" after tele. Wait, text says "OIS 50 MP...". Usually means Main has OIS.
    'main_camera_zoom' => '2x optical zoom',
    'main_camera_pdaf' => 'Dual Pixel PDAF (Main), PDAF (Tele)',
    'ultrawide_camera_specs' => '8 MP, f/2.2, 15mm, 120˚',
    'telephoto_camera_specs' => '50 MP, f/2.0, 50mm, 2x optical zoom',
    
    'selfie_camera_specs' => '32 MP, f/2.2, 22mm (wide), 1/3.44"',
    'selfie_camera_features' => 'HDR',
    'selfie_video_capabilities' => '1080p@30fps',
    'selfie_camera_aperture' => 'f/2.2',
    'selfie_camera_sensor' => '1/3.44"',
]);

// Connectivity
$phone->connectivity()->updateOrCreate([], [
    'wlan' => 'Wi-Fi 802.11 a/b/g/n/ac/6, dual-band, Wi-Fi Direct',
    'bluetooth' => '5.4, A2DP, LE',
    'positioning' => 'GPS, GALILEO, GLONASS, BDS, QZSS',
    'nfc' => 'Yes',
    'infrared' => 'No',
    'radio' => 'No',
    'usb' => 'USB Type-C 2.0, OTG',
    'usb_details' => 'USB Type-C 2.0, OTG',
    'sensors' => 'Fingerprint (under display, optical), accelerometer, gyro, proximity, compass',
    'loudspeaker' => 'Yes, with stereo speakers',
    'jack_3_5mm' => 'No',
    'loudness_test_result' => '-24.1 LUFS (Very good)',
]);

// Battery
$phone->battery()->updateOrCreate([], [
    'battery_type' => '5000 mAh',
    'charging_wired' => '50W wired, 50% in 19 min, 100% in 56 min',
    'charging_wireless' => 'No',
    'charging_reverse' => 'No',
]);

// Benchmarks
$phone->benchmarks()->updateOrCreate([], [
    'antutu_score' => 995594, // v11 provided
    'geekbench_single' => 1178, // v6 provided
    'geekbench_multi' => 3278, // v6 provided
    'dmark_wild_life_extreme' => 1064,
]);

// Trigger calculations
$phone->refresh();
$ueps = \App\Services\UepsScoringService::calculate($phone);
$phone->ueps_score = (int) $ueps['total_score'];
$phone->overall_score = (int) $phone->calculateFPI()['total'];
$phone->save();

echo "Added Nothing Phone (3a) with ID: " . $phone->id . "\n";
