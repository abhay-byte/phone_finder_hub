<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Phone;

$phone = Phone::where('name', 'vivo T4 Ultra')->first();

if ($phone) {
    echo "Updating Video Specs for {$phone->name}...\n";
    $phone->camera->main_video_capabilities = '4K@60fps, 4K@30fps, 1080p@60fps, gyro-EIS';
    $phone->camera->save();
    
    // Recalculate
    $phone->updateScores();
    echo "Scores updated.\n";
    
    // Check new score
    $ueps = \App\Services\UepsScoringService::calculate($phone);
    $idx = array_search('Video Max', array_column($ueps['breakdown']['Camera Mastery']['details'], 'criterion'));
    $score = $ueps['breakdown']['Camera Mastery']['details'][$idx];
    echo "Video Score: {$score['points']} points ({$score['reason']})\n";
}
