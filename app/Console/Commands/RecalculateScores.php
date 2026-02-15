<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Phone;

class RecalculateScores extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'phone:recalculate-scores';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Recalculates UEPS and FPI scores for all phones';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting score recalculation...');

        // Clear the cache for max scores first to ensure freshness
        \Illuminate\Support\Facades\Cache::forget('benchmark_max_scores');

        $phones = Phone::with(['benchmarks', 'body', 'platform', 'camera', 'connectivity', 'battery'])->get();
        $bar = $this->output->createProgressBar($phones->count());

        $bar->start();

        foreach ($phones as $phone) {
            try {
                $phone->updateScores();
            } catch (\Exception $e) {
                $this->error("Failed to update scores for {$phone->name}: " . $e->getMessage());
            }
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info('All scores recalculated successfully!');
    }
}
