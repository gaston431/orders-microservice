<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

/**
 * Stub: existe solo para que Laravel pueda serializar el Job al despacharlo.
 * La logica real (handle) vive en email-microservice, que consume este mensaje.
 */
class SendOrderEmailJob implements ShouldQueue
{
    use Queueable;

    public array $orderData;

    public function __construct(array $orderData = [])
    {
        $this->orderData = $orderData;
    }
}