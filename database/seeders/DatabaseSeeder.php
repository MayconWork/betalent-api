<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Client;
use App\Models\Product;
use App\Models\Gateway;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Usuários
        User::create([
            'name' => 'Admin',
            'email' => 'admin@test.com',
            'password' => bcrypt('123456'),
            'role' => 'ADMIN',
        ]);

        // Clientes
        Client::create([
            'name' => 'Cliente Teste',
            'email' => 'cliente@test.com',
        ]);

        // Produtos
        Product::create([
            'name' => 'Produto A',
            'amount' => 1000,
        ]);

        Product::create([
            'name' => 'Produto B',
            'amount' => 2000,
        ]);

        // Gateways
        Gateway::create(['name' => 'Gateway1', 'is_active' => true, 'priority' => 1]);
        Gateway::create(['name' => 'Gateway2', 'is_active' => true, 'priority' => 2]);
    }
}