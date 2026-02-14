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
            $phone = \App\Models\Phone::updateOrCreate(
                ['name' => 'OnePlus 13'],
                [
                    'brand' => 'OnePlus',
                    'model_variant' => 'CPH2655',
                    'price' => 60999.00,
                    'overall_score' => 95,
                    'release_date' => '2024-11-01',
                    'image_url' => 'https://raw.githubusercontent.com/abhay-byte/phone_finder_hub/master/public/assets/oneplus-13.jpg',
                ]
            );

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
                    'antutu_score' => 2690491, // v10
                    'geekbench_single' => 3000,
                    'geekbench_multi' => 9278, // v6
                    'dmark_wild_life_extreme' => 6615,
                    'battery_endurance_hours' => 15.28,
                ]);
            }
        } catch (\Exception $e) {
            echo "Seeder Error: " . $e->getMessage() . "\n";
            throw $e;
        }
    }
}
