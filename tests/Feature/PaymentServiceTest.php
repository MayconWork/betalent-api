<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\PaymentService;
use App\Gateways\Gateway1Service;
use App\Gateways\Gateway2Service;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Client;
use Mockery;

class PaymentServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_gateway1_success()
    {
        $client = Client::factory()->create();

        $gateway1 = Mockery::mock(Gateway1Service::class);
        $gateway2 = Mockery::mock(Gateway2Service::class);

        $gateway1->shouldReceive('processPayment')
            ->once()
            ->andReturn([
                'success' => true,
                'id' => 'tx123'
            ]);
            
        $gateway2->shouldReceive('processPayment')->never();

        $service = new PaymentService($gateway1, $gateway2);

        $result = $service->process([
            'client_id' => $client->id,
            'amount' => 1000,
            'cardNumber' => '1234567812345678',
            'cvv' => '010'
        ]);

        $this->assertTrue($result['success']);
    }
}