<?php

use App\Models\Phone;
use App\Models\SpecConnectivity;

$phonesToUpdate = [
    'OnePlus 13',
    'vivo iQOO 15',
    'OnePlus 15',
    'OnePlus 15R'
];

$technology = '5G / LTE / HSPA / GSM';

foreach ($phonesToUpdate as $name) {
    $phone = Phone::where('name', 'LIKE', "%$name%")->first();
    
    if ($phone) {
        echo "Found {$phone->name} (ID: {$phone->id})\n";
        
        $connectivity = SpecConnectivity::firstOrNew(['phone_id' => $phone->id]);
        $connectivity->network_bands = $technology;
        $connectivity->save();
        
        echo "  - Updated network_bands to: $technology\n";
    } else {
        echo "Could not find phone matching: $name\n";
    }
}

echo "Done.\n";
