<?php

/**
 * أهداف افتراضية لموظف المبيعات (تُدمج مع sales_kpi_targets لكل شهر).
 *
 * المنطق الواقعي (يوم عمل كامل تقريبًا 8–10 ساعات):
 * 50 محاولة اتصال → ~20 رد (~40٪) → ~8 مؤهل → ~3 اجتماعات → ~2 عرض → ~1 تسجيل مدفوع (هدف طموح يومي).
 * الأرقام متوافقة مع config/sales_manager_scorecard.php حتى تُحاسب بنفس المعيار.
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

    /**
     * شرح قصير يظهر في لوحة ضبط الأهداف — لماذا هذه الأرقام.
     */
    'rationale' => [
        'title' => 'أساس المحاسبة (قمع يومي واقعي)',
        'points' => [
            'محاولات الاتصال = كل نشاط call موثّق على عميل في الـ CRM (نفس اللي يشوفه مدير المبيعات في السكور كارد).',
            '50 محاولة/يوم ≈ 5–6 مكالمات في الساعة خلال شيفت فعّال — رقم يُحاسب عليه بدون مبالغة.',
            '20 رد ≈ نسبة رد ~40٪ من المحاولات (واقعية لكول سنتر/مبيعات كورسات).',
            '8 محادثات مؤهلة → 3 اجتماعات/جلسات → 2 عروض سعر → هدف تسجيل مدفوع يوميًا (طموح لكن قابل للقياس).',
            'أسبوعيًا: ~2–3 صفقات فوز؛ شهريًا: إيراد متوقع من الصفقات المغلقة (قيمة expected_value) حوالي 25 ألف ج.م كحد أدنى للمحاسبة.',
            'leads اليومية = عملاء جدد اتسجّلوا ومسنودين للموظف في نفس اليوم (مش «شغل قديم» فقط).',
        ],
    ],

    'defaults' => [
        /** Leads جديدة مسنودة/مُنشأة في اليوم — متوافق مع توزيع التسويق */
        'leads_daily' => 5,
        'leads_weekly' => 25,
        /** صفقات فوز مؤكدة أسبوعيًا (≈ يوم عمل × معدل إغلاق واقعي) */
        'deals_weekly' => 2,
        /** إيراد شهري من expected_value للصفقات الفائزة — أرضية محاسبة */
        'revenue_monthly' => 25000,
        /**
         * مكالمات CRM اليومية = نفس call_attempts (نشاط type=call مربوط بعميل).
         * لازم يطابق call_attempts_daily عشان متتحاسبش بمعيارين.
         */
        'calls_daily' => 50,
        'meetings_daily' => 3,
        'followups_daily' => 10,
        /** SOS — نفس أرقام سكور كارد المدير */
        'call_attempts_daily' => 50,
        'calls_answered_daily' => 20,
        'qualified_conversations_daily' => 8,
        'discovery_sessions_daily' => 3,
        'proposals_daily' => 2,
        'paid_enrollments_daily' => 1,
        /** أقصى متوسط وقت أول رد مقبول (دقائق) — أقل أفضل */
        'response_minutes_max' => 30,
        /** أدنى نسبة إغلاق won/(won+lost) على الصفقات المقفولة */
        'closing_ratio_pct_min' => 25,
        /** أدنى متوسط تقييم CSAT (1–5) */
        'csat_min' => 4.0,
        /** أقصى نسبة خسارة مقبولة على الصفقات المقفولة */
        'loss_ratio_max_pct' => 50,
        /** أنبوب نشط يكفي Continuity — مش تضخم وهمي */
        'open_opportunities_min' => 12,
        /** دورة بيع كورسات: من أول تواصل لفوز */
        'sales_cycle_max_days' => 30,
        /** متوسط أنشطة CRM يومية (مكالمة+متابعة+اجتماع+…) */
        'crm_activities_daily_min' => 20,
        /** حداثة بيانات الفرص المفتوحة */
        'data_fresh_open_pct_min' => 80,
        /** نسبة أيام الشيفت اللي فيها نشاط موثّق */
        'engagement_days_pct_min' => 90,
        /**
         * تحويل leads→won في نفس الشهر مؤشر تقريبي فقط (الدورة قد تطول).
         * 8–10٪ واقعي أكتر من نسب عالية.
         */
        'conversion_pct_target' => 10,
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
