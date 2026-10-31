<?php

return [
    /*
     * نافذة الإسناد: طلب يُعتمد لطالب له Lead تواصل معه موظف مبيعات خلال هذه الأيام
     * يُربط بذلك الـ Lead ويُحسب للموظف (docs/SALES_INTEGRITY.md).
     */
    'attribution_days' => (int) env('SALES_ATTRIBUTION_DAYS', 90),
];
