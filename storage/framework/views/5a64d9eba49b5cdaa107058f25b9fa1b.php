<?php $__env->startSection('title', 'تقارير الأداء'); ?>
<?php $__env->startSection('header', 'تقارير الأداء'); ?>

<?php $__env->startPush('styles'); ?>
<?php echo $__env->make('employee.sales._styles', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <?php echo $__env->make('employee.sales._hero', [
        'heroTitle' => 'تقارير الأداء',
        'heroSubtitle' => 'معاينة مؤشرات أدائك خلال فترة محددة — بدون تصدير بيانات العملاء',
        'heroIcon' => 'fa-chart-bar',
        'backUrl' => route('employee.sales.dashboard'),
    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <?php if($error): ?>
        <div class="rounded-2xl border-2 border-rose-200 bg-rose-50 text-rose-800 px-4 py-3 text-sm font-semibold"><?php echo e($error); ?></div>
    <?php endif; ?>

    <div class="dashboard-card rounded-2xl p-5 sm:p-6 shadow-xl">
        <form method="get" action="<?php echo e(route('employee.sales.reports.index')); ?>" class="relative z-10 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 items-end">
            <div>
                <label class="block text-xs font-bold text-gray-600 mb-1">من تاريخ</label>
                <input type="date" name="date_from" value="<?php echo e($dateFrom); ?>" class="w-full rounded-xl border-2 border-gray-200 px-3 py-2 text-sm focus:border-emerald-500" required>
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-600 mb-1">إلى تاريخ</label>
                <input type="date" name="date_to" value="<?php echo e($dateTo); ?>" class="w-full rounded-xl border-2 border-gray-200 px-3 py-2 text-sm focus:border-emerald-500" required>
            </div>
            <div class="md:col-span-2">
                <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-sm font-bold shadow-md">
                    <i class="fas fa-sync-alt"></i> تحديث المعاينة
                </button>
            </div>
        </form>
    </div>

    <?php if($start && $end && !$error): ?>
        <div class="dashboard-card rounded-2xl p-5 border-2 border-emerald-200/50 shadow-xl">
            <div class="relative z-10">
                <p class="text-sm text-emerald-900 font-bold mb-3">الفترة: <?php echo e($start->format('Y-m-d')); ?> — <?php echo e($end->format('Y-m-d')); ?> (<?php echo e(max(1, (int) $start->diffInDays($end) + 1)); ?> يوماً)</p>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3 text-sm">
                    <div class="rounded-xl bg-white border-2 border-emerald-100 p-3">
                        <p class="text-xs text-gray-500 font-semibold">المؤشر المركّب</p>
                        <p class="text-2xl font-black text-emerald-700 tabular-nums"><?php echo e($periodReport['composite'] ?? '—'); ?></p>
                    </div>
                    <div class="rounded-xl bg-white border-2 border-emerald-100 p-3">
                        <p class="text-xs text-gray-500 font-semibold">عملاء في التقرير</p>
                        <p class="text-2xl font-black text-slate-800 tabular-nums"><?php echo e($counts['leads'] ?? 0); ?></p>
                    </div>
                    <div class="rounded-xl bg-white border-2 border-emerald-100 p-3">
                        <p class="text-xs text-gray-500 font-semibold">أنشطة CRM</p>
                        <p class="text-2xl font-black text-slate-800 tabular-nums"><?php echo e($counts['activities'] ?? 0); ?></p>
                    </div>
                    <div class="rounded-xl bg-white border-2 border-emerald-100 p-3">
                        <p class="text-xs text-gray-500 font-semibold">Leads أنشأتها أنا</p>
                        <p class="text-2xl font-black text-slate-800 tabular-nums"><?php echo e($counts['leads_created_by_me'] ?? 0); ?></p>
                    </div>
                </div>
                <?php if(!empty($periodReport['alert_flags'])): ?>
                    <ul class="mt-4 text-sm text-amber-900 space-y-1 list-disc list-inside">
                        <?php $__currentLoopData = $periodReport['alert_flags']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $f): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><li><?php echo e($f); ?></li><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>

        <div class="panel-card shadow-xl">
            <div class="panel-card-head">
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
                        <?php $__currentLoopData = ($periodReport['kpi_lines'] ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $line): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
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

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
            <div class="panel-card p-4">
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
            <div class="panel-card p-4">
                <h3 class="text-sm font-black text-gray-900 mb-2">أنشطة CRM (عينة)</h3>
                <?php if($activitiesSample->isEmpty()): ?>
                    <p class="text-xs text-gray-500">لا توجد صفوف.</p>
                <?php else: ?>
                    <ul class="text-xs space-y-2 text-gray-700">
                        <?php $__currentLoopData = $activitiesSample; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li class="border-b border-gray-100 pb-2">
                                <span class="font-bold"><?php echo e(\App\Models\SalesActivity::typeLabel($a->type)); ?></span>
                                <?php if($a->lead): ?><span class="text-gray-500"> — <?php echo e($a->lead->name); ?></span><?php endif; ?>
                            </li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                <?php endif; ?>
            </div>
            <div class="panel-card p-4">
                <h3 class="text-sm font-black text-gray-900 mb-2">سجل النظام (عينة)</h3>
                <?php if($auditSample->isEmpty()): ?>
                    <p class="text-xs text-gray-500">لا توجد صفوف.</p>
                <?php else: ?>
                    <ul class="text-xs space-y-2 text-gray-700">
                        <?php $__currentLoopData = $auditSample; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li class="border-b border-gray-100 pb-2"><?php echo e(\Illuminate\Support\Str::limit($log->description ?? $log->action, 80)); ?></li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.employee', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views/employee/sales/reports/index.blade.php ENDPATH**/ ?>