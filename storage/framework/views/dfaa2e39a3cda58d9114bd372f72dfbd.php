<?php $__env->startSection('title', __('instructor.transfer_account') . ' - Mindlytics'); ?>
<?php $__env->startSection('header', __('instructor.transfer_account')); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6 max-w-4xl mx-auto w-full">
    <div class="relative rounded-2xl border border-slate-200 bg-gradient-to-br from-white via-slate-50/40 to-white shadow-sm overflow-hidden">
        <div class="absolute top-0 right-0 w-28 h-28 rounded-full bg-sky-100/50 -translate-y-1/2 translate-x-1/2 pointer-events-none" aria-hidden="true"></div>
        <div class="relative p-5 sm:p-6 flex items-start gap-4">
            <div class="w-14 h-14 rounded-2xl bg-white border border-slate-100 shadow-sm flex items-center justify-center flex-shrink-0">
                <i class="fas fa-university text-sky-600 text-2xl"></i>
            </div>
            <div class="min-w-0">
                <p class="text-xs font-semibold text-sky-600 uppercase tracking-wider mb-1"><?php echo e(__('instructor.instructor_panel')); ?></p>
                <h1 class="text-xl sm:text-2xl font-bold text-slate-800"><?php echo e(__('instructor.transfer_account')); ?></h1>
                <p class="text-sm text-slate-500 mt-1 leading-relaxed"><?php echo e(__('instructor.transfer_account_desc')); ?></p>
            </div>
        </div>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        <div class="px-5 py-4 sm:px-6 border-b border-slate-200 bg-slate-50/80">
            <h2 class="text-base font-bold text-slate-800 flex items-center gap-2">
                <span class="w-9 h-9 rounded-xl bg-sky-50 border border-slate-100 flex items-center justify-center">
                    <i class="fas fa-id-card text-sky-600 text-sm"></i>
                </span>
                <?php echo e(__('instructor.account_info')); ?>

            </h2>
            <p class="text-xs text-slate-500 mt-2 mr-11 leading-relaxed"><?php echo e(__('instructor.transfer_account_desc')); ?></p>
        </div>

        <form action="<?php echo e(route('instructor.transfer-account.store')); ?>" method="POST" class="p-5 sm:p-8 space-y-6">
            <?php echo csrf_field(); ?>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label for="bank_name" class="block text-sm font-semibold text-slate-700 mb-2"><?php echo e(__('instructor.bank_name')); ?></label>
                    <input type="text" name="bank_name" id="bank_name" value="<?php echo e(old('bank_name', $detail->bank_name)); ?>"
                           class="w-full px-4 py-3 border border-slate-200 rounded-xl text-slate-900 placeholder-slate-400 focus:ring-2 focus:ring-sky-500/30 focus:border-sky-500 transition-shadow"
                           placeholder="<?php echo e(__('instructor.placeholder_bank_example')); ?>">
                    <?php $__errorArgs = ['bank_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-600 text-xs mt-1.5"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <div>
                    <label for="account_holder_name" class="block text-sm font-semibold text-slate-700 mb-2"><?php echo e(__('instructor.account_holder_name')); ?></label>
                    <input type="text" name="account_holder_name" id="account_holder_name" value="<?php echo e(old('account_holder_name', $detail->account_holder_name)); ?>"
                           class="w-full px-4 py-3 border border-slate-200 rounded-xl text-slate-900 placeholder-slate-400 focus:ring-2 focus:ring-sky-500/30 focus:border-sky-500 transition-shadow"
                           placeholder="<?php echo e(__('instructor.placeholder_name_on_card')); ?>">
                    <?php $__errorArgs = ['account_holder_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-600 text-xs mt-1.5"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <div>
                    <label for="account_number" class="block text-sm font-semibold text-slate-700 mb-2"><?php echo e(__('instructor.account_number')); ?></label>
                    <input type="text" name="account_number" id="account_number" value="<?php echo e(old('account_number', $detail->account_number)); ?>" dir="ltr"
                           class="w-full px-4 py-3 border border-slate-200 rounded-xl text-slate-900 placeholder-slate-400 focus:ring-2 focus:ring-sky-500/30 focus:border-sky-500 transition-shadow"
                           placeholder="<?php echo e(__('instructor.placeholder_account_number')); ?>">
                    <?php $__errorArgs = ['account_number'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-600 text-xs mt-1.5"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <div>
                    <label for="iban" class="block text-sm font-semibold text-slate-700 mb-2"><?php echo e(__('instructor.iban')); ?></label>
                    <input type="text" name="iban" id="iban" value="<?php echo e(old('iban', $detail->iban)); ?>" dir="ltr"
                           class="w-full px-4 py-3 border border-slate-200 rounded-xl text-slate-900 placeholder-slate-400 focus:ring-2 focus:ring-sky-500/30 focus:border-sky-500 transition-shadow"
                           placeholder="EG...">
                    <?php $__errorArgs = ['iban'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-600 text-xs mt-1.5"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <div>
                    <label for="branch_name" class="block text-sm font-semibold text-slate-700 mb-2"><?php echo e(__('instructor.branch_name')); ?></label>
                    <input type="text" name="branch_name" id="branch_name" value="<?php echo e(old('branch_name', $detail->branch_name)); ?>"
                           class="w-full px-4 py-3 border border-slate-200 rounded-xl text-slate-900 placeholder-slate-400 focus:ring-2 focus:ring-sky-500/30 focus:border-sky-500 transition-shadow"
                           placeholder="<?php echo e(__('instructor.placeholder_branch_optional')); ?>">
                    <?php $__errorArgs = ['branch_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-600 text-xs mt-1.5"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <div>
                    <label for="swift_code" class="block text-sm font-semibold text-slate-700 mb-2"><?php echo e(__('instructor.swift_code')); ?></label>
                    <input type="text" name="swift_code" id="swift_code" value="<?php echo e(old('swift_code', $detail->swift_code)); ?>" dir="ltr"
                           class="w-full px-4 py-3 border border-slate-200 rounded-xl text-slate-900 placeholder-slate-400 focus:ring-2 focus:ring-sky-500/30 focus:border-sky-500 transition-shadow"
                           placeholder="<?php echo e(__('instructor.placeholder_optional')); ?>">
                    <?php $__errorArgs = ['swift_code'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-600 text-xs mt-1.5"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
            </div>
            <div>
                <label for="notes" class="block text-sm font-semibold text-slate-700 mb-2"><?php echo e(__('instructor.notes')); ?></label>
                <textarea name="notes" id="notes" rows="3"
                          class="w-full px-4 py-3 border border-slate-200 rounded-xl text-slate-900 placeholder-slate-400 focus:ring-2 focus:ring-sky-500/30 focus:border-sky-500 transition-shadow resize-y min-h-[80px]"
                          placeholder="<?php echo e(__('instructor.placeholder_extra_transfer')); ?>"><?php echo e(old('notes', $detail->notes)); ?></textarea>
                <?php $__errorArgs = ['notes'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-600 text-xs mt-1.5"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
            <div class="flex flex-wrap gap-3 pt-2 border-t border-slate-100">
                <button type="submit" class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl bg-sky-600 hover:bg-sky-700 text-white font-semibold text-sm shadow-sm border border-sky-700/20 transition-colors">
                    <i class="fas fa-save text-sm"></i>
                    <?php echo e(__('instructor.save_transfer_data')); ?>

                </button>
            </div>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/cityphone/Documents/all Mindlytics Project/Mindlytics/resources/views/instructor/transfer-account/index.blade.php ENDPATH**/ ?>