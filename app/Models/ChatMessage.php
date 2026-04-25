<?php

namespace App\Models;

use App\Models\Traits\SyncsToFirestore;
use Illuminate\Database\Eloquent\Model;

class ChatMessage extends Model
{
    use SyncsToFirestore;

    protected $fillable = ['chat_id', 'role', 'content'];

    public function chat()
    {
        return $this->belongsTo(Chat::class);
    }
}
