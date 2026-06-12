<?php $__env->startSection('title', __('student.invoices_title')); ?>
<?php $__env->startSection('header', __('student.invoices_title')); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
        <h1 class="text-2xl font-bold text-gray-900"><?php echo e(__('student.invoices_title')); ?></h1>
        <p class="text-gray-600 mt-1"><?php echo e(__('student.invoices_subtitle')); ?></p>
    </div>

    <?php if(isset($invoices) && $invoices->count() > 0): ?>
    <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-200">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase"><?php echo e(__('student.invoice_number')); ?></th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase"><?php echo e(__('student.amount_label')); ?></th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase"><?php echo e(__('common.status')); ?></th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase"><?php echo e(__('student.actions_label')); ?></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php $__currentLoopData = $invoices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $invoice): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td class="px-6 py-4"><?php echo e($invoice->invoice_number); ?></td>
                        <td class="px-6 py-4"><?php echo e(number_format($invoice->total_amount, 2)); ?> <?php echo e(__('public.currency_egp')); ?></td>
                        <td class="px-6 py-4">
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-medium
                                <?php if($invoice->status == 'paid'): ?> bg-green-100 text-green-800
                                <?php else: ?> bg-yellow-100 text-yellow-800
                                <?php endif; ?>">
                                <?php echo e($invoice->status == 'paid' ? __('student.paid_status') : __('student.pending_status_label')); ?>

                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <a href="<?php echo e(route('student.invoices.show', $invoice)); ?>" class="text-sky-600 hover:text-sky-900"><?php echo e(__('common.view')); ?></a>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4"><?php echo e($invoices->links()); ?></div>
    </div>
    <?php else: ?>
    <div class="bg-white rounded-xl shadow-lg p-12 text-center">
        <p class="text-gray-600"><?php echo e(__('student.no_invoices')); ?></p>
    </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/cityphone/Documents/all Mindlytics Project/Mindlytics/resources/views/student/invoices/index.blade.php ENDPATH**/ ?>