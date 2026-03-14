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
        if (Auth::user()->role !== UserRole::ADMIN) {
            abort(403);
        }

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0'
        ]);

        return Product::create($data);
    }

    public function show($id)
    {
        return Product::findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        if (!in_array(Auth::user()->role, [UserRole::ADMIN, UserRole::MANAGER])) {
            abort(403, 'Unauthorized');
        }

        $product = Product::findOrFail($id);
        $product->update($request->all());

        return $product;
    }

    public function destroy($id)
    {
        if (Auth::user()->role !== UserRole::ADMIN) {
            abort(403, 'Only admins can delete products');
        }

        Product::destroy($id);

        return response()->json(null, 204);
    }
}