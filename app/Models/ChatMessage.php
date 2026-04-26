<?php

namespace App\Models;

use App\Repositories\ChatRepository;

class ChatMessage extends FirestoreModel
{
    public function chat(): ?Chat
    {
        return app(ChatRepository::class)->find($this->attributes['chat_id'] ?? '');
    }
}
