

<?php $__env->startSection('title', 'ساعات الأماكن'); ?>
<?php $__env->startSection('header', 'مراجعة ساعات الأماكن الإدارية'); ?>

<?php $__env->startSection('content'); ?>
<div class="p-4 md:p-6 space-y-6">
    <?php if(session('success')): ?><div class="rounded-lg bg-emerald-50 border border-emerald-200 p-3"><?php echo e(session('success')); ?></div><?php endif; ?>

    <form method="GET" class="flex flex-wrap gap-3 bg-white p-4 rounded-xl border">
        <select name="location_id" class="rounded-lg border-slate-300 text-sm">
            <option value="">كل الأماكن</option>
            <?php $__currentLoopData = $locations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $loc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($loc->id); ?>" <?php if(request('location_id') == $loc->id): echo 'selected'; endif; ?>><?php echo e($loc->name); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
        <select name="status" class="rounded-lg border-slate-300 text-sm">
            <option value="">كل الحالات</option>
            <option value="pending" <?php if(request('status') === 'pending'): echo 'selected'; endif; ?>>في الانتظار</option>
            <option value="approved" <?php if(request('status') === 'approved'): echo 'selected'; endif; ?>>موافق</option>
            <option value="rejected" <?php if(request('status') === 'rejected'): echo 'selected'; endif; ?>>مرفوض</option>
        </select>
        <input type="month" name="month" value="<?php echo e(request('month')); ?>" class="rounded-lg border-slate-300 text-sm">
        <button class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm">تصفية</button>
    </form>

    <div class="bg-white rounded-xl border overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="bg-slate-50"><tr>
                <th class="px-4 py-3 text-right">المكان</th>
                <th class="px-4 py-3 text-right">التاريخ</th>
                <th class="px-4 py-3 text-right">ساعات</th>
                <th class="px-4 py-3 text-right">المُسجّل</th>
                <th class="px-4 py-3 text-right">الحالة</th>
                <th class="px-4 py-3 text-right">إجراء</th>
            </tr></thead>
            <tbody class="divide-y">
                <?php $__empty_1 = true; $__currentLoopData = $logs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td class="px-4 py-3"><?php echo e($log->location?->name); ?></td>
                        <td class="px-4 py-3"><?php echo e($log->usage_date->format('Y-m-d')); ?></td>
                        <td class="px-4 py-3 font-semibold"><?php echo e(number_format((float) $log->hours, 2)); ?></td>
                        <td class="px-4 py-3"><?php echo e($log->logger?->name); ?></td>
                        <td class="px-4 py-3"><?php echo e($log->status_label); ?></td>
                        <td class="px-4 py-3">
                            <?php if($log->status === 'pending'): ?>
                                <form action="<?php echo e(route('admin.place-usage-logs.approve', $log)); ?>" method="POST" class="inline">
                                    <?php echo csrf_field(); ?>
                                    <button class="text-emerald-600 font-medium text-xs">موافقة</button>
                                </form>
                                <button type="button" onclick="document.getElementById('reject-<?php echo e($log->id); ?>').classList.toggle('hidden')" class="text-rose-600 font-medium text-xs mr-2">رفض</button>
                                <form id="reject-<?php echo e($log->id); ?>" action="<?php echo e(route('admin.place-usage-logs.reject', $log)); ?>" method="POST" class="hidden mt-2">
                                    <?php echo csrf_field(); ?>
                                    <input type="text" name="rejection_reason" required placeholder="سبب الرفض" class="text-xs border rounded px-2 py-1 w-full mb-1">
                                    <button class="text-xs bg-rose-600 text-white px-2 py-1 rounded">تأكيد الرفض</button>
                                </form>
                            <?php else: ?>
                                —
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="6" class="px-4 py-8 text-center text-slate-500">لا توجد سجلات.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php echo e($logs->links()); ?>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\admin\place-usage-logs\index.blade.php ENDPATH**/ ?>