<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Phone;

class RecalculateGpxScores extends Command
{
    protected $signature = 'gpx:recalculate';
    protected $description = 'Recalculates and stores GPX scores and details for all phones';

    public function handle()
    {
        $phones = Phone::with(['benchmarks', 'body', 'platform', 'battery', 'connectivity'])->get();

        foreach ($phones as $phone) {
            // calculateGPX() returns ['score' => total, 'details' => breakdown] based on Phone.php line 487 return structure
            // Wait, checking Phone.php... 
            // The return of calculateGPX() is:
            // return [
            //     'score' => round($finalScore, 2),
            //     'details' => $breakdown
            // ];
            
            $gpx = $phone->calculateGPX();
            
            $phone->timestamps = false; 
            $phone->gpx_score = $gpx['score'];
            $phone->gpx_details = $gpx['details'];
            $phone->save();
            
            $this->info("Updated {$phone->name}: {$phone->gpx_score}");
        }
    }
}
