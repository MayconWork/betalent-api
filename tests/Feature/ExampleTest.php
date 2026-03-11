<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use app\Models\Product;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    public function test_product_can_be_created()
    {
        $product = Product::create([
            'name' => 'Notebook',
            'amount' => 100000  // Valor em centavos
        ]);

        $this->assertDataBaseHas('products', [
            'name' => 'Notebook'
        ]);
    }

    public function test_product_amount_is_interger()
        {
            $product = Product::create([
                'name' => 'Mouse',
                'amount' => 5000
            ]);

            $this->assertIsInt($product->amount);
        }
}