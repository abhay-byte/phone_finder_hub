<?php

use App\Models\Phone;
use Illuminate\Support\Facades\DB;

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "🚀 Starting addition of Realme GT 7 Pro...\n";

DB::transaction(function () {
    // 1. Create or Update Phone
    $phone = Phone::firstOrCreate(
        ['name' => 'Realme GT 7 Pro'],
        [
            'brand' => 'Realme',
            'model_variant' => 'RMX5010', // From 'Models' RMX5010, RMX5011
            'price' => 49999.00,
            'overall_score' => 0,
            'release_date' => '2024-11-04',
            'announced_date' => '2024-11-04',
            'image_url' => '/storage/phones/realme-gt-7-pro.png',
            'amazon_url' => 'https://www.amazon.in/realme-Snapdragon-Processor-Periscope-RealWorld/dp/B0DMFD8L7J',
            'flipkart_url' => 'https://www.flipkart.com/realme-gt-7-pro-grey-256-gb/p/itma117bb1885029',
            'amazon_price' => null,
            'flipkart_price' => 48999.00,
        ]
    );

    echo "✅ Phone Record: " . ($phone->wasRecentlyCreated ? "Created" : "Found") . "\n";

    // 2. Body Specs
    $phone->body()->updateOrCreate([], [
        'dimensions' => '162.5 x 76.9 x 8.6 mm',
        'weight' => '222.8 g',
        'build_material' => 'Glass front (Gorilla Glass 7i), aluminum frame, glass back (Panda Glass)',
        'cooling_type' => 'Vapor Chamber', // High-end flagship usually has VC, explicitly mentioned in reviews for GT7 Pro
        'sim' => 'Nano-SIM + Nano-SIM',
        'ip_rating' => 'IP68/IP69',
        'colors' => 'Mars Orange, Galaxy Grey, White',
        'display_type' => 'LTPO AMOLED, 1B colors, 120Hz, 2600Hz PWM, HDR10+, Dolby Vision',
        'display_size' => '6.78 inches',
        'display_resolution' => '1264 x 2780 pixels',
        'display_brightness' => '6500 nits (peak)',
        'measured_display_brightness' => '2336 nits',
        'pwm_dimming' => '2600Hz',
        'screen_to_body_ratio' => '~89.4%',
        'pixel_density' => '~450 ppi',
        'aspect_ratio' => '19.8:9', // Calculated approx
        'screen_area' => '111.7 cm²',
        'display_features' => '120Hz, Dolby Vision, 6500 nits peak',
        'display_protection' => 'Corning Gorilla Glass 7i',
        'screen_glass' => 'Corning Gorilla Glass 7i',
        'glass_protection_level' => 'Gorilla Glass 7i',
    ]);
    echo "✅ Body Specs Updated\n";

    // 3. Platform Specs
    $phone->platform()->updateOrCreate([], [
        'os' => 'Android 15, Realme UI 6.0',
        'os_details' => 'Realme UI 6.0',
        'chipset' => 'Qualcomm SM8750-AB Snapdragon 8 Elite (3 nm)',
        'cpu' => 'Octa-core (2x4.32 GHz Oryon V2 Phoenix L + 6x3.53 GHz Oryon V2 Phoenix M)',
        'gpu' => 'Adreno 830',
        'memory_card_slot' => 'No',
        'internal_storage' => '256GB, 512GB, 1TB',
        'ram' => '12GB, 16GB',
        'storage_type' => 'UFS 4.0',
        // Developer Freedom
        'bootloader_unlockable' => true, // Realme allows via APK
        'turnip_support' => true, // Snap 8 Elite -> Adreno 830
        'turnip_support_level' => 'Stable',
        'os_openness' => 'Moderately restricted', // Realme UI
        'gpu_emulation_tier' => 'Adreno 8xx High-tier',
        'aosp_aesthetics_score' => 6,
        'custom_rom_support' => 'Moderate',
    ]);
    echo "✅ Platform Specs Updated\n";

    // 4. Camera Specs
    $phone->camera()->updateOrCreate([], [
        'main_camera_specs' => "50 MP, f/1.8, 24mm (wide), 1/1.56\", 1.0µm, multi-directional PDAF, OIS\n50 MP, f/2.7, 73mm (telephoto), 1/1.95\", 0.8µm, multi-directional PDAF, OIS, 3x optical zoom\n8 MP, f/2.2, 16mm, 112˚ (ultrawide), 1/4.0\", 1.12µm",
        'main_camera_sensors' => 'Main: 1/1.56", Tele: 1/1.95", UW: 1/4.0"',
        'main_camera_apertures' => 'f/1.8 (main), f/2.7 (tele), f/2.2 (uw)',
        'main_camera_focal_lengths' => '24mm (main), 73mm (tele), 16mm (uw)',
        'main_camera_features' => 'Color spectrum sensor, Dual-LED flash, HDR, panorama',
        'main_camera_ois' => 'Yes',
        'main_camera_zoom' => '3x optical zoom',
        'main_camera_pdaf' => 'Multi-directional',
        'main_video_capabilities' => '8K@24fps, 4K@30/60fps, 1080p@30/60/120/240fps, gyro-EIS',
        'ultrawide_camera_specs' => '8 MP, f/2.2, 16mm, 112˚ (ultrawide), 1/4.0", 1.12µm',
        'telephoto_camera_specs' => '50 MP, f/2.7, 73mm (telephoto), 1/1.95", 0.8µm, multi-directional PDAF, OIS, 3x optical zoom',
        'selfie_camera_specs' => '16 MP, f/2.5, 25mm (wide), 1/3.09", 1.0µm',
        'selfie_camera_sensor' => '1/3.09"',
        'selfie_camera_aperture' => 'f/2.5',
        'selfie_camera_features' => 'Panorama',
        'selfie_camera_autofocus' => false, // Not mentioned
        'selfie_video_capabilities' => '1080p@30/60fps',
    ]);
    echo "✅ Camera Specs Updated\n";

    // 5. Connectivity Specs
    $phone->connectivity()->updateOrCreate([], [
        'network_bands' => 'GSM / HSPA / LTE / 5G',
        'wlan' => 'Wi-Fi 802.11 a/b/g/n/ac/6/7, dual-band',
        'wifi_bands' => 'Dual-band',
        'bluetooth' => '5.4, A2DP, LE',
        'positioning' => 'GPS (L1+L5), BDS (B1I+B1c+B2a), GALILEO (E1+E5a), QZSS (L1+L5), NavIC (L5), GLONASS',
        'positioning_details' => 'GPS (L1+L5), BDS (B1I+B1c+B2a), GALILEO (E1+E5a), QZSS (L1+L5), NavIC (L5), GLONASS',
        'nfc' => 'Yes, 360˚',
        'infrared' => 'Yes',
        'radio' => 'No',
        'usb' => 'USB Type-C',
        'usb_details' => 'USB Type-C',
        'sensors' => 'Fingerprint (under display, ultrasonic), accelerometer, gyro, proximity, compass, Color spectrum',
        'loudspeaker' => 'Yes, with stereo speakers',
        'audio_quality' => '-25.3 LUFS (Very good)',
        'jack_3_5mm' => 'No',
        'has_3_5mm_jack' => false,
        'sar_value' => null,
    ]);
    echo "✅ Connectivity Specs Updated\n";

    // 6. Battery Specs
    // Note: Documentation says India-only is 5800mAh, Global 6500mAh.
    // Given price in INR is listed, and "India-only model with 5800 mAh battery",
    // but the spec sheet also lists "6500mAh" at the top block.
    // I will use 5800 mAh to be safe for the Indian market context of ₹49,999.
    // However, the user provided "6500mAh" in the top summary block.
    // I will use 6500 mAh as primarily requested in top block, but clarify in details.
    // Actually, let's use the India spec (5800) if the price is INR, but the user explicitly
    // put 6500mAh in the top block "6500mAh".
    // I will stick to the top block value: 6500mAh.
    $phone->battery()->updateOrCreate([], [
        'battery_type' => 'Si/C Li-Ion 6500 mAh',
        'charging_wired' => '120W wired',
        'charging_specs_detailed' => '120W wired, 13 min to 50%, 37 min to 100%',
        'charging_wireless' => null,
        'charging_reverse' => null,
        'reverse_wired' => null,
        'reverse_wireless' => null,
    ]);
    echo "✅ Battery Specs Updated\n";

    // 7. Benchmarks
    $phone->benchmarks()->updateOrCreate([], [
        'antutu_score' => 3300025, // v11
        'antutu_v10_score' => 2746604,
        'geekbench_single' => 3122, // GB6
        'geekbench_multi' => 9538, // GB6
        'dmark_wild_life_extreme' => 6351,
        'dmark_wild_life_stress_stability' => 71, // Integer required (71.2 -> 71)
        'dmark_test_type' => 'Wild Life Extreme',
        'battery_endurance_hours' => 75, // 75:23h -> 75
        'battery_active_use_score' => '17:31h',
        'charge_time_test' => '37 min to 100%',
        'repairability_score' => 'Class B',
        'free_fall_rating' => 'Class C (90 falls)',
        'energy_label' => 'Class A',
    ]);
    echo "✅ Benchmarks Updated\n";

    // 8. Update Scores
    echo "🔄 Updating internal scores...\n";
    $phone->updateScores();
    echo "✅ Scores Updated\n";

});

echo "\n🎉 Realme GT 7 Pro added successfully!\n";
