<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SpecPlatform extends Model
{
    protected $fillable = [
        'phone_id',
        'os',
        'os_details',
        'chipset',
        'cpu',
        'gpu',
        'memory_card_slot',
        'internal_storage',
        'ram',
        'storage_type',
        'bootloader_unlockable',
        'turnip_support',
        'turnip_support_level', // New
        'os_openness', // New
        'gpu_emulation_tier', // New
        'aosp_aesthetics_score',
        'custom_rom_support', // New
    ];

    public function phone()
    {
        return $this->belongsTo(Phone::class);
    }
}
