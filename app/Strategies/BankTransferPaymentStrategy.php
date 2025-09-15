<?php
// Author: Gooi Ye Fan

namespace App\Strategies;

use App\Models\EventOrderYf;
use App\Models\EventPaymentYf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BankTransferPaymentStrategy implements PaymentStrategy
{
    public function processPayment(array $orders, Request $request): PaymentResult
    {
        Log::info("Processing Bank Transfer payment for " . count($orders) . " orders");
        
        try {
            // Simulate Bank Transfer payment processing
            $transactionId = 'bank_' . uniqid();
            
            
            Log::info("Bank Transfer payment completed successfully. Transaction ID: " . $transactionId);
            
            return new PaymentResult(
                success: true,
                message: 'Payment Successfully...',
                transactionId: $transactionId,
                redirectUrl: route('payment-gateway.show', [
                    'payment_method' => 'bank_transfer',
                    'order_number' => $orders[0]->order_number
                ]),
                metadata: [
                    'payment_method' => 'bank_transfer',
                    'processed_at' => now()->toISOString(),
                    'orders_count' => count($orders),
                    'simulation' => true
                ]
            );
            
        } catch (\Exception $e) {
            Log::error("Bank Transfer payment failed: " . $e->getMessage());
            
            return new PaymentResult(
                success: false,
                message: 'Bank Transfer payment failed: ' . $e->getMessage(),
                metadata: ['error' => $e->getMessage()]
            );
        }
    }

    public function getPaymentMethod(): string
    {
        return 'bank_transfer';
    }

    public function canHandle(string $paymentMethod): bool
    {
        return $paymentMethod === 'bank_transfer';
    }
}

