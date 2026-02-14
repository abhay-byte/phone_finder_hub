<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Benchmark extends Model
{
    protected $fillable = [
        'phone_id',
        'antutu_score',
        'geekbench_single',
        'geekbench_multi',
        'dmark_wild_life_extreme',
        'battery_endurance_hours',
    ];

    public function phone()
    {
        return $this->belongsTo(Phone::class);
    }
}
