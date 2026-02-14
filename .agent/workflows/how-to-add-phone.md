---
description: How to add a new phone to the database with complete specifications.
---

# Handling New Phone Data

This workflow details how to populate the database when you have raw phone specifications.

## 1. Database Structure

Data is distributed across 7 tables. When adding a phone, you must create records in all of them linked by `phone_id`.

| Table | Purpose | Key Fields |
| :--- | :--- | :--- |
| `phones` | Core Info | `name`, `brand`, `price`, `release_date`, `image_url` |
| `spec_bodies` | Physical & Display | `dimensions`, `weight`, `build_material`, `display_type`, `display_size`, `display_resolution` |
| `spec_platforms`| OS & Hardware | `os`, `chipset`, `cpu`, `gpu`, `ram`, `internal_storage` |
| `spec_cameras` | Camera Details | `main_camera_specs`, `main_video_capabilities`, `selfie_camera_specs`, `main_camera_sensors`, `main_camera_ois` |
| `spec_connectivities` | Comms & Sensors | `wlan`, `bluetooth`, `nfc`, `infrared`, `sensors`, `loudspeaker`, `jack_3_5mm`, `sar_value`, `audio_quality` |
| `spec_batteries` | Power | `battery_type`, `charging_wired`, `charging_wireless`, `charging_reverse`, `charging_specs_detailed` |
| `benchmarks` | Performance Scores | `antutu_score`, `geekbench_single`, `geekbench_multi` |

## 2. Image Processing

Phone images should have a transparent background and be stored in `storage/app/public/phones/`.

**Helper Script**:
Use the provided Python script (which uses `rembg`) to download, remove background (AI-powered), and save the image.

```bash
# Usage: .venv/bin/python scripts/process_image_rembg.py <URL> <OUTPUT_PATH>
.venv/bin/python scripts/process_image_rembg.py "https://example.com/phone.jpg" "storage/app/public/phones/oneplus-15.png"
```

## 3. Populating Data (Tinker Method)

The most efficient way to add a single phone is using `php artisan tinker`.

### Step 1: Create Main Phone Record
```php
$phone = App\Models\Phone::firstOrCreate(
    ['name' => 'OnePlus 15'],
    [
        'brand' => 'OnePlus',
        'model_variant' => 'CPH2747 / CPH2745 / PLK110 / CPH2749',
        'price' => 72998.00,
        'overall_score' => 95, 
        'release_date' => '2025-10-28',
        'image_url' => '/storage/phones/oneplus-15.png',
        'amazon_url' => 'https://www.amazon.in/OnePlus-Snapdragon%C2%AE-7300mAh-Personalised-Game-Changing/dp/B0FTR2PJTV?th=1',
        'flipkart_url' => 'https://www.flipkart.com/oneplus-15r-5g-charcoal-black-256-gb/p/itmc1c624041ba6c',
    ]
);

// Body
$phone->body()->updateOrCreate([], [
    'dimensions' => '161.4 x 76.7 x 8.1 mm',
    'weight' => '211 g / 215 g',
    'build_material' => 'Glass front (Gorilla Glass Victus 2), aluminum alloy frame, glass back (Gorilla Glass 7i / Crystal Shield) or fiber-reinforced plastic back',
    'sim' => 'Nano-SIM + Nano-SIM + eSIM',
    'ip_rating' => 'IP68/IP69K',
    'display_type' => 'LTPO AMOLED, 1B colors, 165Hz, PWM, Dolby Vision, HDR10+, HDR Vivid',
    'display_size' => '6.78 inches',
    'display_resolution' => '1272 x 2772 pixels',
    'pixel_density' => '~450 ppi density',
    'display_protection' => 'Corning Gorilla Glass Victus 2',
    'screen_glass' => 'Gorilla Glass 7i / Crystal Shield',
    'display_features' => '800 nits (typ), 1364 nits (measured), 1800 nits (HBM), Ultra HDR support',
    'display_brightness' => '800 nits (typ), 1800 nits (HBM)',
    'pwm_dimming' => 'PWM Dimming',
    'touch_sampling_rate' => '240Hz',
    'screen_to_body_ratio' => '~90.8%',
    'colors' => 'Charcoal Black, Silver, Blue',
]);

// Platform
$phone->platform()->updateOrCreate([], [
    'os' => 'Android 16, OxygenOS 16 (Global) / ColorOS 16 (China)',
    'chipset' => 'Qualcomm SM8850-AC Snapdragon 8 Elite Gen 5 (3 nm)',
    'cpu' => 'Octa-core (2x4.6 GHz Oryon V3 Phoenix L + 6x3.62 GHz Oryon V3 Phoenix M)',
    'gpu' => 'Adreno 840',
    'memory_card_slot' => 'No',
    'internal_storage' => '256GB, 512GB, 1TB',
    'ram' => '12GB, 16GB',
    'storage_type' => 'UFS 4.1',
    // Developer Freedom & Emulation (UEPS 4.5+)
    'bootloader_unlockable' => true, // true/false (False for Vivo/iQOO unless specific model exception)
    'os_openness' => 'Near-AOSP / Minimal restrictions / Easy root', // Options: 'Near-AOSP...', 'Moderately restricted', 'Restricted OEM skin' (Vivo/iQOO = Restricted)
    'turnip_support_level' => 'Full', // Options: 'Full', 'Stable', 'Partial', 'None' (Snapdragon 8 Gen 1+ usually 'Full' or 'Stable')
    'gpu_emulation_tier' => 'Adreno 8xx Elite-class', // Options: 'Adreno 8xx...', 'Adreno 7xx...', 'Adreno 6xx...', 'Immortalis...', 'Mali Valhall...', 'Mali...'
    'custom_rom_support' => 'Major', // Options: 'Major', 'Limited', 'None' (Vivo/iQOO usually 'None' or 'Low')

// Camera
$phone->camera()->updateOrCreate([], [
    'main_camera_specs' => '50 MP, f/1.8, 24mm (wide), 1/1.56", 1.0µm, multi-directional PDAF, OIS + 50 MP, f/2.8, 80mm (periscope telephoto), 1/2.76", 0.64µm, 3.5x optical zoom, PDAF, OIS + 50 MP, f/2.0, 16mm, 116˚ (ultrawide), 1/2.88", 0.61µm, PDAF',
    'main_camera_features' => 'Laser focus, color spectrum sensor, LED flash, HDR, panorama, LUT preview, Hasselblad',
    'main_video_capabilities' => '8K@30fps, 4K@30/60/120fps, 1080p@30/60/240fps, Auto HDR, gyro-EIS, Dolby Vision, LUT',
    'selfie_camera_specs' => '32 MP, f/2.4, 21mm (wide), 1/2.74", 0.64µm',
    'selfie_camera_features' => 'HDR, panorama',
    'selfie_video_capabilities' => '4K@30/60fps, 1080p@30/60fps, gyro-EIS, HDR',
    'main_camera_sensors' => 'Main: 1/1.56", Tele: 1/2.76", UW: 1/2.88"',
    'main_camera_apertures' => 'f/1.8 (main), f/2.8 (tele), f/2.0 (ultrawide)',
    'main_camera_focal_lengths' => '24mm (main), 80mm (tele), 16mm (ultrawide)',
    'main_camera_ois' => 'Yes (Main & Tele)',
]);

// Connectivity
$phone->connectivity()->updateOrCreate([], [
    'wlan' => 'Wi-Fi 802.11 a/b/g/n/ac/6/7, dual or tri-band, Wi-Fi Direct',
    'bluetooth' => '6.0, A2DP, LE, aptX HD, aptX Adaptive, LHDC 5',
    'positioning' => 'GPS (L1+L5), GLONASS (G1), BDS (B1I+B1c+B2a), GALILEO (E1+E5a), QZSS (L1+L5), NavIC (L5)',
    'nfc' => 'Yes',
    'infrared' => 'Yes',
    'radio' => 'No',
    'usb' => 'USB Type-C 3.2, OTG',
    'sensors' => 'Fingerprint (under display, ultrasonic), accelerometer, proximity, gyro (UAV-grade), compass, barometer',
    'loudspeaker' => 'Yes, with stereo speakers',
    'jack_3_5mm' => 'No',
    'network_bands' => 'GSM / HSPA / LTE / 5G',
    'sar_value' => '1.17 W/kg (head), 1.00 W/kg (body)',
    'loudness_test_result' => '-24.8 LUFS (Very good)',
    'audio_quality' => '24-bit/192kHz Hi-Res audio',
]);

// Battery
$phone->battery()->updateOrCreate([], [
    'battery_type' => 'Si/C Li-Ion 7300 mAh',
    'charging_wired' => '120W wired (UFCS, PPS, PD, QC)',
    'charging_specs_detailed' => '120W wired, 120W UFCS, 55W PPS, 36W PD, 36W QC, 50% in 15 min, 100% in 40 min',
    'charging_wireless' => '50W wireless',
    'charging_reverse' => '10W reverse wireless, 5W reverse wired', 
]);

// Benchmarks
$phone->benchmarks()->updateOrCreate([], [
    'antutu_score' => 3688274, // v11
    'geekbench_single' => 0, 
    'geekbench_multi' => 11062, // v6
    'dmark_wild_life_extreme' => 7370,
]);
```
