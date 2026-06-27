

<?php $__env->startSection('title', 'إنشاء كود ورشة'); ?>
<?php $__env->startSection('header', 'إنشاء كود ورشة'); ?>

<?php $__env->startSection('content'); ?>
<div class="p-3 sm:p-6 space-y-6" style="background:#f8fafc;min-height:100vh;">
    <?php echo $__env->make('admin.marketing._tabs', ['active' => 'promo'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <div class="max-w-4xl mx-auto rounded-2xl bg-white border border-slate-200 shadow-sm p-6 sm:p-8">
        <h1 class="text-xl font-black text-slate-900 mb-6">كود خصم جديد مرتبط بورشة</h1>
        <form action="<?php echo e(route('admin.workshop-promo-codes.store')); ?>" method="POST" class="space-y-6">
            <?php echo $__env->make('admin.workshop-promo-codes._form', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            <div class="flex gap-3 pt-4 border-t border-slate-100">
                <button type="submit" class="px-6 py-3 rounded-xl bg-violet-600 text-white font-bold hover:bg-violet-700">حفظ الكود</button>
                <a href="<?php echo e(route('admin.workshop-promo-codes.index')); ?>" class="px-6 py-3 rounded-xl bg-slate-100 text-slate-700 font-semibold">إلغاء</a>
            </div>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\admin\workshop-promo-codes\create.blade.php ENDPATH**/ ?>