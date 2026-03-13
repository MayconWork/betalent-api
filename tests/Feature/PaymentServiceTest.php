<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\PaymentService;
use App\Gateways\Gateway1Service;
use App\Gateways\Gateway2Service;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Client;
use App\Models\Gateway;
use Mockery;

class PaymentServiceTest extends TestCase
{
    use RefreshDatabase;

    private function mockGateway1Success()
    {
        $gateway = \Mockery::mock(\App\Gateways\Gateway1Service::class);

        $gateway->shouldReceive('processPayment')
            ->once()
            ->andReturn([
                'success' => true,
                'transaction_id' => 'tx123'
            ]);

        $gateway->shouldReceive('getId')
            ->once()
            ->andReturn(1);

        return $gateway;
    }

    private function mockGateway2Unused()
    {
        $gateway = \Mockery::mock(\App\Gateways\Gateway2Service::class);

        $gateway->shouldReceive('processPayment')->never();

        return $gateway;
    }

    private function paymentData($clientId)
    {
        return [
            'client_id' => $clientId,
            'amount' => 1000,
            'cardNumber' => '1234567812345678',
            'cvv' => '010'
        ];
    }
    public function test_fallback_to_second_gateway_when_first_fails()
    {
        $client = Client::factory()->create();

        Gateway::create([
            'id' => 1,
            'name' => 'Gateway1',
            'is_active' => true,
            'priority' => 1
        ]);

        Gateway::create([
            'id' => 2,
            'name' => 'Gateway2',
            'is_active' => true,
            'priority' => 2
        ]);

        $gateway1 = Mockery::mock(Gateway1Service::class);

        $gateway1->shouldReceive('processPayment')
            ->once()
            ->andReturn([
                'success' => false
            ]);

        $gateway2 = Mockery::mock(Gateway2Service::class);
        $gateway2->shouldReceive('processPayment')
            ->once()
            ->andReturn([
                'success' => true,
                'transaction_id' => 'tx456'
            ]);

        $gateway2->shouldReceive('getId')
            ->once()
            ->andReturn(2);

        $paymentService = new PaymentService(
            $gateway1,
            $gateway2
        );

        $result = $paymentService->process(
            $this->paymentData($client->id)
        );

        $this->assertTrue($result['success']);
        $this->assertEquals('tx456', $result['transaction_id']);
    }
}