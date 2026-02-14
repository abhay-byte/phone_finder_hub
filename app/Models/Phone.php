<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Phone extends Model
{
    protected $fillable = [
        'name',
        'brand',
        'model_variant',
        'price',
        'overall_score',
        'release_date',
        'image_url',
    ];

    protected $casts = [
        'release_date' => 'date',
        'price' => 'decimal:2',
    ];

    public function body()
    {
        return $this->hasOne(SpecBody::class);
    }

    public function platform()
    {
        return $this->hasOne(SpecPlatform::class);
    }

    public function camera()
    {
        return $this->hasOne(SpecCamera::class);
    }

    public function connectivity()
    {
        return $this->hasOne(SpecConnectivity::class);
    }

    public function battery()
    {
        return $this->hasOne(SpecBattery::class);
    }

    public function benchmarks()
    {
        return $this->hasOne(Benchmark::class);
    }
    public function getValueScoreAttribute()
    {
        if (!$this->benchmarks || !$this->price || $this->price == 0) {
            return 0;
        }

        // Value Score = (AnTuTu Score / Price) * 1000
        // Example: 2,690,491 / 60,999 * 1000 = ~44,107 (This seems too high, maybe just / Price?)
        // Let's stick to the UI/UX doc: "pts/₹1k"
        // So (AnTuTu / Price) * 1000 is correct for "Points per 1000 Rupees".
        
        $score = ($this->benchmarks->antutu_score / $this->price) * 1000;
        return round($score);
    }
}
