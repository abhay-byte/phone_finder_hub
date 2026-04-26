<?php

namespace App\Models;

use App\Repositories\CommentRepository;
use App\Repositories\UserRepository;

class CommentUpvote extends FirestoreModel
{
    public function comment(): ?Comment
    {
        return app(CommentRepository::class)->find($this->attributes['comment_id'] ?? '');
    }

    public function user(): ?User
    {
        return app(UserRepository::class)->find($this->attributes['user_id'] ?? '');
    }
}
