<?php

namespace Tests\Feature;

use App\Repositories\PhoneRepository;
use Tests\TestCase;

class PhoneApiTest extends TestCase
{
    protected PhoneRepository $phones;

    protected function setUp(): void
    {
        parent::setUp();
        $this->phones = app(PhoneRepository::class);
    }

    public function test_can_list_phones(): void
    {
        $response = $this->getJson('/api/phones');

        $response->assertStatus(200)
            ->assertJsonIsArray();
    }

    public function test_can_show_phone(): void
    {
        $all = $this->phones->all();
        if (empty($all)) {
            $this->markTestSkipped('No phones in Firestore to test with');
        }

        $phone = $all[0];
        $response = $this->getJson('/api/phones/'.$phone->id);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'id',
                'name',
                'brand',
            ]);
    }
}
