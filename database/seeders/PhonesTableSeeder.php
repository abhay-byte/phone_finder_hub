<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PhonesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $phone = \App\Models\Phone::create([
            'name' => 'Poco F7',
            'brand' => 'Xiaomi',
            'model_variant' => '25053PC47I (Indian)',
            'price' => 27999.00,
            'overall_score' => 92,
            'release_date' => '2025-06-01', // Approx based on June 2025
            'image_url' => null,
        ]);

        $phone->body()->create([
            'dimensions' => '162.9 x 76.5 x 8.5 mm',
            'weight' => '215.7g',
            'build_material' => 'Glass front (Ceramic Guard), glass/eco leather back, aluminum frame',
            'sim' => 'Nano-SIM + Nano-SIM + eSIM',
            'ip_rating' => 'IP68',
            'colors' => 'Black Eclipse, Arctic Dawn, Midnight Ocean',
            'display_type' => 'LTPO 4.1 AMOLED, 1B colors, 120Hz, Dolby Vision, HDR10+',
            'display_size' => '6.83 inches',
            'display_resolution' => '1280x2772',
            'display_protection' => 'Ceramic Guard glass',
            'display_features' => '3200 nits peak brightness, 2160Hz PWM dimming',
        ]);

        $phone->platform()->create([
            'os' => 'Android 15, HyperOS 2',
            'chipset' => 'Snapdragon 8s Gen 4 (4nm)',
            'cpu' => 'Octa-core (1x3.21 GHz Cortex-X4 + 3x3.0 GHz Cortex-A720...)',
            'gpu' => 'Adreno 825',
            'memory_card_slot' => 'No',
            'internal_storage' => '256GB',
            'ram' => '12GB',
            'storage_type' => 'UFS 4.1',
        ]);

        $phone->camera()->create([
            'main_camera_specs' => '50MP f/1.5 OIS + 8MP ultrawide',
            'main_camera_features' => 'Laser focus, Hasselblad Color Calibration, Dual-LED flash, HDR, panorama',
            'main_video_capabilities' => '8K@30fps, 4K@30/60fps, 1080p@30/60/240/480fps, Dolby Vision',
            'selfie_camera_specs' => '20MP',
            'selfie_camera_features' => 'HDR, panorama',
            'selfie_video_capabilities' => '4K@30/60fps, 1080p@30/60fps',
        ]);

        $phone->connectivity()->create([
            'wlan' => 'Wi-Fi 7, dual/tri-band, Wi-Fi Direct',
            'bluetooth' => '6.0, A2DP, LE, aptX HD, LHDC 5',
            'positioning' => 'GPS (L1+L5), GLONASS, BDS, GALILEO, QZSS, NavIC',
            'nfc' => 'Yes',
            'infrared' => 'Yes',
            'radio' => 'No',
            'usb' => 'USB Type-C 2.0, OTG',
            'sensors' => 'Fingerprint (under display), accelerometer, gyro, proximity, compass, barometer',
            'loudspeaker' => 'Yes, with stereo speakers',
            'jack_3_5mm' => 'No',
        ]);

        $phone->battery()->create([
            'battery_type' => 'Si/C Li-Ion 7550 mAh',
            'charging_wired' => '90W wired',
            'charging_wireless' => 'No',
            'charging_reverse' => 'Yes',
        ]);

        $phone->benchmarks()->create([
            'antutu_score' => 2024751,
            'geekbench_single' => 0, // Not explicitly in summary but usually high
            'geekbench_multi' => 6402,
            'dmark_wild_life_extreme' => 4476,
            'battery_endurance_hours' => 64.03,
        ]);
    }
}
