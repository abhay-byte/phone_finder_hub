<?php

namespace App\Repositories;

use App\Models\ForumCategory;

class ForumCategoryRepository extends FirestoreRepository
{
    protected string $collection = 'forum_categories';

    protected function makeModel(array $data): object
    {
        return new ForumCategory($data);
    }

    protected function modelClass(): string
    {
        return ForumCategory::class;
    }

    public function ordered(): array
    {
        return $this->orderBy('order', 'asc')->get();
    }
}
