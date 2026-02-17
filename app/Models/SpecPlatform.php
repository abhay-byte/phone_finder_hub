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
        'ram_min',
        'ram_max',
        'storage_min',
        'storage_max',
    ];

    public static function booted()
    {
        static::saving(function ($model) {
            // Auto-calculate RAM limits
            if ($model->isDirty('ram') && $model->ram) {
                preg_match_all('/(\d+)\s*GB/i', $model->ram, $matches);
                if (!empty($matches[1])) {
                    $rams = array_map('intval', $matches[1]);
                    $model->ram_min = min($rams);
                    $model->ram_max = max($rams);
                }
            }

            // Auto-calculate Storage limits
            if ($model->isDirty('internal_storage') && $model->internal_storage) {
                $storageValues = [];
                // Match GB
                preg_match_all('/(\d+)\s*GB/i', $model->internal_storage, $gbMatches);
                if (!empty($gbMatches[1])) {
                    foreach ($gbMatches[1] as $val) $storageValues[] = intval($val);
                }
                // Match TB
                preg_match_all('/(\d+)\s*TB/i', $model->internal_storage, $tbMatches);
                if (!empty($tbMatches[1])) {
                    foreach ($tbMatches[1] as $val) $storageValues[] = intval($val) * 1024;
                }

                if (!empty($storageValues)) {
                    $model->storage_min = min($storageValues);
                    $model->storage_max = max($storageValues);
                }
            }
        });
    }

    public function phone()
    {
        return $this->belongsTo(Phone::class);
    }
}
