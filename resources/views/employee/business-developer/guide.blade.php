@extends('layouts.employee')

@section('title', 'دليل Business Developer')
@section('header', 'Business Developer — دليل النظام')

@section('content')
<div class="p-4 sm:p-6 lg:p-8 space-y-6">
    <div class="bg-gradient-to-l from-violet-600 to-indigo-700 rounded-2xl shadow-lg p-5 sm:p-6 text-white">
        <h1 class="text-xl sm:text-2xl font-black flex items-center gap-2">
            <i class="fas fa-compass"></i>
            <span>دليل Business Developer</span>
        </h1>
        <p class="text-sm text-violet-100 mt-2 leading-7 max-w-3xl">
            أنت تجمع بين <strong>صلاحيات مدير المبيعات على كل الفرق</strong> و<strong>بوابة الماركتينغ والميديا</strong>.
            هذا الدليل يشرح دورك، الفرق عن مدير الفريق، وكل الأدوات في السايدبار.
        </p>
    </div>

    <nav class="bg-slate-50 rounded-2xl border border-slate-200 p-4 sm:p-5">
        <p class="text-xs font-bold text-slate-500 mb-2">محتويات الدليل</p>
        <div class="flex flex-wrap gap-2 text-sm">
            <a href="#role" class="px-3 py-1.5 rounded-lg bg-white border border-slate-200 hover:border-violet-300 text-slate-700">الدور</a>
            <a href="#difference" class="px-3 py-1.5 rounded-lg bg-white border border-slate-200 hover:border-violet-300 text-slate-700">الفرق عن مدير المبيعات</a>
            <a href="#marketing" class="px-3 py-1.5 rounded-lg bg-white border border-slate-200 hover:border-violet-300 text-slate-700">الماركتينغ</a>
            <a href="#sales" class="px-3 py-1.5 rounded-lg bg-white border border-slate-200 hover:border-violet-300 text-slate-700">المبيعات</a>
            <a href="#whatsapp" class="px-3 py-1.5 rounded-lg bg-white border border-slate-200 hover:border-violet-300 text-slate-700">واتساب</a>
            <a href="#workflow" class="px-3 py-1.5 rounded-lg bg-white border border-slate-200 hover:border-violet-300 text-slate-700">يومك العملي</a>
            <a href="#links" class="px-3 py-1.5 rounded-lg bg-white border border-slate-200 hover:border-violet-300 text-slate-700">روابط سريعة</a>
        </div>
    </nav>

    {{-- 1. Role --}}
    <section id="role" class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 sm:p-6 space-y-4 scroll-mt-4">
        <h2 class="text-lg font-extrabold text-slate-900 flex items-center gap-2">
            <span class="w-8 h-8 rounded-lg bg-violet-100 text-violet-700 flex items-center justify-center text-sm font-black">1</span>
            ما هو دور Business Developer؟
        </h2>
        <p class="text-sm text-slate-600 leading-7">
            Business Developer هو دور قيادي يربط بين <strong>نمو المبيعات</strong> و<strong>تنفيذ الماركتينغ</strong>.
            ليس موظف مبيعات يومي ولا مدير فريق واحد فقط — بل مشرف تشغيلي على:
        </p>
        <ul class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
            <li class="rounded-xl border border-violet-100 bg-violet-50/50 p-4">
                <p class="font-bold text-violet-900"><i class="fas fa-users-cog ml-1"></i> كل فرق المبيعات</p>
                <p class="text-slate-600 text-xs mt-1 leading-6">ترى عملاء ومحادثات وحضور وKPIs لكل موظفي السيلز عبر كل الفرق — ليس فريقاً واحداً.</p>
            </li>
            <li class="rounded-xl border border-fuchsia-100 bg-fuchsia-50/50 p-4">
                <p class="font-bold text-fuchsia-900"><i class="fas fa-bullhorn ml-1"></i> الماركتينغ والميديا</p>
                <p class="text-slate-600 text-xs mt-1 leading-6">خطط التسويق، طلبات التصميم، طلبات المونتاج، وتسويق اليوم — كصلاحيات مشرف المحتوى.</p>
            </li>
            <li class="rounded-xl border border-teal-100 bg-teal-50/50 p-4">
                <p class="font-bold text-teal-900"><i class="fas fa-share-alt ml-1"></i> التوزيع والتحويل</p>
                <p class="text-slate-600 text-xs mt-1 leading-6">توزيع Leads حسب الاهتمام، تحويل العملاء بين الموظفين، ومتابعة الورش والاستيراد.</p>
            </li>
            <li class="rounded-xl border border-amber-100 bg-amber-50/50 p-4">
                <p class="font-bold text-amber-900"><i class="fas fa-tasks ml-1"></i> المهام والتنسيق</p>
                <p class="text-slate-600 text-xs mt-1 leading-6">مهامك من الإدارة، التقويم، التقارير، والتنسيق بين السيلز والميديا.</p>
            </li>
        </ul>
    </section>

    {{-- 2. Difference --}}
    <section id="difference" class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 sm:p-6 space-y-4 scroll-mt-4">
        <h2 class="text-lg font-extrabold text-slate-900 flex items-center gap-2">
            <span class="w-8 h-8 rounded-lg bg-indigo-100 text-indigo-700 flex items-center justify-center text-sm font-black">2</span>
            الفرق بينك وبين مدير المبيعات العادي
        </h2>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm text-right border border-slate-200 rounded-xl overflow-hidden">
                <thead class="bg-slate-50 text-xs text-slate-500">
                    <tr>
                        <th class="px-3 py-2">الميزة</th>
                        <th class="px-3 py-2">مدير مبيعات</th>
                        <th class="px-3 py-2">Business Developer</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <tr>
                        <td class="px-3 py-2.5 font-semibold">نطاق الفريق</td>
                        <td class="px-3 py-2.5 text-slate-600">فريقه فقط</td>
                        <td class="px-3 py-2.5 text-violet-700 font-semibold">كل موظفي المبيعات في كل الفرق</td>
                    </tr>
                    <tr>
                        <td class="px-3 py-2.5 font-semibold">عملاء الفريق</td>
                        <td class="px-3 py-2.5 text-slate-600">Leads أعضاء فريقه</td>
                        <td class="px-3 py-2.5 text-violet-700 font-semibold">Leads أي موظف سيلز نشط</td>
                    </tr>
                    <tr>
                        <td class="px-3 py-2.5 font-semibold">الماركتينغ</td>
                        <td class="px-3 py-2.5 text-slate-600">—</td>
                        <td class="px-3 py-2.5 text-violet-700 font-semibold">خطط + تصميم + مونتاج + تسويق اليوم</td>
                    </tr>
                    <tr>
                        <td class="px-3 py-2.5 font-semibold">طلبات واتساب</td>
                        <td class="px-3 py-2.5 text-slate-600">فريقه</td>
                        <td class="px-3 py-2.5 text-violet-700 font-semibold">كل الطلبات — توزيع على أي موظف</td>
                    </tr>
                    <tr>
                        <td class="px-3 py-2.5 font-semibold">تبديل الشيفتات</td>
                        <td class="px-3 py-2.5 text-slate-600">فريقه</td>
                        <td class="px-3 py-2.5 text-violet-700 font-semibold">كل طلبات التبديل بين السيلز</td>
                    </tr>
                    <tr>
                        <td class="px-3 py-2.5 font-semibold">محادثات الفريق</td>
                        <td class="px-3 py-2.5 text-slate-600">فريقه</td>
                        <td class="px-3 py-2.5 text-violet-700 font-semibold">محادثات كل موظفي المبيعات</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="rounded-xl border border-indigo-200 bg-indigo-50/70 p-4 text-sm text-indigo-950">
            <p class="font-bold mb-1">ملاحظة مهمة</p>
            <p>أنت <strong>لا تظهر كموظف مبيعات يومي</strong> في السايدبار — لا مركز مبيعات شخصي ولا تقرير يومي سيلز.
            دورك إشراف وتنسيق وليس حمل Leads شخصي (إلا إذا أسندتها لك الإدارة يدوياً).</p>
        </div>
    </section>

    {{-- 3. Marketing --}}
    <section id="marketing" class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 sm:p-6 space-y-4 scroll-mt-4">
        <h2 class="text-lg font-extrabold text-slate-900 flex items-center gap-2">
            <span class="w-8 h-8 rounded-lg bg-fuchsia-100 text-fuchsia-700 flex items-center justify-center text-sm font-black">3</span>
            قسم الماركتينغ (Business Developer — الماركتينغ)
        </h2>
        <p class="text-sm text-slate-600 leading-7">
            هذا القسم في السايدبار يعادل صلاحيات <strong>مشرف المحتوى</strong> — لكنك ترى وتدير كل الطلبات وليس طلباتك فقط.
        </p>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-3 text-sm">
            <a href="{{ route('employee.design-cycles.index') }}" class="rounded-xl border border-slate-200 p-4 hover:border-fuchsia-300 hover:bg-fuchsia-50/30 transition">
                <p class="font-bold text-slate-900"><i class="fas fa-palette text-fuchsia-600 ml-1"></i> طلبات التصميم</p>
                <p class="text-xs text-slate-500 mt-2 leading-6">إنشاء دورة تصميم، متابعة المصمم، planner، وتسليم نهائي للمشرف.</p>
            </a>
            <a href="{{ route('employee.montage-requests.index') }}" class="rounded-xl border border-slate-200 p-4 hover:border-cyan-300 hover:bg-cyan-50/30 transition">
                <p class="font-bold text-slate-900"><i class="fas fa-film text-cyan-600 ml-1"></i> طلبات محرر الفيديو</p>
                <p class="text-xs text-slate-500 mt-2 leading-6">طلب مونتاج، متابعة التسليم، وإغلاق الطلب عند الاستلام.</p>
            </a>
            <a href="{{ route('employee.marketing-plans.index') }}" class="rounded-xl border border-slate-200 p-4 hover:border-pink-300 hover:bg-pink-50/30 transition">
                <p class="font-bold text-slate-900"><i class="fas fa-bullhorn text-pink-600 ml-1"></i> التسويق والمنصات</p>
                <p class="text-xs text-slate-500 mt-2 leading-6">خطط شهرية، منصات، أحداث، وربطها بالتقويم.</p>
            </a>
        </div>
        <div class="rounded-xl border border-fuchsia-200 bg-fuchsia-50/60 p-4 text-sm text-fuchsia-950 space-y-2">
            <p class="font-bold">متى تستخدم الماركتينغ؟</p>
            <ul class="list-disc list-inside space-y-1 text-fuchsia-900/90">
                <li>حملة جديدة تحتاج تصميمات أو Reels → افتح طلب تصميم أو مونتاج.</li>
                <li>جدولة محتوى أسبوعي/شهري → خطط التسويق + تسويق اليوم.</li>
                <li>تنسيق مع السيلز على Leads من إعلان → راجع توزيع الاهتمام بعد الحملة.</li>
            </ul>
        </div>
    </section>

    {{-- 4. Sales --}}
    <section id="sales" class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 sm:p-6 space-y-4 scroll-mt-4">
        <h2 class="text-lg font-extrabold text-slate-900 flex items-center gap-2">
            <span class="w-8 h-8 rounded-lg bg-teal-100 text-teal-700 flex items-center justify-center text-sm font-black">4</span>
            قسم المبيعات (Business Developer — المبيعات)
        </h2>
        <p class="text-sm text-slate-600 leading-7 mb-2">
            أدوات مدير المبيعات — لكن على <strong>كل الفرق</strong>. رتّب يومك حسب الأولوية:
        </p>
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 text-sm">
            <div class="space-y-2">
                <p class="text-xs font-bold text-teal-700 uppercase tracking-wide">مراقبة يومية</p>
                <ul class="space-y-2 text-slate-700">
                    <li class="flex gap-2"><i class="fas fa-tv text-teal-600 mt-0.5"></i><span><strong>اللوحة الحية SOS</strong> — نبض الفريق: اتصالات، متابعات، تأخير.</span></li>
                    <li class="flex gap-2"><i class="fas fa-broadcast-tower text-teal-600 mt-0.5"></i><span><strong>متابعة الفريق اليوم</strong> — حضور، موافقات، تأخير.</span></li>
                    <li class="flex gap-2"><i class="fas fa-shield-halved text-teal-600 mt-0.5"></i><span><strong>مركز الرقابة اليومية</strong> — Scorecard وKPIs.</span></li>
                    <li class="flex gap-2"><i class="fas fa-satellite-dish text-teal-600 mt-0.5"></i><span><strong>مراقبة التواجد</strong> — من متصل ومن غائب عن النبض.</span></li>
                </ul>
            </div>
            <div class="space-y-2">
                <p class="text-xs font-bold text-teal-700 uppercase tracking-wide">إدارة العملاء</p>
                <ul class="space-y-2 text-slate-700">
                    <li class="flex gap-2"><i class="fas fa-user-plus text-teal-600 mt-0.5"></i><span><strong>عملاء الفريق</strong> — كل Leads مع فلترة قوية (مصدر، ورشة، مجموعة).</span></li>
                    <li class="flex gap-2"><i class="fas fa-share-alt text-teal-600 mt-0.5"></i><span><strong>توزيع الاهتمام</strong> — إسناد Leads جديدة حسب نوع الاهتمام.</span></li>
                    <li class="flex gap-2"><i class="fas fa-exchange-alt text-teal-600 mt-0.5"></i><span><strong>تحويل Leads</strong> — نقل دفعة أو عميل بين الموظفين.</span></li>
                    <li class="flex gap-2"><i class="fas fa-clipboard-list text-teal-600 mt-0.5"></i><span><strong>رقابة المتابعات</strong> — متأخرة / اليوم / بلا موعد.</span></li>
                </ul>
            </div>
            <div class="space-y-2">
                <p class="text-xs font-bold text-teal-700 uppercase tracking-wide">الفريق والحضور</p>
                <ul class="space-y-2 text-slate-700">
                    <li class="flex gap-2"><i class="fas fa-clock text-teal-600 mt-0.5"></i><span><strong>حضور الفريق</strong> — موافقة/رفض، إعفاء تأخير.</span></li>
                    <li class="flex gap-2"><i class="fas fa-building text-teal-600 mt-0.5"></i><span><strong>تأكيد حضور المقر</strong> — أيام أوفلاين جماعية.</span></li>
                    <li class="flex gap-2"><i class="fas fa-calendar-week text-teal-600 mt-0.5"></i><span><strong>شيفتات وقنوات الفريق</strong> — جداول وقنوات Leads.</span></li>
                    <li class="flex gap-2"><i class="fas fa-right-left text-teal-600 mt-0.5"></i><span><strong>تبديل الشيفتات</strong> — موافقة طلبات التبديل.</span></li>
                </ul>
            </div>
            <div class="space-y-2">
                <p class="text-xs font-bold text-teal-700 uppercase tracking-wide">تقارير وتحليل</p>
                <ul class="space-y-2 text-slate-700">
                    <li class="flex gap-2"><i class="fas fa-project-diagram text-teal-600 mt-0.5"></i><span><strong>Pipeline الرحلة</strong> — مراحل العملاء وConversion.</span></li>
                    <li class="flex gap-2"><i class="fas fa-sitemap text-teal-600 mt-0.5"></i><span><strong>هيكل الفريق</strong> — Org chart للمبيعات.</span></li>
                    <li class="flex gap-2"><i class="fas fa-clipboard-check text-teal-600 mt-0.5"></i><span><strong>تقرير الفريق للإدارة</strong> — ملخص يُرفع للإدارة.</span></li>
                    <li class="flex gap-2"><i class="fas fa-scale-balanced text-teal-600 mt-0.5"></i><span><strong>قواعد وسياسات المبيعات</strong> — الدليل الرسمي للفريق.</span></li>
                </ul>
            </div>
        </div>
    </section>

    {{-- 5. WhatsApp --}}
    <section id="whatsapp" class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 sm:p-6 space-y-4 scroll-mt-4">
        <h2 class="text-lg font-extrabold text-slate-900 flex items-center gap-2">
            <span class="w-8 h-8 rounded-lg bg-amber-100 text-amber-800 flex items-center justify-center text-sm font-black">5</span>
            طلبات واتساب والمحادثات
        </h2>
        <ol class="space-y-3 text-sm text-slate-700 leading-7 list-decimal list-inside">
            <li><strong>طلبات جديدة</strong> — عميل كتب على الواتساب ولم يُسند بعد. Badge أصفر في السايدبار.</li>
            <li>افتح الطلب → اختر موظف السيلز المناسب (أي موظف نشط، ليس فريقاً واحداً).</li>
            <li>بعد الإسناد يظهر عند الموظف في <strong>محادثات الواتساب</strong> الخاصة به.</li>
            <li>يمكنك متابعة <strong>محادثات الفريق</strong> لمراجعة جودة الرد والمرحلة.</li>
            <li>Messenger & Instagram — نفس المنطق من صندوق Meta Social.</li>
        </ol>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('employee.sales-manager.whatsapp.queue.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-amber-500 text-white text-sm font-semibold hover:bg-amber-600">
                <i class="fas fa-bell"></i> طلبات واتساب
            </a>
            <a href="{{ route('employee.sales-manager.whatsapp.inbox.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border border-slate-300 text-slate-700 text-sm font-semibold hover:bg-slate-50">
                <i class="fab fa-whatsapp"></i> محادثات الفريق
            </a>
        </div>
    </section>

    {{-- 6. Workflow --}}
    <section id="workflow" class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 sm:p-6 space-y-4 scroll-mt-4">
        <h2 class="text-lg font-extrabold text-slate-900 flex items-center gap-2">
            <span class="w-8 h-8 rounded-lg bg-sky-100 text-sky-700 flex items-center justify-center text-sm font-black">6</span>
            يومك العملي المقترح
        </h2>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm text-right border border-slate-200 rounded-xl overflow-hidden">
                <thead class="bg-slate-50 text-xs text-slate-500">
                    <tr>
                        <th class="px-3 py-2">الوقت</th>
                        <th class="px-3 py-2">ماذا تفعل؟</th>
                        <th class="px-3 py-2">أين في النظام؟</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <tr><td class="px-3 py-2.5 tabular-nums">09:00</td><td class="px-3 py-2.5">مراجعة اللوحة الحية + طلبات واتساب</td><td class="px-3 py-2.5 text-sky-700">SOS + Queue</td></tr>
                    <tr><td class="px-3 py-2.5 tabular-nums">09:30</td><td class="px-3 py-2.5">توزيع Leads جديدة / متأخرة</td><td class="px-3 py-2.5 text-sky-700">توزيع الاهتمام + تحويل</td></tr>
                    <tr><td class="px-3 py-2.5 tabular-nums">11:00</td><td class="px-3 py-2.5">متابعة حضور وشيفتات</td><td class="px-3 py-2.5 text-sky-700">Ops Board + حضور</td></tr>
                    <tr><td class="px-3 py-2.5 tabular-nums">14:00</td><td class="px-3 py-2.5">مراجعة Pipeline + متابعات متأخرة</td><td class="px-3 py-2.5 text-sky-700">Pipeline + رقابة المتابعات</td></tr>
                    <tr><td class="px-3 py-2.5 tabular-nums">16:00</td><td class="px-3 py-2.5">تنسيق ماركتينغ (تصميم/مونتاج/خطة)</td><td class="px-3 py-2.5 text-sky-700">قسم الماركتينغ</td></tr>
                    <tr><td class="px-3 py-2.5 tabular-nums">17:30</td><td class="px-3 py-2.5">تقارير الأعضاء + تقرير الفريق</td><td class="px-3 py-2.5 text-sky-700">تقارير الأعضاء</td></tr>
                </tbody>
            </table>
        </div>
    </section>

    {{-- 7. Quick links --}}
    <section id="links" class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 sm:p-6 space-y-4 scroll-mt-4">
        <h2 class="text-lg font-extrabold text-slate-900 flex items-center gap-2">
            <span class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center text-sm font-black">7</span>
            روابط سريعة
        </h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 text-sm">
            <a href="{{ route('employee.sales-manager.dashboard') }}" class="rounded-xl border border-slate-200 p-3 hover:border-teal-300"><i class="fas fa-users-cog text-teal-600 ml-1"></i> مركز الفريق</a>
            <a href="{{ route('employee.sales-manager.leads.index') }}" class="rounded-xl border border-slate-200 p-3 hover:border-teal-300"><i class="fas fa-user-plus text-teal-600 ml-1"></i> عملاء الفريق</a>
            <a href="{{ route('employee.sales-manager.distribution.index') }}" class="rounded-xl border border-slate-200 p-3 hover:border-teal-300"><i class="fas fa-share-alt text-teal-600 ml-1"></i> توزيع الاهتمام</a>
            <a href="{{ route('employee.sales-manager.live-board') }}" class="rounded-xl border border-slate-200 p-3 hover:border-teal-300"><i class="fas fa-tv text-teal-600 ml-1"></i> اللوحة الحية</a>
            <a href="{{ route('employee.marketing-plans.index') }}" class="rounded-xl border border-slate-200 p-3 hover:border-fuchsia-300"><i class="fas fa-bullhorn text-fuchsia-600 ml-1"></i> خطط التسويق</a>
            <a href="{{ route('employee.design-cycles.index') }}" class="rounded-xl border border-slate-200 p-3 hover:border-fuchsia-300"><i class="fas fa-palette text-fuchsia-600 ml-1"></i> طلبات التصميم</a>
            <a href="{{ route('employee.tasks.index') }}" class="rounded-xl border border-slate-200 p-3 hover:border-blue-300"><i class="fas fa-tasks text-blue-600 ml-1"></i> مهامي</a>
            <a href="{{ route('employee.calendar') }}" class="rounded-xl border border-slate-200 p-3 hover:border-blue-300"><i class="fas fa-calendar text-blue-600 ml-1"></i> التقويم</a>
            <a href="{{ route('employee.documentation') }}" class="rounded-xl border border-slate-200 p-3 hover:border-blue-300"><i class="fas fa-book text-blue-600 ml-1"></i> دليل موظف المبيعات</a>
        </div>
    </section>

    <section class="bg-gradient-to-l from-slate-50 to-violet-50/40 rounded-2xl border border-slate-200 p-5 sm:p-6">
        <h2 class="text-base font-extrabold text-slate-900 mb-2">ملخص</h2>
        <p class="text-sm text-slate-700 leading-7">
            Business Developer = <strong>رؤية شاملة للمبيعات</strong> + <strong>تنفيذ الماركتينغ</strong>.
            ابدأ من اللوحة الحية وطلبات الواتساب، وزّع العملاء، راقب الحضور والمتابعات،
            ونسّق مع الميديا — كل ذلك من لوحة موظف واحدة.
        </p>
    </section>
</div>
@endsection
