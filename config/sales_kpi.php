<?php

/**
 * أهداف افتراضية لموظف المبيعات (تُدمج مع sales_kpi_targets لكل شهر).
 * الأوزان: نتائج 40٪، نشاط 30٪، جودة 20٪، التزام 10٪.
 */
return [
    'weights' => [
        'results' => 0.40,
        'activity' => 0.30,
        'quality' => 0.20,
        'discipline' => 0.10,
    ],

    'defaults' => [
        'leads_daily' => 20,
        'leads_weekly' => 100,
        'deals_weekly' => 5,
        'revenue_monthly' => 50000,
        'calls_daily' => 50,
        'meetings_daily' => 5,
        'followups_daily' => 15,
        /** SOS — أهداف يومية قائمة على النتائج */
        'call_attempts_daily' => 120,
        'calls_answered_daily' => 35,
        'qualified_conversations_daily' => 15,
        'discovery_sessions_daily' => 8,
        'proposals_daily' => 5,
        'paid_enrollments_daily' => 2,
        /** أقصى متوسط وقت أول رد مقبول (دقائق) — أقل أفضل */
        'response_minutes_max' => 30,
        /** أدنى نسبة إغلاق won/(won+lost) */
        'closing_ratio_pct_min' => 25,
        /** أدنى متوسط تقييم CSAT (1–5) */
        'csat_min' => 4.0,
        /** أقصى نسبة «خسارة» lost/(won+lost) تُعتبر مقبولة */
        'loss_ratio_max_pct' => 40,
        /** أدنى عدد فرص مفتوحة في الأنبوب */
        'open_opportunities_min' => 5,
        /** أقصى متوسط أيام دورة البيع (won) */
        'sales_cycle_max_days' => 45,
        /** أدنى متوسط أنشطة CRM يومية */
        'crm_activities_daily_min' => 8,
        /** أدنى نسبة عملاء مفتوحين حُدِّثوا خلال 7 أيام */
        'data_fresh_open_pct_min' => 80,
        /** أدنى نسبة أيام عمل بها نشاط مسجّل */
        'engagement_days_pct_min' => 85,
        /** هدف نسبة تحويل leads→won (شهري تقريبي) */
        'conversion_pct_target' => 15,
    ],

    /** عتبات تنبيه للإدارة (مراقبة حادة) */
    'alerts' => [
        'composite_critical' => 45,
        'composite_warning' => 65,
        'stale_leads_per_rep' => 5,
        'overdue_followups' => 3,
    ],
];
