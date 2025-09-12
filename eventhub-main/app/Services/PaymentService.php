<?php

namespace App\Services;

use App\Strategies\PaymentStrategy;
use App\Strategies\PaymentResult;
use App\Strategies\StripePaymentStrategy;
use App\Strategies\TngEwalletPaymentStrategy;
use App\Strategies\BankTransferPaymentStrategy;
use App\Models\EventOrderYf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentService
{
    private array $strategies = [];

    public function __construct()
    {
        $this->strategies = [
            new StripePaymentStrategy(),
            new TngEwalletPaymentStrategy(),
            new BankTransferPaymentStrategy(),
        ];
    }

    /**
     * Process payment using the appropriate strategy
     */
    public function processPayment(array $orders, Request $request): PaymentResult
    {
        $paymentMethod = $request->input('payment_method');
        
        Log::info("Processing payment with method: " . $paymentMethod);
        
        $strategy = $this->getStrategy($paymentMethod);
        
        if (!$strategy) {
            throw new \InvalidArgumentException("Unsupported payment method: " . $paymentMethod);
        }
        
        return $strategy->processPayment($orders, $request);
    }

    /**
     * Get the appropriate payment strategy
     */
    private function getStrategy(string $paymentMethod): ?PaymentStrategy
    {
        foreach ($this->strategies as $strategy) {
            if ($strategy->canHandle($paymentMethod)) {
                return $strategy;
            }
        }
        
        return null;
    }

    /**
     * Get available payment methods
     */
    public function getAvailablePaymentMethods(): array
    {
        return array_map(
            fn($strategy) => $strategy->getPaymentMethod(),
            $this->strategies
        );
    }

    /**
     * Add a custom payment strategy
     */
    public function addStrategy(PaymentStrategy $strategy): void
    {
        $this->strategies[] = $strategy;
    }
}
