<?php

namespace App\Http\Controllers;

use App\Models\Client;

class ClientController extends Controller
{
    public function index()
    {
        return Client::with('transactions')->get();
    }
    
    public function show($id)
    {
        return Client::with([
            'transactions.products'
        ])->findOrFail($id);
    }
}