<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SpecCamera>
 */
class SpecCameraFactory extends Factory
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
            'main_camera_specs' => '50 MP, f/1.6, 23mm (wide)',
            'main_camera_features' => 'Hasselblad Color Calibration, Dual-LED flash, HDR, panorama',
            'main_video_capabilities' => '8K@24fps, 4K@30/60fps',
            'selfie_camera_specs' => '32 MP, f/2.4, 21mm (wide)',
            'selfie_camera_features' => 'HDR, panorama',
            'selfie_video_capabilities' => '4K@30fps, 1080p@30fps',
        ];
    }
}
