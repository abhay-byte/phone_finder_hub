<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SpecConnectivity>
 */
class SpecConnectivityFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'phone_id' => \App\Models\Phone::factory(),
            'wlan' => 'Wi-Fi 802.11 a/b/g/n/ac/6/7',
            'bluetooth' => '5.4, A2DP, LE, aptX HD',
            'positioning' => 'GPS (L1+L5), GLONASS, BDS, GALILEO, QZSS',
            'nfc' => 'Yes',
            'infrared' => 'Yes',
            'radio' => 'No',
            'usb' => 'USB Type-C 3.2',
            'sensors' => 'Fingerprint (under display), accelerometer, gyro, proximity, compass, color spectrum',
            'loudspeaker' => 'Yes, with stereo speakers',
            'jack_3_5mm' => 'No',
        ];
    }
}
