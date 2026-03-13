<?php

namespace App\Gateways;

use Illuminate\Support\Facades\Http;

class Gateway2Service implements GatewayInterface
{
    private $baseUrl = 'http://gateways-mock:3002';

    public function charge(array $data)
    {
        $response = Http::withHeaders([
            'Gateway-Auth-Token' => 'tk_f2198cc671b5289fa856',
            'Gateway-Auth-Secret' => '3d15e8ed6131446ea7e3456728b1211f'
        ])->post($this->baseUrl . '/transacoes', [
            'valor' => $data['amount'],
            'nome' => $data['name'],
            'email' => $data['email'],
            'numeroCartao' => $data['cardNumber'],
            'cvv' => $data['cvv']
        ]);

        return $response->json();
    }

    public function refund(string $transactionId)
    {
        return Http::withHeaders([
            'Gateway-Auth-Token' => 'tk_f2198cc671b5289fa856',
            'Gateway-Auth-Secret' => '3d15e8ed6131446ea7e3456728b1211f'
        ])->post($this->baseUrl . '/transacoes/reembolso', [
            'id' => $transactionId
        ])->json();
    }
    public function processPayment(array $data): array
    {
        return $this->charge($data);
    }

    public function getId(): int
    {
        return 2;
    }
}