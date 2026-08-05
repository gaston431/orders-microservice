<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;
use MongoDB\Client;

class OrderTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_example(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_making_an_api_get_request(): void
    {
        $response = $this->getJson("/api/orders");

        $response
            ->assertOk()
            ->assertJsonStructure([
                '*' => [
                    "product_id",
                    "quantity",
                    "total_price",
                    "updated_at",
                    "created_at",
                    "id"
                ]
            ]);
    }

    public function test_making_an_api_post_request(): void
    {
        // $productId = '66a6cf283d6b00f59500bb1c';

        // Http::fake([
        //     '*' => Http::response([
        //         'id' => $productId,
        //         'name' => 'Laptop',
        //         'price' => 800,
        //         'stock' => 10,
        //     ], 200),
        // ]);

        $client = new Client(env('MONGODB_URI'));

        $product = $client
            ->selectCollection('db_productos', 'products')
            ->findOne();

        $this->assertNotNull($product, 'No existe ningún producto en MongoDB.');

        $productId = (string) $product->_id;

        $response = $this->withHeaders([
            'Authorization' => "Bearer {$this->getAdminToken()}",
        ])->postJson('/api/orders', [
            'product_id' => $productId,
            'quantity'   => 1,
        ]);

        // $response->dump();

        $response
            ->assertCreated()
            ->assertJsonPath('message', 'Pedido registrado con éxito')
            ->assertJsonPath('order.product_id', $productId)
            ->assertJsonPath('order.quantity', 1)
            ->assertJsonStructure([
                'message',
                'order' => [
                    'product_id',
                    'quantity',
                    'total_price',
                    'id',
                    'created_at',
                    'updated_at',
                ],
            ]);
    }
}
