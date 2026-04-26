<?php

namespace Tests\Feature\Auth;

use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class RegistrationTest extends TestCase
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

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_new_users_can_register(): void
    {
        $email = 'test_reg_'.uniqid().'@example.com';
        $username = 'test_reg_'.uniqid();

        $response = $this->post('/register', [
            'name' => 'Test User',
            'username' => $username,
            'email' => $email,
            'password' => 'SecurePass123!',
            'password_confirmation' => 'SecurePass123!',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('home', absolute: false));

        $user = $this->users->findByEmail($email);
        $this->assertNotNull($user);
        $this->createdUserIds[] = $user->id;
    }

    public function test_registration_fails_with_weak_password(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'username' => 'test_'.uniqid(),
            'email' => 'test_'.uniqid().'@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertSessionHasErrors('password');
        $this->assertGuest();
    }

    public function test_registration_fails_with_duplicate_email(): void
    {
        $email = 'test_dup_'.uniqid().'@example.com';
        $user = $this->users->create([
            'name' => 'Existing User',
            'email' => $email,
            'username' => 'test_dup_'.uniqid(),
            'password' => Hash::make('SecurePass123!'),
            'role' => 'user',
            'created_at' => now()->format('c'),
        ]);
        $this->createdUserIds[] = $user->id;

        $response = $this->post('/register', [
            'name' => 'Test User',
            'username' => 'test_dup2_'.uniqid(),
            'email' => $email,
            'password' => 'SecurePass123!',
            'password_confirmation' => 'SecurePass123!',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }
}
