<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Moka API Credentials
    |--------------------------------------------------------------------------
    */
    'dealer_code' => env('MOKA_DEALER_CODE'),
    'username' => env('MOKA_USERNAME'),
    'password' => env('MOKA_PASSWORD'),

    /*
    |--------------------------------------------------------------------------
    | Moka API URLs
    |--------------------------------------------------------------------------
    */
    'sandbox_mode' => env('MOKA_SANDBOX_MODE', true),
    'sandbox_url' => 'https://service.refmoka.com',
    'production_url' => 'https://service.moka.com',

    /*
    |--------------------------------------------------------------------------
    | Payment Settings
    |--------------------------------------------------------------------------
    */
    'store_failed_payments' => env('MOKA_STORE_FAILED_PAYMENTS', false),
    'software' => env('MOKA_SOFTWARE', ''),
    'is_pool_payment' => env('MOKA_IS_POOL_PAYMENT', 0),
    'is_tokenized' => env('MOKA_IS_TOKENIZED', 0),
    'currency' => env('MOKA_CURRENCY', 'TL'),
    'redirect_type' => env('MOKA_REDIRECT_TYPE', 1),
    'language' => env('MOKA_LANGUAGE', 'TR'),
    'is_pre_auth' => env('MOKA_IS_PRE_AUTH', 0),

    /*
    |--------------------------------------------------------------------------
    | Payment Redirect URLs
    |--------------------------------------------------------------------------
    |
    | These URLs will be used to redirect the customer after the payment process
    | is completed. You can set these values in your .env file or override them
    | in your application's config file.
    |
    */
    'payment_success_url' => env('MOKA_PAYMENT_SUCCESS_URL', '/payment/success'),
    'payment_failed_url' => env('MOKA_PAYMENT_FAILED_URL', '/payment/failed'),
];
