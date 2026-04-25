<?php

namespace App\Services\Firestore;

class BlogRepository extends FirestoreRepository
{
    protected function getCollection(): string
    {
        return 'blogs';
    }

    public function findBySlug(string $slug): ?array
    {
        return $this->firstWhere('slug', $slug);
    }

    public function findPublished(): array
    {
        return $this->where('is_published', true);
    }
}
