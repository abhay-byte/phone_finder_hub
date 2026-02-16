<?php

/**
 * Populate cooling_type field for existing phones with educated guesses
 * Run with: php scripts/populate_cooling_types.php
 */

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Phone;

echo "Populating cooling_type for existing phones...\n\n";

$phones = Phone::with('body')->get();
$updated = 0;

foreach ($phones as $phone) {
    if (!$phone->body) {
        echo "⚠️  {$phone->name}: No body record\n";
        continue;
    }
    
    $name = strtolower($phone->name);
    $coolingType = null;
    
    // Active Fan (20 pts) - Gaming phones with active cooling
    if (str_contains($name, 'rog') || str_contains($name, 'redmagic') || str_contains($name, 'black shark')) {
        $coolingType = 'Active Fan';
    }
    // Vapor Chamber (15 pts) - Flagships and gaming-focused phones
    elseif (str_contains($name, 'iqoo') || 
            str_contains($name, 'oneplus') || 
            str_contains($name, 'gt') ||
            str_contains($name, 'pro') ||
            str_contains($name, 'ultra') ||
            str_contains($name, 'nothing phone')) {
        $coolingType = 'Vapor Chamber';
    }
    // Graphite (5 pts) - Mid-range and budget phones
    else {
        $coolingType = 'Graphite';
    }
    
    $phone->body->cooling_type = $coolingType;
    $phone->body->save();
    $updated++;
    
    echo "✓ {$phone->name}: {$coolingType}\n";
}

echo "\n✅ Updated {$updated} phones with cooling_type data\n";
echo "\nNext steps:\n";
echo "1. Run: php artisan phone:recalculate-scores\n";
echo "2. Run: php artisan cache:clear\n";
