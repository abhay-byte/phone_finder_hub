<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PhoneSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        try {
            dump('Seeding OnePlus 13...');
            dump('Seeding OnePlus 13...');
            $phone = \App\Models\Phone::firstOrCreate(
                ['name' => 'OnePlus 13'],
                [
                    'brand' => 'OnePlus',
                    'model_variant' => 'CPH2655',
                    'price' => 60999.00,
                    'overall_score' => 95,
                    'release_date' => '2024-11-01',
                    'image_url' => '/assets/oneplus-13_nobg.png',
                ]
            );

            // Force update fields
            $phone->update([
                'image_url' => '/assets/oneplus-13_nobg.png',
                'amazon_url' => 'https://www.amazon.in/dp/B0DQ8MGRNX',
                'amazon_price' => 60999.00,
                'flipkart_url' => 'https://www.flipkart.com/oneplus-13-midnight-ocean-256-gb/p/itmb4659fd2a037f',
                'flipkart_price' => 60999.00,
            ]);

            if (!$phone->body) {
                $phone->body()->create([
                    'dimensions' => '162.9 x 76.5 x 8.5 mm',
                    'weight' => '210 g',
                    'build_material' => 'Glass front (Ceramic Guard), glass back or silicone polymer back (eco leather), aluminum frame',
                    'sim' => 'Nano-SIM + Nano-SIM + eSIM',
                    'ip_rating' => 'IP68/IP69',
                    'colors' => 'Black Eclipse, Arctic Dawn, Midnight Ocean',
                    'display_type' => 'LTPO 4.1 AMOLED, 1B colors, 120Hz, Dolby Vision, HDR10+',
                    'display_size' => '6.82 inches',
                    'display_resolution' => '1440 x 3168 pixels',
                    'display_protection' => 'Ceramic Guard glass',
                    'display_features' => '4500 nits (peak), 1600 nits (HBM), 800 nits (typ), 2160Hz PWM',
                ]);
            }

            if (!$phone->platform) {
                $phone->platform()->create([
                    'os' => 'Android 15, OxygenOS 16',
                    'chipset' => 'Qualcomm SM8750-AB Snapdragon 8 Elite (3 nm)',
                    'cpu' => 'Octa-core (2x4.32 GHz Oryon V2 Phoenix L + 6x3.53 GHz Oryon V2 Phoenix M)',
                    'gpu' => 'Adreno 830',
                    'memory_card_slot' => 'No',
                    'internal_storage' => '256GB/512GB/1TB',
                    'ram' => '12GB/16GB/24GB',
                    'storage_type' => 'UFS 4.0',
                    'bootloader_unlockable' => true,
                    'turnip_support' => true,
                    'aosp_aesthetics_score' => 8,
                ]);
            }

            if (!$phone->camera) {
                $phone->camera()->create([
                    'main_camera_specs' => '50 MP, f/1.6, 23mm (wide) + 50 MP, f/2.6, 73mm (periscope telephoto) + 50 MP, f/2.0, 15mm (ultrawide)',
                    'main_camera_features' => 'Hasselblad Color Calibration, Dual-LED flash, HDR, panorama',
                    'main_video_capabilities' => '8K@30fps, 4K@30/60fps, 1080p@30/60/240/480fps, Dolby Vision',
                    'selfie_camera_specs' => '32 MP, f/2.4, 21mm (wide)',
                    'selfie_camera_features' => 'HDR, panorama',
                    'selfie_video_capabilities' => '4K@30/60fps, 1080p@30/60fps',
                ]);
            }

            if (!$phone->connectivity) {
                $phone->connectivity()->create([
                    'wlan' => 'Wi-Fi 802.11 a/b/g/n/ac/6/7, dual or tri-band, Wi-Fi Direct',
                    'bluetooth' => '5.4, A2DP, LE, aptX HD, LHDC 5',
                    'positioning' => 'GPS (L1+L5), GLONASS (G1), BDS (B1I+B1c+B2a), GALILEO (E1+E5a), QZSS (L1+L5), NavIC',
                    'nfc' => 'Yes',
                    'infrared' => 'Yes',
                    'radio' => 'No',
                    'usb' => 'USB Type-C 3.2, OTG',
                    'sensors' => 'Fingerprint (under display, ultrasonic), accelerometer, gyro, proximity, compass, barometer, color spectrum',
                    'loudspeaker' => 'Yes, with stereo speakers',
                    'jack_3_5mm' => 'No',
                ]);
            }

            if (!$phone->battery) {
                $phone->battery()->create([
                    'battery_type' => 'Si/C Li-Ion 6000 mAh, non-removable',
                    'charging_wired' => '100W',
                    'charging_wireless' => '50W',
                    'charging_reverse' => '10W',
                ]);
            }

            if (!$phone->benchmarks) {
                $phone->benchmarks()->create([
                    'antutu_score' => 3034524, // v11
                    'geekbench_single' => 3000,
                    'geekbench_multi' => 9278, // v6
                    'dmark_wild_life_extreme' => 6615,
                    'battery_endurance_hours' => 15.28,
                ]);
            } else {
                $phone->benchmarks->update([
                    'antutu_score' => 3034524, // v11
                ]);
            }

            // OnePlus 15R
            $phone15r = \App\Models\Phone::firstOrCreate(
                ['name' => 'OnePlus 15R'],
                [
                    'brand' => 'OnePlus',
                    'model_variant' => 'CPH2769',
                    'price' => 47999.00,
                    'overall_score' => 91,
                    'release_date' => '2025-12-22',
                    'image_url' => '/assets/oneplus-15r_nobg.png',
                ]
            );
            
            // Force update fields
            $phone15r->update([
                'image_url' => 'https://m.media-amazon.com/images/I/61h53LtSVVL._AC_UF894,1000_QL80_.jpg',
                'amazon_url' => 'https://www.amazon.in/dp/B0FZT1LXPZ',
                'amazon_price' => 47999.00,
                'flipkart_url' => 'https://www.flipkart.com/oneplus-15r-5g-mint-breeze-256-gb/p/itmc1c624041ba6c',
                'flipkart_price' => 47999.00,
            ]);

            if (!$phone15r->body) {
                $phone15r->body()->create([
                    'dimensions' => '163.4 x 77 x 8.1 mm',
                    'weight' => '213 g',
                    'build_material' => 'Glass front (Gorilla Glass 7i), aluminum alloy frame',
                    'sim' => 'Dual SIM (Nano-SIM, dual stand-by)',
                    'ip_rating' => 'IP68/IP69K',
                    'colors' => 'Charcoal Black, Mint Breeze, Electric Violet',
                    'display_type' => 'AMOLED, 1B colors, 165Hz, HDR10+',
                    'display_size' => '6.83 inches',
                    'display_resolution' => '1272 x 2800 pixels',
                    'display_protection' => 'Corning Gorilla Glass 7i',
                    'display_features' => '1800 nits (HBM), 3600 nits (peak)',
                ]);
            }

            if (!$phone15r->platform) {
                $phone15r->platform()->create([
                    'os' => 'Android 16, OxygenOS 16',
                    'chipset' => 'Snapdragon 8 Gen 5 (3 nm)',
                    'cpu' => 'Octa-core (2x3.8 GHz & 6x3.32 GHz)',
                    'gpu' => 'Adreno 829',
                    'memory_card_slot' => 'No',
                    'internal_storage' => '256GB/512GB',
                    'ram' => '12GB',
                    'storage_type' => 'UFS 4.1',
                    'bootloader_unlockable' => true,
                    'turnip_support' => true,
                    'aosp_aesthetics_score' => 8,
                ]);
            }

            if (!$phone15r->camera) {
                $phone15r->camera()->create([
                    'main_camera_specs' => '50 MP (wide) + 8 MP (ultrawide)',
                    'main_camera_features' => 'LED flash, HDR, panorama',
                    'main_video_capabilities' => '4K@30/60/120fps, 1080p@30/60/120/240fps',
                    'selfie_camera_specs' => '32 MP (wide)',
                    'selfie_camera_features' => 'HDR, panorama',
                    'selfie_video_capabilities' => '4K@30fps, 1080p@30fps',
                ]);
            }

            if (!$phone15r->connectivity) {
                $phone15r->connectivity()->create([
                    'wlan' => 'Wi-Fi 802.11 a/b/g/n/ac/6/7',
                    'bluetooth' => '6.0, A2DP, LE, aptX HD',
                    'positioning' => 'GPS (L1+L5), GLONASS, BDS, GALILEO, QZSS, NavIC',
                    'nfc' => 'Yes',
                    'infrared' => 'Yes',
                    'radio' => 'No',
                    'usb' => 'USB Type-C 2.0, OTG',
                    'sensors' => 'Fingerprint (under display, ultrasonic), accelerometer, gyro, proximity',
                    'loudspeaker' => 'Yes, with stereo speakers',
                    'jack_3_5mm' => 'No',
                ]);
            }

            if (!$phone15r->battery) {
                $phone15r->battery()->create([
                    'battery_type' => 'Si/C Li-Ion 7400 mAh',
                    'charging_wired' => '80W wired',
                    'charging_wireless' => 'No',
                    'charging_reverse' => 'No',
                ]);
            }

            if (!$phone15r->benchmarks) {
                $phone15r->benchmarks()->create([
                    'antutu_score' => 2981677, // v11
                    'geekbench_single' => 3200, 
                    'geekbench_multi' => 9369,
                    'dmark_wild_life_extreme' => 5016,
                    'battery_endurance_hours' => 21.6, 
                ]);
            } else {
                 $phone15r->benchmarks->update([
                    'antutu_score' => 2981677, // v11
                ]);
            }
            
            // OnePlus 15
            $phone15 = \App\Models\Phone::firstOrCreate(
                ['name' => 'OnePlus 15'],
                [
                    'brand' => 'OnePlus',
                    'model_variant' => 'CPH2747',
                    'price' => 72998.00,
                    'overall_score' => 96,
                    'release_date' => '2025-10-28',
                    'image_url' => '/assets/oneplus-15_nobg.png', 
                ]
            );

             // Force update fields
             $phone15->update([
                'image_url' => '/assets/oneplus-15_nobg.png',
                'amazon_url' => 'https://www.amazon.in/OnePlus-Snapdragon%C2%AE-7300mAh-Personalised-Game-Changing/dp/B0FTR2PJTV?th=1',
                'amazon_price' => 72998.00,
                'flipkart_url' => 'https://www.flipkart.com/oneplus-15-5g-sand-storm-256-gb/p/itm0106a23b51268',
                'flipkart_price' => 72998.00,
            ]);

            // ... (rest of OP15 body/platform/camera/connectivity/battery relations same as before, assuming created) ...
            
             if (!$phone15->body) {
                $phone15->body()->create([
                    'dimensions' => '161.4 x 76.7 x 8.1 mm',
                    'weight' => '211 g',
                    'build_material' => 'Glass front (Gorilla Glass Victus 2), aluminum alloy frame, glass back',
                    'sim' => 'Nano-SIM + Nano-SIM + eSIM',
                    'ip_rating' => 'IP68/IP69K',
                    'colors' => 'Infinite Black, Ultra Violet, Sand Storm',
                    'display_type' => 'LTPO AMOLED, 1B colors, 165Hz, Dolby Vision, HDR10+',
                    'display_size' => '6.78 inches',
                    'display_resolution' => '1272 x 2772 pixels',
                    'display_protection' => 'Corning Gorilla Glass Victus 2',
                    'display_features' => '1800 nits (HBM), 800 nits (typ)',
                ]);
            }

            if (!$phone15->platform) {
                $phone15->platform()->create([
                    'os' => 'Android 16, OxygenOS 16',
                    'chipset' => 'Snapdragon 8 Elite Gen 5 (3 nm)',
                    'cpu' => 'Octa-core (2x4.6 GHz Oryon V3 & 6x3.62 GHz Oryon V3)',
                    'gpu' => 'Adreno 840',
                    'memory_card_slot' => 'No',
                    'internal_storage' => '256GB/512GB/1TB',
                    'ram' => '12GB/16GB',
                    'storage_type' => 'UFS 4.1',
                    'bootloader_unlockable' => true,
                    'turnip_support' => true,
                    'aosp_aesthetics_score' => 8,
                ]);
            }

            if (!$phone15->camera) {
                $phone15->camera()->create([
                    'main_camera_specs' => '50 MP (wide) + 50 MP (periscope) + 50 MP (ultrawide)',
                    'main_camera_features' => 'Laser focus, color spectrum, LED flash, HDR, panorama, LUT',
                    'main_video_capabilities' => '8K@30fps, 4K@30/60/120fps, 1080p@30/60/240fps, Dolby Vision',
                    'selfie_camera_specs' => '32 MP (wide)',
                    'selfie_camera_features' => 'HDR, panorama',
                    'selfie_video_capabilities' => '4K@30/60fps, 1080p@30/60fps, HDR',
                ]);
            }

            if (!$phone15->connectivity) {
                $phone15->connectivity()->create([
                    'wlan' => 'Wi-Fi 802.11 a/b/g/n/ac/6/7',
                    'bluetooth' => '6.0, A2DP, LE, aptX HD/Adaptive, LHDC 5',
                    'positioning' => 'GPS (L1+L5), GLONASS, BDS, GALILEO, QZSS, NavIC',
                    'nfc' => 'Yes',
                    'infrared' => 'Yes',
                    'radio' => 'No',
                    'usb' => 'USB Type-C 3.2, OTG',
                    'sensors' => 'Fingerprint (ultrasonic), accelerometer, gyro, compass, barometer',
                    'loudspeaker' => 'Yes, with stereo speakers',
                    'jack_3_5mm' => 'No',
                ]);
            }

            if (!$phone15->battery) {
                $phone15->battery()->create([
                    'battery_type' => 'Si/C Li-Ion 7300 mAh',
                    'charging_wired' => '120W wired',
                    'charging_wireless' => '50W wireless',
                    'charging_reverse' => '10W reverse wireless',
                ]);
            }

            if (!$phone15->benchmarks) {
                $phone15->benchmarks()->create([
                    'antutu_score' => 3688274, // v11
                    'geekbench_single' => 3250, 
                    'geekbench_multi' => 11062, 
                    'dmark_wild_life_extreme' => 7370,
                    'battery_endurance_hours' => 23.1, 
                ]);
            } else {
                $phone15->benchmarks->update([
                    'antutu_score' => 3688274, // v11
                ]);
            }
        } catch (\Exception $e) {
            echo "Seeder Error: " . $e->getMessage() . "\n";
            throw $e;
        }
    }
}
