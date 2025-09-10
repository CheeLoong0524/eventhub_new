<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Event;
use App\Models\VendorEventApplication;

class TestBoothAvailability extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:booth-availability';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test booth availability logic';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing booth availability logic...');
        
        $event = Event::first();
        if (!$event) {
            $this->error('No events found');
            return 1;
        }
        
        $this->line("Event: {$event->name}");
        $this->line("Total booths: {$event->booth_quantity}");
        $this->line("Sold booths: {$event->booth_sold}");
        $this->line("Available booths: {$event->available_booths}");
        
        // Check if has available slots
        $hasSlots = $event->hasAvailableSlots();
        $this->line("Has available slots: " . ($hasSlots ? 'YES' : 'NO'));
        
        // Check paid applications
        $paidApps = VendorEventApplication::where('event_id', $event->id)
            ->where('status', 'paid')
            ->get();
        
        $this->line("Paid applications: " . $paidApps->count());
        foreach ($paidApps as $app) {
            $this->line("  - App #{$app->id}: {$app->booth_quantity} booths");
        }
        
        // Calculate expected booth_sold
        $expectedSold = $paidApps->sum('booth_quantity');
        $this->line("Expected booth_sold: {$expectedSold}");
        $this->line("Actual booth_sold: {$event->booth_sold}");
        $this->line("Match: " . ($expectedSold == $event->booth_sold ? 'YES' : 'NO'));
        
        return 0;
    }
}
