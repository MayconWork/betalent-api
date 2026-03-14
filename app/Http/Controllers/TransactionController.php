<?php
namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Services\PaymentService;
use Illuminate\Support\Facades\Log;

class TransactionController extends Controller
{
    public function __construct(protected PaymentService $paymentService) {}

    public function index()
    {
        return response()->json(
            Transaction::with(['client', 'products', 'gateway'])->get()
        );
    }

    public function show($id)
    {
        return response()->json(
            Transaction::with(['client', 'products', 'gateway'])->findOrFail($id)
        );
    }

    public function refund($id)
    {
        $transaction = Transaction::with('gateway')->findOrFail($id);

        if ($transaction->status === 'refunded') {
            return response()->json(['message' => 'Transaction already refunded'], 422);
        }

        $gateway = $transaction->gateway_id === 1
            ? app(\App\Gateways\Gateway1Service::class)
            : app(\App\Gateways\Gateway2Service::class);

        try {
            $gateway->refund($transaction->external_id);
            $transaction->update(['status' => 'refunded']);
            return response()->json(['success' => true]);
        } catch (\Throwable $e) {
            Log::error('Refund error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Refund failed'], 500);
        }
    }
}