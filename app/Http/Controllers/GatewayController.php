<?php
namespace App\Http\Controllers;

use App\Models\Gateway;
use Illuminate\Http\Request;

class GatewayController extends Controller
{
    public function toggle($id)
    {
        $gateway = Gateway::findOrFail($id);
        $gateway->update(['is_active' => !$gateway->is_active]);
        return response()->json($gateway);
    }

    public function updatePriority(Request $request, $id)
    {
        $request->validate(['priority' => 'required|integer|min:1']);
        $gateway = Gateway::findOrFail($id);
        $gateway->update(['priority' => $request->priority]);
        return response()->json($gateway);
    }
}