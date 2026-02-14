<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PhoneApiTest extends TestCase
{
    use Illuminate\Foundation\Testing\RefreshDatabase;

    /**
     * A basic feature test example.
     */
    public function test_can_list_phones(): void
    {
        $this->seed(\Database\Seeders\PhonesTableSeeder::class);

        $response = $this->getJson('/api/phones');

        $response->assertStatus(200)
            ->assertJsonStructure([
                '*' => [
                    'id',
                    'name',
                    'brand',
                    'price',
                    'overall_score',
                    'image_url',
                ]
            ]);
    }

    public function test_can_show_phone(): void
    {
        $this->seed(\Database\Seeders\PhonesTableSeeder::class);
        $phone = \App\Models\Phone::first();

        $response = $this->getJson('/api/phones/' . $phone->id);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'id',
                'name',
                'brand',
                'specifications' => [
                    'processor',
                    'ram_capacity',
                ],
                'benchmarks' => [
                    'antutu_score',
                ]
            ]);
    }
}
