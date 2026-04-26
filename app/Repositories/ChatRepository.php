<?php

namespace App\Repositories;

use App\Models\Chat;

class ChatRepository extends FirestoreRepository
{
    protected string $collection = 'chats';

    protected function makeModel(array $data): object
    {
        return new Chat($data);
    }

    protected function modelClass(): string
    {
        return Chat::class;
    }

    public function forUser(string $userId): array
    {
        return $this->where('user_id', '==', $userId)
            ->orderBy('created_at', 'desc')
            ->get();
    }
}
