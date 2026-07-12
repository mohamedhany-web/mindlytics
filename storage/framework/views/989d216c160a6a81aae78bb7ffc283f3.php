

<?php $__env->startSection('title', 'انتهت الجلسة'); ?>

<?php $__env->startSection('content'); ?>
<div class="min-h-[60vh] flex items-center justify-center px-4 py-12">
    <div class="max-w-lg w-full bg-white border border-slate-200 rounded-2xl shadow-xl p-6 sm:p-8">
        <div class="flex items-start gap-3">
            <div class="text-amber-600 mt-0.5">
                <i class="fas fa-triangle-exclamation text-2xl"></i>
            </div>
            <div class="min-w-0">
                <h1 class="text-xl sm:text-2xl font-black text-slate-900">انتهت الجلسة</h1>
                <p class="mt-2 text-slate-700 leading-relaxed">
                    لأسباب أمنية، انتهت صلاحية الجلسة أو فشل التحقق من الطلب (CSRF).
                    حدّث الصفحة ثم أعد المحاولة.
                </p>
                <div class="mt-5 flex flex-wrap gap-3">
                    <a href="<?php echo e(url()->previous()); ?>" class="inline-flex items-center justify-center px-5 py-2.5 rounded-xl bg-slate-900 text-white font-semibold hover:bg-slate-800 transition-colors">
                        رجوع
                    </a>
                    <a href="<?php echo e(url()->current()); ?>" class="inline-flex items-center justify-center px-5 py-2.5 rounded-xl bg-amber-50 text-amber-800 font-semibold hover:bg-amber-100 transition-colors border border-amber-200">
                        تحديث الصفحة
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\errors\419.blade.php ENDPATH**/ ?>