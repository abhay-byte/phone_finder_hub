<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SpecBody>
 */
class SpecBodyFactory extends Factory
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
            'dimensions' => '162.9 x 76.5 x 8.5 mm',
            'weight' => '210 g',
            'build_material' => 'Glass front, glass back, aluminum frame',
            'sim' => 'Dual SIM',
            'ip_rating' => 'IP68',
            'colors' => 'Black, Green, Silver',
            'display_type' => 'AMOLED',
            'display_size' => '6.82 inches',
            'display_resolution' => '1440 x 3168 pixels',
            'display_protection' => 'Gorilla Glass Victus 2',
            'display_features' => '120Hz, HDR10+, Dolby Vision',
        ];
    }
}
