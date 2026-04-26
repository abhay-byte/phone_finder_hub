<?php

namespace Tests\Feature\Auth;

use App\Repositories\UserRepository;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class EmailVerificationTest extends TestCase
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
            'email_verified_at' => null,
            'created_at' => now()->format('c'),
        ], $overrides));

        $this->createdUserIds[] = $user->id;

        return $user;
    }

    public function test_email_verification_screen_can_be_rendered(): void
    {
        $user = $this->createUser();

        $response = $this->actingAs($user)->get('/verify-email');

        $response->assertStatus(200);
    }

    public function test_email_can_be_verified(): void
    {
        $user = $this->createUser();

        Event::fake();

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );

        $response = $this->actingAs($user)->get($verificationUrl);

        Event::assertDispatched(Verified::class);
        $this->assertTrue($user->fresh()->hasVerifiedEmail());
        $response->assertRedirect(route('dashboard', absolute: false).'?verified=1');
    }

    public function test_email_is_not_verified_with_invalid_hash(): void
    {
        $user = $this->createUser();

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1('wrong-email')]
        );

        $this->actingAs($user)->get($verificationUrl);

        $this->assertFalse($user->fresh()->hasVerifiedEmail());
    }
}
