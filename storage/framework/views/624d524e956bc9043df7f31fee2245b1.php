

<?php $__env->startSection('title', 'تعديل الفاتورة ' . $invoice->invoice_number); ?>
<?php $__env->startSection('header', 'تعديل الفاتورة'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-4 py-4 bg-slate-50 border-b border-slate-200 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-amber-500 to-orange-500 flex items-center justify-center text-white shadow-md">
                    <i class="fas fa-edit"></i>
                </div>
                <div>
                    <h2 class="text-xl font-black text-slate-900">تعديل <?php echo e($invoice->invoice_number); ?></h2>
                    <p class="text-xs text-slate-600"><?php echo e($invoice->user->name ?? '—'); ?> · <?php echo e(number_format((float) $invoice->total_amount, 2)); ?> ج.م</p>
                </div>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="<?php echo e(route('admin.invoices.show', $invoice)); ?>" class="inline-flex items-center gap-2 px-3 py-2 text-sm font-semibold text-slate-700 rounded-xl border border-slate-300 hover:bg-white">
                    <i class="fas fa-eye text-blue-600"></i>
                    عرض
                </a>
                <a href="<?php echo e(route('admin.invoices.index')); ?>" class="inline-flex items-center gap-2 px-3 py-2 text-sm font-semibold text-slate-700 rounded-xl border border-slate-300 hover:bg-white">
                    <i class="fas fa-list"></i>
                    القائمة
                </a>
            </div>
        </div>
        <div class="p-4 sm:p-6">
            <?php echo $__env->make('admin.invoices.partials.form', ['users' => $users, 'invoice' => $invoice], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        </div>
    </section>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/cityphone/Documents/all Mindlytics Project/Mindlytics/resources/views/admin/invoices/edit.blade.php ENDPATH**/ ?>