<?php $__env->startSection('title', 'مركز المبيعات'); ?>
<?php $__env->startSection('header', 'مركز المبيعات'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <?php
        $dailyReportService = app(\App\Services\SalesDailyReportService::class);
        $todayDailyReport = $dailyReportService->todayReportFor(auth()->user());
        $dailySettings = \App\Support\SalesDailyReportSettings::all();
    ?>
    <?php if(($dailySettings['enabled'] ?? true) && $dailyReportService->isWorkDay(today(), auth()->user()) && !($todayDailyReport?->isSubmitted())): ?>
        <div class="rounded-2xl border-2 border-amber-400 bg-amber-50 px-5 py-4 flex flex-wrap items-center justify-between gap-3">
            <div class="text-sm text-amber-900">
                <p class="font-bold"><i class="fas fa-exclamation-circle ml-1"></i> التقرير اليومي الإلزامي لم يُسلَّم بعد</p>
                <p class="mt-1 text-xs">يؤثر على KPI — عدم التسليم قبل <?php echo e($dailySettings['deadline_time'] ?? '23:59'); ?> قد يُنشئ خصماً تلقائياً.</p>
            </div>
            <a href="<?php echo e(route('employee.sales.daily-reports.edit')); ?>" class="shrink-0 px-5 py-2.5 bg-amber-600 hover:bg-amber-700 text-white rounded-xl font-bold text-sm">تعبئة التقرير الآن</a>
        </div>
    <?php endif; ?>

    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">مركز المبيعات</h1>
            <p class="text-gray-600 text-sm mt-1">مؤشرات سريعة، قمع المراحل، وقوائم تحتاج حركة اليوم</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="<?php echo e(route('employee.sales.leads.export')); ?>" class="inline-flex items-center gap-2 px-4 py-2.5 bg-gradient-to-l from-emerald-600 to-teal-600 text-white rounded-xl text-sm font-bold shadow-md hover:from-emerald-700 hover:to-teal-700">
                <i class="fas fa-file-excel"></i> تصدير Excel
            </a>
            <a href="<?php echo e(route('employee.sales.leads.create')); ?>" class="inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-semibold text-sm">
                <i class="fas fa-plus"></i> عميل محتمل جديد
            </a>
        </div>
    </div>

    <?php $kq = $kpiQuick ?? null; ?>
    <?php if($kq): ?>
    <section class="rounded-2xl border-2 border-slate-200 bg-gradient-to-br from-slate-900 via-slate-800 to-emerald-900 text-white p-5 md:p-6 shadow-xl">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div>
                <p class="text-xs font-bold text-emerald-300 uppercase tracking-wider mb-1">المؤشر المركّب (الشهر الحالي)</p>
                <p class="text-4xl font-black tabular-nums"><?php echo e($kq['composite_month']); ?><span class="text-lg font-bold text-white/70">/100</span></p>
                <p class="text-xs text-white/70 mt-2 max-w-xl">40٪ نتائج · 30٪ نشاط · 20٪ جودة · 10٪ التزام CRM — تفاصيل كاملة من «KPIs والأداء».</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <?php $__currentLoopData = ($kq['month']['pillars'] ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pk => $pv): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <span class="inline-flex flex-col rounded-xl bg-white/10 border border-white/20 px-3 py-2 text-center min-w-[100px]">
                        <span class="text-[10px] text-white/60"><?php echo e($pk); ?></span>
                        <span class="text-lg font-black"><?php echo e($pv['score'] ?? '—'); ?></span>
                    </span>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <a href="<?php echo e(route('employee.sales.kpi.index')); ?>" class="inline-flex items-center justify-center gap-2 rounded-xl bg-emerald-500 hover:bg-emerald-400 text-slate-900 font-bold px-5 py-3 text-sm">
                    <i class="fas fa-bullseye"></i> لوحة KPIs
                </a>
            </div>
        </div>
        <?php if(!empty($kq['alert_flags'])): ?>
            <div class="mt-4 rounded-xl border border-amber-400/50 bg-amber-500/20 px-4 py-3 text-sm text-amber-100 space-y-1">
                <?php $__currentLoopData = $kq['alert_flags']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $f): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><p><i class="fas fa-exclamation-triangle ml-1"></i><?php echo e($f); ?></p><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        <?php endif; ?>
    </section>
    <?php endif; ?>

    <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-6 gap-3 md:gap-4">
        <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-semibold text-gray-500">الإجمالي</span>
                <i class="fas fa-users text-slate-500"></i>
            </div>
            <p class="text-2xl font-black text-gray-900"><?php echo e($stats['total']); ?></p>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-semibold text-gray-500">نشط (قيد المعالجة)</span>
                <i class="fas fa-fire text-amber-500"></i>
            </div>
            <p class="text-2xl font-black text-gray-900"><?php echo e($stats['active']); ?></p>
        </div>
        <div class="rounded-xl border border-rose-200 bg-rose-50/50 p-4 shadow-sm">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-semibold text-rose-800">متابعات متأخرة</span>
                <i class="fas fa-bell text-rose-500"></i>
            </div>
            <p class="text-2xl font-black text-rose-700"><?php echo e($stats['followups_overdue']); ?></p>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-semibold text-gray-500">متابعات اليوم</span>
                <i class="fas fa-calendar-day text-violet-500"></i>
            </div>
            <p class="text-2xl font-black text-gray-900"><?php echo e($stats['followups_today']); ?></p>
        </div>
        <div class="rounded-xl border border-amber-200 bg-amber-50/40 p-4 shadow-sm">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-semibold text-amber-900">بلا تواصل <?php echo e(\App\Models\SalesLead::STALE_CONTACT_DAYS); ?>+ يوم</span>
                <i class="fas fa-hourglass-end text-amber-600"></i>
            </div>
            <p class="text-2xl font-black text-amber-900"><?php echo e($stats['stale']); ?></p>
        </div>
        <div class="rounded-xl border border-rose-100 bg-white p-4 shadow-sm">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-semibold text-gray-500">أولوية عاجلة (مفتوح)</span>
                <i class="fas fa-bolt text-rose-500"></i>
            </div>
            <p class="text-2xl font-black text-gray-900"><?php echo e($stats['urgent_open']); ?></p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
        <div class="rounded-xl border border-emerald-200 bg-gradient-to-br from-emerald-50 to-white p-5 shadow-sm md:col-span-2">
            <p class="text-xs font-bold text-emerald-800 uppercase tracking-wide mb-1">قيمة الأنابيب (مفتوحة)</p>
            <p class="text-3xl font-black text-emerald-900"><?php echo e(number_format($stats['pipeline_value'], 0)); ?> <span class="text-lg font-bold text-emerald-700">ج.م</span></p>
            <p class="text-xs text-gray-600 mt-2">مجموع «قيمة متوقعة» للعملاء غير المكتمل/الخسارة.</p>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
            <p class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">فوز — هذا الشهر</p>
            <p class="text-2xl font-black text-emerald-700"><?php echo e(number_format($stats['won_month_value'], 0)); ?> <span class="text-sm font-bold">ج.م</span></p>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm flex flex-wrap gap-4 justify-around items-center">
            <div class="text-center">
                <p class="text-xs text-gray-500">مكتمل</p>
                <p class="text-xl font-black text-emerald-600"><?php echo e($stats['won']); ?></p>
            </div>
            <div class="text-center">
                <p class="text-xs text-gray-500">خسارة</p>
                <p class="text-xl font-black text-rose-600"><?php echo e($stats['lost']); ?></p>
            </div>
            <div class="text-center">
                <p class="text-xs text-gray-500">جديد</p>
                <p class="text-xl font-black text-blue-600"><?php echo e($stats['new']); ?></p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 md:p-6">
        <h2 class="font-bold text-gray-900 mb-4">قمع المراحل</h2>
        <p class="text-xs text-gray-500 mb-4">عدد العملاء في كل مرحلة — يساعد على معرفة أين يتراكم العمل.</p>
        <?php $maxF = max($funnel) ?: 1; ?>
        <div class="space-y-3">
            <?php $__currentLoopData = \App\Models\SalesLead::STAGES; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php $c = $funnel[$key] ?? 0; $pct = round(($c / $maxF) * 100); ?>
                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span class="font-medium text-gray-800"><?php echo e($label); ?></span>
                        <span class="text-gray-600 tabular-nums"><?php echo e($c); ?></span>
                    </div>
                    <div class="h-2.5 rounded-full bg-gray-100 overflow-hidden">
                        <div class="h-full rounded-full bg-gradient-to-l from-emerald-500 to-teal-400 transition-all duration-500" style="width: <?php echo e($pct); ?>%"></div>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-xl border border-indigo-200 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-indigo-100 flex justify-between items-center bg-indigo-50/50">
                <h2 class="font-bold text-gray-900">Task Queue اليومية (الأولوية)</h2>
                <span class="text-xs text-indigo-700 font-semibold">Next Best Action</span>
            </div>
            <ul class="divide-y divide-gray-100">
                <?php $__empty_1 = true; $__currentLoopData = ($taskQueue ?? collect()); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <?php $lead = $item['lead']; ?>
                    <li class="px-5 py-3 hover:bg-indigo-50/20">
                        <a href="<?php echo e(route('employee.sales.leads.show', $lead)); ?>" class="block">
                            <div class="flex items-center justify-between gap-2">
                                <span class="font-semibold text-gray-900"><?php echo e($lead->name); ?></span>
                                <span class="text-[11px] px-2 py-0.5 rounded-full bg-indigo-100 text-indigo-700 font-bold"><?php echo e($item['reason']); ?></span>
                            </div>
                            <p class="mt-1 text-xs text-gray-600"><?php echo e($item['next_action']); ?></p>
                        </a>
                    </li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <li class="px-5 py-8 text-center text-gray-500 text-sm">لا توجد عناصر حرجة في القائمة الآن.</li>
                <?php endif; ?>
            </ul>
        </div>

        <div class="bg-white rounded-xl border border-rose-200 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-rose-100 flex justify-between items-center bg-rose-50/50">
                <h2 class="font-bold text-gray-900">متابعات متأخرة</h2>
                <a href="<?php echo e(route('employee.sales.leads.index', ['follow_up' => 'overdue', 'sort' => 'follow_up'])); ?>" class="text-sm text-rose-700 font-semibold hover:underline">عرض الكل</a>
            </div>
            <ul class="divide-y divide-gray-100">
                <?php $__empty_1 = true; $__currentLoopData = $overdueLeads; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $l): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <li class="px-5 py-3 hover:bg-rose-50/30">
                    <a href="<?php echo e(route('employee.sales.leads.show', $l)); ?>" class="flex justify-between gap-2">
                        <span class="font-medium text-gray-900"><?php echo e($l->name); ?></span>
                        <span class="text-xs text-rose-600 font-semibold whitespace-nowrap"><?php echo e($l->next_follow_up_at?->format('Y-m-d H:i')); ?></span>
                    </a>
                </li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <li class="px-5 py-8 text-center text-gray-500 text-sm">لا توجد متابعات متأخرة — ممتاز.</li>
                <?php endif; ?>
            </ul>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-xl border border-violet-200 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-violet-100 flex justify-between items-center bg-violet-50/40">
                <h2 class="font-bold text-gray-900">تنبيه SLA: أول رد متأخر (<?php echo e($slaCutoffHours ?? 24); ?> ساعة+)</h2>
                <span class="text-xs text-violet-700 font-semibold">فوري</span>
            </div>
            <ul class="divide-y divide-gray-100">
                <?php $__empty_1 = true; $__currentLoopData = ($noFirstResponseLeads ?? collect()); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $l): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <li class="px-5 py-3 hover:bg-violet-50/30">
                    <a href="<?php echo e(route('employee.sales.leads.show', $l)); ?>" class="flex justify-between gap-2">
                        <span class="font-medium text-gray-900"><?php echo e($l->name); ?></span>
                        <span class="text-xs text-violet-700 font-semibold"><?php echo e($l->created_at?->diffForHumans()); ?></span>
                    </a>
                </li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <li class="px-5 py-8 text-center text-gray-500 text-sm">ممتاز، لا يوجد تأخير في أول رد ضمن معيار SLA الحالي.</li>
                <?php endif; ?>
            </ul>
        </div>

        <div class="bg-white rounded-xl border border-amber-200 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-amber-100 flex justify-between items-center bg-amber-50/40">
                <h2 class="font-bold text-gray-900">يحتاجون تواصلاً (<?php echo e(\App\Models\SalesLead::STALE_CONTACT_DAYS); ?>+ يوم)</h2>
                <a href="<?php echo e(route('employee.sales.leads.index', ['stale' => 1])); ?>" class="text-sm text-amber-800 font-semibold hover:underline">عرض الكل</a>
            </div>
            <ul class="divide-y divide-gray-100">
                <?php $__empty_1 = true; $__currentLoopData = $staleLeads; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $l): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <li class="px-5 py-3 hover:bg-amber-50/30">
                    <a href="<?php echo e(route('employee.sales.leads.show', $l)); ?>" class="flex justify-between gap-2">
                        <span class="font-medium text-gray-900"><?php echo e($l->name); ?></span>
                        <span class="text-xs text-gray-500"><?php echo e($l->last_contacted_at?->diffForHumans() ?? 'لم يُسجَّل'); ?></span>
                    </a>
                </li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <li class="px-5 py-8 text-center text-gray-500 text-sm">لا يوجد عملاء راكدون حسب المعيار الحالي.</li>
                <?php endif; ?>
            </ul>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 flex justify-between items-center">
                <h2 class="font-bold text-gray-900">متابعات اليوم</h2>
                <a href="<?php echo e(route('employee.sales.leads.index', ['follow_up' => 'today'])); ?>" class="text-sm text-emerald-600 font-medium">القائمة</a>
            </div>
            <ul class="divide-y divide-gray-100">
                <?php $__empty_1 = true; $__currentLoopData = $followupsToday; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $l): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <li class="px-5 py-3 hover:bg-gray-50">
                    <a href="<?php echo e(route('employee.sales.leads.show', $l)); ?>" class="flex justify-between gap-2">
                        <span class="font-medium text-gray-900"><?php echo e($l->name); ?></span>
                        <span class="text-xs text-gray-500"><?php echo e($l->next_follow_up_at?->format('H:i')); ?></span>
                    </a>
                </li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <li class="px-5 py-8 text-center text-gray-500 text-sm">لا توجد متابعات مجدولة اليوم</li>
                <?php endif; ?>
            </ul>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 flex justify-between items-center">
                <h2 class="font-bold text-gray-900">آخر التحديثات</h2>
                <a href="<?php echo e(route('employee.sales.leads.index')); ?>" class="text-sm text-emerald-600 font-medium">الكل</a>
            </div>
            <ul class="divide-y divide-gray-100">
                <?php $__empty_1 = true; $__currentLoopData = $recentLeads; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $l): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <li class="px-5 py-3 hover:bg-gray-50 flex justify-between items-center gap-2">
                    <a href="<?php echo e(route('employee.sales.leads.show', $l)); ?>" class="font-medium text-gray-900"><?php echo e($l->name); ?></a>
                    <span class="text-xs px-2 py-0.5 rounded-full bg-gray-100 text-gray-700"><?php echo e(\App\Models\SalesLead::stageLabel($l->stage)); ?></span>
                </li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <li class="px-5 py-8 text-center text-gray-500 text-sm">ابدأ بإضافة عميل محتمل</li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.employee', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\employee\sales\dashboard.blade.php ENDPATH**/ ?>