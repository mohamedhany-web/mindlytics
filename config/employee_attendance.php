<?php

return [
    'penalties_enabled' => true,
    'late_penalty_enabled' => true,
    'late_penalty_amount' => 25.0,
    'late_penalty_title' => 'غرامة تأخير حضور',
    'absence_penalty_enabled' => true,
    'absence_penalty_amount' => 100.0,
    'absence_penalty_title' => 'غرامة غياب',
    'incomplete_penalty_enabled' => true,
    'incomplete_penalty_amount' => 50.0,
    'incomplete_penalty_title' => 'غرامة عدم إكمال ساعات العمل',
    'penalty_type' => 'penalty',
    'penalty_status' => 'applied',
    'notify_employee' => true,
    /** لا تُحتسب أي غرامة لتاريخ أقدم من هذا اليوم (Y-m-d) — يمنع الخصم بأثر رجعي */
    'penalty_effective_from' => null,

    /** قفل النظام + حضور/انصراف + خصومات الحضور — لموظفي المبيعات فقط */
    'sales_employees_only' => true,
];
