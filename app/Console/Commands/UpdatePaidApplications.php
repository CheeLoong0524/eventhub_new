<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\VendorEventApplication;
use App\Payment\PaymentBuilder;

class UpdatePaidApplications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:paid-applications';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update existing paid applications with payment breakdown amounts';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Updating paid applications with payment breakdown...');
        
        $paidApplications = VendorEventApplication::where('status', 'paid')
            ->whereNull('final_amount')
            ->with('event')
            ->get();
        
        $updated = 0;
        
        foreach ($paidApplications as $application) {
            // Calculate payment breakdown using the same logic as in VendorController
            $baseAmount = (float) ($application->event->booth_price ?? 0);
            $payment = (new PaymentBuilder($baseAmount))
                ->withTax(0.06)
                ->withServiceCharge(10.00)
                ->build();
            
            $finalAmount = round($payment->getAmount(), 2);
            $paymentBreakdown = $payment->getBreakdown();
            
            // Update the application with payment breakdown
            $application->update([
                'base_amount' => $baseAmount,
                'tax_amount' => $paymentBreakdown['tax'] ?? 0,
                'service_charge_amount' => $paymentBreakdown['service_charge'] ?? 0,
                'final_amount' => $finalAmount,
            ]);
            
            $updated++;
            $this->line("Updated application #{$application->id}: RM {$finalAmount}");
        }
        
        $this->info("Successfully updated {$updated} paid applications.");
        
        return 0;
    }
}
