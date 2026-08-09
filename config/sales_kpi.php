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
        /** أهداف واقعية قابلة للتنفيذ لموظف مبيعات واحد */
        'leads_daily' => 8,
        'leads_weekly' => 40,
        'deals_weekly' => 2,
        'revenue_monthly' => 30000,
        'calls_daily' => 40,
        'meetings_daily' => 3,
        'followups_daily' => 12,
        /** SOS — أهداف يومية قائمة على النتائج */
        'call_attempts_daily' => 60,
        'calls_answered_daily' => 20,
        'qualified_conversations_daily' => 8,
        'discovery_sessions_daily' => 4,
        'proposals_daily' => 3,
        'paid_enrollments_daily' => 1,
        /** أقصى متوسط وقت أول رد مقبول (دقائق) — أقل أفضل */
        'response_minutes_max' => 30,
        /** أدنى نسبة إغلاق won/(won+lost) */
        'closing_ratio_pct_min' => 20,
        /** أدنى متوسط تقييم CSAT (1–5) */
        'csat_min' => 4.0,
        /** أقصى نسبة «خسارة» lost/(won+lost) تُعتبر مقبولة */
        'loss_ratio_max_pct' => 45,
        /** أدنى عدد فرص مفتوحة في الأنبوب */
        'open_opportunities_min' => 8,
        /** أقصى متوسط أيام دورة البيع (won) */
        'sales_cycle_max_days' => 45,
        /** أدنى متوسط أنشطة CRM يومية */
        'crm_activities_daily_min' => 10,
        /** أدنى نسبة عملاء مفتوحين حُدِّثوا خلال 7 أيام */
        'data_fresh_open_pct_min' => 80,
        /** أدنى نسبة أيام عمل بها نشاط مسجّل */
        'engagement_days_pct_min' => 85,
        /** هدف نسبة تحويل leads→won (شهري تقريبي) */
        'conversion_pct_target' => 12,
    ],

    /**
     * مفاتيح يجب تعبئتها عند حفظ أهداف موظف لشهر معيّن (أهداف ملزمة).
     *
     * @var list<string>
     */
    'required_on_save' => [
        'leads_daily',
        'leads_weekly',
        'deals_weekly',
        'revenue_monthly',
        'calls_daily',
        'meetings_daily',
        'followups_daily',
        'call_attempts_daily',
        'calls_answered_daily',
        'qualified_conversations_daily',
        'discovery_sessions_daily',
        'proposals_daily',
        'paid_enrollments_daily',
        'response_minutes_max',
        'closing_ratio_pct_min',
        'csat_min',
        'loss_ratio_max_pct',
        'open_opportunities_min',
        'sales_cycle_max_days',
        'crm_activities_daily_min',
        'data_fresh_open_pct_min',
        'engagement_days_pct_min',
        'conversion_pct_target',
    ],

    /** عتبات تنبيه للإدارة (مراقبة حادة) */
    'alerts' => [
        'composite_critical' => 45,
        'composite_warning' => 65,
        'stale_leads_per_rep' => 5,
        'overdue_followups' => 3,
    ],
];
