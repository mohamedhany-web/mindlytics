<?php $__env->startSection('title', 'فاتورة ' . $invoice->invoice_number); ?>
<?php $__env->startSection('header', 'فاتورة الدفع'); ?>

<?php $__env->startSection('content'); ?>
<div class="p-4 md:p-6 max-w-3xl">
    <div class="bg-white rounded-xl border p-8 shadow-sm">
        <div class="flex justify-between items-start mb-8">
            <div>
                <h1 class="text-2xl font-black text-slate-900">فاتورة دفع — مكان إداري</h1>
                <p class="text-slate-500 mt-1"><?php echo e($location->name); ?></p>
            </div>
            <div class="text-left" dir="ltr">
                <p class="font-mono font-bold"><?php echo e($invoice->invoice_number); ?></p>
                <p class="text-sm text-slate-500"><?php echo e($invoice->issued_at?->format('Y-m-d')); ?></p>
            </div>
        </div>
        <div class="mb-6 grid grid-cols-2 gap-4 text-sm">
            <div><span class="text-slate-500">فترة المخالصة</span><p class="font-semibold"><?php echo e($invoice->period_month); ?></p></div>
            <div><span class="text-slate-500">الحالة</span><p class="font-semibold"><?php echo e($invoice->status_label); ?></p></div>
        </div>
        <?php if(is_array($invoice->line_items)): ?>
            <table class="w-full text-sm border-t border-b my-6">
                <?php $__currentLoopData = $invoice->line_items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td class="py-3"><?php echo e($item['description'] ?? ''); ?></td>
                        <td class="py-3 text-left" dir="ltr"><?php echo e(number_format((float) ($item['hours'] ?? 0), 2)); ?> h × <?php echo e(number_format((float) ($item['rate'] ?? 0), 2)); ?></td>
                        <td class="py-3 text-left font-bold" dir="ltr"><?php echo e(number_format((float) ($item['amount'] ?? 0), 2)); ?> <?php echo e($invoice->currency); ?></td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </table>
        <?php endif; ?>
        <p class="text-xl font-black text-violet-700 text-left" dir="ltr">الإجمالي: <?php echo e(number_format((float) $invoice->amount, 2)); ?> <?php echo e($invoice->currency); ?></p>
        <?php if($invoice->notes): ?><p class="text-xs text-slate-500 mt-4"><?php echo e($invoice->notes); ?></p><?php endif; ?>
    </div>
    <a href="<?php echo e(route('place.office.invoices.index')); ?>" class="inline-block mt-4 text-violet-600">← العودة للفواتير</a>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.place-manager', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/cityphone/Documents/all Mindlytics Project/Mindlytics/resources/views/place-office/invoices/show.blade.php ENDPATH**/ ?>