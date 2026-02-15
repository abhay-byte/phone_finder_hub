<?php

use App\Models\Phone;
use App\Services\UepsScoringService;

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$phone = Phone::updateOrCreate(
    ['name' => 'vivo V60'],
    [
        'brand' => 'vivo',
        'model_variant' => 'V2511',
        'price' => 32990.00,
        'overall_score' => 0,
        'ueps_score' => 0,
        'release_date' => '2025-08-19',
        'announced_date' => '2025-08-12',
        'image_url' => 'https://in-exstatic-vivofs.vivo.com/gdHFRinHEMrj3yPG/1754635934969/4454ed92823d741cea3c3f017258b2de.png',
        'amazon_url' => 'https://www.amazon.in/dp/B0FHWMBFLJ',
        'flipkart_url' => 'https://www.flipkart.com/vivo-v60-5g-auspicious-gold-256-gb/p/itmf7d9e4b7bf0b2',
    ]
);

$phone->body()->updateOrCreate([], [
    'dimensions' => '163.5 x 77 x 7.5 mm or 7.8 mm',
    'weight' => '192 g or 201 g',
    'build_material' => 'Glass front, plastic frame, plastic back or glass back',
    'sim' => 'Nano-SIM + Nano-SIM',
    'ip_rating' => 'IP68/IP69 dust tight and water resistant (up to 1.5m for 120 min)',
    'colors' => 'Mist Grey, Moonlit Blue, Ocean Blue, Auspicious Gold, Berry Purple',
    'display_type' => 'AMOLED, 1B colors, HDR10+, 120Hz',
    'display_size' => '6.77 inches',
    'display_resolution' => '1080 x 2392 pixels',
    'display_protection' => 'Schott Xensation Core',
    'pixel_density' => '~388 ppi density',
    'screen_to_body_ratio' => '~88.1%',
    'display_features' => '1500 nits (HBM), 5000 nits (peak)',
    'measured_display_brightness' => '1456 nits max brightness (measured)',
]);

$phone->platform()->updateOrCreate([], [
    'os' => 'Android 15, up to 4 major Android upgrades, Funtouch 15',
    'os_details' => 'Funtouch 15',
    'chipset' => 'Qualcomm SM7750-AB Snapdragon 7 Gen 4 (4 nm)',
    'cpu' => 'Octa-core (1x2.8 GHz Cortex-720 & 4x2.4 GHz Cortex-720 & 3x1.8 GHz Cortex-520)',
    'gpu' => 'Adreno 722',
    'memory_card_slot' => 'No',
    'internal_storage' => '128GB, 256GB, 512GB',
    'ram' => '8GB, 12GB, 16GB',
    'storage_type' => 'UFS 2.2',
    'bootloader_unlockable' => false,
    'os_openness' => 'Restricted OEM skin',
    'turnip_support_level' => 'None',
    'gpu_emulation_tier' => 'Adreno 7xx',
    'custom_rom_support' => 'None',
]);

$phone->camera()->updateOrCreate([], [
    'main_camera_specs' => '50 MP, f/1.9, 23mm (wide), 1/1.56", 1.0µm, PDAF, OIS + 50 MP, f/2.7, 73mm (periscope telephoto), 1/1.95", 0.8µm, PDAF, OIS, 3x optical zoom + 8 MP, f/2.0, 15mm, 120° (ultrawide)',
    'main_camera_features' => 'Zeiss optics, Ring-LED flash, panorama, HDR',
    'main_video_capabilities' => '4K@30fps, 1080p@30/60fps, gyro-EIS, OIS',
    'selfie_camera_specs' => '50 MP, f/2.2, 21mm (wide), 1/2.76", 0.64µm, AF',
    'selfie_camera_features' => 'Zeiss optics, HDR',
    'selfie_video_capabilities' => '4K@30fps, 1080p@30/60fps',
    'main_camera_sensors' => 'Main: 1/1.56", Tele: 1/1.95", Ultrawide: 8 MP sensor',
    'main_camera_apertures' => 'f/1.9 (main), f/2.7 (tele), f/2.0 (ultrawide)',
    'main_camera_focal_lengths' => '23mm (main), 73mm (tele), 15mm (ultrawide)',
    'main_camera_ois' => 'Yes (Main & Tele)',
]);

$phone->connectivity()->updateOrCreate([], [
    'wlan' => 'Wi-Fi 802.11 a/b/g/n/ac/6, dual-band',
    'bluetooth' => '5.4, A2DP, LE',
    'positioning' => 'GPS, GALILEO, GLONASS, QZSS, BDS, NavIC',
    'nfc' => 'Yes',
    'infrared' => 'Yes',
    'radio' => 'No',
    'usb' => 'USB Type-C 2.0, OTG',
    'sensors' => 'Fingerprint (under display, optical), accelerometer, gyro, compass, virtual proximity sensing',
    'loudspeaker' => 'Yes, with stereo speakers',
    'loudness_test_result' => '-24.7 LUFS (Very good)',
    'sar_value' => '0.99 W/kg (head), 0.93 W/kg (body)',
    'network_bands' => 'GSM / HSPA / LTE / 5G',
    'has_3_5mm_jack' => false,
]);

$phone->battery()->updateOrCreate([], [
    'battery_type' => 'Si/C Li-Ion 6500 mAh',
    'charging_wired' => '90W wired, PD',
    'charging_specs_detailed' => '90W wired, PD, reverse wired, bypass charging',
    'charging_wireless' => 'No',
    'charging_reverse' => 'Reverse wired',
]);

$phone->benchmarks()->updateOrCreate([], [
    'antutu_score' => 1261959,
    'antutu_v10_score' => 1009011,
    'geekbench_single' => 1334,
    'geekbench_multi' => 3568,
    'dmark_wild_life_extreme' => 1795,
    'battery_endurance_hours' => 15.9,
]);

$fresh = $phone->fresh(['body', 'platform', 'camera', 'connectivity', 'battery', 'benchmarks']);
$ueps = UepsScoringService::calculate($fresh);
$fpi = $fresh->calculateFPI();

$fresh->ueps_score = (int) round($ueps['total_score']);
$fresh->overall_score = is_array($fpi) ? (int) round($fpi['total']) : 0;
$fresh->saveQuietly();

echo 'Added/updated: ' . $fresh->name . ' (ID ' . $fresh->id . ') | UEPS ' . $fresh->ueps_score . ' | FPI ' . $fresh->overall_score . PHP_EOL;
echo 'Camera sensors: ' . ($fresh->camera->main_camera_sensors ?? 'NULL') . PHP_EOL;
echo 'Apertures: ' . ($fresh->camera->main_camera_apertures ?? 'NULL') . PHP_EOL;
echo 'Focal lengths: ' . ($fresh->camera->main_camera_focal_lengths ?? 'NULL') . PHP_EOL;
echo 'AnTuTu v11: ' . ($fresh->benchmarks->antutu_score ?? 'NULL') . ' | Geekbench single: ' . ($fresh->benchmarks->geekbench_single ?? 'NULL') . ' | 3DMark WLE: ' . ($fresh->benchmarks->dmark_wild_life_extreme ?? 'NULL') . PHP_EOL;
