<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Fawaterak (فواتيرك) — تكامل iframe فقط
    |--------------------------------------------------------------------------
    */
    'integration' => env('FAWATERAK_INTEGRATION', 'iframe'),

    'env' => env('FAWATERAK_ENV', 'test'),

    'vendor_key' => env('FAWATERAK_VENDOR_KEY', ''),
    'provider_key' => env('FAWATERAK_PROVIDER_KEY', ''),

    /** نطاق الـ HMAC (بدون شرطة مائلة في النهاية، مع https://) */
    'iframe_domain' => env('FAWATERAK_IFRAME_DOMAIN', ''),

    'currency' => env('FAWATERAK_CURRENCY', 'EGP'),

    /** نسخة الإضافة (لوحة فواتيرك / الوثائق) */
    'version' => env('FAWATERAK_VERSION', '0'),

    /** اختياري: Bearer لتحميل سكربت الإضافة من خوادم فواتيرك */
    'plugin_bearer_token' => env('FAWATERAK_PLUGIN_BEARER_TOKEN', ''),

    'plugin_urls' => [
        'test' => env('FAWATERAK_TEST_PLUGIN_URL', 'https://app.fawaterk.com/fawaterkPlugin/fawaterkPlugin.min.js'),
        'live' => env('FAWATERAK_LIVE_PLUGIN_URL', 'https://app.fawaterk.com/fawaterkPlugin/fawaterkPlugin.min.js'),
    ],
];
