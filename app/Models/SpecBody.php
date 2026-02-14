<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SpecBody extends Model
{
    protected $fillable = [
        'phone_id',
        'dimensions',
        'weight',
        'build_material',
        'sim',
        'ip_rating',
        'colors',
        'display_type',
        'display_size',
        'display_resolution',
        'display_protection',
        'display_features',
    ];

    public function phone()
    {
        return $this->belongsTo(Phone::class);
    }
}
