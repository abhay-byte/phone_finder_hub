<?php

namespace App\Auth;

use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Support\Facades\Hash;

class FirestoreUserProvider implements UserProvider
{
    protected UserRepository $users;

    public function __construct(UserRepository $users)
    {
        $this->users = $users;
    }

    public function retrieveById($identifier): ?User
    {
        return $this->users->find((string) $identifier);
    }

    public function retrieveByToken($identifier, $token): ?User
    {
        $user = $this->users->find((string) $identifier);
        if ($user && $user->getRememberToken() === $token) {
            return $user;
        }

        return null;
    }

    public function updateRememberToken(Authenticatable $user, $token): void
    {
        if ($user instanceof User) {
            $user->setRememberToken($token);
            $this->users->update($user->getAuthIdentifier(), ['remember_token' => $token]);
        }
    }

    public function retrieveByCredentials(array $credentials): ?User
    {
        if (isset($credentials['email'])) {
            return $this->users->findByEmail($credentials['email']);
        }

        if (isset($credentials['username'])) {
            return $this->users->findByUsername($credentials['username']);
        }

        return null;
    }

    public function validateCredentials(Authenticatable $user, array $credentials): bool
    {
        if (! $user instanceof User) {
            return false;
        }

        $plain = $credentials['password'] ?? '';
        $hashed = $user->getAuthPassword();

        if (empty($hashed)) {
            return false;
        }

        return password_verify($plain, $hashed);
    }

    public function rehashPasswordIfRequired(Authenticatable $user, array $credentials, bool $force = false): void
    {
        if (! $user instanceof User) {
            return;
        }

        if (! Hash::needsRehash($user->getAuthPassword()) && ! $force) {
            return;
        }

        $this->users->update($user->getAuthIdentifier(), [
            'password' => Hash::make($credentials['password']),
        ]);
    }
}
