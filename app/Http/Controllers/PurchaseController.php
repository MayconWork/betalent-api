<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
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
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'name' => 'required|string',
            'email' => 'required|email',
            'cardNumber' => 'required|string',
            'cvv' => 'required|string'
        ]);

        $client = Client::firstOrCreate([
            'email' => $data['email']
        ],[
            'name' => $data['name']
        ]);

        $product = Product::findOrFail($data['product_id']);

        $amount = $product->amount * $data['quantity'];

        $result = $this->paymentService->process([
            'client_id' => $client->id,
            'amount' => $amount,
            'cardNumber' => $data['cardNumber'],
            'cvv' => $data['cvv']
        ]);

        return response()->json($result);
    }
}