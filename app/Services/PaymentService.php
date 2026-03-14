<?php
namespace App\Services;

use App\Models\Gateway;
use App\Models\Transaction;
use App\Gateways\Gateway1Service;
use App\Gateways\Gateway2Service;

class PaymentService
{
    private array $gatewayMap = [
        1 => Gateway1Service::class,
        2 => Gateway2Service::class,
    ];

    public function process(array $paymentData): array
    {
        $gateways = Gateway::where('is_active', true)
            ->orderBy('priority')
            ->get();

        foreach ($gateways as $gateway) {
            if (!isset($this->gatewayMap[$gateway->id])) {
                continue;
            }

            $service = app($this->gatewayMap[$gateway->id]);

            try {
                $response = $service->processPayment($paymentData);

                if (!empty($response['transaction_id']) || !empty($response['id'])) {
                    $transaction = Transaction::create([
                        'client_id'         => $paymentData['client_id'],
                        'gateway_id'        => $gateway->id,
                        'external_id'       => $response['transaction_id'] ?? $response['id'] ?? null,
                        'status'            => 'approved',
                        'amount'            => $paymentData['amount'],
                        'card_last_numbers' => substr($paymentData['cardNumber'], -4)
                    ]);

                    foreach ($paymentData['products'] as $item) {
                        $transaction->products()->attach(
                            $item['product_id'],
                            ['quantity' => $item['quantity']]
                        );
                    }

                    return [
                        'success'        => true,
                        'transaction_id' => $response['transaction_id'] ?? $response['id']
                    ];
                }
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::error('Gateway ' . $gateway->name . ' failed: ' . $e->getMessage());
                continue;
            }
        }

        return [
            'success' => false,
            'message' => 'All gateways failed'
        ];
    }
}