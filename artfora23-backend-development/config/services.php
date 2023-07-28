<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Stripe, Mailgun, SparkPost and others. This file provides a sane
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

    'twilio' => [
        'account_sid' => getenv('TWILIO_ACCOUNT_SID'),
        'auth_token' => getenv('TWILIO_AUTH_TOKEN'),
        'verification_sid' => getenv('TWILIO_VERIFICATION_SID')
    ],

    'stripe' => [
        'model' => App\Models\User::class,
        'key' => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
        'webhook_secret' => env('STRIPE_WEBHOOK_SECRET'),
        'webhook_secret_order' => env('STRIPE_WEBHOOK_SECRET_ORDER'),
        'stripe_currency' => env('STRIPE_CURRENCY', 'EUR'),
        'stripe_seller_payment_after_order_days' => env('STRIPE_SELLER_PAYMENT_AFTER_ORDER_DAYS', 10),
        'stripe_seller_subscription_price_id' => env('STRIPE_SELLER_SUBSCRIPTION_PRICE_ID'),
        'stripe_product_id' => env('STRIPE_PRODUCT_ID'),
    ],

    'mtcaptcha' => [
        'secret' => env('MTCAPTCHA_PRIVATE_KEY')
    ],
    'fedex' => [
        "client_id" => getenv('FEDEX_CLIENT_ID'),
        "client_secret" => getenv("FEDEX_CLIENT_SECRET"),
        "key" => getenv('FEDEX_API_KEY'),
        "account_number" => getenv('FEDEX_API_ACCOUNT_NUMBER'),
        "meter_number" => getenv('FEDEX_METER_NUMBER'),
        "password" => getenv('FEDEX_API_PASSWORD'),
    ],
];
