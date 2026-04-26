<?php

namespace App\Repositories;

use App\Models\User;

class UserRepository extends FirestoreRepository
{
    protected string $collection = 'users';

    protected function makeModel(array $data): object
    {
        return new User($data);
    }

    protected function modelClass(): string
    {
        return User::class;
    }

    public function findByEmail(string $email): ?User
    {
        $result = $this->where('email', '==', $email)->first();

        return $result instanceof User ? $result : null;
    }

    public function findByFirebaseUid(string $uid): ?User
    {
        $result = $this->where('firebase_uid', '==', $uid)->first();

        return $result instanceof User ? $result : null;
    }

    public function findByUsername(string $username): ?User
    {
        $all = $this->all();
        foreach ($all as $user) {
            if (($user->name ?? '') === $username) {
                return $user;
            }
        }

        return null;
    }
}
