<?php

namespace App\Strategies;

use App\Models\EventOrderYf;
use App\Models\EventPaymentYf;
use Illuminate\Http\Request;

interface PaymentStrategy
{
    /**
     * Process the payment for the given orders
     */
    public function processPayment(array $orders, Request $request): PaymentResult;

    /**
     * Get the payment method name
     */
    public function getPaymentMethod(): string;

    /**
     * Check if this strategy can handle the payment method
     */
    public function canHandle(string $paymentMethod): bool;
}

class PaymentResult
{
    public function __construct(
        public bool $success,
        public string $message,
        public ?string $transactionId = null,
        public ?string $redirectUrl = null,
        public array $metadata = []
    ) {}
}

