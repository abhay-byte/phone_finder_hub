<?php

namespace Tests\Feature\Auth;

use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PasswordUpdateTest extends TestCase
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

    public function test_password_can_be_updated(): void
    {
        $user = $this->createUser();

        $response = $this
            ->actingAs($user)
            ->from('/profile')
            ->put('/password', [
                'current_password' => 'SecurePass123!',
                'password' => 'NewSecurePass123!',
                'password_confirmation' => 'NewSecurePass123!',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile');

        $updated = $this->users->find($user->id);
        $this->assertTrue(Hash::check('NewSecurePass123!', $updated->password));
    }

    public function test_correct_password_must_be_provided_to_update_password(): void
    {
        $user = $this->createUser();

        $response = $this
            ->actingAs($user)
            ->from('/profile')
            ->put('/password', [
                'current_password' => 'wrong-password',
                'password' => 'NewSecurePass123!',
                'password_confirmation' => 'NewSecurePass123!',
            ]);

        $response
            ->assertSessionHasErrorsIn('updatePassword', 'current_password')
            ->assertRedirect('/profile');
    }
}
