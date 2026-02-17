<?php

use App\Models\Phone;
use App\Services\UepsScoringService;

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$phone = Phone::updateOrCreate(
    ['name' => 'Poco X6 Pro'],
    [
        'brand' => 'Xiaomi',
        'model_variant' => '2311DRK48G, 2311DRK48I',
        'price' => 26999.00,
        'overall_score' => 0,
        'ueps_score' => 0,
        'release_date' => '2024-01-12',
        'announced_date' => '2024-01-11',
        'image_url' => 'https://cdn.beebom.com/mobile/poco-x6-pro-front-and-back2.png',
        'amazon_url' => null,
        'flipkart_url' => 'https://www.flipkart.com/poco-x6-pro-5g-yellow-256-gb/p/itm46a0b51f57a68',
        'flipkart_price' => 26999.00,
    ]
);

$phone->body()->updateOrCreate([], [
    'dimensions' => '160.5 x 74.3 x 8.3 mm',
    'weight' => '186 g or 190 g',
    'build_material' => 'Glass front (Gorilla Glass 5), plastic frame, plastic back or silicone polymer back (eco leather)',
    'sim' => 'Nano-SIM + Nano-SIM',
    'ip_rating' => 'IP54 dust protected and water resistant (water splashes)',
    'colors' => 'Black, Yellow, Gray',
    'display_type' => 'AMOLED, 68B colors, 120Hz, 1920Hz PWM, HDR10+, Dolby Vision',
    'display_size' => '6.67 inches',
    'display_resolution' => '1220 x 2712 pixels',
    'display_protection' => 'Corning Gorilla Glass 5',
    'pixel_density' => '~446 ppi density',
    'screen_to_body_ratio' => '~90.1%',
    'pwm_dimming' => '1920Hz PWM',
    'display_features' => '500 nits (typ), 1200 nits (HBM), 1800 nits (peak)',
    'measured_display_brightness' => '1148 nits max brightness (measured)',
]);

$phone->platform()->updateOrCreate([], [
    'os' => 'Android 14, up to 3 major Android upgrades, HyperOS',
    'os_details' => 'HyperOS',
    'chipset' => 'Mediatek Dimensity 8300 Ultra (4 nm)',
    'cpu' => 'Octa-core (1x3.35 GHz Cortex-A715 & 3x3.20 GHz Cortex-A715 & 4x2.20 GHz Cortex-A510)',
    'gpu' => 'Mali G615-MC6',
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
    'main_camera_specs' => '64 MP, f/1.7, 25mm (wide), PDAF, OIS + 8 MP, f/2.2, 120-degree (ultrawide) + 2 MP (macro)',
    'main_camera_features' => 'LED flash, HDR, panorama',
    'main_video_capabilities' => '4K@24/30fps, 1080p@30/60fps, gyro-EIS',
    'selfie_camera_specs' => '16 MP, f/2.4, (wide)',
    'selfie_camera_features' => 'HDR, panorama',
    'selfie_video_capabilities' => '1080p@30/60fps',
    'main_camera_sensors' => 'Main: 1/2.0", Ultrawide: 1/4.0", Macro: 2 MP sensor',
    'main_camera_apertures' => 'f/1.7 (main), f/2.2 (ultrawide)',
    'main_camera_focal_lengths' => '25mm (main), 120° FOV (ultrawide)',
    'main_camera_ois' => 'Yes',
]);

$phone->connectivity()->updateOrCreate([], [
    'wlan' => 'Wi-Fi 802.11 a/b/g/n/ac/6, dual-band, Wi-Fi Direct',
    'bluetooth' => '5.4, A2DP, LE, aptX HD',
    'positioning' => 'GPS, GALILEO, GLONASS, QZSS, BDS (B1I+B1c)',
    'nfc' => 'Yes (market/region dependent)',
    'infrared' => 'Yes',
    'radio' => 'No',
    'usb' => 'USB Type-C 2.0, OTG',
    'sensors' => 'Fingerprint (under display, optical), accelerometer, gyro, compass, virtual proximity sensing',
    'loudspeaker' => 'Yes, with dual speakers',
    'audio_quality' => '24-bit/192kHz Hi-Res & Hi-Res Wireless audio',
    'loudness_test_result' => '-24.3 LUFS (Very good)',
    'sar_value' => '0.99 W/kg (head), 1.00 W/kg (body)',
    'network_bands' => 'GSM / HSPA / LTE / 5G',
    'has_3_5mm_jack' => false,
]);

$phone->battery()->updateOrCreate([], [
    'battery_type' => 'Li-Po 5000 mAh',
    'charging_wired' => '67W wired, QC2.0, PD3.0, 100% in 45 min',
    'charging_specs_detailed' => '67W wired, QC2.0, PD3.0, 100% in 45 min',
    'charging_wireless' => 'No',
    'charging_reverse' => 'No',
]);

$phone->benchmarks()->updateOrCreate([], [
    'antutu_score' => 1624789,
    'antutu_v10_score' => 1396547,
    'geekbench_single' => 1457,
    'geekbench_multi' => 4696,
    'dmark_wild_life_extreme' => 3086,
    'battery_endurance_hours' => 11.8,
]);

$fresh = $phone->fresh(['body', 'platform', 'camera', 'connectivity', 'battery', 'benchmarks']);
$ueps = UepsScoringService::calculate($fresh);
$fpi = $fresh->calculateFPI();

$fresh->ueps_score = (int) round($ueps['total_score']);
$fresh->overall_score = is_array($fpi) ? (int) round($fpi['total']) : 0;
$fresh->saveQuietly();

echo 'Added/updated: ' . $fresh->name . ' (ID ' . $fresh->id . ') | UEPS ' . $fresh->ueps_score . ' | FPI ' . $fresh->overall_score . PHP_EOL;
