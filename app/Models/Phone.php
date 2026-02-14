<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Services\FlagshipScoringService;

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
        'amazon_url',
        'amazon_price',
        'flipkart_url',
        'flipkart_price',
        'announced_date',
    ];

    protected $casts = [
        'release_date' => 'date',
        'price' => 'decimal:2',
        'announced_date' => 'date',
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
        $fpi = $this->calculateFPI();
        $totalFpi = is_array($fpi) ? $fpi['total'] : 0;

        if ($totalFpi == 0) return 0;

        // Value Score = (FPI / Price) * 10,000
        // Example: 92 / 60,000 * 10,000 = 15.3 points per ₹10k
        $score = ($totalFpi / $this->price) * 10000;
        return round($score, 1);
    }

    public function calculateFPI()
    {
        if (!$this->benchmarks) {
            return 0;
        }

        // 1. Get Max Scores dynamically
        $maxAntutu = Benchmark::max('antutu_score') ?: 4000000; // Fallback to 4M
        $maxGeekbenchMulti = Benchmark::max('geekbench_multi') ?: 12000;
        $maxGeekbenchSingle = Benchmark::max('geekbench_single') ?: 3500;
        $max3DMark = Benchmark::max('dmark_wild_life_extreme') ?: 8000;

        // 2. Normalize Scores (0-100)
        $normAntutu = ($this->benchmarks->antutu_score / $maxAntutu) * 100;
        $normGeekbenchMulti = ($this->benchmarks->geekbench_multi / $maxGeekbenchMulti) * 100;
        $normGeekbenchSingle = ($this->benchmarks->geekbench_single / $maxGeekbenchSingle) * 100;
        $norm3DMark = ($this->benchmarks->dmark_wild_life_extreme / $max3DMark) * 100;

        // 3. Apply Weights
        // AnTuTu (40%), GB Multi (25%), GB Single (15%), 3DMark (20%)
        $weightedAntutu = $normAntutu * 0.40;
        $weightedGeekbenchMulti = $normGeekbenchMulti * 0.25;
        $weightedGeekbenchSingle = $normGeekbenchSingle * 0.15;
        $weighted3DMark = $norm3DMark * 0.20;

        // 4. Calculate Final FPI
        $fpi = $weightedAntutu + $weightedGeekbenchMulti + $weightedGeekbenchSingle + $weighted3DMark;

        return [
            'total' => round($fpi, 1),
            'breakdown' => [
                'antutu' => round($weightedAntutu, 1),
                'geekbench_multi' => round($weightedGeekbenchMulti, 1),
                'geekbench_single' => round($weightedGeekbenchSingle, 1),
                '3dmark' => round($weighted3DMark, 1),
            ],
            'max_possible' => 100 // Since we normalize to max in DB, the top phone will be 100
        ];
    }
    public function getUepsScoreAttribute()
    {
        return \App\Services\UepsScoringService::calculate($this);
    }
}
