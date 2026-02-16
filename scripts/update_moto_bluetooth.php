<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Phone;

$phone = Phone::where('name', 'Motorola Edge 60 Pro')->first();

if ($phone) {
    echo "Updating Bluetooth for {$phone->name}...\n";
    echo "Old value: " . ($phone->connectivity->bluetooth ?? 'NULL') . "\n";
    
    if ($phone->connectivity) {
        $phone->connectivity->update([
            'bluetooth' => '5.4'
        ]);
        echo "New value: 5.4\n";
    } else {
        echo "No connectivity record found!\n";
    }
} else {
    echo "Phone not found!\n";
}
