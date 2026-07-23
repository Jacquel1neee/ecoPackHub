<?php

return [
    'enabled' => env('TOYYIBPAY_ENABLED', false),
    'mode' => env('TOYYIBPAY_MODE', 'test'),
    'secret_key' => env('TOYYIBPAY_SECRET_KEY', ''),
    'category_code' => env('TOYYIBPAY_CATEGORY_CODE', ''),
    'endpoint' => env('TOYYIBPAY_ENDPOINT', ''),
    'auto_endpoint_fallback' => env('TOYYIBPAY_AUTO_ENDPOINT_FALLBACK', true),
    'payment_url_base' => env('TOYYIBPAY_PAYMENT_URL_BASE', ''),
    'return_url' => env('TOYYIBPAY_RETURN_URL', env('APP_URL') . '/orders/{order}/payment-return'),
    'callback_url' => env('TOYYIBPAY_CALLBACK_URL', env('APP_URL') . '/payment/callback'),
];
