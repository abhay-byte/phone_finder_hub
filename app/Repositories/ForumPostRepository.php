<?php

namespace App\Repositories;

use App\Models\ForumPost;

class ForumPostRepository extends FirestoreRepository
{
    protected string $collection = 'forum_posts';

    protected function makeModel(array $data): object
    {
        return new ForumPost($data);
    }

    protected function modelClass(): string
    {
        return ForumPost::class;
    }

    public function forCategory(string $categoryId): array
    {
        $all = $this->all();
        $posts = array_filter($all, fn ($post) => ($post->forum_category_id ?? '') === $categoryId);
        usort($posts, fn ($a, $b) => ($b->created_at ?? '') <=> ($a->created_at ?? ''));

        return $posts;
    }

    public function findBySlug(string $slug): ?ForumPost
    {
        $result = $this->where('slug', '==', $slug)->first();

        return $result instanceof ForumPost ? $result : null;
    }
}
