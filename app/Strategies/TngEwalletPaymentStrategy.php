<?php

namespace App\Strategies;

use App\Models\EventOrderYf;
use App\Models\EventPaymentYf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TngEwalletPaymentStrategy implements PaymentStrategy
{
    public function processPayment(array $orders, Request $request): PaymentResult
    {
        Log::info("Processing TNG eWallet payment for " . count($orders) . " orders");
        
        try {
            // Simulate TNG eWallet payment processing
            $transactionId = 'tng_' . uniqid();
            
            
            Log::info("TNG eWallet payment completed successfully. Transaction ID: " . $transactionId);
            
            return new PaymentResult(
                success: true,
                message: 'Payment Successfully...',
                transactionId: $transactionId,
                redirectUrl: route('payment-gateway.show', [
                    'payment_method' => 'tng_ewallet',
                    'order_number' => $orders[0]->order_number
                ]),
                metadata: [
                    'payment_method' => 'tng_ewallet',
                    'processed_at' => now()->toISOString(),
                    'orders_count' => count($orders),
                    'simulation' => true
                ]
            );
            
        } catch (\Exception $e) {
            Log::error("TNG eWallet payment failed: " . $e->getMessage());
            
            return new PaymentResult(
                success: false,
                message: 'TNG eWallet payment failed: ' . $e->getMessage(),
                metadata: ['error' => $e->getMessage()]
            );
        }
    }

    public function getPaymentMethod(): string
    {
        return 'tng_ewallet';
    }

    public function canHandle(string $paymentMethod): bool
    {
        return $paymentMethod === 'tng_ewallet';
    }
}

