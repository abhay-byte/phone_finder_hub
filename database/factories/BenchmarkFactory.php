<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Benchmark>
 */
class BenchmarkFactory extends Factory
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
            'antutu_score' => $this->faker->numberBetween(1000000, 3000000),
            'geekbench_single' => $this->faker->numberBetween(1000, 3000),
            'geekbench_multi' => $this->faker->numberBetween(3000, 10000),
            'dmark_wild_life_extreme' => $this->faker->numberBetween(3000, 8000),
            'battery_endurance_hours' => $this->faker->randomFloat(2, 40, 100),
        ];
    }
}
