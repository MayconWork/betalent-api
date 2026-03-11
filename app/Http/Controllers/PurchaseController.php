<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PurchaseController extends Controller
{
    public function purchase(Request $request)
    {
        return response()->json([
            'message' => 'Purchase endpoint working'
        ]);
    }
}