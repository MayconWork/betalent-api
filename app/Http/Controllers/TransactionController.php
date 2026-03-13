<?php

namespace App\Http\Controllers;

use App\Models\Transaction;

class TransactionController extends Controller
{
    public function index()
    {
        return Transaction::with([
            'client',
            'products',
            'gateway'
        ])->get();
    }
    public function show($id)
    {
        return Transaction::with([
            'client',
            'products',
            'gateway'
        ])->findOrFail($id);
    }
}
