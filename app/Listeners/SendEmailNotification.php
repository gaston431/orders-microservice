<?php

namespace App\Listeners;

use App\Events\OrderCreated;
use App\Jobs\SendOrderEmailJob;

class SendEmailNotification
{
    public function handle(OrderCreated $event): void
    {
        SendOrderEmailJob::dispatch([
            'order_id'     => $event->order->id,
            'user_name'    => $event->userName,
            'user_email'   => $event->userEmail,
            'product_name' => $event->productName,
            'quantity'     => $event->order->quantity,
            'total_price'  => $event->order->total_price,
        ])
            ->onConnection('rabbitmq')
            ->onQueue('orders_queue');
    }
}