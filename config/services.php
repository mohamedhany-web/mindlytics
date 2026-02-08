<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | WhatsApp Business API Configuration
    |--------------------------------------------------------------------------
    |
    | إعدادات WhatsApp API لإرسال الرسائل والتقارير
    | الأنواع المتاحة: disabled, local, official
    |
    */
    'whatsapp' => [
        'type' => env('WHATSAPP_TYPE', 'disabled'), // disabled, local, official, custom
        'api_url' => env('WHATSAPP_API_URL', 'https://graph.facebook.com/v18.0'),
        'api_token' => env('WHATSAPP_API_TOKEN'),
        'phone_number_id' => env('WHATSAPP_PHONE_NUMBER_ID'),
        'webhook_verify_token' => env('WHATSAPP_WEBHOOK_VERIFY_TOKEN'),
        'app_secret' => env('WHATSAPP_APP_SECRET'),
        'enabled' => env('WHATSAPP_ENABLED', false),
        'local_api_url' => env('WHATSAPP_LOCAL_API_URL', 'http://localhost:3001'),
        // إعدادات API مخصص
        'request_method' => env('WHATSAPP_REQUEST_METHOD', 'POST'),
        'phone_param' => env('WHATSAPP_PHONE_PARAM', 'phone'),
        'message_param' => env('WHATSAPP_MESSAGE_PARAM', 'message'),
        'extra_params' => env('WHATSAPP_EXTRA_PARAMS', '{}'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Platform Settings
    |--------------------------------------------------------------------------
    |
    | إعدادات عامة للمنصة
    |
    */
    'platform' => [
        'support_phone' => env('PLATFORM_SUPPORT_PHONE', '+201000000000'),
        'support_email' => env('PLATFORM_SUPPORT_EMAIL', 'support@platform.com'),
        'monthly_reports_enabled' => env('MONTHLY_REPORTS_ENABLED', true),
        'auto_send_exam_results' => env('AUTO_SEND_EXAM_RESULTS', true),
    ],

];