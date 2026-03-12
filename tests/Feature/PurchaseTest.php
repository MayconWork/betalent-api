<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Product;

class PurchaseTest extends TestCase
{
    public function test_user_can_purchase()
    {
        $product = Product::create([
            'name' => 'Test Product',
            'amount' => 1000
        ]);

        $response = $this->postJson('/api/purchase', [
            'product_id' => $product->id,
            'quantity' => 1,
            'name' => 'Tester',
            'email' => 'tester@email.com',
            'cardNumber' => '5569000000006063',
            'cvv' => '010'
        ]);

        $response->assertStatus(200);
    }
}
