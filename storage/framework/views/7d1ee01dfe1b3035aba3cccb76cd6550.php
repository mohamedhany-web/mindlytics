<?php $__env->startSection('title', __('instructor.submit_request_title') . ' - Mindlytics'); ?>
<?php $__env->startSection('header', __('instructor.submit_request_title')); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6 max-w-3xl mx-auto w-full">
    <div class="relative rounded-2xl border border-slate-200 bg-gradient-to-br from-white via-slate-50/40 to-white shadow-sm overflow-hidden">
        <div class="absolute top-0 right-0 w-24 h-24 rounded-full bg-sky-100/50 -translate-y-1/2 translate-x-1/2 pointer-events-none" aria-hidden="true"></div>
        <div class="relative p-5 sm:p-6 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-white border border-slate-100 shadow-sm flex items-center justify-center flex-shrink-0">
                <i class="fas fa-edit text-sky-600 text-lg"></i>
            </div>
            <div>
                <h1 class="text-lg sm:text-xl font-bold text-slate-800"><?php echo e(__('instructor.submit_new_request_title')); ?></h1>
                <p class="text-sm text-slate-500 mt-0.5"><?php echo e(__('instructor.submit_request_desc')); ?></p>
            </div>
        </div>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm p-6 sm:p-8">
        <form action="<?php echo e(route('instructor.management-requests.store')); ?>" method="POST" class="space-y-6">
            <?php echo csrf_field(); ?>

            <div>
                <label for="mgmt-subject" class="block text-sm font-semibold text-slate-700 mb-2"><?php echo e(__('instructor.request_subject_required')); ?></label>
                <input id="mgmt-subject" type="text" name="subject" value="<?php echo e(old('subject')); ?>" required
                       class="w-full rounded-xl border border-slate-200 px-4 py-3 text-slate-900 placeholder-slate-400 focus:ring-2 focus:ring-sky-500/30 focus:border-sky-500 transition-shadow"
                       placeholder="<?php echo e(__('instructor.subject_placeholder')); ?>">
                <?php $__errorArgs = ['subject'];
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
                <label for="mgmt-message" class="block text-sm font-semibold text-slate-700 mb-2"><?php echo e(__('instructor.request_details_required')); ?></label>
                <textarea id="mgmt-message" name="message" rows="6" required
                          class="w-full rounded-xl border border-slate-200 px-4 py-3 text-slate-900 placeholder-slate-400 focus:ring-2 focus:ring-sky-500/30 focus:border-sky-500 transition-shadow resize-y min-h-[140px]"
                          placeholder="<?php echo e(__('instructor.message_placeholder')); ?>"><?php echo e(old('message')); ?></textarea>
                <?php $__errorArgs = ['message'];
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

            <div class="flex flex-wrap gap-3 justify-end pt-2 border-t border-slate-100">
                <a href="<?php echo e(route('instructor.management-requests.index')); ?>"
                   class="inline-flex items-center justify-center px-5 py-2.5 rounded-xl border border-slate-200 text-slate-700 font-semibold text-sm hover:bg-slate-50 transition-colors">
                    <?php echo e(__('common.cancel')); ?>

                </a>
                <button type="submit" class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl bg-sky-600 hover:bg-sky-700 text-white font-semibold text-sm shadow-sm border border-sky-700/20 transition-colors">
                    <i class="fas fa-paper-plane text-sm"></i>
                    <?php echo e(__('instructor.send_request')); ?>

                </button>
            </div>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\instructor\management-requests\create.blade.php ENDPATH**/ ?>