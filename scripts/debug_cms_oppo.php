<?php

use App\Models\Phone;
use App\Services\CmsScoringService;

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$phone = Phone::where('name', 'Oppo Find X9 Pro')->first();

if (!$phone) {
    echo "Phone not found!\n";
    exit;
}

echo "Phone: " . $phone->name . "\n";
echo "Other Benchmark Score (DB): " . ($phone->benchmarks->other_benchmark_score ?? 'NULL') . "\n";

$result = CmsScoringService::calculate($phone);

echo "CMS Total: " . $result['total_score'] . "\n";
echo "Benchmarks Breakdown:\n";
print_r($result['breakdown']['benchmarks']);
