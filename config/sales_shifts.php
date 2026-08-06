<?php

/**
 * نظام شيفتات وقنوات فريق المبيعات.
 */
return [
    /** بداية/نهاية يوم العمل (ساعة 26 = 2 ص اليوم التالي) */
    'work_day_start_hour' => 10,
    'work_day_end_hour' => 26,

    /** دقائق قبل السماح لغير مالك الشيفت بالرد */
    'takeover_grace_minutes' => 10,

    'channels' => [
        'messenger' => ['label' => 'ماسنجر', 'color' => '#F5A623', 'inbox' => 'meta_social'],
        'instagram' => ['label' => 'انستا', 'color' => '#EC6A9C', 'inbox' => 'meta_social'],
        'calls' => ['label' => 'مكالمات', 'color' => '#6366F1', 'inbox' => 'crm'],
        'whatsapp' => ['label' => 'واتساب', 'color' => '#25D366', 'inbox' => 'whatsapp'],
        'followup' => ['label' => 'فولو أب', 'color' => '#4DD0E1', 'inbox' => 'crm'],
        'comments' => ['label' => 'كومنتات', 'color' => '#8BD450', 'inbox' => 'meta_social'],
        'all' => ['label' => 'كل القنوات', 'color' => '#94A3B8', 'inbox' => 'any'],
    ],

    'segment_modes' => [
        'normal' => 'من المقر / عادي',
        'home' => 'من البيت',
    ],

    'swap_statuses' => [
        'pending' => 'بانتظار الموافقة',
        'approved' => 'معتمد',
        'rejected' => 'مرفوض',
        'cancelled' => 'ملغى',
    ],

    /** أسماء أيام الأسبوع (السبت = 0) */
    'day_names' => [
        0 => 'السبت',
        1 => 'الأحد',
        2 => 'الاثنين',
        3 => 'الثلاثاء',
        4 => 'الأربعاء',
        5 => 'الخميس',
        6 => 'الجمعة',
    ],

    'rules' => [
        'no_cross_shift_reply' => 'ممنوع الرد في شيفت غيرك إلا بعد انتهاء مهلة عدم الرد.',
        'shift_responsibility' => 'أي خطأ في شيفتك أنت المسؤول عنه.',
        'swap_must_be_written' => 'تبديل الشيفت يُكتب ويُعتمد من المدير.',
        'assign_leads' => 'كل موظف يعمل assign/label للعميل الذي يتابعه.',
    ],
];
