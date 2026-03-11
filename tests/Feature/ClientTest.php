<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Client;

class ClientTest extends TestCase
{
    use RefreshDatabase;

    public function test_client_can_be_created()
    {
        $client = Client::create([
            'name' => 'Maycon',
            'email' => 'maycon@email.com'
        ]);

        $this->assertDatabaseHas('clients', [
            'email' => 'maycon@email.com'
        ]);
    }
}