<?php

namespace Tests\Feature\Integration;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class GatewayOrderFlowTest extends TestCase
{

    private function login(): string
    {
        $login = Http::post(
            env('API_GATEWAY_URL') . '/api/auth/login',
            [
                'email' => 'admin@example.com',
                'password' => 'admin123',
            ]
        );

        $this->assertEquals(200, $login->status());

        $this->assertArrayHasKey('token', $login->json());

        $this->assertEquals(
            'Autenticado con éxito',
            $login->json('message')
        );

        return $login->json('token');
    }

    private function firstProduct(string $token): array
    {
        $response = Http::withToken($token)
            ->get(env('API_GATEWAY_URL') . '/api/products');

        $this->assertTrue($response->successful());
        $this->assertNotEmpty($response->json());

        return $response->json()[0];
    }

    public function test_e2e_post_order(): void
    {

        $token = $this->login();

        $product = $this->firstProduct($token);

        $order = Http::withToken($token)
            ->post(env('API_GATEWAY_URL') . '/api/orders', [
                'product_id' => $product['id'],
                'quantity' => 1
            ]);

        $this->assertEquals(201, $order->status());

        $this->assertEquals(
            'Pedido registrado con éxito',
            $order->json('message')
        );

        $this->assertArrayHasKey('order', $order->json());

        $this->assertArrayHasKey('id', $order->json('order'));
        $this->assertArrayHasKey('product_id', $order->json('order'));
        $this->assertArrayHasKey('quantity', $order->json('order'));
        $this->assertArrayHasKey('total_price', $order->json('order'));
        $this->assertArrayHasKey('created_at', $order->json('order'));
        $this->assertArrayHasKey('updated_at', $order->json('order'));
    }
}
