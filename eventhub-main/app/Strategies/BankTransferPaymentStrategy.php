<?php

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
            // Bank transfer requires manual verification
            $transactionId = 'bank_' . uniqid();
            
            // Orders remain pending until payment gateway completion
            // No payment processing here - just redirect to gateway
            
            Log::info("Bank transfer payment initiated. Transaction ID: " . $transactionId);
            
            return new PaymentResult(
                success: true,
                message: 'Redirecting to bank transfer payment gateway...',
                transactionId: $transactionId,
                redirectUrl: route('payment-gateway.show', [
                    'payment_method' => 'bank_transfer',
                    'order_number' => $orders[0]->order_number
                ]),
                metadata: [
                    'payment_method' => 'bank_transfer',
                    'status' => 'pending_verification',
                    'processed_at' => now()->toISOString(),
                    'orders_count' => count($orders),
                    'instructions' => 'Please transfer the amount to the provided bank account details.'
                ]
            );
            
        } catch (\Exception $e) {
            Log::error("Bank transfer payment failed: " . $e->getMessage());
            
            return new PaymentResult(
                success: false,
                message: 'Bank transfer payment failed: ' . $e->getMessage(),
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
