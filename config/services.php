<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Stripe, Mailgun, Mandrill, and others. This file provides a sane
    | default location for this type of information, allowing packages
    | to have a conventional place to find your various credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
    ],

    'ses' => [
        'key' => env('SES_KEY'),
        'secret' => env('SES_SECRET'),
        'region' => 'us-east-1',
    ],

    'sparkpost' => [
        'secret' => env('SPARKPOST_SECRET'),
    ],

    'stripe' => [
        'model' => App\User::class,
        'key' => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
    ],
    
    'google' => [
        'client_id' => '299660833452-eob0ae4jllaaepvbjsv4jd4qmgsvdtq5.apps.googleusercontent.com',
        'client_secret' => 'yVsMmoZ9QVUsdCdKVS5DdU3Q',
        'redirect' => 'http://localhost:8000/auth/google/callback',
    ],
    'facebook' => [
        'client_id' => '1044535712297851',
        'client_secret' => '2da6f68e539903dcf1c53b9d0ff01020',
        'redirect' => 'http://localhost:8000/auth/facebook/callback',
    ]

];
