<?php

use App\Models\Phone;

$phone = Phone::firstOrCreate(
    ['name' => 'Nothing Phone (3)'],
    [
        'brand' => 'Nothing',
        'model_variant' => 'A024',
        'price' => 46900.00,
        'overall_score' => 0, // Will be calculated
        'release_date' => '2025-07-15',
        'image_url' => '/storage/phones/nothing-phone-3.png',
        'amazon_url' => 'https://www.amazon.in/Nothing-Phone-Black-12GB-256GB/dp/B0F7R6V1LM?crid=15OVYC3WCXEX6&dib=eyJ2IjoiMSJ9.5UionNV6FwkFEnE_U5kNIOL5PfSNQro6s5Ifv8-W1PSdqYy2AK2zCciBgRgrXorw2calp87lApuoNIv_6yRHhlTum8xcY8DWpTTfYzjV4JhMVUOj_71LQeOAShmS5UceN8TcKOWsWLcZCpKFWmDIuYVRWUNtl3UETpY983AzD-xI0J6RG10d7lHg0o5cEb7TaZf8gg0HlAQxezljfyNA6F6H4WPvJ_4X50qQUGAuRug.mLGgGITlwhf0zL7GYhTZjNfTTIoWLa1WvMaK9H3HDNc&dib_tag=se&keywords=nothing+3&qid=1771130657&sprefix=nothing+%2Caps%2C342&sr=8-3',
        'flipkart_url' => 'https://www.flipkart.com/nothing-phone-3-black-256-gb/p/itm0c32a18b0df8a?pid=MOBHCY4CDV4Z6CRT&lid=LSTMOBHCY4CDV4Z6CRTRACX4Y&marketplace=FLIPKART&q=nothing+3&store=tyy%2F4io&srno=s_1_1&otracker=search&otracker1=search&fm=Search&iid=b466b59f-988d-4956-bfe1-0d8951f3e26c.MOBHCY4CDV4Z6CRT.SEARCH&ppt=sp&ppn=sp&ssid=wrv1l1ok9x7fm5fk1771130676252&qH=b525ce510adbd3d0&ov_redirect=true',
    ]
);

// Body
$phone->body()->updateOrCreate([], [
    'dimensions' => '160.6 x 75.6 x 9 mm',
    'weight' => '218 g',
    'build_material' => 'Glass front (Gorilla Glass 7i), glass back (Gorilla Glass Victus), aluminum frame',
    'sim' => 'Nano-SIM + Nano-SIM + eSIM (max 2 at a time)',
    'ip_rating' => 'IP68',
    'display_type' => 'OLED, 1B colors, 120Hz, 960Hz PWM, HDR10+, 4500 nits (peak)',
    'display_size' => '6.67 inches',
    'display_resolution' => '1260 x 2800 pixels',
    'display_protection' => 'Corning Gorilla Glass 7i',
    'display_features' => '1507 nits (measured), 4500 nits (peak), Monochrome LED display on the back',
    // 'colors' => 'White, Black', // Added to end as separate update if body relation doesn't auto-update? No, firstOrCreate on body handles it.
     'colors' => 'White, Black',
]);

// Platform
$phone->platform()->updateOrCreate([], [
    'os' => 'Android 15, up to Android 16, Nothing OS 4.0',
    'chipset' => 'Qualcomm SM8735 Snapdragon 8s Gen 4 (4 nm)',
    'cpu' => 'Octa-core (1x3.21 GHz Cortex-X4 & 3x3.0 GHz Cortex-A720 & 2x2.8 GHz Cortex-A720 & 2x2.0 GHz Cortex-A720)',
    'gpu' => 'Adreno 825',
    'memory_card_slot' => 'No',
    'internal_storage' => '256GB, 512GB',
    'ram' => '12GB, 16GB',
    'storage_type' => 'UFS 4.0',
    // Developer Freedom (Estimated based on Nothing brand)
    'bootloader_unlockable' => true,
    'os_openness' => 'Near-AOSP / Minimal restrictions',
    'turnip_support_level' => 'Full', // Snapdragon usually good
    'gpu_emulation_tier' => 'Adreno 825', // Should map to Adreno 8xx in scorer
    'custom_rom_support' => 'Major',
]);

// Camera
$phone->camera()->updateOrCreate([], [
    'main_camera_specs' => '50 MP, f/1.7, 24mm (wide), 1/1.3", PDAF, OIS + 50 MP, f/2.7, (periscope telephoto), 1/2.75", PDAF, 3x optical zoom, OIS + 50 MP, f/2.2, 114˚ (ultrawide), 1/2.76"',
    'main_camera_features' => 'LED flash, panorama, HDR',
    'main_video_capabilities' => '4K@30/60fps, 1080p@30/60fps, gyro-EIS, OIS',
    'selfie_camera_specs' => '50 MP, f/2.2, (wide), 1/2.76"',
    'selfie_camera_features' => 'HDR',
    'selfie_video_capabilities' => '4K@60fps, 1080p@60fps',
]);

// Connectivity
$phone->connectivity()->updateOrCreate([], [
    'wlan' => 'Wi-Fi 802.11 a/b/g/n/ac/6/7, tri-band, Wi-Fi Direct',
    'bluetooth' => '6.0, A2DP, LE',
    'positioning' => 'GPS (L1+L5), GLONASS, BDS, GALILEO, QZSS, NavIC, SBAS',
    'nfc' => 'Yes',
    'infrared' => 'No', // Doesn't say Yes
    'radio' => 'No',
    'usb' => 'USB Type-C 2.0, OTG',
    'sensors' => 'Fingerprint (under display, optical), accelerometer, proximity, gyro, compass',
    'loudspeaker' => 'Yes, with stereo speakers',
    'jack_3_5mm' => 'No',
    'loudness_test_result' => '-23.9 LUFS (Very good)',
]);

// Battery
$phone->battery()->updateOrCreate([], [
    'battery_type' => 'Si/C 5500 mAh', // Taking the higher India model capacity
    'charging_wired' => '65W wired, PD3.0, PPS, QC4',
    'charging_wireless' => '15W wireless',
    'charging_reverse' => '7.5W reverse charging, 5W reverse wireless',
]);

// Benchmarks (User provided v11 score but likely database expects v10 range or standardized?
// Actually our scoring logic normalizes against max. 2.2M is typical for 8 Gen 3/8s Gen 4.
// Scoring service handles it.)
$phone->benchmarks()->updateOrCreate([], [
    'antutu_score' => 2270521,
    'geekbench_single' => 2162,
    'geekbench_multi' => 6900,
    'dmark_wild_life_extreme' => 4588,
]);

// Trigger score calculations
$phone->refresh();
$ueps = \App\Services\UepsScoringService::calculate($phone);
$phone->ueps_score = $ueps['total_score'];
$phone->overall_score = $phone->calculateFPI()['total'];
$phone->save();

echo "Added Nothing Phone (3) with ID: " . $phone->id . "\n";
echo "FPI Score: " . $phone->overall_score . "\n";
echo "UEPS Score: " . $phone->ueps_score . "\n";
