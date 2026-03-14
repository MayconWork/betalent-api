<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use App\Enums\UserRole;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RoleTest extends TestCase
{
    use RefreshDatabase;

    private function actingAsRole(UserRole $role)
    {
        $user = User::factory()->create(['role' => $role->value]);
        $this->actingAs($user, 'sanctum');
        return $user;
    }

    public function test_admin_can_create_product()
    {
        $this->actingAsRole(UserRole::ADMIN);

        $response = $this->postJson('/api/products', [
            'name'   => 'Produto Teste',
            'amount' => 1000
        ]);

        $response->assertStatus(201);
    }

        public function test_manager_can_create_product()
    {
        $this->actingAsRole(UserRole::MANAGER);

        $response = $this->postJson('/api/products', [
            'name'   => 'Produto Teste',
            'amount' => 1000
        ]);

        $response->assertStatus(201);
    }

        public function test_finance_cannot_create_product()
    {
        $this->actingAsRole(UserRole::FINANCE);

        $response = $this->postJson('/api/products', [
            'name'   => 'Produto Teste',
            'amount' => 1000
        ]);

        $response->assertStatus(403);
    }

        public function test_user_cannot_create_product()
    {
        $this->actingAsRole(UserRole::USER);

        $response = $this->postJson('/api/products', [
            'name'   => 'Produto Teste',
            'amount' => 1000
        ]);

        $response->assertStatus(403);
    }

        public function test_finance_cannot_delete_product()
    {
        $this->actingAsRole(UserRole::FINANCE);
        $product = Product::factory()->create();

        $response = $this->deleteJson("/api/products/{$product->id}");

        $response->assertStatus(403);
    }

    public function test_admin_can_delete_product()
    {
        $this->actingAsRole(UserRole::ADMIN);
        $product = Product::factory()->create();

        $response = $this->deleteJson("/api/products/{$product->id}");

        $response->assertStatus(204);
    }

        public function test_unauthenticated_cannot_access_private_routes()
    {
        $response = $this->getJson('/api/clients');

        $response->assertStatus(401);
    }

        public function test_finance_can_refund()
    {
        $this->actingAsRole(UserRole::FINANCE);

        $client  = \App\Models\Client::factory()->create();
        $gateway = \App\Models\Gateway::factory()->create();

        $transaction = \App\Models\Transaction::create([
            'client_id'        => $client->id,
            'gateway_id'       => $gateway->id,
            'external_id'      => 'ext-123',
            'status'           => 'approved',
            'amount'           => 1000,
            'card_last_numbers'=> '6063'
        ]);

        $response = $this->postJson("/api/transactions/{$transaction->id}/refund");

        // 200 ou 500 (gateway mock pode falhar, mas a rota deve ser acessível)
        $this->assertNotEquals(403, $response->status());
    }

        public function test_manager_cannot_refund()
    {
        $this->actingAsRole(UserRole::MANAGER);

        $response = $this->postJson('/api/transactions/1/refund');

        $response->assertStatus(403);
    }
}