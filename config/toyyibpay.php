<?php

return [
    'enabled' => env('TOYYIBPAY_ENABLED', false),
    'mode' => env('TOYYIBPAY_MODE', 'test'),
    'secret_key' => env('TOYYIBPAY_SECRET_KEY', ''),
    'category_code' => env('TOYYIBPAY_CATEGORY_CODE', ''),
    'endpoint' => env('TOYYIBPAY_ENDPOINT', 'https://toyyibpay.com/index.php/api/createBill'),
    'payment_url_base' => env('TOYYIBPAY_PAYMENT_URL_BASE', 'https://toyyibpay.com'),
    'return_url' => env('TOYYIBPAY_RETURN_URL', env('APP_URL') . '/orders/{order}/payment-return'),
    'callback_url' => env('TOYYIBPAY_CALLBACK_URL', env('APP_URL') . '/payment/callback'),
];
