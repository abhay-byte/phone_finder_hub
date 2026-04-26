<?php

namespace Tests\Feature\Auth;

use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PasswordConfirmationTest extends TestCase
{
    protected UserRepository $users;

    protected array $createdUserIds = [];

    protected function setUp(): void
    {
        parent::setUp();
        $this->users = app(UserRepository::class);
    }

    protected function tearDown(): void
    {
        foreach ($this->createdUserIds as $id) {
            $this->users->delete($id);
        }
        parent::tearDown();
    }

    protected function createUser(array $overrides = []): object
    {
        $user = $this->users->create(array_merge([
            'name' => 'Test User',
            'email' => 'test_'.uniqid().'@example.com',
            'username' => 'test_'.uniqid(),
            'password' => Hash::make('SecurePass123!'),
            'role' => 'user',
            'created_at' => now()->format('c'),
        ], $overrides));

        $this->createdUserIds[] = $user->id;

        return $user;
    }

    public function test_confirm_password_screen_can_be_rendered(): void
    {
        $user = $this->createUser();

        $response = $this->actingAs($user)->get('/confirm-password');

        $response->assertStatus(200);
    }

    public function test_password_can_be_confirmed(): void
    {
        $user = $this->createUser();

        $response = $this->actingAs($user)->post('/confirm-password', [
            'password' => 'SecurePass123!',
        ]);

        $response->assertRedirect();
        $response->assertSessionHasNoErrors();
    }

    public function test_password_is_not_confirmed_with_invalid_password(): void
    {
        $user = $this->createUser();

        $response = $this->actingAs($user)->post('/confirm-password', [
            'password' => 'wrong-password',
        ]);

        $response->assertSessionHasErrors();
    }
}
