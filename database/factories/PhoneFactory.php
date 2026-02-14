<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Phone>
 */
class PhoneFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->words(3, true),
            'brand' => $this->faker->company(),
            'model_variant' => $this->faker->bothify('##??'),
            'price' => $this->faker->randomFloat(2, 10000, 100000),
            'overall_score' => $this->faker->numberBetween(50, 100),
            'release_date' => $this->faker->date(),
            'image_url' => $this->faker->imageUrl(),
        ];
    }
}
