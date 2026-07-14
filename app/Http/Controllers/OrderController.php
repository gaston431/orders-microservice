<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Order::all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'product_id' => 'required|integer',
            'quantity' => 'required|integer|min:1',
        ]);

        $productId = $request->input('product_id');
        $quantity = $request->input('quantity');

        // Llamada HTTP interna hacia el microservicio de productos
        // En producción, reemplaza localhost por el nombre del servicio o dominio interno
        // $response = Http::get("http://localhost:8001/api/products/{$productId}");

        $url = env('PRODUCT_SERVICE_URL', 'http://app-productos:80') . "/api/products/{$productId}";
        $response = Http::get($url);
        
        if ($response->failed()) {
            return response()->json(['error' => 'El producto no existe o el servicio no responde'], 400);
        }

        $product = $response->json();

        // Validar stock antes de confirmar el pedido
        if ($product['stock'] < $quantity) {
            return response()->json(['error' => 'Stock insuficiente'], 400);
        }

        // Actualizar stock de producto
        $updateResponse = Http::put(
            $url,
            ['stock' => $product['stock'] - $quantity]
        );

        if ($updateResponse->failed()) {
            return response()->json(['error' => 'No se pudo actualizar el stock del producto'], 500);
        }

        DB::beginTransaction();
        try {
            $totalPrice = $product['price'] * $quantity;

            $order = Order::create([
                'product_id' => $productId,
                'quantity' => $quantity,
                'total_price' => $totalPrice,
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Pedido registrado con éxito',
                'order' => $order
            ], 201);

        } catch (Exception $e) {
            DB::rollBack();

            // --- ACCIÓN DE COMPENSACIÓN ---
            // Si la base de datos local falló, le devolvemos el stock original a Productos
            Http::put($url, ['stock' => $product['stock']]);

            return response()->json([
                'error' => 'Fallo interno al registrar el pedido. Operación revertida.',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
