<?php

use App\Models\Phone;

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$phone = Phone::where('name', 'vivo iQOO Neo 10')->first();
if ($phone) {
    echo "Specs: " . $phone->camera->main_camera_specs . "\n";
    echo "Sensors: " . $phone->camera->main_camera_sensors . "\n";
    echo "Apertures: " . $phone->camera->main_camera_apertures . "\n";
}
