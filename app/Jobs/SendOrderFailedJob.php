<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SendOrderFailedJob implements ShouldQueue
{
    use Queueable;

    public array $productData;
    /**
     * Create a new job instance.
     */
    public function __construct(array $productData = [])
    {
        $this->productData = $productData;
    }
}
