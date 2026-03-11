<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Client;
use App\Models\Transaction;
use App\Models\Gateway;

class TransactionTest extends TestCase
{
    use RefreshDatabase;

    public function test_transaction_can_be_created()
    {
        $client = Client::create([
            'name' => 'Maycon',
            'email' => 'maycon@email.com'
        ]);

        $gateway = Gateway::create([
            'name' => 'Gateway 1'
        ]);

        $transaction = Transaction::create([
            'client_id' => $client->id,
            'gateway_id' => $gateway->id,
            'status' => 'approved',
            'amount' => 10000,
            'card_last_numbers' => '1234'
        ]);

        $this->assertDatabaseHas('transactions', [
            'client_id' => $client->id,
            'amount' => 10000
        ]);
    }
}