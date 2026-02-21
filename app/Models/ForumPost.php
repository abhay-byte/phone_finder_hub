<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ForumPost extends Model
{
    protected $fillable = ['forum_category_id', 'user_id', 'title', 'slug', 'content', 'views', 'upvotes'];

    public function category()
    {
        return $this->belongsTo(ForumCategory::class, 'forum_category_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function comments()
    {
        return $this->hasMany(ForumComment::class);
    }
}
