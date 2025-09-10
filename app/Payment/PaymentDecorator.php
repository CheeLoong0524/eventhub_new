<?php

namespace App\Payment;

abstract class PaymentDecorator implements PaymentInterface
{
    protected PaymentInterface $payment;

    public function __construct(PaymentInterface $payment)
    {
        $this->payment = $payment;
    }

    public function getBreakdown(): array
    {
        return $this->payment->getBreakdown();
    }
}


