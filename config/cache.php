<?php

return [
    'default' => env('CACHE_DRIVER', 'redis'),
    'stores' => [
        //this is the 'redis' cache store
        'redis' => [
            //uses the redis driver from config/database.php
            'driver' => 'redis',
            //uses the 'cache' connection from the redis driver in config/database.php
            'connection' => 'cache',
        ],
    ],
    //this is the redis cache key prefix applied to all cache keys
    'prefix' => '',

    'stores' => [

        'apc' => [
            'driver' => 'apc',
        ],

        'array' => [
            'driver' => 'array',
            'serialize' => false,
        ],

        'database' => [
            'driver' => 'database',
            'table' => 'cache',
            'connection' => null,
            'lock_connection' => null,
        ],

        'redis' => [
            'driver' => 'redis',
            'connection' => 'cache',
            'lock_connection' => 'default',
        ],
    ],
];
