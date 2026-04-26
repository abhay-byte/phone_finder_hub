<?php

namespace App\Repositories;

use App\Models\Blog;
use Illuminate\Support\Collection;

class BlogRepository extends FirestoreRepository
{
    protected string $collection = 'blogs';

    protected function makeModel(array $data): object
    {
        return new Blog($data);
    }

    protected function modelClass(): string
    {
        return Blog::class;
    }

    /**
     * Get published blogs ordered by published_at.
     */
    public function published(int $limit = 0): Collection
    {
        $all = $this->all();
        $blogs = array_filter($all, fn ($blog) => $blog->is_published);
        usort($blogs, fn ($a, $b) => ($b->published_at ?? '') <=> ($a->published_at ?? ''));

        if ($limit > 0) {
            $blogs = array_slice($blogs, 0, $limit);
        }

        return collect($blogs);
    }

    /**
     * Find by slug.
     */
    public function findBySlug(string $slug): ?Blog
    {
        $result = $this->where('slug', '==', $slug)->first();

        return $result instanceof Blog ? $result : null;
    }
}
