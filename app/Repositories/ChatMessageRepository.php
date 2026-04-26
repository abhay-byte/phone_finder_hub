<?php

namespace App\Repositories;

use App\Models\ChatMessage;

class ChatMessageRepository extends FirestoreRepository
{
    protected string $collection = 'chat_messages';

    protected function makeModel(array $data): object
    {
        return new ChatMessage($data);
    }

    protected function modelClass(): string
    {
        return ChatMessage::class;
    }

    public function forChat(string $chatId): array
    {
        return $this->where('chat_id', '==', $chatId)
            ->orderBy('created_at', 'asc')
            ->get();
    }
}
