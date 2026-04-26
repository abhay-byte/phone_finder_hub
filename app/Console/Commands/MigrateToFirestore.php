<?php

namespace App\Console\Commands;

use App\Services\Firestore\FirestoreClient;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class MigrateToFirestore extends Command
{
    protected $signature = 'migrate:to-firestore {--clear : Clear Firestore collections before migrating} {--skip-existing : Skip documents that already exist in Firestore}';

    protected $description = 'Migrate all SQLite data to Firestore safely';

    public function handle(FirestoreClient $client)
    {
        if ($this->option('clear')) {
            $confirmed = $this->confirm('This will DELETE all existing Firestore data. Are you sure?', false);
            if (! $confirmed) {
                $this->info('Migration cancelled.');

                return Command::SUCCESS;
            }

            $this->warn('Clearing Firestore collections...');
            $this->clearCollection($client, 'phones');
            $this->clearCollection($client, 'users');
            $this->clearCollection($client, 'comments');
            $this->clearCollection($client, 'blogs');
            $this->clearCollection($client, 'forum_categories');
            $this->clearCollection($client, 'forum_posts');
            $this->clearCollection($client, 'forum_comments');
            $this->clearCollection($client, 'comment_upvotes');
            $this->clearCollection($client, 'chats');
            $this->clearCollection($client, 'chat_messages');
        }

        $skipExisting = $this->option('skip-existing');

        $this->migrateUsers($client, $skipExisting);
        $this->migratePhones($client, $skipExisting);
        $this->migrateComments($client, $skipExisting);
        $this->migrateBlogs($client, $skipExisting);
        $this->migrateForumCategories($client, $skipExisting);
        $this->migrateForumPosts($client, $skipExisting);
        $this->migrateForumComments($client, $skipExisting);
        $this->migrateCommentUpvotes($client, $skipExisting);
        $this->migrateChats($client, $skipExisting);
        $this->migrateChatMessages($client, $skipExisting);

        $this->info('Migration complete!');

        return Command::SUCCESS;
    }

    protected function clearCollection(FirestoreClient $client, string $collection): void
    {
        $docs = $client->listDocuments($collection);
        foreach ($docs as $doc) {
            $client->deleteDocument($collection, $doc['id']);
        }
        $this->info("Cleared {$collection}");
    }

    protected function migrateUsers(FirestoreClient $client, bool $skipExisting = false): void
    {
        $users = DB::table('users')->get();
        $bar = $this->output->createProgressBar(count($users));
        $bar->start();
        $migrated = 0;
        $skipped = 0;

        foreach ($users as $user) {
            $data = (array) $user;
            $data['id'] = (string) $data['id'];
            if ($skipExisting && $client->getDocument('users', $data['id'])) {
                $skipped++;
                $bar->advance();

                continue;
            }
            $data['created_at'] = $this->formatDate($data['created_at'] ?? null);
            $data['updated_at'] = $this->formatDate($data['updated_at'] ?? null);
            $data['email_verified_at'] = $this->formatDate($data['email_verified_at'] ?? null);
            $client->setDocument('users', $data['id'], $data);
            $migrated++;
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("Migrated {$migrated} users, skipped {$skipped} existing");
    }

    protected function migratePhones(FirestoreClient $client, bool $skipExisting = false): void
    {
        $phones = DB::table('phones')->get();
        $bar = $this->output->createProgressBar(count($phones));
        $bar->start();
        $migrated = 0;
        $skipped = 0;

        foreach ($phones as $phone) {
            $data = (array) $phone;
            $data['id'] = (string) $data['id'];
            if ($skipExisting && $client->getDocument('phones', $data['id'])) {
                $skipped++;
                $bar->advance();

                continue;
            }
            $data['created_at'] = $this->formatDate($data['created_at'] ?? null);
            $data['updated_at'] = $this->formatDate($data['updated_at'] ?? null);
            $data['release_date'] = $this->formatDate($data['release_date'] ?? null);
            $data['announced_date'] = $this->formatDate($data['announced_date'] ?? null);

            // Embed specs
            $body = DB::table('spec_bodies')->where('phone_id', $phone->id)->first();
            if ($body) {
                $data['body'] = (array) $body;
                unset($data['body']['id'], $data['body']['phone_id'], $data['body']['created_at'], $data['body']['updated_at']);
            }

            $platform = DB::table('spec_platforms')->where('phone_id', $phone->id)->first();
            if ($platform) {
                $data['platform'] = (array) $platform;
                unset($data['platform']['id'], $data['platform']['phone_id'], $data['platform']['created_at'], $data['platform']['updated_at']);
            }

            $camera = DB::table('spec_cameras')->where('phone_id', $phone->id)->first();
            if ($camera) {
                $data['camera'] = (array) $camera;
                unset($data['camera']['id'], $data['camera']['phone_id'], $data['camera']['created_at'], $data['camera']['updated_at']);
            }

            $connectivity = DB::table('spec_connectivities')->where('phone_id', $phone->id)->first();
            if ($connectivity) {
                $data['connectivity'] = (array) $connectivity;
                unset($data['connectivity']['id'], $data['connectivity']['phone_id'], $data['connectivity']['created_at'], $data['connectivity']['updated_at']);
            }

            $battery = DB::table('spec_batteries')->where('phone_id', $phone->id)->first();
            if ($battery) {
                $data['battery'] = (array) $battery;
                unset($data['battery']['id'], $data['battery']['phone_id'], $data['battery']['created_at'], $data['battery']['updated_at']);
            }

            $benchmarks = DB::table('benchmarks')->where('phone_id', $phone->id)->first();
            if ($benchmarks) {
                $data['benchmarks'] = (array) $benchmarks;
                unset($data['benchmarks']['id'], $data['benchmarks']['phone_id'], $data['benchmarks']['created_at'], $data['benchmarks']['updated_at']);
            }

            $client->setDocument('phones', $data['id'], $data);
            $migrated++;
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("Migrated {$migrated} phones, skipped {$skipped} existing");
    }

    protected function migrateComments(FirestoreClient $client, bool $skipExisting = false): void
    {
        $comments = DB::table('comments')->get();
        $bar = $this->output->createProgressBar(count($comments));
        $bar->start();
        $migrated = 0;
        $skipped = 0;

        foreach ($comments as $comment) {
            $data = (array) $comment;
            $data['id'] = (string) $data['id'];
            if ($skipExisting && $client->getDocument('comments', $data['id'])) {
                $skipped++;
                $bar->advance();

                continue;
            }
            $data['created_at'] = $this->formatDate($data['created_at'] ?? null);
            $data['updated_at'] = $this->formatDate($data['updated_at'] ?? null);
            $client->setDocument('comments', $data['id'], $data);
            $migrated++;
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("Migrated {$migrated} comments, skipped {$skipped} existing");
    }

    protected function migrateBlogs(FirestoreClient $client, bool $skipExisting = false): void
    {
        $blogs = DB::table('blogs')->get();
        $bar = $this->output->createProgressBar(count($blogs));
        $bar->start();
        $migrated = 0;
        $skipped = 0;

        foreach ($blogs as $blog) {
            $data = (array) $blog;
            $data['id'] = (string) $data['id'];
            if ($skipExisting && $client->getDocument('blogs', $data['id'])) {
                $skipped++;
                $bar->advance();

                continue;
            }
            $data['created_at'] = $this->formatDate($data['created_at'] ?? null);
            $data['updated_at'] = $this->formatDate($data['updated_at'] ?? null);
            $data['published_at'] = $this->formatDate($data['published_at'] ?? null);
            $client->setDocument('blogs', $data['id'], $data);
            $migrated++;
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("Migrated {$migrated} blogs, skipped {$skipped} existing");
    }

    protected function migrateForumCategories(FirestoreClient $client, bool $skipExisting = false): void
    {
        $categories = DB::table('forum_categories')->get();
        $bar = $this->output->createProgressBar(count($categories));
        $bar->start();
        $migrated = 0;
        $skipped = 0;

        foreach ($categories as $category) {
            $data = (array) $category;
            $data['id'] = (string) $data['id'];
            if ($skipExisting && $client->getDocument('forum_categories', $data['id'])) {
                $skipped++;
                $bar->advance();

                continue;
            }
            $data['created_at'] = $this->formatDate($data['created_at'] ?? null);
            $data['updated_at'] = $this->formatDate($data['updated_at'] ?? null);
            $client->setDocument('forum_categories', $data['id'], $data);
            $migrated++;
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("Migrated {$migrated} forum categories, skipped {$skipped} existing");
    }

    protected function migrateForumPosts(FirestoreClient $client, bool $skipExisting = false): void
    {
        $posts = DB::table('forum_posts')->get();
        $bar = $this->output->createProgressBar(count($posts));
        $bar->start();
        $migrated = 0;
        $skipped = 0;

        foreach ($posts as $post) {
            $data = (array) $post;
            $data['id'] = (string) $data['id'];
            if ($skipExisting && $client->getDocument('forum_posts', $data['id'])) {
                $skipped++;
                $bar->advance();

                continue;
            }
            $data['created_at'] = $this->formatDate($data['created_at'] ?? null);
            $data['updated_at'] = $this->formatDate($data['updated_at'] ?? null);
            $client->setDocument('forum_posts', $data['id'], $data);
            $migrated++;
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("Migrated {$migrated} forum posts, skipped {$skipped} existing");
    }

    protected function migrateForumComments(FirestoreClient $client, bool $skipExisting = false): void
    {
        $comments = DB::table('forum_comments')->get();
        $bar = $this->output->createProgressBar(count($comments));
        $bar->start();
        $migrated = 0;
        $skipped = 0;

        foreach ($comments as $comment) {
            $data = (array) $comment;
            $data['id'] = (string) $data['id'];
            if ($skipExisting && $client->getDocument('forum_comments', $data['id'])) {
                $skipped++;
                $bar->advance();

                continue;
            }
            $data['created_at'] = $this->formatDate($data['created_at'] ?? null);
            $data['updated_at'] = $this->formatDate($data['updated_at'] ?? null);
            $client->setDocument('forum_comments', $data['id'], $data);
            $migrated++;
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("Migrated {$migrated} forum comments, skipped {$skipped} existing");
    }

    protected function migrateCommentUpvotes(FirestoreClient $client, bool $skipExisting = false): void
    {
        $upvotes = DB::table('comment_upvotes')->get();
        $bar = $this->output->createProgressBar(count($upvotes));
        $bar->start();
        $migrated = 0;
        $skipped = 0;

        foreach ($upvotes as $upvote) {
            $data = (array) $upvote;
            $data['id'] = (string) $data['id'];
            if ($skipExisting && $client->getDocument('comment_upvotes', $data['id'])) {
                $skipped++;
                $bar->advance();

                continue;
            }
            $data['created_at'] = $this->formatDate($data['created_at'] ?? null);
            $data['updated_at'] = $this->formatDate($data['updated_at'] ?? null);
            $client->setDocument('comment_upvotes', $data['id'], $data);
            $migrated++;
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("Migrated {$migrated} comment upvotes, skipped {$skipped} existing");
    }

    protected function migrateChats(FirestoreClient $client, bool $skipExisting = false): void
    {
        $chats = DB::table('chats')->get();
        $bar = $this->output->createProgressBar(count($chats));
        $bar->start();
        $migrated = 0;
        $skipped = 0;

        foreach ($chats as $chat) {
            $data = (array) $chat;
            $data['id'] = (string) $data['id'];
            if ($skipExisting && $client->getDocument('chats', $data['id'])) {
                $skipped++;
                $bar->advance();

                continue;
            }
            $data['created_at'] = $this->formatDate($data['created_at'] ?? null);
            $data['updated_at'] = $this->formatDate($data['updated_at'] ?? null);
            $client->setDocument('chats', $data['id'], $data);
            $migrated++;
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("Migrated {$migrated} chats, skipped {$skipped} existing");
    }

    protected function migrateChatMessages(FirestoreClient $client, bool $skipExisting = false): void
    {
        $messages = DB::table('chat_messages')->get();
        $bar = $this->output->createProgressBar(count($messages));
        $bar->start();
        $migrated = 0;
        $skipped = 0;

        foreach ($messages as $message) {
            $data = (array) $message;
            $data['id'] = (string) $data['id'];
            if ($skipExisting && $client->getDocument('chat_messages', $data['id'])) {
                $skipped++;
                $bar->advance();

                continue;
            }
            $data['created_at'] = $this->formatDate($data['created_at'] ?? null);
            $data['updated_at'] = $this->formatDate($data['updated_at'] ?? null);
            $client->setDocument('chat_messages', $data['id'], $data);
            $migrated++;
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("Migrated {$migrated} chat messages, skipped {$skipped} existing");
    }

    protected function formatDate(?string $date): ?string
    {
        if (! $date) {
            return null;
        }
        try {
            return (new \DateTime($date))->format('c');
        } catch (\Exception $e) {
            return null;
        }
    }
}
