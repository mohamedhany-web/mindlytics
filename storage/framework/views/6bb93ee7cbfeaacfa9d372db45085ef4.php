

<?php $__env->startSection('title', __('auth.register')); ?>

<?php $__env->startSection('content'); ?>
<div class="w-full max-w-md">
    <div class="bg-white rounded-3xl border border-slate-200 shadow-xl shadow-slate-200/50 p-6 sm:p-8">
        <div class="text-center mb-6">
            <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-gradient-to-br from-blue-500 to-cyan-600 text-white shadow-lg mb-4">
                <i class="fas fa-user-plus text-xl"></i>
            </div>
            <h1 class="text-2xl font-black text-slate-900 mb-1"><?php echo e(__('auth.register')); ?></h1>
            <p class="text-slate-600 text-sm"><?php echo e(__('auth.register_subtitle')); ?></p>
        </div>

        <?php if($errors->any()): ?>
            <div class="mb-4 p-4 rounded-xl bg-red-50 border border-red-200 text-red-700 text-sm space-y-1">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $err): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?> <p><?php echo e($err); ?></p> <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="<?php echo e(route('community.register.post')); ?>" class="space-y-4">
            <?php echo csrf_field(); ?>
            <div>
                <label for="name" class="block text-sm font-bold text-slate-700 mb-1"><?php echo e(__('auth.full_name')); ?></label>
                <input type="text" name="name" id="name" value="<?php echo e(old('name')); ?>" required
                       class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50/50 text-slate-900 placeholder-slate-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-colors"
                       placeholder="<?php echo e(__('auth.enter_full_name')); ?>">
            </div>
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-1"><?php echo e(__('auth.phone_number')); ?></label>
                <div class="flex rounded-xl overflow-hidden border border-slate-200 bg-slate-50/50 focus-within:border-blue-500 focus-within:ring-2 focus-within:ring-blue-500/20 transition-colors">
                    <select name="country_code" required class="bg-slate-100 border-0 text-slate-900 py-3 px-3 text-sm min-w-[5rem]" dir="ltr">
                        <?php $__currentLoopData = $countries ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($c['dial_code'] ?? ''); ?>" <?php echo e(old('country_code', $defaultCountry['dial_code'] ?? '+20') === ($c['dial_code'] ?? '') ? 'selected' : ''); ?>><?php echo e($c['dial_code'] ?? ''); ?> <?php echo e($c['name_ar'] ?? $c['name'] ?? ''); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <input type="tel" name="phone" value="<?php echo e(old('phone')); ?>" required dir="ltr"
                           class="flex-1 min-w-0 py-3 px-4 bg-transparent border-0 text-slate-900 placeholder-slate-400 focus:ring-0">
                </div>
            </div>
            <div>
                <label for="email" class="block text-sm font-bold text-slate-700 mb-1"><?php echo e(__('auth.email')); ?> <span class="text-red-500">*</span></label>
                <input type="email" name="email" id="email" value="<?php echo e(old('email')); ?>" required dir="ltr"
                       class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50/50 text-slate-900 placeholder-slate-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-colors"
                       placeholder="example@email.com">
                <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-xs text-red-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
            <div>
                <label for="password" class="block text-sm font-bold text-slate-700 mb-1"><?php echo e(__('auth.password')); ?></label>
                <input type="password" name="password" id="password" required
                       class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50/50 text-slate-900 placeholder-slate-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-colors">
            </div>
            <div>
                <label for="password_confirmation" class="block text-sm font-bold text-slate-700 mb-1"><?php echo e(__('auth.password_confirmation')); ?></label>
                <input type="password" name="password_confirmation" id="password_confirmation" required
                       class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50/50 text-slate-900 placeholder-slate-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-colors">
            </div>
            <div class="flex items-start gap-2">
                <input type="checkbox" name="terms" id="terms" required class="mt-1 w-4 h-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                <label for="terms" class="text-xs text-slate-600"><?php echo e(__('auth.agree_terms')); ?> <a href="<?php echo e(url('/terms')); ?>" class="text-blue-600 hover:underline font-medium"><?php echo e(__('auth.terms_of_use')); ?></a> <?php echo e(__('auth.and')); ?> <a href="<?php echo e(url('/privacy')); ?>" class="text-blue-600 hover:underline font-medium"><?php echo e(__('auth.privacy_policy')); ?></a></label>
            </div>
            <button type="submit" class="w-full py-3.5 rounded-xl bg-gradient-to-r from-blue-600 to-cyan-600 text-white font-bold shadow-lg hover:from-blue-700 hover:to-cyan-700 transition-all focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                <?php echo e(__('auth.create_account_btn')); ?>

            </button>
        </form>

        <div class="mt-6 pt-6 border-t border-slate-200 space-y-2">
            <p class="text-center text-slate-600 text-sm">
                <?php echo e(__('auth.already_have_account')); ?>

                <a href="<?php echo e(route('community.login')); ?>" class="text-blue-600 font-bold hover:text-blue-700 hover:underline"><?php echo e(__('auth.go_to_login')); ?></a>
            </p>
            <p class="text-center text-slate-500 text-xs">
                <a href="<?php echo e(route('register')); ?>" class="hover:text-slate-700 hover:underline">إنشاء حساب من المنصة الرئيسية</a>
            </p>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('community.layouts.guest', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\community\auth\register.blade.php ENDPATH**/ ?>