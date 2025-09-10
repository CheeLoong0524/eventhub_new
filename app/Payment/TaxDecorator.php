<?php

namespace App\Payment;

class TaxDecorator extends PaymentDecorator
{
    private float $taxRate;

    public function __construct(PaymentInterface $payment, float $taxRate)
    {
        parent::__construct($payment);
        $this->taxRate = $taxRate;
    }

    public function getAmount(): float
    {
        $amount = $this->payment->getAmount();
        return $amount + ($amount * $this->taxRate);
    }

    public function getBreakdown(): array
    {
        $breakdown = parent::getBreakdown();
        $base = $this->payment->getAmount();
        $breakdown['tax'] = round($base * $this->taxRate, 2);
        $breakdown['total'] = round($this->getAmount(), 2);
        return $breakdown;
    }
}


