<?php

require __DIR__ . '/../vendor/autoload.php';

use App\Models\Phone;
use App\Models\Benchmark;

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$updates = [
    'OnePlus 15R' => 21.36,
    'Poco X7 Pro' => 13.5,
    'Poco F7' => 18.5,
    'Motorola Edge 60 Pro' => 16.16,
    'OnePlus 13' => 15.25,
    'Nothing Phone (3)' => 12.56,
    'Nothing Phone (3a)' => 13.38,
    'vivo iQOO 15' => 21.47,
];

foreach ($updates as $name => $hours) {
    echo "📱 processing $name...\n";
    
    // Case-insensitive search
    $phone = Phone::whereRaw('LOWER(name) = ?', [strtolower($name)])->first();

    if (!$phone) {
        echo "❌ Phone '$name' not found!\n";
        continue;
    }

    echo "✅ Found {$phone->name} (ID: {$phone->id})\n";

    // Update or Create Benchmark entry
    if (!$phone->benchmarks) {
        echo "⚠️ Creating new benchmarks entry...\n";
        $phone->benchmarks()->create([
            'battery_endurance_hours' => $hours
        ]);
    } else {
        $phone->benchmarks->battery_endurance_hours = $hours;
        $phone->benchmarks->save();
    }
    
    echo "🔋 Updated Endurance Hours to: $hours\n";

    // Recalculate all scores (Endurance, Value, UEPS etc)
    echo "🔄 Recalculating scores...\n";
    $phone->updateScores();
    
    echo "🎉 New Endurance Score: {$phone->endurance_score}\n";
    echo "🎉 New Value Score: {$phone->value_score}\n";
    echo "----------------------------------------\n";
}

echo "Done!\n";
