<?php

namespace App\Payment;

class PaymentBuilder
{
    private PaymentInterface $payment;

    public function __construct(float $base)
    {
        $this->payment = new BasePayment($base);
    }

    public function withTax(float $taxRate): self
    {
        $this->payment = new TaxDecorator($this->payment, $taxRate);
        return $this;
    }

    public function withServiceCharge(float $serviceCharge): self
    {
        $this->payment = new ServiceChargeDecorator($this->payment, $serviceCharge);
        return $this;
    }

    public function build(): PaymentInterface
    {
        return $this->payment;
    }
}


