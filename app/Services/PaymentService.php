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
    
    public function index()
    {
        return Transaction::with(['client','products','gateway'])->get();
    }

    public function process(array $paymentData): array
    {
        foreach ($this->gateways as $gateway) {

            try {

                $response = $gateway->processPayment($paymentData);

                if (!empty($response['transaction_id']) || !empty($response['id'])) {

                    $transaction = Transaction::create([
                        'client_id' => $paymentData['client_id'],
                        'gateway_id' => $gateway->getId(),
                        'external_id' => $response['transaction_id'] ?? $response['id'] ?? null,
                        'status' => 'approved',
                        'amount' => $paymentData['amount'],
                        'card_last_numbers' => substr($paymentData['cardNumber'], -4)
                    ]);

                    $transaction->products()->attach(
                        $paymentData['product_id'],
                        ['quantity' => $paymentData['quantity']]
                    );

                    return [
                        'success' => true,
                        'transaction_id' => $response['transaction_id'] ?? $response['id']
                    ];
                }

            } catch (\Throwable $e) {
                \Log::error($e);
                throw $e;
            }
        }

        return [
            'success' => false,
            'message' => 'All gateways failed'
        ];
    }
}