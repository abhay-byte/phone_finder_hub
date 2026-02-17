<?php

require __DIR__ . '/../vendor/autoload.php';

use App\Models\Phone;

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$phones = ['OnePlus 13', 'Poco X6 Pro'];

foreach ($phones as $name) {
    $phone = Phone::where('name', $name)->with(['camera', 'benchmarks'])->first();
    
    if ($phone) {
        echo "\n" . str_repeat('=', 70) . "\n";
        echo "📱 {$phone->name}\n";
        echo str_repeat('=', 70) . "\n";
        echo "Total CMS Score: {$phone->cms_score}/1330\n\n";
        
        if ($phone->cms_details && isset($phone->cms_details['resolution'])) {
            $res = $phone->cms_details['resolution'];
            echo "📐 RESOLUTION & BINNING: {$res['score']}/{$res['max']} points\n";
            echo str_repeat('-', 70) . "\n";
            
            foreach ($res['details'] as $detail) {
                printf("%-40s %3d pts - %s\n", 
                    $detail['criterion'], 
                    $detail['points'], 
                    $detail['reason']
                );
            }
            
            echo "\n📊 Camera Resolution Info:\n";
            echo str_repeat('-', 70) . "\n";
            if ($phone->camera) {
                echo "Main Camera Specs: " . ($phone->camera->main_camera_specs ?? 'N/A') . "\n";
                
                // Check for binning keywords
                $specs = strtolower($phone->camera->main_camera_specs ?? '');
                $features = strtolower($phone->camera->main_camera_features ?? '');
                $allSpecs = $specs . ' ' . $features;
                
                $binningKeywords = [];
                if (str_contains($allSpecs, 'quad bayer')) $binningKeywords[] = 'Quad Bayer';
                if (str_contains($allSpecs, 'tetra binning')) $binningKeywords[] = 'Tetra Binning';
                if (str_contains($allSpecs, 'pixel binning')) $binningKeywords[] = 'Pixel Binning';
                if (str_contains($allSpecs, '4-in-1')) $binningKeywords[] = '4-in-1';
                if (str_contains($allSpecs, '9-in-1')) $binningKeywords[] = '9-in-1';
                
                if (!empty($binningKeywords)) {
                    echo "Binning Technology: " . implode(', ', $binningKeywords) . " ✅\n";
                } else {
                    echo "Binning Technology: Not detected\n";
                }
            }
        }
        echo "\n";
    }
}
