<?php

namespace App\Services;

use App\Models\Phone;

class CmsScoringService
{
    /**
     * Calculate CMS-1330 Score
     * Returns array with 'total_score' and 'breakdown'
     */
    public static function calculate(Phone $phone)
    {
        $breakdown = [];
        $total = 0;

        // Helper to add points
        $addPoints = function (&$categoryScore, &$categoryDetails, $criterion, $points, $reason) {
            $categoryScore += $points;
            $categoryDetails[] = ['criterion' => $criterion, 'points' => $points, 'reason' => $reason];
        };

        // ==========================================
        // 1. Sensor & Optics (240 Points)
        // ==========================================
        $sensorScore = 0;
        $sensorDetails = [];

        // We need to parse per-camera specs.
        // Assuming 'main_camera_sensors', 'main_camera_apertures' strings are available.
        // Format example: "1/1.4", 1/1.56", 1/4.0""
        
        $sensors = self::parseList($phone->camera->main_camera_sensors ?? '');
        $apertures = self::parseList($phone->camera->main_camera_apertures ?? '');
        $pixelSizes = self::parseList($phone->camera->main_camera_specs ?? ''); // Extract microns from specs string if possible, or use heuristics

        // -- Sensor Size (40 pts per camera, weighted) --
        // Simplified: Score the Main camera heavily (50%), others less.
        // Actually, rulebook says "Per-camera evaluation with weighted fusion".
        // Let's score the Main Camera first as it's most important.
        
        $mainSensorSize = $sensors[0] ?? '';
        $mainSensorPoints = 0;
        
        if (self::compareSensorSize($mainSensorSize, '1/1.4')) $mainSensorPoints = 40;
        elseif (self::compareSensorSize($mainSensorSize, '1/1.5')) $mainSensorPoints = 32;
        elseif (self::compareSensorSize($mainSensorSize, '1/1.8')) $mainSensorPoints = 24;
        elseif (self::compareSensorSize($mainSensorSize, '1/2.5')) $mainSensorPoints = 16;
        else $mainSensorPoints = 8;

        $addPoints($sensorScore, $sensorDetails, 'Main Sensor Size', $mainSensorPoints, $mainSensorSize ?: 'Unknown');

        // -- Pixel Size (25 pts) --
        // Hard to parse directly from specs string reliably without regex on specific format.
        // Heuristic based on MP and sensor size or checking specs string for "µm"
        $specs = $phone->camera->main_camera_specs ?? '';
        $pixelScore = 0;
        $pixelLabel = 'Standard';
        
        if (preg_match('/(\d+\.?\d*)\s*µm/', $specs, $matches)) {
            $um = floatval($matches[1]);
            if ($um >= 1.4) { $pixelScore = 25; $pixelLabel = "{$um}µm (Large)"; }
            elseif ($um >= 1.1) { $pixelScore = 20; $pixelLabel = "{$um}µm (Good)"; }
            elseif ($um >= 0.9) { $pixelScore = 15; $pixelLabel = "{$um}µm (Average)"; }
            elseif ($um >= 0.7) { $pixelScore = 10; $pixelLabel = "{$um}µm (Small)"; }
            else { $pixelScore = 5; $pixelLabel = "{$um}µm (Tiny)"; }
        } else {
             // Fallback: Low MP usually means higher pixel size
             $mp = self::extractMP($specs);
             if ($mp <= 12) { $pixelScore = 20; $pixelLabel = "Est. Large (Low MP)"; } // e.g. iPhone 12MP
             elseif ($mp >= 200) { $pixelScore = 5; $pixelLabel = "Est. Small (200MP)"; }
             else { $pixelScore = 15; $pixelLabel = "Est. Average"; }
        }
        $addPoints($sensorScore, $sensorDetails, 'Pixel Size', $pixelScore, $pixelLabel);

        // -- Aperture (25 pts) --
        $mainAperture = $apertures[0] ?? '';
        $apertureScore = 0;
        $fVal = self::extractFNumber($mainAperture);
        
        if ($fVal > 0) {
            if ($fVal <= 1.6) $apertureScore = 25;
            elseif ($fVal <= 1.9) $apertureScore = 20;
            elseif ($fVal <= 2.3) $apertureScore = 15;
            elseif ($fVal <= 2.7) $apertureScore = 10;
            else $apertureScore = 5;
        }
        $addPoints($sensorScore, $sensorDetails, 'Aperture', $apertureScore, "f/{$fVal}");

        // -- Optics Type (10 pts) --
        // Check for specific keywords in specs/features
        $opticsScore = 4; // Standard lens default
        $opticsLabel = 'Standard Lens';
        $allSpecs = strtolower(($phone->camera->main_camera_specs ?? '') . ' ' . ($phone->camera->main_camera_features ?? ''));
        $allSpecsLow = $allSpecs; // Define early for use throughout function
        
        if (str_contains($allSpecs, 'hasselblad') || str_contains($allSpecs, 'leica') || str_contains($allSpecs, 'zeiss')) {
             $opticsScore = 10;
             $opticsLabel = 'Co-engineered / Premium';
        } elseif (str_contains($allSpecs, 'aspherical') || str_contains($allSpecs, '8p') || str_contains($allSpecs, '7p')) {
             $opticsScore = 6;
             $opticsLabel = 'Advanced Optics';
        }
        $addPoints($sensorScore, $sensorDetails, 'Optics Quality', $opticsScore, $opticsLabel);
        
        // Scale Sensor & Optics score to fit 240 max if we judged mostly on Main camera
        // (Since we simply scored main camera components, let's multiply to fill the category weight)
        // Current max: 40+25+25+10 = 100.
        // We need 240. Let's assume Secondary cameras add significant value.
        // Strategy: Base score (100) * 2.4 scaling factor is too simple.
        // Implementation: Score secondary cameras briefly.
        
        $secondaryScore = 0;
        $hasCams = count($sensors);
        if ($hasCams >= 3) $secondaryScore += 80; // Tele + UW
        elseif ($hasCams >= 2) $secondaryScore += 40; // UW only
        
        // Add Periscope bonus
        if (str_contains($allSpecs, 'periscope')) {
            $secondaryScore += 40;
            $addPoints($sensorScore, $sensorDetails, 'Periscope Telephoto', 40, 'Present');
        } else {
             $addPoints($sensorScore, $sensorDetails, 'Periscope Telephoto', 0, 'Not Present');
        }

        // Fill remaining points based on general quality (placeholder logic for now to reach ~200 for flagships)
        // Let's cap and scale the raw hardware score.
        // Current raw: ~100 (main) + ~120 (secondary) = 220. Close to 240.
        
        $addPoints($sensorScore, $sensorDetails, 'Secondary Cameras', $secondaryScore > 100 ? 100 : $secondaryScore, "$hasCams cameras total");
        
        $breakdown['sensor_optics'] = ['score' => min($sensorScore, 240), 'max' => 240, 'details' => $sensorDetails];
        $total += $breakdown['sensor_optics']['score'];

        // ==========================================
        // 2. Resolution & Binning (90 Points)
        // ==========================================
        $resScore = 0;
        $resDetails = [];
        $mp = self::extractMP($specs);
        $resPoints = 10; // Default
        $resLabel = "{$mp}MP";
        
        // Check for binning keywords
        $hasBinning = str_contains($allSpecsLow, 'quad bayer') || 
                      str_contains($allSpecsLow, 'tetra binning') || 
                      str_contains($allSpecsLow, 'pixel binning') ||
                      str_contains($allSpecsLow, '4-in-1') ||
                      str_contains($allSpecsLow, '9-in-1');
        
        if ($mp >= 200) { 
            $resPoints = 90; 
            $resLabel = "200MP (Multi-bin)"; 
        } elseif ($mp >= 108) { 
            $resPoints = 80; 
            $resLabel = "108MP" . ($hasBinning ? " (Tetra-bin)" : ""); 
        } elseif ($mp >= 50 && $mp <= 64) { 
            $resPoints = 65; 
            $resLabel = "{$mp}MP" . ($hasBinning ? " (Quad-bin)" : ""); 
        } elseif ($mp >= 48 && $mp < 50) { 
            $resPoints = 45; 
            $resLabel = "48MP" . ($hasBinning ? " (Quad-bin)" : ""); 
        } elseif ($mp >= 16) { 
            $resPoints = 25; 
            $resLabel = "16-24MP"; 
        }
        
        $addPoints($resScore, $resDetails, 'Resolution & Binning', $resPoints, $resLabel);
        
        $breakdown['resolution'] = ['score' => $resScore, 'max' => 90, 'details' => $resDetails];
        $total += $resScore;

        // ==========================================
        // 3. Focus & Stability (200 Points)
        // ==========================================
        $focusScore = 0;
        $focusDetails = [];
        
        // Autofocus (100 pts)
        $afScore = 0;
        $pdaf = strtolower($phone->camera->main_camera_pdaf ?? '');
        $afLabel = 'Contrast AF';
        
        if (str_contains($allSpecsLow, 'laser') && (str_contains($allSpecsLow, 'dual pixel') || str_contains($allSpecsLow, 'dual-pixel'))) {
            $afScore = 100;
            $afLabel = 'Dual-Pixel + Laser';
        } elseif (str_contains($allSpecsLow, 'dual pixel') || str_contains($allSpecsLow, 'dual-pixel')) {
            $afScore = 80;
            $afLabel = 'Dual-Pixel AF';
        } elseif (str_contains($allSpecsLow, 'multi-directional') || str_contains($allSpecsLow, 'all-pixel')) {
            $afScore = 60;
            $afLabel = 'Multi-PDAF';
        } elseif (str_contains($allSpecsLow, 'pdaf')) {
            $afScore = 40;
            $afLabel = 'PDAF';
        } else {
            $afScore = 20;
        }
        $addPoints($focusScore, $focusDetails, 'Autofocus', $afScore, $afLabel);
        
        // Stabilization (100 pts)
        $stabScore = 0;
        $ois = strtolower($phone->camera->main_camera_ois ?? '');
        $video = strtolower($phone->camera->main_video_capabilities ?? '');
        $stabLabel = 'None';
        
        if ((str_contains($ois, 'yes') || str_contains($allSpecsLow, 'ois')) && (str_contains($video, 'gyro-eis') || str_contains($allSpecsLow, 'gyro-eis'))) {
             $stabScore = 100;
             $stabLabel = 'OIS + Gyro-EIS';
        } elseif (str_contains($ois, 'yes') || str_contains($allSpecsLow, 'ois')) {
             $stabScore = 60; // OIS alone
             // Check standard EIS
             if (str_contains($video, 'eis')) {
                 $stabScore = 80;
                 $stabLabel = 'OIS + EIS';
             } else {
                 $stabLabel = 'OIS';
             }
        } elseif (str_contains($video, 'gyro-eis')) {
             $stabScore = 40;
             $stabLabel = 'Gyro-EIS';
        } elseif (str_contains($video, 'eis')) {
             $stabScore = 20;
             $stabLabel = 'EIS';
        } else {
             $stabScore = 10;
        }
        
        // Gimbal OIS bonus validation
        if (str_contains($allSpecsLow, 'gimbal')) {
             $stabScore = 100;
             $stabLabel = 'Gimbal OIS';
        }

        $addPoints($focusScore, $focusDetails, 'Stabilization', $stabScore, $stabLabel);

        $breakdown['focus_stability'] = ['score' => $focusScore, 'max' => 200, 'details' => $focusDetails];
        $total += $focusScore;

        // ==========================================
        // 4. Video System (200 Points)
        // ==========================================
        $videoScore = 0;
        $videoDetails = [];
        $videoCaps = strtolower($phone->camera->main_video_capabilities ?? '');
        
        $vidPoints = 20; // Default 1080p
        $vidLabel = '1080p';
        
        if (str_contains($videoCaps, '8k') && str_contains($videoCaps, '60fps')) { // Rare
            $vidPoints = 200; $vidLabel = '8K60';
        } elseif (str_contains($videoCaps, '8k')) { // 8K30 or 8K24
            $vidPoints = 170; $vidLabel = '8K30';
        } elseif (str_contains($videoCaps, '4k') && str_contains($videoCaps, '60fps')) {
            $vidPoints = 100; $vidLabel = '4K60';
        } elseif (str_contains($videoCaps, '4k')) {
            $vidPoints = 60; $vidLabel = '4K30';
        }
        
        $addPoints($videoScore, $videoDetails, 'Max Resolution', $vidPoints, $vidLabel);
        
        // Bonus for Slow Motion / HDR Video
        if (str_contains($videoCaps, '960fps') || str_contains($videoCaps, '480fps')) {
             $addPoints($videoScore, $videoDetails, 'Slow Mo', 0, 'Incr. capabilities'); // Included in tier above implicitly
        }
        
        $breakdown['video'] = ['score' => $videoScore, 'max' => 200, 'details' => $videoDetails];
        $total += $videoScore;

        // ==========================================
        // 5. Multi-Camera Fusion (200 Points)
        // ==========================================
        $fusionScore = 0;
        $fusionDetails = [];
        
        $camCount = count($sensors) ?: 1;
        
        // Fallback: Check specs string for "+" separators if sensor count seems low
        if ($camCount < 2) {
            $specsCamCount = substr_count($phone->camera->main_camera_specs ?? '', '+') + 1;
            if ($specsCamCount > $camCount) {
                $camCount = $specsCamCount;
            }
        }
        
        if ($camCount >= 4) {
             $fusionScore = 140; // Quad
             $addPoints($fusionScore, $fusionDetails, 'Camera Count', 140, 'Quad-Camera System');
        } elseif ($camCount == 3) {
             $fusionScore = 100; // Triple
             $addPoints($fusionScore, $fusionDetails, 'Camera Count', 100, 'Triple-Camera System');
        } elseif ($camCount == 2) {
             $fusionScore = 60; // Dual
             $addPoints($fusionScore, $fusionDetails, 'Camera Count', 60, 'Dual-Camera System');
        } else {
             $fusionScore = 20; // Single
             $addPoints($fusionScore, $fusionDetails, 'Camera Count', 20, 'Single Camera');
        }
        
        // AI Fusion Bonus (Heuristic based on Year/Chipset/Keywords)
        // Modern flagships get this bonus implicitly
        if ($phone->release_date && $phone->release_date->year >= 2024 && str_contains($allSpecsLow, 'ai')) {
             $fusionBonus = 40;
             $addPoints($fusionScore, $fusionDetails, 'AI/Computational Fusion', $fusionBonus, 'Advanced Processing');
        }
        
        // Max cap
        $fusionScore = min($fusionScore, 200);

        $breakdown['fusion'] = ['score' => $fusionScore, 'max' => 200, 'details' => $fusionDetails];
        $total += $fusionScore;

        // ==========================================
        // 6. Special Features (100 Points)
        // ==========================================
        $featureScore = 0;
        $featureDetails = [];
        
        $features = [
            'raw' => 15,
            'hdr' => 15,
            'night' => 15,
            'depth' => 15, // ToF / LiDAR
            'spectrum' => 20,
            'log' => 20 // Pro video
        ];
        
        // Check keywords
        if (str_contains($allSpecsLow, 'raw') || str_contains($allSpecsLow, 'dng')) {
             $addPoints($featureScore, $featureDetails, 'RAW Capture', 15, 'Supported');
        }
        if (str_contains($allSpecsLow, 'hdr')) {
             $addPoints($featureScore, $featureDetails, 'HDR', 15, 'Supported');
        }
        if (str_contains($allSpecsLow, 'night') || str_contains($allSpecsLow, 'low light')) {
             $addPoints($featureScore, $featureDetails, 'Night Mode', 15, 'Supported');
        }
        if (str_contains($allSpecsLow, 'tof') || str_contains($allSpecsLow, 'lidar') || str_contains($allSpecsLow, 'depth')) {
             $addPoints($featureScore, $featureDetails, 'Depth/LiDAR', 15, 'Supported');
        }
        if (str_contains($allSpecsLow, 'color spectrum') || str_contains($allSpecsLow, 'multispectral')) {
             $addPoints($featureScore, $featureDetails, 'Spectrum Sensor', 20, 'Supported');
        }
        if (str_contains($videoCaps, 'log') || str_contains($videoCaps, '10-bit') || str_contains($videoCaps, 'dolby vision')) {
             $addPoints($featureScore, $featureDetails, 'Pro Video (LOG/10-bit)', 20, 'Supported');
        }
        
        // Heuristic: Flagships usually feature packed
        if ($featureScore < 50 && $phone->price > 60000) {
            $addPoints($featureScore, $featureDetails, 'Flagship Feature Set (Est)', 30, 'High-end Model');
        }

        $featureScore = min($featureScore, 100);
        $breakdown['features'] = ['score' => $featureScore, 'max' => 100, 'details' => $featureDetails];
        $total += $featureScore;
        
        // ==========================================
        // 7. Online Benchmarks (390 Points)
        // ==========================================
        $benchScore = 0;
        $benchDetails = [];
        
        // DxOMark Camera Score: Max 180 points
        // Scale from typical max ~150 to 180 points
        $dxo = $phone->benchmarks->dxomark_score ?? null;
        $dxoPoints = 0;
        if ($dxo !== null && $dxo > 0) {
            // Scale: (score / 150) * 180, capped at 180
            $dxoPoints = min(($dxo / 150) * 180, 180);
            $addPoints($benchScore, $benchDetails, 'DxOMark', round($dxoPoints, 1), "Score: {$dxo}");
        } else {
            $addPoints($benchScore, $benchDetails, 'DxOMark', 0, 'No Data');
        }
        
        // PhoneArena Camera Score: Max 130 points
        // Scale from typical max ~100 to 130 points
        $pa = $phone->benchmarks->phonearena_camera_score ?? null;
        $paPoints = 0;
        if ($pa !== null && $pa > 0) {
            // Scale: (score / 100) * 130, capped at 130
            $paPoints = min(($pa / 100) * 130, 130);
            $addPoints($benchScore, $benchDetails, 'PhoneArena', round($paPoints, 1), "Score: {$pa}");
        } else {
            $addPoints($benchScore, $benchDetails, 'PhoneArena', 0, 'No Data');
        }
        
        // Other Benchmarks: Max 80 points (reserved for future use)
        // Currently not implemented - would include GSMArena, AnandTech, etc.
        $addPoints($benchScore, $benchDetails, 'Other Benchmarks', 0, 'Not Available');
        
        $breakdown['benchmarks'] = ['score' => $benchScore, 'max' => 390, 'details' => $benchDetails];
        $total += $benchScore;

        return [
            'total_score' => $total,
            'breakdown' => $breakdown
        ];
    }
    
    // Helpers
    private static function parseList($str) {
        $str = str_replace([' + ', ' & ', ' and '], ',', $str);
        $parts = explode(',', $str);
        return array_map('trim', $parts);
    }
    
    private static function compareSensorSize($current, $target) {
        // Simple string comparison for standard fractional sizes
        // "1/1.4" vs "1/1.4"
        return str_contains($current, $target);
    }
    
    private static function extractMP($str) {
        if (preg_match('/(\d+)\s*MP/i', $str, $matches)) {
            return intval($matches[1]);
        }
        return 0;
    }
    
    private static function extractFNumber($str) {
        if (preg_match('/f\/(\d+\.?\d*)/i', $str, $matches)) {
            return floatval($matches[1]);
        }
        return 0;
    }
}
