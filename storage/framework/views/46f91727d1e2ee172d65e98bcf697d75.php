

<?php $__env->startSection('title', 'تعديل كود ورشة'); ?>
<?php $__env->startSection('header', 'تعديل كود ورشة'); ?>

<?php $__env->startSection('content'); ?>
<div class="p-3 sm:p-6 space-y-6" style="background:#f8fafc;min-height:100vh;">
    <?php echo $__env->make('admin.marketing._tabs', ['active' => 'promo'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <div class="max-w-4xl mx-auto rounded-2xl bg-white border border-slate-200 shadow-sm p-6 sm:p-8">
        <h1 class="text-xl font-black text-slate-900 mb-2">تعديل: <span class="font-mono text-violet-700"><?php echo e($workshopPromoCode->code); ?></span></h1>
        <form action="<?php echo e(route('admin.workshop-promo-codes.update', $workshopPromoCode)); ?>" method="POST" class="space-y-6">
            <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
            <?php echo $__env->make('admin.workshop-promo-codes._form', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            <div class="flex gap-3 pt-4 border-t border-slate-100">
                <button type="submit" class="px-6 py-3 rounded-xl bg-violet-600 text-white font-bold hover:bg-violet-700">حفظ التعديلات</button>
                <a href="<?php echo e(route('admin.workshop-promo-codes.show', $workshopPromoCode)); ?>" class="px-6 py-3 rounded-xl bg-slate-100 text-slate-700 font-semibold">إلغاء</a>
            </div>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\admin\workshop-promo-codes\edit.blade.php ENDPATH**/ ?>