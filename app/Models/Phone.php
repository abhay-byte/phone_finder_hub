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
        'announced_date',
        'ueps_score',
        'value_score',
    ];

    protected $casts = [
        'release_date' => 'date',
        'price' => 'decimal:2',
        'announced_date' => 'date',
        'overall_score' => 'decimal:1',
        'ueps_score' => 'decimal:1',
        'value_score' => 'decimal:2',
    ];

    protected $appends = [
        'value_score',
        'ueps_details',
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
        if (!$this->price || $this->price == 0) {
            return 0;
        }

        // Use stored FPI (overall_score) if available, otherwise calculate
        $fpi = $this->overall_score ?? ($this->calculateFPI()['total'] ?? 0);

        if ($fpi == 0) return 0;

        // Value Score = (FPI / Price) * 10,000
        $score = ($fpi / $this->price) * 10000;
        return round($score, 1);
    }


    public function calculateFPI()
    {
        if (!$this->benchmarks) {
            return 0;
        }

        // 1. Get Max Scores dynamically (Cached for performance)
        $maxScores = \Illuminate\Support\Facades\Cache::remember('benchmark_max_scores', 3600, function () {
            return [
                'antutu' => Benchmark::max('antutu_score') ?: 4000000,
                'geekbench_multi' => Benchmark::max('geekbench_multi') ?: 12000,
                'geekbench_single' => Benchmark::max('geekbench_single') ?: 3500,
                '3dmark' => Benchmark::max('dmark_wild_life_extreme') ?: 8000,
            ];
        });

        // 2. Normalize Scores (0-100)
        $normAntutu = ($this->benchmarks->antutu_score / $maxScores['antutu']) * 100;
        $normGeekbenchMulti = ($this->benchmarks->geekbench_multi / $maxScores['geekbench_multi']) * 100;
        $normGeekbenchSingle = ($this->benchmarks->geekbench_single / $maxScores['geekbench_single']) * 100;
        $norm3DMark = ($this->benchmarks->dmark_wild_life_extreme / $maxScores['3dmark']) * 100;

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

    /**
     * Recalculate and save all scores for this phone.
     */
    public function updateScores()
    {
        // Calculate UEPS
        $ueps = \App\Services\UepsScoringService::calculate($this);
        $this->ueps_score = $ueps['total_score'];

        // Calculate FPI
        $fpi = $this->calculateFPI();
        if (is_array($fpi)) {
            $this->overall_score = $fpi['total'];
        }
        
        // Calculate Value Score (Persisted)
        if ($this->price > 0 && $this->overall_score > 0) {
            $this->value_score = round(($this->overall_score / $this->price) * 10000, 2);
        } else {
            $this->value_score = 0;
        }

        $this->saveQuietly();
    }

    public function getUepsDetailsAttribute()
    {
        return \App\Services\UepsScoringService::calculate($this);
    }
}
