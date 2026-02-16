---
description: How to add a new phone to the database with complete specifications.
---

# Handling New Phone Data

This workflow details how to populate the database when you have raw phone specifications.

## 1. Database Structure

Data is distributed across 7 tables. When adding a phone, you must create records in all of them linked by `phone_id`.

| Table | Purpose | Key Fields |
| :--- | :--- | :--- |
| `phones` | Core Info | `name`, `brand`, `model_variant`, `price`, `release_date`, `announced_date`, `image_url`, `amazon_url`, `flipkart_url`, `amazon_price`, `flipkart_price` |
| `spec_bodies` | Physical & Display | `dimensions`, `weight`, `build_material`, `sim`, `ip_rating`, `colors`, `display_type`, `display_size`, `display_resolution`, `display_brightness`, `pwm_dimming`, `screen_to_body_ratio`, `pixel_density`, `aspect_ratio` |
| `spec_platforms`| OS & Hardware | `os`, `os_details`, `chipset`, `cpu`, `gpu`, `ram`, `internal_storage`, `storage_type`, `bootloader_unlockable`, `turnip_support_level`, `os_openness`, `custom_rom_support` |
| `spec_cameras` | Camera Details | `main_camera_specs`, `main_camera_sensors`, `main_camera_apertures`, `main_camera_ois`, `ultrawide_camera_specs`, `telephoto_camera_specs`, `selfie_camera_specs`, `main_video_capabilities`, `selfie_video_capabilities` |
| `spec_connectivities` | Comms & Sensors | `network_bands` (Required), `wlan`, `bluetooth`, `positioning`, `nfc`, `infrared`, `usb`, `sensors`, `loudspeaker`, `jack_3_5mm`, `has_3_5mm_jack` |
| `spec_batteries` | Power | `battery_type`, `charging_wired`, `charging_wireless`, `charging_reverse`, `reverse_wired`, `reverse_wireless`, `charging_specs_detailed` |
| `benchmarks` | Performance Scores | `antutu_score`, `geekbench_single`, `geekbench_multi`, `dmark_wild_life_extreme`, `dmark_wild_life_stress_stability`, `battery_endurance_hours` |

## 2. Image Processing

Phone images should be stored in `storage/app/public/phones/` with a transparent background (if possible).

### Downloading and Storing Images

If you have an image URL (e.g., from manufacturer or retailer), download and store it:

```bash
# Navigate to the storage directory
cd /home/abhay/repos/phone_finder/storage/app/public/phones/

# Download the image (replace URL and filename)
wget -O oppo-k13-turbo-pro.png "https://media-ik.croma.com/prod/https://media.tatacroma.com/Croma%20Assets/Communication/Mobiles/Images/318038_0_e_-nASo2LY.png?updatedAt=1756212278973"

# Or use curl
curl -o oppo-k13-turbo-pro.png "https://media-ik.croma.com/prod/https://media.tatacroma.com/Croma%20Assets/Communication/Mobiles/Images/318038_0_e_-nASo2LY.png?updatedAt=1756212278973"
```

**Image naming convention**: Use lowercase with hyphens, matching the phone name:
- "Oppo K13 Turbo Pro" → `oppo-k13-turbo-pro.png`
- "OnePlus 15" → `oneplus-15.png`

**Then reference in database**: `/storage/phones/oppo-k13-turbo-pro.png`

**Optional**: Use Python script to remove background:
```bash
python process_image.py storage/app/public/phones/oppo-k13-turbo-pro.png
```

## 3. Populating Data

### Method 1: Artisan Command (RECOMMENDED) ✅

The **safest and easiest** way to add a phone:

```bash
php artisan phone:add
```

This guided command:
- ✅ Prompts for all required fields
- ✅ Validates data types (stability as integer)
- ✅ Shows errors immediately (no silent failures)
- ✅ Automatically calculates scores
- ✅ Offers to recalculate all scores and clear cache

**Use this method to avoid the issues we encountered!**

### Method 2: Interactive Tinker (Alternative)

For more control, use **interactive** tinker (not --execute):

### Example: Full Manual Addition (Tinker)

> ⚠️ **Warning**: Use interactive tinker, NOT `--execute` for complex operations.
> The `--execute` flag can silently fail on multi-line commands.

**Step 1: Start Interactive Tinker**
```bash
php artisan tinker
```

**Step 2: Create Main Phone Record**
```php
$phone = App\\Models\\Phone::firstOrCreate(
    ['name' => 'Oppo K13 Turbo Pro'],
    [
        'brand' => 'Oppo',
        'model_variant' => 'PLE110',
        'price' => 27999.00, // ₹27,999 (340 EUR ≈ ₹27,999)
        'overall_score' => 0, // Will be calculated automatically
        'release_date' => '2025-07-25',
        'announced_date' => '2025-07-21',
        'image_url' => '/storage/phones/oppo-k13-turbo-pro.png',
        'amazon_url' => null, // No Amazon link provided
        'flipkart_url' => 'https://www.flipkart.com/oppo-k13-turbo-pro-5g-midnight-maverick-256-gb/p/itmeefeb718f01fe',
        'amazon_price' => null,
        'flipkart_price' => 27999.00,
    ]
);

// Body Specs
$phone->body()->updateOrCreate([], [
    'dimensions' => '162.8 x 77.2 x 7.3 mm',
    'weight' => '208 g',
    'build_material' => 'Built-in cooling fan, IPX8/IPX9 water resistant',
    'cooling_type' => null, // 'Active Fan', 'Vapor Chamber', 'Graphite', or null (0 pts if empty)
    'sim' => 'Nano-SIM + Nano-SIM',
    'ip_rating' => 'IPX8/IPX9',
    'colors' => 'Silver Knight, Purple Phantom, Midnight Maverick',
    'display_type' => 'AMOLED, 1B colors, 120Hz',
    'display_size' => '6.8 inches',
    'display_resolution' => '1280 x 2800 pixels',
    'display_brightness' => '1600 nits (peak)',
    'measured_display_brightness' => null, // Not provided
    'pwm_dimming' => null, // Not specified
    'screen_to_body_ratio' => '~89.8%',
    'pixel_density' => '~453 ppi',
    'aspect_ratio' => '19.5:9',
    'screen_area' => '112.8 cm²',
    'touch_sampling_rate' => null, // Not specified
    'screen_glass' => null, // Not specified
    'display_protection' => null, // Not specified
    'glass_protection_level' => null,
    'display_features' => '1600 nits peak brightness',
]);

// Platform Specs
$phone->platform()->updateOrCreate([], [
    'os' => 'Android 15, ColorOS 15',
    'os_details' => 'ColorOS 15',
    'chipset' => 'Qualcomm SM8735 Snapdragon 8s Gen 4 (4 nm)',
    'cpu' => 'Octa-core (1x3.21 GHz Cortex-X4 & 3x3.0 GHz Cortex-A720 & 2x2.8 GHz Cortex-A720 & 2x2.0 GHz Cortex-A720)',
    'gpu' => 'Adreno 825',
    'memory_card_slot' => 'No',
    'internal_storage' => '256GB, 512GB',
    'ram' => '8GB, 12GB, 16GB',
    'storage_type' => 'UFS 4.0',
    // Developer Freedom & Emulation
    'bootloader_unlockable' => false, // Oppo typically locked
    'turnip_support' => true,
    'turnip_support_level' => 'Stable', // Snapdragon 8s Gen 4 = Adreno 825
    'os_openness' => 'Restricted OEM skin', // ColorOS is heavily customized
    'gpu_emulation_tier' => 'Adreno 8xx Mid-tier',
    'aosp_aesthetics_score' => 5, // ColorOS is heavily skinned
    'custom_rom_support' => 'Limited', // Oppo has limited ROM community
]);

// Camera Specs
$phone->camera()->updateOrCreate([], [
    'main_camera_specs' => '50 MP, f/1.8, 27mm (wide), PDAF, OIS',
    'main_camera_sensors' => 'Main: 50MP',
    'main_camera_apertures' => 'f/1.8 (main)',
    'main_camera_focal_lengths' => '27mm (main)',
    'main_camera_features' => 'LED flash, HDR, panorama',
    'main_camera_ois' => 'Yes (Main)',
    'main_camera_zoom' => null,
    'main_camera_pdaf' => 'Yes',
    'main_video_capabilities' => '4K@30/60fps, 1080p@30fps',
    'ultrawide_camera_specs' => null, // Only 2MP depth sensor mentioned
    'telephoto_camera_specs' => null,
    'video_features' => null,
    'selfie_camera_specs' => '16 MP, f/2.4, 22mm (wide)',
    'selfie_camera_sensor' => '16MP',
    'selfie_camera_aperture' => 'f/2.4',
    'selfie_camera_features' => null,
    'selfie_camera_autofocus' => false,
    'selfie_video_capabilities' => '1080p@30fps',
    'selfie_video_features' => null,
]);

// Connectivity Specs
$phone->connectivity()->updateOrCreate([], [
    'network_bands' => 'GSM / CDMA / HSPA / LTE / 5G',
    'wlan' => 'Wi-Fi 802.11 a/b/g/n/ac/6/7, dual-band',
    'wifi_bands' => 'Dual-band',
    'bluetooth' => '5.4, A2DP, LE, aptX HD, LHDC 5',
    'positioning' => 'GPS, GLONASS, GALILEO, BDS, QZSS',
    'positioning_details' => 'GPS, GLONASS, GALILEO, BDS, QZSS',
    'nfc' => 'Yes',
    'infrared' => 'Yes',
    'radio' => 'No',
    'usb' => 'USB Type-C 2.0, OTG',
    'usb_details' => 'USB Type-C 2.0, OTG',
    'sensors' => 'Fingerprint (under display, optical), accelerometer, gyro, proximity, compass',
    'loudspeaker' => 'Yes, with stereo speakers',
    'audio_quality' => null,
    'loudness_test_result' => null,
    'jack_3_5mm' => 'No',
    'has_3_5mm_jack' => false,
    'sar_value' => null,
]);

// Battery Specs
$phone->battery()->updateOrCreate([], [
    'battery_type' => 'Li-Po 7000 mAh',
    'charging_wired' => '80W wired',
    'charging_specs_detailed' => '80W wired, 13.5W PD, 44W UFCS, 33W PPS',
    'charging_wireless' => null,
    'charging_reverse' => 'Reverse wired',
    'reverse_wired' => 'Yes',
    'reverse_wireless' => null,
]);

// Benchmarks
$phone->benchmarks()->updateOrCreate([], [
    'antutu_score' => 2130234, // AnTuTu v11
    'antutu_v10_score' => null,
    'geekbench_single' => 2135, // Geekbench 6
    'geekbench_multi' => 6613, // Geekbench 6
    'dmark_wild_life_extreme' => 3984, // 3DMark Wild Life Extreme
    'dmark_wild_life_stress_stability' => 99, // ⚠️ MUST BE INTEGER (not 98.9) - Round to nearest whole number
    'dmark_test_type' => 'Wild Life Extreme',
    'battery_endurance_hours' => null, // Not provided
    'battery_active_use_score' => null,
    'charge_time_test' => null,
    'repairability_score' => null,
    'free_fall_rating' => null,
    'energy_label' => null,
]);

// Update all calculated scores (FPI, UEPS, GPX, Value Score)
$phone->updateScores();
```

## 5. Important Field Notes

### Required Fields
- **phones**: `name`, `brand`, `price`, `release_date`
- **spec_connectivities**: `network_bands` (for Technology row in comparison)
- **benchmarks**: `dmark_wild_life_stress_stability` (CRITICAL for GPX-300 gaming score)

### Key Fields by Category

#### Phones Table
- `announced_date`: Announcement date (different from release_date)
- `amazon_price`, `flipkart_price`: Store-specific pricing
- Scores (`overall_score`, `ueps_score`, `value_score`, `gpx_score`) are auto-calculated via `updateScores()`

#### Spec Bodies
- `aspect_ratio`: Display aspect ratio (e.g., "19.5:9", "20:9")
- `screen_area`: Display area in cm²
- `measured_display_brightness`: Lab-tested brightness (vs claimed)
- `cooling_type`: **NEW** - Cooling hardware type for GPX-300 scoring
  - Values: `'Active Fan'` (20 pts), `'Vapor Chamber'` (15 pts), `'Graphite'` (5 pts), `null` (0 pts)
  - **IMPORTANT**: If empty/null when adding phone, GPX gives 0 points (no assumptions)
  - Examples: ROG phones = 'Active Fan', iQOO/OnePlus flagships = 'Vapor Chamber', mid-range = 'Graphite'

#### Spec Platforms
- `os_details`: Specific OS variant (Global vs China)
- `os_openness`: "Near-AOSP", "Moderately restricted", "Restricted OEM skin"
- `turnip_support_level`: "Full", "Stable", "Partial", "None"
- `gpu_emulation_tier`: GPU class for emulation
- `custom_rom_support`: "Major", "Limited", "None"

#### Spec Cameras
- `ultrawide_camera_specs`: Ultrawide camera specifications
- `telephoto_camera_specs`: Telephoto/periscope camera specs
- `main_camera_pdaf`: PDAF support details
- `selfie_video_features`: Selfie video specific features

#### Spec Connectivities
- `positioning_details`: Detailed GPS/GNSS info
- `has_3_5mm_jack`: Boolean for 3.5mm jack presence
- `wifi_bands`: "Dual-band", "Tri-band"

#### Spec Batteries
- `reverse_wired`: Reverse wired charging support
- `reverse_wireless`: Reverse wireless charging power

#### Benchmarks
- `dmark_wild_life_stress_stability`: **REQUIRED** - Stability % (0-100) from 3DMark stress test
  - ⚠️ **CRITICAL**: Must be INTEGER, not decimal (use 99, not 98.9)
  - Database field type is `integer`, decimals will cause SQL error
- `antutu_v10_score`: AnTuTu v10 score (if available separately from v11)
- `battery_endurance_hours`: GSMArena battery endurance rating

## 6. Data Extraction Tips

### From GSMArena Specs:
- **Network Technology** → `network_bands`
- **Launch Announced** → `announced_date`
- **Launch Status** → `release_date`
- **Body Dimensions** → `dimensions`
- **Body Weight** → `weight`
- **Display Type** → `display_type` + `display_brightness` (extract nits)
- **Display Size** → `display_size` + `screen_area` + `screen_to_body_ratio`
- **Display Resolution** → `display_resolution` + `pixel_density` + `aspect_ratio`
- **Platform OS** → `os` + `os_details`
- **Platform Chipset** → `chipset`
- **Platform CPU** → `cpu`
- **Platform GPU** → `gpu`
- **Memory Internal** → `internal_storage` + `ram` + `storage_type`
- **Main Camera** → Parse into `main_camera_specs`, `main_camera_sensors`, `main_camera_apertures`, `main_camera_focal_lengths`, `main_camera_ois`
- **Selfie Camera** → `selfie_camera_specs`, `selfie_camera_aperture`, `selfie_camera_sensor`
- **Sound** → `loudspeaker` + `jack_3_5mm` + `has_3_5mm_jack`
- **Comms** → `wlan`, `bluetooth`, `positioning`, `nfc`, `infrared`, `usb`
- **Features Sensors** → `sensors`
- **Battery Type** → `battery_type` (extract mAh)
- **Battery Charging** → Parse into `charging_wired`, `charging_wireless`, `charging_reverse`, `reverse_wired`, `reverse_wireless`

### From Benchmark Data:
- AnTuTu v11 → `antutu_score`
- Geekbench 6 Single → `geekbench_single`
- Geekbench 6 Multi → `geekbench_multi`
- 3DMark Wild Life Extreme → `dmark_wild_life_extreme`
- **3DMark Stability %** → `dmark_wild_life_stress_stability` (CRITICAL!)

## 7. QA Checklist Before Saving

- [ ] `name` is unique in database
- [ ] `price` is in INR (convert if needed)
- [ ] `release_date` and `announced_date` are set
- [ ] `network_bands` is filled (required)
- [ ] Camera specs parsed into granular fields (sensors, apertures, focal lengths)
- [ ] `dmark_wild_life_stress_stability` is set (required for GPX-300)
- [ ] Developer metrics set based on brand (Oppo/Vivo = restricted, OnePlus/Xiaomi = open)
- [ ] `has_3_5mm_jack` boolean matches `jack_3_5mm` string
- [ ] `updateScores()` called to calculate all scoring metrics
- [ ] URLs don't exceed varchar limits (strip tracking params if needed)

## 8. Post-Addition: Update Rankings & Leaderboards ⚠️ CRITICAL

After adding a new phone, you **MUST** recalculate all scores and clear caches to update the leaderboards properly.

### Why This is Needed

The ranking system uses:
- **FPI (overall_score)**: Flagship Performance Index - weighted benchmark score
- **UEPS (ueps_score)**: User Experience Performance Score
- **GPX (gpx_score)**: Gaming Performance Index (GPX-300)
- **Value Score (value_score)**: Performance per rupee metric

These scores are **relative** - they depend on the max scores in the database. When you add a new phone with higher benchmarks, ALL phones' scores need to be recalculated to maintain accurate rankings.

### Step-by-Step Process

#### 1. Recalculate All Scores

Run the artisan command to recalculate FPI, UEPS, GPX, and Value scores for ALL phones:

```bash
php artisan phone:recalculate-scores
```

This command:
- Clears the benchmark max scores cache
- Recalculates scores for all phones based on new max values
- Updates `overall_score`, `ueps_score`, `gpx_score`, `value_score` in database

**Expected output:**
```
Starting score recalculation...
████████████████████████████████████████ 100%
All scores recalculated successfully!
```

#### 2. Clear Application Caches

The rankings and phone listings are heavily cached. Clear all caches:

```bash
# Clear all application caches
php artisan cache:clear

# Or clear specific cache keys if you know them
# php artisan cache:forget phones_index_html_value_score
# php artisan cache:forget rankings_ueps_ueps_score_desc_1_html
```

#### 3. Verify Rankings

Check that the new phone appears in the rankings:

```bash
# In tinker
php artisan tinker

# Check the new phone's scores
$phone = App\Models\Phone::where('name', 'Oppo K13 Turbo Pro')->first();
echo "FPI: " . $phone->overall_score . "\n";
echo "UEPS: " . $phone->ueps_score . "\n";
echo "GPX: " . $phone->gpx_score . "\n";
echo "Value: " . $phone->value_score . "\n";

# Check rankings (top 5 by value score)
App\Models\Phone::orderBy('value_score', 'desc')->take(5)->pluck('name', 'value_score');
```

#### 4. Test in Browser

Visit these URLs to verify:
- **Homepage**: `http://localhost:8000/` - Check if new phone appears
- **UEPS Rankings**: `http://localhost:8000/rankings?tab=ueps`
- **Performance Rankings**: `http://localhost:8000/rankings?tab=performance`
- **Gaming Rankings**: `http://localhost:8000/rankings?tab=gaming`
- **Value Rankings**: `http://localhost:8000/rankings?tab=value`

### Quick Reference Commands

```bash
# Full workflow after adding a phone
php artisan phone:recalculate-scores  # Recalculate all scores
php artisan cache:clear                # Clear caches
php artisan serve                      # Restart server (if needed)
```

### Troubleshooting

**Problem**: New phone doesn't appear in rankings
- **Solution**: Run `php artisan phone:recalculate-scores` and `php artisan cache:clear`

**Problem**: Scores seem incorrect
- **Solution**: Check that `updateScores()` was called when adding the phone, then run recalculate command

**Problem**: Rankings show old data
- **Solution**: Clear cache with `php artisan cache:clear`