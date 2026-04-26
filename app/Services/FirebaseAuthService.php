<?php

namespace App\Services;

use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Contract\Auth as FirebaseAuth;
use Kreait\Firebase\Exception\Auth\FailedToVerifyToken;
use Kreait\Laravel\Firebase\Facades\Firebase;

class FirebaseAuthService
{
    protected ?FirebaseAuth $auth = null;

    protected UserRepository $users;

    public function __construct(UserRepository $users)
    {
        $this->users = $users;
    }

    protected function getAuth(): FirebaseAuth
    {
        if ($this->auth === null) {
            $this->auth = Firebase::auth();
        }

        return $this->auth;
    }

    public function verifyIdToken(string $idToken): ?object
    {
        try {
            $verifiedIdToken = $this->getAuth()->verifyIdToken($idToken);

            return $verifiedIdToken;
        } catch (FailedToVerifyToken $e) {
            Log::error('Firebase token verification failed: '.$e->getMessage());

            return null;
        } catch (\Exception $e) {
            Log::error('Firebase auth error: '.$e->getMessage());

            return null;
        }
    }

    public function getUser(string $uid): ?object
    {
        try {
            return $this->getAuth()->getUser($uid);
        } catch (\Exception $e) {
            Log::error('Failed to get Firebase user: '.$e->getMessage());

            return null;
        }
    }

    public function syncUser(object $firebaseUser): \App\Models\User
    {
        $user = $this->users->findByFirebaseUid($firebaseUser->uid);

        if (! $user) {
            $user = $this->users->findByEmail($firebaseUser->email);
        }

        if ($user) {
            $this->users->update($user->id, [
                'firebase_uid' => $firebaseUser->uid,
                'name' => $firebaseUser->displayName ?? $user->name,
                'email_verified_at' => $firebaseUser->emailVerified ? now()->format('c') : $user->email_verified_at,
                'photo_url' => $firebaseUser->photoUrl ?? $user->photo_url,
            ]);
            $user = $this->users->find($user->id);
        } else {
            $user = $this->users->create([
                'firebase_uid' => $firebaseUser->uid,
                'name' => $firebaseUser->displayName ?? explode('@', $firebaseUser->email)[0],
                'email' => $firebaseUser->email,
                'username' => $this->generateUniqueUsername($firebaseUser->email),
                'email_verified_at' => $firebaseUser->emailVerified ? now()->format('c') : null,
                'photo_url' => $firebaseUser->photoUrl ?? null,
                'role' => 'user',
                'password' => bcrypt(uniqid()),
                'created_at' => now()->format('c'),
            ]);
        }

        return $user;
    }

    public function createCustomToken(string $uid, array $claims = []): ?string
    {
        try {
            $customToken = $this->getAuth()->createCustomToken($uid, $claims);

            return $customToken->toString();
        } catch (\Exception $e) {
            Log::error('Failed to create custom token: '.$e->getMessage());

            return null;
        }
    }

    public function revokeRefreshTokens(string $uid): bool
    {
        try {
            $this->getAuth()->revokeRefreshTokens($uid);

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to revoke refresh tokens: '.$e->getMessage());

            return false;
        }
    }

    protected function generateUniqueUsername(string $email): string
    {
        $base = explode('@', $email)[0];
        $username = $base;
        $counter = 1;

        while ($this->users->findByUsername($username)) {
            $username = $base.$counter;
            $counter++;
        }

        return $username;
    }
}
