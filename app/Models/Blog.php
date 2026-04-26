<?php

namespace App\Models;

use App\Repositories\UserRepository;
use App\Services\SEO\SEOData;
use Illuminate\Support\Str;

class Blog extends FirestoreModel
{
    protected array $casts = [
        'is_published' => 'boolean',
        'published_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function author(): ?User
    {
        return app(UserRepository::class)->find($this->attributes['user_id'] ?? '');
    }

    public function getSEOData(): SEOData
    {
        $imageUrl = $this->featured_image ?
            (str_starts_with($this->featured_image, 'http') ? $this->featured_image : url($this->featured_image))
            : asset('assets/logo.png');

        $publishedAt = $this->published_at;
        $createdAt = $this->attributes['created_at'] ?? null;

        $datePublished = $publishedAt instanceof \DateTimeInterface
            ? $publishedAt->format('c')
            : ($publishedAt ?: ($createdAt ?: now()->format('c')));

        return new SEOData(
            title: "{$this->title} | PhoneFinderHub Blog",
            description: $this->excerpt ?: Str::limit(strip_tags($this->content), 150),
            image: $imageUrl,
            url: route('blogs.show', $this->slug),
            type: 'article',
            schema: [
                '@context' => 'https://schema.org',
                '@type' => 'NewsArticle',
                'headline' => $this->title,
                'image' => [$imageUrl],
                'datePublished' => $datePublished,
                'author' => [
                    [
                        '@type' => 'Person',
                        'name' => $this->author?->name ?? 'PhoneFinderHub',
                    ],
                ],
            ]
        );
    }
}
