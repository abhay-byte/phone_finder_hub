<?php

require __DIR__ . '/../vendor/autoload.php';

use App\Models\Phone;

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$phones = ['OnePlus 13R', 'OnePlus 13', 'Poco X6 Pro', 'vivo V60'];

foreach ($phones as $name) {
    $phone = Phone::where('name', $name)->with(['camera'])->first();
    
    if (!$phone) {
        continue;
    }
    
    echo "\n" . str_repeat('=', 60) . "\n";
    echo "📱 {$phone->name}\n";
    echo str_repeat('=', 60) . "\n\n";
    
    if (isset($phone->cms_details['resolution'])) {
        $res = $phone->cms_details['resolution'];
        echo "🔬 RESOLUTION & BINNING (Max 50 pts)\n";
        echo "Score: {$res['score']} pts\n";
        echo str_repeat('-', 40) . "\n";
        
        foreach ($res['details'] as $detail) {
            // Ignore the "Average Score" label if I added it awkwardly
            if ($detail['criterion'] == 'Average Score') continue; 
            
            $name = str_pad($detail['criterion'], 20);
            echo "{$name} {$detail['points']} pts ({$detail['reason']})\n";
        }
        echo str_repeat('-', 40) . "\n";
    }
}
