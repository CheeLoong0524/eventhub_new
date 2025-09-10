<?php

namespace App\Payment;

interface PaymentInterface
{
    public function getAmount(): float;
    public function getBreakdown(): array;
}


