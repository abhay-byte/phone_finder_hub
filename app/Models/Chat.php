<?php

namespace App\Models;

use App\Models\Traits\SyncsToFirestore;
use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    use SyncsToFirestore;

    protected $fillable = ['user_id', 'title'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function messages()
    {
        return $this->hasMany(ChatMessage::class);
    }
}
