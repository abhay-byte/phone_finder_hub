<?php

namespace App\Models;

use App\Repositories\CommentRepository;
use App\Repositories\CommentUpvoteRepository;
use App\Repositories\UserRepository;
use Illuminate\Support\Collection;

class Comment extends FirestoreModel
{
    protected array $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function phone(): ?Phone
    {
        return app(\App\Repositories\PhoneRepository::class)->find($this->attributes['phone_id'] ?? '');
    }

    public function user(): ?User
    {
        return app(UserRepository::class)->find($this->attributes['user_id'] ?? '');
    }

    public function parent(): ?Comment
    {
        if (empty($this->attributes['parent_id'])) {
            return null;
        }

        $result = app(CommentRepository::class)->find($this->attributes['parent_id']);

        return $result instanceof Comment ? $result : null;
    }

    public function replies(): Collection
    {
        return collect(app(CommentRepository::class)->repliesFor($this->attributes['id'] ?? ''));
    }

    public function upvotes(): Collection
    {
        return collect(app(CommentUpvoteRepository::class)->forComment($this->attributes['id'] ?? ''));
    }

    public function getAuthorNameAttribute(): string
    {
        if (! empty($this->attributes['user_id'])) {
            $user = $this->user();
            if ($user) {
                return $user->name;
            }
        }

        return 'Anonymous '.substr(md5('anon'.($this->attributes['id'] ?? '0')), 0, 6);
    }
}
