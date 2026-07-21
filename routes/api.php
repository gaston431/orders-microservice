<?php

use App\Http\Controllers\OrderController;
use Illuminate\Support\Facades\Route;

Route::get('/orders', [OrderController::class, 'index']);

// Ruta directa al controlador protegida por el middleware JWT
Route::middleware(['auth.jwt:admin,client'])->group(function () {
    Route::post('/orders', [OrderController::class, 'store']);
});