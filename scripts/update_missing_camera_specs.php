<?php

use App\Models\Phone;
use App\Models\SpecCamera;

$updates = [
    'Poco F7' => [
        'main_camera_sensors' => 'Main: 1/1.95", UW: 1/4.0"',
        'main_camera_apertures' => 'f/1.6 (main), f/2.2 (ultrawide)',
        'main_camera_focal_lengths' => '26mm (main), 16mm (ultrawide)',
        'main_camera_ois' => 'Yes (Main)',
    ],
    'OnePlus 13' => [
        'main_camera_sensors' => 'Main: 1/1.4", Tele: 1/1.95", UW: 1/2.76"',
        'main_camera_apertures' => 'f/1.6 (main), f/2.6 (tele), f/2.0 (ultrawide)',
        'main_camera_focal_lengths' => '23mm (main), 73mm (tele), 15mm (ultrawide)',
        'main_camera_ois' => 'Yes (Main & Tele)',
    ]
];

foreach ($updates as $name => $specs) {
    $phone = Phone::where('name', 'LIKE', "%$name%")->first();
    
    if ($phone) {
        echo "Found {$phone->name} (ID: {$phone->id})\n";
        
        $camera = SpecCamera::firstOrNew(['phone_id' => $phone->id]);
        
        foreach ($specs as $key => $value) {
            $camera->$key = $value;
            echo "  - Updated $key: $value\n";
        }
        
        $camera->save();
        echo "Saved camera specs for {$phone->name}.\n";
    } else {
        echo "Could not find phone matching: $name\n";
    }
}

echo "Done.\n";
