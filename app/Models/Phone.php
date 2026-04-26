<?php

namespace App\Models;

use App\Repositories\CommentRepository;
use App\Services\SEO\SEOData;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class Phone extends FirestoreModel
{
    protected array $casts = [
        'release_date' => 'date',
        'price' => 'decimal:2',
        'announced_date' => 'date',
        'overall_score' => 'decimal:1',
        'ueps_score' => 'decimal:1',
        'value_score' => 'decimal:2',
        'expert_score' => 'decimal:2',
        'gpx_score' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'gpx_details' => 'array',
        'cms_score' => 'decimal:1',
        'cms_details' => 'array',
    ];

    protected array $appends = [
        'value_score',
        'ueps_details',
        'gpx_details_append',
    ];

    public function getGpxDetailsAppendAttribute(): array
    {
        return $this->gpx_details ?? [];
    }

    public function body(): ?object
    {
        $data = $this->attributes['body'] ?? [];

        return empty($data) ? null : (object) $data;
    }

    public function platform(): ?object
    {
        $data = $this->attributes['platform'] ?? [];

        return empty($data) ? null : (object) $data;
    }

    public function camera(): ?object
    {
        $data = $this->attributes['camera'] ?? [];

        return empty($data) ? null : (object) $data;
    }

    public function connectivity(): ?object
    {
        $data = $this->attributes['connectivity'] ?? [];

        return empty($data) ? null : (object) $data;
    }

    public function battery(): ?object
    {
        $data = $this->attributes['battery'] ?? [];

        return empty($data) ? null : (object) $data;
    }

    public function benchmarks(): ?object
    {
        $data = $this->attributes['benchmarks'] ?? [];

        return empty($data) ? null : (object) $data;
    }

    public function comments(): Collection
    {
        return collect(app(CommentRepository::class)->forPhone($this->attributes['id'] ?? ''));
    }

    /**
     * Calculate Endurance Score (Adaptive)
     */
    public function calculateEnduranceScore(): float
    {
        $mah = 0;
        $battery = $this->battery;
        if ($battery && $battery->battery_type) {
            if (preg_match('/(\d{3,5})\s*mAh/i', $battery->battery_type, $matches)) {
                $mah = intval($matches[1]);
            }
        }

        $activeUseHours = 0;
        $benchmarks = $this->benchmarks;
        if ($benchmarks && ($benchmarks->battery_active_use_score ?? null)) {
            $rawScore = $benchmarks->battery_active_use_score;
            if (preg_match('/(\d+):(\d+)h/i', $rawScore, $matches)) {
                $activeUseHours = intval($matches[1]) + (intval($matches[2]) / 60);
            } elseif (preg_match('/(\d+(\.\d+)?)\s*h/i', $rawScore, $matches)) {
                $activeUseHours = floatval($matches[1]);
            }
        }

        $enduranceHours = 0;
        if ($benchmarks && ($benchmarks->battery_endurance_hours ?? null)) {
            $enduranceHours = floatval($benchmarks->battery_endurance_hours);
        }

        if ($mah === 0 && $enduranceHours === 0 && $activeUseHours === 0) {
            return 0;
        }

        $capacityScore = $mah / 100;

        if ($activeUseHours > 0) {
            $enduranceScore = $activeUseHours * 3.5;
        } elseif ($enduranceHours > 30) {
            $enduranceScore = $enduranceHours * 0.45;
        } else {
            $enduranceScore = $enduranceHours * 3.5;
        }

        $totalScore = $capacityScore + $enduranceScore;

        return round($totalScore, 1);
    }

    public function getValueScoreAttribute(): float
    {
        if (! $this->price || $this->price == 0) {
            return 0;
        }

        $rating = $this->calculateExpertRating();

        if ($rating == 0) {
            return 0;
        }

        $score = ($rating / $this->price) * 10000;

        return round($score, 1);
    }

    /**
     * Calculate the raw weighted rating based on all metrics.
     */
    public function calculateExpertRating(): float
    {
        $normUeps = ($this->ueps_score ?? 0) / 2.55;
        $normFpi = $this->overall_score ?? 0;
        $normGpx = ($this->gpx_score ?? 0) / 3.0;
        $normCms = ($this->cms_score ?? 0) / 13.3;

        $endurance = $this->endurance_score ?? $this->calculateEnduranceScore();
        $normEndurance = $endurance / 1.6;

        $rating = ($normUeps * 0.25) + ($normFpi * 0.25) + ($normCms * 0.25) + ($normGpx * 0.15) + ($normEndurance * 0.10);

        return $rating;
    }

    public function calculateFPI(): array|int
    {
        $benchmarks = $this->benchmarks;
        if (! $benchmarks) {
            return 0;
        }

        $maxScores = Cache::remember('benchmark_max_scores', 3600, function () {
            $repo = app(\App\Repositories\PhoneRepository::class);
            $maxAntutu = 0;
            $maxGbMulti = 0;
            $maxGbSingle = 0;
            $max3dmark = 0;

            foreach ($repo->all() as $phone) {
                $b = $phone->benchmarks;
                if ($b) {
                    $maxAntutu = max($maxAntutu, $b->antutu_score ?? 0);
                    $maxGbMulti = max($maxGbMulti, $b->geekbench_multi ?? 0);
                    $maxGbSingle = max($maxGbSingle, $b->geekbench_single ?? 0);
                    $max3dmark = max($max3dmark, $b->dmark_wild_life_extreme ?? 0);
                }
            }

            return [
                'antutu' => $maxAntutu ?: 4000000,
                'geekbench_multi' => $maxGbMulti ?: 12000,
                'geekbench_single' => $maxGbSingle ?: 3500,
                '3dmark' => $max3dmark ?: 8000,
            ];
        });

        $normAntutu = (($benchmarks->antutu_score ?? 0) / $maxScores['antutu']) * 100;
        $normGeekbenchMulti = (($benchmarks->geekbench_multi ?? 0) / $maxScores['geekbench_multi']) * 100;
        $normGeekbenchSingle = (($benchmarks->geekbench_single ?? 0) / $maxScores['geekbench_single']) * 100;
        $norm3DMark = (($benchmarks->dmark_wild_life_extreme ?? 0) / $maxScores['3dmark']) * 100;

        $weightedAntutu = $normAntutu * 0.40;
        $weightedGeekbenchMulti = $normGeekbenchMulti * 0.25;
        $weightedGeekbenchSingle = $normGeekbenchSingle * 0.15;
        $weighted3DMark = $norm3DMark * 0.20;

        $fpi = $weightedAntutu + $weightedGeekbenchMulti + $weightedGeekbenchSingle + $weighted3DMark;

        return [
            'total' => round($fpi, 1),
            'breakdown' => [
                'antutu' => round($weightedAntutu, 1),
                'geekbench_multi' => round($weightedGeekbenchMulti, 1),
                'geekbench_single' => round($weightedGeekbenchSingle, 1),
                '3dmark' => round($weighted3DMark, 1),
            ],
            'max_possible' => 100,
        ];
    }

    /**
     * Recalculate and save all scores for this phone.
     */
    public function updateScores(): void
    {
        $ueps = \App\Services\UepsScoringService::calculate($this);
        $this->attributes['ueps_score'] = $ueps['total_score'];

        $fpi = $this->calculateFPI();
        if (is_array($fpi)) {
            $this->attributes['overall_score'] = $fpi['total'];
        }

        $gpx = $this->calculateGPX();
        $this->attributes['gpx_score'] = $gpx['score'];
        $this->attributes['gpx_details'] = $gpx['details'];

        $cms = \App\Services\CmsScoringService::calculate($this);
        $this->attributes['cms_score'] = $cms['total_score'];
        $this->attributes['cms_details'] = $cms['breakdown'];

        $this->attributes['endurance_score'] = $this->calculateEnduranceScore();
        $this->attributes['expert_score'] = round($this->calculateExpertRating(), 2);

        if (($this->attributes['price'] ?? 0) > 0) {
            $this->attributes['value_score'] = $this->getValueScoreAttribute();
        } else {
            $this->attributes['value_score'] = 0;
        }
    }

    public function getUepsDetailsAttribute(): array
    {
        return \App\Services\UepsScoringService::calculate($this);
    }

    /**
     * Calculate GPX-300 Gaming Score
     */
    public function calculateGPX(): array
    {
        $breakdown = [];
        $total = 0;

        $addPoints = function (&$categoryScore, &$categoryDetails, $criterion, $points, $reason) {
            $categoryScore += $points;
            $categoryDetails[] = ['criterion' => $criterion, 'points' => $points, 'reason' => $reason];
        };

        $socScore = 0;
        $socDetails = [];
        $platform = $this->platform;
        $chipset = strtolower($platform->chipset ?? '');
        $gpu = strtolower($platform->gpu ?? '');

        $gpuPoints = 0;
        $gpuTier = 'No benchmark data';
        $benchmarks = $this->benchmarks;

        if ($benchmarks && ($benchmarks->dmark_wild_life_extreme ?? null)) {
            $wildLifeScore = $benchmarks->dmark_wild_life_extreme;

            if ($wildLifeScore >= 7000) {
                $gpuPoints = 45;
                $gpuTier = sprintf('Ultra Elite (%d)', $wildLifeScore);
            } elseif ($wildLifeScore >= 6000) {
                $gpuPoints = 40;
                $gpuTier = sprintf('Flagship+ (%d)', $wildLifeScore);
            } elseif ($wildLifeScore >= 5000) {
                $gpuPoints = 35;
                $gpuTier = sprintf('Flagship (%d)', $wildLifeScore);
            } elseif ($wildLifeScore >= 4500) {
                $gpuPoints = 32;
                $gpuTier = sprintf('Upper High-End (%d)', $wildLifeScore);
            } elseif ($wildLifeScore >= 4000) {
                $gpuPoints = 28;
                $gpuTier = sprintf('High-End (%d)', $wildLifeScore);
            } elseif ($wildLifeScore >= 3500) {
                $gpuPoints = 24;
                $gpuTier = sprintf('Upper Mid-tier (%d)', $wildLifeScore);
            } elseif ($wildLifeScore >= 3000) {
                $gpuPoints = 20;
                $gpuTier = sprintf('Mid-tier+ (%d)', $wildLifeScore);
            } elseif ($wildLifeScore >= 2000) {
                $gpuPoints = 15;
                $gpuTier = sprintf('Mid-tier (%d)', $wildLifeScore);
            } elseif ($wildLifeScore >= 1000) {
                $gpuPoints = 10;
                $gpuTier = sprintf('Entry-level (%d)', $wildLifeScore);
            } else {
                $gpuPoints = 5;
                $gpuTier = sprintf('Low-end (%d)', $wildLifeScore);
            }
        }

        $addPoints($socScore, $socDetails, 'GPU Tier', $gpuPoints, $gpuTier);

        $cpuPoints = 0;
        if ($benchmarks) {
            $maxMulti = 13000;
            $maxSingle = 4000;

            $normMulti = min((($benchmarks->geekbench_multi ?? 0) / $maxMulti), 1);
            $normSingle = min((($benchmarks->geekbench_single ?? 0) / $maxSingle), 1);

            $percentage = ($normMulti * 0.60) + ($normSingle * 0.40);
            $cpuPoints = $percentage * 25;

            $addPoints($socScore, $socDetails, 'CPU Power', round($cpuPoints, 1),
                sprintf('GB Multi: %d, Single: %d', $benchmarks->geekbench_multi ?? 0, $benchmarks->geekbench_single ?? 0));
        } else {
            $addPoints($socScore, $socDetails, 'CPU Power', 0, 'No benchmark data');
        }

        $breakdown['soc_gpu'] = ['score' => round($socScore, 1), 'max' => 70, 'details' => $socDetails];
        $total += $socScore;

        $sustainedScore = 0;
        $sustainedDetails = [];

        if ($benchmarks && ($benchmarks->dmark_wild_life_stress_stability ?? null)) {
            $stability = $benchmarks->dmark_wild_life_stress_stability;
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

        $body = $this->body;
        $coolingType = $body->cooling_type ?? null;
        $coolingPoints = 0;
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

        $displayScore = 0;
        $displayDetails = [];
        $name = strtolower($this->name);
        $displaySpecs = strtolower($body->display_type ?? '');
        $displayFeatures = strtolower($body->display_features ?? '');
        $combinedDisplay = $displaySpecs.' '.$displayFeatures;

        $refreshPoints = 0;
        $refreshRate = '60Hz';

        if (preg_match('/(\d+)\s*hz/i', $combinedDisplay, $matches)) {
            $hz = intval($matches[1]);
            if ($hz >= 165) {
                $refreshPoints = 10;
                $refreshRate = $hz.'Hz';
            } elseif ($hz >= 144) {
                $refreshPoints = 8;
                $refreshRate = $hz.'Hz';
            } elseif ($hz >= 120) {
                $refreshPoints = 6;
                $refreshRate = $hz.'Hz';
            } elseif ($hz >= 90) {
                $refreshPoints = 3;
                $refreshRate = $hz.'Hz';
            }
        }
        $addPoints($displayScore, $displayDetails, 'Refresh Rate', $refreshPoints, $refreshRate);

        $touchRate = 240;
        $touchField = $body->touch_sampling_rate ?? '';
        if (preg_match('/(\d+)\s*hz/i', $touchField, $matches)) {
            $touchRate = intval($matches[1]);
        } elseif (str_contains($name, 'rog') || str_contains($name, 'redmagic')) {
            $touchRate = 1000;
        } elseif (str_contains($name, 'iqoo') || str_contains($name, 'gt') || str_contains($name, 'ultra')) {
            $touchRate = 480;
        }

        $touchPoints = 4;
        if ($touchRate >= 1000) {
            $touchPoints = 10;
        } elseif ($touchRate >= 720) {
            $touchPoints = 7;
        }

        $addPoints($displayScore, $displayDetails, 'Touch Sampling', $touchPoints, sprintf('%dHz', $touchRate));

        $nits = 0;
        if (preg_match('/(\d+)\s*nits/i', $combinedDisplay, $matches)) {
            $nits = intval($matches[1]);
        }

        if ($nits == 0) {
            if (str_contains($name, '15') || str_contains($name, '2025') || str_contains($name, '2026')) {
                $nits = 4500;
            } elseif (str_contains($name, '14') || str_contains($name, '24')) {
                $nits = 2500;
            }
        }

        $brightnessPoints = 0;
        if ($nits > 3000) {
            $brightnessPoints = 10;
        } elseif ($nits >= 2000) {
            $brightnessPoints = 6;
        }

        $addPoints($displayScore, $displayDetails, 'Brightness', $brightnessPoints, sprintf('%d nits', $nits));

        $pwm = 0;
        $pwmField = strtolower($body->pwm_dimming ?? '');
        $pwmCombined = $pwmField.' '.$combinedDisplay;

        if (preg_match('/(\d+)\s*hz\s*pwm/i', $pwmCombined, $matches)) {
            $pwm = intval($matches[1]);
        } elseif (preg_match('/pwm[:\s]*(\d+)\s*hz/i', $pwmCombined, $matches)) {
            $pwm = intval($matches[1]);
        }

        if ($pwm == 0 && (str_contains($name, 'honor') || str_contains($name, 'iqoo') || str_contains($name, 'oneplus') || str_contains($name, 'poco'))) {
            $pwm = 2160;
        }

        $pwmPoints = 0;
        if ($pwm >= 2160) {
            $pwmPoints = 10;
        } elseif ($pwm >= 1440) {
            $pwmPoints = 6;
        }

        $addPoints($displayScore, $displayDetails, 'Eye Comfort (PWM)', $pwmPoints, sprintf('%dHz', $pwm));

        $breakdown['display'] = ['score' => round($displayScore, 1), 'max' => 40, 'details' => $displayDetails];
        $total += $displayScore;

        $memoryScore = 0;
        $memoryDetails = [];

        $storageType = strtolower($platform->storage_type ?? '');
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

        $ram = strtolower($platform->ram ?? '');
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

        $batteryScore = 0;
        $batteryDetails = [];
        $batteryData = $this->battery;

        $batteryType = $batteryData->battery_type ?? '';
        $capacityPoints = 0;
        $capacityLabel = '0 mAh';
        if (preg_match('/(\d+)\s*mah/i', $batteryType, $matches)) {
            $mah = intval($matches[1]);
            $capacityLabel = $mah.' mAh';
            if ($mah >= 6000) {
                $capacityPoints = 10;
            } elseif ($mah >= 5000) {
                $capacityPoints = 7;
            } elseif ($mah >= 4500) {
                $capacityPoints = 4;
            }
        }
        $addPoints($batteryScore, $batteryDetails, 'Battery Capacity', $capacityPoints, $capacityLabel);

        $charging = $batteryData->charging_wired ?? '';
        $chargingPoints = 0;
        $chargingLabel = '0W';
        if (preg_match('/(\d+)\s*w/i', $charging, $matches)) {
            $watts = intval($matches[1]);
            $chargingLabel = $watts.'W';
            if ($watts >= 100) {
                $chargingPoints = 15;
            } elseif ($watts >= 80) {
                $chargingPoints = 12;
            } elseif ($watts >= 65) {
                $chargingPoints = 8;
            } elseif ($watts >= 45) {
                $chargingPoints = 5;
            }
        }
        $addPoints($batteryScore, $batteryDetails, 'Fast Charging', $chargingPoints, $chargingLabel);

        $breakdown['battery'] = ['score' => round($batteryScore, 1), 'max' => 25, 'details' => $batteryDetails];
        $total += $batteryScore;

        $softwareScore = 0;
        $softwareDetails = [];

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

        $osPoints = 5;
        $osLabel = 'Moderate';
        if (str_contains($name, 'nothing') || str_contains($name, 'oneplus')) {
            $osPoints = 10;
            $osLabel = 'Near-AOSP';
        }
        $addPoints($softwareScore, $softwareDetails, 'OS Optimization', $osPoints, $osLabel);

        $breakdown['software'] = ['score' => round($softwareScore, 1), 'max' => 30, 'details' => $softwareDetails];
        $total += $softwareScore;

        $connectivityScore = 0;
        $connectivityDetails = [];
        $conn = $this->connectivity;

        $wlan = strtolower($conn->wlan ?? '');
        $wifiPoints = 0;
        $wifiLabel = 'Unknown';

        if (str_contains($wlan, 'wi-fi 7') || str_contains($wlan, '802.11be') || preg_match('/[\/,]7(?![0-9])/', $wlan)) {
            $wifiPoints = 10;
            $wifiLabel = 'Wi-Fi 7';
        } elseif (str_contains($wlan, 'wi-fi 6e') || str_contains($wlan, '6e') || preg_match('/[\/,]6e/', $wlan)) {
            $wifiPoints = 7;
            $wifiLabel = 'Wi-Fi 6E';
        } elseif (str_contains($wlan, 'wi-fi 6') || str_contains($wlan, '802.11ax') || preg_match('/[\/,]6(?![e0-9])/', $wlan)) {
            $wifiPoints = 5;
            $wifiLabel = 'Wi-Fi 6';
        }
        $addPoints($connectivityScore, $connectivityDetails, 'Wi-Fi', $wifiPoints, $wifiLabel);

        $bluetooth = $conn->bluetooth ?? '';
        $btPoints = 0;
        $btLabel = 'Unknown';

        if (preg_match('/6\.\d/', $bluetooth) || str_contains($bluetooth, '6.0')) {
            $btPoints = 5;
            $btLabel = 'BT 6.0';
        } elseif (preg_match('/5\.[45]/', $bluetooth) || str_contains($bluetooth, '5.4') || str_contains($bluetooth, '5.5')) {
            $btPoints = 4;
            $btLabel = 'BT 5.4';
        } elseif (preg_match('/5\.3/', $bluetooth) || str_contains($bluetooth, '5.3')) {
            $btPoints = 3;
            $btLabel = 'BT 5.3';
        } elseif (preg_match('/5\.[0-2]/', $bluetooth)) {
            $btPoints = 2;
            $btLabel = 'BT 5.0+';
        }
        $addPoints($connectivityScore, $connectivityDetails, 'Bluetooth', $btPoints, $btLabel);

        $network = strtolower($conn->network_bands ?? '');
        $fiveGPoints = str_contains($network, '5g') ? 5 : 0;
        $fiveGLabel = str_contains($network, '5g') ? 'Yes' : 'No';
        $addPoints($connectivityScore, $connectivityDetails, '5G Support', $fiveGPoints, $fiveGLabel);

        $breakdown['connectivity'] = ['score' => round($connectivityScore, 1), 'max' => 20, 'details' => $connectivityDetails];
        $total += $connectivityScore;

        $audioScore = 0;
        $audioDetails = [];

        $loudspeaker = strtolower($conn->loudspeaker ?? '');
        $speakerPoints = str_contains($loudspeaker, 'stereo') ? 5 : 0;
        $speakerLabel = str_contains($loudspeaker, 'stereo') ? 'Stereo' : 'Mono';
        $addPoints($audioScore, $audioDetails, 'Speakers', $speakerPoints, $speakerLabel);

        $hapticsPoints = 3;
        $hapticsLabel = 'Standard';
        if (str_contains($name, 'rog') || str_contains($name, 'redmagic') || str_contains($name, 'iqoo')) {
            $hapticsPoints = 5;
            $hapticsLabel = 'Gaming-Grade';
        }
        $addPoints($audioScore, $audioDetails, 'Haptics', $hapticsPoints, $hapticsLabel);

        $breakdown['audio'] = ['score' => round($audioScore, 1), 'max' => 10, 'details' => $audioDetails];
        $total += $audioScore;

        $emuScore = 0;
        $emuDetails = [];

        $gpuEmuPoints = 10;
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

        $gpm = 0;
        if ($benchmarks) {
            $maxWildLife = 8000;
            $maxAntutuGpu = 1500000;

            $normWildLife = min((($benchmarks->dmark_wild_life_extreme ?? 0) / $maxWildLife), 1);
            $antutuGpu = ($benchmarks->antutu_score ?? 0) * 0.4;
            $normAntutu = min($antutuGpu / $maxAntutuGpu, 1);

            $gpm = (($normWildLife * 0.6) + ($normAntutu * 0.4)) * 100;
        }

        $finalScore = $total + ($gpm * 0.5);
        $finalScore = min($finalScore, 300);

        return [
            'score' => round($finalScore, 2),
            'details' => $breakdown,
            'gpm' => round($gpm, 1),
        ];
    }

    public function getSEOData(): SEOData
    {
        $imageUrl = $this->image_url ?
            (str_starts_with($this->image_url, 'http') ? $this->image_url : url($this->image_url))
            : asset('assets/logo.png');

        return new SEOData(
            title: "{$this->name} Specs, Price & Reviews | PhoneFinderHub",
            description: "Detailed specifications, features, and user reviews for the {$this->brand} {$this->name}. Compare prices and find out if it's the right phone for you.",
            image: $imageUrl,
            url: route('phones.show', $this->slug ?? $this->id),
            type: 'product',
            schema: [
                '@context' => 'https://schema.org',
                '@type' => 'Product',
                'name' => $this->name,
                'image' => $imageUrl,
                'description' => "Specifications and features for {$this->name}.",
                'brand' => [
                    '@type' => 'Brand',
                    'name' => $this->brand ?? 'Unknown',
                ],
            ]
        );
    }
}
