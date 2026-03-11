<?php

namespace App\Services;

use App\Gateways\Gateway1Service;
use App\Gateways\Gateway2Service;

class PaymentService
{
    protected $gateway1;
    protected $gateway2;

    public function __construct(
        Gateway1Service $gateway1,
        Gateway2Service $gateway2
    ){
        $this->gateway1 = $gateway1;
        $this->gateway2 = $gateway2;
    }

    public function process(array $paymentData):array
    {
        $response = $this->gateway1->processPayment($paymentData);

        if ($response['success']){
            return $response;
        }

        return $this->gateway2->processPayment($paymentData);
    }
}