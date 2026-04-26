<?php

namespace Tests\Feature;

use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ProfileTest extends TestCase
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
            'email_verified_at' => now()->format('c'),
            'created_at' => now()->format('c'),
        ], $overrides));

        $this->createdUserIds[] = $user->id;

        return $user;
    }

    public function test_profile_page_is_displayed(): void
    {
        $user = $this->createUser();

        $response = $this
            ->actingAs($user)
            ->get('/profile');

        $response->assertOk();
    }

    public function test_profile_information_can_be_updated(): void
    {
        $user = $this->createUser();

        $response = $this
            ->actingAs($user)
            ->patch('/profile', [
                'name' => 'Test User',
                'email' => 'test@example.com',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile');

        $updated = $this->users->find($user->id);

        $this->assertSame('Test User', $updated->name);
        $this->assertSame('test@example.com', $updated->email);
        $this->assertNull($updated->email_verified_at);
    }

    public function test_email_verification_status_is_unchanged_when_the_email_address_is_unchanged(): void
    {
        $user = $this->createUser();

        $response = $this
            ->actingAs($user)
            ->patch('/profile', [
                'name' => 'Test User',
                'email' => $user->email,
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile');

        $updated = $this->users->find($user->id);
        $this->assertNotNull($updated->email_verified_at);
    }

    public function test_user_can_delete_their_account(): void
    {
        $user = $this->createUser();

        $response = $this
            ->actingAs($user)
            ->delete('/profile', [
                'password' => 'SecurePass123!',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/');

        $this->assertGuest();
        $this->assertNull($this->users->find($user->id));
    }

    public function test_correct_password_must_be_provided_to_delete_account(): void
    {
        $user = $this->createUser();

        $response = $this
            ->actingAs($user)
            ->from('/profile')
            ->delete('/profile', [
                'password' => 'wrong-password',
            ]);

        $response
            ->assertSessionHasErrorsIn('userDeletion', 'password')
            ->assertRedirect('/profile');

        $this->assertNotNull($this->users->find($user->id));
    }
}
