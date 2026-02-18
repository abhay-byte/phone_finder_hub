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
        'antutu_v10_score',
        'dmark_test_type',
        'repairability_score',
        'energy_label',
        'battery_active_use_score',
        'charge_time_test',
        'free_fall_rating',
        'dmark_wild_life_stress_stability',
        'dxomark_score',
        'phonearena_camera_score',
        'other_benchmark_score',
    ];

    public function phone()
    {
        return $this->belongsTo(Phone::class);
    }
}
