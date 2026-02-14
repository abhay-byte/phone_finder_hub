<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SpecPlatform extends Model
{
    protected $fillable = [
        'phone_id',
        'os',
        'chipset',
        'cpu',
        'gpu',
        'memory_card_slot',
        'internal_storage',
        'ram',
        'storage_type',
        'bootloader_unlockable',
        'turnip_support',
        'aosp_aesthetics_score',
    ];

    public function phone()
    {
        return $this->belongsTo(Phone::class);
    }
}
