<?php

namespace App\Listeners;

use App\Events\OrderCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Http;

class SendEmailNotification implements ShouldQueue
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(OrderCreated $event): void
    {
        $emailServiceUrl = env('EMAIL_SERVICE_URL', 'http://app-email:80');

        // La llamada HTTP pesada se ejecuta aquí en segundo plano
        Http::post($emailServiceUrl . '/api/email/order', [
            'order_id' => $event->order->id,
            'user_name' => $event->userName,
            'user_email' => $event->userEmail,
            'product_name' => $event->productName,
            'quantity' => $event->order->quantity,
            'total_price' => $event->order->total_price
        ]);
    }
}
