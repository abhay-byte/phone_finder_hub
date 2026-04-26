<?php

namespace App\Models;

use App\Repositories\ForumPostRepository;
use Illuminate\Support\Collection;

class ForumCategory extends FirestoreModel
{
    protected array $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function posts(): Collection
    {
        return collect(app(ForumPostRepository::class)->forCategory($this->attributes['id'] ?? ''));
    }
}
