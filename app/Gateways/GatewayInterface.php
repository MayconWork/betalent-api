<?php

namespace App\Gateways;

interface GatewayInterface
{
    public function processPayment(array $data): array;
}