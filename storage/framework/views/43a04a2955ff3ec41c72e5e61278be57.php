

<?php $__env->startSection('title', 'إرسال واتساب - Mindlytics'); ?>
<?php $__env->startSection('header', 'قسم الواتساب'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <?php echo $__env->make('admin.whatsapp._nav', ['active' => 'send'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <?php if(session('error')): ?>
        <div class="rounded-xl bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 text-sm"><?php echo e(session('error')); ?></div>
    <?php endif; ?>

    <section class="rounded-3xl bg-white border border-slate-200 shadow-sm p-6 max-w-2xl">
        <h3 class="text-lg font-bold text-slate-900 mb-4">إرسال رسالة واتساب</h3>
        <form method="POST" action="<?php echo e(route('admin.whatsapp.send.post')); ?>" class="space-y-4">
            <?php echo csrf_field(); ?>
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">رقم الهاتف</label>
                <input type="text" name="phone" value="<?php echo e(old('phone')); ?>" required
                       placeholder="01012345678 أو 201012345678"
                       class="w-full rounded-xl border-slate-300 focus:border-emerald-500 focus:ring-emerald-500">
                <?php $__errorArgs = ['phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-rose-600 text-xs mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">الرسالة</label>
                <textarea name="message" rows="6" required
                          class="w-full rounded-xl border-slate-300 focus:border-emerald-500 focus:ring-emerald-500"><?php echo e(old('message')); ?></textarea>
                <?php $__errorArgs = ['message'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-rose-600 text-xs mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
            <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-emerald-600 text-white font-semibold hover:bg-emerald-700">
                <i class="fab fa-whatsapp"></i>
                إرسال
            </button>
        </form>
    </section>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views/admin/whatsapp/send.blade.php ENDPATH**/ ?>