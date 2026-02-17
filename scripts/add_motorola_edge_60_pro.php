<?php

use App\Models\Phone;

// Create Phone
$phone = Phone::firstOrCreate(
    ['name' => 'Motorola Edge 60 Pro'],
    [
        'brand' => 'Motorola',
        'model_variant' => 'XT2507-1',
        'price' => 28999.00, // INR
        'overall_score' => 92, // Estimated based on specs
        'release_date' => '2025-04-30',
        'announced_date' => '2025-04-24',
        'image_url' => '/storage/phones/motorola-edge-60-pro.png',
        'amazon_url' => 'https://www.amazon.in/Motorola-Edge-Blue-256GB-Storage/dp/B0F83HKHMG?crid=TVSJQSN92NF4&dib=eyJ2IjoiMSJ9.HAr_KTTVMujlk_OW3pAa7W-LxsRtSXz9OY4GfneAyJg-_F7K7iVXoY2F1AeThzwQQpjQsXW8zJg1WzFFx-FaDahTNIV-ksgmLLChcX2O_jxb4_y00VpRGubYJIrsGaaNO5HpnlJBKUrUUfwyFbJMTxJ_8wnhhpC_FNuLywURuwos4_eac0AUKzxzSoMlLuRg2IEgo25B_lbTcZm6rbOiJ15i_V1ZQNlaqZoWBFn1bWk.sKdZdWhaqfjAI3BaTINXLlYwlJJsCdFgcvnxQyJLkAA&dib_tag=se&keywords=edge+60+pro&qid=1771176292&sprefix=edge+60+p%2Caps%2C347&sr=8-2',
        'flipkart_url' => 'https://www.flipkart.com/motorola-edge-60-pro-pantone-shadow-256-gb/p/itm3823c3f8f3cc9?pid=MOBH9C9JEHCZYHF2&lid=LSTMOBH9C9JEHCZYHF2YESB5E&marketplace=FLIPKART&q=edge+60+pro&store=tyy%2F4io&spotlightTagId=default_BestsellerId_tyy%2F4io&srno=s_1_1&otracker=search&otracker1=search&fm=Search&iid=49c11bc2-8625-4cb1-a271-09b5327bc82c.MOBH9C9JEHCZYHF2.SEARCH&ppt=sp&ppn=sp&ssid=zw565rpl03ef670g1771176263850&qH=ee2a1101424eac19&ov_redirect=true',
        'amazon_price' => 28999.00,
        'flipkart_price' => 28999.00,
    ]
);

// Body
$phone->body()->updateOrCreate([], [
    'dimensions' => '160.7 x 73.1 x 8.2 mm (6.33 x 2.88 x 0.32 in)',
    'weight' => '186 g (6.56 oz)',
    'build_material' => 'Glass front (Gorilla Glass 7i), plastic frame, silicone polymer (eco leather) back',
    'sim' => 'Nano-SIM / Nano-SIM + eSIM / Nano-SIM + Nano-SIM',
    'ip_rating' => 'IP68/IP69 dust/water resistant (up to 1.5m for 30 min)',
    'colors' => 'Pantone: Shadow, Dazzling Blue, Sparkling Grape',
    'display_type' => 'P-OLED, 1B colors, 120Hz, 720Hz PWM, HDR10+, 4500 nits peak',
    'display_size' => '6.7 inches',
    'display_resolution' => '1220 x 2712 pixels',
    'pixel_density' => '~444 ppi',
    'display_protection' => 'Corning Gorilla Glass 7i, Mohs level 4',
    'display_features' => '1595 nits max brightness (measured), 4500 nits peak',
    'screen_to_body_ratio' => '~92.2%',
    'aspect_ratio' => '20:9',
    'measured_display_brightness' => '1595 nits max brightness (measured)',
    'pwm_dimming' => '720Hz PWM',
]);

// Platform
$phone->platform()->updateOrCreate([], [
    'os' => 'Android 15, up to 3 major Android upgrades',
    'chipset' => 'Mediatek Dimensity 8350 Extreme (4 nm)',
    'cpu' => 'Octa-core (1x3.35 GHz Cortex-A715 & 3x3.20 GHz Cortex-A715 & 4x2.20 GHz Cortex-A510)',
    'gpu' => 'Mali G615-MC6',
    'memory_card_slot' => 'No',
    'internal_storage' => '256GB/512GB',
    'ram' => '8GB/12GB/16GB',
    'storage_type' => 'UFS 4.0',
    // Developer Freedom defaults based on general Moto policy unless specified
    'bootloader_unlockable' => true, 
    'os_openness' => 'Near-AOSP / Minimal restrictions',
    'turnip_support' => false,
    'turnip_support_level' => 'None', // Mali GPU
    'custom_rom_support' => 'Limited', // Mediatek usually limited
]);

// Camera
$phone->camera()->updateOrCreate([], [
    'main_camera_specs' => '50 MP, f/1.8, 24mm (wide), 1/1.56", 1.0µm, multi-directional PDAF, OIS + 10 MP, f/2.0, 73mm (telephoto), 1/3.94", 1.0µm, PDAF, 3x optical zoom, OIS + 50 MP, f/2.0, 120˚ (ultrawide), 1/2.76", 0.64µm, PDAF',
    'main_camera_features' => 'Color spectrum sensor, LED flash, HDR, panorama, Pantone Validated Colour and Skin Tones',
    'main_video_capabilities' => '4K@30fps, 1080p@30/60/120/240fps, gyro-EIS, HDR10+',
    'selfie_camera_specs' => '50 MP, f/2.0, (wide), 1/2.76", 0.64µm',
    'selfie_camera_features' => 'HDR',
    'selfie_video_capabilities' => '4K@30fps, 1080p@30/120fps',
    // Granular details
    'main_camera_sensors' => 'Main: 1/1.56", Tele: 1/3.94", UW: 1/2.76"',
    'main_camera_apertures' => 'f/1.8 (main), f/2.0 (tele), f/2.0 (ultrawide)',
    'main_camera_focal_lengths' => '24mm (main), 73mm (tele)',
    'main_camera_ois' => 'Yes (Main & Tele)',
    'main_camera_pdaf' => 'Multi-directional PDAF',
    'main_camera_zoom' => '3x optical zoom',
    'selfie_camera_sensor' => '1/2.76"',
    'selfie_camera_aperture' => 'f/2.0',
]);

// Connectivity
$phone->connectivity()->updateOrCreate([], [
    'wlan' => 'Wi-Fi 802.11 a/b/g/n/ac/6e, tri-band',
    'bluetooth' => 'Yes', 
    'positioning' => 'GPS, GLONASS, GALILEO, BDS, NavIC',
    'nfc' => 'Yes',
    'radio' => 'No',
    'usb' => 'USB Type-C, OTG',
    'sensors' => 'Fingerprint (under display, optical), accelerometer, gyro, proximity, compass, Smart Connect support',
    'loudspeaker' => 'Yes, with stereo speakers (with Dolby Atmos)',
    'jack_3_5mm' => 'No',
    'network_bands' => 'GSM / HSPA / LTE / 5G',
    'sar_value' => '0.90 W/kg (head), 1.39 W/kg (body)',
    'audio_quality' => '24-bit/192kHz Hi-Res audio',
    'loudness_test_result' => '-25.1 LUFS (Very good)',
]);

// Battery
$phone->battery()->updateOrCreate([], [
    'battery_type' => 'Si/C Li-Ion 6000 mAh',
    'charging_wired' => '90W wired',
    'charging_specs_detailed' => '90W wired, PD3.0',
    'charging_wireless' => '15W wireless',
    'charging_reverse' => '5W reverse wired',
    'reverse_wired' => '5W',
]);

// Benchmarks
$phone->benchmarks()->updateOrCreate([], [
    'antutu_score' => 1702539, // v11 from user
    'geekbench_single' => 1412,
    'geekbench_multi' => 4476, // v6
    'dmark_wild_life_extreme' => 3221,
    'battery_endurance_hours' => 67, // from battery endurance text
]);

echo "Motorola Edge 60 Pro added/updated successfully.\n";
