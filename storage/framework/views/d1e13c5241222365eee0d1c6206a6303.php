

<?php $__env->startSection('title', 'تقارير المبيعات الشاملة'); ?>
<?php $__env->startSection('header', 'تقارير المبيعات الشاملة'); ?>

<?php $__env->startSection('content'); ?>
<div class="p-4 md:p-6 space-y-6" style="background:#f8fafc;min-height:100vh;">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div class="space-y-1">
            <p class="text-sm text-gray-600 max-w-2xl">تقرير واحد يجمع: مؤشرات الأداء (KPIs)، العملاء المحتملون ذوو الصلة بالفترة، أنشطة CRM، وسجل أنشطة المبيعات في النظام. اختر فترة زمنية واختيارياً موظفاً لتضييق النطاق، ثم صدّر إلى Excel بتنسيق جاهز للطباعة.</p>
            <a href="<?php echo e(route('admin.sales.leads.index')); ?>" class="text-sm text-emerald-700 font-medium hover:underline">← العملاء المحتملون</a>
        </div>
    </div>

    <?php if($error): ?>
        <div class="rounded-xl border border-rose-200 bg-rose-50 text-rose-800 px-4 py-3 text-sm font-semibold"><?php echo e($error); ?></div>
    <?php endif; ?>

    <div class="rounded-2xl border border-gray-200 bg-white shadow-sm p-5 md:p-6">
        <form method="get" action="<?php echo e(route('admin.sales.reports.index')); ?>" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 items-end">
            <div>
                <label class="block text-xs font-bold text-gray-600 mb-1">من تاريخ</label>
                <input type="date" name="date_from" value="<?php echo e($dateFrom); ?>" class="w-full rounded-xl border border-gray-300 px-3 py-2 text-sm" required>
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-600 mb-1">إلى تاريخ</label>
                <input type="date" name="date_to" value="<?php echo e($dateTo); ?>" class="w-full rounded-xl border border-gray-300 px-3 py-2 text-sm" required>
            </div>
            <div class="md:col-span-2 lg:col-span-2">
                <label class="block text-xs font-bold text-gray-600 mb-1">الموظف (اختياري — اتركه فارغاً لجميع موظفي المبيعات)</label>
                <select name="user_id" class="w-full rounded-xl border border-gray-300 px-3 py-2 text-sm">
                    <option value="">— كل الفريق —</option>
                    <?php $__currentLoopData = $salesReps; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($r->id); ?>" <?php if((string)$userId === (string)$r->id): echo 'selected'; endif; ?>><?php echo e($r->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div class="flex flex-wrap gap-2 lg:col-span-4">
                <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-sm font-bold shadow-md">
                    <i class="fas fa-sync-alt"></i> تحديث المعاينة
                </button>
                <a href="<?php echo e(route('admin.sales.reports.export', request()->query())); ?>" class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-l from-emerald-600 to-teal-600 text-white rounded-xl text-sm font-bold shadow-lg border border-emerald-400/40">
                    <i class="fas fa-file-excel"></i> تصدير Excel كامل
                </a>
            </div>
        </form>
    </div>

    <?php if($start && $end && !$error): ?>
        <div class="rounded-2xl border border-emerald-200 bg-gradient-to-br from-emerald-50 to-white p-5 shadow-sm">
            <p class="text-sm text-emerald-900 font-bold mb-2">الفترة: <?php echo e($start->format('Y-m-d')); ?> — <?php echo e($end->format('Y-m-d')); ?> (<?php echo e(max(1, (int) $start->diffInDays($end) + 1)); ?> يوماً)</p>
            <?php if($selectedRep && $periodReport): ?>
                <p class="text-sm text-gray-700 mb-3">الموظف: <span class="font-black text-gray-900"><?php echo e($selectedRep->name); ?></span></p>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3 text-sm">
                    <div class="rounded-xl bg-white border border-emerald-100 p-3">
                        <p class="text-xs text-gray-500 font-semibold">المؤشر المركّب</p>
                        <p class="text-2xl font-black text-emerald-700 tabular-nums"><?php echo e($periodReport['composite']); ?></p>
                    </div>
                    <div class="rounded-xl bg-white border border-emerald-100 p-3">
                        <p class="text-xs text-gray-500 font-semibold">إيرادات فوز</p>
                        <p class="text-lg font-bold text-gray-900 tabular-nums"><?php echo e(number_format((float) ($periodReport['metrics']['revenue_closed'] ?? 0), 2)); ?> ج.م</p>
                    </div>
                    <div class="rounded-xl bg-white border border-emerald-100 p-3">
                        <p class="text-xs text-gray-500 font-semibold">عملاء في التقرير</p>
                        <p class="text-2xl font-black text-slate-800 tabular-nums"><?php echo e($counts['leads'] ?? 0); ?></p>
                    </div>
                    <div class="rounded-xl bg-white border border-emerald-100 p-3">
                        <p class="text-xs text-gray-500 font-semibold">أنشطة CRM</p>
                        <p class="text-2xl font-black text-slate-800 tabular-nums"><?php echo e($counts['activities'] ?? 0); ?></p>
                    </div>
                </div>
                <?php if(!empty($periodReport['alert_flags'])): ?>
                    <ul class="mt-4 text-sm text-amber-900 space-y-1 list-disc list-inside">
                        <?php $__currentLoopData = $periodReport['alert_flags']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $f): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li><?php echo e($f); ?></li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                <?php endif; ?>
            <?php else: ?>
                <p class="text-sm text-gray-700 mb-3">نطاق: <span class="font-bold">جميع موظفي المبيعات النشطين</span></p>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-3 text-sm">
                    <div class="rounded-xl bg-white border border-emerald-100 p-3">
                        <p class="text-xs text-gray-500 font-semibold">عملاء (فريق)</p>
                        <p class="text-2xl font-black text-slate-800 tabular-nums"><?php echo e($counts['leads'] ?? 0); ?></p>
                    </div>
                    <div class="rounded-xl bg-white border border-emerald-100 p-3">
                        <p class="text-xs text-gray-500 font-semibold">أنشطة CRM</p>
                        <p class="text-2xl font-black text-slate-800 tabular-nums"><?php echo e($counts['activities'] ?? 0); ?></p>
                    </div>
                    <div class="rounded-xl bg-white border border-emerald-100 p-3">
                        <p class="text-xs text-gray-500 font-semibold">سجل النظام</p>
                        <p class="text-2xl font-black text-slate-800 tabular-nums"><?php echo e($counts['audit'] ?? 0); ?></p>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <?php if($selectedRep && $periodReport): ?>
            <div class="rounded-2xl border border-gray-200 bg-white shadow-sm overflow-hidden">
                <div class="px-5 py-3 border-b border-gray-100 bg-slate-50">
                    <h2 class="text-base font-black text-gray-900">تفصيل KPIs (معاينة)</h2>
                </div>
                <div class="overflow-x-auto p-4">
                    <table class="min-w-[640px] w-full text-sm">
                        <thead>
                            <tr class="bg-emerald-800 text-white">
                                <th class="px-3 py-2 text-right">المؤشر</th>
                                <th class="px-3 py-2 text-center">الفعلي</th>
                                <th class="px-3 py-2 text-center">الهدف (فترة)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <?php $__currentLoopData = $periodReport['kpi_lines'] ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $line): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr class="hover:bg-emerald-50/50">
                                    <td class="px-3 py-2 font-medium text-gray-800"><?php echo e($line['label'] ?? ''); ?></td>
                                    <td class="px-3 py-2 text-center tabular-nums"><?php echo e($line['actual'] ?? '—'); ?></td>
                                    <td class="px-3 py-2 text-center tabular-nums"><?php echo e($line['target'] ?? '—'); ?></td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php elseif($repSummaries->isNotEmpty()): ?>
            <div class="rounded-2xl border border-gray-200 bg-white shadow-sm overflow-hidden">
                <div class="px-5 py-3 border-b border-gray-100 bg-slate-50">
                    <h2 class="text-base font-black text-gray-900">ملخص الفريق (معاينة)</h2>
                </div>
                <div class="overflow-x-auto p-4">
                    <table class="min-w-[720px] w-full text-sm">
                        <thead>
                            <tr class="bg-emerald-800 text-white">
                                <th class="px-3 py-2 text-right">الموظف</th>
                                <th class="px-3 py-2 text-center">مركّب</th>
                                <th class="px-3 py-2 text-center">إيراد فوز</th>
                                <th class="px-3 py-2 text-center">فوز</th>
                                <th class="px-3 py-2 text-center">Leads</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <?php $__currentLoopData = $repSummaries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php $m = $row['report']['metrics'] ?? []; ?>
                                <tr class="hover:bg-emerald-50/50">
                                    <td class="px-3 py-2 font-semibold text-gray-900"><?php echo e($row['user']->name); ?></td>
                                    <td class="px-3 py-2 text-center tabular-nums font-bold text-emerald-700"><?php echo e($row['report']['composite'] ?? '—'); ?></td>
                                    <td class="px-3 py-2 text-center tabular-nums"><?php echo e(number_format((float) ($m['revenue_closed'] ?? 0), 2)); ?></td>
                                    <td class="px-3 py-2 text-center tabular-nums"><?php echo e((int) ($m['won_closed'] ?? 0)); ?></td>
                                    <td class="px-3 py-2 text-center tabular-nums"><?php echo e((int) ($m['new_leads'] ?? 0)); ?></td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
            <div class="rounded-2xl border border-gray-200 bg-white shadow-sm p-4">
                <h3 class="text-sm font-black text-gray-900 mb-2">عملاء محتملون (عينة)</h3>
                <?php if($leadsSample->isEmpty()): ?>
                    <p class="text-xs text-gray-500">لا توجد صفوف في المعاينة.</p>
                <?php else: ?>
                    <ul class="text-xs space-y-2 text-gray-700">
                        <?php $__currentLoopData = $leadsSample; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $l): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li class="border-b border-gray-100 pb-2">
                                <span class="font-bold"><?php echo e($l->name); ?></span>
                                <span class="text-gray-500"> — <?php echo e(\App\Models\SalesLead::stageLabel($l->stage)); ?></span>
                            </li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                <?php endif; ?>
            </div>
            <div class="rounded-2xl border border-gray-200 bg-white shadow-sm p-4">
                <h3 class="text-sm font-black text-gray-900 mb-2">أنشطة CRM (عينة)</h3>
                <?php if($activitiesSample->isEmpty()): ?>
                    <p class="text-xs text-gray-500">لا توجد صفوف في المعاينة.</p>
                <?php else: ?>
                    <ul class="text-xs space-y-2 text-gray-700">
                        <?php $__currentLoopData = $activitiesSample; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li class="border-b border-gray-100 pb-2">
                                <span class="font-bold"><?php echo e(\App\Models\SalesActivity::typeLabel($a->type)); ?></span>
                                <?php if($a->lead): ?>
                                    <span class="text-gray-500"> — <?php echo e($a->lead->name); ?></span>
                                <?php endif; ?>
                            </li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                <?php endif; ?>
            </div>
            <div class="rounded-2xl border border-gray-200 bg-white shadow-sm p-4">
                <h3 class="text-sm font-black text-gray-900 mb-2">سجل النظام (عينة)</h3>
                <?php if($auditSample->isEmpty()): ?>
                    <p class="text-xs text-gray-500">لا توجد صفوف في المعاينة.</p>
                <?php else: ?>
                    <ul class="text-xs space-y-2 text-gray-700">
                        <?php $__currentLoopData = $auditSample; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li class="border-b border-gray-100 pb-2 line-clamp-2"><?php echo e(\Illuminate\Support\Str::limit($log->description ?? $log->action, 80)); ?></li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/cityphone/Documents/all Mindlytics Project/Mindlytics/resources/views/admin/sales/reports/index.blade.php ENDPATH**/ ?>