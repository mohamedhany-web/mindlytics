

<?php $__env->startSection('title', 'تعديل خطة — ' . $plan->title); ?>
<?php $__env->startSection('header', 'تعديل الخطة'); ?>

<?php $__env->startSection('content'); ?>
<?php echo $__env->make('admin.investment._styles', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<div class="p-3 sm:p-4 md:p-6 space-y-4 sm:space-y-6" style="background: #f8fafc; min-height: 100vh;">
    <?php echo $__env->make('admin.investment._alerts', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('admin.investment._nav', ['active' => 'plans'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('admin.investment._header', ['title' => 'تعديل: ' . $plan->title, 'subtitle' => $plan->planTypeLabel() . ' · ' . $plan->riskLevelLabel(), 'icon' => 'fas fa-edit'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <section class="<?php echo e($invSectionClass); ?>">
        <?php echo $__env->make('admin.investment._section-head', [
            'icon' => 'fas fa-edit',
            'title' => 'تعديل بيانات الخطة',
        ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <form method="POST" action="<?php echo e(route('admin.investment.plans.update', $plan)); ?>" class="p-6 space-y-5">
            <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
            <?php echo $__env->make('admin.investment.plans._form', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            <div class="flex flex-wrap gap-3 pt-4 border-t border-slate-200">
                <button type="submit" class="<?php echo e($invBtnPrimary); ?>"><i class="fas fa-save"></i> حفظ</button>
                <a href="<?php echo e(route('admin.investment.plans.show', $plan)); ?>" class="<?php echo e($invBtnSecondary); ?>">عرض</a>
            </div>
        </form>
    </section>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\admin\investment\plans\edit.blade.php ENDPATH**/ ?>