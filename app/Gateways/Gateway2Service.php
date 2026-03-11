<?php

namespace App\Gateways;

class Gateway2Service implements GatewayInterface
{
    public function processPayment(array $data): array
    {
        return [
            'success' => true,
            'transaction_id' => uniqid(),
            'message' => 'Payment Processed by Gateway 2'
        ];
    }
}