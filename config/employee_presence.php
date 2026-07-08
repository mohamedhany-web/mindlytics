<?php

return [
    'enabled' => env('EMPLOYEE_PRESENCE_ENABLED', true),

    /** نبضة كل X ثانية — يجب أن يكون النظام مفتوحاً */
    'heartbeat_interval_seconds' => (int) env('EMPLOYEE_PRESENCE_HEARTBEAT', 45),

    /** بدون نبضة → «بعيد» (away) */
    'away_threshold_seconds' => (int) env('EMPLOYEE_PRESENCE_AWAY', 120),

    /** بدون نبضة → «غير متصل» + تسجيل مخالفة */
    'offline_threshold_seconds' => (int) env('EMPLOYEE_PRESENCE_OFFLINE', 300),

    /** أقل مدة انقطاع تُسجَّل كمخالفة */
    'violation_min_seconds' => (int) env('EMPLOYEE_PRESENCE_VIOLATION_MIN', 180),

    /** خصم تلقائي عند تجاوز دقائق الانقطاع خلال اليوم */
    'presence_penalty_enabled' => true,
    'presence_penalty_amount' => 35.0,
    'presence_penalty_min_offline_minutes' => 15,
    'presence_penalty_title' => 'غرامة انقطاع عن النظام أثناء الدوام',
];
