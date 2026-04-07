@extends('layouts.employee')

@section('title', 'Documentation الموظف')
@section('header', 'Documentation النظام')

@section('content')
<div class="p-4 sm:p-6 lg:p-8 space-y-6">
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 sm:p-6">
        <h1 class="text-xl sm:text-2xl font-black text-slate-900 flex items-center gap-2">
            <i class="fas fa-book-open text-blue-600"></i>
            <span>دليل النظام للموظف</span>
        </h1>
        <p class="text-sm text-slate-600 mt-2 leading-7">
            هذه الصفحة مرجع عملي موحّد يشرح طريقة العمل داخل النظام، المطلوب من الموظف، مؤشرات الأداء، وخطة التطوير القادمة
            لقسم المبيعات على مراحل 30 / 60 / 90 يوم.
        </p>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <section class="xl:col-span-2 bg-white rounded-2xl border border-slate-200 shadow-sm p-5 sm:p-6 space-y-5">
            <h2 class="text-lg font-extrabold text-slate-900">1) المطلوب اليومي من موظف المبيعات</h2>
            <ul class="space-y-3 text-sm text-slate-700 leading-7">
                <li>تحديث حالة كل Lead بعد أي تواصل مباشر (اتصال، واتساب، أو إيميل).</li>
                <li>تحديد موعد متابعة واضح في <span class="font-semibold">Next Follow-up</span> لكل Lead مفتوح.</li>
                <li>تسجيل نشاط فعلي لا يقل عن الحد الأدنى اليومي المتفق عليه مع الإدارة.</li>
                <li>عدم ترك أي Lead بدون أول رد أكثر من SLA المحدد.</li>
                <li>عند الإغلاق كـ <span class="font-semibold">lost</span> يجب توثيق سبب الخسارة بشكل واضح.</li>
            </ul>
        </section>

        <aside class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 sm:p-6 space-y-4">
            <h3 class="text-base font-extrabold text-slate-900">مؤشرات تقييم الموظف</h3>
            <ul class="space-y-2 text-sm text-slate-700">
                <li>سرعة أول رد.</li>
                <li>الالتزام بالمتابعة في موعدها.</li>
                <li>نسبة التحويل من جديد إلى مكتمل.</li>
                <li>جودة التوثيق داخل النظام.</li>
                <li>درجة رضا العميل (CSAT).</li>
            </ul>
        </aside>
    </div>

    <section class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 sm:p-6 space-y-4">
        <h2 class="text-lg font-extrabold text-slate-900">2) سياسة المراحل (Pipeline Policy)</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-3 text-sm">
            <div class="rounded-xl border border-slate-200 p-4 bg-slate-50">
                <p class="font-bold text-slate-800 mb-1">new</p>
                <p class="text-slate-600">عميل جديد لم يتم التواصل معه بشكل فعلي.</p>
            </div>
            <div class="rounded-xl border border-slate-200 p-4 bg-slate-50">
                <p class="font-bold text-slate-800 mb-1">contacted</p>
                <p class="text-slate-600">تم أول تواصل وتأكيد بيانات العميل الأساسية.</p>
            </div>
            <div class="rounded-xl border border-slate-200 p-4 bg-slate-50">
                <p class="font-bold text-slate-800 mb-1">qualified</p>
                <p class="text-slate-600">العميل مناسب للعرض ويوجد اهتمام واضح.</p>
            </div>
            <div class="rounded-xl border border-slate-200 p-4 bg-slate-50">
                <p class="font-bold text-slate-800 mb-1">proposal</p>
                <p class="text-slate-600">تم إرسال عرض سعر أو عرض برنامج مناسب للعميل.</p>
            </div>
            <div class="rounded-xl border border-slate-200 p-4 bg-emerald-50 border-emerald-200">
                <p class="font-bold text-emerald-800 mb-1">won</p>
                <p class="text-emerald-700">إغلاق ناجح مكتمل مع توثيق القيمة النهائية.</p>
            </div>
            <div class="rounded-xl border border-rose-200 p-4 bg-rose-50">
                <p class="font-bold text-rose-800 mb-1">lost</p>
                <p class="text-rose-700">خسارة مع سبب خسارة إلزامي وقابل للتحليل.</p>
            </div>
        </div>
    </section>

    <section class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 sm:p-6 space-y-5">
        <h2 class="text-lg font-extrabold text-slate-900">3) Roadmap تنفيذ 30 / 60 / 90 يوم</h2>

        <div class="rounded-2xl border border-blue-200 bg-blue-50/60 p-4 sm:p-5">
            <h3 class="text-base font-black text-blue-900 mb-3">خلال 30 يوم (Quick Wins)</h3>
            <ul class="space-y-2 text-sm text-blue-900">
                <li>Dashboard للـ SLA + تنبيهات التأخير.</li>
                <li>Loss Reasons Analytics لتجميع وتحليل أسباب الخسارة.</li>
                <li>Auto Follow-up Reminders للمتابعات اليومية.</li>
                <li>Source Performance Dashboard لمقارنة جودة المصادر.</li>
                <li>Quality Score أولي لكل موظف مبيعات.</li>
            </ul>
        </div>

        <div class="rounded-2xl border border-emerald-200 bg-emerald-50/60 p-4 sm:p-5">
            <h3 class="text-base font-black text-emerald-900 mb-3">خلال 60 يوم (Middle Layer)</h3>
            <ul class="space-y-2 text-sm text-emerald-900">
                <li>Task Queue ذكية يومية لأولويات التنفيذ.</li>
                <li>Next Best Action لكل Lead حسب المرحلة والسلوك.</li>
                <li>قوالب تواصل جاهزة (واتساب/إيميل/ملاحظات اتصال).</li>
                <li>توزيع حمل Leads (Workload Balancer) بين الموظفين.</li>
                <li>Leaderboards متوازنة (كمية + جودة + التزام).</li>
            </ul>
        </div>

        <div class="rounded-2xl border border-amber-200 bg-amber-50/60 p-4 sm:p-5">
            <h3 class="text-base font-black text-amber-900 mb-3">خلال 90 يوم (Governance & Forecast)</h3>
            <ul class="space-y-2 text-sm text-amber-900">
                <li>Pipeline Forecast شهري/ربعي بالتنبؤ.</li>
                <li>Cohort Analysis حسب شهر دخول الـ Leads.</li>
                <li>Compliance Rules إلزامية قبل نقل المرحلة.</li>
                <li>Anomaly Detection لرصد الشذوذ والنشاط غير الطبيعي.</li>
                <li>تقوية الفصل بين الصلاحيات (Segregation of Duties).</li>
            </ul>
        </div>
    </section>

    <section class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 sm:p-6">
        <h2 class="text-lg font-extrabold text-slate-900 mb-3">4) متطلبات الالتزام والتوثيق</h2>
        <ul class="space-y-2 text-sm text-slate-700 leading-7">
            <li>أي تغيير في المرحلة يجب أن يكون مدعوم بنشاط مسجل داخل النظام.</li>
            <li>أي Lead بدون تحديث لفترة طويلة يدخل تلقائيا ضمن الحالات الحرجة للمراجعة.</li>
            <li>لا يتم حذف البيانات التشغيلية بدون صلاحية واضحة وتسجيل تدقيقي.</li>
            <li>كل موظف مسؤول عن دقة بيانات العملاء وجودة الملاحظات.</li>
        </ul>
    </section>
</div>
@endsection
