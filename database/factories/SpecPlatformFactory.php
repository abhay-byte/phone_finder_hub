<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SpecPlatform>
 */
class SpecPlatformFactory extends Factory
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
            'os' => 'Android 15',
            'chipset' => 'Snapdragon 8 Elite',
            'cpu' => 'Octa-core',
            'gpu' => 'Adreno 830',
            'memory_card_slot' => 'No',
            'internal_storage' => '256GB',
            'ram' => '12GB',
            'storage_type' => 'UFS 4.0',
        ];
    }
}
