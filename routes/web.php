<?php

use Illuminate\Support\Facades\Route;

// Ruta raíz básica de control de salud
Route::get('/', function () {
    return response()->json(['status' => 'Microservicio de Pedidos Activo']);
});