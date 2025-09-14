<?php

// Author  : Choong Yoong Sheng (Vendor module)

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Event;
use App\Models\VendorEventApplication;

class FixBoothSales extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:booth-sales';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix booth sales data by recalculating booth_sold based on paid applications';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Fixing booth sales data...');
        
        $events = Event::all();
        $fixed = 0;
        
        foreach ($events as $event) {
            // Calculate actual booth sales from paid applications
            $actualBoothSold = VendorEventApplication::where('event_id', $event->id)
                ->where('status', 'paid')
                ->sum('booth_quantity');
            
            $oldBoothSold = $event->booth_sold;
            
            if ($actualBoothSold != $oldBoothSold) {
                $event->update(['booth_sold' => $actualBoothSold]);
                $event->updateFinancials();
                
                $this->line("Event '{$event->name}': {$oldBoothSold} -> {$actualBoothSold} booths sold");
                $fixed++;
            }
        }
        
        $this->info("Fixed {$fixed} events with incorrect booth sales data.");
        
        return 0;
    }
}