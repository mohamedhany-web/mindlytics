@extends('layouts.admin')

@section('title', 'توثيق الواتساب - Mindlytics')
@section('header', 'قسم الواتساب')

@section('content')
@php
    $pacingEnabled = (bool) ($pacing['enabled'] ?? true);
    $maxHour = (int) ($pacing['max_per_hour'] ?? 70);
    $maxDay = (int) ($pacing['max_per_day'] ?? 320);
    $minDelay = (int) ($pacing['min_delay_seconds'] ?? 5);
    $maxDelay = (int) ($pacing['max_delay_seconds'] ?? 14);
    $pauseEvery = (int) ($pacing['pause_every'] ?? 20);
    $businessOnly = (bool) ($pacing['business_hours_only'] ?? false);
@endphp
<div class="p-3 sm:p-4 md:p-6 space-y-4 sm:space-y-6" style="background: #f8fafc; min-height: 100vh;">
    @include('admin.whatsapp._alerts')
    @include('admin.whatsapp._nav', ['active' => 'docs'])

    @include('admin.whatsapp._page-header', [
        'title' => 'توثيق قسم الواتساب',
        'subtitle' => 'دليل الربط (QR ورمز الربط)، الإرسال الآمن، والحدود — كل شيء في مكان واحد.',
        'icon' => 'fas fa-book',
        'actions' => '
            <a href="' . route('admin.whatsapp.index') . '" class="' . $waBtnSecondary . '"><i class="fas fa-arrow-right"></i> لوحة الواتساب</a>
        ',
    ])

    <section class="{{ $waSectionClass }}" id="overview">
        <div class="px-5 py-4 border-b border-slate-200">
            <h3 class="text-lg font-bold text-slate-900">نظرة عامة</h3>
        </div>
        <div class="p-5 prose prose-slate max-w-none text-sm leading-relaxed">
            <p>قسم الواتساب يربط منصة Mindlytics بـ <strong>whatsapp-web.js Bridge</strong> على VPS. Laravel (Hostinger) يرسل طلبات HTTP للجسر، والجسر يتعامل مع واتساب عبر Puppeteer.</p>
            <ul class="list-disc mr-5 space-y-1">
                <li><strong>لوحة الواتساب</strong> — الربط والحالة</li>
                <li><strong>إرسال رسالة</strong> — رسالة فردية فورية</li>
                <li><strong>دفعات الإرسال</strong> — إرسال جماعي في الخلفية مع تأخير آمن</li>
                <li><strong>الرسائل العامة</strong> — Meta WhatsApp Business API (منفصل عن Bridge)</li>
            </ul>
        </div>
    </section>

    <section class="{{ $waSectionClass }}" id="connection">
        <div class="px-5 py-4 border-b border-slate-200">
            <h3 class="text-lg font-bold text-slate-900">طرق ربط الحساب</h3>
        </div>
        <div class="p-5 grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="rounded-xl border border-emerald-200 bg-emerald-50/50 p-4">
                <h4 class="font-bold text-emerald-900 flex items-center gap-2"><i class="fas fa-qrcode"></i> مسح QR</h4>
                <ol class="list-decimal list-inside mt-3 text-sm text-slate-700 space-y-1.5">
                    <li>افتح <a href="{{ route('admin.whatsapp.index') }}" class="text-emerald-700 underline">لوحة الواتساب</a></li>
                    <li>اختر تبويب «مسح QR»</li>
                    <li>من الهاتف: واتساب → الأجهزة المرتبطة → ربط جهاز → امسح الرمز</li>
                </ol>
                <p class="text-xs text-slate-500 mt-3">مناسب عندما يكون الكاميرا متاحة على نفس الجهاز.</p>
            </div>
            <div class="rounded-xl border border-violet-200 bg-violet-50/50 p-4">
                <h4 class="font-bold text-violet-900 flex items-center gap-2"><i class="fas fa-key"></i> رمز الربط (بدون QR)</h4>
                <ol class="list-decimal list-inside mt-3 text-sm text-slate-700 space-y-1.5">
                    <li>اختر تبويب «رمز الربط»</li>
                    <li>أدخل رقم الواتساب مع كود الدولة (مثال: <code class="bg-white px-1 rounded">201012345678</code>)</li>
                    <li>اضغط «طلب رمز الربط» وانتظر ظهور الرمز</li>
                    <li>من الهاتف: واتساب → الأجهزة المرتبطة → ربط جهاز → <strong>ربط برقم الهاتف</strong> → أدخل الرمز</li>
                </ol>
                <p class="text-xs text-slate-500 mt-3">الرمز ينتهي خلال ~3 دقائق. يتجدد تلقائياً من Bridge.</p>
            </div>
        </div>
        <div class="mx-5 mb-5 rounded-xl bg-amber-50 border border-amber-200 px-4 py-3 text-sm text-amber-900">
            <p class="font-semibold">مهم — تحديث Bridge على VPS</p>
            <p class="mt-1">رمز الربط يحتاج نسخة Bridge محدّثة من مجلد <code class="bg-white px-1 rounded text-xs">whatsapp-bridge/</code> في المشروع. بعد الرفع:</p>
            <pre class="mt-2 text-xs bg-white rounded-lg p-3 overflow-x-auto dir-ltr text-left">cd /path/to/whatsapp-bridge
git pull   # أو انسخ server.js المحدّث
npm install
pm2 restart mindlytics-whatsapp</pre>
        </div>
    </section>

    <section class="{{ $waSectionClass }}" id="env">
        <div class="px-5 py-4 border-b border-slate-200">
            <h3 class="text-lg font-bold text-slate-900">متغيرات البيئة (.env)</h3>
        </div>
        <div class="p-5 overflow-x-auto">
            <table class="w-full text-sm border-collapse">
                <thead>
                    <tr class="bg-slate-50 text-right">
                        <th class="border border-slate-200 px-3 py-2 font-bold">المتغير</th>
                        <th class="border border-slate-200 px-3 py-2 font-bold">الوصف</th>
                        <th class="border border-slate-200 px-3 py-2 font-bold">مثال</th>
                    </tr>
                </thead>
                <tbody class="font-mono text-xs dir-ltr text-left">
                    <tr><td class="border px-3 py-2">WHATSAPP_ENABLED</td><td class="border px-3 py-2 font-sans text-right">تفعيل الإرسال</td><td class="border px-3 py-2">true</td></tr>
                    <tr><td class="border px-3 py-2">WHATSAPP_TYPE</td><td class="border px-3 py-2 font-sans text-right">نوع الخدمة</td><td class="border px-3 py-2">wwebjs</td></tr>
                    <tr><td class="border px-3 py-2">WHATSAPP_LOCAL_API_URL</td><td class="border px-3 py-2 font-sans text-right">رابط Bridge</td><td class="border px-3 py-2">https://wa-api.example.com</td></tr>
                    <tr><td class="border px-3 py-2">WHATSAPP_BRIDGE_TOKEN</td><td class="border px-3 py-2 font-sans text-right">نفس API_TOKEN على VPS</td><td class="border px-3 py-2">secret...</td></tr>
                    <tr><td class="border px-3 py-2">WHATSAPP_PACING_ENABLED</td><td class="border px-3 py-2 font-sans text-right">الإرسال الآمن</td><td class="border px-3 py-2">true</td></tr>
                    <tr><td class="border px-3 py-2">WHATSAPP_MAX_PER_HOUR</td><td class="border px-3 py-2 font-sans text-right">حد الساعة</td><td class="border px-3 py-2">{{ $maxHour }}</td></tr>
                    <tr><td class="border px-3 py-2">WHATSAPP_MAX_PER_DAY</td><td class="border px-3 py-2 font-sans text-right">حد اليوم</td><td class="border px-3 py-2">{{ $maxDay }}</td></tr>
                </tbody>
            </table>
            <p class="text-xs text-slate-500 mt-3">بعد تعديل .env نفّذ: <code class="bg-slate-100 px-1 rounded">php artisan config:clear</code></p>
        </div>
    </section>

    <section class="{{ $waSectionClass }}" id="anti-ban">
        <div class="px-5 py-4 border-b border-slate-200">
            <h3 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                <i class="fas fa-shield-alt text-sky-600"></i>
                تجنب الحظر — الإرسال الآمن
            </h3>
        </div>
        <div class="p-5 space-y-4 text-sm text-slate-700">
            <p class="rounded-xl bg-rose-50 border border-rose-200 px-4 py-3 text-rose-900">
                <strong>تنبيه:</strong> واتساب Web غير رسمي. لا يوجد ضمان 100% ضد الحظر. الإعدادات التالية تقلّل المخاطر فقط.
            </p>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <div class="rounded-xl border border-slate-200 p-4 bg-white">
                    <p class="font-bold text-slate-900">الحالة الحالية</p>
                    <ul class="mt-2 space-y-1 text-sm">
                        <li>الإرسال الآمن: <strong>{{ $pacingEnabled ? 'مفعّل' : 'معطّل' }}</strong></li>
                        <li>حد الساعة: <strong>{{ $maxHour }}</strong> رسالة</li>
                        <li>حد اليوم: <strong>{{ $maxDay }}</strong> رسالة</li>
                        <li>تأخير بين الرسائل: <strong>{{ $minDelay }}–{{ $maxDelay }}</strong> ثانية</li>
                        <li>استراحة كل <strong>{{ $pauseEvery }}</strong> رسالة</li>
                        <li>ساعات العمل فقط: <strong>{{ $businessOnly ? 'نعم' : 'لا' }}</strong></li>
                    </ul>
                </div>
                <div class="rounded-xl border border-slate-200 p-4 bg-white">
                    <p class="font-bold text-slate-900">أفضل الممارسات</p>
                    <ul class="mt-2 list-disc mr-5 space-y-1 text-sm">
                        <li>لا ترسل آلاف الرسائل دفعة واحدة — استخدم <strong>دفعات الإرسال</strong></li>
                        <li>خصّص الرسائل باسم المستلم — تجنب النص المكرر بالضبط</li>
                        <li>أرسل فقط لمن وافق على التواصل (Leads/طلاب مسجّلين)</li>
                        <li>للحملات الكبيرة والرسمية استخدم <strong>Meta WhatsApp Business API</strong></li>
                        <li>راقب «رسائل فاشلة» في السجل — توقف إذا زادت الأخطاء</li>
                    </ul>
                </div>
            </div>
            <p class="text-xs text-slate-500">Bridge يحاكي «يكتب...» قبل الإرسال عندما <code>WHATSAPP_SIMULATE_TYPING=true</code>.</p>
        </div>
    </section>

    <section class="{{ $waSectionClass }}" id="api">
        <div class="px-5 py-4 border-b border-slate-200">
            <h3 class="text-lg font-bold text-slate-900">واجهة Bridge API</h3>
        </div>
        <div class="p-5 overflow-x-auto text-xs font-mono dir-ltr text-left">
            <table class="w-full border-collapse">
                <thead><tr class="bg-slate-50"><th class="border px-2 py-1">Method</th><th class="border px-2 py-1">Path</th><th class="border px-2 py-1 font-sans text-right">الوصف</th></tr></thead>
                <tbody>
                    <tr><td class="border px-2 py-1">GET</td><td class="border px-2 py-1">/api/status</td><td class="border px-2 py-1 font-sans text-right">حالة الاتصال</td></tr>
                    <tr><td class="border px-2 py-1">GET</td><td class="border px-2 py-1">/api/qr</td><td class="border px-2 py-1 font-sans text-right">صورة QR</td></tr>
                    <tr><td class="border px-2 py-1">GET</td><td class="border px-2 py-1">/api/pairing-code</td><td class="border px-2 py-1 font-sans text-right">جلب رمز الربط الحالي</td></tr>
                    <tr><td class="border px-2 py-1">POST</td><td class="border px-2 py-1">/api/pairing-code</td><td class="border px-2 py-1 font-sans text-right">{ "phone": "2010..." }</td></tr>
                    <tr><td class="border px-2 py-1">POST</td><td class="border px-2 py-1">/api/qr-mode</td><td class="border px-2 py-1 font-sans text-right">العودة لوضع QR</td></tr>
                    <tr><td class="border px-2 py-1">POST</td><td class="border px-2 py-1">/api/send</td><td class="border px-2 py-1 font-sans text-right">إرسال رسالة</td></tr>
                    <tr><td class="border px-2 py-1">POST</td><td class="border px-2 py-1">/api/repair</td><td class="border px-2 py-1 font-sans text-right">إصلاح بدون logout</td></tr>
                    <tr><td class="border px-2 py-1">POST</td><td class="border px-2 py-1">/api/logout</td><td class="border px-2 py-1 font-sans text-right">قطع الاتصال</td></tr>
                </tbody>
            </table>
            <p class="font-sans text-right text-sm text-slate-600 mt-3">Header: <code>Authorization: Bearer API_TOKEN</code></p>
        </div>
    </section>

    <section class="{{ $waSectionClass }}" id="queue">
        <div class="px-5 py-4 border-b border-slate-200">
            <h3 class="text-lg font-bold text-slate-900">دفعات الإرسال والطابور (Queue)</h3>
        </div>
        <div class="p-5 text-sm text-slate-700 space-y-3">
            <p>الإرسال الجماعي يمر عبر <strong>دفعات</strong> — كل رسالة تُرسل في Job منفصل في طابور Laravel (<code>jobs</code>) لتجنب timeout على الاستضافة.</p>
            <ul class="list-disc mr-5 space-y-1">
                <li>بعد بدء الدفعة تُجدول أول Job تلقائياً</li>
                <li>كل Job يرسل رسالة واحدة (افتراضياً) ثم يُجدول التالي</li>
                <li>صفحة المتابعة تُحدّث الحالة كل 3 ثوانٍ وتُعيد التشغيل إن علقت الدفعة</li>
            </ul>
            <p class="font-semibold text-slate-900">على Hostinger — تأكد من cron:</p>
            <pre class="text-xs bg-slate-900 text-slate-100 rounded-lg p-3 overflow-x-auto dir-ltr text-left">* * * * * cd /path/to/project && php artisan schedule:run >> /dev/null 2>&1</pre>
            <p class="text-xs text-slate-500">الـ schedule يشغّل <code>queue:work --stop-when-empty</code> كل دقيقة لمعالجة الدفعات.</p>
            <p>متغيرات اختيارية: <code>WHATSAPP_BATCH_CHUNK_SIZE=1</code> · <code>QUEUE_CONNECTION=database</code></p>
        </div>
    </section>

    <section class="{{ $waSectionClass }}" id="troubleshooting">
        <div class="px-5 py-4 border-b border-slate-200">
            <h3 class="text-lg font-bold text-slate-900">استكشاف الأخطاء</h3>
        </div>
        <div class="p-5 text-sm text-slate-700 space-y-3">
            <div><strong>تعذّر الاتصال بالجسر</strong> — تحقق من الرابط والتوكن في <a href="{{ route('admin.whatsapp.settings') }}" class="underline">إعدادات الربط</a> ومن أن VPS يعمل.</div>
            <div><strong>رمز الربط لا يظهر</strong> — حدّث <code>server.js</code> على VPS وأعد تشغيل PM2.</div>
            <div><strong>LOGOUT / browser is already running</strong> — اضغط «إصلاح الاتصال» أو على VPS: <code class="text-xs bg-slate-100 px-1 rounded">pm2 restart mindlytics-whatsapp</code></div>
            <div><strong>تم الوصول للحد الأقصى</strong> — انتظر الساعة/اليوم التالي أو خفّض حجم الدفعة.</div>
        </div>
    </section>
</div>
@endsection
