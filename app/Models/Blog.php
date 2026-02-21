<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;
use App\Models\Traits\HasSEO;
use App\Services\SEO\SEOData;

class Blog extends Model
{
    use HasSEO;

    protected $fillable = [
        'title',
        'slug',
        'content',
        'excerpt',
        'featured_image',
        'user_id',
        'is_published',
        'published_at',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'published_at' => 'datetime',
    ];

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getSEOData(): SEOData
    {
        // Ensure image URL is absolute for SEO metadata
        $imageUrl = $this->featured_image ? 
            (str_starts_with($this->featured_image, 'http') ? $this->featured_image : url($this->featured_image)) 
            : asset('assets/logo.png');

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
                'datePublished' => $this->published_at ? $this->published_at->toIso8601String() : $this->created_at->toIso8601String(),
                'author' => [
                    [
                        '@type' => 'Person', 
                        'name' => $this->author->name ?? 'PhoneFinderHub'
                    ]
                ]
            ]
        );
    }
}
