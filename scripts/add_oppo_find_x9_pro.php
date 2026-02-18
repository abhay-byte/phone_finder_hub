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
        ['name' => 'Oppo Find X9 Pro'],
        [
            'brand' => 'Oppo',
            'model_variant' => 'CPH2791', // International variant from docs
            'price' => 109999.00,
            'overall_score' => 0, // Will be recalculated
            'release_date' => '2025-10-22',
            'announced_date' => '2025-10-16',
            'image_url' => '/storage/phones/oppo-find-x9-pro.webp',
            // Shortened URL to avoid string truncation
            'amazon_url' => 'https://www.amazon.in/Titanium-Charcoal-Storage-Additional-Exchange/dp/B0FXXJRXVX',
            'flipkart_url' => 'https://www.flipkart.com/oppo-find-x9-pro-titanium-charcoal-512-gb/p/itma0d37896b3848',
            'amazon_price' => 109999.00,
            'flipkart_price' => 109999.00, // Assuming similar pricing
        ]
    );

    // Body
    $phone->body()->updateOrCreate([], [
        'dimensions' => '161.3 x 76.5 x 8.3 mm',
        'weight' => '224 g',
        'build_material' => 'Glass front (Gorilla Glass Victus 2), glass back, aluminum frame',
        'sim' => 'Nano-SIM + eSIM / Nano-SIM + Nano-SIM',
        'ip_rating' => 'IP68/IP69',
        'colors' => 'Silk White, Titanium Charcoal, Velvet Red',
        'display_type' => 'LTPO AMOLED, 1B colors, 120Hz, 2160Hz PWM, Dolby Vision, HDR10+, HDR Vivid',
        'display_size' => '6.78 inches',
        'display_resolution' => '1272 x 2772 pixels',
        'display_brightness' => '3600 nits (peak)',
        'measured_display_brightness' => 1175, // From "Display 1175 nits max brightness (measured)"
        'pwm_dimming' => 2160,
        'screen_to_body_ratio' => '~91.1%',
        'pixel_density' => '~450 ppi',
        'aspect_ratio' => '19.5:9',
        'screen_area' => '112.4 cm²',
        'display_protection' => 'Corning Gorilla Glass Victus 2',
    ]);

    // Platform
    $phone->platform()->updateOrCreate([], [
        'os' => 'Android 16, ColorOS 16',
        'os_details' => 'ColorOS 16',
        'chipset' => 'Mediatek Dimensity 9500 (3 nm)',
        'cpu' => 'Octa-core (1x4.21 GHz C1-Ultra & 3x3.5 GHz C1-Premium & 4x2.7 GHz C1-Pro)',
        'gpu' => 'Arm G1-Ultra',
        'memory_card_slot' => 'No',
        'internal_storage' => '256GB, 512GB, 1TB',
        'ram' => '12GB, 16GB',
        'storage_type' => 'UFS 4.1',
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
        'main_camera_specs' => "50 MP, f/1.5, 23mm (wide), 1/1.28\", 1.22µm, multi-directional PDAF, OIS\n200 MP, f/2.1, 70mm (periscope telephoto), 1/1.56\", 0.5µm, 3x optical zoom, multi-directional PDAF, OIS\n50 MP, f/2.0, 15mm, 120˚ (ultrawide), 1/2.76\", 0.64µm, multi-directional PDAF",
        'telephoto_camera_specs' => '200 MP, f/2.1, 70mm (periscope telephoto), 1/1.56", 0.5µm, 3x optical zoom, multi-directional PDAF, OIS',
        'ultrawide_camera_specs' => '50 MP, f/2.0, 15mm, 120˚ (ultrawide), 1/2.76", 0.64µm, multi-directional PDAF',
        
        // Granular fields for Table View
        'main_camera_sensors' => '1/1.28", 1/1.56", 1/2.76"',
        'main_camera_apertures' => 'f/1.5, f/2.1, f/2.0',
        'main_camera_focal_lengths' => '23mm, 70mm, 15mm',
        'main_camera_ois' => 'Yes, Yes, No',

        'main_camera_features' => 'Laser AF, color spectrum sensor, Hasselblad Color Calibration, LED flash, HDR, panorama, LUT preview',
        'main_video_capabilities' => '4K@30/60/120fps, 1080p@30/60/240fps; gyro-EIS; HDR, 10‑bit video, Dolby Vision, LOG',
        'selfie_camera_specs' => '50 MP, f/2.0, 21mm (wide), 1/2.76", 0.64µm, PDAF',
        'selfie_camera_features' => 'Panorama',
        'selfie_video_capabilities' => '4K@30/60fps, 1080p@30/60fps, gyro-EIS',
        'main_camera_manual' => true,
    ]);

    // Connectivity
    $phone->connectivity()->updateOrCreate([], [
        'network_bands' => 'GSM / HSPA / LTE / 5G',
        'wlan' => 'Wi-Fi 802.11 a/b/g/n/ac/6/7, dual-band or tri-band, Wi-Fi Direct',
        'bluetooth' => '6.0, A2DP, LE, aptX HD, LHDC 5',
        'positioning' => 'GPS (L1+L5), BDS (B1I+B1c+B2a+B2b), GALILEO (E1+E5a+E5b), QZSS (L1+L5), GLONASS, NavIC (L5)',
        'nfc' => 'Yes',
        'infrared' => 'Yes',
        'usb' => 'USB Type-C 3.2, OTG',
        'sensors' => 'Fingerprint (under display, ultrasonic), accelerometer, gyro, proximity, compass, barometer',
        'loudspeaker' => 'Yes, with stereo speakers',
        'jack_3_5mm' => 'No',
        'has_3_5mm_jack' => false,
    ]);

    // Battery
    $phone->battery()->updateOrCreate([], [
        'battery_type' => 'Si/C Li-Ion 7500 mAh',
        'charging_wired' => '80W wired, 80W UFCS, 55W PPS, 11.7W PD',
        'charging_wireless' => '50W wireless',
        'charging_reverse' => '10W reverse wireless',
        'reverse_wired' => 'No', // Assuming wireless reverse only unless specified
    ]);

    // Benchmarks
    $phone->benchmarks()->updateOrCreate([], [
        'antutu_score' => 3563384, // Using the higher score from text (v11) as it matches X300 Pro range
        'antutu_v10_score' => 2719560,
        // Block said 3469273 for v11, but text said 3563384. 
        // 3563384 is explicitly labeled v11 in "Our Tests". I will use that.
        'geekbench_single' => 3235,
        'geekbench_multi' => 9626,
        'dmark_wild_life_extreme' => 7227, // Score from block
        'dmark_wild_life_stress_stability' => 48, // 48.5 -> 48 integer
        'battery_endurance_hours' => 80, // 80:06h -> 80
        'battery_active_use_score' => '21:57h', // 21:57h
        'dxomark_score' => 166,
        'phonearena_camera_score' => 152,
        // GSM Arena: 4.6/5 -> 92
        // Mobile91: 9/10 -> 90
        // Average: 91
        'other_benchmark_score' => 91, 
    ]);
    
    echo "Phone synced successfully: " . $phone->name . " (ID: " . $phone->id . ")\n";
});
