<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class DatabaseMigrationsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function basic_tables_and_columns_exist()
    {
        // tabelas
        $this->assertTrue(Schema::hasTable('users'), 'Tabela users inexistente');
        $this->assertTrue(Schema::hasTable('gateways'), 'Tabela gateways inexistente');
        $this->assertTrue(Schema::hasTable('clients'), 'Tabela clients inexistente');
        $this->assertTrue(Schema::hasTable('products'), 'Tabela products inexistente');
        $this->assertTrue(Schema::hasTable('transactions'), 'Tabela transactions inexistente');
        $this->assertTrue(Schema::hasTable('transaction_products'), 'Tabela transaction_products inexistente');

        // colunas mínimas (exemplos)
        $this->assertTrue(Schema::hasColumn('users', 'email'), 'users.email ausente');
        $this->assertTrue(Schema::hasColumn('users', 'password'), 'users.password ausente');
        $this->assertTrue(Schema::hasColumn('users', 'role'), 'users.role ausente');

        $this->assertTrue(Schema::hasColumn('gateways', 'name'), 'gateways.name ausente');
        $this->assertTrue(Schema::hasColumn('gateways', 'is_active'), 'gateways.is_active ausente');
        $this->assertTrue(Schema::hasColumn('gateways', 'priority'), 'gateways.priority ausente');

        $this->assertTrue(Schema::hasColumn('clients', 'name'), 'clients.name ausente');
        $this->assertTrue(Schema::hasColumn('clients', 'email'), 'clients.email ausente');

        $this->assertTrue(Schema::hasColumn('products', 'name'), 'products.name ausente');
        $this->assertTrue(Schema::hasColumn('products', 'amount'), 'products.amount ausente');

        $this->assertTrue(Schema::hasColumn('transactions', 'client_id'), 'transactions.client_id ausente');
        $this->assertTrue(Schema::hasColumn('transactions', 'gateway_id'), 'transactions.gateway_id ausente');
        $this->assertTrue(Schema::hasColumn('transactions', 'external_id'), 'transactions.external_id ausente');
        $this->assertTrue(Schema::hasColumn('transactions', 'status'), 'transactions.status ausente');
        $this->assertTrue(Schema::hasColumn('transactions', 'amount'), 'transactions.amount ausente');
        $this->assertTrue(Schema::hasColumn('transactions', 'card_last_numbers'), 'transactions.card_last_numbers ausente');

        $this->assertTrue(Schema::hasColumn('transaction_products', 'transaction_id'), 'transaction_products.transaction_id ausente');
        $this->assertTrue(Schema::hasColumn('transaction_products', 'product_id'), 'transaction_products.product_id ausente');
        $this->assertTrue(Schema::hasColumn('transaction_products', 'quantity'), 'transaction_products.quantity ausente');
    }
}