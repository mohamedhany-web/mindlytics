

<?php $__env->startSection('title', __('instructor.new_withdrawal_request') . ' - Mindlytics'); ?>
<?php $__env->startSection('header', __('instructor.new_withdrawal_request')); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6 max-w-4xl mx-auto w-full">
    <div class="relative rounded-2xl border border-slate-200 bg-gradient-to-br from-white via-slate-50/40 to-white shadow-sm overflow-hidden">
        <div class="absolute top-0 right-0 w-28 h-28 rounded-full bg-sky-100/50 -translate-y-1/2 translate-x-1/2 pointer-events-none" aria-hidden="true"></div>
        <div class="relative flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 p-5 sm:p-6">
            <div class="flex items-center gap-4 min-w-0">
                <div class="w-14 h-14 rounded-2xl bg-white border border-slate-100 shadow-sm flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-plus-circle text-sky-600 text-2xl"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-xs font-semibold text-sky-600 uppercase tracking-wider mb-1"><?php echo e(__('instructor.instructor_panel')); ?></p>
                    <h1 class="text-xl sm:text-2xl font-bold text-slate-800"><?php echo e(__('instructor.new_withdrawal_request')); ?></h1>
                    <p class="text-sm text-slate-500 mt-0.5">
                        <?php echo e(__('instructor.available_amount')); ?>:
                        <span class="font-bold text-slate-800 tabular-nums"><?php echo e(number_format($stats['available_amount'], 2)); ?> <?php echo e(__('public.currency_egp')); ?></span>
                    </p>
                </div>
            </div>
            <a href="<?php echo e(route('instructor.withdrawals.index')); ?>"
               class="inline-flex items-center justify-center gap-2 text-sm font-semibold text-slate-600 hover:text-sky-700 transition-colors flex-shrink-0">
                <i class="fas fa-arrow-right text-xs"></i>
                <?php echo e(__('instructor.withdrawal_requests')); ?>

            </a>
        </div>
    </div>

    <div>
        <h2 class="text-sm font-bold text-slate-600 uppercase tracking-wide mb-3 flex items-center gap-2">
            <i class="fas fa-chart-pie text-sky-500 text-xs"></i>
            <?php echo e(__('instructor.finance_overview')); ?>

        </h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 transition-shadow hover:shadow-md">
                <div class="flex items-center justify-between gap-2">
                    <div class="min-w-0">
                        <p class="text-xs font-semibold text-slate-500 mb-1"><?php echo e(__('instructor.total_earned')); ?></p>
                        <p class="text-lg font-bold text-slate-800 tabular-nums"><?php echo e(number_format($stats['total_earned'], 2)); ?></p>
                    </div>
                    <div class="w-11 h-11 rounded-xl bg-emerald-50 border border-slate-100 flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-money-bill-wave text-emerald-600 text-sm"></i>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 transition-shadow hover:shadow-md">
                <div class="flex items-center justify-between gap-2">
                    <div class="min-w-0">
                        <p class="text-xs font-semibold text-slate-500 mb-1"><?php echo e(__('instructor.total_withdrawn')); ?></p>
                        <p class="text-lg font-bold text-slate-800 tabular-nums"><?php echo e(number_format($stats['total_withdrawn'], 2)); ?></p>
                    </div>
                    <div class="w-11 h-11 rounded-xl bg-sky-50 border border-slate-100 flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-arrow-down text-sky-600 text-sm"></i>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 transition-shadow hover:shadow-md">
                <div class="flex items-center justify-between gap-2">
                    <div class="min-w-0">
                        <p class="text-xs font-semibold text-slate-500 mb-1"><?php echo e(__('instructor.pending_withdrawals')); ?></p>
                        <p class="text-lg font-bold text-slate-800 tabular-nums"><?php echo e(number_format($stats['pending_withdrawals'], 2)); ?></p>
                    </div>
                    <div class="w-11 h-11 rounded-xl bg-amber-50 border border-slate-100 flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-clock text-amber-600 text-sm"></i>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 transition-shadow hover:shadow-md">
                <div class="flex items-center justify-between gap-2">
                    <div class="min-w-0">
                        <p class="text-xs font-semibold text-slate-500 mb-1"><?php echo e(__('instructor.available_amount')); ?></p>
                        <p class="text-lg font-bold text-slate-800 tabular-nums"><?php echo e(number_format($stats['available_amount'], 2)); ?></p>
                    </div>
                    <div class="w-11 h-11 rounded-xl bg-violet-50 border border-slate-100 flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-piggy-bank text-violet-600 text-sm"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        <div class="px-5 py-4 sm:px-6 border-b border-slate-200 bg-slate-50/80">
            <h2 class="text-base font-bold text-slate-800 flex items-center gap-2">
                <span class="w-9 h-9 rounded-xl bg-sky-50 border border-slate-100 flex items-center justify-center">
                    <i class="fas fa-file-invoice-dollar text-sky-600 text-sm"></i>
                </span>
                <?php echo e(__('instructor.new_withdrawal_request')); ?>

            </h2>
        </div>

        <?php if($stats['available_amount'] <= 0): ?>
            <div class="p-10 sm:p-12 text-center">
                <div class="w-16 h-16 rounded-2xl bg-slate-100 border border-slate-200 flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-wallet text-slate-400 text-2xl"></i>
                </div>
                <p class="font-bold text-slate-800"><?php echo e(__('instructor.withdrawal_no_balance')); ?></p>
                <p class="text-sm text-slate-500 mt-2 max-w-md mx-auto"><?php echo e(__('instructor.withdrawal_no_balance_hint')); ?></p>
                <a href="<?php echo e(route('instructor.withdrawals.index')); ?>" class="inline-flex items-center gap-2 mt-6 px-5 py-2.5 rounded-xl border border-slate-200 text-slate-700 font-semibold text-sm hover:bg-slate-50 transition-colors">
                    <i class="fas fa-arrow-right text-xs"></i>
                    <?php echo e(__('instructor.withdrawal_requests')); ?>

                </a>
            </div>
        <?php else: ?>
            <form action="<?php echo e(route('instructor.withdrawals.store')); ?>" method="POST" class="p-5 sm:p-8 space-y-6">
                <?php echo csrf_field(); ?>

                <div>
                    <label for="amount" class="block text-sm font-semibold text-slate-700 mb-2"><?php echo e(__('instructor.amount')); ?> (<?php echo e(__('public.currency_egp')); ?>) <span class="text-red-500">*</span></label>
                    <input type="number" name="amount" id="amount" value="<?php echo e(old('amount')); ?>" min="0.01" step="0.01" max="<?php echo e($stats['available_amount']); ?>" required
                           class="w-full rounded-xl border border-slate-200 px-4 py-3 text-slate-900 focus:ring-2 focus:ring-sky-500/30 focus:border-sky-500 transition-shadow"
                           placeholder="0.00">
                    <p class="mt-1.5 text-xs text-slate-500"><?php echo e(__('instructor.available_amount')); ?>: <?php echo e(number_format($stats['available_amount'], 2)); ?> <?php echo e(__('public.currency_egp')); ?></p>
                    <?php $__errorArgs = ['amount'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="mt-1.5 text-sm text-red-600"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div>
                    <label for="payment_method" class="block text-sm font-semibold text-slate-700 mb-2"><?php echo e(__('instructor.payment_method')); ?> <span class="text-red-500">*</span></label>
                    <select name="payment_method" id="payment_method" required
                            class="w-full rounded-xl border border-slate-200 px-4 py-3 text-slate-900 focus:ring-2 focus:ring-sky-500/30 focus:border-sky-500 transition-shadow">
                        <option value=""><?php echo e(__('instructor.select_payment_method')); ?></option>
                        <option value="bank_transfer" <?php echo e(old('payment_method') == 'bank_transfer' ? 'selected' : ''); ?>><?php echo e(__('instructor.bank_transfer')); ?></option>
                        <option value="wallet" <?php echo e(old('payment_method') == 'wallet' ? 'selected' : ''); ?>><?php echo e(__('instructor.wallet')); ?></option>
                        <option value="cash" <?php echo e(old('payment_method') == 'cash' ? 'selected' : ''); ?>><?php echo e(__('instructor.cash')); ?></option>
                        <option value="other" <?php echo e(old('payment_method') == 'other' ? 'selected' : ''); ?>><?php echo e(__('instructor.other')); ?></option>
                    </select>
                    <?php $__errorArgs = ['payment_method'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="mt-1.5 text-sm text-red-600"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div id="bank_fields" class="space-y-4 <?php echo e(old('payment_method') != 'bank_transfer' ? 'hidden' : ''); ?>">
                    <div>
                        <label for="bank_name" class="block text-sm font-semibold text-slate-700 mb-2"><?php echo e(__('instructor.bank_name')); ?></label>
                        <input type="text" name="bank_name" id="bank_name" value="<?php echo e(old('bank_name')); ?>"
                               class="w-full rounded-xl border border-slate-200 px-4 py-3 text-slate-900 focus:ring-2 focus:ring-sky-500/30 focus:border-sky-500 transition-shadow"
                               placeholder="<?php echo e(__('instructor.placeholder_bank_example')); ?>">
                        <?php $__errorArgs = ['bank_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="mt-1.5 text-sm text-red-600"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    <div>
                        <label for="account_holder_name" class="block text-sm font-semibold text-slate-700 mb-2"><?php echo e(__('instructor.account_holder_name')); ?></label>
                        <input type="text" name="account_holder_name" id="account_holder_name" value="<?php echo e(old('account_holder_name')); ?>"
                               class="w-full rounded-xl border border-slate-200 px-4 py-3 text-slate-900 focus:ring-2 focus:ring-sky-500/30 focus:border-sky-500 transition-shadow">
                        <?php $__errorArgs = ['account_holder_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="mt-1.5 text-sm text-red-600"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    <div>
                        <label for="account_number" class="block text-sm font-semibold text-slate-700 mb-2"><?php echo e(__('instructor.account_number')); ?></label>
                        <input type="text" name="account_number" id="account_number" value="<?php echo e(old('account_number')); ?>"
                               class="w-full rounded-xl border border-slate-200 px-4 py-3 text-slate-900 focus:ring-2 focus:ring-sky-500/30 focus:border-sky-500 transition-shadow">
                        <?php $__errorArgs = ['account_number'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="mt-1.5 text-sm text-red-600"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    <div>
                        <label for="iban" class="block text-sm font-semibold text-slate-700 mb-2"><?php echo e(__('instructor.iban')); ?> (<?php echo e(__('instructor.placeholder_optional')); ?>)</label>
                        <input type="text" name="iban" id="iban" value="<?php echo e(old('iban')); ?>"
                               class="w-full rounded-xl border border-slate-200 px-4 py-3 text-slate-900 focus:ring-2 focus:ring-sky-500/30 focus:border-sky-500 transition-shadow"
                               placeholder="EG...">
                        <?php $__errorArgs = ['iban'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="mt-1.5 text-sm text-red-600"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                </div>

                <div>
                    <label for="notes" class="block text-sm font-semibold text-slate-700 mb-2"><?php echo e(__('instructor.notes')); ?></label>
                    <textarea name="notes" id="notes" rows="3"
                              class="w-full rounded-xl border border-slate-200 px-4 py-3 text-slate-900 focus:ring-2 focus:ring-sky-500/30 focus:border-sky-500 transition-shadow resize-y min-h-[100px]"
                              placeholder="<?php echo e(__('instructor.notes')); ?>"><?php echo e(old('notes')); ?></textarea>
                    <?php $__errorArgs = ['notes'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="mt-1.5 text-sm text-red-600"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div class="flex flex-wrap gap-3 justify-end pt-4 border-t border-slate-100">
                    <a href="<?php echo e(route('instructor.withdrawals.index')); ?>"
                       class="inline-flex items-center justify-center px-5 py-2.5 rounded-xl border border-slate-200 text-slate-700 font-semibold text-sm hover:bg-slate-50 transition-colors">
                        <?php echo e(__('common.cancel')); ?>

                    </a>
                    <button type="submit" class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl bg-sky-600 hover:bg-sky-700 text-white font-semibold text-sm shadow-sm border border-sky-700/20 transition-colors">
                        <i class="fas fa-paper-plane text-sm"></i>
                        <?php echo e(__('instructor.send_request')); ?>

                    </button>
                </div>
            </form>
        <?php endif; ?>
    </div>
</div>

<script>
document.getElementById('payment_method')?.addEventListener('change', function() {
    var bankFields = document.getElementById('bank_fields');
    if (bankFields) bankFields.classList.toggle('hidden', this.value !== 'bank_transfer');
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views/instructor/withdrawals/create.blade.php ENDPATH**/ ?>