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
        'cms_score',
        'cms_details',
        'expert_score',
    ];

    protected $casts = [
        'release_date' => 'date',
        'price' => 'decimal:2',
        'announced_date' => 'date',
        'overall_score' => 'decimal:1',
        'ueps_score' => 'decimal:1',
        'value_score' => 'decimal:2',
        'expert_score' => 'decimal:2', // Expert/Overall Score
        'gpx_score' => 'decimal:2',
        'gpx_details' => 'array',
        'cms_score' => 'decimal:1',
        'cms_details' => 'array',
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

    /**
     * Calculate Endurance Score (Adaptive)
     */
    public function calculateEnduranceScore()
    {
        $mah = 0;
        if ($this->battery && $this->battery->battery_type) {
            if (preg_match('/(\\d{3,5})\\s*mAh/i', $this->battery->battery_type, $matches)) {
                $mah = intval($matches[1]);
            }
        }

        // Check for Active Use Score first (Format: "17:31h" or "19.1h")
        $activeUseHours = 0;
        if ($this->benchmarks && $this->benchmarks->battery_active_use_score) {
            $rawScore = $this->benchmarks->battery_active_use_score;
            // Parse HH:MMh
            if (preg_match('/(\\d+):(\\d+)h/i', $rawScore, $matches)) {
                $activeUseHours = intval($matches[1]) + (intval($matches[2]) / 60);
            }
            // Parse decimal 19.1h
            elseif (preg_match('/(\\d+(\\.\\d+)?)\\s*h/i', $rawScore, $matches)) {
                $activeUseHours = floatval($matches[1]);
            }
        }

        $enduranceHours = 0;
        if ($this->benchmarks && $this->benchmarks->battery_endurance_hours) {
            $enduranceHours = floatval($this->benchmarks->battery_endurance_hours);
        }

        if ($mah === 0 && $enduranceHours === 0 && $activeUseHours === 0) return 0;

        // Base Score from Capacity (e.g. 5000mAh -> 50pts)
        $capacityScore = $mah / 100;

        // Adaptive Endurance Score
        // Prioritize Active Use Score (New Standard)
        if ($activeUseHours > 0) {
            // Active Use Score (e.g. 17h is great). Multiplier needed to match legacy scale.
            // 15h Active Use ~ 110h Endurance?
            // Let's say 16h * 3.5 = 56pts.
            $enduranceScore = $activeUseHours * 3.5;
        }
        // Fallback to Legacy Endurance Rating
        elseif ($enduranceHours > 30) {
             $enduranceScore = $enduranceHours * 0.45; // 120h -> 54pts
        } else {
             // Low endurance hours (unlikely legacy, maybe misparsed active?)
             $enduranceScore = $enduranceHours * 3.5;
        }

        $totalScore = $capacityScore + $enduranceScore;
        return round($totalScore, 1);
    }

    public function getValueScoreAttribute()
    {
        if (!$this->price || $this->price == 0) {
            return 0;
        }

        // 1. Calculate weighted rating (This logic is shared with expert_score)
        $rating = $this->calculateExpertRating();

        if ($rating == 0) return 0;

        // 3. Value Score = (Rating / Price) * 10,000
        $score = ($rating / $this->price) * 10000;
        return round($score, 1);
    }
    
    /**
     * Calculate the raw weighted rating based on all metrics.
     * Weights: UEPS (25%), FPI (25%), CMS (25%), GPX (15%), Endurance (10%)
     */
    public function calculateExpertRating()
    {
        // 1. Normalize all scores to 0-100
        $normUeps = ($this->ueps_score ?? 0) / 2.55;
        $normFpi = $this->overall_score ?? 0;
        $normGpx = ($this->gpx_score ?? 0) / 3.0;
        $normCms = ($this->cms_score ?? 0) / 13.3;
        
        // Ensure endurance score is available
        $endurance = $this->endurance_score ?? $this->calculateEnduranceScore();
        $normEndurance = $endurance / 1.6;

        // 2. Weighted Rating (Total 100%)
        $rating = ($normUeps * 0.25) + ($normFpi * 0.25) + ($normCms * 0.25) + ($normGpx * 0.15) + ($normEndurance * 0.10);
        
        return $rating;
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

        // Calculate CMS
        $cms = \App\Services\CmsScoringService::calculate($this);
        $this->cms_score = $cms['total_score'];
        $this->cms_details = $cms['breakdown'];
        
        // Calculate Endurance Score
        $this->endurance_score = $this->calculateEnduranceScore();

        // Calculate Expert/Overall Score (Persisted)
        $this->expert_score = round($this->calculateExpertRating(), 2);

        // Calculate Value Score (Persisted)
        if ($this->price > 0) {
            $this->value_score = $this->getValueScoreAttribute();
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
     * Calculate GPX-300 Gaming Score (Rulebook v2)
     * 300-point system focusing on stability, thermals, and specific hardware feature sets.
     * Returns detailed breakdown similar to UEPS for comparison page display.
     */
    public function calculateGPX()
    {
        $breakdown = [];
        $total = 0;

        // Helper to add points with details
        $addPoints = function(&$categoryScore, &$categoryDetails, $criterion, $points, $reason) {
            $categoryScore += $points;
            $categoryDetails[] = ['criterion' => $criterion, 'points' => $points, 'reason' => $reason];
        };

        // 1. SoC & GPU Power (70 pts)
        $socScore = 0;
        $socDetails = [];
        $chipset = strtolower($this->platform->chipset ?? '');
        $gpu = strtolower($this->platform->gpu ?? '');
        
        // GPU Tier (45 pts) - Based on 3DMark Wild Life Extreme score
        $gpuPoints = 0;
        $gpuTier = 'No benchmark data';
        
        if ($this->benchmarks && $this->benchmarks->dmark_wild_life_extreme) {
            $wildLifeScore = $this->benchmarks->dmark_wild_life_extreme;
            
            // Normalize to 45 points based on score ranges
            // Reference: Top phones score ~7000-7500, entry-level ~1000-1500
            if ($wildLifeScore >= 7000) {
                $gpuPoints = 45; // Ultra Elite
                $gpuTier = sprintf('Ultra Elite (%d)', $wildLifeScore);
            } elseif ($wildLifeScore >= 6000) {
                $gpuPoints = 40; // Flagship+
                $gpuTier = sprintf('Flagship+ (%d)', $wildLifeScore);
            } elseif ($wildLifeScore >= 5000) {
                $gpuPoints = 35; // Flagship
                $gpuTier = sprintf('Flagship (%d)', $wildLifeScore);
            } elseif ($wildLifeScore >= 4500) {
                $gpuPoints = 32; // Upper High-End
                $gpuTier = sprintf('Upper High-End (%d)', $wildLifeScore);
            } elseif ($wildLifeScore >= 4000) {
                $gpuPoints = 28; // High-End
                $gpuTier = sprintf('High-End (%d)', $wildLifeScore);
            } elseif ($wildLifeScore >= 3500) {
                $gpuPoints = 24; // Upper Mid-tier
                $gpuTier = sprintf('Upper Mid-tier (%d)', $wildLifeScore);
            } elseif ($wildLifeScore >= 3000) {
                $gpuPoints = 20; // Mid-tier+
                $gpuTier = sprintf('Mid-tier+ (%d)', $wildLifeScore);
            } elseif ($wildLifeScore >= 2000) {
                $gpuPoints = 15; // Mid-tier
                $gpuTier = sprintf('Mid-tier (%d)', $wildLifeScore);
            } elseif ($wildLifeScore >= 1000) {
                $gpuPoints = 10; // Entry-level
                $gpuTier = sprintf('Entry-level (%d)', $wildLifeScore);
            } else {
                $gpuPoints = 5; // Low-end
                $gpuTier = sprintf('Low-end (%d)', $wildLifeScore);
            }
        }
        
        $addPoints($socScore, $socDetails, 'GPU Tier', $gpuPoints, $gpuTier);

        // CPU Power (25 pts) - Normalized Formula
        $cpuPoints = 0;
        if ($this->benchmarks) {
            $maxMulti = 13000;
            $maxSingle = 4000;

            $normMulti = min(($this->benchmarks->geekbench_multi / $maxMulti), 1);
            $normSingle = min(($this->benchmarks->geekbench_single / $maxSingle), 1);
            
            $percentage = ($normMulti * 0.60) + ($normSingle * 0.40);
            $cpuPoints = $percentage * 25;
            
            $addPoints($socScore, $socDetails, 'CPU Power', round($cpuPoints, 1), 
                sprintf('GB Multi: %d, Single: %d', $this->benchmarks->geekbench_multi, $this->benchmarks->geekbench_single));
        } else {
            $addPoints($socScore, $socDetails, 'CPU Power', 0, 'No benchmark data');
        }
        
        $breakdown['soc_gpu'] = ['score' => round($socScore, 1), 'max' => 70, 'details' => $socDetails];
        $total += $socScore;

        // 2. Sustained Performance & Cooling (50 pts)
        $sustainedScore = 0;
        $sustainedDetails = [];
        
        // Thermal Stability (30 pts)
        if ($this->benchmarks && $this->benchmarks->dmark_wild_life_stress_stability) {
             $stability = $this->benchmarks->dmark_wild_life_stress_stability;
             $stabilityPoints = 0;
             
             if ($stability <= 50) {
                 $stabilityPoints = 5;
                 $addPoints($sustainedScore, $sustainedDetails, 'Thermal Stability', 5, sprintf('%d%% (Poor)', $stability));
             } else {
                 $stabilityPoints = 5 + (($stability - 50) * 0.5);
                 $tier = $stability >= 90 ? 'Excellent' : ($stability >= 70 ? 'Good' : 'Fair');
                 $addPoints($sustainedScore, $sustainedDetails, 'Thermal Stability', round($stabilityPoints, 1), sprintf('%d%% (%s)', $stability, $tier));
             }
        } else {
            $addPoints($sustainedScore, $sustainedDetails, 'Thermal Stability', 0, 'No stability data');
        }

        // Cooling Hardware (20 pts) - Use actual data from database
        $coolingType = $this->body->cooling_type ?? null;
        $coolingPoints = 0; // Default to 0 if not specified
        $coolingDisplay = $coolingType ?? 'Not Specified';
        
        if ($coolingType) {
            $coolingLower = strtolower($coolingType);
            if (str_contains($coolingLower, 'active fan')) {
                $coolingPoints = 20;
            } elseif (str_contains($coolingLower, 'vapor chamber')) {
                $coolingPoints = 15;
            } elseif (str_contains($coolingLower, 'graphite')) {
                $coolingPoints = 5;
            }
        }
        
        $addPoints($sustainedScore, $sustainedDetails, 'Cooling Hardware', $coolingPoints, $coolingDisplay);
        
        $breakdown['sustained'] = ['score' => round($sustainedScore, 1), 'max' => 50, 'details' => $sustainedDetails];
        $total += $sustainedScore;

        // 3. Gaming Display (40 pts)
        $displayScore = 0;
        $displayDetails = [];
        $name = strtolower($this->name); // Used for heuristics
        $displaySpecs = strtolower($this->body->display_type ?? '');
        $displayFeatures = strtolower($this->body->display_features ?? '');
        $combinedDisplay = $displaySpecs . ' ' . $displayFeatures; // Check both fields
        
        // Refresh Rate (10 pts) - Parse from either field
        $refreshPoints = 0;
        $refreshRate = '60Hz';
        
        if (preg_match('/(\d+)\s*hz/i', $combinedDisplay, $matches)) {
            $hz = intval($matches[1]);
            if ($hz >= 165) {
                $refreshPoints = 10;
                $refreshRate = $hz . 'Hz';
            } elseif ($hz >= 144) {
                $refreshPoints = 8;
                $refreshRate = $hz . 'Hz';
            } elseif ($hz >= 120) {
                $refreshPoints = 6;
                $refreshRate = $hz . 'Hz';
            } elseif ($hz >= 90) {
                $refreshPoints = 3;
                $refreshRate = $hz . 'Hz';
            }
        }
        $addPoints($displayScore, $displayDetails, 'Refresh Rate', $refreshPoints, $refreshRate);

        // Touch Sampling (10 pts) - Check actual field first, then heuristics
        $touchRate = 240; // Default
        
        // Try to parse from touch_sampling_rate field
        $touchField = $this->body->touch_sampling_rate ?? '';
        if (preg_match('/(\d+)\s*hz/i', $touchField, $matches)) {
            $touchRate = intval($matches[1]);
        }
        // Fallback heuristics if not found
        elseif (str_contains($name, 'rog') || str_contains($name, 'redmagic')) {
            $touchRate = 1000;
        } elseif (str_contains($name, 'iqoo') || str_contains($name, 'gt') || str_contains($name, 'ultra')) {
            $touchRate = 480;
        }

        $touchPoints = 4; // Default
        if ($touchRate >= 1000) $touchPoints = 10;
        elseif ($touchRate >= 720) $touchPoints = 7;
        
        $addPoints($displayScore, $displayDetails, 'Touch Sampling', $touchPoints, sprintf('%dHz', $touchRate));

        // Brightness (10 pts) - Parse from combined fields
        $nits = 0;
        
        // Try multiple patterns: "4500 nits", "4500nits", "peak brightness"
        if (preg_match('/(\d+)\s*nits/i', $combinedDisplay, $matches)) {
            $nits = intval($matches[1]);
        }
        
        // Fallback heuristics for recent flagships if not found
        if ($nits == 0) {
             if (str_contains($name, '15') || str_contains($name, '2025') || str_contains($name, '2026')) {
                 $nits = 4500;
             } elseif (str_contains($name, '14') || str_contains($name, '24')) {
                 $nits = 2500;
             }
        }

        $brightnessPoints = 0;
        if ($nits > 3000) $brightnessPoints = 10;
        elseif ($nits >= 2000) $brightnessPoints = 6;
        
        $addPoints($displayScore, $displayDetails, 'Brightness', $brightnessPoints, sprintf('%d nits', $nits));

        // PWM / Eye Comfort (10 pts) - Check pwm_dimming field and combined display
        $pwm = 0;
        $pwmField = strtolower($this->body->pwm_dimming ?? '');
        $pwmCombined = $pwmField . ' ' . $combinedDisplay;
        
        // Parse PWM frequency
        if (preg_match('/(\d+)\s*hz\s*pwm/i', $pwmCombined, $matches)) {
            $pwm = intval($matches[1]);
        } elseif (preg_match('/pwm[:\s]*(\d+)\s*hz/i', $pwmCombined, $matches)) {
            $pwm = intval($matches[1]);
        }
        
        // Fallback heuristics for known brands
        if ($pwm == 0 && (str_contains($name, 'honor') || str_contains($name, 'iqoo') || str_contains($name, 'oneplus') || str_contains($name, 'poco'))) {
            $pwm = 2160; 
        }

        $pwmPoints = 0;
        if ($pwm >= 2160) $pwmPoints = 10;
        elseif ($pwm >= 1440) $pwmPoints = 6;
        
        $addPoints($displayScore, $displayDetails, 'Eye Comfort (PWM)', $pwmPoints, sprintf('%dHz', $pwm));

        $breakdown['display'] = ['score' => round($displayScore, 1), 'max' => 40, 'details' => $displayDetails];
        $total += $displayScore;

        // 4. Memory & Storage (25 pts)
        $memoryScore = 0;
        $memoryDetails = [];
        
        // Storage Type (15 pts max)
        $storageType = strtolower($this->platform->storage_type ?? '');
        $storagePoints = 0;
        $storageLabel = 'Unknown';
        if (str_contains($storageType, 'ufs 4')) {
            $storagePoints = 15;
            $storageLabel = 'UFS 4.0';
        } elseif (str_contains($storageType, 'ufs 3.1')) {
            $storagePoints = 10;
            $storageLabel = 'UFS 3.1';
        } elseif (str_contains($storageType, 'ufs 3')) {
            $storagePoints = 5;
            $storageLabel = 'UFS 3.0';
        }
        $addPoints($memoryScore, $memoryDetails, 'Storage Type', $storagePoints, $storageLabel);
        
        // RAM Capacity (10 pts max)
        $ram = strtolower($this->platform->ram ?? '');
        $ramPoints = 0;
        $ramLabel = 'Unknown';
        if (str_contains($ram, '16gb') || str_contains($ram, '18gb') || str_contains($ram, '24gb')) {
            $ramPoints = 10;
            $ramLabel = preg_match('/(\d+gb)/i', $ram, $m) ? $m[1] : '16GB+';
        } elseif (str_contains($ram, '12gb')) {
            $ramPoints = 7;
            $ramLabel = '12GB';
        } elseif (str_contains($ram, '8gb')) {
            $ramPoints = 4;
            $ramLabel = '8GB';
        }
        $addPoints($memoryScore, $memoryDetails, 'RAM Capacity', $ramPoints, $ramLabel);
        
        $breakdown['memory'] = ['score' => round($memoryScore, 1), 'max' => 25, 'details' => $memoryDetails];
        $total += $memoryScore;

        // 5. Battery & Charging (25 pts)
        $batteryScore = 0;
        $batteryDetails = [];
        
        // Battery Capacity (10 pts max)
        $batteryType = $this->battery->battery_type ?? '';
        $capacityPoints = 0;
        $capacityLabel = '0 mAh';
        if (preg_match('/(\d+)\s*mah/i', $batteryType, $matches)) {
            $mah = intval($matches[1]);
            $capacityLabel = $mah . ' mAh';
            if ($mah >= 6000) $capacityPoints = 10;
            elseif ($mah >= 5000) $capacityPoints = 7;
            elseif ($mah >= 4500) $capacityPoints = 4;
        }
        $addPoints($batteryScore, $batteryDetails, 'Battery Capacity', $capacityPoints, $capacityLabel);
        
        // Fast Charging (15 pts max)
        $charging = $this->battery->charging_wired ?? '';
        $chargingPoints = 0;
        $chargingLabel = '0W';
        if (preg_match('/(\d+)\s*w/i', $charging, $matches)) {
            $watts = intval($matches[1]);
            $chargingLabel = $watts . 'W';
            if ($watts >= 100) $chargingPoints = 15;
            elseif ($watts >= 80) $chargingPoints = 12;
            elseif ($watts >= 65) $chargingPoints = 8;
            elseif ($watts >= 45) $chargingPoints = 5;
        }
        $addPoints($batteryScore, $batteryDetails, 'Fast Charging', $chargingPoints, $chargingLabel);
        
        $breakdown['battery'] = ['score' => round($batteryScore, 1), 'max' => 25, 'details' => $batteryDetails];
        $total += $batteryScore;

        // 6. Gaming Software (30 pts)
        $softwareScore = 0;
        $softwareDetails = [];
        
        // Dedicated Game Mode (20 pts max)
        $gameModePoints = 10;
        $gameModeLabel = 'Standard';
        if (str_contains($name, 'rog') || str_contains($name, 'redmagic')) {
            $gameModePoints = 20;
            $gameModeLabel = 'Gaming Phone';
        } elseif (str_contains($name, 'iqoo') || str_contains($name, 'oneplus') || str_contains($name, 'poco') || str_contains($name, 'gt')) {
            $gameModePoints = 15;
            $gameModeLabel = 'Performance Focused';
        }
        $addPoints($softwareScore, $softwareDetails, 'Game Mode', $gameModePoints, $gameModeLabel);
        
        // OS Optimization (10 pts max)
        $osPoints = 5;
        $osLabel = 'Moderate';
        if (str_contains($name, 'nothing') || str_contains($name, 'oneplus')) {
            $osPoints = 10;
            $osLabel = 'Near-AOSP';
        }
        $addPoints($softwareScore, $softwareDetails, 'OS Optimization', $osPoints, $osLabel);
        
        $breakdown['software'] = ['score' => round($softwareScore, 1), 'max' => 30, 'details' => $softwareDetails];
        $total += $softwareScore;

        // 7. Connectivity & Latency (20 pts)
        $connectivityScore = 0;
        $connectivityDetails = [];
        
        // Wi-Fi (10 pts max)
        $wlan = strtolower($this->connectivity->wlan ?? '');
        $wifiPoints = 0;
        $wifiLabel = 'Unknown';
        
        // Check for Wi-Fi 7 (802.11be or ".../7")
        if (str_contains($wlan, 'wi-fi 7') || str_contains($wlan, '802.11be') || preg_match('/[\/,]7(?![0-9])/', $wlan)) {
            $wifiPoints = 10;
            $wifiLabel = 'Wi-Fi 7';
        } 
        // Check for Wi-Fi 6E (802.11ax extended or ".../6e")
        elseif (str_contains($wlan, 'wi-fi 6e') || str_contains($wlan, '6e') || preg_match('/[\/,]6e/', $wlan)) {
            $wifiPoints = 7;
            $wifiLabel = 'Wi-Fi 6E';
        } 
        // Check for Wi-Fi 6 (802.11ax or ".../6")
        elseif (str_contains($wlan, 'wi-fi 6') || str_contains($wlan, '802.11ax') || preg_match('/[\/,]6(?![e0-9])/', $wlan)) {
            $wifiPoints = 5;
            $wifiLabel = 'Wi-Fi 6';
        }
        $addPoints($connectivityScore, $connectivityDetails, 'Wi-Fi', $wifiPoints, $wifiLabel);
        
        // Bluetooth (5 pts max)
        $bluetooth = $this->connectivity->bluetooth ?? '';
        $btPoints = 0;
        $btLabel = 'Unknown';
        
        // Check for Bluetooth 6.x
        if (preg_match('/6\.\d/', $bluetooth) || str_contains($bluetooth, '6.0')) {
            $btPoints = 5;
            $btLabel = 'BT 6.0';
        }
        // Check for Bluetooth 5.4/5.5
        elseif (preg_match('/5\.[45]/', $bluetooth) || str_contains($bluetooth, '5.4') || str_contains($bluetooth, '5.5')) {
            $btPoints = 4;
            $btLabel = 'BT 5.4';
        }
        // Check for Bluetooth 5.3
        elseif (preg_match('/5\.3/', $bluetooth) || str_contains($bluetooth, '5.3')) {
            $btPoints = 3;
            $btLabel = 'BT 5.3';
        }
        // Check for Bluetooth 5.2 and below
        elseif (preg_match('/5\.[0-2]/', $bluetooth)) {
            $btPoints = 2;
            $btLabel = 'BT 5.0+';
        }
        $addPoints($connectivityScore, $connectivityDetails, 'Bluetooth', $btPoints, $btLabel);
        
        // 5G (5 pts)
        $network = strtolower($this->connectivity->network_bands ?? '');
        $fiveGPoints = str_contains($network, '5g') ? 5 : 0;
        $fiveGLabel = str_contains($network, '5g') ? 'Yes' : 'No';
        $addPoints($connectivityScore, $connectivityDetails, '5G Support', $fiveGPoints, $fiveGLabel);
        
        $breakdown['connectivity'] = ['score' => round($connectivityScore, 1), 'max' => 20, 'details' => $connectivityDetails];
        $total += $connectivityScore;

        // 8. Audio & Haptics (10 pts)
        $audioScore = 0;
        $audioDetails = [];
        
        // Stereo Speakers (5 pts)
        $loudspeaker = strtolower($this->connectivity->loudspeaker ?? '');
        $speakerPoints = str_contains($loudspeaker, 'stereo') ? 5 : 0;
        $speakerLabel = str_contains($loudspeaker, 'stereo') ? 'Stereo' : 'Mono';
        $addPoints($audioScore, $audioDetails, 'Speakers', $speakerPoints, $speakerLabel);
        
        // Haptics (5 pts max)
        $hapticsPoints = 3;
        $hapticsLabel = 'Standard';
        if (str_contains($name, 'rog') || str_contains($name, 'redmagic') || str_contains($name, 'iqoo')) {
            $hapticsPoints = 5;
            $hapticsLabel = 'Gaming-Grade';
        }
        $addPoints($audioScore, $audioDetails, 'Haptics', $hapticsPoints, $hapticsLabel);
        
        $breakdown['audio'] = ['score' => round($audioScore, 1), 'max' => 10, 'details' => $audioDetails];
        $total += $audioScore;
        
        // 9. Emulator & Developer Advantage (30 pts)
        $emuScore = 0;
        $emuDetails = [];
        
        // GPU Driver & Emulation Support (20 pts)
        $gpuEmuPoints = 10; // Default
        $gpuEmuTier = 'Standard';
        
        if (str_contains($chipset, 'snapdragon')) {
             if (str_contains($chipset, 'elite')) {
                 $gpuEmuPoints = 20;
                 $gpuEmuTier = 'Adreno Elite + Turnip';
             } elseif (str_contains($chipset, 'gen 3') || str_contains($chipset, 'gen 2')) {
                 $gpuEmuPoints = 16;
                 $gpuEmuTier = 'Adreno 7xx';
             }
        } elseif (str_contains($chipset, 'dimensity')) {
             if (str_contains($chipset, '9400') || str_contains($chipset, '9500') || str_contains($chipset, '9300')) {
                 $gpuEmuPoints = 14;
                 $gpuEmuTier = 'Immortalis G925/G720';
             }
        }
        
        $addPoints($emuScore, $emuDetails, 'GPU Emulation', $gpuEmuPoints, $gpuEmuTier);

        // Bootloader / Root / ROM (10 pts)
        $romPoints = 0;
        $romSupport = 'Locked';
        
        if (str_contains($this->brand, 'OnePlus') || str_contains($this->brand, 'Xiaomi') || str_contains($this->brand, 'Nothing') || str_contains($this->brand, 'Google')) {
            $romPoints = 10;
            $romSupport = 'Unlockable + ROM Support';
        } elseif (str_contains($name, 'rog')) {
            $romPoints = 5;
            $romSupport = 'Unlockable';
        }
        
        $addPoints($emuScore, $emuDetails, 'Bootloader/ROM', $romPoints, $romSupport);
        
        $breakdown['emulator'] = ['score' => round($emuScore, 1), 'max' => 30, 'details' => $emuDetails];
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
