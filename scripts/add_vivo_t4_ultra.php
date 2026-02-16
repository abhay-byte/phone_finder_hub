<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Phone;

echo "Adding vivo T4 Ultra...\n";

// 1. Create Main Phone Record
$phone = Phone::firstOrCreate(
    ['name' => 'vivo T4 Ultra'],
    [
        'brand' => 'vivo',
        'model_variant' => 'Global',
        'price' => 34999.00, // Approx 390 EUR
        'overall_score' => 0,
        'release_date' => '2025-06-18',
        'announced_date' => '2025-06-11',
        'image_url' => '/storage/phones/vivo-t4-ultra.png',
        'amazon_url' => 'https://www.amazon.in/vivo-Battery-Periscope-Dimensity-Processor/dp/B0FF29BYHF',
        'flipkart_url' => 'https://www.flipkart.com/vivo-t4-ultra-5g-meteor-grey-256-gb/p/itm9cfd8118c9ce0',
        'amazon_price' => 34999.00,
        'flipkart_price' => 34999.00,
    ]
);

// 2. Body Specs
$phone->body()->updateOrCreate([], [
    'dimensions' => '160.6 x 75 x 7.5 mm',
    'weight' => '192 g',
    'build_material' => 'Glass front, glass back, IP64',
    'cooling_type' => 'Vapor Chamber', // Assumed for Dimensity 9300+
    'sim' => 'Nano-SIM + Nano-SIM',
    'ip_rating' => 'IP64',
    'colors' => 'Phoenix Gold, Meteor Grey',
    'display_type' => 'AMOLED, 1B colors, 120Hz',
    'display_size' => '6.67 inches',
    'display_resolution' => '1260 x 2800 pixels',
    'display_brightness' => '1600 nits (HBM), 5000 nits (peak)',
    'measured_display_brightness' => null,
    'pwm_dimming' => '2160Hz',
    'screen_to_body_ratio' => '~89.2%',
    'pixel_density' => '~460 ppi',
    'aspect_ratio' => '20:9',
    'screen_area' => '107.4 cm²',
    'display_features' => '120Hz, 2160Hz PWM, 1600 nits (HBM), 5000 nits (peak)',
]);

// 3. Platform Specs
$phone->platform()->updateOrCreate([], [
    'os' => 'Android 15, Funtouch 15',
    'os_details' => 'Funtouch 15',
    'chipset' => 'Mediatek Dimensity 9300+ (4 nm)',
    'cpu' => 'Octa-core (1x3.4 GHz Cortex-X4 & 3x2.85 GHz Cortex-X4 & 4x2.0 GHz Cortex-A720)',
    'gpu' => 'Immortalis-G720 MC12',
    'memory_card_slot' => 'No',
    'internal_storage' => '256GB, 512GB',
    'ram' => '8GB, 12GB',
    'storage_type' => 'UFS 3.1',
    // Developer Freedom
    'bootloader_unlockable' => false,
    'turnip_support' => false, // Mali GPU (Immortalis), no Turnip
    'turnip_support_level' => 'None',
    'os_openness' => 'Restricted OEM skin',
    'gpu_emulation_tier' => 'Mali High-tier', // Powerful Mali but drivers are issue
    'aosp_aesthetics_score' => 5,
    'custom_rom_support' => 'Limited',
]);

// 4. Camera Specs
$phone->camera()->updateOrCreate([], [
    'main_camera_specs' => '50 MP, f/1.9, 23mm (wide), 1/1.56", PDAF, OIS',
    'main_camera_sensors' => 'Main: 50MP',
    'main_camera_apertures' => 'f/1.9 (main), f/2.6 (periscope), f/2.2 (ultrawide)',
    'main_camera_focal_lengths' => '23mm (main), 85mm (periscope)',
    'main_camera_features' => 'Ring-LED flash, panorama, HDR',
    'main_camera_ois' => 'Yes (Main, Periscope)',
    'main_camera_zoom' => '3x optical zoom',
    'main_camera_pdaf' => 'Yes',
    'main_video_capabilities' => '4K, 1080p, gyro-EIS',
    'ultrawide_camera_specs' => '8 MP, f/2.2',
    'telephoto_camera_specs' => '50 MP, f/2.6, 85mm (periscope telephoto), 3x optical',
    'selfie_camera_specs' => '32 MP, f/2.5, (wide)',
    'selfie_camera_sensor' => '32MP',
    'selfie_camera_aperture' => 'f/2.5',
    'selfie_video_capabilities' => 'Yes',
]);

// 5. Connectivity Specs
$phone->connectivity()->updateOrCreate([], [
    'network_bands' => 'GSM / HSPA / LTE / 5G',
    'wlan' => 'Wi-Fi 802.11 a/b/g/n/ac/6, dual-band, Wi-Fi Direct',
    'wifi_bands' => 'Dual-band',
    'bluetooth' => '5.4, A2DP, LE',
    'positioning' => 'GPS, GLONASS, GALILEO, BDS, NavIC',
    'positioning_details' => 'GPS, GLONASS, GALILEO, BDS, NavIC',
    'nfc' => 'Yes',
    'infrared' => 'No', // Not mentioned
    'radio' => 'No',
    'usb' => 'USB Type-C 2.0, OTG',
    'sensors' => 'Fingerprint (under display, optical), accelerometer, gyro, proximity, compass',
    'loudspeaker' => 'Yes, with stereo speakers',
    'jack_3_5mm' => 'No',
    'has_3_5mm_jack' => false,
]);

// 6. Battery Specs
$phone->battery()->updateOrCreate([], [
    'battery_type' => 'Si/C Li-Ion 5500 mAh',
    'charging_wired' => '90W wired',
    'charging_specs_detailed' => '90W wired, PD, Reverse wired',
    'charging_wireless' => null,
    'charging_reverse' => 'Reverse wired',
    'reverse_wired' => 'Yes',
]);

// 7. Benchmarks
$phone->benchmarks()->updateOrCreate([], [
    'antutu_score' => 2286808,
    'geekbench_single' => 2252,
    'geekbench_multi' => 7277,
    'dmark_wild_life_extreme' => 4935,
    'dmark_wild_life_stress_stability' => 53,
    'dmark_test_type' => 'Wild Life Extreme',
]);

echo "Phone added successfully!\n";
echo "Run 'php artisan phone:recalculate-scores' to update rankings.\n";
