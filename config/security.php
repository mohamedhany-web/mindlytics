<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Security Configuration
    |--------------------------------------------------------------------------
    |
    | إعدادات الأمان العامة للنظام
    |
    */

    // IPs المحظورة
    'blocked_ips' => env('BLOCKED_IPS', ''),
    
    // Rate Limiting
    'rate_limiting' => [
        'enabled' => env('RATE_LIMITING_ENABLED', true),
        'max_attempts' => env('RATE_LIMITING_MAX_ATTEMPTS', 60),
        'decay_minutes' => env('RATE_LIMITING_DECAY_MINUTES', 1),
    ],

    // File Upload Security
    'file_upload' => [
        'max_size' => env('FILE_UPLOAD_MAX_SIZE', 10485760), // 10 MB
        'allowed_mimes' => [
            'image' => ['image/jpeg', 'image/png', 'image/gif', 'image/webp'],
            'video' => ['video/mp4', 'video/webm', 'video/ogg'],
            'document' => [
                'application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'text/csv',
            ],
        ],
        'blocked_extensions' => ['php', 'phtml', 'php3', 'php4', 'php5', 'phps', 'phar', 'exe', 'bat', 'sh', 'js', 'jar'],
    ],

    // Password Requirements
    'password' => [
        'min_length' => env('PASSWORD_MIN_LENGTH', 8),
        'require_uppercase' => env('PASSWORD_REQUIRE_UPPERCASE', true),
        'require_lowercase' => env('PASSWORD_REQUIRE_LOWERCASE', true),
        'require_numbers' => env('PASSWORD_REQUIRE_NUMBERS', true),
        'require_symbols' => env('PASSWORD_REQUIRE_SYMBOLS', false),
    ],

    // Session Security
    'session' => [
        'lifetime' => env('SESSION_LIFETIME', 120),
        'secure' => env('SESSION_SECURE', false),
        'http_only' => env('SESSION_HTTP_ONLY', true),
        'same_site' => env('SESSION_SAME_SITE', 'lax'),
    ],

    // CSRF Protection
    'csrf' => [
        'enabled' => env('CSRF_ENABLED', true),
        'except' => [
            // Routes مستثناة من CSRF
        ],
    ],

    // Security Logging
    'logging' => [
        'enabled' => env('SECURITY_LOGGING_ENABLED', true),
        'log_suspicious_activity' => env('LOG_SUSPICIOUS_ACTIVITY', true),
        'log_failed_logins' => env('LOG_FAILED_LOGINS', true),
        'log_file_uploads' => env('LOG_FILE_UPLOADS', true),
    ],
];
