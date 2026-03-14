<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Enums\UserRole;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    
    public function index()
    {
        return Product::all();
    }

    public function store(Request $request)
    {

    $data = $request->validate([
        'name'   => 'required|string|max:255',
        'amount' => 'required|integer|min:0',
    ]);

        return response()->json(Product::create($data), 201);
    }

    public function show($id)
    {
        return response()->json(Product::findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        $product->update($request->all());

        return $product;
    }

    public function destroy($id)
    {

        Product::destroy($id);

        return response()->json(null, 204);
    }
}