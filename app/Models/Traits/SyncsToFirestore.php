<?php

namespace App\Models\Traits;

use App\Services\Firestore\FirestoreSyncService;

/**
 * Trait to automatically sync Eloquent model changes to Firestore.
 * Add `use SyncsToFirestore;` to any model you want to sync.
 */
trait SyncsToFirestore
{
    public static function bootSyncsToFirestore(): void
    {
        static::created(function ($model) {
            $model->syncToFirestore();
        });

        static::updated(function ($model) {
            $model->syncToFirestore();
        });

        static::deleted(function ($model) {
            try {
                $syncService = app(FirestoreSyncService::class);
                $collection = FirestoreSyncService::collectionFor(get_class($model));
                $syncService->delete($collection, (string) $model->getKey());
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Firestore sync delete failed: '.$e->getMessage());
            }
        });
    }

    /**
     * Sync current model state to Firestore.
     */
    public function syncToFirestore(): void
    {
        try {
            $syncService = app(FirestoreSyncService::class);
            $collection = FirestoreSyncService::collectionFor(get_class($this));

            $data = $this->getAttributes();

            // Handle JSON casts
            foreach ($this->getCasts() as $key => $cast) {
                if (in_array($cast, ['array', 'json', 'object', 'collection'])) {
                    $data[$key] = $this->getAttributeValue($key);
                }
            }

            // Remove null password hashes from sync for security
            if (isset($data['password'])) {
                unset($data['password']);
            }

            // Convert dates to ISO strings for Firestore
            foreach ($data as $key => $value) {
                if ($value instanceof \DateTimeInterface) {
                    $data[$key] = $value->format('c');
                }
            }

            $syncService->sync($collection, (string) $this->getKey(), $data);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Firestore sync failed: '.$e->getMessage());
        }
    }
}
