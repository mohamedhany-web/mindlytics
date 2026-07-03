

<?php $__env->startSection('title', 'إنشاء قالب واتساب'); ?>
<?php $__env->startSection('header', 'قسم الواتساب'); ?>

<?php $__env->startSection('content'); ?>
<div class="p-3 sm:p-4 md:p-6 space-y-4 sm:space-y-6" style="background: #f8fafc; min-height: 100vh;">
    <?php echo $__env->make('admin.whatsapp._alerts', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <?php echo $__env->make('admin.whatsapp._page-header', [
        'title' => 'إنشاء قالب Meta جديد',
        'subtitle' => 'صمّم القالب هنا ثم أرسله إلى Meta للمراجعة — لا حاجة لفتح WhatsApp Manager.',
        'icon' => 'fas fa-plus-circle',
        'actions' => '<a href="' . route('admin.whatsapp.templates.index') . '" class="' . $waBtnSecondary . '"><i class="fas fa-arrow-right"></i> كل القوالب</a>',
    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <form method="POST" action="<?php echo e(route('admin.whatsapp.templates.store')); ?>">
        <?php echo csrf_field(); ?>
        <section class="<?php echo e($waSectionClass); ?> p-5 sm:p-6 space-y-6">
            <?php echo $__env->make('admin.whatsapp.templates._form', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

            <div class="flex flex-wrap gap-3 pt-4 border-t border-slate-200">
                <button type="submit" name="submit_now" value="0" class="<?php echo e($waBtnSecondary); ?>">
                    <i class="fas fa-save"></i> حفظ كمسودة
                </button>
                <button type="submit" name="submit_now" value="1" class="<?php echo e($waBtnPrimary); ?>">
                    <i class="fab fa-meta"></i> Submit to Meta
                </button>
            </div>
        </section>
    </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\admin\whatsapp\templates\create.blade.php ENDPATH**/ ?>