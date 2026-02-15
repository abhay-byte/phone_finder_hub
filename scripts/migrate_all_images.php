<?php

use App\Models\Phone;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;


echo "Starting image migration...\n";

// Ensure directory exists
if (!file_exists(storage_path('app/public/phones'))) {
    mkdir(storage_path('app/public/phones'), 0755, true);
}

// Find phones with external images
$phones = Phone::where('image_url', 'like', 'http%')
    ->where('image_url', 'not like', '%localhost%')
    ->where('image_url', 'not like', '%phone-finder-shjs.onrender.com%') // in case some point to old production
    ->get();

echo "Found " . $phones->count() . " phones with external images.\n";

foreach ($phones as $phone) {
    echo "Processing: " . $phone->name . "...\n";
    $url = $phone->image_url;
    
    try {
        // Generate filename
        $slug = Str::slug($phone->name);
        $extension = pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION) ?: 'png';
        // Handle query params or weird extensions
        if (strlen($extension) > 4 || empty($extension)) {
             $extension = 'png';
        }
        
        $filename = $slug . '.' . $extension;
        $localPath = 'phones/' . $filename;
        $fullPath = storage_path('app/public/' . $localPath);

        // Download
        $content = @file_get_contents($url);
        if ($content === false) {
             // Try curl as fallback
             $ch = curl_init($url);
             curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
             curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
             curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0');
             $content = curl_exec($ch);
             curl_close($ch);
        }

        if ($content) {
            file_put_contents($fullPath, $content);
            
            // Update DB
            $phone->image_url = '/storage/' . $localPath;
            $phone->save();
            
            echo "  -> Downloaded to: $localPath\n";
            echo "  -> Updated DB URL.\n";
        } else {
            echo "  -> FAILED to download image from: $url\n";
        }

    } catch (\Exception $e) {
        echo "  -> ERROR: " . $e->getMessage() . "\n";
    }
}

echo "Migration completed.\n";
