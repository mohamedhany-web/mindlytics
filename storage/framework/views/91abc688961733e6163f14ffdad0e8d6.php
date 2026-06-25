<?php $__env->startSection('title', 'ساعات الأماكن'); ?>
<?php $__env->startSection('header', 'مراجعة ساعات ومصاريف الأماكن'); ?>

<?php $__env->startSection('content'); ?>
<div class="p-4 md:p-6 space-y-6 max-w-7xl mx-auto">
    <?php if(session('success')): ?><div class="rounded-lg bg-emerald-50 border border-emerald-200 p-3 text-sm"><?php echo e(session('success')); ?></div><?php endif; ?>
    <?php if(session('error')): ?><div class="rounded-lg bg-rose-50 border border-rose-200 p-3 text-sm"><?php echo e(session('error')); ?></div><?php endif; ?>

    <form method="GET" class="flex flex-wrap gap-2 bg-white p-3 rounded-xl border text-sm">
        <select name="location_id" class="rounded-lg border-slate-300 text-sm min-w-[140px]">
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
        <button class="px-3 py-1.5 bg-blue-600 text-white rounded-lg text-sm">تصفية</button>
    </form>

    <div class="bg-white rounded-xl border overflow-hidden">
        <div class="px-4 py-2 bg-slate-50 border-b font-bold text-sm">ساعات الاستخدام</div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50 text-slate-600">
                    <tr>
                        <th class="px-3 py-2 text-right">المكان</th>
                        <th class="px-3 py-2 text-right">التاريخ</th>
                        <th class="px-3 py-2 text-right">النوع</th>
                        <th class="px-3 py-2 text-right">الكورس</th>
                        <th class="px-3 py-2 text-right">ساعات</th>
                        <th class="px-3 py-2 text-right">المُسجّل</th>
                        <th class="px-3 py-2 text-right">الحالة</th>
                        <th class="px-3 py-2 text-right">إجراء</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    <?php $__empty_1 = true; $__currentLoopData = $logs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td class="px-3 py-2"><?php echo e($log->location?->name); ?></td>
                            <td class="px-3 py-2"><?php echo e($log->usage_date->format('Y-m-d')); ?></td>
                            <td class="px-3 py-2 text-xs"><?php echo e($log->usage_type_label); ?></td>
                            <td class="px-3 py-2 text-xs text-slate-600"><?php echo e($log->offlineCourse?->title ?? '—'); ?></td>
                            <td class="px-3 py-2 font-semibold"><?php echo e(number_format((float) $log->hours, 2)); ?></td>
                            <td class="px-3 py-2 text-xs"><?php echo e($log->logger?->name); ?></td>
                            <td class="px-3 py-2 text-xs"><?php echo e($log->status_label); ?></td>
                            <td class="px-3 py-2 whitespace-nowrap">
                                <?php if($log->status === 'pending'): ?>
                                    <form action="<?php echo e(route('admin.place-usage-logs.approve', $log)); ?>" method="POST" class="inline">
                                        <?php echo csrf_field(); ?>
                                        <button class="text-emerald-600 font-medium text-xs">موافقة</button>
                                    </form>
                                    <button type="button" onclick="document.getElementById('reject-log-<?php echo e($log->id); ?>').classList.toggle('hidden')" class="text-rose-600 font-medium text-xs mr-2">رفض</button>
                                    <form id="reject-log-<?php echo e($log->id); ?>" action="<?php echo e(route('admin.place-usage-logs.reject', $log)); ?>" method="POST" class="hidden mt-1">
                                        <?php echo csrf_field(); ?>
                                        <input type="text" name="rejection_reason" required placeholder="سبب الرفض" class="text-xs border rounded px-2 py-1 w-40 mb-1">
                                        <button class="text-xs bg-rose-600 text-white px-2 py-0.5 rounded">تأكيد</button>
                                    </form>
                                <?php else: ?> — <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr><td colspan="8" class="px-4 py-6 text-center text-slate-500">لا توجد سجلات ساعات.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php echo e($logs->links()); ?>

    </div>

    <div class="bg-white rounded-xl border overflow-hidden">
        <div class="px-4 py-2 bg-violet-50 border-b font-bold text-sm text-violet-900">مصاريف يومية (أكل، مشروبات…)</div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50 text-slate-600">
                    <tr>
                        <th class="px-3 py-2 text-right">المكان</th>
                        <th class="px-3 py-2 text-right">التاريخ</th>
                        <th class="px-3 py-2 text-right">البيان</th>
                        <th class="px-3 py-2 text-right">الفئة</th>
                        <th class="px-3 py-2 text-right">المبلغ</th>
                        <th class="px-3 py-2 text-right">المُسجّل</th>
                        <th class="px-3 py-2 text-right">الحالة</th>
                        <th class="px-3 py-2 text-right">إجراء</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    <?php $__empty_1 = true; $__currentLoopData = $dailyExpenses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $expense): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td class="px-3 py-2"><?php echo e($expense->location?->name); ?></td>
                            <td class="px-3 py-2"><?php echo e($expense->expense_date->format('Y-m-d')); ?></td>
                            <td class="px-3 py-2"><?php echo e($expense->title); ?></td>
                            <td class="px-3 py-2 text-xs"><?php echo e($expense->category_label); ?></td>
                            <td class="px-3 py-2 font-semibold tabular-nums"><?php echo e(number_format($expense->lineTotal(), 2)); ?></td>
                            <td class="px-3 py-2 text-xs"><?php echo e($expense->logger?->name); ?></td>
                            <td class="px-3 py-2 text-xs"><?php echo e($expense->status_label); ?></td>
                            <td class="px-3 py-2 whitespace-nowrap">
                                <?php if($expense->status === 'pending'): ?>
                                    <form action="<?php echo e(route('admin.place-daily-expenses.approve', $expense)); ?>" method="POST" class="inline">
                                        <?php echo csrf_field(); ?>
                                        <button class="text-emerald-600 font-medium text-xs">موافقة</button>
                                    </form>
                                    <button type="button" onclick="document.getElementById('reject-exp-<?php echo e($expense->id); ?>').classList.toggle('hidden')" class="text-rose-600 font-medium text-xs mr-2">رفض</button>
                                    <form id="reject-exp-<?php echo e($expense->id); ?>" action="<?php echo e(route('admin.place-daily-expenses.reject', $expense)); ?>" method="POST" class="hidden mt-1">
                                        <?php echo csrf_field(); ?>
                                        <input type="text" name="rejection_reason" required placeholder="سبب الرفض" class="text-xs border rounded px-2 py-1 w-40 mb-1">
                                        <button class="text-xs bg-rose-600 text-white px-2 py-0.5 rounded">تأكيد</button>
                                    </form>
                                <?php else: ?> — <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr><td colspan="8" class="px-4 py-6 text-center text-slate-500">لا توجد مصاريف يومية.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php echo e($dailyExpenses->links()); ?>

    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/cityphone/Documents/all Mindlytics Project/Mindlytics/resources/views/admin/place-usage-logs/index.blade.php ENDPATH**/ ?>