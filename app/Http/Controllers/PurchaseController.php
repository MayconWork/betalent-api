<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Client;
use App\Services\PaymentService;

class PurchaseController extends Controller
{
    protected $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    public function purchase(Request $request)
    {
        $data = $request->validate([
            'products'                => 'required|array|min:1',
            'products.*.product_id'   => 'required|integer|exists:products,id',
            'products.*.quantity'     => 'required|integer|min:1',
            'name'                    => 'required|string|max:255',
            'email'                   => 'required|email|max:255',
            'cardNumber'              => 'required|string|size:16',
            'cvv'                     => 'required|string|min:3|max:4',
        ]);

        // Cria ou busca o cliente
        $client = Client::firstOrCreate(
            ['email' => $data['email']],
            ['name' => $data['name']]
        );

        // Calcula o total
        $amount = 0;
        foreach ($data['products'] as $item) {
            $product = \App\Models\Product::findOrFail($item['product_id']);
            $amount += $product->amount * $item['quantity'];
        }

        $result = $this->paymentService->process([
            'client_id' => $client->id,
            'products' => $data['products'],
            'amount' => $amount,
            'name' => $data['name'],
            'email' => $data['email'],
            'cardNumber' => $data['cardNumber'],
            'cvv' => $data['cvv']
        ]);

        return response()->json($result, $result['success'] ? 200 : 422);
    }
}