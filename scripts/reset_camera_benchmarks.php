<?php

require __DIR__ . '/../vendor/autoload.php';

use App\Models\Phone;
use Illuminate\Support\Facades\DB;

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "🔄 Setting all camera benchmark scores to NULL...\n\n";

// Update all benchmarks to set camera scores to NULL
$updated = DB::table('benchmarks')->update([
    'dxomark_score' => null,
    'phonearena_camera_score' => null
]);

echo "✅ Updated {$updated} benchmark records\n";

echo "\n🔄 Recalculating CMS scores without benchmarks...\n\n";

$phones = Phone::with(['camera', 'benchmarks'])->get();
$recalculated = 0;

foreach ($phones as $phone) {
    try {
        $result = \App\Services\CmsScoringService::calculate($phone);
        
        $phone->cms_score = $result['total_score'];
        $phone->cms_details = $result['breakdown'];
        $phone->save();
        
        $recalculated++;
        $benchScore = $result['breakdown']['benchmarks']['score'] ?? 0;
        echo "✅ {$phone->name}: {$result['total_score']}/1330 (Benchmarks: {$benchScore}/390)\n";
        
    } catch (Exception $e) {
        echo "❌ {$phone->name}: Error - {$e->getMessage()}\n";
    }
}

echo "\n" . str_repeat('=', 50) . "\n";
echo "📊 Summary:\n";
echo "   Benchmark records updated: {$updated}\n";
echo "   Phones recalculated: {$recalculated}\n";
echo "\n✨ All camera benchmarks set to NULL!\n";
echo "💡 Benchmark contribution is now 0/390 for all phones.\n";
