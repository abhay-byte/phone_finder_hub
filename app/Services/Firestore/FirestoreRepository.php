<?php

namespace App\Services\Firestore;

/**
 * Base Firestore Repository
 * Provides CRUD operations via Firestore REST API
 */
abstract class FirestoreRepository
{
    protected FirestoreClient $client;

    protected string $collection;

    public function __construct(FirestoreClient $client)
    {
        $this->client = $client;
        $this->collection = $this->getCollection();
    }

    abstract protected function getCollection(): string;

    /**
     * Find a document by ID
     */
    public function find(string $id): ?array
    {
        return $this->client->getDocument($this->collection, $id);
    }

    /**
     * Create or update a document
     */
    public function save(string $id, array $data, bool $merge = true): bool
    {
        return $this->client->setDocument($this->collection, $id, $data, $merge);
    }

    /**
     * Delete a document
     */
    public function delete(string $id): bool
    {
        return $this->client->deleteDocument($this->collection, $id);
    }

    /**
     * Query documents with filters
     */
    public function query(array $filters = [], array $orderBy = [], int $limit = 0): array
    {
        return $this->client->query($this->collection, $filters, $orderBy, $limit);
    }

    /**
     * List all documents
     */
    public function all(): array
    {
        return $this->client->listDocuments($this->collection);
    }

    /**
     * Find documents where field equals value
     */
    public function where(string $field, mixed $value, string $op = 'EQUAL'): array
    {
        return $this->query([$field => ['op' => $op, 'value' => $value]]);
    }

    /**
     * Find one document where field equals value
     */
    public function firstWhere(string $field, mixed $value): ?array
    {
        $results = $this->where($field, $value);

        return $results[0] ?? null;
    }
}
