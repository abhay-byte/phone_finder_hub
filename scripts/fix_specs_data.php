<?php

use App\Models\Phone;
use App\Models\SpecBody;
use App\Models\SpecPlatform;

// 1. Fix OnePlus 15 (ID 3) Display Features
$p15 = Phone::find(3);
if ($p15 && $p15->body) {
    echo "Fixing OnePlus 15 Display Features...\n";
    // Replace '120Hz' with 'Adaptive LTPO 1–165Hz' or just remove 120Hz if 165Hz is elsewhere
    // User said: "Adaptive LTPO 1–165Hz" and "Remove the 120Hz line entirely".
    // Current string: '120Hz, Dolby Vision, HDR10+, HDR Vivid, Ultra HDR, 1B colors'
    $newFeatures = 'Adaptive LTPO 1–165Hz, Dolby Vision, HDR10+, HDR Vivid, Ultra HDR, 1B colors';
    $p15->body->update(['display_features' => $newFeatures]);
    echo "Updated OP15 display_features.\n";
}

// 2. Fix CPU Names (Remove Code Names)
// OnePlus 13 (ID 1), OnePlus 15R (ID 2), OnePlus 15 (ID 3)
// ID 1: 'Qualcomm SM8750-AB Snapdragon 8 Elite (3 nm)' -> 'Snapdragon 8 Elite (3 nm)'
// ID 2: 'Qualcomm SM8845 Snapdragon 8 Gen 5 (3 nm)' -> 'Snapdragon 8 Gen 5 (3 nm)'
// ID 3: 'Qualcomm SM8850-AB Snapdragon 8 Elite (3 nm)' (Hypothetical, checking via update)

$ids = [1, 2, 3];
foreach ($ids as $id) {
    $phone = Phone::find($id);
    if ($phone && $phone->platform) {
        $chipset = $phone->platform->chipset;
        // Regex to remove "Qualcomm SMxxxx-xx " or "Qualcomm SMxxxx "
        $newChipset = preg_replace('/^Qualcomm\s+SM[A-Z0-9-]+\s+/', '', $chipset);
        
        if ($newChipset !== $chipset) {
            echo "Fixing Chipset for {$phone->name} (ID $id)...\n";
            echo "Old: $chipset\nNew: $newChipset\n";
            $phone->platform->update(['chipset' => $newChipset]);
        }
    }
}

echo "Data fix complete.\n";
