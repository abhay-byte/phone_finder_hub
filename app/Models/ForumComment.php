<?php

namespace App\Models;

use App\Repositories\ForumPostRepository;
use App\Repositories\UserRepository;

class ForumComment extends FirestoreModel
{
    protected array $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function post(): ?ForumPost
    {
        return app(ForumPostRepository::class)->find($this->attributes['forum_post_id'] ?? '');
    }

    public function user(): ?User
    {
        return app(UserRepository::class)->find($this->attributes['user_id'] ?? '');
    }
}
