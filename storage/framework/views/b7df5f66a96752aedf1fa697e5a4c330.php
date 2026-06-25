<?php $__env->startSection('title', 'المخالصة الشهرية'); ?>
<?php $__env->startSection('header', 'المخالصة الشهرية — ' . $location->name); ?>

<?php $__env->startSection('content'); ?>
<div class="p-4 md:p-6 space-y-6">
    <h1 class="text-xl font-bold">المخالصات الشهرية</h1>
    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
        <table class="min-w-full text-sm">
            <thead class="bg-slate-50"><tr>
                <th class="px-4 py-3 text-right">الشهر</th>
                <th class="px-4 py-3 text-right">الساعات</th>
                <th class="px-4 py-3 text-right">المبلغ</th>
                <th class="px-4 py-3 text-right">الحالة</th>
                <th class="px-4 py-3 text-right"></th>
            </tr></thead>
            <tbody class="divide-y">
                <?php $__empty_1 = true; $__currentLoopData = $settlements; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td class="px-4 py-3"><?php echo e($s->period_month); ?></td>
                        <td class="px-4 py-3"><?php echo e(number_format((float) $s->total_hours, 2)); ?></td>
                        <td class="px-4 py-3"><?php echo e(number_format((float) $s->total_amount, 2)); ?> <?php echo e($s->currency); ?></td>
                        <td class="px-4 py-3"><?php echo e($s->status_label); ?></td>
                        <td class="px-4 py-3"><a href="<?php echo e(route('place.office.settlements.show', $s)); ?>" class="text-violet-600 font-medium">التفاصيل</a></td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="5" class="px-4 py-8 text-center text-slate-500">لا توجد مخالصات.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php echo e($settlements->links()); ?>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.place-manager', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/cityphone/Documents/all Mindlytics Project/Mindlytics/resources/views/place-office/settlements/index.blade.php ENDPATH**/ ?>