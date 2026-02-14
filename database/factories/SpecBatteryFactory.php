<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SpecBattery>
 */
class SpecBatteryFactory extends Factory
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
            'battery_type' => 'Li-Po 6000 mAh, non-removable',
            'charging_wired' => '100W',
            'charging_wireless' => '50W',
            'charging_reverse' => '10W',
        ];
    }
}
