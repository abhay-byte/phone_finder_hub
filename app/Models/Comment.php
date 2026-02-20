<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Comment extends Model
{
    protected $fillable = [
        'phone_id',
        'user_id',
        'parent_id',
        'content',
        'upvotes_count',
    ];

    public function phone(): BelongsTo
    {
        return $this->belongsTo(Phone::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(Comment::class, 'parent_id');
    }

    public function upvotes(): HasMany
    {
        return $this->hasMany(CommentUpvote::class);
    }

    public function getAuthorNameAttribute(): string
    {
        if ($this->user_id) {
            return $this->user->name;
        }

        // Generate a consistently random but anonymous sounding name based on the comment ID or IP
        // Since we don't have IP here easily without request, let's use a hash of the comment ID to keep it somewhat stable
        return 'Anonymous ' . substr(md5('anon' . $this->id), 0, 6);
    }
}
