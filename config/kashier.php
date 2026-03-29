<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Kashier Payment Gateway
    |--------------------------------------------------------------------------
    | بوابة الدفع كاشير - إعدادات التكامل
    | التوثيق: https://developers.kashier.io/
    */

    'mode' => env('KASHIER_MODE', 'test'), // test | live

    'test' => [
        'api_base_url' => env('KASHIER_TEST_API_BASE_URL', 'https://test-api.kashier.io'),
        'mid' => env('KASHIER_TEST_MID', ''),
        'api_key' => env('KASHIER_TEST_API_KEY', ''),
        'secret' => env('KASHIER_TEST_SECRET', ''),
        'base_url' => env('KASHIER_TEST_BASE_URL', 'https://checkout.kashier.io'),
    ],

    'live' => [
        'api_base_url' => env('KASHIER_LIVE_API_BASE_URL', 'https://api.kashier.io'),
        'mid' => env('KASHIER_LIVE_MID', ''),
        'api_key' => env('KASHIER_LIVE_API_KEY', ''),
        'secret' => env('KASHIER_LIVE_SECRET', ''),
        'base_url' => env('KASHIER_LIVE_BASE_URL', 'https://checkout.kashier.io'),
    ],

    'currency' => env('KASHIER_CURRENCY', 'EGP'),

    'allowed_methods' => env('KASHIER_ALLOWED_METHODS', 'card,wallet,bank_installments'),

    /*
     * قاعدة واجهة الدفع (iframe/redirect) عند غياب sessionUrl في الاستجابة.
     * التوثيق: https://payments.kashier.io/session/{sessionId}?mode=test|live
     */
    'payments_session_base' => env('KASHIER_PAYMENTS_SESSION_BASE', 'https://payments.kashier.io'),

    /*
     * رابط العودة بعد الدفع. إن تركته فارغاً يُبنى من APP_URL + /checkout/kashier/callback (مع ترقية http→https).
     */
    'merchant_redirect_url' => env('KASHIER_MERCHANT_REDIRECT_URL', ''),

    /*
     * إن كان true: يُرسل merchantRedirect بعد rawurlencode (بعض صياغ وثائق كاشير).
     * الافتراضي false — جرّبه فقط إذا بقي خطأ "must be a valid URL".
     */
    'encode_merchant_redirect' => filter_var(env('KASHIER_ENCODE_MERCHANT_REDIRECT', false), FILTER_VALIDATE_BOOL),

];
