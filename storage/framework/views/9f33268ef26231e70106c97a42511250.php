<?php $__env->startSection('title', 'التقرير اليومي'); ?>
<?php $__env->startSection('header', 'التقرير اليومي'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <?php if(session('success')): ?>
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 text-emerald-800 px-4 py-3 text-sm font-semibold"><?php echo e(session('success')); ?></div>
    <?php endif; ?>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="rounded-xl border border-sky-200 bg-sky-50 p-4">
            <p class="text-xs font-semibold text-sky-700">تقارير هذا الشهر</p>
            <p class="text-2xl font-black text-sky-900"><?php echo e($submittedThisMonth); ?></p>
        </div>
        <div class="rounded-xl border p-4 <?php echo e($todayReport?->isSubmitted() ? 'border-emerald-200 bg-emerald-50' : 'border-amber-200 bg-amber-50'); ?>">
            <p class="text-xs font-semibold">تقرير اليوم</p>
            <p class="text-lg font-bold"><?php echo e($todayReport?->isSubmitted() ? 'مُرسل ✓' : 'لم يُرسل بعد'); ?></p>
        </div>
        <div class="flex items-center">
            <a href="<?php echo e(route('employee.daily-reports.edit')); ?>" class="w-full text-center px-4 py-3 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm">
                <i class="fas fa-pen ml-1"></i> كتابة / إرسال تقرير اليوم
            </a>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden shadow-sm">
        <div class="px-4 py-3 border-b bg-gray-50 font-bold text-gray-900">سجل التقارير</div>
        <table class="w-full text-sm">
            <thead class="bg-gray-50"><tr>
                <th class="text-right py-2 px-4">التاريخ</th>
                <th class="text-right py-2 px-4">الحالة</th>
                <th class="text-right py-2 px-4">ساعات</th>
                <th class="text-right py-2 px-4"></th>
            </tr></thead>
            <tbody class="divide-y">
                <?php $__empty_1 = true; $__currentLoopData = $reports; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td class="py-2 px-4"><?php echo e($r->report_date->format('Y-m-d')); ?></td>
                    <td class="py-2 px-4">
                        <span class="px-2 py-0.5 rounded text-xs font-semibold <?php echo e($r->isSubmitted() ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-100 text-gray-600'); ?>">
                            <?php echo e($r->isSubmitted() ? 'مُرسل' : 'مسودة'); ?>

                        </span>
                    </td>
                    <td class="py-2 px-4"><?php echo e($r->hours_worked ?? '—'); ?></td>
                    <td class="py-2 px-4"><a href="<?php echo e(route('employee.daily-reports.edit', ['date' => $r->report_date->toDateString()])); ?>" class="text-blue-600 font-semibold">فتح</a></td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr><td colspan="4" class="py-8 text-center text-gray-500">لا تقارير بعد</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.employee', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/cityphone/Documents/all Mindlytics Project/Mindlytics/resources/views/employee/daily-reports/index.blade.php ENDPATH**/ ?>