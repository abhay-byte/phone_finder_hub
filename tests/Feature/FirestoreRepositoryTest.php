<?php

namespace Tests\Feature;

use App\Repositories\PhoneRepository;
use App\Repositories\UserRepository;
use Tests\TestCase;

class FirestoreRepositoryTest extends TestCase
{
    protected PhoneRepository $phones;

    protected UserRepository $users;

    protected function setUp(): void
    {
        parent::setUp();
        $this->phones = app(PhoneRepository::class);
        $this->users = app(UserRepository::class);
    }

    public function test_phone_repository_can_list_and_find(): void
    {
        $all = $this->phones->all();
        $this->assertIsArray($all);

        if (count($all) > 0) {
            $phone = $all[0];
            $found = $this->phones->find($phone->id);
            $this->assertNotNull($found);
            $this->assertEquals($phone->id, $found->id);
        }
    }

    public function test_phone_repository_search(): void
    {
        $results = $this->phones->search('test');
        $this->assertIsArray($results);
    }

    public function test_user_repository_can_create_and_find(): void
    {
        $email = 'test_'.uniqid().'@example.com';
        $user = $this->users->create([
            'name' => 'Test User',
            'email' => $email,
            'username' => 'test_'.uniqid(),
            'password' => bcrypt('password'),
            'role' => 'user',
            'created_at' => now()->format('c'),
        ]);

        $this->assertNotNull($user);
        $this->assertEquals('Test User', $user->name);

        $found = $this->users->findByEmail($email);
        $this->assertNotNull($found);
        $this->assertEquals($user->id, $found->id);

        // Cleanup
        $this->users->delete($user->id);
    }

    public function test_user_repository_update(): void
    {
        $email = 'test_update_'.uniqid().'@example.com';
        $user = $this->users->create([
            'name' => 'Before',
            'email' => $email,
            'username' => 'test_update_'.uniqid(),
            'password' => bcrypt('password'),
            'role' => 'user',
            'created_at' => now()->format('c'),
        ]);

        $this->users->update($user->id, ['name' => 'After']);
        $updated = $this->users->find($user->id);
        $this->assertEquals('After', $updated->name);

        // Cleanup
        $this->users->delete($user->id);
    }
}
