<?php

/**
 * مركز رقابة مدير المبيعات — أوزان الدرجة اليومية الموثّقة (CRM فقط).
 */
return [
    'weights' => [
        'results' => 0.35,
        'activity' => 0.25,
        'quality' => 0.15,
        'crm_discipline' => 0.15,
        'attendance' => 0.10,
    ],

    'daily_targets' => [
        /** متوافق مع sales_kpi — بدون اجتماعات للمحاسبة */
        'call_attempts' => 50,
        'calls_answered' => 20,
        'qualified' => 8,
        'meetings' => 0,
        'followups' => 10,
        'whatsapp_linked' => 15,
        'cold_worked_pct' => 70,
        'paid_enrollments' => 1,
        'crm_activities' => 20,
        'overdue_followups_max' => 2,
        'calls_with_outcome_pct' => 90,
    ],

    'alerts' => [
        'critical_below' => 45,
        'warning_below' => 65,
    ],

    'recommendations' => [
        'bonus' => 'مكافأة',
        'praise' => 'تنويه إيجابي',
        'coaching' => 'جلسة متابعة',
        'warning' => 'تنبيه',
        'deduction' => 'خصم مقترح (يتطلب اعتماد)',
        'none' => 'لا إجراء',
    ],
];
