<?php
namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Transaction;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TransactionController extends Controller
{
    public function __construct(protected PaymentService $paymentService) {}

    public function index()
    {
        return Transaction::with(['client', 'products', 'gateway'])->get();
    }

    public function show($id)
    {
        return Transaction::with(['client', 'products', 'gateway'])->findOrFail($id);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'                    => 'required|string',
            'email'                   => 'required|email',
            'products'                => 'required|array|min:1',
            'products.*.product_id'   => 'required|integer|exists:products,id',
            'products.*.quantity'     => 'required|integer|min:1',
            'cardNumber'              => 'required|string',
            'cvv'                     => 'required|string',
        ]);

        // Busca ou cria o cliente pelo e-mail
        $client = Client::firstOrCreate(
            ['email' => $data['email']],
            ['name'  => $data['name']]
        );

        $paymentData = [
            'client_id'  => $client->id,
            'amount'     => collect($data['products'])->sum(fn($p) => $p['quantity'] * \App\Models\Product::find($p['product_id'])->amount),
            'name'       => $client->name,
            'email'      => $client->email,
            'cardNumber' => $data['cardNumber'],
            'cvv'        => $data['cvv'],
            'products'   => $data['products'],
        ];

        $result = $this->paymentService->process($paymentData);

        return response()->json($result, $result['success'] ? 200 : 422);
    }

    public function refund($id)
    {
        $transaction = Transaction::with('gateway')->findOrFail($id);

        if ($transaction->status === 'refunded') {
            return response()->json(['message' => 'Transaction already refunded'], 422);
        }

        // Chama o gateway correto baseado no gateway_id da transação
        $gatewayId = $transaction->gateway_id;
        $gateway = $gatewayId === 1
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