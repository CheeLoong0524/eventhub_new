<?php

namespace App\Services;

use App\Models\EventOrderYf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentService
{
    /**
     * Process payment using the appropriate strategy
     */
    public function processPayment(array $orders, Request $request): object
    {
        $paymentMethod = $request->input('payment_method');
        
        Log::info("Processing payment with method: " . $paymentMethod);
        
        // For now, we'll redirect to the payment gateway
        // In a real implementation, this would integrate with actual payment providers
        $redirectUrl = route('payment-gateway.show', [
            'payment_method' => $paymentMethod,
            'order_number' => $orders[0]->order_number
        ]);
        
        return (object) [
            'success' => true,
            'message' => 'Redirecting to payment gateway...',
            'redirectUrl' => $redirectUrl
        ];
    }

    /**
     * Get available payment methods
     */
    public function getAvailablePaymentMethods(): array
    {
        return [
            'stripe' => 'Credit/Debit Card',
            'tng_ewallet' => 'TNG eWallet',
            'bank_transfer' => 'Bank Transfer'
        ];
    }
}