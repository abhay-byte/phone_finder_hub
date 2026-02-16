<?php

use App\Models\Phone;

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$phones = Phone::all(['id', 'name']);
foreach ($phones as $phone) {
    echo "{$phone->name}\n";
}
