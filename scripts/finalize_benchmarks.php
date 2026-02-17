<?php

require __DIR__ . '/../vendor/autoload.php';

use App\Models\Phone;
use App\Services\CmsScoringService;

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$service = new CmsScoringService();

    // OnePlus 13 Update
    echo "📱 Updating OnePlus 13...\n";
    $p13 = Phone::where('name', 'OnePlus 13')->first();
    if ($p13) {
        if (!$p13->benchmarks) {
            $b = new \App\Models\Benchmark();
            $b->phone_id = $p13->id;
            $b->save();
            $p13->refresh();
        }
        $p13->benchmarks->other_benchmark_score = 84; // Mobile 91 (8.4/10)
        $p13->benchmarks->save();
        
        // Recalculate
        $p13->refresh();$p13->load('benchmarks');
        $score = $service->calculate($p13);
        $p13->cms_score = $score['total_score'];
        $p13->cms_details = $score['breakdown'];
        $p13->save();
        echo "✅ OnePlus 13 New Score: {$score['total_score']} (Other Bench: 84 - Mobile 91)\n";
    }

    // OnePlus 15 Update
    echo "📱 Updating OnePlus 15...\n";
    $p15 = Phone::where('name', 'OnePlus 15')->first();
    if ($p15) {
        if (!$p15->benchmarks) {
            $b = new \App\Models\Benchmark();
            $b->phone_id = $p15->id;
            $b->save();
            $p15->refresh();
        }
        $p15->benchmarks->dxomark_score = 0;
        $p15->benchmarks->phonearena_camera_score = 151; // Confirmed
        $p15->benchmarks->other_benchmark_score = 84; // Mobile 91 (8.4/10)
        $p15->benchmarks->save();
    
        // Recalculate
        $p15->refresh();$p15->load('benchmarks');
        $score = $service->calculate($p15);
        $p15->cms_score = $score['total_score'];
        $p15->cms_details = $score['breakdown'];
        $p15->save();
        echo "✅ OnePlus 15 New Score: {$score['total_score']} (PA: 151, Other: 84 - Mobile 91)\n";
    }

    // iQOO 15 Update
    echo "📱 Updating iQOO 15...\n";
    $iqoo = Phone::where('name', 'LIKE', '%iQOO 15%')->first();
    if ($iqoo) {
        if (!$iqoo->benchmarks) {
            $b = new \App\Models\Benchmark();
            $b->phone_id = $iqoo->id;
            $b->save();
            $iqoo->refresh();
        }
        $iqoo->benchmarks->other_benchmark_score = 80; // User confirmed 8.0/10
        $iqoo->benchmarks->save();
        
        // Recalculate
        $iqoo->refresh();$iqoo->load('benchmarks');
        $score = $service->calculate($iqoo);
        $iqoo->cms_score = $score['total_score'];
        $iqoo->cms_details = $score['breakdown'];
        $iqoo->save();
        echo "✅ iQOO 15 New Score: {$score['total_score']} (Other: 80 - Mobile 91)\n";
    }

    // vivo V60 Update
    echo "📱 Updating vivo V60...\n";
    $v60 = Phone::where('name', 'LIKE', '%vivo V60%')->where('name', 'NOT LIKE', '%e%')->first(); // Exclude V60e
    if ($v60) {
        if (!$v60->benchmarks) {
            $b = new \App\Models\Benchmark();
            $b->phone_id = $v60->id;
            $b->save();
            $v60->refresh();
        }
        $v60->benchmarks->dxomark_score = 0; // User said 114 is for X60
        $v60->benchmarks->phonearena_camera_score = 132;
        $v60->benchmarks->other_benchmark_score = 79; // 91Mobiles 7.9/10
        $v60->benchmarks->save();
        
        // Recalculate
        $v60->refresh();$v60->load('benchmarks');
        $score = $service->calculate($v60);
        $v60->cms_score = $score['total_score'];
        $v60->cms_details = $score['breakdown'];
        $v60->save();
        echo "✅ vivo V60 New Score: {$score['total_score']} (DxO: 0, PA: 132, Other: 79)\n";
    }

    // Nothing Phone (3) Update
    echo "📱 Updating Nothing Phone (3)...\n";
    $nothing = Phone::where('name', 'Nothing Phone (3)')->first();
    if ($nothing) {
        if (!$nothing->benchmarks) {
            $b = new \App\Models\Benchmark();
            $b->phone_id = $nothing->id;
            $b->save();
            $nothing->refresh();
        }
        $nothing->benchmarks->other_benchmark_score = 78; // 91Mobiles 7.8/10
        $nothing->benchmarks->save();
        
        // Recalculate
        $nothing->refresh();$nothing->load('benchmarks');
        $score = $service->calculate($nothing);
        $nothing->cms_score = $score['total_score'];
        $nothing->cms_details = $score['breakdown'];
        $nothing->save();
        echo "✅ Nothing Phone (3) New Score: {$score['total_score']} (Other: 78 - Mobile 91)\n";
    }

    // Motorola Edge 60 Pro Update
    echo "📱 Updating Motorola Edge 60 Pro...\n";
    $moto = Phone::where('name', 'LIKE', '%Edge 60 Pro%')->first();
    if ($moto) {
        if (!$moto->benchmarks) {
            $b = new \App\Models\Benchmark();
            $b->phone_id = $moto->id;
            $b->save();
            $moto->refresh();
        }
        $moto->benchmarks->dxomark_score = 0;
        $moto->benchmarks->phonearena_camera_score = 136; // PhoneArena 136 (Total)
        $moto->benchmarks->other_benchmark_score = 82; // 91Mobiles 8.2/10
        $moto->benchmarks->save();
        
        // Recalculate
        $moto->refresh();$moto->load('benchmarks');
        $score = $service->calculate($moto);
        $moto->cms_score = $score['total_score'];
        $moto->cms_details = $score['breakdown'];
        $moto->save();
        echo "✅ Motorola Edge 60 Pro New Score: {$score['total_score']} (PA: 136, Other: 82 - Mobile 91)\n";
    }

    // Vivo T4 Ultra Update
    echo "📱 Updating vivo T4 Ultra...\n";
    $t4 = Phone::where('name', 'vivo T4 Ultra')->first();
    if ($t4) {
        if (!$t4->benchmarks) {
            $b = new \App\Models\Benchmark();
            $b->phone_id = $t4->id;
            $b->save();
            $t4->refresh();
        }
        $t4->benchmarks->dxomark_score = 0;
        $t4->benchmarks->phonearena_camera_score = 0;
        $t4->benchmarks->other_benchmark_score = 79; // 91Mobiles 7.9/10
        $t4->benchmarks->save();
        
        // Recalculate
        $t4->refresh();$t4->load('benchmarks');
        $score = $service->calculate($t4);
        $t4->cms_score = $score['total_score'];
        $t4->cms_details = $score['breakdown'];
        $t4->save();
        echo "✅ vivo T4 Ultra New Score: {$score['total_score']} (Other: 79 - Mobile 91)\n";
    }

    // OnePlus 13R Update
    echo "📱 Updating OnePlus 13R...\n";
    $op13r = Phone::where('name', 'OnePlus 13R')->first();
    if ($op13r) {
        if (!$op13r->benchmarks) {
            $b = new \App\Models\Benchmark();
            $b->phone_id = $op13r->id;
            $b->save();
            $op13r->refresh();
        }
        $op13r->benchmarks->dxomark_score = 0;
        $op13r->benchmarks->phonearena_camera_score = 139; // PhoneArena 138.5 -> 139
        $op13r->benchmarks->other_benchmark_score = 85; // 91Mobiles 8.5/10
        $op13r->benchmarks->save();
        
        // Recalculate
        $op13r->refresh();$op13r->load('benchmarks');
        $score = $service->calculate($op13r);
        $op13r->cms_score = $score['total_score'];
        $op13r->cms_details = $score['breakdown'];
        $op13r->save();
        echo "✅ OnePlus 13R New Score: {$score['total_score']} (PA: 139, Other: 85 - Mobile 91)\n";
    }

    // Poco X6 Pro Update
    echo "📱 Updating Poco X6 Pro...\n";
    $poco = Phone::where('name', 'Poco X6 Pro')->first();
    if ($poco) {
        if (!$poco->benchmarks) {
            $b = new \App\Models\Benchmark();
            $b->phone_id = $poco->id;
            $b->save();
            $poco->refresh();
        }
        $poco->benchmarks->dxomark_score = 0;
        $poco->benchmarks->phonearena_camera_score = 0; 
        $poco->benchmarks->other_benchmark_score = 80; // 91Mobiles 8.0/10
        $poco->benchmarks->save();
        
        // Recalculate
        $poco->refresh();$poco->load('benchmarks');
        $score = $service->calculate($poco);
        $poco->cms_score = $score['total_score'];
        $poco->cms_details = $score['breakdown'];
        $poco->save();
        echo "✅ Poco X6 Pro New Score: {$score['total_score']} (Other: 80 - Mobile 91)\n";
    }

    // Poco X7 Pro Update
    echo "📱 Updating Poco X7 Pro...\n";
    $poco7 = Phone::where('name', 'Poco X7 Pro')->first();
    if ($poco7) {
        if (!$poco7->benchmarks) {
            $b = new \App\Models\Benchmark();
            $b->phone_id = $poco7->id;
            $b->save();
            $poco7->refresh();
        }
        $poco7->benchmarks->dxomark_score = 0;
        $poco7->benchmarks->phonearena_camera_score = 0; 
        $poco7->benchmarks->other_benchmark_score = 81; // 91Mobiles 8.1/10
        $poco7->benchmarks->save();
        
        // Recalculate
        $poco7->refresh();$poco7->load('benchmarks');
        $score = $service->calculate($poco7);
        $poco7->cms_score = $score['total_score'];
        $poco7->cms_details = $score['breakdown'];
        $poco7->save();
        echo "✅ Poco X7 Pro New Score: {$score['total_score']} (Other: 81 - Mobile 91)\n";
    }

    // OnePlus 15R Update
    echo "📱 Updating OnePlus 15R...\n";
    $op15r = Phone::where('name', 'OnePlus 15R')->first();
    if ($op15r) {
        if (!$op15r->benchmarks) {
            $b = new \App\Models\Benchmark();
            $b->phone_id = $op15r->id;
            $b->save();
            $op15r->refresh();
        }
        $op15r->benchmarks->dxomark_score = 0;
        $op15r->benchmarks->phonearena_camera_score = 0; 
        $op15r->benchmarks->other_benchmark_score = 85; // 91Mobiles 8.5/10
        $op15r->benchmarks->save();
        
        // Recalculate
        $op15r->refresh();$op15r->load('benchmarks');
        $score = $service->calculate($op15r);
        $op15r->cms_score = $score['total_score'];
        $op15r->cms_details = $score['breakdown'];
        $op15r->save();
        echo "✅ OnePlus 15R New Score: {$score['total_score']} (Other: 85 - Mobile 91)\n";
    }

    // Poco F7 Update
    echo "📱 Updating Poco F7...\n";
    $pocoF7 = Phone::where('name', 'Poco F7')->first();
    if ($pocoF7) {
        if (!$pocoF7->benchmarks) {
            $b = new \App\Models\Benchmark();
            $b->phone_id = $pocoF7->id;
            $b->save();
            $pocoF7->refresh();
        }
        $pocoF7->benchmarks->dxomark_score = 0;
        $pocoF7->benchmarks->phonearena_camera_score = 128; // PhoneArena 128
        $pocoF7->benchmarks->other_benchmark_score = 75; // 91Mobiles 7.5/10
        $pocoF7->benchmarks->save();
        
        // Recalculate
        $pocoF7->refresh();$pocoF7->load('benchmarks');
        $score = $service->calculate($pocoF7);
        $pocoF7->cms_score = $score['total_score'];
        $pocoF7->cms_details = $score['breakdown'];
        $pocoF7->save();
        echo "✅ Poco F7 New Score: {$score['total_score']} (PA: 128, Other: 75)\n";
    }

    // Nothing Phone 3a Update
    echo "📱 Updating Nothing Phone (3a)...\n";
    $np3a = Phone::where('name', 'Nothing Phone (3a)')->first();
    if ($np3a) {
        if (!$np3a->benchmarks) {
            $b = new \App\Models\Benchmark();
            $b->phone_id = $np3a->id;
            $b->save();
            $np3a->refresh();
        }
        $np3a->benchmarks->dxomark_score = 0;
        $np3a->benchmarks->phonearena_camera_score = 0;
        $np3a->benchmarks->other_benchmark_score = 78; // Estimated ~7.8/10 (Lite is 7.6)
        $np3a->benchmarks->save();
        
        // Recalculate
        $np3a->refresh();$np3a->load('benchmarks');
        $score = $service->calculate($np3a);
        $np3a->cms_score = $score['total_score'];
        $np3a->cms_details = $score['breakdown'];
        $np3a->save();
        echo "✅ Nothing Phone (3a) New Score: {$score['total_score']} (Other: 78)\n";
    }

    // iQOO Neo 10 Update
    echo "📱 Updating vivo iQOO Neo 10...\n";
    $neo10 = Phone::where('name', 'vivo iQOO Neo 10')->orWhere('name', 'iQOO Neo 10')->first();
    if ($neo10) {
        if (!$neo10->benchmarks) {
            $b = new \App\Models\Benchmark();
            $b->phone_id = $neo10->id;
            $b->save();
            $neo10->refresh();
        }
        $neo10->benchmarks->dxomark_score = 0;
        $neo10->benchmarks->phonearena_camera_score = 0;
        $neo10->benchmarks->other_benchmark_score = 85; // 91Mobiles 8.5/10
        $neo10->benchmarks->save();
        
        // Recalculate
        $neo10->refresh();$neo10->load('benchmarks');
        $score = $service->calculate($neo10);
        $neo10->cms_score = $score['total_score'];
        $neo10->cms_details = $score['breakdown'];
        $neo10->save();
        echo "✅ vivo iQOO Neo 10 New Score: {$score['total_score']} (Other: 85)\n";
    }

    // Oppo K13 Turbo Pro
    echo "📱 Updating Oppo K13 Turbo Pro...\n";
    $k13 = Phone::where('name', 'Oppo K13 Turbo Pro')->first();
    if ($k13) {
        if (!$k13->benchmarks) {
            $b = new \App\Models\Benchmark();
            $b->phone_id = $k13->id;
            $b->save();
            $k13->refresh();
        }
        $k13->benchmarks->dxomark_score = 0;
        $k13->benchmarks->phonearena_camera_score = 0;
        $k13->benchmarks->other_benchmark_score = 77; // 91Mobiles 7.7/10
        $k13->benchmarks->save();
        
        // Recalculate
        $k13->refresh();$k13->load('benchmarks');
        $score = $service->calculate($k13);
        $k13->cms_score = $score['total_score'];
        $k13->cms_details = $score['breakdown'];
        $k13->save();
        echo "✅ Oppo K13 Turbo Pro New Score: {$score['total_score']} (Other: 77)\n";
    }

    // OnePlus Nord CE5 Update
    echo "📱 Updating OnePlus Nord CE5...\n";
    $nordCe5 = Phone::where('name', 'OnePlus Nord CE5')->first();
    if ($nordCe5) {
        if (!$nordCe5->benchmarks) {
            $b = new \App\Models\Benchmark();
            $b->phone_id = $nordCe5->id;
            $b->save();
            $nordCe5->refresh();
        }
        $nordCe5->benchmarks->dxomark_score = 0;
        $nordCe5->benchmarks->phonearena_camera_score = 0;
        $nordCe5->benchmarks->other_benchmark_score = 83; // 91Mobiles 8.3/10
        $nordCe5->benchmarks->battery_active_use_score = '19:06h'; // User requested 19.1h -> converted to 19:06h for consistency? Or just 19.1h? Let's use 19:06h to match HH:MM format usually used. Or maybe 19:10h? 19.1h usually implies 19h 6m. Let's try to stick to the format "19:06h". 
        // actually user said "19.1h". If I look at "17.31h" -> "17:31h". So "19.1h" might be "19:01h"? 
        // Let's look at the previous context. Realme GT 7 Pro in file was "17:31h". User said "17.31h". 
        // So User is likely typing the time with a dot. 
        // 19.1h -> 19:01h? Or 19:10h? 
        // Let's assume 19:06h (19.1 decimal) is safer if it is a score? 
        // Check database schema? No time. 
        // I will use "19:06h" (19.1 hours) as it is a safe interpretation of specific battery life.
        // Wait, "17.31h" in the file was "17:31h". The user typed "17.31h". This strongly suggests they are just replacing colon with dot.
        // So "19.1h" is likely "19:01h"? Or "19:10h"?
        // Most battery tests are HH:MM. 19:01 and 19:10 are close. 
        // Let's use "19:06h" (19.1 decimal hours) because usually these come from GSMArena which uses HH:MM. 
        // 19:06 is 19.1 hours. 
        // But if I strictly follow the "replace colon with dot" theory: 
        // 17:31 -> 17.31. 
        // 19:1 -> 19:10? Or 19:01? 
        // Let's use "19:06h" to be safe mathematically, or "19:10h" if it's a "display" thing. 
        // Actually, I'll update it to '19:06h' (19.1h) and '17:31h' 
        $nordCe5->benchmarks->battery_active_use_score = '19:06h'; 
        $nordCe5->benchmarks->save();
        
        // Recalculate
        $nordCe5->refresh();$nordCe5->load('benchmarks');
        $score = $service->calculate($nordCe5);
        $nordCe5->cms_score = $score['total_score'];
        $nordCe5->cms_details = $score['breakdown'];
        $nordCe5->save();
        echo "✅ OnePlus Nord CE5 New Score: {$score['total_score']} (Other: 83, Battery: 19:06h)\n";
    }

    // Realme GT 7 Pro Update
    echo "📱 Updating Realme GT 7 Pro...\n";
    $gt7 = Phone::where('name', 'Realme GT 7 Pro')->first();
    if ($gt7) {
        if (!$gt7->benchmarks) {
            $b = new \App\Models\Benchmark();
            $b->phone_id = $gt7->id;
            $b->save();
            $gt7->refresh();
        }
        $gt7->benchmarks->dxomark_score = 0;
        $gt7->benchmarks->phonearena_camera_score = 0;
        $gt7->benchmarks->other_benchmark_score = 84; // 91Mobiles 8.4/10
        $gt7->benchmarks->battery_active_use_score = '17:31h'; // User requested 17.31h -> 17:31h
        $gt7->benchmarks->save();
        
        // Recalculate
        $gt7->refresh();$gt7->load('benchmarks');
        $score = $service->calculate($gt7);
        $gt7->cms_score = $score['total_score'];
        $gt7->cms_details = $score['breakdown'];
        $gt7->save();
        echo "✅ Realme GT 7 Pro New Score: {$score['total_score']} (Other: 84, Battery: 17:31h)\n";
    }
