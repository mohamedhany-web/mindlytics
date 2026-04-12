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

    /**
     * سر توقيع HMAC لـ iframe (Domain&ProviderKey). إن ترك فارغاً يُستخدم FAWATERAK_VENDOR_KEY.
     * إن كان في لوحة فواتيرك حقل Secret منفصل عن API Key، ضعه هنا.
     */
    'hmac_secret' => env('FAWATERAK_HMAC_SECRET', ''),

    /**
     * نطاق يدوي لسلسلة الـ HMAC عندما لا يُرسل hostname موثوق من المتصفح.
     * يجب أن يطابق ما تُرسله الإضافة وما سجّلته في لوحة فواتيرك (بدون / أخيرة، بدون منفذ).
     */
    'iframe_domain' => env('FAWATERAK_IFRAME_DOMAIN', ''),

    /** طلب تجريبي لـ getPaymentmethods مع HASH قبل إرجاع pluginConfig (يوضح سبب «Invalid Token» بدل تنبيه المتصفح) */
    'iframe_preflight' => env('FAWATERAK_IFRAME_PREFLIGHT', true),

    'currency' => env('FAWATERAK_CURRENCY', 'EGP'),

    /** نسخة الإضافة (لوحة فواتيرك / الوثائق) */
    'version' => env('FAWATERAK_VERSION', '0'),

    /**
     * Bearer لسكربت الإضافة: تحميل السكربت من خوادم فواتيرك، وحقل pluginConfig.token لطلبات الويدجت.
     * إن ترك فارغاً يُستخدم FAWATERAK_VENDOR_KEY (عندما يكون نفس المفتاح صالحاً للطرفين).
     */
    'plugin_bearer_token' => env('FAWATERAK_PLUGIN_BEARER_TOKEN', ''),

    'plugin_urls' => [
        'test' => env('FAWATERAK_TEST_PLUGIN_URL', 'https://app.fawaterk.com/fawaterkPlugin/fawaterkPlugin.min.js'),
        'live' => env('FAWATERAK_LIVE_PLUGIN_URL', 'https://app.fawaterk.com/fawaterkPlugin/fawaterkPlugin.min.js'),
    ],
];
