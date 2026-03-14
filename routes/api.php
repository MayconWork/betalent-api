<?php
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\GatewayController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PurchaseController;
use Illuminate\Support\Facades\Route;

// Rotas públicas
Route::post('/login', [AuthController::class, 'login']);
Route::post('/transactions', [PurchaseController::class, 'purchase']);

// Rotas privadas
Route::middleware('auth:sanctum')->group(function () {

    // Produtos — ADMIN/MANAGER/FINANCE
    Route::get('products', [ProductController::class, 'index']);
    Route::get('products/{id}', [ProductController::class, 'show']);
    Route::post('products', [ProductController::class, 'store'])
        ->middleware('role:ADMIN,MANAGER,FINANCE');
    Route::put('products/{id}', [ProductController::class, 'update'])
        ->middleware('role:ADMIN,MANAGER,FINANCE');
    Route::delete('products/{id}', [ProductController::class, 'destroy'])
        ->middleware('role:ADMIN');

    // Clientes
    Route::get('clients', [ClientController::class, 'index']);
    Route::get('clients/{id}', [ClientController::class, 'show']);

    // Transações
    Route::get('transactions', [TransactionController::class, 'index']);
    Route::get('transactions/{id}', [TransactionController::class, 'show']);
    Route::post('transactions/{id}/refund', [TransactionController::class, 'refund'])
        ->middleware('role:ADMIN,FINANCE');

    // Gateways
    Route::patch('gateways/{id}/toggle', [GatewayController::class, 'toggle'])
        ->middleware('role:ADMIN');
    Route::patch('gateways/{id}/priority', [GatewayController::class, 'updatePriority'])
        ->middleware('role:ADMIN');

    // Usuários
    Route::apiResource('users', UserController::class)
        ->middleware('role:ADMIN,MANAGER');
});