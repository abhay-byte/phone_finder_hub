<?php

namespace App\Services\Firestore;

class UserRepository extends FirestoreRepository
{
    protected function getCollection(): string
    {
        return 'users';
    }

    public function findByEmail(string $email): ?array
    {
        return $this->firstWhere('email', $email);
    }

    public function findByFirebaseUid(string $uid): ?array
    {
        return $this->firstWhere('firebase_uid', $uid);
    }

    public function findByUsername(string $username): ?array
    {
        return $this->firstWhere('username', $username);
    }
}
