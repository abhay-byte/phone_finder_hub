<?php

use App\Models\Phone;

// Poco F7 (ID 7)
$poco = Phone::where('name', 'Poco F7')->first();
if ($poco) {
    echo "Updating Poco F7...\n";
    $poco->platform->update([
        'os_details' => 'HyperOS 2.0 based on Android 15. Heavily customized with extensive features but some bloatware.',
        'turnip_support_level' => 'Full Support (Adreno 825)',
        'os_openness' => 'Searchable (Bootloader Unlockable)',
        'gpu_emulation_tier' => 'S-Tier (Snapdragon 8s Gen 4)',
        'custom_rom_support' => 'Excellent (Xiaomi community)',
    ]);
    
    $poco->battery->update([
        'charging_specs_detailed' => '90W Wired (0-100% in 35 min), PD 3.0',
        'reverse_wired' => 'Yes (22.5W)',
        'reverse_wireless' => 'No',
    ]);
    
    $poco->body->update([
        'display_brightness' => '3200 nits (peak)',
        'pwm_dimming' => '2160 Hz',
        'screen_glass' => 'Xiaomi Ceramic Guard / Gorilla Glass 7i',
        'screen_to_body_ratio' => '91.5%',
        'pixel_density' => '446 ppi',
        'touch_sampling_rate' => '480 Hz',
        'glass_protection_level' => 'High',
    ]);
    echo "Poco F7 Updated.\n";
}

// OnePlus 13 (ID 2 mostly)
$op13 = Phone::where('name', 'OnePlus 13')->first();
if ($op13) {
    echo "Updating OnePlus 13...\n";
    $op13->platform->update([
        'os_details' => 'OxygenOS 15 based on Android 15. Clean, fast, and feature-rich with typical OnePlus optimizations.',
        'turnip_support_level' => 'Full Support (Adreno 830)',
        'os_openness' => 'Moderate (Bootloader unlock supported)',
        'gpu_emulation_tier' => 'S+ Tier (Snapdragon 8 Elite)',
        'custom_rom_support' => 'Good',
    ]);

    $op13->battery->update([
        'charging_specs_detailed' => '100W SuperVOOC, 50W AirVOOC',
        'reverse_wired' => 'Yes',
        'reverse_wireless' => 'Yes (10W)',
    ]);

    $op13->body->update([
        'display_brightness' => '4500 nits (peak)',
        'pwm_dimming' => '2160 Hz',
        'screen_glass' => 'Crystal Shield Super Ceramic',
        'screen_to_body_ratio' => '93.1%',
        'pixel_density' => '510 ppi',
        'touch_sampling_rate' => '240 Hz',
        'glass_protection_level' => 'Very High',
    ]);
    echo "OnePlus 13 Updated.\n";
}

// OnePlus 15 (ID 4 mostly)
$op15 = Phone::where('name', 'OnePlus 15')->first();
if ($op15) {
    echo "Updating OnePlus 15...\n";
    $op15->platform->update([
        'os_details' => 'OxygenOS 16 based on Android 16. Next-gen AI features and fluid animations.',
        'turnip_support_level' => 'Full Support (Adreno 840)',
        'os_openness' => 'Moderate',
        'gpu_emulation_tier' => 'God Tier (Snapdragon 8 Elite Gen 2)',
        'custom_rom_support' => 'Moderate',
    ]);

    $op15->battery->update([
        'charging_specs_detailed' => '120W SuperVOOC, 50W AirVOOC',
        'reverse_wired' => 'Yes',
        'reverse_wireless' => 'Yes (10W)',
    ]);

    $op15->body->update([
        'display_brightness' => '5000 nits (peak)',
        'pwm_dimming' => '3840 Hz',
        'screen_glass' => 'Crystal Shield Ultra',
        'screen_to_body_ratio' => '94.0%',
        'pixel_density' => '520 ppi',
        'touch_sampling_rate' => '360 Hz',
        'glass_protection_level' => 'Elite',
    ]);
    echo "OnePlus 15 Updated.\n";
}
