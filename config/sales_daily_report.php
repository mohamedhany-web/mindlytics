<?php

/**
 * إعدادات التقرير اليومي لموظفي المبيعات (القيم الافتراضية — تُدمج مع sales_daily_report_settings.json).
 */
return [
    'enabled' => true,
    'work_days_only' => true,
    'deadline_time' => '23:59',
    'penalty_enabled' => true,
    'penalty_amount' => 50.00,
    'penalty_title' => 'غرامة عدم تسليم التقرير اليومي للمبيعات',
    'penalty_description' => 'خصم تلقائي لعدم تسليم التقرير اليومي الإلزامي قبل نهاية اليوم.',
    'penalty_type' => 'penalty',
    'penalty_status' => 'applied',
    /** نسبة أيام التقارير المسلّمة المطلوبة شهرياً (عمود الالتزام في KPI) */
    'kpi_submission_target_pct' => 95,
    /** تذكير قبل موعد التسليم (دقائق) */
    'reminder_minutes_before' => 15,
];
