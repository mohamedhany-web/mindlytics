<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Human-like pacing (whatsapp-web.js Bridge)
    |--------------------------------------------------------------------------
    | يقلّل سرعة الإرسال ويحاكي سلوكاً بشرياً — لا يضمن عدم الحظر.
    */
    'pacing' => [
        'enabled' => env('WHATSAPP_PACING_ENABLED', true),

        /** تأخير عشوائي قبل كل رسالة (ثوانٍ) */
        'min_delay_seconds' => (int) env('WHATSAPP_MIN_DELAY', 5),
        'max_delay_seconds' => (int) env('WHATSAPP_MAX_DELAY', 14),

        /** حدود يومية/ساعية — Laravel Cache */
        'max_per_hour' => (int) env('WHATSAPP_MAX_PER_HOUR', 70),
        'max_per_day' => (int) env('WHATSAPP_MAX_PER_DAY', 320),

        /** إرسال داخل ساعات العمل فقط (توقيت التطبيق) */
        'business_hours_only' => env('WHATSAPP_BUSINESS_HOURS_ONLY', true),
        'business_start' => (int) env('WHATSAPP_BUSINESS_START', 9),
        'business_end' => (int) env('WHATSAPP_BUSINESS_END', 21),

        /** استراحة أطول كل N رسائل في نفس الدفعة */
        'pause_every' => (int) env('WHATSAPP_PAUSE_EVERY', 20),
        'pause_min_seconds' => (int) env('WHATSAPP_PAUSE_MIN', 50),
        'pause_max_seconds' => (int) env('WHATSAPP_PAUSE_MAX', 110),

        /** Bridge: محاكاة «يكتب...» قبل الإرسال */
        'simulate_typing' => env('WHATSAPP_SIMULATE_TYPING', true),
    ],

];
