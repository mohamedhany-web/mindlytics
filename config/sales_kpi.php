<?php

/**
 * أهداف افتراضية لموظف المبيعات (تُدمج مع sales_kpi_targets لكل شهر).
 *
 * القمع اليومي للمحاسبة:
 * 100 شخص تم العمل عليهم (إدخال/تحديث/تحريك Pipeline) → محاولات اتصال → رد → مؤهل → عرض → تسجيل مدفوع.
 *
 * الأوزان: نتائج 40٪، نشاط 30٪، جودة 20٪، التزام 10٪.
 */
return [
    'weights' => [
        'results' => 0.40,
        'activity' => 0.30,
        'quality' => 0.20,
        'discipline' => 0.10,
    ],

    'rationale' => [
        'title' => 'أساس المحاسبة اليومية',
        'points' => [
            'المحاسبة اليومية تبدأ بعدد الأشخاص الفريدين الذين أُدخلوا أو حُدّثوا أو تُحرّكوا في الـ Pipeline اليوم (الهدف الافتراضي 100).',
            'ثم قمع CRM الموثّق: محاولات اتصال → رد → مؤهل → عرض سعر → تسجيل مدفوع.',
            'الاجتماعات/الجلسات غير مطلوبة للمحاسبة (الهدف = 0 ولا تدخل في الخصم).',
            'عدم تحقيق الهدف اليومي (أقل من 70٪ على المؤشر) يفعّل خصمًا تلقائيًا بعد نهاية اليوم.',
            'التقرير اليومي يعكس نفس أرقام CRM الموثّقة — لا تعتمد على أرقام يدوية منفصلة عن النظام.',
        ],
    ],

    'defaults' => [
        'leads_daily' => 5,
        'leads_weekly' => 25,
        'deals_weekly' => 2,
        'revenue_monthly' => 25000,
        'calls_daily' => 50,
        /** اجتماعات: غير مطلوبة — 0 = تُستبعد من التقييم والخصم */
        'meetings_daily' => 0,
        'followups_daily' => 10,
        /** أشخاص فريدون تم إدخالهم أو تحديثهم أو تحريكهم في Pipeline اليوم */
        'people_worked_daily' => 100,
        'call_attempts_daily' => 50,
        'calls_answered_daily' => 20,
        'qualified_conversations_daily' => 8,
        'discovery_sessions_daily' => 0,
        'proposals_daily' => 2,
        'paid_enrollments_daily' => 1,
        'response_minutes_max' => 30,
        'closing_ratio_pct_min' => 25,
        'csat_min' => 4.0,
        'loss_ratio_max_pct' => 50,
        'open_opportunities_min' => 12,
        'sales_cycle_max_days' => 30,
        'crm_activities_daily_min' => 20,
        'data_fresh_open_pct_min' => 80,
        'engagement_days_pct_min' => 90,
        'conversion_pct_target' => 10,
    ],

    'required_on_save' => [
        'leads_daily',
        'leads_weekly',
        'deals_weekly',
        'revenue_monthly',
        'calls_daily',
        'followups_daily',
        'people_worked_daily',
        'call_attempts_daily',
        'calls_answered_daily',
        'qualified_conversations_daily',
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

    /**
     * خصم تلقائي عند عدم تحقيق KPI اليومي (CRM SOS).
     * يُطبَّق بعد موعد نهاية اليوم على المؤشرات chargeable فقط.
     */
    'daily_kpi_penalty' => [
        'enabled' => true,
        /** أقل من هذه النسبة = خلف الهدف → خصم */
        'threshold_pct' => 70,
        'deadline_time' => '23:59',
        'work_days_only' => true,
        /** يمنع الخصم بأثر رجعي قبل هذا التاريخ */
        'penalty_effective_from' => '2026-08-09',
        'penalty_type' => 'penalty',
        /**
         * مؤشرات تُحاسب يوميًا (بدون اجتماعات).
         * amount بالجنيه لكل مؤشر لم يُحقَّق.
         */
        'metrics' => [
            'people_worked_daily' => [
                'amount' => 40,
                'title' => 'غرامة KPI يومي — أشخاص تم العمل عليهم',
            ],
            'call_attempts_daily' => [
                'amount' => 30,
                'title' => 'غرامة KPI يومي — محاولات اتصال',
            ],
            'calls_answered_daily' => [
                'amount' => 25,
                'title' => 'غرامة KPI يومي — مكالمات تم الرد',
            ],
            'qualified_conversations_daily' => [
                'amount' => 25,
                'title' => 'غرامة KPI يومي — محادثات مؤهلة',
            ],
            'proposals_daily' => [
                'amount' => 20,
                'title' => 'غرامة KPI يومي — عروض سعر',
            ],
            'paid_enrollments_daily' => [
                'amount' => 40,
                'title' => 'غرامة KPI يومي — تسجيلات مدفوعة',
            ],
        ],
    ],

    'alerts' => [
        'composite_critical' => 45,
        'composite_warning' => 65,
        'stale_leads_per_rep' => 5,
        'overdue_followups' => 3,
    ],
];
