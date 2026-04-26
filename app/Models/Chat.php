<?php

namespace App\Models;

use App\Repositories\ChatMessageRepository;
use App\Repositories\UserRepository;
use Illuminate\Support\Collection;

class Chat extends FirestoreModel
{
    public function user(): ?User
    {
        return app(UserRepository::class)->find($this->attributes['user_id'] ?? '');
    }

    public function messages(): Collection
    {
        return collect(app(ChatMessageRepository::class)->forChat($this->attributes['id'] ?? ''));
    }
}
