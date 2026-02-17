<?php

use App\Models\Phone;
use Illuminate\Support\Facades\DB;

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "🚀 Starting addition of OnePlus Nord CE5...\n";

DB::transaction(function () {
    // 1. Create or Update Phone
    $phone = Phone::firstOrCreate(
        ['name' => 'OnePlus Nord CE5'],
        [
            'brand' => 'OnePlus',
            'model_variant' => 'CPH2719', // From 'Models' in data, assuming this is the model variant
            'price' => 24999.00,
            'overall_score' => 0,
            'release_date' => '2025-07-08',
            'announced_date' => '2025-07-08',
            'image_url' => '/storage/phones/oneplus-nord-ce5.png',
            'amazon_url' => 'https://www.amazon.in/OnePlus-Massive-MediaTek-Dimensity-Infinity/dp/B0FCMLCX46',
            'flipkart_url' => 'https://www.flipkart.com/oneplus-nord-ce5-5g-nexus-blue-128-gb/p/itm9259708fe4e3c',
            'amazon_price' => null,
            'flipkart_price' => 24999.00,
        ]
    );

    echo "✅ Phone Record: " . ($phone->wasRecentlyCreated ? "Created" : "Found") . "\n";

    // 2. Body Specs
    $phone->body()->updateOrCreate([], [
        'dimensions' => '163.6 x 76 x 8.2 mm',
        'weight' => '199 g',
        'build_material' => 'Glass front, plastic frame, plastic back', // Inferred, typical for Nord CE series
        'cooling_type' => 'Graphite', // Typical for mid-range, no specific mention of VC or Fan
        'sim' => 'Nano-SIM + Nano-SIM',
        'ip_rating' => 'IP65',
        'colors' => 'Marble Mist, Black Infinity, Nexus Blue',
        'display_type' => 'Fluid AMOLED, 1B colors, 120Hz, HDR10+',
        'display_size' => '6.77 inches',
        'display_resolution' => '1080 x 2392 pixels',
        'display_brightness' => '1430 nits (peak)',
        'measured_display_brightness' => '1231 nits',
        'pwm_dimming' => null,
        'screen_to_body_ratio' => '~89.7%',
        'pixel_density' => '~387 ppi',
        'aspect_ratio' => '20:9', // calculated broadly from resolution
        'screen_area' => '111.5 cm²',
        'display_features' => '120Hz, HDR10+, 1430 nits peak',
    ]);
    echo "✅ Body Specs Updated\n";

    // 3. Platform Specs
    $phone->platform()->updateOrCreate([], [
        'os' => 'Android 15, ColorOS 15',
        'os_details' => 'ColorOS 15',
        'chipset' => 'Mediatek Dimensity 8350 Apex (4 nm)',
        'cpu' => 'Octa-core (1x3.35 GHz Cortex-A715 & 3x3.20 GHz Cortex-A715 & 4x2.20 GHz Cortex-A510)',
        'gpu' => 'Mali G615-MC6',
        'memory_card_slot' => 'microSDXC (uses shared SIM slot)',
        'internal_storage' => '128GB, 256GB',
        'ram' => '8GB, 12GB',
        'storage_type' => 'UFS 3.1',
        // Developer Freedom
        'bootloader_unlockable' => true, // OnePlus generally allows this
        'turnip_support' => false, // Mali GPU
    'turnip_support_level' => 'None',
        'os_openness' => 'Moderately restricted', // ColorOS codebase
        'gpu_emulation_tier' => 'Mali Mid-tier',
        'aosp_aesthetics_score' => 6,
        'custom_rom_support' => 'Major', // OnePlus usually has good ROM support
    ]);
    echo "✅ Platform Specs Updated\n";

    // 4. Camera Specs
    $phone->camera()->updateOrCreate([], [
        'main_camera_specs' => "50 MP, f/1.8, 26mm (wide), 1/1.95\", 0.8µm, PDAF, OIS\n8 MP, f/2.2, 16mm, 112˚ (ultrawide), 1/4.0\", 1.12µm",
        'main_camera_sensors' => 'Main: 1/1.95", UW: 1/4.0"',
        'main_camera_apertures' => 'f/1.8 (main), f/2.2 (uw)',
        'main_camera_focal_lengths' => '26mm (main), 16mm (uw)',
        'main_camera_features' => 'LED flash, Ultra HDR, panorama',
        'main_camera_ois' => 'Yes',
        'main_camera_zoom' => null, // No telephoto
        'main_camera_pdaf' => 'Yes',
        'main_video_capabilities' => '4K@30/60fps, 1080p@30/60/120/480fps, 720p@960fps, gyro-EIS, OIS',
        'ultrawide_camera_specs' => '8 MP, f/2.2, 16mm, 112˚ (ultrawide), 1/4.0", 1.12µm',
        'telephoto_camera_specs' => null,
        'selfie_camera_specs' => '16 MP, f/2.4, 23mm (wide), 1/3.0", 1.0µm',
        'selfie_camera_sensor' => '1/3.0"',
        'selfie_camera_aperture' => 'f/2.4',
        'selfie_camera_features' => 'Panorama',
        'selfie_camera_autofocus' => false,
        'selfie_video_capabilities' => '1080p@30/60fps, gyro-EIS',
    ]);
    echo "✅ Camera Specs Updated\n";

    // 5. Connectivity Specs
    $phone->connectivity()->updateOrCreate([], [
        'network_bands' => 'GSM / HSPA / LTE / 5G',
        'wlan' => 'Wi-Fi 802.11 a/b/g/n/ac/6, dual-band',
        'wifi_bands' => 'Dual-band',
        'bluetooth' => '5.4, A2DP, LE, aptX HD, LHDC 5',
        'positioning' => 'GPS, GALILEO, GLONASS, BDS, QZSS',
        'positioning_details' => 'GPS, GALILEO, GLONASS, BDS, QZSS',
        'nfc' => 'Yes',
        'infrared' => 'Yes',
        'radio' => 'No',
        'usb' => 'USB Type-C 2.0',
        'usb_details' => 'USB Type-C 2.0',
        'sensors' => 'Fingerprint (under display, optical), accelerometer, gyro, proximity, compass',
        'loudspeaker' => 'Yes',
        'audio_quality' => '-27.1 LUFS (Good)',
        'jack_3_5mm' => 'No',
        'has_3_5mm_jack' => false,
        'sar_value' => '1.20 W/kg (head), 0.91 W/kg (body)',
    ]);
    echo "✅ Connectivity Specs Updated\n";

    // 6. Battery Specs
    $phone->battery()->updateOrCreate([], [
        'battery_type' => 'Si/C Li-Ion 7100 mAh',
        'charging_wired' => '80W wired',
        'charging_specs_detailed' => '80W wired, Bypass charging',
        'charging_wireless' => null,
        'charging_reverse' => null,
        'reverse_wired' => null,
        'reverse_wireless' => null,
    ]);
    echo "✅ Battery Specs Updated\n";

    // 7. Benchmarks
    $phone->benchmarks()->updateOrCreate([], [
        'antutu_score' => 1690757, // v11
        'antutu_v10_score' => 1390121,
        'geekbench_single' => 1319,
        'geekbench_multi' => 4068,
        'dmark_wild_life_extreme' => 3043,
        'dmark_wild_life_stress_stability' => 68,
        'dmark_test_type' => 'Wild Life Extreme',
        'battery_endurance_hours' => 61, // From 61:48h endurance
        'battery_active_use_score' => '14:42h',
        'charge_time_test' => null,
        'repairability_score' => null,
        'free_fall_rating' => 'Class A (300 falls)',
        'energy_label' => 'Class A',
    ]);
    echo "✅ Benchmarks Updated\n";

    // 8. Update Scores
    echo "🔄 Updating internal scores...\n";
    $phone->updateScores();
    echo "✅ Scores Updated\n";

});

echo "\n🎉 OnePlus Nord CE5 added successfully!\n";
