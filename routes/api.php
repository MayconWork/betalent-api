<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\ClientController;

Route::post('/login', [AuthController::class, 'login']);

Route::post('/purchase', [PurchaseController::class, 'purchase']);

Route::get('/transactions', [TransactionController::class, 'index']);

Route::get('/transactions/{id}', [TransactionController::class, 'show']);

Route::get('/clients', [ClientController::class, 'index']);