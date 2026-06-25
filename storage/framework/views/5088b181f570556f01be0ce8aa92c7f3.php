

<?php $__env->startSection('title', __('auth.login')); ?>

<?php $__env->startSection('content'); ?>
<div class="w-full max-w-md">
    <div class="bg-white rounded-3xl border border-slate-200 shadow-xl shadow-slate-200/50 p-6 sm:p-8">
        <div class="text-center mb-6">
            <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-gradient-to-br from-blue-500 to-cyan-600 text-white shadow-lg mb-4">
                <i class="fas fa-sign-in-alt text-xl"></i>
            </div>
            <h1 class="text-2xl font-black text-slate-900 mb-1"><?php echo e(__('auth.login')); ?></h1>
            <p class="text-slate-600 text-sm"><?php echo e(__('auth.enter_credentials')); ?></p>
        </div>

        <?php if($errors->any()): ?>
            <div class="mb-4 p-4 rounded-xl bg-amber-50 border border-amber-200 text-amber-800 text-sm">
                <?php echo e($errors->first()); ?>

            </div>
        <?php endif; ?>

        <form method="POST" action="<?php echo e(route('community.login.post')); ?>" class="space-y-4">
            <?php echo csrf_field(); ?>
            <div>
                <label for="email" class="block text-sm font-bold text-slate-700 mb-1"><?php echo e(__('auth.email')); ?> <span class="text-red-500">*</span></label>
                <input type="email" name="email" id="email" value="<?php echo e(old('email')); ?>" required autocomplete="email" dir="ltr"
                       class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50/50 text-slate-900 placeholder-slate-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-colors"
                       placeholder="example@email.com">
            </div>
            <div>
                <label for="password" class="block text-sm font-bold text-slate-700 mb-1"><?php echo e(__('auth.password')); ?> <span class="text-red-500">*</span></label>
                <input type="password" name="password" id="password" required autocomplete="current-password"
                       class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50/50 text-slate-900 placeholder-slate-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-colors">
            </div>
            <div class="flex items-center gap-2">
                <input type="checkbox" name="remember" id="remember" class="w-4 h-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                <label for="remember" class="text-sm text-slate-600"><?php echo e(__('auth.remember')); ?></label>
            </div>
            <button type="submit" class="w-full py-3.5 rounded-xl bg-gradient-to-r from-blue-600 to-cyan-600 text-white font-bold shadow-lg hover:from-blue-700 hover:to-cyan-700 transition-all focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                <?php echo e(__('auth.login')); ?>

            </button>
        </form>

        <div class="mt-6 pt-6 border-t border-slate-200 space-y-2">
            <p class="text-center text-slate-600 text-sm">
                <?php echo e(__('auth.no_account_question')); ?>

                <a href="<?php echo e(route('community.register')); ?>" class="text-blue-600 font-bold hover:text-blue-700 hover:underline"><?php echo e(__('auth.no_account_register_now')); ?></a>
            </p>
            <p class="text-center text-slate-500 text-xs">
                <a href="<?php echo e(route('login')); ?>" class="hover:text-slate-700 hover:underline">الذهاب لصفحة تسجيل الدخول الرئيسية</a>
            </p>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('community.layouts.guest', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/cityphone/Documents/all Mindlytics Project/Mindlytics/resources/views/community/auth/login.blade.php ENDPATH**/ ?>