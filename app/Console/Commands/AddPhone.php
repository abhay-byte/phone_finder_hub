<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Phone;

class AddPhone extends Command
{
    protected $signature = 'phone:add';
    protected $description = 'Add a new phone to the database with guided prompts';

    public function handle()
    {
        $this->info('=== Add New Phone ===');
        $this->newLine();

        // Basic Info
        $name = $this->ask('Phone name (e.g., "Oppo K13 Turbo Pro")');
        $brand = $this->ask('Brand');
        $modelVariant = $this->ask('Model variant (e.g., "PLE110")');
        $price = $this->ask('Price (INR)');
        $releaseDate = $this->ask('Release date (YYYY-MM-DD)');
        $announcedDate = $this->ask('Announced date (YYYY-MM-DD)');
        $imageUrl = $this->ask('Image URL (e.g., "/storage/phones/phone-name.png")');
        
        // Store URLs
        $amazonUrl = $this->ask('Amazon URL (or press Enter to skip)') ?: null;
        $flipkartUrl = $this->ask('Flipkart URL (or press Enter to skip)') ?: null;
        $amazonPrice = $amazonUrl ? $this->ask('Amazon price') : null;
        $flipkartPrice = $flipkartUrl ? $this->ask('Flipkart price') : null;

        // Create phone
        $phone = Phone::create([
            'name' => $name,
            'brand' => $brand,
            'model_variant' => $modelVariant,
            'price' => $price,
            'overall_score' => 0,
            'release_date' => $releaseDate,
            'announced_date' => $announcedDate,
            'image_url' => $imageUrl,
            'amazon_url' => $amazonUrl,
            'flipkart_url' => $flipkartUrl,
            'amazon_price' => $amazonPrice,
            'flipkart_price' => $flipkartPrice,
        ]);

        $this->info("✓ Phone created (ID: {$phone->id})");
        $this->newLine();

        // Benchmarks (CRITICAL)
        if ($this->confirm('Add benchmark data?', true)) {
            $this->info('Enter benchmark scores:');
            $antutu = $this->ask('AnTuTu v11 score');
            $gbSingle = $this->ask('Geekbench 6 Single-Core');
            $gbMulti = $this->ask('Geekbench 6 Multi-Core');
            $dmark = $this->ask('3DMark Wild Life Extreme');
            $stability = $this->ask('3DMark Stability % (INTEGER, e.g., 99)');

            $phone->benchmarks()->create([
                'antutu_score' => $antutu,
                'geekbench_single' => $gbSingle,
                'geekbench_multi' => $gbMulti,
                'dmark_wild_life_extreme' => $dmark,
                'dmark_wild_life_stress_stability' => (int)$stability, // Force integer
                'dmark_test_type' => 'Wild Life Extreme',
            ]);

            $this->info('✓ Benchmarks added');
        }

        // Add other specs
        if ($this->confirm('Continue adding specs (body, platform, camera, etc.)?', true)) {
            $this->warn('Use tinker or manual SQL for detailed specs.');
            $this->info("Run: php artisan tinker");
            $this->info("Then: \$phone = App\\Models\\Phone::find({$phone->id});");
        }

        // Calculate scores
        $this->newLine();
        $this->info('Calculating scores...');
        $phone->updateScores();
        
        $this->info("✓ Scores calculated:");
        $this->line("  FPI: {$phone->overall_score}");
        $this->line("  UEPS: {$phone->ueps_score}");
        $this->line("  GPX: {$phone->gpx_score}");
        $this->line("  Value: {$phone->value_score}");

        // Recalculate all and clear cache
        if ($this->confirm('Recalculate all phone scores and clear cache?', true)) {
            $this->call('phone:recalculate-scores');
            $this->call('cache:clear');
        }

        $this->newLine();
        $this->info('✓ Phone added successfully!');
        $this->info("View at: http://localhost:8000/phones/{$phone->id}");
    }
}
