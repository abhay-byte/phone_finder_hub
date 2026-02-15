<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Phone;

class IqooSeeder extends Seeder
{
    public function run()
    {
        // iQOO 15
        $iqoo15 = Phone::firstOrCreate(
            ['name' => 'vivo iQOO 15'],
            [
                'brand' => 'iQOO',
                'model_variant' => 'I2501 / V2505A',
                'price' => 72998.00,
                'overall_score' => 98,
                'release_date' => '2025-10-20',
                'announced_date' => '2025-10-20',
                'image_url' => '/assets/iqoo-15_nobg.png',
                'amazon_url' => 'https://www.amazon.in/iQOO-Storage-Fastest-Snapdragon-Processor/dp/B0FYGBSKFB',
                'flipkart_url' => 'https://www.flipkart.com/iqoo-15-5g-alpha-512-gb/p/itm9db365d8009fa',
                'amazon_price' => 72998.00,
                'flipkart_price' => 72998.00,
                'ueps_score' => 202,
            ]
        );

        $iqoo15->body()->updateOrCreate([], [
            'dimensions' => '163.7 x 76.8 x 8.1 mm',
            'weight' => '215 g',
            'build_material' => 'Glass front, aluminum frame, fiber-reinforced plastic back or glass back',
            'sim' => 'Nano-SIM + Nano-SIM + eSIM',
            'ip_rating' => 'IP68/IP69',
            'colors' => 'Alpha, Legend, Gray, Green, Blue',
            'display_type' => 'LTPO AMOLED, 1B colors, 144Hz, PWM, Dolby Vision, HDR10+, HDR Vivid',
            'display_size' => '6.85 inches',
            'display_resolution' => '1440 x 3168 pixels',
            'display_protection' => 'Glass front',
            'display_features' => '1000 nits (typ), 2600 nits (HBM), 6000 nits (peak)',
            'screen_to_body_ratio' => '~90.7%',
            'pixel_density' => '~508 ppi',
            'pwm_dimming' => 'Yes (PWM)',
        ]);

        $iqoo15->platform()->updateOrCreate([], [
            'os' => 'Android 16, OriginOS 6',
            'chipset' => 'Qualcomm SM8850-AC Snapdragon 8 Elite Gen 5 (3 nm)',
            'cpu' => 'Octa-core (2x4.6 GHz Oryon V3 Phoenix L + 6x3.62 GHz Oryon V3 Phoenix M)',
            'gpu' => 'Adreno 840',
            'memory_card_slot' => 'No',
            'internal_storage' => '256GB, 512GB, 1TB',
            'ram' => '12GB, 16GB',
            'storage_type' => 'UFS 4.1',
            'bootloader_unlockable' => false,
            'turnip_support' => true,
            'aosp_aesthetics_score' => 4,
            'turnip_support_level' => 'Full',
            'os_openness' => 'Restricted OEM skin',
            'gpu_emulation_tier' => 'Adreno 8xx Elite-class',
            'custom_rom_support' => 'None',
        ]);

        $iqoo15->camera()->updateOrCreate([], [
            'main_camera_specs' => '50 MP (wide) + 50 MP (periscope) + 50 MP (ultrawide)',
            'main_camera_features' => 'LED flash, HDR, panorama',
            'main_video_capabilities' => '8K@30fps, 4K@24/30/60fps, 1080p@30/60/120/240fps, gyro-EIS',
            'main_camera_sensors' => 'Main: 1/1.56", Tele: 1/1.95", UW: 1/2.76"',
            'main_camera_apertures' => 'f/1.9 (main), f/2.6 (tele), f/2.1 (ultrawide)',
            'main_camera_focal_lengths' => '24mm (main), 85mm (tele), 15mm (ultrawide)',
            'main_camera_ois' => 'Yes (Main & Tele)',
            'selfie_camera_specs' => '32 MP, f/2.2, 21mm (wide)',
            'selfie_camera_features' => 'HDR',
            'selfie_video_capabilities' => '4K@30/60fps, 1080p@30/60fps',
        ]);

        $iqoo15->connectivity()->updateOrCreate([], [
            'wlan' => 'Wi-Fi 802.11 a/b/g/n/ac/6/7, dual-band, Wi-Fi Direct',
            'bluetooth' => '6.0, A2DP, LE, aptX HD, aptX Adaptive, aptX Lossless, LHDC 5',
            'positioning' => 'GPS (L1+L5), GLONASS (G1), BDS (B1I+B1c+B2a), GALILEO (E1+E5a), QZSS (L1+L5)',
            'nfc' => 'Yes',
            'infrared' => 'Yes',
            'radio' => 'No',
            'usb' => 'USB Type-C 3.2, OTG',
            'sensors' => 'Fingerprint (under display, ultrasonic), accelerometer, gyro, proximity, compass',
            'loudspeaker' => 'Yes, with stereo speakers',
            'audio_quality' => '24-bit/192kHz Hi-Res audio, Snapdragon Sound',
            'loudness_test_result' => '-25.0 LUFS (Very good)',
            'has_3_5mm_jack' => false,
        ]);

        $iqoo15->battery()->updateOrCreate([], [
            'battery_type' => 'Si/C Li-Ion 7000 mAh',
            'charging_wired' => '100W wired, PPS, PD',
            'charging_wireless' => '40W wireless, Bypass charging',
            'charging_specs_detailed' => '100W wired, PPS, PD, 40W wireless, Bypass charging',
        ]);

        $iqoo15->benchmarks()->updateOrCreate([], [
            'antutu_score' => 3785250,
            'geekbench_single' => 3678,
            'geekbench_multi' => 10466,
            'dmark_wild_life_extreme' => 7229,
        ]);

        // iQOO Neo 10
        $neo10 = Phone::firstOrCreate(
            ['name' => 'vivo iQOO Neo 10'],
            [
                'brand' => 'iQOO',
                'model_variant' => null,
                'price' => 36998.00,
                'overall_score' => 59,
                'release_date' => '2025-06-02',
                'image_url' => '/assets/iqoo-neo-10_nobg.png',
                'amazon_url' => 'https://www.amazon.in/iQOO-Snapdragon-Processor-SuperComputing-Smartphone/dp/B0F83LL1D2',
                'flipkart_url' => 'https://www.flipkart.com/iqoo-neo-10-inferno-red-256-gb/p/itm4faf25d0485ec',
                'amazon_price' => 36998.00,
                'flipkart_price' => 36998.00,
                'ueps_score' => 142,
            ]
        );

        $neo10->body()->updateOrCreate([], [
            'dimensions' => '163.7 x 75.9 x 8.1 mm',
            'weight' => '206 g',
            'build_material' => 'Glass front (Shield Glass), plastic back, plastic frame',
            'sim' => 'Nano-SIM + Nano-SIM',
            'ip_rating' => 'IP65',
            'colors' => 'Inferno Red, Titanium Chrome, Blaze Orange, Onyx Black',
            'display_type' => 'AMOLED, 1B colors, 144Hz, HDR, 2000 nits (HBM), 4400 nits (peak)',
            'display_size' => '6.78 inches',
            'display_resolution' => '1260 x 2800 pixels',
            'display_protection' => 'Shield Glass',
            'display_features' => '144Hz, 4320Hz PWM, 2000 nits (HBM), 4400 nits (peak)',
            'screen_to_body_ratio' => '~89.3%',
            'pixel_density' => '~453 ppi',
            'pwm_dimming' => '4320Hz PWM',
            'touch_sampling_rate' => '300Hz',
            'screen_glass' => 'Shield Glass',
        ]);

        $neo10->platform()->updateOrCreate([], [
            'os' => 'Android 15, Funtouch 15',
            'chipset' => 'Qualcomm SM8735 Snapdragon 8s Gen 4 (4 nm)',
            'cpu' => 'Octa-core (1x3.21 GHz Cortex-X4 & 3x3.0 GHz Cortex-A720 & 2x2.8 GHz Cortex-A720 & 2x2.0 GHz Cortex-A720)',
            'gpu' => 'Adreno 825',
            'memory_card_slot' => 'No',
            'internal_storage' => '128GB/256GB/512GB',
            'ram' => '8GB/12GB/16GB',
            'storage_type' => 'UFS 4.1',
            'bootloader_unlockable' => false,
            'turnip_support' => true,
            'aosp_aesthetics_score' => 8,
            'turnip_support_level' => 'Full Support',
            'os_openness' => 'Restricted (Funtouch)',
            'gpu_emulation_tier' => 'High',
            'custom_rom_support' => 'None',
        ]);

        $neo10->camera()->updateOrCreate([], [
            'main_camera_specs' => '50 MP, f/1.8, (wide), OIS + 8 MP, f/2.2, (ultrawide)',
            'main_camera_features' => 'LED flash, HDR, panorama',
            'main_video_capabilities' => '4K@30/60fps, 1080p, gyro-EIS, OIS',
            'main_camera_sensors' => 'Main: 1/1.95"',
            'main_camera_apertures' => 'f/1.8 (main), f/2.2 (ultrawide)',
            'main_camera_ois' => 'Yes (Main)',
            'selfie_camera_specs' => '32 MP, f/2.5, (wide)',
            'selfie_camera_features' => 'HDR',
            'selfie_video_capabilities' => '4K@30/60fps, 1080p, gyro-EIS',
        ]);

        $neo10->connectivity()->updateOrCreate([], [
            'wlan' => 'Wi-Fi 802.11 a/b/g/n/ac/6/7, dual-band',
            'bluetooth' => '5.4, A2DP, LE',
            'positioning' => 'GPS (L1+L5), GLONASS, GALILEO, BDS, QZSS, NavIC',
            'nfc' => 'Yes',
            'infrared' => 'Yes',
            'radio' => 'No',
            'usb' => 'USB Type-C 2.0, OTG',
            'sensors' => 'Fingerprint (under display, optical), accelerometer, gyro, proximity, compass',
            'loudspeaker' => 'Yes, with stereo speakers',
            'has_3_5mm_jack' => false,
        ]);

        $neo10->battery()->updateOrCreate([], [
            'battery_type' => 'Si/C Li-Ion 7000 mAh',
            'charging_wired' => '120W',
            'charging_reverse' => 'Reverse wired',
            'charging_specs_detailed' => '120W wired, 100W PD, 100W PPS, 50% in 15 min, 100% in 36 min',
        ]);

        $neo10->benchmarks()->updateOrCreate([], [
            'antutu_score' => 2369435,
            'geekbench_single' => 2073,
            'geekbench_multi' => 6649,
            'dmark_wild_life_extreme' => 3876,
        ]);
    }
}
