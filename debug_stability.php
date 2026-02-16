<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$phone = App\Models\Phone::where('name', 'like', '%Poco X7 Pro%')->first();

if ($phone) {
    echo "Phone: " . $phone->name . "\n";
    echo "Benchmark ID: " . ($phone->benchmarks->id ?? 'N/A') . "\n";
    echo "Stability: " . ($phone->benchmarks->dmark_wild_life_stress_stability ?? 'NULL') . "\n";
    echo "GPX Score: " . $phone->gpx_score . "\n";
    echo "GPX Details: " . json_encode($phone->gpx_details) . "\n";
    
    // Test calculation
    $calc = $phone->calculateGPX();
    echo "Calculated GPX: " . $calc['score'] . "\n";
    echo "Calculated Details: " . json_encode($calc['details']) . "\n";
} else {
    echo "Phone not found.\n";
}
