<?php

namespace App\Services\Firestore;

class CommentRepository extends FirestoreRepository
{
    protected function getCollection(): string
    {
        return 'comments';
    }

    public function findByPhoneId(string $phoneId): array
    {
        return $this->where('phone_id', $phoneId);
    }

    public function findByUserId(string $userId): array
    {
        return $this->where('user_id', $userId);
    }
}
