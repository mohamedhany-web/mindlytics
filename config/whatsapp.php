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

        /** إرسال داخل ساعات العمل فقط (توقيت التطبيق) — false = إرسال على مدار اليوم */
        'business_hours_only' => env('WHATSAPP_BUSINESS_HOURS_ONLY', false),
        'business_start' => (int) env('WHATSAPP_BUSINESS_START', 9),
        'business_end' => (int) env('WHATSAPP_BUSINESS_END', 21),

        /** استراحة أطول كل N رسائل في نفس الدفعة */
        'pause_every' => (int) env('WHATSAPP_PAUSE_EVERY', 20),
        'pause_min_seconds' => (int) env('WHATSAPP_PAUSE_MIN', 50),
        'pause_max_seconds' => (int) env('WHATSAPP_PAUSE_MAX', 110),

        /** Bridge: محاكاة «يكتب...» قبل الإرسال */
        'simulate_typing' => env('WHATSAPP_SIMULATE_TYPING', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | إرسال الدفعات بدون Queue Worker (مهم على Hostinger)
    |--------------------------------------------------------------------------
    | true = يبدأ الإرسال فوراً بعد تحميل صفحة المتابعة (لا ينتظر cron)
    */
    'dispatch_after_response' => env('WHATSAPP_DISPATCH_AFTER_RESPONSE', true),

    /** مهلة HTTP للـ Bridge (ثوانٍ) — الدفعات الكبيرة تحتاج وقتاً أطول */
    'bridge_timeout' => (int) env('WHATSAPP_BRIDGE_TIMEOUT', 180),

    /**
     * عدد الرسائل في كل تشغيل للـ Job — يمنع timeout على الاستضافة المشتركة.
     * بعد كل دفعة يُعاد جدولة Job تلقائياً إن بقي مستلمون.
     */
    'batch_chunk_size' => max(1, (int) env('WHATSAPP_BATCH_CHUNK_SIZE', 1)),

    /** دقائق قبل اعتبار عنصر «processing» عالقاً وإعادته لـ pending */
    'batch_stale_minutes' => max(1, (int) env('WHATSAPP_BATCH_STALE_MINUTES', 10)),

    /** طابور Laravel المنفصل — لا يختلط مع تسجيل الطلاب أو أي jobs أخرى */
    'queue' => env('WHATSAPP_QUEUE', 'whatsapp'),

];
