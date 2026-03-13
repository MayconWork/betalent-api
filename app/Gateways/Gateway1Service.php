<?php

namespace App\Gateways;

use Illuminate\Support\Facades\Http;

class Gateway1Service implements GatewayInterface
{
    private $baseUrl = 'http://gateways-mock:3001';

    private function getToken()
    {
        $response = Http::post($this->baseUrl.'/login', [
            'email' => 'dev@betalent.tech',
            'token' => 'FEC9BB078BF338F464F96B48089EB498'
        ]);

        return $response->json()['token'] ?? null;
    }

    public function charge(array $data)
    {
        $token = $this->getToken();

        $response = Http::withToken($token)
            ->post($this->baseUrl.'/transactions', [
                'amount' => $data['amount'],
                'name' => $data['name'],
                'email' => $data['email'],
                'cardNumber' => $data['cardNumber'],
                'cvv' => $data['cvv']
            ]);

        return $response->json();
    }

    public function refund(string $transactionId)
    {
        return Http::post($this->baseUrl."/transactions/$transactionId/charge_back")
            ->json();
    }

    public function processPayment(array $data): array
    {
        return $this->charge($data);
    }

    public function getId(): int
    {
        return 1;
    }
}