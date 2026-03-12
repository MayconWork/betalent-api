<?php

namespace App\Services;

use App\Gateways\Gateway1Service;
use App\Gateways\Gateway2Service;
use App\Models\Transaction;

class PaymentService
{
    protected array $gateways;

    public function __construct(
        Gateway1Service $gateway1,
        Gateway2Service $gateway2
    ){
        $this->gateways = [
            $gateway1,
            $gateway2
        ];
    }

    public function process(array $paymentData): array
    {
        foreach ($this->gateways as $gateway) {

            try {

                $response = $gateway->processPayment($paymentData);

                if (($response['success'] ?? false) === true) {

                Transaction::create([
                    'client_id' => $paymentData['client_id'],
                    'gateway_id' => $paymentData['gateway_id'] ?? null,
                    'external_id' => $response['transaction_id'] ?? null,
                    'status' => 'approved',
                    'amount' => $paymentData['amount'],
                    'card_last_numbers' => substr($paymentData['cardNumber'], -4)
                ]);

                    return $response;
                }

            } catch (\Throwable $e) {
                continue;
            }
        }

        return [
            'success' => false,
            'message' => 'All gateways failed'
        ];
    }
}