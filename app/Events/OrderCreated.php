<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\Order;

class OrderCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Order $order;
    public string $userName;
    public string $userEmail;
    public string $productName;

    /**
     * Create a new event instance.
     */
    public function __construct(Order $order)
    {
        $this->order = $order;

        // Usamos fallbacks ("invitado", etc.) por seguridad en caso de que falten
        $this->userName    = $order->temp_user_name ?? 'Usuario';
        $this->userEmail   = $order->temp_user_email ?? 'no-reply@tienda.com';
        $this->productName = $order->temp_product_name ?? 'Producto';
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('channel-name'),
        ];
    }
}
