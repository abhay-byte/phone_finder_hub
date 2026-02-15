<?php

use App\Models\Phone;
use App\Models\Benchmark;

// 1. Poco F7 (ID 7) - Single Core: 2074
// 2. OnePlus 13 (ID 2) - Single Core: 3186
// 3. OnePlus 15 (ID 4) - Single Core: 3831

$updates = [
    'Poco F7' => 2074,
    'OnePlus 13' => 3186,
    'OnePlus 15' => 3831,
];

foreach ($updates as $name => $score) {
    $phone = Phone::where('name', 'LIKE', "%$name%")->first();
    
    if ($phone) {
        echo "Found {$phone->name} (ID: {$phone->id})\n";
        
        $benchmark = Benchmark::firstOrNew(['phone_id' => $phone->id]);
        $benchmark->geekbench_single = $score;
        $benchmark->save();
        
        echo "Updated Geekbench Single Core to: $score\n";
    } else {
        echo "Could not find phone matching: $name\n";
    }
}

echo "Done.\n";
