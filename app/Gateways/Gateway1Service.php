<?php

namespace App\Gateways;

class Gateway1Service implements GatewayInterface
{
    public function processPayment(array $data): array
    {
        return [
            'success' =>true,
            'transaction_id' => uniqid(),
            'message' => 'payment processed by Gateway 1'
        ];
    }
}