

<?php $__env->startSection('title', 'مدفوعات الفرع'); ?>
<?php $__env->startSection('header', 'مدفوعات الفرع — ' . $branch->name); ?>

<?php $__env->startSection('content'); ?>
<div class="p-6 lg:p-8 max-w-[1400px] mx-auto space-y-6">
    <form method="GET" class="flex flex-wrap gap-3 items-end">
        <div>
            <label class="block text-xs font-semibold text-slate-500 mb-1">الحالة</label>
            <input type="text" name="status" value="<?php echo e(request('status')); ?>" placeholder="مثال: completed"
                   class="rounded-xl border border-slate-200 px-3 py-2 text-sm w-48">
        </div>
        <button type="submit" class="rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 text-sm font-bold">تصفية</button>
    </form>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50 text-slate-600">
                    <tr>
                        <th class="text-right px-4 py-3 font-semibold">#</th>
                        <th class="text-right px-4 py-3 font-semibold">المستخدم</th>
                        <th class="text-right px-4 py-3 font-semibold">المبلغ</th>
                        <th class="text-right px-4 py-3 font-semibold">الحالة</th>
                        <th class="text-right px-4 py-3 font-semibold">تاريخ الدفع</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php $__currentLoopData = $payments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td class="px-4 py-3 text-slate-500"><?php echo e($p->id); ?></td>
                            <td class="px-4 py-3"><?php echo e($p->user->name ?? '—'); ?></td>
                            <td class="px-4 py-3 tabular-nums"><?php echo e(number_format((float) $p->amount, 2)); ?></td>
                            <td class="px-4 py-3"><?php echo e($p->status); ?></td>
                            <td class="px-4 py-3 text-slate-500"><?php echo e($p->paid_at?->format('Y-m-d H:i') ?? '—'); ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-t border-slate-100"><?php echo e($payments->withQueryString()->links()); ?></div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/cityphone/Documents/all Mindlytics Project/Mindlytics/resources/views/branch-office/payments.blade.php ENDPATH**/ ?>