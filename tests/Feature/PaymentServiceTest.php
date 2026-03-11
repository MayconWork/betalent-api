<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Services\PaymentService;
use App\Gateways\Gateway1Service;
use App\Gateways\Gateway2Service;

class PaymentServiceTest extends TestCase
{
    public function test_payment_is_processed()
    {
        $paymentService = new PaymentService(
            new Gateway1Service(),
            new Gateway2Service
        );

        $response = $paymentService->process([
            'amount' => 10000,
            'card' => '1234'
        ]);

        $this->assertTrue($response['success']);
    }
}
