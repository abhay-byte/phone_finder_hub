<?php

use App\Http\Controllers\PhoneController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Mock Request
$request = Request::create('/rankings', 'GET', ['tab' => 'cms']);

// Instantiate Controller
$controller = new PhoneController();

try {
    echo "Testing CMS Rankings Page Generation...\n";
    
    // Clear cache to ensure we get fresh data
    Cache::forget('rankings_cms_cms_score_desc_1_html');
    
    $response = $controller->rankings($request);
    $html = $response->getContent();
    
    // Checks
    $checks = [
        'Camera Mastery Score (CMS-1330)' => false,
        'CMS Score' => false,
        'DxOMark' => false,
        'PhoneArena' => false,
        '/1330' => false,
        'OnePlus 13' => false, // Should be present
        'vivo V60' => false // Should be present
    ];
    
    foreach ($checks as $term => &$found) {
        if (strpos($html, $term) !== false) {
            $found = true;
        }
    }
    
    $allPassed = true;
    foreach ($checks as $term => $found) {
        if ($found) {
            echo "[PASS] Found '$term'\n";
        } else {
            echo "[FAIL] Missing '$term'\n";
            $allPassed = false;
        }
    }
    
    if ($allPassed) {
        echo "\nSUCCESS: CMS Rankings Page rendered correctly.\n";
    } else {
        echo "\nFAILURE: Some elements are missing.\n";
    }
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
}
