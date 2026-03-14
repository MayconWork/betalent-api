<?php
namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Transaction;
use App\Services\PaymentService;
use Illuminate\Http\Request;

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
        // implemente conforme necessário
    }
}