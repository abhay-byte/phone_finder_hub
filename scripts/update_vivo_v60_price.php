<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$phone = App\Models\Phone::with(['body', 'platform', 'camera', 'connectivity', 'battery', 'benchmarks'])
    ->where('name', 'vivo V60')
    ->first();

if (!$phone) {
    echo "NOT_FOUND\n";
    exit(1);
}

$phone->price = 38999.00;
$phone->saveQuietly();

$ueps = App\Services\UepsScoringService::calculate($phone->fresh(['body', 'platform', 'camera', 'connectivity', 'battery', 'benchmarks']));
$fpi = $phone->fresh(['benchmarks'])->calculateFPI();

$phone->ueps_score = (int) round($ueps['total_score']);
$phone->overall_score = is_array($fpi) ? (int) round($fpi['total']) : $phone->overall_score;
$phone->saveQuietly();

echo 'UPDATED|ID:' . $phone->id . '|price:' . $phone->price . '|UEPS:' . $phone->ueps_score . '|FPI:' . $phone->overall_score . PHP_EOL;
