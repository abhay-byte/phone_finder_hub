<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$p = App\Models\Phone::with(['platform', 'camera', 'benchmarks'])->where('name', 'Poco X7 Pro')->first();

if (!$p) {
    echo "NOT_FOUND\n";
    exit(1);
}

echo 'ID: ' . $p->id . PHP_EOL;
echo 'UEPS: ' . $p->ueps_score . PHP_EOL;
echo 'FPI: ' . $p->overall_score . PHP_EOL;
echo 'Chipset: ' . ($p->platform->chipset ?? 'NULL') . PHP_EOL;
echo 'Sensors: ' . ($p->camera->main_camera_sensors ?? 'NULL') . PHP_EOL;
echo 'Apertures: ' . ($p->camera->main_camera_apertures ?? 'NULL') . PHP_EOL;
echo 'Focal: ' . ($p->camera->main_camera_focal_lengths ?? 'NULL') . PHP_EOL;
echo 'AnTuTu v11: ' . ($p->benchmarks->antutu_score ?? 'NULL') . PHP_EOL;
echo 'GB single: ' . ($p->benchmarks->geekbench_single ?? 'NULL') . PHP_EOL;
echo '3DMark WLE: ' . ($p->benchmarks->dmark_wild_life_extreme ?? 'NULL') . PHP_EOL;
