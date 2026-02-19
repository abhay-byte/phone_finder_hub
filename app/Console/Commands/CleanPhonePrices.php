<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Phone;

class CleanPhonePrices extends Command
{
    protected $signature = 'phone:clean-prices {--threshold=4000 : Minimum price threshold}';
    protected $description = 'Remove shopping links with suspiciously low prices';

    public function handle()
    {
        $threshold = (int) $this->option('threshold');
        $this->info("Cleaning phone prices below ₹{$threshold}...");

        // clean amazon
        $phones = Phone::where('amazon_price', '>', 0)
            ->where('amazon_price', '<', $threshold)
            ->get();

        $count = 0;
        foreach ($phones as $phone) {
            $this->line("Fixing {$phone->name}: Amazon Price ₹{$phone->amazon_price}");
            $phone->amazon_price = null;
            $phone->amazon_url = null;
            
            // Recalculate main price
            if ($phone->flipkart_price && $phone->flipkart_price >= $threshold) {
                 $phone->price = $phone->flipkart_price;
            } else {
                 $phone->price = 0; 
            }

            $phone->save();
            $count++;
        }

        $this->info("Cleaned {$count} records associated with Amazon.");

        // clean flipkart
        $fPhones = Phone::where('flipkart_price', '>', 0)
            ->where('flipkart_price', '<', $threshold)
            ->get();
        
        $fCount = 0;
        foreach ($fPhones as $phone) {
            $this->line("Fixing {$phone->name}: Flipkart Price ₹{$phone->flipkart_price}");
            $phone->flipkart_price = null;
            $phone->flipkart_url = null;
            
            // Recalculate main price
            if ($phone->amazon_price && $phone->amazon_price >= $threshold) {
                $phone->price = $phone->amazon_price;
            } else {
                $phone->price = 0;
            }
            
            $phone->save();
            $fCount++;
        }

        $this->info("Cleaned {$fCount} records associated with Flipkart.");
        return 0;
    }
}
