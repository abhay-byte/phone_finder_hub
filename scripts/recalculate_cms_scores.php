<?php

require __DIR__ . '/../vendor/autoload.php';

use App\Models\Phone;
use App\Services\CmsScoringService;

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "🔄 Recalculating CMS scores for all phones...\n\n";

$phones = Phone::with(['camera', 'benchmarks'])->get();
$updated = 0;
$errors = 0;

foreach ($phones as $phone) {
    try {
        $result = CmsScoringService::calculate($phone);
        
        $phone->cms_score = $result['total_score'];
        $phone->cms_details = $result['breakdown'];
        $phone->save();
        
        $updated++;
        echo "✅ {$phone->name}: {$result['total_score']}/1330\n";
        
        // Show benchmark contribution if available
        if (isset($result['breakdown']['benchmarks'])) {
            $benchScore = $result['breakdown']['benchmarks']['score'];
            if ($benchScore > 0) {
                echo "   📊 Benchmarks: {$benchScore}/390\n";
            }
        }
        
    } catch (Exception $e) {
        $errors++;
        echo "❌ {$phone->name}: Error - {$e->getMessage()}\n";
    }
}

echo "\n" . str_repeat('=', 50) . "\n";
echo "📊 Summary:\n";
echo "   Total phones: " . $phones->count() . "\n";
echo "   Updated: {$updated}\n";
echo "   Errors: {$errors}\n";
echo "\n✨ CMS recalculation complete!\n";
