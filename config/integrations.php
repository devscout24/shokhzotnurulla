<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Integration Providers Catalog
    |--------------------------------------------------------------------------
    |
    | Defines every third-party integration available to dealers.
    | Each entry contains the credential fields required, the integration
    | type (script = frontend injection, api = backend API calls), and
    | optionally the provider service class for testable connections.
    |
    */

    'providers' => [

        'ga4' => [
            'name'   => 'Google Analytics 4',
            'fields' => [
                ['key' => 'measurement_id', 'label' => 'Measurement ID', 'type' => 'text',
                 'placeholder' => 'G-XXXXXXXXXX', 'required' => true],
            ],
            'type' => 'script',
        ],

        'gtm' => [
            'name'   => 'Google Tag Manager',
            'fields' => [
                ['key' => 'container_id', 'label' => 'Container ID', 'type' => 'text',
                 'placeholder' => 'GTM-XXXXXXX', 'required' => true],
            ],
            'type' => 'script',
        ],

        'carfax' => [
            'name'   => 'Carfax',
            'fields' => [
                ['key' => 'username', 'label' => 'Username', 'type' => 'text', 'required' => true],
                ['key' => 'password', 'label' => 'Password', 'type' => 'password', 'required' => true],
            ],
            'type'     => 'api',
            'provider' => \App\Services\Integrations\CarfaxProvider::class,
        ],

        '700credit' => [
            'name'   => '700Credit',
            'fields' => [
                ['key' => 'api_key', 'label' => 'API Key', 'type' => 'password', 'required' => true],
                ['key' => 'dealer_code', 'label' => 'Dealer Code', 'type' => 'text', 'required' => true],
            ],
            'type'     => 'api',
            'provider' => \App\Services\Integrations\Credit700Provider::class,
        ],

        'stripe' => [
            'name'   => 'Stripe',
            'fields' => [
                ['key' => 'publishable_key', 'label' => 'Publishable Key', 'type' => 'text', 'required' => true],
                ['key' => 'secret_key', 'label' => 'Secret Key', 'type' => 'password', 'required' => true],
                ['key' => 'webhook_secret', 'label' => 'Webhook Secret', 'type' => 'password', 'required' => false],
            ],
            'type'     => 'api',
            'provider' => null,
        ],

        'autocheck' => [
            'name'   => 'Auto Check',
            'fields' => [
                ['key' => 'api_key', 'label' => 'API Key', 'type' => 'password', 'required' => true],
                ['key' => 'account_id', 'label' => 'Account ID', 'type' => 'text', 'required' => true],
            ],
            'type'     => 'api',
            'provider' => null,
        ],

        'carnow' => [
            'name'   => 'CarNow',
            'fields' => [
                ['key' => 'dealer_id', 'label' => 'Dealer ID', 'type' => 'text', 'required' => true],
                ['key' => 'embed_script', 'label' => 'Embed Script URL', 'type' => 'text', 'required' => false],
            ],
            'type'     => 'script',
            'provider' => null,
        ],

        'complyauto' => [
            'name'   => 'ComplyAuto',
            'fields' => [
                ['key' => 'api_key', 'label' => 'API Key', 'type' => 'password', 'required' => true],
            ],
            'type'     => 'api',
            'provider' => null,
        ],

        'dealercenter' => [
            'name'   => 'Dealer Center',
            'fields' => [
                ['key' => 'api_key', 'label' => 'API Key', 'type' => 'password', 'required' => true],
                ['key' => 'dealer_code', 'label' => 'Dealer Code', 'type' => 'text', 'required' => true],
            ],
            'type'     => 'api',
            'provider' => null,
        ],

        'driveo' => [
            'name'   => 'Driveo',
            'fields' => [
                ['key' => 'api_key', 'label' => 'API Key', 'type' => 'password', 'required' => true],
            ],
            'type'     => 'api',
            'provider' => null,
        ],

        'ipacket' => [
            'name'   => 'iPacket',
            'fields' => [
                ['key' => 'dealer_id', 'label' => 'Dealer ID', 'type' => 'text', 'required' => true],
                ['key' => 'api_token', 'label' => 'API Token', 'type' => 'password', 'required' => true],
            ],
            'type'     => 'api',
            'provider' => null,
        ],

        'monroney' => [
            'name'   => 'Monroney Labels',
            'fields' => [
                ['key' => 'api_key', 'label' => 'API Key', 'type' => 'password', 'required' => true],
            ],
            'type'     => 'api',
            'provider' => null,
        ],

        'promax' => [
            'name'   => 'ProMax',
            'fields' => [
                ['key' => 'dealer_id', 'label' => 'Dealer ID', 'type' => 'text', 'required' => true],
                ['key' => 'api_key', 'label' => 'API Key', 'type' => 'password', 'required' => true],
            ],
            'type'     => 'api',
            'provider' => null,
        ],

    ],

];
