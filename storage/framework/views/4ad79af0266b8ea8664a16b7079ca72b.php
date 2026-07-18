

<?php $__env->startSection('title', 'التقارير اليومية للموظفين'); ?>
<?php $__env->startSection('header', 'رقابة التقارير اليومية'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <?php if(session('success')): ?>
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 text-emerald-800 px-4 py-3 text-sm font-semibold"><?php echo e(session('success')); ?></div>
    <?php endif; ?>
    <?php if(!empty($penaltiesSynced)): ?>
        <div class="rounded-xl border border-amber-200 bg-amber-50 text-amber-900 px-4 py-3 text-sm">
            <i class="fas fa-gavel ml-1"></i>
            تم تطبيق أو تحديث <strong><?php echo e($penaltiesSynced); ?></strong> خصم تلقائي للأيام بدون تسليم.
        </div>
    <?php endif; ?>

    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-4 py-4 bg-slate-50 border-b flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-sky-500 to-sky-600 flex items-center justify-center text-white"><i class="fas fa-clipboard-check"></i></div>
                <div>
                    <h2 class="text-xl font-black text-slate-900">رقابة التقارير اليومية</h2>
                    <p class="text-xs text-slate-600">متابعة التزام الموظفين — غرامة تلقائية عند عدم الإرسال</p>
                </div>
            </div>
            <a href="<?php echo e(route('admin.employee-daily-reports.settings')); ?>" class="px-4 py-2 rounded-xl border border-slate-300 text-sm font-semibold hover:bg-white">الإعدادات</a>
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 p-4">
            <div class="rounded-xl border p-4"><p class="text-xs text-slate-600">مُرسل اليوم</p><p class="text-2xl font-black"><?php echo e($stats['total_today']); ?></p></div>
            <div class="rounded-xl border border-rose-200 bg-rose-50 p-4"><p class="text-xs text-rose-700">لم يُرسلوا اليوم</p><p class="text-2xl font-black text-rose-800"><?php echo e($stats['missing_today']); ?></p></div>
            <div class="rounded-xl border border-amber-200 bg-amber-50 p-4"><p class="text-xs text-amber-800">بخصم تلقائي</p><p class="text-2xl font-black text-amber-900"><?php echo e($stats['with_penalty'] ?? 0); ?></p></div>
            <div class="rounded-xl border p-4"><p class="text-xs text-slate-600">موظفون نشطون</p><p class="text-2xl font-black"><?php echo e($stats['employees']); ?></p></div>
        </div>
    </section>

    <?php if(count($missingToday) > 0): ?>
    <section class="rounded-xl border border-rose-200 bg-rose-50 p-4">
        <p class="font-bold text-rose-900 mb-2"><i class="fas fa-exclamation-triangle ml-1"></i> لم يُرسلوا تقرير اليوم:</p>
        <div class="flex flex-wrap gap-2">
            <?php $__currentLoopData = $missingToday; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $emp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <span class="px-3 py-1 rounded-lg bg-white border border-rose-200 text-sm font-semibold text-rose-800"><?php echo e($emp->name); ?></span>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </section>
    <?php endif; ?>

    <section class="rounded-2xl bg-white border shadow-lg overflow-hidden">
        <div class="px-4 py-3 border-b bg-slate-50 font-bold">نسب الالتزام — الشهر الحالي</div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50"><tr>
                    <th class="text-right px-4 py-2">الموظف</th>
                    <th class="text-center px-4 py-2">مطلوب</th>
                    <th class="text-center px-4 py-2">مُرسل</th>
                    <th class="text-center px-4 py-2">%</th>
                </tr></thead>
                <tbody class="divide-y">
                    <?php $__currentLoopData = $complianceRows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td class="px-4 py-2 font-semibold"><?php echo e($row['employee']->name); ?></td>
                        <td class="px-4 py-2 text-center"><?php echo e($row['required']); ?></td>
                        <td class="px-4 py-2 text-center"><?php echo e($row['submitted']); ?></td>
                        <td class="px-4 py-2 text-center font-bold <?php echo e(($row['rate'] ?? 100) < 80 ? 'text-rose-600' : 'text-emerald-600'); ?>"><?php echo e($row['rate'] !== null ? $row['rate'].'%' : '—'); ?></td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
    </section>

    <section class="rounded-2xl bg-white border shadow-lg overflow-hidden">
        <div class="px-4 py-3 border-b bg-slate-50 font-bold">التقارير</div>
        <table class="w-full text-sm">
            <thead class="bg-slate-50"><tr>
                <th class="text-right px-4 py-2">الموظف</th>
                <th class="text-right px-4 py-2">التاريخ</th>
                <th class="text-right px-4 py-2">الحالة</th>
                <th class="text-right px-4 py-2"></th>
            </tr></thead>
            <tbody class="divide-y">
                <?php $__currentLoopData = $reports; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td class="px-4 py-2"><?php echo e($r->user->name ?? '—'); ?></td>
                    <td class="px-4 py-2"><?php echo e($r->report_date->format('Y-m-d')); ?></td>
                    <td class="px-4 py-2"><?php echo e($r->isSubmitted() ? 'مُرسل' : 'مسودة'); ?></td>
                    <td class="px-4 py-2"><a href="<?php echo e(route('admin.employee-daily-reports.show', $r->id)); ?>" class="text-sky-600 font-semibold">عرض</a></td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
        <div class="p-4"><?php echo e($reports->links()); ?></div>
    </section>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\admin\employee-daily-reports\index.blade.php ENDPATH**/ ?>