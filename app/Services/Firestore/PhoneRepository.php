<?php

namespace App\Services\Firestore;

class PhoneRepository extends FirestoreRepository
{
    protected function getCollection(): string
    {
        return 'phones';
    }

    public function search(string $query): array
    {
        $all = $this->all();
        $query = strtolower($query);

        return array_filter($all, fn ($phone) => str_contains(strtolower($phone['name'] ?? ''), $query) ||
            str_contains(strtolower($phone['brand'] ?? ''), $query)
        );
    }

    public function findBySlug(string $slug): ?array
    {
        return $this->firstWhere('slug', $slug);
    }

    public function findByName(string $name): ?array
    {
        return $this->firstWhere('name', $name);
    }
}
