@extends('layouts.employee')

@section('title', 'دليل موظف المبيعات')
@section('header', 'Documentation — دليل عمل المبيعات')

@section('content')
@php
    $isSales = auth()->user()?->isSalesEmployee() || auth()->user()?->isSalesManager();
    $stages = \App\Models\SalesLead::STAGES;
@endphp
<div class="p-4 sm:p-6 lg:p-8 space-y-6">
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 sm:p-6">
        <h1 class="text-xl sm:text-2xl font-black text-slate-900 flex items-center gap-2">
            <i class="fas fa-book-open text-blue-600"></i>
            <span>دليل النظام لموظف المبيعات</span>
        </h1>
        <p class="text-sm text-slate-600 mt-2 leading-7">
            هذا الدليل يشرح <strong>ماذا تفعل في الوردية</strong>، <strong>كيف تستخدم النظام</strong>،
            و<strong>كل حالات الـ Pipeline</strong> من أول Lead حتى التسجيل واعتماد الإدارة.
        </p>
        @if(!$isSales)
            <p class="mt-3 text-xs text-amber-800 bg-amber-50 border border-amber-200 rounded-xl px-3 py-2">
                المحتوى التالي موجّه لفريق المبيعات. إن لم تكن موظفاً في المبيعات فقد لا تظهر لك كل الشاشات المذكورة.
            </p>
        @endif
    </div>

    {{-- TOC --}}
    <nav class="bg-slate-50 rounded-2xl border border-slate-200 p-4 sm:p-5">
        <p class="text-xs font-bold text-slate-500 mb-2">محتويات الدليل</p>
        <div class="flex flex-wrap gap-2 text-sm">
            <a href="#shift" class="px-3 py-1.5 rounded-lg bg-white border border-slate-200 hover:border-teal-300 text-slate-700">الوردية اليومية</a>
            <a href="#how" class="px-3 py-1.5 rounded-lg bg-white border border-slate-200 hover:border-teal-300 text-slate-700">كيف تستخدم النظام</a>
            <a href="#pipeline" class="px-3 py-1.5 rounded-lg bg-white border border-slate-200 hover:border-teal-300 text-slate-700">مراحل الـ Pipeline</a>
            <a href="#calls" class="px-3 py-1.5 rounded-lg bg-white border border-slate-200 hover:border-teal-300 text-slate-700">نتائج المكالمات</a>
            <a href="#win" class="px-3 py-1.5 rounded-lg bg-white border border-slate-200 hover:border-teal-300 text-slate-700">التسجيل والعمولة</a>
            <a href="#kpi" class="px-3 py-1.5 rounded-lg bg-white border border-slate-200 hover:border-teal-300 text-slate-700">KPI والتقرير</a>
            <a href="#rules" class="px-3 py-1.5 rounded-lg bg-white border border-slate-200 hover:border-teal-300 text-slate-700">قواعد الالتزام</a>
        </div>
    </nav>

    {{-- SHIFT --}}
    <section id="shift" class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 sm:p-6 space-y-4 scroll-mt-4">
        <h2 class="text-lg font-extrabold text-slate-900 flex items-center gap-2">
            <span class="w-8 h-8 rounded-lg bg-teal-100 text-teal-700 flex items-center justify-center text-sm font-black">1</span>
            ماذا تفعل في الوردية الآن؟ (Sales Operating System)
        </h2>
        <p class="text-sm text-slate-600 leading-7">
            يومك مقسّم إلى <strong>Blocks</strong> إنتاجية تظهر على <a href="{{ route('employee.sales.dashboard') }}" class="text-teal-700 font-semibold underline">مركز المبيعات</a>.
            التزم بالبلوك الحالي وهدفه — المدير يرى على اللوحة الحية من المتأخر عن نبض آخر ساعتين.
        </p>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm text-right border border-slate-200 rounded-xl overflow-hidden">
                <thead class="bg-slate-50 text-xs text-slate-500">
                    <tr>
                        <th class="px-3 py-2">الوقت (تقريبي)</th>
                        <th class="px-3 py-2">البلوك</th>
                        <th class="px-3 py-2">ماذا تفعل؟</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <tr><td class="px-3 py-2.5 tabular-nums">09:00–09:20</td><td class="px-3 py-2.5 font-semibold">Morning Brief</td><td class="px-3 py-2.5 text-slate-600">راجع أرقام أمس، هدف اليوم، والاعتراضات الشائعة.</td></tr>
                    <tr><td class="px-3 py-2.5 tabular-nums">09:20–11:00</td><td class="px-3 py-2.5 font-semibold">Call Block #1</td><td class="px-3 py-2.5 text-slate-600">اتصالات مركّزة بدون مقاطعات — كل مكالمة لها نتيجة إلزامية.</td></tr>
                    <tr><td class="px-3 py-2.5 tabular-nums">11:00–11:15</td><td class="px-3 py-2.5 font-semibold">Break</td><td class="px-3 py-2.5 text-slate-600">استراحة قصيرة.</td></tr>
                    <tr><td class="px-3 py-2.5 tabular-nums">11:15–13:00</td><td class="px-3 py-2.5 font-semibold">Follow-up</td><td class="px-3 py-2.5 text-slate-600">متابعة العملاء القدامى وإغلاق الفرص الجاهزة.</td></tr>
                    <tr><td class="px-3 py-2.5 tabular-nums">13:00–14:00</td><td class="px-3 py-2.5 font-semibold">Lunch</td><td class="px-3 py-2.5 text-slate-600">غداء.</td></tr>
                    <tr><td class="px-3 py-2.5 tabular-nums">14:00–15:30</td><td class="px-3 py-2.5 font-semibold">Call Block #2</td><td class="px-3 py-2.5 text-slate-600">جولة اتصالات جديدة.</td></tr>
                    <tr><td class="px-3 py-2.5 tabular-nums">15:30–15:45</td><td class="px-3 py-2.5 font-semibold">Break</td><td class="px-3 py-2.5 text-slate-600">استراحة قصيرة.</td></tr>
                    <tr><td class="px-3 py-2.5 tabular-nums">15:45–17:00</td><td class="px-3 py-2.5 font-semibold">WhatsApp + Closing</td><td class="px-3 py-2.5 text-slate-600">رسائل، عروض، حجز، وتأكيد الدفع.</td></tr>
                    <tr><td class="px-3 py-2.5 tabular-nums">17:00–17:20</td><td class="px-3 py-2.5 font-semibold">Daily Report</td><td class="px-3 py-2.5 text-slate-600">سلّم التقرير اليومي الإلزامي قبل الموعد النهائي.</td></tr>
                </tbody>
            </table>
        </div>
        <div class="rounded-xl border border-teal-200 bg-teal-50/70 p-4 text-sm text-teal-950 space-y-1">
            <p class="font-bold">أهداف النتائج اليومية الافتراضية (تظهر على الداشبورد مقابل الفعلي):</p>
            <p>120 محاولة اتصال · 35 رد · 15 محادثة مؤهلة · 8 جلسات · 5 عروض · 2 تسجيل مدفوع</p>
        </div>
        <div class="rounded-xl border border-violet-200 bg-violet-50/70 p-4 text-sm text-violet-950 space-y-2">
            <p class="font-bold">الحضور: أونلاين vs أوفلاين</p>
            <ul class="list-disc list-inside space-y-1 text-violet-900/90">
                <li><strong>أونلاين:</strong> سجّل الحضور من أي مكان عند بدء الشيفت.</li>
                <li><strong>أوفلاين:</strong> لازم تكون في المكتب → اضغط «طلب إذن الحضور» → المدير يشوفك ويعمل قبول (في الميعاد / إعفاء تأخير بدون خصم / تأخير بخصم).</li>
                <li>الأوفلاين قد يكون Full-time أو أيام نزول محددة من ملفك في الأدمن.</li>
            </ul>
        </div>
    </section>

    {{-- HOW TO USE --}}
    <section id="how" class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 sm:p-6 space-y-4 scroll-mt-4">
        <h2 class="text-lg font-extrabold text-slate-900 flex items-center gap-2">
            <span class="w-8 h-8 rounded-lg bg-sky-100 text-sky-700 flex items-center justify-center text-sm font-black">2</span>
            كيف تتعامل مع النظام خطوة بخطوة
        </h2>
        <ol class="space-y-4 text-sm text-slate-700 leading-7 list-decimal list-inside">
            <li>
                <strong>افتح مركز المبيعات</strong> من السايدبار.
                شاهد البلوك الحالي، نتائج اليوم مقابل الهدف، وقائمة المهام العاجلة (متابعات متأخرة / SLA / راكد).
            </li>
            <li>
                <strong>اختر عميلاً من قائمة العملاء أو المتابعات.</strong>
                اضغط اتصال سريع أو افتح صفحة العميل.
            </li>
            <li>
                <strong>سجّل المكالمة بنتيجة واضحة</strong> (Interested / Follow Up / No Answer / …).
                بدون نتيجة لن تُحتسب المكالمة بشكل صحيح في KPI.
            </li>
            <li>
                <strong>حدّث مرحلة الـ Pipeline من بطاقة «Lead Status»</strong> أعلى صفحة العميل.
                النظام يطلب الحقول الإلزامية حسب المرحلة (مثل Qualification أو سبب الاعتراض).
            </li>
            <li>
                <strong>حدّد Next Follow-up</strong> دائماً للعملاء المفتوحين (تاريخ + ساعة + قناة إن لزم).
            </li>
            <li>
                <strong>استخدم واتساب</strong> من صندوق الوارد عند الحاجة، وحدّث المرحلة من هناك إن تغيّرت حالة العميل.
            </li>
            <li>
                <strong>في نهاية اليوم:</strong> أكمل التقرير اليومي الإلزامي (أرقام تلقائية من CRM + ملاحظاتك اليدوية).
            </li>
        </ol>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
            <a href="{{ route('employee.sales.dashboard') }}" class="rounded-xl border border-slate-200 p-4 hover:border-teal-300 hover:bg-teal-50/40">
                <p class="font-bold text-slate-900">مركز المبيعات</p>
                <p class="text-slate-500 text-xs mt-1">بلوك اليوم + نتائج SOS + Task Queue</p>
            </a>
            <a href="{{ route('employee.sales.leads.index') }}" class="rounded-xl border border-slate-200 p-4 hover:border-teal-300 hover:bg-teal-50/40">
                <p class="font-bold text-slate-900">العملاء المحتملون</p>
                <p class="text-slate-500 text-xs mt-1">قائمة Leads + فلاتر المراحل + نشاط سريع</p>
            </a>
            <a href="{{ route('employee.sales.follow-ups.index') }}" class="rounded-xl border border-slate-200 p-4 hover:border-teal-300 hover:bg-teal-50/40">
                <p class="font-bold text-slate-900">متابعاتي</p>
                <p class="text-slate-500 text-xs mt-1">متأخرة / اليوم / بلا موعد / راكد</p>
            </a>
            <a href="{{ route('employee.sales.daily-reports.edit') }}" class="rounded-xl border border-slate-200 p-4 hover:border-teal-300 hover:bg-teal-50/40">
                <p class="font-bold text-slate-900">التقرير اليومي</p>
                <p class="text-slate-500 text-xs mt-1">إلزامي قبل نهاية اليوم</p>
            </a>
        </div>
    </section>

    {{-- PIPELINE --}}
    <section id="pipeline" class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 sm:p-6 space-y-4 scroll-mt-4">
        <h2 class="text-lg font-extrabold text-slate-900 flex items-center gap-2">
            <span class="w-8 h-8 rounded-lg bg-violet-100 text-violet-700 flex items-center justify-center text-sm font-black">3</span>
            رحلة العميل الكاملة (Academy Pipeline)
        </h2>
        <p class="text-sm text-slate-600 leading-7">
            كل انتقال يُسجَّل باسمك مع الوقت والملاحظات. يمكنك القفز لمرحلة مناسبة إذا كانت البيانات جاهزة،
            لكن يجب تعبئة الحقول المطلوبة للمرحلة المختارة.
        </p>
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-3 text-sm">
            @foreach([
                ['new_lead', 'عميل جديد من إعلان / واتساب / موقع. يُسجَّل الاسم والهاتف والمصدر والاهتمام.'],
                ['first_contact', 'أول محاولة اتصال. سجّل هل تم الرد أم لا + مدة المكالمة إن أمكن.'],
                ['no_answer', 'لم يرد. النظام يعد Attempt #1 ثم #2 بعد ساعتين ثم #3 في اليوم التالي.'],
                ['dormant', 'بعد 3 محاولات بدون رد. أعد تنشيطه لاحقاً بالانتقال إلى Connected.'],
                ['connected', 'تم الرد. اختر النتيجة: مهتم / مشغول / معاودة اتصال / يسأل فقط / رقم خطأ.'],
                ['qualification', 'أهم مرحلة: طالب/خريج/موظف، السن، المجال، الخبرة، الدافع، موعد البدء، القدرة على الدفع.'],
                ['interested', 'سجّل نسبة الاهتمام: 40٪ / 60٪ / 80٪ / 100٪.'],
                ['objection', 'لا تكتب «اعترض» فقط — اختر السبب (سعر، وقت، أهل، منافس، تقسيط…) + Notes.'],
                ['follow_up_scheduled', 'حدّد تاريخ ووقت المتابعة + القناة (Call / WhatsApp / Meeting).'],
                ['offer_sent', 'تم إرسال السعر/البروشور/التقسيط. سجّل السعر والخصم إن وُجد.'],
                ['payment_pending', 'وافق وينتظر الدفع: طريقة الدفع + القيمة + تاريخ الاستحقاق.'],
                ['payment_received', 'تم الدفع: رقم العملية + المبلغ + تاريخ الدفع.'],
                ['enrollment_completed', 'تم التسجيل في الأكاديمية. يصل طلب اعتماد Win للإدارة لاعتماد الكوميشن.'],
                ['upsell', 'بعد التسجيل: عرض كورسات أخرى / باقات / Membership.'],
                ['lost', 'خسارة مع سبب إلزامي (سعر، منافس، أجل القرار، لا وقت، لا يثق…).'],
            ] as [$key, $desc])
                <div @class([
                    'rounded-xl border p-4',
                    'bg-emerald-50 border-emerald-200' => $key === 'enrollment_completed',
                    'bg-rose-50 border-rose-200' => in_array($key, ['lost', 'dormant'], true),
                    'bg-amber-50 border-amber-200' => $key === 'objection',
                    'bg-slate-50 border-slate-200' => ! in_array($key, ['enrollment_completed', 'lost', 'dormant', 'objection'], true),
                ])>
                    <p class="font-bold text-slate-900 mb-1">{{ $stages[$key] ?? $key }}</p>
                    <p class="text-xs text-slate-600 leading-6">{{ $desc }}</p>
                </div>
            @endforeach
        </div>

        <div class="rounded-xl border border-amber-200 bg-amber-50/80 p-4 text-sm text-amber-950 space-y-2">
            <p class="font-bold">قاعدة No Answer المهمة</p>
            <ul class="list-disc list-inside space-y-1 text-amber-900/90">
                <li>Attempt #1 → جدولة محاولة بعد ساعتين</li>
                <li>Attempt #2 → جدولة في اليوم التالي</li>
                <li>Attempt #3 → يتحول تلقائياً إلى <strong>Dormant Lead</strong></li>
            </ul>
        </div>
    </section>

    {{-- CALL OUTCOMES --}}
    <section id="calls" class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 sm:p-6 space-y-4 scroll-mt-4">
        <h2 class="text-lg font-extrabold text-slate-900 flex items-center gap-2">
            <span class="w-8 h-8 rounded-lg bg-amber-100 text-amber-800 flex items-center justify-center text-sm font-black">4</span>
            نتائج المكالمة الإلزامية
        </h2>
        <p class="text-sm text-slate-600">أي نشاط من نوع «مكالمة» يجب أن يكون له نتيجة. هذا يغذي KPI اليومي ولوحة المدير.</p>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 text-sm">
            @foreach(\App\Models\SalesActivity::OUTCOMES as $k => $label)
                <div class="rounded-xl border border-slate-200 bg-slate-50 p-3">
                    <p class="font-bold text-slate-800">{{ $label }}</p>
                    <p class="text-xs text-slate-500 mt-1">
                        @switch($k)
                            @case('interested') يحدّث غالباً إلى Connected (مهتم) @break
                            @case('follow_up') متابعة / Connected حسب المرحلة الحالية @break
                            @case('no_answer') مسار No Answer + عدّاد المحاولات @break
                            @case('not_interested') Lost (لا يحتاج) @break
                            @case('wrong_number') Lost (رقم خطأ) @break
                            @case('closed_won') Enrollment Completed → طلب اعتماد للإدارة @break
                            @case('closed_lost') Lost @break
                            @default —
                        @endswitch
                    </p>
                </div>
            @endforeach
        </div>
        <p class="text-xs text-slate-500">يمكنك أيضاً إضافة مدة المكالمة بالثواني ورابط التسجيل الصوتي (اختياري) عند تحديث المرحلة.</p>
    </section>

    {{-- WIN --}}
    <section id="win" class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 sm:p-6 space-y-4 scroll-mt-4">
        <h2 class="text-lg font-extrabold text-slate-900 flex items-center gap-2">
            <span class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-800 flex items-center justify-center text-sm font-black">5</span>
            التسجيل، اعتماد الإدارة، والعمولة
        </h2>
        <ol class="space-y-2 text-sm text-slate-700 leading-7 list-decimal list-inside">
            <li>عندما تصل إلى <strong>Enrollment Completed</strong> يُرسل النظام طلباً للإدارة لاعتماد الصفقة.</li>
            <li>ستظهر لك رسالة: «في انتظار موافقة الإدارة» على صفحة العميل.</li>
            <li>بعد اعتماد الإدارة يُصرف الكوميشن ويظهر مؤكداً على صفحتك.</li>
            <li>إذا رُفض الاعتماد تعود الصفقة إلى <strong>Offer Sent</strong> مع سبب الرفض — أكمل المعالجة.</li>
            <li>بعد الإغلاق الناجح سجّل تقييم رضا العميل (CSAT) إن طُلب.</li>
        </ol>
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-950">
            <p class="font-bold mb-1">مهم</p>
            <p>لا يُحتسب الكوميشن نهائياً بمجرد تسجيل الفوز عندك — الاعتماد من الإدارة خطوة إلزامية.</p>
        </div>
    </section>

    {{-- KPI --}}
    <section id="kpi" class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 sm:p-6 space-y-4 scroll-mt-4">
        <h2 class="text-lg font-extrabold text-slate-900 flex items-center gap-2">
            <span class="w-8 h-8 rounded-lg bg-indigo-100 text-indigo-800 flex items-center justify-center text-sm font-black">6</span>
            KPI والتقرير اليومي
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
            <div class="rounded-xl border border-slate-200 p-4 space-y-2">
                <p class="font-bold text-slate-900">نتائج اليوم (SOS)</p>
                <p class="text-slate-600 leading-7">تُحسب تلقائياً من CRM: محاولات، ردود، مؤهل، جلسات، عروض، تسجيلات. تظهر على الداشبورد وداخل شاشة التقرير (قراءة فقط).</p>
            </div>
            <div class="rounded-xl border border-slate-200 p-4 space-y-2">
                <p class="font-bold text-slate-900">التقرير اليومي الإلزامي</p>
                <p class="text-slate-600 leading-7">يُملأ تلقائياً جزء كبير من الأرقام من نشاطك. أكمل المشاكل/الاحتياجات والملاحظات يدوياً، ثم سلّم قبل الموعد.</p>
            </div>
            <div class="rounded-xl border border-slate-200 p-4 space-y-2">
                <p class="font-bold text-slate-900">المؤشر المركّب الشهري</p>
                <p class="text-slate-600 leading-7">40٪ نتائج · 30٪ نشاط · 20٪ جودة · 10٪ التزام CRM — راجعه من لوحة KPIs.</p>
            </div>
            <div class="rounded-xl border border-slate-200 p-4 space-y-2">
                <p class="font-bold text-slate-900">ما يراه المدير</p>
                <p class="text-slate-600 leading-7">اللوحة الحية SOS + لوحة Pipeline (أعداد المراحل، التحويل، زمن البقاء، أسباب الخسارة، ترتيب الفريق).</p>
            </div>
        </div>
    </section>

    {{-- RULES --}}
    <section id="rules" class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 sm:p-6 space-y-4 scroll-mt-4">
        <h2 class="text-lg font-extrabold text-slate-900 flex items-center gap-2">
            <span class="w-8 h-8 rounded-lg bg-rose-100 text-rose-700 flex items-center justify-center text-sm font-black">7</span>
            قواعد الالتزام والحالات الحرجة
        </h2>
        <ul class="space-y-2 text-sm text-slate-700 leading-7">
            <li>لا تترك Lead جديداً بدون أول تواصل ضمن SLA.</li>
            <li>كل مكالمة لها نتيجة — لا تسجّل مكالمات فارغة.</li>
            <li>كل مرحلة أعمق تحتاج متابعة مجدولة (ما عدا الاستراحات/الحالات المغلقة).</li>
            <li>Lost و Objection دائماً بسبب واضح قابل للتحليل.</li>
            <li>لا يمكن إعادة فتح Lead مغلق (Lost / Dormant / Enrollment) من واجهتك — راجع الإدارة.</li>
            <li>Lead راكد بلا تواصل لمدة {{ \App\Models\SalesLead::STALE_CONTACT_DAYS }} أيام يدخل قائمة المهام العاجلة.</li>
            <li>التقرير اليومي غير المسلَّم يؤثر على KPI وقد ينشئ خصماً حسب إعدادات الإدارة.</li>
            <li>أنت مسؤول عن دقة بيانات العميل وجودة الملاحظات في النظام.</li>
        </ul>
    </section>

    <section class="bg-gradient-to-l from-slate-50 to-teal-50/40 rounded-2xl border border-slate-200 p-5 sm:p-6">
        <h2 class="text-base font-extrabold text-slate-900 mb-2">ملخص سريع لبداية الوردية</h2>
        <p class="text-sm text-slate-700 leading-7">
            ادخل مركز المبيعات → اقرأ البلوك الحالي → اعمل من Task Queue / المتابعات → سجّل مكالمات بنتائج →
            حدّث Pipeline بالحقول المطلوبة → تابع العروض والدفع → عند Enrollment انتظر اعتماد الإدارة →
            سلّم التقرير اليومي قبل نهاية اليوم.
        </p>
    </section>
</div>
@endsection
