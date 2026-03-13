<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PurchaseController;

// Rota de exemplo para a página inicial
Route::get('/', function () {
    return view('welcome');
});