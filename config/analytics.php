<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Marketing web analytics (GTM / GA4 / Clarity / Meta Pixel)
    |--------------------------------------------------------------------------
    |
    | Env values are defaults. Admins can override them from
    | Marketing → تتبع التسويق (MarketingWebAnalyticsSettings JSON).
    | Prefer GTM as the primary injector; GA4 is usually configured inside GTM.
    |
    */

    'enabled' => (bool) env('ANALYTICS_ENABLED', true),

    /** Only inject on public/marketing surfaces (never employee/admin by default). */
    'public_only' => (bool) env('ANALYTICS_PUBLIC_ONLY', true),

    'gtm_container_id' => env('GTM_CONTAINER_ID'),

    /** Reference / docs — actual GA4 hits should go through GTM when GTM is used. */
    'ga4_measurement_id' => env('GA4_MEASUREMENT_ID'),

    'clarity_project_id' => env('CLARITY_PROJECT_ID'),

    /** Meta (Facebook) Pixel for ads attribution & conversions. */
    'meta_pixel_id' => env('META_PIXEL_ID'),

    'meta_pixel_enabled' => (bool) env('META_PIXEL_ENABLED', true),

    'currency' => env('ANALYTICS_CURRENCY', 'EGP'),

    'item_brand' => env('ANALYTICS_ITEM_BRAND', 'Mindlytics'),
];
