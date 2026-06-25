<?php $__env->startSection('title', 'فواتير الدفع'); ?>
<?php $__env->startSection('header', 'فواتير الدفع — ' . $location->name); ?>

<?php $__env->startSection('content'); ?>
<div class="p-4 md:p-6 space-y-6">
    <div class="bg-white rounded-xl border overflow-hidden">
        <table class="min-w-full text-sm">
            <thead class="bg-slate-50"><tr>
                <th class="px-4 py-3 text-right">رقم الفاتورة</th>
                <th class="px-4 py-3 text-right">الشهر</th>
                <th class="px-4 py-3 text-right">المبلغ</th>
                <th class="px-4 py-3 text-right">الحالة</th>
                <th class="px-4 py-3 text-right"></th>
            </tr></thead>
            <tbody class="divide-y">
                <?php $__empty_1 = true; $__currentLoopData = $invoices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $inv): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td class="px-4 py-3 font-mono" dir="ltr"><?php echo e($inv->invoice_number); ?></td>
                        <td class="px-4 py-3"><?php echo e($inv->period_month); ?></td>
                        <td class="px-4 py-3"><?php echo e(number_format((float) $inv->amount, 2)); ?> <?php echo e($inv->currency); ?></td>
                        <td class="px-4 py-3"><?php echo e($inv->status_label); ?></td>
                        <td class="px-4 py-3"><a href="<?php echo e(route('place.office.invoices.show', $inv)); ?>" class="text-violet-600">عرض</a></td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="5" class="px-4 py-8 text-center text-slate-500">لا توجد فواتير بعد.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php echo e($invoices->links()); ?>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.place-manager', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/cityphone/Documents/all Mindlytics Project/Mindlytics/resources/views/place-office/invoices/index.blade.php ENDPATH**/ ?>