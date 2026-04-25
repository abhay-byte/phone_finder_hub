<?php

namespace App\Models;

use App\Models\Traits\SyncsToFirestore;
use Illuminate\Database\Eloquent\Model;

class ForumCategory extends Model
{
    use SyncsToFirestore;

    protected $fillable = ['name', 'slug', 'description', 'order', 'rules_banner'];

    public function posts()
    {
        return $this->hasMany(ForumPost::class);
    }
}
