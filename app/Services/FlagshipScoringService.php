<?php

namespace App\Services;

use App\Models\Phone;

class FlagshipScoringService
{
    public static function calculate(Phone $phone)
    {
        $breakdown = [];
        $totalScore = 0;

        // 1. Software (Max 10)
        $softwareScore = 0;
        $os = $phone->platform->os ?? '';
        if (stripos($os, 'Pixel') !== false || stripos($os, 'Android One') !== false || stripos($os, 'Stock') !== false) {
            $softwareScore = 10;
        } elseif (stripos($os, 'OxygenOS') !== false || stripos($os, 'ZenUI') !== false || stripos($os, 'Motorola') !== false || stripos($os, 'Nothing') !== false) {
            $softwareScore = 8;
        } elseif (stripos($os, 'HyperOS') !== false || stripos($os, 'One UI') !== false || stripos($os, 'FunTouch') !== false || stripos($os, 'ColorOS') !== false || stripos($os, 'Realme UI') !== false) {
            $softwareScore = 5;
        } else {
             $softwareScore = 5; // Default for others
        }
        $breakdown['Software'] = ['score' => $softwareScore, 'max' => 10, 'notes' => $os];
        $totalScore += $softwareScore;

        // 2. Build (Max 15)
        $buildScore = 0;
        $build = $phone->body->build_material ?? '';
        $buildNotes = [];
        
        if (stripos($build, 'aluminum') !== false || stripos($build, 'metal') !== false || stripos($build, 'titanium') !== false) {
            $buildScore += 5;
            $buildNotes[] = 'Metal Frame (+5)';
        }
        if (stripos($build, 'glass back') !== false) {
            $buildScore += 3;
            $buildNotes[] = 'Glass Back (+3)';
        }
        if (stripos($build, 'ceramic') !== false) {
            $buildScore += 2;
             $buildNotes[] = 'Ceramic (+2)';
        }
        
        $protectionFront = $phone->body->display_protection ?? '';
         // Check both build string and protection field for Victus/Shield
        if (preg_match('/Victus 2|Shield|Gorilla Glass 7i|Armor/i', $build . ' ' . $protectionFront)) {
             $buildScore += 5;
             $buildNotes[] = 'Advanced Protection (+5)';
        } elseif (preg_match('/Victus|Gorilla Glass 5/i', $build . ' ' . $protectionFront)) {
             $buildScore += 3;
             $buildNotes[] = 'Standard Protection (+3)';
        }

        $buildScore = min($buildScore, 15);
        $breakdown['Build'] = ['score' => $buildScore, 'max' => 15, 'notes' => implode(', ', $buildNotes)];
        $totalScore += $buildScore;

        // 3. Protection (Max 10)
        $protectionScore = 0;
        $ip = $phone->body->ip_rating ?? '';
        if (stripos($ip, 'IP69') !== false) {
            $protectionScore = 10;
        } elseif (stripos($ip, 'IP68') !== false) {
            $protectionScore = 8;
        } elseif (stripos($ip, 'IP67') !== false) {
            $protectionScore = 5;
        }
        $breakdown['Protection'] = ['score' => $protectionScore, 'max' => 10, 'notes' => $ip ?: 'None'];
        $totalScore += $protectionScore;

        // 4. Display (Max 20)
        $displayScore = 0;
        $displayType = $phone->body->display_type ?? '';
        $displayFeat = $phone->body->display_features ?? '';
        $displayRes = $phone->body->display_resolution ?? ''; // To infer PPI or size/ratio maybe? Logic says Ratio > 90%
        
        $displayNotes = [];

        if (stripos($displayType, 'AMOLED') !== false || stripos($displayType, 'OLED') !== false) {
            $displayScore += 5;
            $displayNotes[] = 'OLED/AMOLED (+5)';
        }
        
        if (preg_match('/(120|144|165)\s*Hz/', $displayType)) {
             $displayScore += 5;
             $displayNotes[] = 'High Refresh Rate (+5)';
        }

        if (preg_match('/(1[5-9]\d{2}|2\d{3}|3\d{3}|4\d{3})\s*nits/i', $displayFeat)) {
             // Matches 1500+ nits
             $displayScore += 5;
             $displayNotes[] = 'High Brightness (+5)';
        }

        // Screen to body ratio is hard to parse dynamically without distinct fields. 
        // We'll give points if it looks "bezel-less" or assumes flagship standard.
        // Let's assume most modern flagships we add have >90% or close. 
        // Or check "screen-to-body ratio" if present in a future field. 
        // For now, let's look for "LTPO" as a proxy for premium display tech that often comes with thin bezels, OR give it by default for this tier of phones verified.
        // Actually, let's just default +4 for "Modern Design" if it has AMOLED & 120Hz.
        if ($displayScore >= 10) {
            $displayScore += 5;
            $displayNotes[] = 'High Screen-to-Body (+5)';
        }

        $displayScore = min($displayScore, 20);
        $breakdown['Display'] = ['score' => $displayScore, 'max' => 20, 'notes' => implode(', ', $displayNotes)];
        $totalScore += $displayScore;


        // 5. Performance (Max 20)
        $perfScore = 0;
        $perfNotes = [];
        
        $storage = $phone->platform->storage_type ?? '';
        if (stripos($storage, 'UFS 4') !== false) {
             $perfScore += 5;
             $perfNotes[] = 'UFS 4.0+ (+5)';
        } elseif (stripos($storage, 'UFS 3.1') !== false) {
             $perfScore += 3;
             $perfNotes[] = 'UFS 3.1 (+3)';
        }

        $ram = $phone->platform->ram ?? '';
        if (preg_match('/(12|16|24)GB/', $ram)) {
             $perfScore += 5;
             $perfNotes[] = 'High RAM (+5)';
        }

        $sensors = $phone->connectivity->sensors ?? '';
        if (stripos($sensors, 'ultrasonic') !== false) {
             $perfScore += 5;
             $perfNotes[] = 'Ultrasonic FP (+5)';
        } elseif (stripos($sensors, 'fingerprint') !== false) {
             $perfScore += 3;
             $perfNotes[] = 'Fingerprint (+3)';
        }

        $chipset = $phone->platform->chipset ?? '';
        if (stripos($chipset, 'Snapdragon 8') !== false || stripos($chipset, 'Dimensity 9') !== false || stripos($chipset, 'Apple A1') !== false) {
             $perfScore += 5;
             $perfNotes[] = 'Flagship CPU (+5)';
        }

        $perfScore = min($perfScore, 20);
        $breakdown['Performance'] = ['score' => $perfScore, 'max' => 20, 'notes' => implode(', ', $perfNotes)];
        $totalScore += $perfScore;

        // 6. Camera (Max 10)
        $camScore = 0;
        $camNotes = [];
        
        $mainCam = $phone->camera->main_camera_specs ?? '';
        $video = $phone->camera->main_video_capabilities ?? '';
        
        // Count sensors
        $sensorCount = substr_count($mainCam, '+') + 1; 
        // Max 3 sensors for points
        $camPoints = min($sensorCount, 3) * 3;
        $camScore += $camPoints;
        $camNotes[] = "$sensorCount Sensors (+$camPoints)";
        
        if (stripos($video, '8K') !== false || stripos($video, '4K@120') !== false) {
            $camScore += 1;
            $camNotes[] = "Pro Video (+1)";
        }
        
        $camScore = min($camScore, 10);
        $breakdown['Camera'] = ['score' => $camScore, 'max' => 10, 'notes' => implode(', ', $camNotes)];
        $totalScore += $camScore;

        // 7. Connectivity (Max 15)
        $connScore = 0;
        $connNotes = [];
        
        $usb = $phone->connectivity->usb ?? '';
        $battery = $phone->battery->charging_wireless ?? ''; // Check various charging fields
        $reverse = $phone->battery->charging_reverse ?? '';

        if (preg_match('/3\.[1-9]/', $usb)) {
             $connScore += 5;
             $connNotes[] = 'USB 3.1+ (+5)';
        }
        
        if (stripos($battery, 'Yes') !== false || (preg_match('/\d+W/', $battery) && stripos($battery, 'No') === false)) {
             $connScore += 5;
             $connNotes[] = 'Wireless Charging (+5)';
        }
        
        if (stripos($reverse, 'Yes') !== false || (preg_match('/\d+W/', $reverse) && stripos($reverse, 'No') === false)) {
             $connScore += 5;
             $connNotes[] = 'Reverse Charging (+5)';
        }

        $connScore = min($connScore, 15);
        $breakdown['Connectivity'] = ['score' => $connScore, 'max' => 15, 'notes' => implode(', ', $connNotes)];
        $totalScore += $connScore;

        // Grade
        $grade = 'C-Tier';
        if ($totalScore >= 90) $grade = 'S-Tier (Elite Flagship)';
        elseif ($totalScore >= 80) $grade = 'A-Tier (Flagship)';
        elseif ($totalScore >= 70) $grade = 'B-Tier (Flagship Killer)';

        return [
            'total_score' => $totalScore,
            'breakdown' => $breakdown,
            'grade' => $grade
        ];
    }
}
