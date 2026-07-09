

<?php $__env->startSection('title', 'مخالصات الأماكن'); ?>
<?php $__env->startSection('header', 'المخالصات الشهرية للأماكن'); ?>

<?php $__env->startSection('content'); ?>
<div class="p-4 md:p-6 space-y-6">
    <form method="GET" class="flex flex-wrap gap-3 bg-white p-4 rounded-xl border">
        <select name="location_id" class="rounded-lg border-slate-300 text-sm">
            <option value="">كل الأماكن</option>
            <?php $__currentLoopData = $locations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $loc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($loc->id); ?>" <?php if(request('location_id') == $loc->id): echo 'selected'; endif; ?>><?php echo e($loc->name); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
        <select name="status" class="rounded-lg border-slate-300 text-sm">
            <option value="">كل الحالات</option>
            <?php $__currentLoopData = ['open','submitted','approved','closed','paid']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $st): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($st); ?>" <?php if(request('status') === $st): echo 'selected'; endif; ?>><?php echo e($st); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
        <input type="month" name="month" value="<?php echo e(request('month')); ?>" class="rounded-lg border-slate-300 text-sm">
        <button class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm">تصفية</button>
    </form>

    <div class="bg-white rounded-xl border overflow-hidden">
        <table class="min-w-full text-sm">
            <thead class="bg-slate-50"><tr>
                <th class="px-4 py-3 text-right">المكان</th>
                <th class="px-4 py-3 text-right">الشهر</th>
                <th class="px-4 py-3 text-right">ساعات</th>
                <th class="px-4 py-3 text-right">المبلغ</th>
                <th class="px-4 py-3 text-right">الحالة</th>
                <th class="px-4 py-3 text-right"></th>
            </tr></thead>
            <tbody class="divide-y">
                <?php $__empty_1 = true; $__currentLoopData = $settlements; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td class="px-4 py-3"><?php echo e($s->location?->name); ?></td>
                        <td class="px-4 py-3"><?php echo e($s->period_month); ?></td>
                        <td class="px-4 py-3"><?php echo e(number_format((float) $s->total_hours, 2)); ?></td>
                        <td class="px-4 py-3"><?php echo e(number_format((float) $s->total_amount, 2)); ?></td>
                        <td class="px-4 py-3"><?php echo e($s->status_label); ?></td>
                        <td class="px-4 py-3"><a href="<?php echo e(route('admin.place-settlements.show', $s)); ?>" class="text-blue-600">مراجعة</a></td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="6" class="px-4 py-8 text-center text-slate-500">لا توجد مخالصات.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php echo e($settlements->links()); ?>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\admin\place-settlements\index.blade.php ENDPATH**/ ?>