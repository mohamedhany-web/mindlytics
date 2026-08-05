<?php

return [
    'enabled' => true,
    'penalty_enabled' => true,
    'penalty_amount' => 50.0,
    'reminder_hour' => 17,
    'work_days_only' => true,
    'exclude_sales_employees' => true,
    /** لا تُحتسب أي غرامة لتاريخ أقدم من هذا اليوم (Y-m-d) — يمنع الخصم بأثر رجعي */
    'penalty_effective_from' => null,
];
