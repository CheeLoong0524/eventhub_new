<?php

// Author  : Choong Yoong Sheng (Vendor module)


namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Event;
use App\Models\VendorEventApplication;

class FixBoothQuantity extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:booth-quantity';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix booth quantity data by recalculating booth_quantity based on booth_sold';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Fixing booth quantity data...');
        
        $events = Event::all();
        $fixed = 0;
        
        foreach ($events as $event) {
            $oldBoothSold = $event->booth_sold;
            $oldBoothQuantity = $event->booth_quantity;
            
            // Check if this event has the old logic (booth_quantity represents total, not remaining)
            // If booth_sold > 0 and booth_quantity > booth_sold, then it's using old logic
            if ($oldBoothSold > 0 && $oldBoothQuantity > $oldBoothSold) {
                // This is using old logic: booth_quantity = total, booth_sold = sold
                // Convert to new logic: booth_quantity = remaining, booth_sold = sold
                $newBoothQuantity = $oldBoothQuantity - $oldBoothSold;
                
                $event->update(['booth_quantity' => $newBoothQuantity]);
                $this->line("Event '{$event->name}': booth_quantity {$oldBoothQuantity} -> {$newBoothQuantity} (sold: {$oldBoothSold})");
                $fixed++;
            } elseif ($oldBoothSold > 0 && $oldBoothQuantity == $oldBoothSold) {
                // This is using old logic where all booths are sold
                // Convert to new logic: booth_quantity = 0, booth_sold = sold
                $event->update(['booth_quantity' => 0]);
                $this->line("Event '{$event->name}': booth_quantity {$oldBoothQuantity} -> 0 (all sold: {$oldBoothSold})");
                $fixed++;
            }
            
            // Update financials
            $event->updateFinancials();
        }
        
        $this->info("Fixed {$fixed} events with incorrect booth quantity data.");
        
        return 0;
    }
}