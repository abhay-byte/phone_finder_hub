<?php

use App\Models\Phone;
use App\Models\SpecPlatform;
use App\Models\SpecConnectivity;

$phone = Phone::where('name', 'LIKE', '%OnePlus 13R%')->first();

if (!$phone) {
    echo "OnePlus 13R not found!\n";
    exit(1);
}

echo "Updating details for {$phone->name} (ID: {$phone->id})...\n";

// Connectivity - Network Bands
SpecConnectivity::updateOrCreate(['phone_id' => $phone->id], [
    'network_bands' => '5G / LTE / HSPA / GSM',
]);

// Platform - Developer Stats
SpecPlatform::updateOrCreate(['phone_id' => $phone->id], [
    'bootloader_unlockable' => true,
    'os_openness' => 'Near-AOSP / Minimal restrictions / Easy root',
    'turnip_support_level' => 'Full',
    'gpu_emulation_tier' => 'Adreno 8xx Elite-class', // Assuming 8 Gen 4/5 or similar high-end
    'custom_rom_support' => 'Major',
]);

echo "Done.\n";
