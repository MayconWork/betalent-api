<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Client;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionProduct;
use App\Models\Gateway;

class TransactionProductTest extends TestCase
{
    use RefreshDatabase;

    public function test_product_can_be_attached_to_transaction()
    {
        $client = Client::create([
            'name' => 'Maycon',
            'email' => 'maycon@email.com'
        ]);

        $gateway = Gateway::create([
            'name' => 'Gateway 1'
        ]);

        $product = Product::create([
            'name' => 'Notebook',
            'amount' => 100000
        ]);

        $transaction = Transaction::create([
            'client_id' => $client->id,
            'gateway_id' => $gateway->id,
            'status' => 'approved',
            'amount' => 100000,
            'card_last_numbers' => '1234'
        ]);

        TransactionProduct::create([
            'transaction_id' => $transaction->id,
            'product_id' => $product->id,
            'quantity' => 1
        ]);

        $this->assertDatabaseHas('transaction_products', [
            'transaction_id' => $transaction->id,
            'product_id' => $product->id
        ]);
    }
}