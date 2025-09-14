<?php

namespace App\Services;

use App\Models\EventOrderYf;
use App\Services\PaymentStrategyFactory;
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
        
        try {
            // Use Strategy Pattern
            $strategy = PaymentStrategyFactory::create($paymentMethod);
            $result = $strategy->processPayment($orders, $request);
            
            Log::info("Strategy processing completed", [
                'payment_method' => $paymentMethod,
                'success' => $result->success,
                'message' => $result->message
            ]);
            
            return (object) [
                'success' => $result->success,
                'message' => $result->message,
                'redirectUrl' => $result->redirectUrl,
                'transactionId' => $result->transactionId,
                'metadata' => $result->metadata
            ];
            
        } catch (\Exception $e) {
            Log::error("Payment processing failed: " . $e->getMessage(), [
                'payment_method' => $paymentMethod,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return (object) [
                'success' => false,
                'message' => 'Payment processing failed: ' . $e->getMessage(),
                'redirectUrl' => null,
                'transactionId' => null,
                'metadata' => ['error' => $e->getMessage()]
            ];
        }
    }

    /**
     * Get available payment methods
     */
    public function getAvailablePaymentMethods(): array
    {
        return PaymentStrategyFactory::getAvailableMethods();
    }
}