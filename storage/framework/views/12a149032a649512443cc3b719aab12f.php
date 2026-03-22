<?php $__env->startSection('title', __('instructor.withdrawal_requests') . ' - Mindlytics'); ?>
<?php $__env->startSection('header', __('instructor.withdrawal_requests')); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    
    <div class="relative rounded-2xl border border-slate-200 bg-gradient-to-br from-white via-slate-50/40 to-white shadow-sm overflow-hidden">
        <div class="absolute top-0 right-0 w-28 h-28 rounded-full bg-sky-100/50 -translate-y-1/2 translate-x-1/2 pointer-events-none" aria-hidden="true"></div>
        <div class="relative flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 p-5 sm:p-6">
            <div class="flex items-center gap-4 min-w-0 flex-1">
                <div class="w-14 h-14 rounded-2xl bg-white border border-slate-100 shadow-sm flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-wallet text-sky-600 text-2xl"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-xs font-semibold text-sky-600 uppercase tracking-wider mb-1"><?php echo e(__('instructor.instructor_panel')); ?></p>
                    <h1 class="text-xl sm:text-2xl font-bold text-slate-800 truncate"><?php echo e(__('instructor.withdrawal_requests')); ?></h1>
                    <p class="text-sm text-slate-500 mt-0.5"><?php echo e(__('instructor.finance_overview')); ?></p>
                </div>
            </div>
            <?php if($stats['available_amount'] > 0): ?>
            <a href="<?php echo e(route('instructor.withdrawals.create')); ?>"
               class="inline-flex items-center justify-center gap-2 rounded-xl bg-sky-600 hover:bg-sky-700 text-white px-5 py-2.5 text-sm font-semibold shadow-sm border border-sky-700/20 transition-colors flex-shrink-0">
                <i class="fas fa-plus text-sm"></i>
                <?php echo e(__('instructor.new_withdrawal_request')); ?>

            </a>
            <?php endif; ?>
        </div>
    </div>

    
    <div>
        <h2 class="text-sm font-bold text-slate-600 uppercase tracking-wide mb-3 flex items-center gap-2">
            <i class="fas fa-chart-pie text-sky-500 text-xs"></i>
            <?php echo e(__('instructor.finance_overview')); ?>

        </h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 sm:gap-6">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 sm:p-6 transition-shadow hover:shadow-md">
                <div class="flex items-center justify-between gap-3">
                    <div class="min-w-0 flex-1">
                        <p class="text-xs sm:text-sm font-semibold text-slate-500 uppercase tracking-wide mb-2"><?php echo e(__('instructor.total_earned')); ?></p>
                        <p class="text-2xl sm:text-3xl font-bold text-slate-800 tabular-nums leading-tight"><?php echo e(number_format($stats['total_earned'], 2)); ?></p>
                        <p class="text-xs text-slate-400 mt-1"><?php echo e(__('public.currency_egp')); ?></p>
                    </div>
                    <div class="w-14 h-14 rounded-2xl bg-emerald-50 flex items-center justify-center flex-shrink-0 border border-slate-100">
                        <i class="fas fa-money-bill-wave text-emerald-600 text-xl"></i>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 sm:p-6 transition-shadow hover:shadow-md">
                <div class="flex items-center justify-between gap-3">
                    <div class="min-w-0 flex-1">
                        <p class="text-xs sm:text-sm font-semibold text-slate-500 uppercase tracking-wide mb-2"><?php echo e(__('instructor.total_withdrawn')); ?></p>
                        <p class="text-2xl sm:text-3xl font-bold text-slate-800 tabular-nums leading-tight"><?php echo e(number_format($stats['total_withdrawn'], 2)); ?></p>
                        <p class="text-xs text-slate-400 mt-1"><?php echo e(__('public.currency_egp')); ?></p>
                    </div>
                    <div class="w-14 h-14 rounded-2xl bg-sky-50 flex items-center justify-center flex-shrink-0 border border-slate-100">
                        <i class="fas fa-arrow-down text-sky-600 text-xl"></i>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 sm:p-6 transition-shadow hover:shadow-md">
                <div class="flex items-center justify-between gap-3">
                    <div class="min-w-0 flex-1">
                        <p class="text-xs sm:text-sm font-semibold text-slate-500 uppercase tracking-wide mb-2"><?php echo e(__('instructor.pending_withdrawals')); ?></p>
                        <p class="text-2xl sm:text-3xl font-bold text-slate-800 tabular-nums leading-tight"><?php echo e(number_format($stats['pending_withdrawals'], 2)); ?></p>
                        <p class="text-xs text-slate-400 mt-1"><?php echo e(__('public.currency_egp')); ?></p>
                    </div>
                    <div class="w-14 h-14 rounded-2xl bg-amber-50 flex items-center justify-center flex-shrink-0 border border-slate-100">
                        <i class="fas fa-clock text-amber-600 text-xl"></i>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 sm:p-6 transition-shadow hover:shadow-md">
                <div class="flex items-center justify-between gap-3">
                    <div class="min-w-0 flex-1">
                        <p class="text-xs sm:text-sm font-semibold text-slate-500 uppercase tracking-wide mb-2"><?php echo e(__('instructor.available_amount')); ?></p>
                        <p class="text-2xl sm:text-3xl font-bold text-slate-800 tabular-nums leading-tight"><?php echo e(number_format($stats['available_amount'], 2)); ?></p>
                        <p class="text-xs text-slate-400 mt-1"><?php echo e(__('public.currency_egp')); ?></p>
                    </div>
                    <div class="w-14 h-14 rounded-2xl bg-violet-50 flex items-center justify-center flex-shrink-0 border border-slate-100">
                        <i class="fas fa-piggy-bank text-violet-600 text-xl"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    
    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        <div class="px-5 py-4 sm:px-6 border-b border-slate-200">
            <h2 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                <span class="w-9 h-9 rounded-xl bg-sky-50 border border-slate-100 flex items-center justify-center">
                    <i class="fas fa-list-ul text-sky-600 text-sm"></i>
                </span>
                <?php echo e(__('instructor.withdrawal_requests')); ?>

            </h2>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th scope="col" class="px-4 sm:px-6 py-3.5 text-right text-xs font-bold text-slate-600 uppercase tracking-wide"><?php echo e(__('instructor.request_number')); ?></th>
                        <th scope="col" class="px-4 sm:px-6 py-3.5 text-right text-xs font-bold text-slate-600 uppercase tracking-wide"><?php echo e(__('instructor.amount')); ?></th>
                        <th scope="col" class="px-4 sm:px-6 py-3.5 text-right text-xs font-bold text-slate-600 uppercase tracking-wide"><?php echo e(__('instructor.payment_method')); ?></th>
                        <th scope="col" class="px-4 sm:px-6 py-3.5 text-right text-xs font-bold text-slate-600 uppercase tracking-wide"><?php echo e(__('common.status')); ?></th>
                        <th scope="col" class="px-4 sm:px-6 py-3.5 text-right text-xs font-bold text-slate-600 uppercase tracking-wide"><?php echo e(__('instructor.request_date')); ?></th>
                        <th scope="col" class="px-4 sm:px-6 py-3.5 text-center text-xs font-bold text-slate-600 uppercase tracking-wide"><?php echo e(__('instructor.actions')); ?></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 bg-white">
                    <?php $__empty_1 = true; $__currentLoopData = $withdrawals; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $withdrawal): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="hover:bg-slate-50/80 transition-colors">
                        <td class="px-4 sm:px-6 py-4 whitespace-nowrap">
                            <span class="font-semibold text-slate-900 font-mono text-sm"><?php echo e($withdrawal->request_number ?? '#' . $withdrawal->id); ?></span>
                        </td>
                        <td class="px-4 sm:px-6 py-4 whitespace-nowrap">
                            <span class="text-sm font-bold text-slate-900 tabular-nums"><?php echo e(number_format($withdrawal->amount, 2)); ?> <?php echo e(__('public.currency_egp')); ?></span>
                        </td>
                        <td class="px-4 sm:px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-bold border bg-sky-50 text-sky-800 border-sky-100">
                                <?php if($withdrawal->payment_method == 'bank_transfer'): ?>
                                    <i class="fas fa-university text-[10px]"></i> <?php echo e(__('instructor.bank_transfer')); ?>

                                <?php elseif($withdrawal->payment_method == 'wallet'): ?>
                                    <i class="fas fa-wallet text-[10px]"></i> <?php echo e(__('instructor.wallet')); ?>

                                <?php elseif($withdrawal->payment_method == 'cash'): ?>
                                    <i class="fas fa-money-bill text-[10px]"></i> <?php echo e(__('instructor.cash')); ?>

                                <?php else: ?>
                                    <i class="fas fa-ellipsis-h text-[10px]"></i> <?php echo e(__('instructor.other')); ?>

                                <?php endif; ?>
                            </span>
                        </td>
                        <td class="px-4 sm:px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold border
                                <?php if($withdrawal->status == 'completed'): ?> bg-emerald-50 text-emerald-800 border-emerald-100
                                <?php elseif($withdrawal->status == 'processing'): ?> bg-sky-50 text-sky-800 border-sky-100
                                <?php elseif($withdrawal->status == 'approved'): ?> bg-amber-50 text-amber-800 border-amber-100
                                <?php elseif($withdrawal->status == 'pending'): ?> bg-slate-100 text-slate-700 border-slate-200
                                <?php elseif($withdrawal->status == 'rejected'): ?> bg-rose-50 text-rose-800 border-rose-100
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
                        </td>
                        <td class="px-4 sm:px-6 py-4 whitespace-nowrap text-sm text-slate-600 tabular-nums">
                            <?php echo e($withdrawal->created_at->format('Y-m-d H:i')); ?>

                        </td>
                        <td class="px-4 sm:px-6 py-4 text-center whitespace-nowrap">
                            <div class="flex items-center justify-center gap-2">
                                <a href="<?php echo e(route('instructor.withdrawals.show', $withdrawal)); ?>"
                                   class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-sky-50 hover:bg-sky-100 text-sky-700 border border-sky-100 transition-colors"
                                   title="<?php echo e(__('common.view')); ?>">
                                    <i class="fas fa-eye text-sm"></i>
                                </a>
                                <?php if(in_array($withdrawal->status, ['pending', 'approved'])): ?>
                                <form action="<?php echo e(route('instructor.withdrawals.cancel', $withdrawal)); ?>"
                                      method="POST"
                                      onsubmit="return confirm('<?php echo e(__('instructor.confirm_cancel_withdrawal')); ?>');"
                                      class="inline">
                                    <?php echo csrf_field(); ?>
                                    <button type="submit"
                                            class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-100 transition-colors"
                                            title="<?php echo e(__('instructor.cancel')); ?>">
                                        <i class="fas fa-times text-sm"></i>
                                    </button>
                                </form>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="6" class="px-6 py-14 text-center">
                            <div class="flex flex-col items-center gap-4 max-w-sm mx-auto">
                                <div class="w-16 h-16 rounded-2xl bg-slate-100 border border-slate-200 flex items-center justify-center">
                                    <i class="fas fa-money-bill-wave text-slate-400 text-2xl"></i>
                                </div>
                                <div>
                                    <p class="font-bold text-slate-800"><?php echo e(__('instructor.no_withdrawals')); ?></p>
                                    <p class="text-sm text-slate-500 mt-1"><?php echo e(__('instructor.no_withdrawals_description')); ?></p>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if($withdrawals->hasPages()): ?>
        <div class="px-5 py-4 sm:px-6 border-t border-slate-200 bg-slate-50/80">
            <?php echo e($withdrawals->links()); ?>

        </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views/instructor/withdrawals/index.blade.php ENDPATH**/ ?>