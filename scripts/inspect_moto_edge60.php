<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Phone;

$phones = Phone::where('name', 'LIKE', '%Edge 60 Pro%')->get();

foreach ($phones as $phone) {
    echo "ID: {$phone->id} | Name: {$phone->name} | Price: {$phone->price} | CMS Score: {$phone->cms_score}\n";
    if ($phone->benchmarks) {
        echo "  - Benchmarks: PA: {$phone->benchmarks->phonearena_camera_score}, DxO: {$phone->benchmarks->dxomark_score}, Other: {$phone->benchmarks->other_benchmark_score}\n";
    } else {
        echo "  - No Benchmarks\n";
    }
    
    // Check key specs affecting score
    $main = $phone->cameras()->where('type', 'main')->first();
    echo "  - Main Sensor: " . ($main ? $main->sensor_size : 'N/A') . "\n";
}
