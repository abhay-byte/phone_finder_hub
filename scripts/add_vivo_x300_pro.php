<?php

use App\Models\Phone;
use Illuminate\Support\Facades\DB;

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Wrapping in transaction
DB::transaction(function () {
    // Start with the phone itself
    $phone = Phone::updateOrCreate(
        ['name' => 'vivo X300 Pro'],
        [
            'brand' => 'vivo',
            'model_variant' => 'V2514', // International model
            'price' => 109998.00,
            'overall_score' => 0, // Will be recalculated
            'release_date' => '2025-10-17',
            'announced_date' => '2025-10-13',
            'image_url' => '/storage/phones/vivo-x300-pro.webp',
            // Shortened URL to avoid string truncation (max 255 chars)
            'amazon_url' => 'https://www.amazon.in/vivo-X300-Pro-Additional-Exchange/dp/B0G26JXRTX',
            'flipkart_url' => null,
            'amazon_price' => 109998.00,
            'flipkart_price' => null,
        ]
    );

    // Body
    $phone->body()->updateOrCreate([], [
        'dimensions' => '161.2 x 75.5 x 8 mm',
        'weight' => '226 g',
        'build_material' => 'Glass front (Armor Glass), aluminum alloy frame, glass back',
        'sim' => 'Nano-SIM + Nano-SIM + eSIM',
        'ip_rating' => 'IP68/IP69',
        'colors' => 'Phantom Black, Mist Blue, Dune Brown, Cloud White',
        'display_type' => 'LTPO AMOLED, 1B colors, 120Hz, 2160Hz PWM, HDR10+, HDR Vivid, Dolby Vision',
        'display_size' => '6.78 inches',
        'display_resolution' => '1260 x 2800 pixels',
        'display_brightness' => '4500 nits (peak)',
        'measured_display_brightness' => 2113,
        'pwm_dimming' => 2160,
        'screen_to_body_ratio' => '~91.6%',
        'pixel_density' => '~452 ppi',
        'aspect_ratio' => '20:9',
        'screen_area' => '111.5 cm²',
        'display_protection' => 'Armor Glass',
    ]);

    // Platform
    $phone->platform()->updateOrCreate([], [
        'os' => 'Android 16, OriginOS 6',
        'os_details' => 'OriginOS 6',
        'chipset' => 'Mediatek Dimensity 9500 (3 nm)',
        'cpu' => 'Octa-core (1x4.21 GHz C1-Ultra & 3x3.5 GHz C1-Premium & 4x2.7 GHz C1-Pro)',
        'gpu' => 'Arm G1-Ultra',
        'memory_card_slot' => 'No',
        'internal_storage' => '256GB, 512GB, 1TB',
        'ram' => '12GB, 16GB',
        'storage_type' => 'Dual UFS 4.1',
        'bootloader_unlockable' => false,
        'turnip_support' => false, // Mali GPU
        'turnip_support_level' => 'None',
        'os_openness' => 'Restricted OEM skin',
        'gpu_emulation_tier' => 'Mali High-tier',
        'custom_rom_support' => 'None',
        'ram_min' => 12,
        'ram_max' => 16,
        'storage_min' => 256,
        'storage_max' => 1024,
    ]);

    // Camera
    $phone->camera()->updateOrCreate([], [
        'main_camera_specs' => "50 MP, f/1.6, 24mm (wide), 1/1.28\", 1.22µm, PDAF, OIS\n200 MP, f/2.7, 85mm (periscope telephoto), 1/1.4\", 0.56µm, multi-directional PDAF, OIS, 3.7x optical zoom, macro 2.7:1\n50 MP, f/2.0, 15mm, 119˚ (ultrawide), 1/2.76\", 0.64µm, AF",
        'telephoto_camera_specs' => '200 MP, f/2.7, 85mm (periscope telephoto), 1/1.4", 0.56µm, multi-directional PDAF, OIS, 3.7x optical zoom, macro 2.7:1',
        'ultrawide_camera_specs' => '50 MP, f/2.0, 15mm, 119˚ (ultrawide), 1/2.76", 0.64µm, AF',
        
        // Granular fields for Table View
        'main_camera_sensors' => '1/1.28", 1/1.4", 1/2.76"',
        'main_camera_apertures' => 'f/1.6, f/2.7, f/2.0',
        'main_camera_focal_lengths' => '24mm, 85mm, 15mm',
        'main_camera_ois' => 'Yes, Yes, No',

        'main_camera_features' => 'Laser AF, color spectrum sensor, Zeiss optics, Zeiss T* lens coating, LED flash, panorama, HDR, 3D LUT import',
        'main_video_capabilities' => '8K@30fps, 4K@30/60/120fps, 1080p@30/60/120/240fps, gyro-EIS, 4K@120fps 10-bit Log, Dolby Vision HDR',
        'selfie_camera_specs' => '50 MP, f/2.0, 20mm (wide), 1/2.76", 0.64µm, AF',
        'selfie_camera_features' => 'HDR',
        'selfie_video_capabilities' => '4K@30/60fps, 1080p@30/60fps',
        'main_camera_manual' => true,
    ]);

    // Connectivity
    $phone->connectivity()->updateOrCreate([], [
        'network_bands' => 'GSM / HSPA / LTE / 5G',
        'wlan' => 'Wi-Fi 802.11 a/b/g/n/ac/6/7, dual-band',
        'bluetooth' => '5.4, A2DP, LE, aptX HD, LHDC 5',
        'positioning' => 'GPS (L1+L5), GLONASS (L1), BDS (B1I+B1c+B2a+B2b), GALILEO (E1+E5a+E5b), QZSS (L1+L5), NavIC (L5)',
        'nfc' => 'Yes',
        'infrared' => 'Yes',
        'usb' => 'USB Type-C 3.2, OTG',
        'sensors' => 'Fingerprint (under display, ultrasonic), accelerometer, gyro, proximity, compass, color spectrum',
        'loudspeaker' => 'Yes, with stereo speakers',
        'jack_3_5mm' => 'No',
        'has_3_5mm_jack' => false,
    ]);

    // Battery
    $phone->battery()->updateOrCreate([], [
        'battery_type' => 'Si/C Li-Ion 6510 mAh',
        'charging_wired' => '90W wired',
        'charging_wireless' => '40W wireless',
        'charging_reverse' => 'Reverse wireless',
        'reverse_wired' => 'Reverse wired', 
    ]);

    // Benchmarks
    $phone->benchmarks()->updateOrCreate([], [
        'antutu_score' => 3810252, // v11
        'antutu_v10_score' => 2982875,
        'geekbench_single' => 3563,
        'geekbench_multi' => 10512,
        'dmark_wild_life_extreme' => 7301,
        'dmark_wild_life_stress_stability' => 53,
        'battery_endurance_hours' => 48,
        'battery_active_use_score' => '12:45',
    ]);
    
    // External Camera Scores
   DB::table('benchmarks')->where('phone_id', $phone->id)->update([
        'dxomark_score' => 171,
        'phonearena_camera_score' => 150,
        'other_benchmark_score' => 87, // Avg of GSM (4.5/5=90) and 91M (8.3/10=83)
    ]);

    echo "Phone synced successfully: " . $phone->name . " (ID: " . $phone->id . ")\n";
});
