<?php

namespace App\Models;

use App\Repositories\ForumCategoryRepository;
use App\Repositories\ForumCommentRepository;
use App\Repositories\UserRepository;
use Illuminate\Support\Collection;

class ForumPost extends FirestoreModel
{
    protected array $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function category(): ?ForumCategory
    {
        return app(ForumCategoryRepository::class)->find($this->attributes['forum_category_id'] ?? '');
    }

    public function user(): ?User
    {
        return app(UserRepository::class)->find($this->attributes['user_id'] ?? '');
    }

    public function comments(): Collection
    {
        return collect(app(ForumCommentRepository::class)->forPost($this->attributes['id'] ?? ''));
    }
}
