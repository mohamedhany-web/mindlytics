

<?php $__env->startSection('title', __('instructor.request_number') . ' ' . ($withdrawal->request_number ?? '#' . $withdrawal->id) . ' - Mindlytics'); ?>
<?php $__env->startSection('header', __('instructor.withdrawal_requests')); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6 max-w-3xl mx-auto w-full">
    <a href="<?php echo e(route('instructor.withdrawals.index')); ?>"
       class="inline-flex items-center gap-2 text-sm font-semibold text-slate-600 hover:text-sky-700 transition-colors">
        <i class="fas fa-arrow-right text-xs"></i>
        <?php echo e(__('instructor.withdrawal_requests')); ?>

    </a>

    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        <div class="px-5 py-5 sm:px-6 border-b border-slate-200 bg-slate-50/80">
            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                <div class="min-w-0">
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1"><?php echo e(__('instructor.request_number')); ?></p>
                    <h1 class="text-xl sm:text-2xl font-bold text-slate-900 font-mono"><?php echo e($withdrawal->request_number ?? '#' . $withdrawal->id); ?></h1>
                    <p class="text-sm text-slate-500 mt-2 tabular-nums">
                        <i class="fas fa-calendar-alt text-slate-400 text-xs ml-1"></i>
                        <?php echo e($withdrawal->created_at->format('Y-m-d H:i')); ?>

                    </p>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold border
                        <?php if($withdrawal->status == 'completed'): ?> bg-emerald-50 text-emerald-800 border-emerald-100
                        <?php elseif($withdrawal->status == 'processing'): ?> bg-sky-50 text-sky-800 border-sky-100
                        <?php elseif($withdrawal->status == 'approved'): ?> bg-amber-50 text-amber-800 border-amber-100
                        <?php elseif($withdrawal->status == 'pending'): ?> bg-slate-100 text-slate-700 border-slate-200
                        <?php elseif($withdrawal->status == 'rejected'): ?> bg-rose-50 text-rose-800 border-rose-100
                        <?php elseif($withdrawal->status == 'cancelled'): ?> bg-slate-50 text-slate-600 border-slate-200
                        <?php else: ?> bg-slate-50 text-slate-700 border-slate-100
                        <?php endif; ?>">
                        <?php if($withdrawal->status == 'completed'): ?> <?php echo e(__('instructor.completed')); ?>

                        <?php elseif($withdrawal->status == 'processing'): ?> <?php echo e(__('instructor.processing')); ?>

                        <?php elseif($withdrawal->status == 'approved'): ?> <?php echo e(__('instructor.approved')); ?>

                        <?php elseif($withdrawal->status == 'pending'): ?> <?php echo e(__('instructor.pending_status')); ?>

                        <?php elseif($withdrawal->status == 'rejected'): ?> <?php echo e(__('instructor.rejected')); ?>

                        <?php elseif($withdrawal->status == 'cancelled'): ?> <?php echo e(__('instructor.cancelled')); ?>

                        <?php else: ?> <?php echo e($withdrawal->status); ?>

                        <?php endif; ?>
                    </span>
                </div>
            </div>
        </div>

        <div class="px-5 py-6 sm:px-6 space-y-6">
            <div class="rounded-xl border border-slate-100 bg-slate-50/50 p-4 sm:p-5">
                <p class="text-xs font-bold text-slate-500 uppercase tracking-wide mb-2"><?php echo e(__('instructor.amount')); ?></p>
                <p class="text-2xl sm:text-3xl font-bold text-slate-900 tabular-nums"><?php echo e(number_format($withdrawal->amount, 2)); ?> <?php echo e(__('public.currency_egp')); ?></p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="rounded-xl border border-slate-100 bg-white p-4">
                    <p class="text-xs font-semibold text-slate-500 mb-1"><?php echo e(__('instructor.payment_method')); ?></p>
                    <p class="text-sm font-bold text-slate-900">
                        <?php if($withdrawal->payment_method == 'bank_transfer'): ?>
                            <span class="inline-flex items-center gap-1.5"><i class="fas fa-university text-sky-600"></i> <?php echo e(__('instructor.bank_transfer')); ?></span>
                        <?php elseif($withdrawal->payment_method == 'wallet'): ?>
                            <span class="inline-flex items-center gap-1.5"><i class="fas fa-wallet text-sky-600"></i> <?php echo e(__('instructor.wallet')); ?></span>
                        <?php elseif($withdrawal->payment_method == 'cash'): ?>
                            <span class="inline-flex items-center gap-1.5"><i class="fas fa-money-bill text-sky-600"></i> <?php echo e(__('instructor.cash')); ?></span>
                        <?php else: ?>
                            <span class="inline-flex items-center gap-1.5"><i class="fas fa-ellipsis-h text-sky-600"></i> <?php echo e(__('instructor.other')); ?></span>
                        <?php endif; ?>
                    </p>
                </div>
                <?php if($withdrawal->processed_at): ?>
                <div class="rounded-xl border border-slate-100 bg-white p-4">
                    <p class="text-xs font-semibold text-slate-500 mb-1"><?php echo e(__('instructor.processed_at') ?? 'تاريخ المعالجة'); ?></p>
                    <p class="text-sm font-bold text-slate-900 tabular-nums"><?php echo e($withdrawal->processed_at->format('Y-m-d H:i')); ?></p>
                </div>
                <?php endif; ?>
            </div>

            <?php if($withdrawal->payment_method === 'bank_transfer' && ($withdrawal->bank_name || $withdrawal->account_holder_name || $withdrawal->account_number || $withdrawal->iban)): ?>
            <div class="rounded-xl border border-sky-100 bg-sky-50/40 p-4 sm:p-5 space-y-3">
                <h2 class="text-sm font-bold text-slate-800 flex items-center gap-2">
                    <i class="fas fa-university text-sky-600"></i>
                    <?php echo e(__('instructor.bank_transfer')); ?>

                </h2>
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
                    <?php if($withdrawal->bank_name): ?>
                    <div>
                        <dt class="text-xs font-semibold text-slate-500"><?php echo e(__('instructor.bank_name')); ?></dt>
                        <dd class="font-medium text-slate-900 mt-0.5"><?php echo e($withdrawal->bank_name); ?></dd>
                    </div>
                    <?php endif; ?>
                    <?php if($withdrawal->account_holder_name): ?>
                    <div>
                        <dt class="text-xs font-semibold text-slate-500"><?php echo e(__('instructor.account_holder_name')); ?></dt>
                        <dd class="font-medium text-slate-900 mt-0.5"><?php echo e($withdrawal->account_holder_name); ?></dd>
                    </div>
                    <?php endif; ?>
                    <?php if($withdrawal->account_number): ?>
                    <div>
                        <dt class="text-xs font-semibold text-slate-500"><?php echo e(__('instructor.account_number')); ?></dt>
                        <dd class="font-medium text-slate-900 mt-0.5 font-mono" dir="ltr"><?php echo e($withdrawal->account_number); ?></dd>
                    </div>
                    <?php endif; ?>
                    <?php if($withdrawal->iban): ?>
                    <div>
                        <dt class="text-xs font-semibold text-slate-500"><?php echo e(__('instructor.iban')); ?></dt>
                        <dd class="font-medium text-slate-900 mt-0.5 font-mono" dir="ltr"><?php echo e($withdrawal->iban); ?></dd>
                    </div>
                    <?php endif; ?>
                </dl>
            </div>
            <?php endif; ?>

            <?php if($withdrawal->notes): ?>
            <div>
                <p class="text-xs font-bold text-slate-500 uppercase tracking-wide mb-2"><?php echo e(__('instructor.notes')); ?></p>
                <div class="rounded-xl border border-slate-100 bg-slate-50/50 p-4 text-sm text-slate-800 whitespace-pre-wrap"><?php echo e($withdrawal->notes); ?></div>
            </div>
            <?php endif; ?>

            <?php if($withdrawal->admin_notes): ?>
            <div class="rounded-xl border border-amber-100 bg-amber-50/50 p-4">
                <p class="text-xs font-bold text-amber-800 uppercase tracking-wide mb-2"><?php echo e(__('instructor.admin_notes') ?? 'ملاحظات الإدارة'); ?></p>
                <p class="text-sm text-slate-800 whitespace-pre-wrap"><?php echo e($withdrawal->admin_notes); ?></p>
            </div>
            <?php endif; ?>
        </div>

        <?php if(in_array($withdrawal->status, ['pending', 'approved'])): ?>
        <div class="px-5 py-4 sm:px-6 border-t border-slate-200 bg-slate-50/80 flex flex-wrap gap-3">
            <form action="<?php echo e(route('instructor.withdrawals.cancel', $withdrawal)); ?>" method="POST"
                  onsubmit="return confirm('<?php echo e(__('instructor.confirm_cancel_withdrawal')); ?>');">
                <?php echo csrf_field(); ?>
                <button type="submit"
                        class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl border border-rose-200 bg-rose-50 text-rose-800 font-semibold text-sm hover:bg-rose-100 transition-colors">
                    <i class="fas fa-times text-sm"></i>
                    <?php echo e(__('instructor.cancel')); ?>

                </button>
            </form>
        </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views/instructor/withdrawals/show.blade.php ENDPATH**/ ?>