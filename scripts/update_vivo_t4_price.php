<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Phone;

$phone = Phone::where('name', 'vivo T4 Ultra')->first();

if ($phone) {
    echo "Updating Price for {$phone->name}...\n";
    echo "Old Price: {$phone->price}\n";
    
    $phone->price = 35999.00;
    $phone->amazon_price = 35999.00;
    $phone->flipkart_price = 35999.00;
    $phone->save();
    
    echo "New Price: {$phone->price}\n";
    
    // Recalculate Value score
    $phone->updateScores();
    echo "Scores updated. Value Score: {$phone->value_score}\n";
} else {
    echo "Phone not found!\n";
}
