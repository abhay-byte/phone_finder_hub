<?php

namespace App\Services\Firestore;

use Illuminate\Support\Facades\Log;

/**
 * Firestore Sync Service
 * Automatically syncs Eloquent model changes to Firestore.
 * This enables a smooth migration: SQL (SQLite) for reads, Firestore for external access.
 */
class FirestoreSyncService
{
    protected FirestoreClient $client;

    public function __construct(FirestoreClient $client)
    {
        $this->client = $client;
    }

    /**
     * Sync a model's data to Firestore.
     */
    public function sync(string $collection, string $documentId, array $data): void
    {
        try {
            $this->client->setDocument($collection, $documentId, $data, true);
        } catch (\Exception $e) {
            Log::error("Firestore sync failed for {$collection}/{$documentId}: ".$e->getMessage());
        }
    }

    /**
     * Delete a document from Firestore.
     */
    public function delete(string $collection, string $documentId): void
    {
        try {
            $this->client->deleteDocument($collection, $documentId);
        } catch (\Exception $e) {
            Log::error("Firestore delete failed for {$collection}/{$documentId}: ".$e->getMessage());
        }
    }

    /**
     * Get the Firestore collection name for a model class.
     */
    public static function collectionFor(string $modelClass): string
    {
        $map = [
            \App\Models\User::class => 'users',
            \App\Models\Phone::class => 'phones',
            \App\Models\Benchmark::class => 'benchmarks',
            \App\Models\SpecBattery::class => 'spec_batteries',
            \App\Models\SpecBody::class => 'spec_bodies',
            \App\Models\SpecCamera::class => 'spec_cameras',
            \App\Models\SpecConnectivity::class => 'spec_connectivities',
            \App\Models\SpecPlatform::class => 'spec_platforms',
            \App\Models\Comment::class => 'comments',
            \App\Models\CommentUpvote::class => 'comment_upvotes',
            \App\Models\Blog::class => 'blogs',
            \App\Models\ForumCategory::class => 'forum_categories',
            \App\Models\ForumPost::class => 'forum_posts',
            \App\Models\ForumComment::class => 'forum_comments',
            \App\Models\Chat::class => 'chats',
            \App\Models\ChatMessage::class => 'chat_messages',
        ];

        return $map[$modelClass] ?? strtolower(class_basename($modelClass)).'s';
    }
}
