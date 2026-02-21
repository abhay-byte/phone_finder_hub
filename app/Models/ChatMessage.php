<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatMessage extends Model
{
    protected $fillable = ['chat_id', 'role', 'content'];

    public function chat()
    {
        return $this->belongsTo(Chat::class);
    }
}
