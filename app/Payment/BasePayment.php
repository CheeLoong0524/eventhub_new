<?php

namespace App\Payment;

class BasePayment implements PaymentInterface
{
    protected float $baseAmount;

    public function __construct(float $baseAmount)
    {
        $this->baseAmount = $baseAmount;
    }

    public function getAmount(): float
    {
        return $this->baseAmount;
    }

    public function getBreakdown(): array
    {
        return [
            'base' => $this->baseAmount,
        ];
    }
}


