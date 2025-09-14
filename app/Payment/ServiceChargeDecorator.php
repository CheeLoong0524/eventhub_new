<?php       

// Author  : Choong Yoong Sheng (Payment module)

namespace App\Payment;

class ServiceChargeDecorator extends PaymentDecorator
{
    private float $serviceCharge;

    public function __construct(PaymentInterface $payment, float $serviceCharge)
    {
        parent::__construct($payment);
        $this->serviceCharge = $serviceCharge;
    }

    public function getAmount(): float
    {
        return $this->payment->getAmount() + $this->serviceCharge;
    }

    public function getBreakdown(): array
    {
        $breakdown = parent::getBreakdown();
        $breakdown['service_charge'] = round($this->serviceCharge, 2);
        $breakdown['total'] = round($this->getAmount(), 2);
        return $breakdown;
    }
}


