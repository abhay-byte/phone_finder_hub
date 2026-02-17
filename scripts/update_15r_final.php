<?php

use App\Models\Phone;
use App\Models\SpecBody;
use App\Models\SpecPlatform;
use App\Models\SpecCamera;
use App\Models\SpecConnectivity;
use App\Models\SpecBattery;
use App\Models\Benchmark;
use Illuminate\Support\Facades\DB;

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Find the phone
$phone = Phone::where('model_variant', 'CPH2769')->orWhere('name', 'OnePlus 15R')->first();

if (!$phone) {
    echo "OnePlus 15R not found!\n";
    exit(1);
}

echo "Updating OnePlus 15R (ID: {$phone->id})...\n";

DB::transaction(function () use ($phone) {
    // 1. Update Main Phone Record
    $phone->update([
        'announced_date' => '2025-12-17',
        'release_date' => '2025-12-22',
        'price' => 47999, // INR
    ]);

    // 2. Update Body Specs
    SpecBody::updateOrCreate(
        ['phone_id' => $phone->id],
        [
            'dimensions' => '163.4 x 77 x 8.1 mm or 8.3 mm',
            'weight' => '213 g or 219 g (7.51 oz)',
            'build_material' => 'Glass front (Gorilla Glass 7i), aluminum alloy frame, glass back or fiber-reinforced plastic back',
            'sim' => 'Nano-SIM + Nano-SIM + eSIM (max 2 at a time) or Nano-SIM + Nano-SIM',
            'ip_rating' => 'IP68/IP69K dust/water resistant (up to 1.5m for 30 min)',
            'display_type' => 'AMOLED, 1B colors, 165Hz, PWM, Dolby Vision, HDR10+, HDR Vivid',
            'display_size' => '6.83 inches',
            'screen_to_body_ratio' => '~90.1%',
            'display_resolution' => '1272 x 2800 pixels',
            'pixel_density' => '~450 ppi density',
            'display_protection' => 'Corning Gorilla Glass 7i',
            'display_brightness' => '1800 nits (HBM), 3600 nits (peak)',
            'measured_display_brightness' => '1204 nits max brightness (measured)',
            'screen_area' => '113.3 cm2',
            'aspect_ratio' => '20:9', // Inferring from resolution
            'glass_protection_level' => 'Mohs level 5',
            'pwm_dimming' => 'PWM', // As per text
            'display_features' => 'Ultra HDR image support',
            'colors' => 'Charcoal Black, Mint Breeze, Electric Violet',
        ]
    );

    // 3. Update Platform Specs
    SpecPlatform::updateOrCreate(
        ['phone_id' => $phone->id],
        [
            'os' => 'Android 16',
            'ui' => 'OxygenOS 16',
            'chipset' => 'Qualcomm SM8845 Snapdragon 8 Gen 5 (3 nm)',
            'cpu' => 'Octa-core (2x3.8 GHz Oryon V3 Phoenix L + 6x3.32 GHz Oryon V3 Phoenix M)',
            'gpu' => 'Adreno 829',
            'memory_options' => '256GB 12GB RAM, 512GB 12GB RAM',
            'storage_type' => 'UFS 4.1',
            'card_slot' => 'No',
        ]
    );

    // 4. Update Camera Specs
    SpecCamera::updateOrCreate(
        ['phone_id' => $phone->id],
        [
            'main_camera_specs' => '50 MP, f/1.8, 24mm (wide), 1/1.56", 1.0µm, PDAF, OIS',
            'main_camera_features' => 'Color spectrum sensor, LED flash, HDR, panorama',
            'main_video_capabilities' => '4K@30/60/120fps, 1080p@30/60/120/240fps, gyro-EIS, OIS',
            'ultrawide_camera_specs' => '8 MP, f/2.2, 16mm, 112˚ (ultrawide), 1/4.0", 1.12µm',
            'selfie_camera_specs' => '32 MP, f/2.0, 25mm (wide), AF',
            'selfie_camera_features' => 'HDR, panorama',
            'selfie_video_capabilities' => '4K@30fps, 1080p@30fps, gyro-EIS, OIS',
            // Assigning features to new granular columns
            'main_camera_pdaf' => 'PDAF', // Extracted
            'selfie_video_features' => '4K@30fps, 1080p@30fps, gyro-EIS, OIS',
        ]
    );

    // 5. Update Connectivity Specs
    SpecConnectivity::updateOrCreate(
        ['phone_id' => $phone->id],
        [
            'wlan' => 'Wi-Fi 802.11 a/b/g/n/ac/6/7, dual-band',
            'bluetooth' => '6.0, A2DP, LE, aptX HD, LHDC 5',
            'gps' => 'GPS (L1+L5), GLONASS (G1), BDS (B1I+B1c+B2a), GALILEO (E1+E5a), QZSS (L1+L5), NavIC (L5)',
            'nfc' => 'Yes',
            'infrared' => 'Yes',
            'radio' => 'No',
            'usb' => 'USB Type-C 2.0, OTG',
            'sensors' => 'Fingerprint (under display, ultrasonic), accelerometer, gyro, proximity, compass',
            'positioning_details' => 'GPS (L1+L5), GLONASS (G1), BDS (B1I+B1c+B2a), GALILEO (E1+E5a), QZSS (L1+L5), NavIC (L5)',
            'has_3_5mm_jack' => false,
        ]
    );

    // 6. Update Battery Specs
    SpecBattery::updateOrCreate(
        ['phone_id' => $phone->id],
        [
             'capacity' => 'Si/C Li-Ion 7400 mAh', // Preserving original capacity string style
             'charging_wired' => '80W wired',
             'charging_specs_detailed' => '80W wired',
        ]
    );

    // 7. Update Benchmarks
    Benchmark::updateOrCreate(
        ['phone_id' => $phone->id],
        [
            'antutu_score' => 2981677, // Taking v11 score as primary or latest
            'geekbench_multi' => 9369,
            'dmark_wild_life_extreme' => 5016, 
            'battery_endurance_hours' => 77.85, // 77:51h converted to decimal hours approx
            'battery_active_use_score' => '21:36',
            'energy_label' => 'Class A',
            'repairability_score' => 'Class B',
            'free_fall_rating' => 'Class D (45 falls)',
            // 'loudspeaker_lufs' => -25.7, // Assuming we have this column or will add it? The user provided -25.7 LUFS. 
            // Checking DB schema earlier, benchmarks table changes... assuming we might not have lufs column yet or mapped differently.
            // Let's stick to what we have in the model for now.
        ]
    );

});

echo "OnePlus 15R updated successfully!\n";
