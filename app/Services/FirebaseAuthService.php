<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Contract\Auth as FirebaseAuth;
use Kreait\Firebase\Exception\Auth\FailedToVerifyToken;
use Kreait\Laravel\Firebase\Facades\Firebase;

class FirebaseAuthService
{
    protected ?FirebaseAuth $auth = null;

    protected function getAuth(): FirebaseAuth
    {
        if ($this->auth === null) {
            $this->auth = Firebase::auth();
        }

        return $this->auth;
    }

    /**
     * Verify a Firebase ID token
     */
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

    /**
     * Get user by Firebase UID
     */
    public function getUser(string $uid): ?object
    {
        try {
            return $this->getAuth()->getUser($uid);
        } catch (\Exception $e) {
            Log::error('Failed to get Firebase user: '.$e->getMessage());

            return null;
        }
    }

    /**
     * Create or update local user from Firebase user data
     */
    public function syncUser(object $firebaseUser): User
    {
        $user = User::where('firebase_uid', $firebaseUser->uid)->first();

        if (! $user) {
            $user = User::where('email', $firebaseUser->email)->first();
        }

        if ($user) {
            $user->update([
                'firebase_uid' => $firebaseUser->uid,
                'name' => $firebaseUser->displayName ?? $user->name,
                'email_verified_at' => $firebaseUser->emailVerified ? now() : $user->email_verified_at,
                'photo_url' => $firebaseUser->photoUrl ?? $user->photo_url,
            ]);
        } else {
            $user = User::create([
                'firebase_uid' => $firebaseUser->uid,
                'name' => $firebaseUser->displayName ?? explode('@', $firebaseUser->email)[0],
                'email' => $firebaseUser->email,
                'username' => $this->generateUniqueUsername($firebaseUser->email),
                'email_verified_at' => $firebaseUser->emailVerified ? now() : null,
                'photo_url' => $firebaseUser->photoUrl ?? null,
                'role' => 'user',
                'password' => bcrypt(uniqid()),
            ]);
        }

        return $user;
    }

    /**
     * Create a custom token for a user
     */
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

    /**
     * Revoke refresh tokens for a user
     */
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

    /**
     * Generate a unique username from email
     */
    protected function generateUniqueUsername(string $email): string
    {
        $base = explode('@', $email)[0];
        $username = $base;
        $counter = 1;

        while (User::where('username', $username)->exists()) {
            $username = $base.$counter;
            $counter++;
        }

        return $username;
    }
}
