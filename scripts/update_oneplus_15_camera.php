<?php

use App\Models\Phone;
use App\Models\SpecCamera;

$phone = Phone::where('name', 'OnePlus 15')->first();

if (!$phone) {
    echo "OnePlus 15 not found!\n";
    exit(1);
}

echo "Updating camera details for {$phone->name}...\n";

SpecCamera::updateOrCreate(['phone_id' => $phone->id], [
    'main_camera_apertures' => 'f/1.8 (main), f/2.6 (tele), f/2.2 (ultrawide)',
    'main_camera_focal_lengths' => '24mm (main), 70mm (tele), 14mm (ultrawide)',
]);

echo "Done.\n";
