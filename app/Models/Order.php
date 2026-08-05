<?php

namespace App\Models;

use App\Events\OrderCreated;
// use Illuminate\Database\Eloquent\Model;
use MongoDB\Laravel\Eloquent\Model;

class Order extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'orders';
    protected $fillable = ['product_id','quantity','total_price'];

    // --- PROPIEDADES CONTEXTUALES PARA EL EVENTO (NO VAN A LA BASE DE DATOS) ---
    public ?string $temp_user_name = null;
    public ?string $temp_user_email = null;
    public ?string $temp_product_name = null;
    
    protected $dispatchesEvents = [
        'created' => OrderCreated::class,
    ];
}
