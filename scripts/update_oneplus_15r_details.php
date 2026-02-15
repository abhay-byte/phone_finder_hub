<?php

use App\Models\Phone;
use App\Models\SpecBody;
use App\Models\SpecPlatform;
use App\Models\SpecCamera;
use App\Models\SpecConnectivity;
use App\Models\SpecBattery;
use App\Models\Benchmark;

$phoneName = 'OnePlus 15R';
$phone = Phone::where('name', 'LIKE', "%$phoneName%")->first();

if (!$phone) {
    echo "Phone $phoneName not found!\n";
    exit(1);
}

echo "Updating specs for {$phone->name} (ID: {$phone->id})...\n";

// 1. Phone Core
$phone->update([
    'release_date' => '2025-12-22',
    'price' => 47999,
    'model_variant' => 'CPH2769 / CPH2767',
]);

// 2. Body
SpecBody::updateOrCreate(['phone_id' => $phone->id], [
    'dimensions' => '163.4 x 77 x 8.1 mm or 8.3 mm',
    'weight' => '213 g or 219 g',
    'build_material' => 'Glass front (Gorilla Glass 7i), aluminum alloy frame, glass back or fiber-reinforced plastic back',
    'sim' => 'Nano-SIM + Nano-SIM + eSIM',
    'ip_rating' => 'IP68/IP69K',
    'display_type' => 'AMOLED, 1B colors, 165Hz, PWM, Dolby Vision, HDR10+, HDR Vivid',
    'display_size' => '6.83 inches',
    'display_resolution' => '1272 x 2800 pixels',
    'pixel_density' => '~450 ppi density',
    'display_protection' => 'Corning Gorilla Glass 7i, Mohs level 5',
    'screen_glass' => 'Gorilla Glass 7i',
    'screen_to_body_ratio' => '~90.1%',
    'display_brightness' => '1800 nits (HBM), 3600 nits (peak)',
    'measured_display_brightness' => '1204 nits max brightness (measured)',
    'pwm_dimming' => 'PWM Dimming',
    'display_features' => 'Ultra HDR image support',
    'colors' => 'Charcoal Black, Mint Breeze, Electric Violet',
]);

// 3. Platform
SpecPlatform::updateOrCreate(['phone_id' => $phone->id], [
    'os' => 'Android 16, OxygenOS 16',
    'chipset' => 'Qualcomm SM8845 Snapdragon 8 Gen 5 (3 nm)',
    'cpu' => 'Octa-core (2x3.8 GHz Oryon V3 Phoenix L + 6x3.32 GHz Oryon V3 Phoenix M)',
    'gpu' => 'Adreno 829',
    'memory_card_slot' => 'No',
    'internal_storage' => '256GB, 512GB',
    'ram' => '12GB',
    'storage_type' => 'UFS 4.1',
    'bootloader_unlockable' => true,
    'os_openness' => 'Near-AOSP / Minimal restrictions / Easy root',
    'turnip_support_level' => 'Full',
    'gpu_emulation_tier' => 'Adreno 8xx Elite-class',
    'custom_rom_support' => 'Major',
]);

// 4. Camera
SpecCamera::updateOrCreate(['phone_id' => $phone->id], [
    'main_camera_specs' => '50 MP, f/1.8, 24mm (wide), 1/1.56", 1.0µm, PDAF, OIS + 8 MP, f/2.2, 16mm, 112˚ (ultrawide), 1/4.0", 1.12µm',
    'main_camera_sensors' => 'Main: 1/1.56", UW: 1/4.0"',
    'main_camera_apertures' => 'f/1.8 (main), f/2.2 (ultrawide)',
    'main_camera_focal_lengths' => '24mm (main), 16mm (ultrawide)',
    'main_camera_ois' => 'Yes (Main)',
    'main_camera_features' => 'Color spectrum sensor, LED flash, HDR, panorama',
    'main_video_capabilities' => '4K@30/60/120fps, 1080p@30/60/120/240fps, gyro-EIS, OIS',
    'selfie_camera_specs' => '32 MP, f/2.0, 25mm (wide), AF',
    'selfie_camera_features' => 'HDR, panorama',
    'selfie_video_capabilities' => '4K@30fps, 1080p@30fps, gyro-EIS, OIS',
]);

// 5. Connectivity
SpecConnectivity::updateOrCreate(['phone_id' => $phone->id], [
    'wlan' => 'Wi-Fi 802.11 a/b/g/n/ac/6/7, dual-band',
    'bluetooth' => '6.0, A2DP, LE, aptX HD, LHDC 5',
    'positioning' => 'GPS (L1+L5), GLONASS (G1), BDS (B1I+B1c+B2a), GALILEO (E1+E5a), QZSS (L1+L5), NavIC (L5)',
    'nfc' => 'Yes',
    'infrared' => 'Yes',
    'radio' => 'No',
    'usb' => 'USB Type-C 2.0, OTG',
    'sensors' => 'Fingerprint (under display, ultrasonic), accelerometer, gyro, proximity, compass',
    'loudspeaker' => 'Yes, with stereo speakers',
    'has_3_5mm_jack' => false,
    'sar_value' => '1.08 W/kg (head), 1.00 W/kg (body)',
    'loudness_test_result' => '-25.7 LUFS (Very good)',
    'network_bands' => '5G / LTE / HSPA / GSM', // Ensure this sticks
]);

// 6. Battery
SpecBattery::updateOrCreate(['phone_id' => $phone->id], [
    'battery_type' => 'Si/C Li-Ion 7400 mAh',
    'charging_wired' => '80W wired',
    'charging_wireless' => null,
    'charging_reverse' => 'No',
]);

// 7. Benchmarks
Benchmark::updateOrCreate(['phone_id' => $phone->id], [
    'antutu_score' => 2981677, // v11
    'antutu_v10_score' => 1954910, // v10
    'geekbench_multi' => 9369, // v6
    'dmark_wild_life_extreme' => 5016,
    'battery_active_use_score' => '21:36h',
    'energy_label' => 'Class A',
    'repairability_score' => 'Class B',
    'free_fall_rating' => 'Class D (45 falls)',
]);

echo "Done.\n";
