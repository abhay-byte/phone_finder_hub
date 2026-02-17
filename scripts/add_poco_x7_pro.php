<?php

use App\Models\Phone;
use App\Services\UepsScoringService;

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$phone = Phone::updateOrCreate(
    ['name' => 'Poco X7 Pro'],
    [
        'brand' => 'Xiaomi',
        'model_variant' => '2412DPC0AG, 2412DPC0AI',
        'price' => 21999.00,
        'overall_score' => 0,
        'ueps_score' => 0,
        'release_date' => '2025-01-09',
        'announced_date' => '2025-01-09',
        'image_url' => 'https://cdn.beebom.com/mobile/poco-x7-pro-5g/poco-x7-pro-5g-front-and-back-3.png',
        'amazon_url' => null,
        'flipkart_url' => 'https://www.flipkart.com/poco-x7-pro-5g-yellow-256-gb/p/itm0a066ed95064a',
        'flipkart_price' => 21999.00,
    ]
);

$phone->body()->updateOrCreate([], [
    'dimensions' => '160.8 x 75.2 x 8.3 mm',
    'weight' => '195 g or 198 g',
    'build_material' => 'Glass front (Gorilla Glass 7i), plastic back, silicone polymer back (eco leather)',
    'sim' => 'Nano-SIM + Nano-SIM',
    'ip_rating' => 'IP68/IP69 (market/region dependent)',
    'colors' => 'Black/Yellow, White, Green, Red (Iron Man Edition)',
    'display_type' => 'AMOLED, 68B colors, 120Hz, 1920Hz PWM, Dolby Vision, HDR10+',
    'display_size' => '6.67 inches',
    'display_resolution' => '1220 x 2712 pixels',
    'display_protection' => 'Corning Gorilla Glass 7i',
    'pixel_density' => '~446 ppi density',
    'screen_to_body_ratio' => '~88.8%',
    'pwm_dimming' => '1920Hz PWM',
    'display_features' => '1400 nits (HBM), 3200 nits (peak)',
    'measured_display_brightness' => '1265 nits max brightness (measured)',
]);

$phone->platform()->updateOrCreate([], [
    'os' => 'Android 15, HyperOS 2',
    'os_details' => 'HyperOS 2',
    'chipset' => 'Mediatek Dimensity 8400 Ultra (4 nm)',
    'cpu' => 'Octa-core (1x3.25 GHz Cortex-A725 & 3x3.0 GHz Cortex-A725 & 4x2.1 GHz Cortex-A725)',
    'gpu' => 'Mali-G720 MC7',
    'memory_card_slot' => 'No',
    'internal_storage' => '256GB, 512GB',
    'ram' => '8GB, 12GB',
    'storage_type' => 'UFS 4.0',
    'bootloader_unlockable' => true,
    'os_openness' => 'Moderately restricted',
    'turnip_support' => false,
    'turnip_support_level' => 'None',
    'gpu_emulation_tier' => 'Mali Valhall',
    'custom_rom_support' => 'Limited',
]);

$phone->camera()->updateOrCreate([], [
    'main_camera_specs' => '50 MP, f/1.5, 26mm (wide), 1/1.95", 0.8µm, PDAF, OIS + 8 MP, f/2.2, 15mm (ultrawide), 1/4.0", 1.12µm',
    'main_camera_features' => 'LED flash, HDR, panorama',
    'main_video_capabilities' => '4K@24/30/60fps, 1080p@30/60/120/240/960fps, HDR10+, gyro-EIS, OIS',
    'selfie_camera_specs' => '20 MP, f/2.2, 25mm (wide), 1/4.0", 0.7µm',
    'selfie_video_capabilities' => '1080p@30fps',
    'main_camera_sensors' => 'Main: 1/1.95", Ultrawide: 1/4.0"',
    'main_camera_apertures' => 'f/1.5 (main), f/2.2 (ultrawide)',
    'main_camera_focal_lengths' => '26mm (main), 15mm (ultrawide)',
    'main_camera_ois' => 'Yes (Main)',
]);

$phone->connectivity()->updateOrCreate([], [
    'wlan' => 'Wi-Fi 802.11 a/b/g/n/ac/6, dual-band, Wi-Fi Direct',
    'bluetooth' => '5.4/6.0, A2DP, LE, aptX HD',
    'positioning' => 'GPS (L1+L5), GLONASS (G1), BDS (B1I+B1c+B2a), GALILEO (E1+E5a), NavIC (L5)',
    'nfc' => 'Yes (market/region dependent)',
    'infrared' => 'Yes',
    'radio' => 'No',
    'usb' => 'USB Type-C 2.0, OTG',
    'sensors' => 'Fingerprint (under display, optical), accelerometer, gyro, proximity, compass',
    'loudspeaker' => 'Yes, with stereo speakers',
    'audio_quality' => '24-bit/192kHz Hi-Res & Hi-Res Wireless audio',
    'loudness_test_result' => '-24.7 LUFS (Very good)',
    'sar_value' => '1.09 W/kg (head), 1.08 W/kg (body); SAR EU 0.99 W/kg (head), 0.99 W/kg (body)',
    'network_bands' => 'GSM / HSPA / LTE / 5G',
    'has_3_5mm_jack' => false,
]);

$phone->battery()->updateOrCreate([], [
    'battery_type' => 'Si/C Li-Ion 6000 mAh (Global), Si/C Li-Ion 6550 mAh (India)',
    'charging_wired' => '90W wired, PD3.0, QC3+, 100% in 42 min',
    'charging_specs_detailed' => '90W wired, PD3.0, QC3+, 100% in 42 min, reverse wired charging',
    'charging_wireless' => 'No',
    'charging_reverse' => 'Reverse wired',
]);

$phone->benchmarks()->updateOrCreate([], [
    'antutu_score' => 1986589,
    'antutu_v10_score' => 1568331,
    'geekbench_single' => 1565,
    'geekbench_multi' => 6311,
    'dmark_wild_life_extreme' => 3880,
    'battery_endurance_hours' => 12.7,
]);

$fresh = $phone->fresh(['body', 'platform', 'camera', 'connectivity', 'battery', 'benchmarks']);
$ueps = UepsScoringService::calculate($fresh);
$fpi = $fresh->calculateFPI();

$fresh->ueps_score = (int) round($ueps['total_score']);
$fresh->overall_score = is_array($fpi) ? (int) round($fpi['total']) : 0;
$fresh->saveQuietly();

echo 'Added/updated: ' . $fresh->name . ' (ID ' . $fresh->id . ') | UEPS ' . $fresh->ueps_score . ' | FPI ' . $fresh->overall_score . PHP_EOL;
