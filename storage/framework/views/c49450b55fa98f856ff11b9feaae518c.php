

<?php $__env->startSection('title', 'فواتير الفرع'); ?>
<?php $__env->startSection('header', 'فواتير الفرع — ' . $branch->name); ?>

<?php $__env->startSection('content'); ?>
<div class="p-6 lg:p-8 max-w-[1400px] mx-auto space-y-6">
    <form method="GET" class="flex flex-wrap gap-3 items-end">
        <div>
            <label class="block text-xs font-semibold text-slate-500 mb-1">الحالة</label>
            <input type="text" name="status" value="<?php echo e(request('status')); ?>" placeholder="مثال: pending"
                   class="rounded-xl border border-slate-200 px-3 py-2 text-sm w-48">
        </div>
        <button type="submit" class="rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 text-sm font-bold">تصفية</button>
    </form>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50 text-slate-600">
                    <tr>
                        <th class="text-right px-4 py-3 font-semibold">رقم</th>
                        <th class="text-right px-4 py-3 font-semibold">المستخدم</th>
                        <th class="text-right px-4 py-3 font-semibold">الإجمالي</th>
                        <th class="text-right px-4 py-3 font-semibold">الحالة</th>
                        <th class="text-right px-4 py-3 font-semibold">تاريخ الإنشاء</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php $__currentLoopData = $invoices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $inv): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td class="px-4 py-3 font-mono text-xs" dir="ltr"><?php echo e($inv->invoice_number); ?></td>
                            <td class="px-4 py-3"><?php echo e($inv->user->name ?? '—'); ?></td>
                            <td class="px-4 py-3 tabular-nums"><?php echo e(number_format((float) $inv->total_amount, 2)); ?></td>
                            <td class="px-4 py-3"><?php echo e($inv->status); ?></td>
                            <td class="px-4 py-3 text-slate-500"><?php echo e($inv->created_at?->format('Y-m-d')); ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-t border-slate-100"><?php echo e($invoices->withQueryString()->links()); ?></div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/cityphone/Documents/all Mindlytics Project/Mindlytics/resources/views/branch-office/invoices.blade.php ENDPATH**/ ?>