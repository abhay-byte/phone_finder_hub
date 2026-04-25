<?php

namespace App\Models;

use App\Models\Traits\SyncsToFirestore;
use Illuminate\Database\Eloquent\Model;

class ForumComment extends Model
{
    use SyncsToFirestore;

    protected $fillable = ['forum_post_id', 'user_id', 'content'];

    public function post()
    {
        return $this->belongsTo(ForumPost::class, 'forum_post_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
