<?php

namespace App\Listeners;

use App\Events\OrderFailed;
use App\Jobs\SendOrderFailedJob;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendOrderFailed
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
    public function handle(OrderFailed $event): void
    {
        SendOrderFailedJob::dispatch([
            'product_id'    =>  $event->productId,
            'quantity'      =>  $event->quantity
        ])
            ->onConnection(config('queue.order_events_connection'))
            ->onQueue('product_events_queue');
    }
}
