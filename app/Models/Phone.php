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
        'gpx_score',
        'gpx_details',
    ];

    protected $casts = [
        'release_date' => 'date',
        'price' => 'decimal:2',
        'announced_date' => 'date',
        'overall_score' => 'decimal:1',
        'ueps_score' => 'decimal:1',
        'value_score' => 'decimal:2',
        'gpx_score' => 'decimal:2',
        'gpx_details' => 'array',
    ];

    protected $appends = [
        'value_score',
        'ueps_details',
        'gpx_details_append',
    ];

    public function getGpxDetailsAppendAttribute()
    {
        return $this->gpx_details ?? [];
    }

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
        
        // Calculate GPX
        $gpx = $this->calculateGPX();
        $this->gpx_score = $gpx['score'];
        $this->gpx_details = $gpx['details'];

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
    
    /**
     * Calculate GPX-300 Gaming Score
     * Based on user provided rulebook.
     */
    /**
     * Calculate GPX-300 Gaming Score (Rulebook v2)
     * 300-point system focusing on stability, thermals, and specific hardware feature sets.
     */
    public function calculateGPX()
    {
        $breakdown = [];
        $total = 0;

        // 1. SoC & GPU Power (70 pts)
        $socScore = 0;
        $chipset = strtolower($this->platform->chipset ?? '');
        
        // GPU Tier (45 pts)
        if (str_contains($chipset, 'snapdragon 8 elite') && str_contains($chipset, 'gen 5')) {
             $socScore += 45; // Ultra Elite - SD 8 Elite Gen 5
        } elseif (str_contains($chipset, 'dimensity 9500')) {
             $socScore += 45; // Ultra Elite - Dimensity 9500
        } elseif (str_contains($chipset, 'snapdragon 8 elite')) {
             $socScore += 38; // Flagship
        } elseif (str_contains($chipset, 'dimensity 9400')) {
             $socScore += 38; // Flagship
        } elseif (str_contains($chipset, 'snapdragon 8 gen 3') || str_contains($chipset, 'dimensity 9300')) {
            $socScore += 30; // Upper High
        } elseif (str_contains($chipset, 'snapdragon 8 gen 2') || str_contains($chipset, 'dimensity 9200')) {
            $socScore += 20; // Mid-tier
        } else {
             $socScore += 10; // Base
        }

        // CPU Power (25 pts) - Normalized Formula
        // (Geekbench Multi × 60%) + (Geekbench Single × 40%)
        // Scored as percentage of best-in-database.
        $cpuScore = 0;
        if ($this->benchmarks) {
            // Get max scores from DB or use static baselines if DB is empty/low
            // To ensure consistency, we'll use high static baselines for "best in class" reference
            $maxMulti = 13000; // Estimated SD 8 Elite Gen 5 / Dimensity 9500 level
            $maxSingle = 4000;

            $normMulti = min(($this->benchmarks->geekbench_multi / $maxMulti), 1);
            $normSingle = min(($this->benchmarks->geekbench_single / $maxSingle), 1);
            
            $percentage = ($normMulti * 0.60) + ($normSingle * 0.40);
            $cpuScore = $percentage * 25;
        }
        $socScore += $cpuScore;
        
        $breakdown['soc_gpu'] = round($socScore, 1);
        $total += $socScore;

        // 2. Sustained Performance & Cooling (50 pts)
        $sustainedScore = 0;
        
        // Thermal Stability (30 pts)
        // 3DMark Wild Life Extreme Stress Test stability
        if ($this->benchmarks && $this->benchmarks->dmark_wild_life_stress_stability) {
             $stability = $this->benchmarks->dmark_wild_life_stress_stability;
             if ($stability >= 95) $sustainedScore += 30;
             elseif ($stability >= 85) $sustainedScore += 22;
             elseif ($stability >= 75) $sustainedScore += 15;
             else $sustainedScore += 5;
        } else {
            // Penalty for missing stability data (Crucial for GPX)
            $sustainedScore += 0; 
        }

        // Cooling Hardware (20 pts)
        $coolingScore = 5; // Basic graphite default
        $name = strtolower($this->name);
        // Heuristics based on model name and known features
        if (str_contains($name, 'rog') || str_contains($name, 'redmagic')) {
             $coolingScore = 20; // Active fan
        } elseif (str_contains($name, 'pro') || str_contains($name, 'ultra') || str_contains($name, 'gt') || str_contains($name, 'iqoo') || str_contains($name, 'oneplus')) {
             // Assuming High-end phones have VC
             // Refine this if we have a specific 'cooling' field in specs later
             $coolingScore = 15; // Vapor Chamber (Dual or Large) - giving benefit of doubt to flagships
             if (str_contains($name, '15') || str_contains($name, '16')) {
                 $coolingScore = 15; // Modern flagships usually have good VC
             }
        }
        
        $sustainedScore += $coolingScore;
        $breakdown['sustained'] = $sustainedScore;
        $total += $sustainedScore;

        // 3. Gaming Display (40 pts)
        $displayScore = 0;
        $displaySpecs = strtolower($this->body->display_type ?? '');
        $displayFeatures = strtolower($this->body->display_features ?? '');
        
        // Refresh Rate (10 pts) - parsing from string or separate field if available
        // Heuristic mapping since we don't have separate column yet, using display_type text
        if (str_contains($displaySpecs, '165hz') || str_contains($displaySpecs, '144hz')) {
            if (str_contains($displaySpecs, '165hz')) $displayScore += 10;
            else $displayScore += 8;
        } elseif (str_contains($displaySpecs, '120hz')) {
            $displayScore += 6;
        } else {
            $displayScore += 0;
        }

        // Touch Sampling (10 pts)
        // Using heuristics based on known gaming phones or high-end models
        $touchRate = 240; // Default generic
        if (str_contains($name, 'rog') || str_contains($name, 'redmagic')) $touchRate = 1000;
        elseif (str_contains($name, 'iqoo') || str_contains($name, 'gt') || str_contains($name, 'ultra')) $touchRate = 480;
        
        // Check if explicit data is in display_features (rare but possible)
        if (str_contains($this->body->touch_sampling_rate ?? '', 'hz')) {
             $ts = intval($this->body->touch_sampling_rate);
             if ($ts > $touchRate) $touchRate = $ts;
        }

        if ($touchRate >= 1000) $displayScore += 10;
        elseif ($touchRate >= 720) $displayScore += 7;
        else $displayScore += 4; // < 720Hz

        // Brightness (10 pts)
        // > 3000 nits -> 10, 2000-3000 -> 6
        $nits = 0;
        if (preg_match('/(\d+)\s*nits/', $displayFeatures, $matches)) {
            $nits = intval($matches[1]);
        }
        // Fallback or override for known recent flagships
        if ($nits == 0) {
             if (str_contains($name, '15') || str_contains($name, '2025') || str_contains($name, '2026')) $nits = 4500; // Assumption for 2026 flagships
             elseif (str_contains($name, '14') || str_contains($name, '24')) $nits = 2500;
        }

        if ($nits > 3000) $displayScore += 10;
        elseif ($nits >= 2000) $displayScore += 6;
        else $displayScore += 0;

        // PWM / Eye Comfort (10 pts)
        // 2160Hz+ -> 10, 1440Hz -> 6
        // Heuristic: Recent flagships usually have high PWM
        $pwm = 0;
        if (str_contains($displayFeatures, 'pwm') || str_contains($this->body->pwm_dimming ?? '', 'pwm')) {
             if (preg_match('/(\d+)\s*hz/', $this->body->pwm_dimming ?? '', $matches)) {
                 $pwm = intval($matches[1]);
             }
        }
        
        // Default high PWM for devices known to have it if not parsed
        if ($pwm == 0 && (str_contains($name, 'honor') || str_contains($name, 'iqoo') || str_contains($name, 'oneplus'))) {
            $pwm = 2160; 
        }

        if ($pwm >= 2160) $displayScore += 10;
        elseif ($pwm >= 1440) $displayScore += 6;
        else $displayScore += 0;

        $breakdown['display'] = $displayScore;
        $total += $displayScore;

        // 4. Memory & Storage (25 pts)
        $memScore = 0;
        $storage = strtolower($this->platform->storage_type ?? '');
        $ramText = $this->platform->ram ?? '';
        
        // Storage Speed
        if (str_contains($storage, '4.1')) $memScore += 10;
        elseif (str_contains($storage, '4.0')) $memScore += 8;
        else $memScore += 5; // UFS 3.1 fallback

        // RAM Size
        $maxRam = 8;
        if (preg_match_all('/(\d+)gb/', strtolower($ramText), $matches)) {
            $maxRam = max($matches[1]);
        }
        if ($maxRam >= 16) $memScore += 7;
        if ($maxRam >= 24) $memScore += 3; // Bonus

        // LPDDR5X Check (Heuristic if not in DB, usually paired with UFS 4.0+)
        if (str_contains($storage, '4.0') || str_contains($storage, '4.1')) {
            $memScore += 5; // Assume LPDDR5X for UFS 4.x devices
        }

        $breakdown['memory'] = $memScore;
        $total += $memScore;

        // 5. Battery & Charging (25 pts)
        $batScore = 0;
        if ($this->battery) {
            // Capacity
            if ($this->battery->capacity_mah >= 6000) $batScore += 10;
            elseif ($this->battery->capacity_mah >= 5000) $batScore += 7;
            
            // Speed
            if ($this->battery->charging_speed_w >= 120) $batScore += 10;
            elseif ($this->battery->charging_speed_w >= 80) $batScore += 7;

            // Bypass Charging (5 pts)
            // Hard to detect from specs string, defaulting to "Yes" for gaming/flagship phones
            if (str_contains($name, 'rog') || str_contains($name, 'redmagic') || str_contains($name, 'black shark') || str_contains($name, 'iqoo') || str_contains($name, 'pooc') || str_contains($name, 'gt')) {
                $batScore += 5;
            } elseif ($this->battery->charging_speed_w >= 100) {
                 // Fast charging often implies advanced power mgt
                 $batScore += 5; 
            }
        }
        $breakdown['battery'] = $batScore;
        $total += $batScore;

        // 6. Gaming Software (30 pts)
        $softScore = 5; // Base
        // Dedicated Gaming Mode / Profiles / Optimization
        if (str_contains($name, 'rog') || str_contains($name, 'redmagic')) {
             $softScore = 30; // The kings of software suites
        } elseif (str_contains($name, 'iqoo') || str_contains($name, 'gt') || str_contains($name, 'oneplus') || str_contains($name, 'xiaomi')) {
             $softScore = 20; // Game Turbo, GT Mode, etc.
        } elseif (str_contains($name, 'samsung')) {
             $softScore = 15; // Game Booster
        }
        $breakdown['software'] = $softScore;
        $total += $softScore;

        // 7. Connectivity & Latency (20 pts)
        $connScore = 0;
        // WiFi 7 (8 pts)
        $wlan = strtolower($this->connectivity->wlan ?? '');
        if (str_contains($wlan, '7') || str_contains($wlan, 'be')) $connScore += 8;
        
        // 5G Advanced (5 pts) - Assume all modern flagships have it
        if (str_contains($chipset, 'snapdragon 8') || str_contains($chipset, 'dimensity 9')) $connScore += 5;

        // Gaming Antenna (4 pts) - Heuristic for gaming phones
        if (str_contains($name, 'rog') || str_contains($name, 'redmagic')) $connScore += 4;

        // BT 5.4 Low Latency (3 pts)
        $bt = strtolower($this->connectivity->bluetooth ?? '');
        if (str_contains($bt, '5.4') || str_contains($bt, '5.3')) $connScore += 3;

        $breakdown['connectivity'] = $connScore;
        $total += $connScore;

        // 8. Audio & Haptics (10 pts)
        // Dual speakers (5 pts) + X-axis motor (5 pts)
        $audioScore = 0;
        if (str_contains(strtolower($this->connectivity->loudspeaker ?? ''), 'stereo')) $audioScore += 5;
        else $audioScore += 5; // Default assumption for high-end

        // Haptics (tough to parse, assuming high end = good haptics)
        if ($this->price > 400 || str_contains($name, 'pro')) $audioScore += 5;

        $breakdown['audio'] = $audioScore;
        $total += $audioScore;

        // 9. Emulator & Developer Advantage (30 pts)
        $emuScore = 0;
        
        // GPU Driver & Emulation Support (20 pts)
        if (str_contains($chipset, 'snapdragon')) {
             if (str_contains($chipset, 'elite')) $emuScore += 20; // Adreno Elite + Turnip
             elseif (str_contains($chipset, 'gen 3') || str_contains($chipset, 'gen 2')) $emuScore += 16; // Adreno 7xx
             else $emuScore += 10;
        } elseif (str_contains($chipset, 'dimensity')) {
             if (str_contains($chipset, '9400') || str_contains($chipset, '9500') || str_contains($chipset, '9300')) {
                 $emuScore += 14; // Immortalis G925/G720
             } else {
                 $emuScore += 10; // Mali
             }
        }

        // Bootloader / Root / ROM (10 pts)
        // Custom ROM community support
        if (str_contains($this->brand, 'OnePlus') || str_contains($this->brand, 'Xiaomi') || str_contains($this->brand, 'Nothing') || str_contains($this->brand, 'Google')) {
            $emuScore += 10;
        } elseif (str_contains($name, 'rog')) {
            $emuScore += 5;
        }
        
        $breakdown['emulator'] = $emuScore;
        $total += $emuScore;
        
        // 10. GPM Multiplier
        // (3DMark Wild Life Extreme × 50%) + (AnTuTu GPU Score × 30%) + (GFXBench Aztec High Offscreen × 20%)
        // Normalized.
        $gpm = 0;
        if ($this->benchmarks) {
            $maxWildLife = 8000;
            $maxAntutuGpu = 1500000;
            // GFXBench not in DB yet, distributing weight to others: 3DMark 60%, Antutu 40%
            
            $normWildLife = min(($this->benchmarks->dmark_wild_life_extreme ?? 0) / $maxWildLife, 1);
            $antutuGpu = $this->benchmarks->antutu_score * 0.4; // Estimating GPU portion
            $normAntutu = min($antutuGpu / $maxAntutuGpu, 1);
            
            $gpm = (($normWildLife * 0.6) + ($normAntutu * 0.4)) * 100;
        }
        
        // Final Score Formula: Category Points + (GPM × 0.5)
        $finalScore = $total + ($gpm * 0.5);

        // Cap at 300 just in case
        $finalScore = min($finalScore, 300);

        return [
            'score' => round($finalScore, 2),
            'details' => $breakdown,
            'gpm' => round($gpm, 1)
        ];
    }
}
