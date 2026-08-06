<?php

/**
 * تدقيق استخدام CRM للسيلز — أوزان الدرجة المركّبة (موثّق فقط).
 */
return [
    'weights' => [
        'crm_usage' => 0.30,
        'recording_quality' => 0.25,
        'social_link' => 0.15,
        'report_accuracy' => 0.15,
        'finance_verification' => 0.15,
    ],

    'targets' => [
        'min_crm_activities_per_workday' => 8,
        'calls_with_outcome_pct' => 90,
        'qualification_fill_pct' => 90,
        'social_link_pct' => 85,
        'report_accuracy_pct' => 85,
        'finance_verified_pct' => 80,
        'stage_with_contact_pct' => 80,
    ],

    'alerts' => [
        'critical_below' => 45,
        'warning_below' => 65,
    ],

    'qualification_fields' => [
        'profile_type',
        'age',
        'field_domain',
        'experience_level',
        'course_motivation',
        'start_preference',
        'can_pay',
    ],

    /** مراحل لا تُحسب «قفزة بدون تواصل» عند الانتقال إليها */
    'soft_stage_targets' => [
        'new_lead',
        'first_contact',
        'no_answer',
        'dormant',
        'lost',
    ],
];
