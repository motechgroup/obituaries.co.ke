<?php

return [
    'env' => env('MPESA_ENV', 'sandbox'),
    'consumer_key' => env('MPESA_CONSUMER_KEY', ''),
    'consumer_secret' => env('MPESA_CONSUMER_SECRET', ''),
    'shortcode' => env('MPESA_SHORTCODE', '174379'),
    'passkey' => env('MPESA_PASSKEY', ''),
    'callback_url' => env('MPESA_CALLBACK_URL', 'http://localhost/api/v1/mpesa/callback'),
    'mock_mode' => env('MPESA_MOCK_MODE', true),

    'urls' => [
        'sandbox' => [
            'oauth' => 'https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials',
            'stkpush' => 'https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest',
            'stkquery' => 'https://sandbox.safaricom.co.ke/mpesa/stkpushquery/v1/query',
        ],
        'live' => [
            'oauth' => 'https://api.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials',
            'stkpush' => 'https://api.safaricom.co.ke/mpesa/stkpush/v1/processrequest',
            'stkquery' => 'https://api.safaricom.co.ke/mpesa/stkpushquery/v1/query',
        ],
    ],
];
