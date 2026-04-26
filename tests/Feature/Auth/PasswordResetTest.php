<?php

namespace Tests\Feature\Auth;

use App\Repositories\UserRepository;
use App\Services\Firestore\FirestoreClient;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    protected UserRepository $users;

    protected FirestoreClient $firestore;

    protected array $createdUserIds = [];

    protected array $createdTokens = [];

    protected function setUp(): void
    {
        parent::setUp();
        $this->users = app(UserRepository::class);
        $this->firestore = app(FirestoreClient::class);
    }

    protected function tearDown(): void
    {
        foreach ($this->createdUserIds as $id) {
            $this->users->delete($id);
        }
        foreach ($this->createdTokens as $token) {
            $this->firestore->deleteDocument('password_reset_tokens', $token);
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

    public function test_reset_password_link_screen_can_be_rendered(): void
    {
        $response = $this->get('/forgot-password');

        $response->assertStatus(200);
    }

    public function test_reset_password_link_can_be_requested(): void
    {
        Mail::fake();

        $user = $this->createUser();

        $response = $this->post('/forgot-password', ['email' => $user->email]);

        $response->assertSessionHasNoErrors();

        $tokens = $this->firestore->listDocuments('password_reset_tokens');
        $found = false;
        foreach ($tokens as $token) {
            if (($token['email'] ?? '') === $user->email) {
                $found = true;
                $this->createdTokens[] = $token['id'];
            }
        }
        $this->assertTrue($found, 'Password reset token was not created in Firestore');
    }

    public function test_reset_password_screen_can_be_rendered(): void
    {
        $user = $this->createUser();
        $token = 'test_token_'.uniqid();

        $this->firestore->setDocument('password_reset_tokens', $token, [
            'email' => $user->email,
            'token' => Hash::make($token),
            'created_at' => now()->format('c'),
        ]);
        $this->createdTokens[] = $token;

        $response = $this->get('/reset-password/'.$token.'?email='.$user->email);

        $response->assertStatus(200);
    }

    public function test_password_can_be_reset_with_valid_token(): void
    {
        $user = $this->createUser();
        $token = 'test_token_'.uniqid();

        $this->firestore->setDocument('password_reset_tokens', $token, [
            'email' => $user->email,
            'token' => Hash::make($token),
            'created_at' => now()->format('c'),
        ]);
        $this->createdTokens[] = $token;

        $response = $this->post('/reset-password', [
            'token' => $token,
            'email' => $user->email,
            'password' => 'NewSecurePass123!',
            'password_confirmation' => 'NewSecurePass123!',
        ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('login'));

        $updated = $this->users->find($user->id);
        $this->assertTrue(Hash::check('NewSecurePass123!', $updated->password));
    }

    public function test_password_can_not_be_reset_with_invalid_token(): void
    {
        $user = $this->createUser();

        $response = $this->post('/reset-password', [
            'token' => 'invalid-token',
            'email' => $user->email,
            'password' => 'NewSecurePass123!',
            'password_confirmation' => 'NewSecurePass123!',
        ]);

        $response->assertSessionHasErrors('email');
    }
}
