<?php

namespace App\Gateways;

interface GatewayInterface
{
    public function charge(array $data);

    public function refund(string $transactionId);
    
    public function processPayment(array $data): array;
}