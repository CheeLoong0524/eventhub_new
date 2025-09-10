<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Event;

class UpdateEventFinancials extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:event-financials';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update event financial data with correct revenue calculations';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Updating event financial data...');
        
        $events = Event::all();
        $updated = 0;
        
        foreach ($events as $event) {
            $event->updateFinancials();
            $updated++;
            $this->line("Updated event: {$event->name} - Revenue: RM {$event->total_revenue}");
        }
        
        $this->info("Successfully updated {$updated} events.");
        
        return 0;
    }
}
