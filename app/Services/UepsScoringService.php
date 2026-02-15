<?php

namespace App\Services;

use App\Models\Phone;

class UepsScoringService
{
    /**
     * Helper to check if any keyword exists in a target string with robust matching.
     * 
     * @param string $target The detailed spec string to search in.
     * @param string|array $keywords Single keyword or array of keywords.
     * @param bool $regex If true, treats keywords as regex patterns.
     * @return bool True if match found.
     */
    private static function checkSpecs($target, $keywords, $regex = false)
    {
        if (empty($target)) return false;
        
        $keywords = (array) $keywords;
        $normalizedTarget = strtolower(preg_replace('/\s+/', '', $target)); // Remove all spaces and lowercase

        foreach ($keywords as $keyword) {
            if ($regex) {
                // For regex, we run it against the original string (case-insensitive)
                // This preserves spaces if the regex specifically handles them (e.g. /120\s*Hz/)
                if (preg_match($keyword, $target)) {
                    return true;
                }
            } else {
                // For standard keywords, we normalize them too
                // "Snapdragon 8 Elite" -> "snapdragon8elite"
                $normalizedKeyword = strtolower(preg_replace('/\s+/', '', $keyword));
                if (str_contains($normalizedTarget, $normalizedKeyword)) {
                    return true;
                }
            }
        }
        return false;
    }

    public static function calculate(Phone $phone)
    {
        $breakdown = [];
        $totalScore = 0;
        
        // Helper to add points
        $addPoints = function(&$categoryScore, &$categoryDetails, $criterion, $points, $reason) {
            $categoryScore += $points;
            $categoryDetails[] = ['criterion' => $criterion, 'points' => $points, 'reason' => $reason];
        };

        // --- A. Build & Durability (30 pts) ---
        $catA_Score = 0;
        $catA_Details = [];
        $build = $phone->body?->build_material ?? '';
        $dimensions = $phone->body?->dimensions ?? '';
        $ip = $phone->body?->ip_rating ?? '';
        $protection = $phone->body?->display_protection ?? '';

        // 1. Frame Material
        if (self::checkSpecs($build, ['titanium', 'stainless'])) {
            $addPoints($catA_Score, $catA_Details, 'Frame Material', 5, 'Titanium/Stainless (+5)');
        } elseif (self::checkSpecs($build, ['aluminum', 'metal', 'aluminium'])) {
             $addPoints($catA_Score, $catA_Details, 'Frame Material', 3, 'Aluminum (+3)');
        } else {
             $addPoints($catA_Score, $catA_Details, 'Frame Material', 0, 'Standard/Plastic (+0)');
        }

        // 2. Back Material
        if (self::checkSpecs($build, ['glass back', 'ceramic'])) {
             $addPoints($catA_Score, $catA_Details, 'Back Material', 5, 'Glass/Ceramic (+5)');
        } elseif (self::checkSpecs($build, ['leather', 'polymer', 'eco-leather'])) {
             $addPoints($catA_Score, $catA_Details, 'Back Material', 3, 'High-grade Fiber/Leather (+3)');
        } else {
             $addPoints($catA_Score, $catA_Details, 'Back Material', 1, 'Plastic/Other (+1)');
        }

        // 3. Front Glass
        if (self::checkSpecs($protection . $build, ['Victus 2', 'Gorilla Glass 7i', 'Shield', 'Armor', 'Kunlun'])) {
             $addPoints($catA_Score, $catA_Details, 'Front Glass', 5, 'Victus 2/Shield/Top-tier (+5)');
        } else {
             $addPoints($catA_Score, $catA_Details, 'Front Glass', 3, 'Standard Protection (+3)');
        }

        // 4. IP Rating
        if (self::checkSpecs($ip, ['IP69'])) {
             $addPoints($catA_Score, $catA_Details, 'IP Rating', 10, 'IP69/K (+10)');
        } elseif (self::checkSpecs($ip, ['IP68'])) {
             $addPoints($catA_Score, $catA_Details, 'IP Rating', 5, 'IP68 (+5)');
        } else {
             $addPoints($catA_Score, $catA_Details, 'IP Rating', 0, 'IP67/None (+0)');
        }

        // 5. Bezel Size
        $addPoints($catA_Score, $catA_Details, 'Bezel Size', 5, '<1.5mm (Est) (+5)');

        $breakdown['Build & Durability'] = ['score' => min($catA_Score, 30), 'max' => 30, 'details' => $catA_Details];
        $totalScore += min($catA_Score, 30);


        // --- B. Display Tech (40 pts) ---
        $catB_Score = 0;
        $catB_Details = [];
        $dispType = $phone->body?->display_type ?? '';
        $dispFeat = $phone->body?->display_features ?? '';
        $dispRes = $phone->body?->display_resolution ?? '';

        // 6. Panel Type
        if (self::checkSpecs($dispType, ['LTPO'])) {
             $addPoints($catB_Score, $catB_Details, 'Panel Type', 5, 'LTPO AMOLED/OLED (+5)');
        } elseif (self::checkSpecs($dispType, ['AMOLED', 'OLED'])) {
             $addPoints($catB_Score, $catB_Details, 'Panel Type', 3, 'Standard AMOLED (+3)');
        } else {
             $addPoints($catB_Score, $catB_Details, 'Panel Type', 1, 'LCD (+1)');
        }

        // 7. Refresh Rate
        if (self::checkSpecs($dispType, ['/144\s*Hz/i', '/165\s*Hz/i'], true)) {
             $addPoints($catB_Score, $catB_Details, 'Refresh Rate', 5, '144Hz-165Hz (+5)');
        } elseif (self::checkSpecs($dispType, ['/120\s*Hz/i'], true)) {
             $addPoints($catB_Score, $catB_Details, 'Refresh Rate', 3, '120Hz (+3)');
        }

        // 8. Peak Brightness
        if (self::checkSpecs($dispFeat, ['/4\d{3}\s*nits/i'], true)) { // >4000
             $addPoints($catB_Score, $catB_Details, 'Brightness', 10, '>4000 nits (+10)');
        } elseif (self::checkSpecs($dispFeat, ['/[2-3]\d{3}\s*nits/i'], true)) { // 2000-3999
             $addPoints($catB_Score, $catB_Details, 'Brightness', 5, '>2000 nits (+5)');
        } else {
             $addPoints($catB_Score, $catB_Details, 'Brightness', 0, 'Standard (<2000) (+0)');
        }

        // 9. Resolution
        // 1440p / 1.5K / 2K check.
        if (self::checkSpecs($dispRes, ['/1[2-4]\d{2}\s*x/i', '/3168/i', '/1440\s*x/i'], true)) {
             $addPoints($catB_Score, $catB_Details, 'Resolution', 5, '1.5K/2K (+5)');
        } else {
             $addPoints($catB_Score, $catB_Details, 'Resolution', 0, 'FHD+ (+0)');
        }

        // 10. Eye Care (PWM)
        // Check features for Hz > 2160
        if (self::checkSpecs($dispFeat, ['2160Hz', '2880Hz', '3840Hz', '4320Hz'])) {
             $addPoints($catB_Score, $catB_Details, 'Eye Care', 5, 'High PWM Dimming (+5)');
        } else {
             $addPoints($catB_Score, $catB_Details, 'Eye Care', 0, 'Standard PWM (+0)');
        }

        // 11. Color Depth
        if (self::checkSpecs($dispType, ['1B colors', '10-bit', '12-bit', '68B colors'])) {
             $addPoints($catB_Score, $catB_Details, 'Color Depth', 5, '10/12-bit (+5)');
        }

        // 12. Screen-to-Body
        $addPoints($catB_Score, $catB_Details, 'S2B Ratio', 5, '>90% (Est) (+5)');

        $breakdown['Display Tech'] = ['score' => min($catB_Score, 30), 'max' => 30, 'details' => $catB_Details];
        $totalScore += min($catB_Score, 30);


        // --- C. Processing & Memory (30 pts) ---
        $catC_Score = 0;
        $catC_Details = [];
        $chipset = $phone->platform?->chipset ?? '';
        $ram = $phone->platform?->ram ?? '';
        $storage = $phone->platform?->storage_type ?? '';
        $card = $phone->platform?->memory_card_slot ?? '';

        // 13. Processor Tier
        if (self::checkSpecs($chipset, ['Snapdragon 8 Elite', 'SD 8 Elite', 'Dimensity 9400', 'Gen 4', 'Gen 5'])) {
             $addPoints($catC_Score, $catC_Details, 'Processor', 10, 'Elite/9400 Tier (+10)');
        } elseif (self::checkSpecs($chipset, ['Gen 3', 'Snapdragon 8s Gen 3', 'SD 8 Gen 3', 'Dimensity 9300'])) {
             $addPoints($catC_Score, $catC_Details, 'Processor', 5, 'Gen 3 Tier (+5)');
        }

        // 14. Cooling System
        $addPoints($catC_Score, $catC_Details, 'Cooling', 5, 'Vapor Chamber (+5)');

        // 15. RAM Tech
        $addPoints($catC_Score, $catC_Details, 'RAM Tech', 3, 'LPDDR5X (+3)');

        // 16. Storage Tech
        if (self::checkSpecs($storage, ['UFS 4'])) {
             $addPoints($catC_Score, $catC_Details, 'Storage Tech', 5, 'UFS 4.0/4.1 (+5)');
        }

        // 17. SD Card
        if (self::checkSpecs($card, ['microSD'])) {
             $addPoints($catC_Score, $catC_Details, 'SD Slot', 5, 'Available (+5)');
        } else {
             $addPoints($catC_Score, $catC_Details, 'SD Slot', 0, 'No Slot (+0)');
        }

        // 18. RAM Options
        if (self::checkSpecs($ram, ['/16\s*GB/i', '/24\s*GB/i'], true)) {
             $addPoints($catC_Score, $catC_Details, 'RAM Options', 5, '16GB/24GB Variants (+5)');
        }

        $breakdown['Processing & Memory'] = ['score' => min($catC_Score, 30), 'max' => 30, 'details' => $catC_Details];
        $totalScore += min($catC_Score, 30);


        // --- D. Power & Charging (30 pts) ---
        $catD_Score = 0;
        $catD_Details = [];
        $battType = $phone->battery?->battery_type ?? '';
        $wired = $phone->battery?->charging_wired ?? '';
        $wireless = $phone->battery?->charging_wireless ?? '';
        $reverse = $phone->battery?->charging_reverse ?? '';

        // 19. Capacity
        if (self::checkSpecs($battType, ['/[7-9]\d{3}/'], true)) { // >7000
             $addPoints($catD_Score, $catD_Details, 'Capacity', 10, '>7000mAh (+10)');
        } elseif (self::checkSpecs($battType, ['/[5-6]\d{3}/'], true)) { // >5000
             $addPoints($catD_Score, $catD_Details, 'Capacity', 5, '>5000mAh (+5)');
        }

        // 20. Wired Speed
        if (self::checkSpecs($wired, ['/(1\d{2}|2\d{2})\s*W/i'], true)) { // >100W, allowing space
             $addPoints($catD_Score, $catD_Details, 'Wired Speed', 5, '>100W (+5)');
        }

        // 21. Wireless
        if (self::checkSpecs($wireless, ['/([5-9]\d|1\d{2})\s*W/i'], true)) { // >50W
             $addPoints($catD_Score, $catD_Details, 'Wireless', 5, '>50W (+5)');
        } elseif (!self::checkSpecs($wireless, ['No']) && !empty($wireless)) {
             $addPoints($catD_Score, $catD_Details, 'Wireless', 3, 'Available (+3)');
        }

        // 22. Reverse Wireless
        if (!self::checkSpecs($reverse, ['No']) && !empty($reverse)) {
             $addPoints($catD_Score, $catD_Details, 'Reverse Wireless', 5, 'Available (+5)');
        }

        // 23. Bypass Charging
        if (self::checkSpecs($wired, ['/(1\d{2}|2\d{2})\s*W/i'], true)) {
            $addPoints($catD_Score, $catD_Details, 'Bypass Charging', 5, 'Supported (+5)');
        }
        
        // 24. Reverse Wired
        $usb = $phone->connectivity?->usb ?? '';
        if (self::checkSpecs($usb, ['3.'])) {
             $addPoints($catD_Score, $catD_Details, 'Reverse Wired', 5, 'Supported (+5)');
        }

        $breakdown['Power & Charging'] = ['score' => min($catD_Score, 30), 'max' => 30, 'details' => $catD_Details];
        $totalScore += min($catD_Score, 30);


        // --- E. Camera Mastery (30 pts) ---
        $catE_Score = 0;
        $catE_Details = [];
        $mainCam = $phone->camera?->main_camera_specs ?? '';
        $mainFeat = $phone->camera?->main_camera_features ?? '';
        $mainVideo = $phone->camera?->main_video_capabilities ?? '';
        $selfieFeat = $phone->camera?->selfie_camera_features ?? '';

        // 25. Main Sensor Size
        $addPoints($catE_Score, $catE_Details, 'Sensor Size', 5, 'Large/1-inch Type (+5)');

        // 26. Zoom Hardware
        if (self::checkSpecs($mainCam, ['periscope'])) {
             $addPoints($catE_Score, $catE_Details, 'Zoom', 5, 'Periscope Telephoto (+5)');
        }

        // 27. Video Max
        if (self::checkSpecs($mainVideo, ['4K@120'])) {
             $addPoints($catE_Score, $catE_Details, 'Video Max', 5, '4K/120fps (+5)');
        } elseif (self::checkSpecs($mainVideo, ['8K'])) {
             $addPoints($catE_Score, $catE_Details, 'Video Max', 2, '8K Support (+2)');
        }

        // 28. Front AF
         $addPoints($catE_Score, $catE_Details, 'Front AF', 3, 'Autofocus (+3)');

        // 29. Additional Sensors
        if (self::checkSpecs($mainFeat, ['spectrum', 'color'])) {
             $addPoints($catE_Score, $catE_Details, 'Extra Sensors', 5, 'Color Spectrum (+5)');
        }

        // 30. OIS
        $addPoints($catE_Score, $catE_Details, 'OIS', 5, 'Multi-lens OIS (+5)');

        $breakdown['Camera Mastery'] = ['score' => min($catE_Score, 30), 'max' => 30, 'details' => $catE_Details];
        $totalScore += min($catE_Score, 30);


        // --- F. Connectivity & Ports (25 pts) ---
        $catF_Score = 0;
        $catF_Details = [];
        $wlan = $phone->connectivity?->wlan ?? '';
        $bt = $phone->connectivity?->bluetooth ?? '';
        $nfc = $phone->connectivity?->nfc ?? '';
        $infra = $phone->connectivity?->infrared ?? '';

        // 31. USB Speed
        if (self::checkSpecs($usb, ['3.'])) {
             $addPoints($catF_Score, $catF_Details, 'USB Speed', 5, 'USB 3.2 (+5)');
        }

        // 32. Video Out
        if (self::checkSpecs($usb, ['3.'])) {
             $addPoints($catF_Score, $catF_Details, 'Video Out', 5, 'DisplayPort Alt (+5)');
        }

        // 33. IR Blaster
        if (self::checkSpecs($infra, ['Yes'])) {
             $addPoints($catF_Score, $catF_Details, 'IR Blaster', 5, 'Included (+5)');
        }

        // 34. NFC
        if (self::checkSpecs($nfc, ['Yes'])) {
             $addPoints($catF_Score, $catF_Details, 'NFC', 2, 'Included (+2)');
        }

        // 35. BT/Wi-Fi
        if (self::checkSpecs($wlan, ['Wi-Fi 7']) || self::checkSpecs($bt, ['6.0'])) {
             $addPoints($catF_Score, $catF_Details, 'Wireless', 3, 'Wi-Fi 7 / BT 6.0 (+3)');
        }

        // 36. Satellite
        if (self::checkSpecs($phone->connectivity?->positioning ?? '', ['Satellite'])) {
             $addPoints($catF_Score, $catF_Details, 'Satellite', 5, 'Supported (+5)');
        }

        $breakdown['Connectivity'] = ['score' => min($catF_Score, 25), 'max' => 25, 'details' => $catF_Details];
        $totalScore += min($catF_Score, 25);


        // --- G. Audio & Extras (15 pts) ---
        $catG_Score = 0;
        $catG_Details = [];
        $jack = $phone->connectivity?->jack_3_5mm ?? '';
        $radio = $phone->connectivity?->radio ?? '';
        $sensors = $phone->connectivity?->sensors ?? '';

        // 37. Headphone Jack
        if (self::checkSpecs($jack, ['Yes'])) {
             $addPoints($catG_Score, $catG_Details, '3.5mm Jack', 5, 'Included (+5)');
        }

        // 38. FM Radio
        if (self::checkSpecs($radio, ['Yes'])) {
             $addPoints($catG_Score, $catG_Details, 'FM Radio', 5, 'Included (+5)');
        }

        // 39. Fingerprint
        if (self::checkSpecs($sensors, ['ultrasonic'])) {
             $addPoints($catG_Score, $catG_Details, 'Fingerprint', 5, 'Ultrasonic (+5)');
        } elseif (self::checkSpecs($sensors, ['fingerprint'])) {
             $addPoints($catG_Score, $catG_Details, 'Fingerprint', 3, 'Optical (+3)');
        }

        // 40. Haptics
        $addPoints($catG_Score, $catG_Details, 'Haptics', 5, 'X-axis Motor (+5)');

        $breakdown['Audio & Extras'] = ['score' => min($catG_Score, 15), 'max' => 15, 'details' => $catG_Details];
        $totalScore += min($catG_Score, 15);


        // --- H. Developer Freedom & Emulation (55 pts) ---
        $catH_Score = 0;
        $catH_Details = [];
        $bootloader = $phone->platform?->bootloader_unlockable ?? false;
        $turnipLevel = $phone->platform?->turnip_support_level ?? '';
        $osOpenness = $phone->platform?->os_openness ?? '';
        $gpuTier = $phone->platform?->gpu_emulation_tier ?? '';
        $romSupport = $phone->platform?->custom_rom_support ?? '';


        // 41. Bootloader Unlockable
        if ($bootloader) {
             $addPoints($catH_Score, $catH_Details, 'Bootloader', 10, 'Official Unlock (+10)');
        } else {
             $addPoints($catH_Score, $catH_Details, 'Bootloader', 0, 'No Official Unlock (+0)');
        }

        // 42. OS Openness
        if (self::checkSpecs($osOpenness, ['Near-AOSP', 'Pixel', 'Nothing'])) {
             $addPoints($catH_Score, $catH_Details, 'OS Openness', 10, 'Near-AOSP / Easy Root (+10)');
        } elseif (self::checkSpecs($osOpenness, ['Moderate'])) {
             $addPoints($catH_Score, $catH_Details, 'OS Openness', 5, 'Moderately Restricted (+5)');
        } else {
             $addPoints($catH_Score, $catH_Details, 'OS Openness', 0, 'Restricted OEM Skin (+0)');
        }

        // 43. Turnip / Mesa Support
        if (self::checkSpecs($turnipLevel, ['Full'])) {
             $addPoints($catH_Score, $catH_Details, 'Turnip Support', 20, 'Full Latest Mesa Support (+20)');
        } elseif (self::checkSpecs($turnipLevel, ['Stable'])) {
             $addPoints($catH_Score, $catH_Details, 'Turnip Support', 15, 'Stable / Outdated (+15)');
        } elseif (self::checkSpecs($turnipLevel, ['Partial'])) {
             $addPoints($catH_Score, $catH_Details, 'Turnip Support', 8, 'Partial / Unofficial (+8)');
        } else {
             $addPoints($catH_Score, $catH_Details, 'Turnip Support', 0, 'Not Supported (+0)');
        }

        // 44. GPU Emulation Tier
        if (self::checkSpecs($gpuTier, ['Adreno 8', 'Elite', 'Adreno 825'])) {
             $addPoints($catH_Score, $catH_Details, 'Emulation Tier', 20, 'Adreno 8xx Elite (+20)');
        } elseif (self::checkSpecs($gpuTier, ['Adreno 7'])) {
             $addPoints($catH_Score, $catH_Details, 'Emulation Tier', 16, 'Adreno 7xx (+16)');
        } elseif (self::checkSpecs($gpuTier, ['Adreno 6'])) {
             $addPoints($catH_Score, $catH_Details, 'Emulation Tier', 10, 'Adreno 6xx (+10)');
        } elseif (self::checkSpecs($gpuTier, ['Immortalis'])) {
             $addPoints($catH_Score, $catH_Details, 'Emulation Tier', 14, 'Immortalis High-Tier (+14)');
        } elseif (self::checkSpecs($gpuTier, ['Mali Valhall'])) {
             $addPoints($catH_Score, $catH_Details, 'Emulation Tier', 10, 'Mali Valhall (+10)');
        } elseif (self::checkSpecs($gpuTier, ['Mali'])) {
             $addPoints($catH_Score, $catH_Details, 'Emulation Tier', 6, 'Older Mali (+6)');
        }

        // 45. Custom ROM Support
        if (self::checkSpecs($romSupport, ['Major'])) {
             $addPoints($catH_Score, $catH_Details, 'Custom ROMs', 10, 'Major Active Ecosystem (+10)');
        } elseif (self::checkSpecs($romSupport, ['Limited'])) {
             $addPoints($catH_Score, $catH_Details, 'Custom ROMs', 5, 'Limited/Unofficial (+5)');
        } else {
             $addPoints($catH_Score, $catH_Details, 'Custom ROMs', 0, 'None (+0)');
        }

        $breakdown['Developer Freedom'] = ['score' => min($catH_Score, 55), 'max' => 55, 'details' => $catH_Details];
        $totalScore += min($catH_Score, 55);


        // Calculate Percentage (New Total: 245)
        $percentage = ($totalScore / 245) * 100;

        return [
            'total_score' => $totalScore,
            'max_score' => 245,
            'percentage' => round($percentage, 1),
            'breakdown' => $breakdown
        ];
    }
}
