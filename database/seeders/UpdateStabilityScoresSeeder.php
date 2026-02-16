<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Phone;

class UpdateStabilityScoresSeeder extends Seeder
{
    public function run()
    {
        // Estimated Stability Scores (0-100) based on tech reviews
        // High stability = better sustained performance
        $stabilityMap = [
            'OnePlus 15' => 52,
            'OnePlus 13' => 51.7,
            'POCO X7 Pro' => 55.9,
            'Xiaomi Poco F7' => 78.6,
            'Nothing Phone (3)' => 61.9,
            'Motorola Edge 60 Pro' => 42.1,
            'POCO X6 Pro' => 65, 
            'vivo iQOO 15' => 65.8,
            'Nothing Phone (3a)' => 99.8,
            'Vivo V60' => 78.5,
            'OnePlus 13R' => 55.5, // Updated
            'OnePlus 15R' => 74.6, // New
            
            // Previous Defaults (Keep if needed or overwrite)
            'OnePlus 12' => 72, 
            'vivo X100 Pro' => 68,
            'iQOO 12' => 88,
            'Samsung Galaxy S24' => 58,
            'Redmi Note 13 Pro+' => 90,
            'vivo iQOO Neo 10' => 85,
            'RedMagic' => 99,
            'ROG Phone' => 96,
            'iPhone 15 Pro' => 65,
            'Pixel 8' => 55,
            'Xiaomi 14' => 65,
        ];

        foreach ($stabilityMap as $name => $score) {
             // Use LIKE to match partial names if needed
             $phones = Phone::where('name', 'LIKE', "%$name%")->get();
             
             foreach ($phones as $phone) {
                 if ($phone->benchmarks) {
                     $phone->benchmarks->update([
                         'dmark_wild_life_stress_stability' => round((float)$score)
                     ]);
                     $this->command->info("Updated stability for {$phone->name}: $score%");
                 }
             }
        }
    }
}
