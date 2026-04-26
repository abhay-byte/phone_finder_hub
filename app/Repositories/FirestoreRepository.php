<?php

namespace App\Repositories;

use App\Services\Firestore\FirestoreClient;
use Illuminate\Support\Collection;

abstract class FirestoreRepository
{
    protected FirestoreClient $client;

    protected string $collection;

    protected array $filters = [];

    protected array $orderBy = [];

    protected int $limit = 0;

    public function __construct()
    {
        $this->client = app(FirestoreClient::class);
    }

    /**
     * Reset query state.
     */
    protected function resetQuery(): void
    {
        $this->filters = [];
        $this->orderBy = [];
        $this->limit = 0;
    }

    /**
     * Get all documents in the collection.
     */
    public function all(): array
    {
        $docs = $this->client->listDocuments($this->collection);
        $this->resetQuery();

        return array_map(fn (array $doc) => $this->makeModel($doc), $docs);
    }

    /**
     * Find a document by ID.
     */
    public function find(string $id): ?object
    {
        $doc = $this->client->getDocument($this->collection, $id);

        return $doc ? $this->makeModel($doc) : null;
    }

    /**
     * Find or throw 404.
     */
    public function findOrFail(string $id): object
    {
        $model = $this->find($id);
        if (! $model) {
            abort(404, class_basename($this->modelClass()).' not found');
        }

        return $model;
    }

    /**
     * Add a filter (simplified - Firestore supports limited filtering).
     * For complex filters, fetch all and filter in PHP.
     */
    public function where(string $field, mixed $operator = '==', mixed $value = null): static
    {
        if ($value === null && func_num_args() === 2) {
            $value = $operator;
            $operator = '==';
        }

        $this->filters[] = ['field' => $field, 'op' => $this->mapOperator($operator), 'value' => $value];

        return $this;
    }

    /**
     * Order by a field.
     */
    public function orderBy(string $field, string $direction = 'asc'): static
    {
        $this->orderBy[$field] = $direction;

        return $this;
    }

    /**
     * Limit results.
     */
    public function limit(int $count): static
    {
        $this->limit = $count;

        return $this;
    }

    /**
     * Execute query with Firestore filters + PHP filtering for complex cases.
     */
    public function get(): array
    {
        // Firestore only supports simple equality/range filters
        $firestoreFilters = [];
        $phpFilters = [];

        foreach ($this->filters as $filter) {
            if (in_array($filter['op'], ['EQUAL', 'LESS_THAN', 'LESS_THAN_OR_EQUAL', 'GREATER_THAN', 'GREATER_THAN_OR_EQUAL'])) {
                $firestoreFilters[] = $filter;
            } else {
                $phpFilters[] = $filter;
            }
        }

        if (! empty($firestoreFilters) || ! empty($this->orderBy) || $this->limit > 0) {
            $docs = $this->client->query($this->collection, $firestoreFilters, $this->orderBy, $this->limit);
        } else {
            $docs = $this->client->listDocuments($this->collection);
        }

        $models = array_map(fn (array $doc) => $this->makeModel($doc), $docs);

        // Apply PHP-level filters for unsupported operations (like substring search)
        foreach ($phpFilters as $filter) {
            $models = array_filter($models, function ($model) use ($filter) {
                $value = $model->__get($filter['field']);

                return match ($filter['op']) {
                    'LIKE' => str_contains(strtolower((string) $value), strtolower((string) $filter['value'])),
                    'IN' => in_array($value, (array) $filter['value']),
                    default => $value == $filter['value'],
                };
            });
        }

        $this->resetQuery();

        return array_values($models);
    }

    /**
     * Get first result.
     */
    public function first(): ?object
    {
        $this->limit(1);
        $results = $this->get();

        return $results[0] ?? null;
    }

    /**
     * Create a new document.
     */
    public function create(array $data): object
    {
        $id = $data['id'] ?? uniqid();
        $this->client->setDocument($this->collection, (string) $id, $data);

        return $this->find((string) $id);
    }

    /**
     * Update a document.
     */
    public function update(string $id, array $data): bool
    {
        return $this->client->setDocument($this->collection, $id, $data, true);
    }

    /**
     * Delete a document.
     */
    public function delete(string $id): bool
    {
        return $this->client->deleteDocument($this->collection, $id);
    }

    /**
     * Count all documents (fetches all - use sparingly).
     */
    public function count(): int
    {
        return count($this->client->listDocuments($this->collection));
    }

    /**
     * Get distinct values for a field.
     */
    public function distinct(string $field): array
    {
        $docs = $this->client->listDocuments($this->collection);
        $values = [];
        foreach ($docs as $doc) {
            if (isset($doc[$field]) && $doc[$field] !== '' && $doc[$field] !== null) {
                $values[] = $doc[$field];
            }
        }

        return array_values(array_unique($values));
    }

    /**
     * Get max value for a field.
     */
    public function max(string $field): float
    {
        $docs = $this->client->listDocuments($this->collection);
        $max = 0;
        foreach ($docs as $doc) {
            $value = $doc[$field] ?? 0;
            if ($value > $max) {
                $max = $value;
            }
        }

        return (float) $max;
    }

    /**
     * Pluck a single field from all documents.
     */
    public function pluck(string $field): array
    {
        $docs = $this->client->listDocuments($this->collection);

        return array_column($docs, $field);
    }

    /**
     * Check if a document exists matching filters.
     */
    public function exists(): bool
    {
        return $this->first() !== null;
    }

    /**
     * Get documents by IDs.
     */
    public function findMany(array $ids): array
    {
        $models = [];
        foreach ($ids as $id) {
            $model = $this->find($id);
            if ($model) {
                $models[] = $model;
            }
        }

        return $models;
    }

    /**
     * Map PHP operators to Firestore operators.
     */
    protected function mapOperator(string $operator): string
    {
        return match (strtoupper($operator)) {
            '=', '==' => 'EQUAL',
            '!=' => 'NOT_EQUAL',
            '<' => 'LESS_THAN',
            '<=' => 'LESS_THAN_OR_EQUAL',
            '>' => 'GREATER_THAN',
            '>=' => 'GREATER_THAN_OR_EQUAL',
            'LIKE' => 'LIKE',
            'IN' => 'IN',
            default => 'EQUAL',
        };
    }

    /**
     * Convert raw Firestore document array to model instance.
     */
    abstract protected function makeModel(array $data): object;

    /**
     * Get the model class name.
     */
    abstract protected function modelClass(): string;
}
