<?php $__env->startSection('title', 'أهداف KPIs المبيعات'); ?>
<?php $__env->startSection('header', 'أهداف KPIs المبيعات'); ?>

<?php
    $labels = [
        'leads_daily' => 'Leads جديدة / يوم',
        'leads_weekly' => 'Leads جديدة / أسبوع',
        'deals_weekly' => 'صفقات مغلقة / أسبوع',
        'revenue_monthly' => 'إيراد شهري (ج.م) — قيمة متوقعة مكتملة',
        'calls_daily' => 'مكالمات / يوم',
        'meetings_daily' => 'اجتماعات أو ديمو / يوم',
        'followups_daily' => 'متابعات مسجّلة / يوم',
        'response_minutes_max' => 'أقصى متوسط أول رد (دقيقة)',
        'closing_ratio_pct_min' => 'أدنى نسبة إغلاق won/(won+lost) %',
        'csat_min' => 'أدنى متوسط CSAT (1–5)',
        'loss_ratio_max_pct' => 'أقصى نسبة خسارة مقبولة %',
        'open_opportunities_min' => 'أدنى فرص مفتوحة في الأنبوب',
        'sales_cycle_max_days' => 'أقصى متوسط دورة بيع (يوم)',
        'crm_activities_daily_min' => 'أدنى أنشطة CRM / يوم',
        'data_fresh_open_pct_min' => 'أدنى % فرص محدّثة خلال 7 أيام',
        'engagement_days_pct_min' => 'أدنى % أيام بتفاعل مسجّل',
        'conversion_pct_target' => 'هدف نسبة تحويل % (شهري)',
    ];
    $groups = [
        'النشاط اليومي' => ['icon' => 'fas fa-bolt text-amber-600', 'keys' => ['leads_daily', 'leads_weekly', 'calls_daily', 'meetings_daily', 'followups_daily', 'crm_activities_daily_min']],
        'النتائج والإيراد' => ['icon' => 'fas fa-coins text-emerald-600', 'keys' => ['deals_weekly', 'revenue_monthly', 'closing_ratio_pct_min', 'conversion_pct_target']],
        'الجودة والالتزام' => ['icon' => 'fas fa-star text-sky-600', 'keys' => ['response_minutes_max', 'csat_min', 'loss_ratio_max_pct', 'sales_cycle_max_days', 'engagement_days_pct_min']],
        'الأنبوب والبيانات' => ['icon' => 'fas fa-filter text-violet-600', 'keys' => ['open_opportunities_min', 'data_fresh_open_pct_min']],
    ];
    $filledCount = collect($labels)->filter(fn ($_, $key) => ($targets[$key] ?? '') !== '' && ($targets[$key] ?? null) !== null)->count();
    $inputClass = 'w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500 focus:border-sky-500';
    $statCards = [
        ['label' => 'موظفو مبيعات', 'value' => number_format($salesReps->count()), 'icon' => 'fas fa-users', 'bg' => 'bg-emerald-100', 'text' => 'text-emerald-600', 'description' => 'نشطون'],
        ['label' => 'حقول الأهداف', 'value' => number_format(count($labels)), 'icon' => 'fas fa-sliders-h', 'bg' => 'bg-sky-100', 'text' => 'text-sky-600', 'description' => 'مؤشر KPI'],
        ['label' => 'أهداف مُعبّأة', 'value' => number_format($filledCount), 'icon' => 'fas fa-check-circle', 'bg' => 'bg-violet-100', 'text' => 'text-violet-600', 'description' => 'للموظف والشهر'],
        ['label' => 'الشهر', 'value' => $yearMonth, 'icon' => 'fas fa-calendar-alt', 'bg' => 'bg-amber-100', 'text' => 'text-amber-600', 'description' => $rep?->name ?? '—'],
    ];
?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    
    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-4 py-4 bg-slate-50 border-b border-slate-200 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-violet-500 to-violet-600 flex items-center justify-center text-white shadow-md">
                    <i class="fas fa-sliders-h"></i>
                </div>
                <div>
                    <h2 class="text-xl font-black text-slate-900">أهداف KPIs المبيعات</h2>
                    <p class="text-xs text-slate-600">ضبط الأهداف الشهرية وإعدادات الكوميشن لكل موظف.</p>
                </div>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <a href="<?php echo e(route('admin.sales.kpi.index')); ?>" class="inline-flex items-center gap-2 px-3 py-2 text-sm font-semibold text-slate-700 rounded-xl border border-slate-300 hover:bg-white">
                    <i class="fas fa-chart-line text-emerald-600"></i>
                    لوحة المراقبة
                </a>
                <a href="<?php echo e(route('admin.sales.leads.index')); ?>" class="inline-flex items-center gap-2 px-3 py-2 text-sm font-semibold text-slate-700 rounded-xl border border-slate-300 hover:bg-white">
                    <i class="fas fa-user-tag text-emerald-600"></i>
                    العملاء المحتملون
                </a>
            </div>
        </div>
        <div class="grid grid-cols-2 xl:grid-cols-4 gap-3 p-4">
            <?php $__currentLoopData = $statCards; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $card): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                    <div class="flex items-center justify-between gap-2">
                        <div class="min-w-0">
                            <p class="text-xs font-semibold text-slate-600 truncate"><?php echo e($card['label']); ?></p>
                            <p class="text-xl font-black text-slate-900 truncate tabular-nums"><?php echo e($card['value']); ?></p>
                        </div>
                        <div class="w-9 h-9 rounded-lg <?php echo e($card['bg']); ?> flex items-center justify-center <?php echo e($card['text']); ?> flex-shrink-0">
                            <i class="<?php echo e($card['icon']); ?> text-sm"></i>
                        </div>
                    </div>
                    <p class="text-[11px] text-slate-500 mt-1 truncate"><?php echo e($card['description']); ?></p>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </section>

    <?php if(session('success')): ?>
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-emerald-900 text-sm font-semibold flex items-center gap-2">
            <i class="fas fa-check-circle text-emerald-600"></i>
            <?php echo e(session('success')); ?>

        </div>
    <?php endif; ?>

    <?php if($errors->any()): ?>
        <div class="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-rose-900 text-sm">
            <p class="font-semibold mb-1 flex items-center gap-2"><i class="fas fa-exclamation-circle"></i> تحقق من البيانات</p>
            <ul class="list-disc list-inside space-y-0.5 text-xs">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($error); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
    <?php endif; ?>

    <?php if($salesReps->isEmpty()): ?>
        <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
            <div class="px-4 py-12 text-center">
                <div class="w-14 h-14 mx-auto mb-3 rounded-xl bg-slate-100 flex items-center justify-center text-slate-400">
                    <i class="fas fa-users text-xl"></i>
                </div>
                <p class="text-sm font-semibold text-slate-900">لا موظفو مبيعات</p>
                <p class="text-xs text-slate-500 mt-1">لا يوجد موظفو مبيعات نشطون لضبط الأهداف.</p>
            </div>
        </section>
    <?php else: ?>
        
        <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
            <div class="px-4 py-3 border-b border-slate-200 bg-slate-50">
                <h3 class="text-base font-black text-slate-900 flex items-center gap-2">
                    <i class="fas fa-filter text-sky-600"></i>
                    اختيار الموظف والشهر
                </h3>
            </div>
            <div class="p-4">
                <form method="get" action="<?php echo e(route('admin.sales.kpi.targets')); ?>" class="flex flex-col gap-3">
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 items-end">
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-semibold text-slate-700 mb-1">الموظف</label>
                            <select name="user_id" class="<?php echo e($inputClass); ?>">
                                <?php $__currentLoopData = $salesReps; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sr): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($sr->id); ?>" <?php if((int) $userId === (int) $sr->id): echo 'selected'; endif; ?>><?php echo e($sr->name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">الشهر</label>
                            <input type="month" name="year_month" value="<?php echo e($yearMonth); ?>" class="<?php echo e($inputClass); ?>">
                        </div>
                        <div>
                            <button type="submit" class="w-full inline-flex items-center justify-center gap-2 rounded-xl bg-sky-600 hover:bg-sky-700 px-4 py-2 text-sm font-semibold text-white">
                                <i class="fas fa-search"></i>
                                عرض
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </section>

        <form method="post" action="<?php echo e(route('admin.sales.kpi.targets.update')); ?>">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>
            <input type="hidden" name="user_id" value="<?php echo e($userId); ?>">
            <input type="hidden" name="year_month" value="<?php echo e($yearMonth); ?>">

            
            <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
                <div class="px-4 py-3 border-b border-slate-200 bg-slate-50 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                    <div>
                        <h3 class="text-base font-black text-slate-900 flex items-center gap-2">
                            <i class="fas fa-coins text-emerald-600"></i>
                            إعدادات الكوميشن
                        </h3>
                        <p class="text-xs text-slate-600">لكل موظف — تُستخدم عند اعتماد wins من الإدارة.</p>
                    </div>
                    <span class="text-xs font-semibold text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-lg border border-emerald-200"><?php echo e($rep?->name ?? '—'); ?></span>
                </div>
                <div class="p-4">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">النظام</label>
                            <select name="sales_commission_mode" class="<?php echo e($inputClass); ?>">
                                <option value="none" <?php if(old('sales_commission_mode', $rep?->sales_commission_mode ?? 'none') === 'none'): echo 'selected'; endif; ?>>بدون</option>
                                <option value="percent" <?php if(old('sales_commission_mode', $rep?->sales_commission_mode) === 'percent'): echo 'selected'; endif; ?>>نسبة % من expected value</option>
                                <option value="fixed" <?php if(old('sales_commission_mode', $rep?->sales_commission_mode) === 'fixed'): echo 'selected'; endif; ?>>مبلغ ثابت لكل win</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">القيمة</label>
                            <input type="number" step="0.01" min="0" name="sales_commission_value"
                                   value="<?php echo e(old('sales_commission_value', $rep?->sales_commission_value)); ?>"
                                   class="<?php echo e($inputClass); ?>">
                        </div>
                        <div class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3">
                            <p class="text-xs font-semibold text-slate-600">الإعداد الحالي</p>
                            <p class="text-sm font-black text-emerald-700 mt-0.5"><?php echo e($rep?->salesCommissionLabel() ?? '—'); ?></p>
                        </div>
                    </div>
                </div>
            </section>

            
            <?php $__currentLoopData = $groups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $groupTitle => $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
                    <div class="px-4 py-3 border-b border-slate-200 bg-slate-50">
                        <h3 class="text-base font-black text-slate-900 flex items-center gap-2">
                            <i class="<?php echo e($group['icon']); ?>"></i>
                            <?php echo e($groupTitle); ?>

                        </h3>
                    </div>
                    <div class="p-4 grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                        <?php $__currentLoopData = $group['keys']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="rounded-xl border border-slate-200 bg-white p-3 shadow-sm">
                                <label class="block text-sm font-semibold text-slate-700 mb-2" for="t_<?php echo e($key); ?>"><?php echo e($labels[$key] ?? $key); ?></label>
                                <input type="number" step="any" name="<?php echo e($key); ?>" id="t_<?php echo e($key); ?>"
                                       value="<?php echo e(old($key, $targets[$key] ?? '')); ?>"
                                       class="<?php echo e($inputClass); ?>">
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </section>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

            <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
                <div class="px-4 py-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <p class="text-xs text-slate-600">
                        <i class="fas fa-info-circle text-sky-600 ml-1"></i>
                        الحفظ يطبّق على <strong><?php echo e($rep?->name); ?></strong> — <?php echo e($yearMonth); ?>

                    </p>
                    <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-sm font-semibold">
                        <i class="fas fa-save"></i>
                        حفظ الأهداف
                    </button>
                </div>
            </section>
        </form>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\admin\sales\kpi\targets.blade.php ENDPATH**/ ?>