

<?php $__env->startSection('title', 'سجل الساعات'); ?>
<?php $__env->startSection('header', 'سجل الساعات — ' . $location->name); ?>

<?php $__env->startSection('content'); ?>
<div class="p-4 md:p-6 space-y-6">
    <div class="flex flex-wrap justify-between items-center gap-3">
        <h1 class="text-xl font-bold text-slate-900">سجل الساعات</h1>
        <a href="<?php echo e(route('place.office.usage-logs.create')); ?>" class="px-4 py-2 bg-violet-600 text-white rounded-lg font-medium">تسجيل جديد</a>
    </div>

    <?php if(session('success')): ?>
        <div class="rounded-lg bg-emerald-50 border border-emerald-200 p-3 text-emerald-800"><?php echo e(session('success')); ?></div>
    <?php endif; ?>

    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
        <table class="min-w-full text-sm">
            <thead class="bg-slate-50 text-slate-600">
                <tr>
                    <th class="px-4 py-3 text-right">التاريخ</th>
                    <th class="px-4 py-3 text-right">الساعات</th>
                    <th class="px-4 py-3 text-right">الوصف</th>
                    <th class="px-4 py-3 text-right">الحالة</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php $__empty_1 = true; $__currentLoopData = $logs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td class="px-4 py-3"><?php echo e($log->usage_date->format('Y-m-d')); ?></td>
                        <td class="px-4 py-3 font-semibold"><?php echo e(number_format((float) $log->hours, 2)); ?></td>
                        <td class="px-4 py-3 text-slate-600"><?php echo e($log->description ?: '—'); ?></td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 rounded text-xs font-semibold
                                <?php if($log->status === 'approved'): ?> bg-emerald-100 text-emerald-800
                                <?php elseif($log->status === 'rejected'): ?> bg-rose-100 text-rose-800
                                <?php else: ?> bg-amber-100 text-amber-800 <?php endif; ?>">
                                <?php echo e($log->status_label); ?>

                            </span>
                            <?php if($log->rejection_reason): ?>
                                <p class="text-xs text-rose-600 mt-1"><?php echo e($log->rejection_reason); ?></p>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="4" class="px-4 py-8 text-center text-slate-500">لا توجد سجلات بعد.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php echo e($logs->links()); ?>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.place-manager', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\place-office\usage-logs\index.blade.php ENDPATH**/ ?>