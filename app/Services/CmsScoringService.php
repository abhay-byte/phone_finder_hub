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
        
        // -- Sensor Size (40 pts per camera, weighted) --
        $mainSensorSize = $sensors[0] ?? '';
        $mainSpecs = $phone->camera->main_camera_specs ?? '';
        
        // Fallback: Extract from specs if missing or doesn't look like fraction
        if (empty($mainSensorSize) || !str_contains($mainSensorSize, '/')) {
             if (preg_match('/1\/\d+(\.\d+)?\"?/', $mainSpecs, $matches)) {
                 $mainSensorSize = str_replace('"', '', $matches[0]);
             }
        }
        
        $mainSensorPoints = 0;
        
        if (self::compareSensorSize($mainSensorSize, '1/1.0')) $mainSensorPoints = 40; // 1 inch
        elseif (self::compareSensorSize($mainSensorSize, '1/1.1')) $mainSensorPoints = 38;
        elseif (self::compareSensorSize($mainSensorSize, '1/1.2')) $mainSensorPoints = 36;
        elseif (self::compareSensorSize($mainSensorSize, '1/1.3')) $mainSensorPoints = 34; // Nothing Phone 3
        elseif (self::compareSensorSize($mainSensorSize, '1/1.4')) $mainSensorPoints = 32;
        elseif (self::compareSensorSize($mainSensorSize, '1/1.5')) $mainSensorPoints = 30; // 1/1.56 standard flagship
        elseif (self::compareSensorSize($mainSensorSize, '1/1.7')) $mainSensorPoints = 24; // Sony IMX
        elseif (self::compareSensorSize($mainSensorSize, '1/2.0')) $mainSensorPoints = 16;
        elseif (self::compareSensorSize($mainSensorSize, '1/2.5')) $mainSensorPoints = 12; // Small
        else $mainSensorPoints = 8; // Very small
        
        // Adjust for "50MP" or just MP being passed as sensor size (fix for bad data)
        if (!str_contains($mainSensorSize, '/')) $mainSensorSize .= ' (Est)';

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
        
        // Count valid cameras (excluding low-res depth/macro)
        $validMainCameras = 0;
        $allSpecParts = preg_split('/(\n|\+)/', $phone->camera->main_camera_specs ?? '');
        foreach ($allSpecParts as $part) {
            $pLower = strtolower($part);
            $mp = self::extractMP($pLower);
            if ($mp > 0) {
                // Check if it's a "filler" camera
                if ((str_contains($pLower, 'depth') || str_contains($pLower, 'macro')) && $mp < 12) {
                    continue; // Skip low res depth/macro
                }
                $validMainCameras++;
            }
        }
        // Check explicit columns for additional useful cameras
        $explicitExtras = 0;
        if (!empty($phone->camera->telephoto_camera_specs)) $explicitExtras++;
        if (!empty($phone->camera->ultrawide_camera_specs)) $explicitExtras++;
        
        // Use the higher count: detailed main parsing vs 1 (Main) + Explicit Extras
        // This ensures phones with "Main \n UW" string work (count=2)
        // And phones with "Main" string + "UW" column work (max(1, 1+1)=2)
        $hasCams = max($validMainCameras, 1 + $explicitExtras);
        
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
        
        // ==========================================
        // Front/Selfie Camera (40 Points)
        // ==========================================
        $selfieScore = 0;
        $selfieSpecs = strtolower($phone->camera->selfie_camera_specs ?? '');
        
        if (!empty($selfieSpecs)) {
            // Resolution (20 pts)
            $selfieMp = self::extractMP($selfieSpecs);
            $resPts = 0;
            if ($selfieMp >= 50) $resPts = 20;
            elseif ($selfieMp >= 32) $resPts = 16;
            elseif ($selfieMp >= 20) $resPts = 12;
            elseif ($selfieMp >= 12) $resPts = 8;
            else $resPts = 4;
            $selfieScore += $resPts;
            
            // Aperture (15 pts)
            $selfieAperture = self::extractFNumber($selfieSpecs);
            $apPts = 0;
            if ($selfieAperture > 0) {
                if ($selfieAperture <= 1.8) $apPts = 15;
                elseif ($selfieAperture <= 2.0) $apPts = 12;
                elseif ($selfieAperture <= 2.4) $apPts = 8;
                else $apPts = 4;
            }
            $selfieScore += $apPts;
            
            // Autofocus bonus (5 pts)
            if (str_contains($selfieSpecs, 'pdaf') || str_contains($selfieSpecs, 'autofocus') || str_contains($selfieSpecs, 'af')) {
                $selfieScore += 5;
            }
            
            $selfieLabel = "{$selfieMp}MP, f/{$selfieAperture}";
        } else {
            $selfieLabel = "No Data";
        }
        
        $addPoints($sensorScore, $sensorDetails, 'Front Camera', $selfieScore, $selfieLabel);
        
        $breakdown['sensor_optics'] = ['score' => min($sensorScore, 280), 'max' => 280, 'details' => $sensorDetails];
        $total += $breakdown['sensor_optics']['score'];

        // ==========================================
        // 2. Resolution & Binning (50 Points)
        // ==========================================
        $resScore = 0;
        $resDetails = [];
        
        // Helper to score a single sensor's resolution
        $scoreResolution = function($mp, $specsString) {
             $hasBinning = str_contains($specsString, 'quad bayer') || 
                           str_contains($specsString, 'tetra binning') || 
                           str_contains($specsString, 'pixel binning') ||
                           str_contains($specsString, '4-in-1') ||
                           str_contains($specsString, '9-in-1');
             
             if ($mp >= 200) return ['points' => 50, 'label' => "200MP"];
             if ($mp >= 108) return ['points' => 44, 'label' => "108MP"];
             if ($mp >= 64)  return ['points' => 36, 'label' => "{$mp}MP"];
             if ($mp >= 50)  return ['points' => 30, 'label' => "{$mp}MP"];
             if ($mp >= 40)  return ['points' => 24, 'label' => "{$mp}MP"];
             if ($mp >= 32)  return ['points' => 20, 'label' => "{$mp}MP"];
             if ($mp >= 20)  return ['points' => 16, 'label' => "{$mp}MP"];
             if ($mp >= 12)  return ['points' => 12, 'label' => "{$mp}MP"];
             return ['points' => 6, 'label' => "{$mp}MP"];
        };
        
        // Collect all cameras
        $cameras = [];
        
        // Main
        $mainSpecs = strtolower($phone->camera->main_camera_specs ?? ''); // Ensure mainSpecs is defined for this section
        $mainMP = self::extractMP($mainSpecs);
        if ($mainMP > 0) $cameras[] = ['name' => 'Main', 'mp' => $mainMP, 'specs' => $mainSpecs];
        
        // Telephoto
        $teleSpecs = strtolower($phone->camera->telephoto_camera_specs ?? '');
        $teleMP = 0;
        if (!empty($teleSpecs)) {
            $teleMP = self::extractMP($teleSpecs);
        } else {
             // Split main specs and look for telephoto part
             $parts = preg_split('/(\n|\+)/', $mainSpecs);
             foreach ($parts as $part) {
                 if (str_contains($part, 'telephoto') || str_contains($part, 'periscope')) {
                     $teleMP = self::extractMP($part);
                     if ($teleMP > 0) {
                         $teleSpecs = trim($part);
                         break;
                     }
                 }
             }
        }
        if ($teleMP > 0) $cameras[] = ['name' => 'Telephoto', 'mp' => $teleMP, 'specs' => $teleSpecs];
        
        // Ultrawide
        $uwSpecs = strtolower($phone->camera->ultrawide_camera_specs ?? '');
        $uwMP = 0;
        if (!empty($uwSpecs)) {
            $uwMP = self::extractMP($uwSpecs);
        } else {
             // Split main specs and look for ultrawide part
             $parts = preg_split('/(\n|\+)/', $mainSpecs);
             foreach ($parts as $part) {
                 if (str_contains($part, 'ultrawide') || str_contains($part, 'ultra-wide') || str_contains($part, '120˚') || str_contains($part, '120 degree')) {
                     $uwMP = self::extractMP($part);
                     if ($uwMP > 0) {
                         $uwSpecs = trim($part);
                         break;
                     }
                 }
             }
        }
        if ($uwMP > 0) $cameras[] = ['name' => 'Ultrawide', 'mp' => $uwMP, 'specs' => $uwSpecs];
        
        // Macro / Depth (Extra Sensors)
        // Split main specs and look for other sensors not yet counted
        $parts = preg_split('/(\n|\+)/', $mainSpecs);
        foreach ($parts as $part) {
             $partLower = strtolower($part);
             // Skip if it looks like the main, tele, or uw we already found
             // This is a simple heuristic: if we already have a 64MP main and this part is 64MP, skip.
             // Better: explicitly look for "macro" or "depth" keywords
             
             if (str_contains($partLower, 'macro') && !str_contains($partLower, 'tele')) { // Exclude tele-macro which might be the tele lens
                 $macroMP = self::extractMP($partLower);
                 if ($macroMP > 0 && $macroMP < 10) { // usually low MP. If high MP, likely a shared lens
                     $cameras[] = ['name' => 'Macro', 'mp' => $macroMP, 'specs' => trim($part)];
                 }
             } elseif (str_contains($partLower, 'depth')) {
                 $depthMP = self::extractMP($partLower);
                 if ($depthMP > 0) {
                     $cameras[] = ['name' => 'Depth', 'mp' => $depthMP, 'specs' => trim($part)];
                 }
             }
        }
        
        // Front
        $frontSpecs = strtolower($phone->camera->selfie_camera_specs ?? '');
        $frontMP = self::extractMP($frontSpecs);
        if ($frontMP > 0) $cameras[] = ['name' => 'Front', 'mp' => $frontMP, 'specs' => $frontSpecs];
        
        // Calculate Average
        $totalResPoints = 0;
        $sensorCount = count($cameras);
        
        if ($sensorCount > 0) {
            foreach ($cameras as $cam) {
                $res = $scoreResolution($cam['mp'], strtolower($cam['specs']));
                $totalResPoints += $res['points'];
                $resDetails[] = ['criterion' => "{$cam['name']} Sensor", 'points' => $res['points'], 'reason' => $res['label']];
            }
            
            // Final Score = Average
            $resScore = $totalResPoints / $sensorCount;
            $resDetails[] = ['criterion' => 'Average Score', 'points' => round($resScore, 1), 'reason' => "Avg of {$sensorCount} sensors"];
        } else {
             $resScore = 0;
             $resDetails[] = ['criterion' => 'No Data', 'points' => 0, 'reason' => 'No sensors found'];
        }
        
        $breakdown['resolution'] = ['score' => round($resScore, 1), 'max' => 50, 'details' => $resDetails];
        $total += $breakdown['resolution']['score'];

        // ==========================================
        // 3. Focus & Stability (200 Points)
        // ==========================================
        // Per-camera scoring to reward phones with premium features across multiple cameras
        // Autofocus: 100pts (Main 40, Tele 20, UW 20, Front 20)
        // Stabilization: 100pts (Main OIS 40, Tele OIS 20, Gyro-EIS 20, Advanced 20)
        
        $focusScore = 0;
        $focusDetails = [];
        
        // Parse camera specs for each camera
        $mainSpecs = strtolower($phone->camera->main_camera_specs ?? '');
        $teleSpecs = strtolower($phone->camera->telephoto_camera_specs ?? '');
        $uwSpecs = strtolower($phone->camera->ultrawide_camera_specs ?? '');
        $frontSpecs = strtolower($phone->camera->selfie_camera_specs ?? '');
        $features = strtolower($phone->camera->main_camera_features ?? '');
        $video = strtolower($phone->camera->main_video_capabilities ?? '');
        
        // FALLBACK: If separate telephoto/ultrawide fields are NULL, try to extract from main_camera_specs
        if (empty($teleSpecs) && !empty($mainSpecs)) {
            // Split by newline OR by "+" (common separator in specs)
            $parts = preg_split('/(\n|\+)/', $mainSpecs);
            foreach ($parts as $part) {
                $part = trim($part);
                if (empty($part)) continue;
                
                if (str_contains(strtolower($part), 'telephoto') || str_contains(strtolower($part), 'periscope')) {
                    $teleSpecs = $part;
                    break;
                }
            }
        }
        
        if (empty($uwSpecs) && !empty($mainSpecs)) {
            // Split by newline OR by "+"
            $parts = preg_split('/(\n|\+)/', $mainSpecs);
            foreach ($parts as $part) {
                $part = trim($part);
                if (empty($part)) continue;
                
                if (str_contains(strtolower($part), 'ultrawide') || str_contains(strtolower($part), 'ultra-wide') || preg_match('/\d+˚/', $part)) {
                    $uwSpecs = $part;
                    break;
                }
            }
        }
        
        // Helper function to score autofocus for a camera
        $scoreAutofocus = function($specs, $features) {
            $hasLaser = str_contains($features, 'laser focus') || str_contains($features, 'laser af');
            $hasDualPixel = str_contains($specs, 'dual pixel') || str_contains($specs, 'dual-pixel') || str_contains($specs, 'dpaf');
            $hasMultiPDAF = str_contains($specs, 'multi-directional pdaf') || str_contains($specs, 'all-pixel pdaf') || str_contains($specs, 'omnidirectional pdaf');
            $hasPDAF = str_contains($specs, 'pdaf');
            
            if ($hasLaser && $hasDualPixel) return ['score' => 1.0, 'label' => 'Dual-Pixel+Laser'];
            if ($hasLaser && ($hasMultiPDAF || $hasPDAF)) return ['score' => 0.9, 'label' => 'Laser+Multi-PDAF'];
            if ($hasDualPixel) return ['score' => 0.8, 'label' => 'Dual-Pixel AF'];
            if ($hasMultiPDAF) return ['score' => 0.7, 'label' => 'Multi-PDAF'];
            if ($hasPDAF) return ['score' => 0.5, 'label' => 'PDAF'];
            
            // Generic AF (Autofocus) - Valuable for Selfie cameras vs Fixed Focus
            if (str_contains($specs, ' af') || str_contains($specs, 'autofocus')) {
                return ['score' => 0.4, 'label' => 'Autofocus'];
            }
            
            return ['score' => 0.0, 'label' => 'Fixed Focus/Contrast']; 
        };
        
        // Main Camera Autofocus (40 pts)
        $mainAF = $scoreAutofocus($mainSpecs, $features);
        $mainAFScore = $mainAF['score'] * 40;
        $addPoints($focusScore, $focusDetails, 'Main Camera AF', $mainAFScore, $mainAF['label']);
        
        // Telephoto Camera Autofocus (20 pts)
        if (!empty($teleSpecs)) {
            $teleAF = $scoreAutofocus($teleSpecs, '');
            $teleAFScore = $teleAF['score'] * 20;
            $addPoints($focusScore, $focusDetails, 'Telephoto AF', $teleAFScore, $teleAF['label']);
        } else {
            $addPoints($focusScore, $focusDetails, 'Telephoto AF', 0, 'No Telephoto');
        }
        
        // Ultrawide Camera Autofocus (20 pts)
        if (!empty($uwSpecs)) {
            $uwAF = $scoreAutofocus($uwSpecs, '');
            $uwAFScore = $uwAF['score'] * 20;
            $addPoints($focusScore, $focusDetails, 'Ultrawide AF', $uwAFScore, $uwAF['label']);
        } else {
            $addPoints($focusScore, $focusDetails, 'Ultrawide AF', 0, 'No Ultrawide');
        }
        
        // Front Camera Autofocus (20 pts)
        if (!empty($frontSpecs)) {
            $frontAF = $scoreAutofocus($frontSpecs, '');
            $frontAFScore = $frontAF['score'] * 20;
            $addPoints($focusScore, $focusDetails, 'Front Camera AF', $frontAFScore, $frontAF['label']);
        } else {
            $addPoints($focusScore, $focusDetails, 'Front Camera AF', 0, 'Contrast AF');
        }
        

        
        // Define common variables for subsequent sections
        $videoCaps = strtolower($phone->camera->main_video_capabilities ?? '');
        $selfieVideo = strtolower($phone->camera->selfie_video_features ?? '');
        $features = strtolower($phone->camera->main_camera_features ?? '');

        $stabScore = 0;
        $stabDetails = [];
        $allSpecsLow = $mainSpecs . ' ' . $teleSpecs . ' ' . $uwSpecs . ' ' . $features . ' ' . $videoCaps; // Ensure allSpecsLow is defined for this section

        // D. Stabilization (Max 100pts) -> Adjusted to make room for Front Cam
        // Main OIS
        if (str_contains($mainSpecs, 'ois') || str_contains($features, 'ois')) {
             $addPoints($stabScore, $stabDetails, 'Main Camera OIS', 40, 'Optical Stab.');
        }
        
        // Telephoto OIS
        if (str_contains($teleSpecs, 'ois')) {
             $addPoints($stabScore, $stabDetails, 'Telephoto OIS', 20, 'Optical Stab.');
        }
        
        // Front Camera OIS (Premium Feature)
        if (str_contains($phone->camera->selfie_camera_specs ?? '', 'ois') || str_contains($phone->camera->selfie_camera_features ?? '', 'ois')) {
             $addPoints($stabScore, $stabDetails, 'Front Camera OIS', 15, 'Optical Stab.');
        }
        
        // Video Stabilization (EIS/OIS in video)
        if (str_contains($videoCaps, 'eis') || str_contains($videoCaps, 'ois')) {
             $addPoints($stabScore, $stabDetails, 'Video Stabilization', 15, 'Rear Gyro-EIS/OIS');
        }
        
        // Front Video Stabilization
        $selfieVideo = strtolower($phone->camera->selfie_video_features ?? '');
        if (str_contains($selfieVideo, 'eis') || str_contains($selfieVideo, 'ois')) {
             $addPoints($stabScore, $stabDetails, 'Front Video Stab.', 10, 'Selfie EIS/OIS');
        }
        
        // Advanced Stabilization Modes
        if (str_contains($videoCaps, 'gimbal') || str_contains($videoCaps, 'horizon') || str_contains($videoCaps, 'steady video')) {
             $addPoints($stabScore, $stabDetails, 'Advanced Stabilization', 10, 'Gimbal/Horizon Level');
        }
        
        $breakdown['focus_stability'] = ['score' => min($focusScore + $stabScore, 200), 'max' => 200, 'details' => array_merge($focusDetails, $stabDetails)];
        $total += $breakdown['focus_stability']['score'];

        // ==========================================
        // 4. Video System (200 Points)
        // ==========================================
        $videoScore = 0;
        $videoDetails = [];
        $videoCaps = strtolower($phone->camera->main_video_capabilities ?? '');
        $selfieVideo = strtolower($phone->camera->selfie_video_features ?? '');
        $mainFeatures = strtolower($phone->camera->main_camera_features ?? '');
        
        // A. Resolution & Frame Rate (Max 100pts)
        $resScore = 20;
        $resLabel = '1080p@30';
        
        if (str_contains($videoCaps, '8k')) {
            $resScore = 100; $resLabel = '8K Video';
        } elseif (str_contains($videoCaps, '4k') && (str_contains($videoCaps, '120fps') || str_contains($videoCaps, '120 fps'))) {
            $resScore = 90; $resLabel = '4K @ 120fps';
        } elseif (str_contains($videoCaps, '4k') && (str_contains($videoCaps, '60fps') || str_contains($videoCaps, '60 fps'))) {
            $resScore = 80; $resLabel = '4K @ 60fps';
        } elseif (str_contains($videoCaps, '4k')) {
            $resScore = 50; $resLabel = '4K @ 30fps';
        } elseif (str_contains($videoCaps, '1080p') && (str_contains($videoCaps, '60fps') || str_contains($videoCaps, '60 fps'))) {
            $resScore = 30; $resLabel = '1080p @ 60fps';
        }
        $addPoints($videoScore, $videoDetails, 'Max Resolution', $resScore, $resLabel);
        
        // B. Video Features (Max 60pts)
        if (str_contains($videoCaps, 'dolby vision') || str_contains($videoCaps, 'hdr10+')) {
             $addPoints($videoScore, $videoDetails, 'High-End HDR', 30, 'Dolby Vision/HDR10+');
        } elseif (str_contains($videoCaps, 'hdr')) {
             $addPoints($videoScore, $videoDetails, 'Standard HDR', 10, 'HDR Video');
        }
        
        if (str_contains($videoCaps, '10-bit') || str_contains($videoCaps, '10 bit')) {
             $addPoints($videoScore, $videoDetails, 'Color Depth', 20, '10-bit Color');
        }
        
        if (str_contains($videoCaps, 'log') || str_contains($videoCaps, 'raw') || str_contains($mainFeatures, 'cinematic')) {
             $addPoints($videoScore, $videoDetails, 'Pro Video Features', 20, 'LOG/Cinematic');
        }
        
        if (str_contains($videoCaps, '960fps') || str_contains($videoCaps, '480fps')) {
             $addPoints($videoScore, $videoDetails, 'Super Slow-Mo', 20, '960fps+');
        }
        
        // C. Selfie Video (Max 40pts)
        $selfieScore = 10; // Default
        $selfieLabel = '1080p@30';
        
        if (str_contains($selfieVideo, '4k')) {
            $selfieScore = 40; $selfieLabel = '4K Selfie Video';
        } elseif (str_contains($selfieVideo, '60fps')) {
            $selfieScore = 30; $selfieLabel = '1080p @ 60fps';
        }
        $addPoints($videoScore, $videoDetails, 'Selfie Video', $selfieScore, $selfieLabel);

        $breakdown['video'] = ['score' => min($videoScore, 200), 'max' => 200, 'details' => $videoDetails];
        $total += $breakdown['video']['score'];

        // ==========================================
        // 5. Multi-Camera Fusion (200 Points)
        // ==========================================
        $fusionScore = 0;
        $fusionDetails = [];
        
        // A. Focal Length Coverage (Max 80pts)
        $hasUltrawide = !empty($uwSpecs) || str_contains($mainSpecs, 'ultrawide') || str_contains($mainSpecs, '120˚') || str_contains($mainSpecs, '13mm') || str_contains($mainSpecs, '14mm') || str_contains($mainSpecs, '15mm') || str_contains($mainSpecs, '16mm');
        $hasTelephoto = !empty($teleSpecs) || str_contains($mainSpecs, 'telephoto') || str_contains($mainSpecs, 'periscope') || str_contains($mainSpecs, 'optical zoom');
        
        if ($hasUltrawide && $hasTelephoto) {
             $addPoints($fusionScore, $fusionDetails, 'Focal Length Coverage', 80, 'Ultrawide + Telephoto');
        } elseif ($hasUltrawide) {
             $addPoints($fusionScore, $fusionDetails, 'Focal Length Coverage', 40, 'Ultrawide Only');
        } elseif ($hasTelephoto) { // Rare combo
             $addPoints($fusionScore, $fusionDetails, 'Focal Length Coverage', 40, 'Telephoto Only');
        } else {
             $addPoints($fusionScore, $fusionDetails, 'Focal Length Coverage', 10, 'Main Camera Only');
        }
        
        // Define allSpecsLow for Fusion analysis
        $allSpecsLow = $mainSpecs . ' ' . $teleSpecs . ' ' . $uwSpecs . ' ' . $features;

        // B. Zoom Capabilities (Max 60pts)
        $zoomScore = 0;
        $zoomLabel = 'Digital Zoom';
        
        // Extract optical zoom level (Fixed Regex for decimals)
        $zoomLevel = 0;
        if (preg_match('/(\d+(\.\d+)?)x\s*optical/i', $allSpecsLow, $matches)) {
            $zoomLevel = floatval($matches[1]);
        }
        
        if (str_contains($allSpecsLow, 'periscope') && $zoomLevel >= 5) {
            $zoomScore = 60; $zoomLabel = '5x+ Periscope';
        } elseif (str_contains($allSpecsLow, 'periscope')) {
            $zoomScore = 50; $zoomLabel = 'Periscope Zoom';
        } elseif ($zoomLevel >= 3) {
            $zoomScore = 40; $zoomLabel = '3x+ Telephoto';
        } elseif ($zoomLevel >= 2) {
            $zoomScore = 30; $zoomLabel = '2x Telephoto';
        }
        $addPoints($fusionScore, $fusionDetails, 'Zoom Capabilities', $zoomScore, $zoomLabel);
        
        // C. Macro Capabilities (Max 20pts)
        if (str_contains($allSpecsLow, 'tele-macro') || str_contains($allSpecsLow, 'telemacro')) {
             $addPoints($fusionScore, $fusionDetails, 'Macro', 20, 'Tele-Macro lens');
        } elseif ((str_contains($allSpecsLow, 'macro') && str_contains($allSpecsLow, 'ultrawide')) || (str_contains($allSpecsLow, 'af') && str_contains($uwSpecs, 'macro'))) {
             $addPoints($fusionScore, $fusionDetails, 'Macro', 15, 'Ultrawide Macro');
        } elseif (str_contains($allSpecsLow, 'macro')) {
             $addPoints($fusionScore, $fusionDetails, 'Macro', 10, 'Dedicated Macro');
        }
        
        // D. Consistency & Color (Max 40pts)
        if (str_contains($features, 'hasselblad') || str_contains($features, 'leica') || str_contains($features, 'zeiss')) {
             $addPoints($fusionScore, $fusionDetails, 'Color Science', 25, 'Brand Partnership');
        }
        
        if (str_contains($features, 'spectrum') || str_contains($features, 'pro features') || str_contains($features, 'ai fusion')) {
             $addPoints($fusionScore, $fusionDetails, 'Consistency', 15, 'Color Spectrum/AI');
        }
        
        $breakdown['fusion'] = ['score' => min($fusionScore, 200), 'max' => 200, 'details' => $fusionDetails];
        $total += $breakdown['fusion']['score'];

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
        
        // Other Benchmarks: Max 80 points
        // Assuming input score is out of 100 (e.g. Percentage, or generic rating)
        $other = $phone->benchmarks->other_benchmark_score ?? null;
        $otherPoints = 0;
        if ($other !== null && $other > 0) {
            // Scale: (score / 100) * 80, capped at 80
            $otherPoints = min(($other / 100) * 80, 80);
            
            $reason = "Score: {$other}";
            // Custom detail for Oppo Find X9 Pro as requested
            if ($phone->name === 'Oppo Find X9 Pro') {
                $reason = "Avg of GSM (4.6/5) & Mobile91 (9/10)";
            }
            
            $addPoints($benchScore, $benchDetails, 'Other Benchmarks', round($otherPoints, 1), $reason);
        } else {
            $addPoints($benchScore, $benchDetails, 'Other Benchmarks', 0, 'Not Available');
        }
        
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
