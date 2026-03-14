<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Services\PaymentService;
use App\Gateways\Gateway1Service;
use App\Gateways\Gateway2Service;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Client;
use App\Models\Gateway;
use App\Models\Product;
use Mockery;

class PaymentServiceTest extends TestCase
{
    use RefreshDatabase;

    private function mockGateway1Fail()
    {
        $gateway = Mockery::mock(Gateway1Service::class);

        $gateway->shouldReceive('processPayment')
            ->once()
            ->andReturn(['success' => false]);

        return $gateway;
    }

    private function mockGateway2Success()
    {
        $gateway = Mockery::mock(Gateway2Service::class);

        $gateway->shouldReceive('processPayment')
            ->once()
            ->andReturn([
                'success' => true,
                'transaction_id' => 'tx456'
            ]);

        $gateway->shouldReceive('getId')
            ->once()
            ->andReturn(2);

        return $gateway;
    }

    public function test_fallback_to_second_gateway_when_first_fails()
    {
        // Cria cliente
        $client = Client::factory()->create();

        // Cria gateways ativos
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

        // Cria produto de teste
        $product = Product::create([
            'name' => 'Produto Teste',
            'amount' => 1000
        ]);

        // Dados do pagamento
        $paymentData = [
            'client_id' => $client->id,
            'amount' => 1000,
            'cardNumber' => '1234567812345678',
            'cvv' => '010',
            'products' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 2
                ]
            ]
        ];

        // Mock dos gateways
        $gateway1 = $this->mockGateway1Fail();
        $gateway2 = $this->mockGateway2Success();

        // PaymentService com fallback
        $paymentService = new PaymentService($gateway1, $gateway2);

        $result = $paymentService->process($paymentData);

        // Asserts
        $this->assertTrue($result['success']);
        $this->assertEquals('tx456', $result['transaction_id']);

        // Verifica se a transação foi criada corretamente
        $this->assertDatabaseHas('transactions', [
            'client_id' => $client->id,
            'gateway_id' => 2,
            'amount' => 1000
        ]);

        // Verifica se o produto foi associado
        $this->assertDatabaseHas('transaction_products', [
            'product_id' => $product->id,
            'quantity' => 2
        ]);
    }
}