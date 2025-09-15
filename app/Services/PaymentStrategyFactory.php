<?php
// Author: Gooi Ye Fan

namespace App\Services;

use App\Strategies\PaymentStrategy;
use App\Strategies\StripePaymentStrategy;
use App\Strategies\TngEwalletPaymentStrategy;
use App\Strategies\BankTransferPaymentStrategy;

class PaymentStrategyFactory
{
    /**
     * Create a payment strategy based on the payment method
     */
    public static function create(string $paymentMethod): PaymentStrategy
    {
        return match($paymentMethod) {
            'stripe' => new StripePaymentStrategy(),
            'tng_ewallet' => new TngEwalletPaymentStrategy(),
            'bank_transfer' => new BankTransferPaymentStrategy(),
            default => throw new \InvalidArgumentException("Unsupported payment method: {$paymentMethod}")
        };
    }

    /**
     * Get all available payment methods
     */
    public static function getAvailableMethods(): array
    {
        return [
            'stripe' => 'Credit/Debit Card',
            'tng_ewallet' => 'TNG eWallet',
            'bank_transfer' => 'Bank Transfer'
        ];
    }

    /**
     * Check if a payment method is supported
     */
    public static function isSupported(string $paymentMethod): bool
    {
        return in_array($paymentMethod, array_keys(self::getAvailableMethods()));
    }
}
