<?php
namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Client;
use App\Models\Product;
use App\Services\PaymentService;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PurchaseTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_purchase()
    {
        $this->mock(PaymentService::class, function ($mock) {
            $mock->shouldReceive('process')
                ->once()
                ->andReturn([
                    'success'        => true,
                    'transaction_id' => 'tx123'
                ]);
        });

        $client  = Client::factory()->create();
        $product = Product::factory()->create([
            'name'   => 'Produto Teste',
            'amount' => 1000
        ]);

        $response = $this->postJson('/api/transactions', [
            'name'     => $client->name,
            'email'    => $client->email,
            'products' => [
                ['product_id' => $product->id, 'quantity' => 2]
            ],
            'cardNumber' => '5569000000006063',
            'cvv'        => '010'
        ]);

        $response->assertStatus(200)
                 ->assertJson(['success' => true]);
    }
}