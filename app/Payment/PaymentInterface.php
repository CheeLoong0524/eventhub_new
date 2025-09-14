<?php       

// Author  : Choong Yoong Sheng (Payment module)

namespace App\Payment;

interface PaymentInterface
{
    public function getAmount(): float;
    public function getBreakdown(): array;
}


