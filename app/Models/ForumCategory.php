<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ForumCategory extends Model
{
    protected $fillable = ['name', 'slug', 'description', 'order', 'rules_banner'];

    public function posts()
    {
        return $this->hasMany(ForumPost::class);
    }
}
