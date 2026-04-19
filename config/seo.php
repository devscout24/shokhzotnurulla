<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default SEO Values
    |--------------------------------------------------------------------------
    */

    'defaults' => [

        'title' => env('APP_NAME', 'StacksMind Pvt Ltd.'),

        'description' => 'Default website description',

        'keywords' => '',

        'image' => '/default-og.jpg',

        'robots' => 'index, follow',

        'type' => 'website',

    ],

    /*
    |--------------------------------------------------------------------------
    | Tracking Cache TTL (in seconds)
    |--------------------------------------------------------------------------
    |
    | Defines how long tracking scripts should be cached.
    | Recommended: 3600 (1 hour)
    |
    */

    'tracking_cache_ttl' => 3600,

];