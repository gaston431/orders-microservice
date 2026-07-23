<?php

return [

    'default' => env('QUEUE_CONNECTION', 'sync'),

    // Nueva clave: define qué conexión usa el flujo de eventos de pedidos
    'order_events_connection' => env('ORDER_EVENTS_CONNECTION', 'rabbitmq'),

    'connections' => [

        'sync' => [
            'driver' => 'sync',
        ],

        'database' => [
            'driver' => 'database',
            'table' => 'jobs',
            'queue' => 'default',
            'retry_after' => 90,
            'after_commit' => false,
        ],

        // --- CONEXIÓN DE RABBITMQ ULTRA-LIMPIA Y ACTUALIZADA ---
        'rabbitmq' => [
            'driver' => 'rabbitmq',
            'queue' => env('RABBITMQ_QUEUE', 'orders_queue'),
            'hosts' => [
                [
                    'host' => env('RABBITMQ_HOST', 'rabbitmq'),
                    'port' => env('RABBITMQ_PORT', 5672),
                    'user' => env('RABBITMQ_USER', 'guest'),
                    'password' => env('RABBITMQ_PASSWORD', 'guest'),
                    'vhost' => env('RABBITMQ_VHOST', '/'),
                ],
            ],
            'options' => [
                'ssl_options' => [
                    'ssl_on' => env('RABBITMQ_SSL', false),
                ],
            ],
        ],

        'redis' => [
            'driver' => 'redis',
            'connection' => 'default',
            'queue' => env('REDIS_QUEUE', 'orders_queue'),
            'retry_after' => 90,
            'block_for' => null,
        ],

    ],

    'failed' => [
        'driver' => env('FAILED_JOB_DRIVER', 'database-uuids'),
        'database' => env('DB_CONNECTION', 'mysql'),
        'table' => 'failed_jobs',
    ],

];
