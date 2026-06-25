<?php $__env->startSection('title', 'قالب تقييم جديد — HR'); ?>
<?php $__env->startSection('header', 'قالب تقييم جديد — HR'); ?>

<?php $__env->startSection('content'); ?>

<div class="p-3 sm:p-4 md:p-6 space-y-4 sm:space-y-6" style="background: #f8fafc; min-height: 100vh;">
    <?php echo $__env->make('admin.hr._nav', ['active' => 'rubrics'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('admin.hr._alerts', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <?php echo $__env->make('admin.hr._page-header', [
        'title' => 'قالب تقييم جديد',
        'subtitle' => 'أنشئ Rubric بمعايير وأوزان لحساب درجة المتقدم.',
        'icon' => 'fas fa-plus-circle',
        'actions' => '<a href="' . route('admin.hr.rubrics.index') . '" class="' . $hrBtnSecondary . '"><i class="fas fa-arrow-right"></i> رجوع للقائمة</a>',
    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <section class="<?php echo e($hrSectionClass); ?> max-w-4xl">
        <div class="px-5 py-4 border-b border-slate-200 bg-slate-50/80">
            <h3 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                <i class="fas fa-list text-pink-600"></i>
                بيانات القالب
            </h3>
        </div>
        <form method="post" action="<?php echo e(route('admin.hr.rubrics.store')); ?>" class="p-5 sm:p-6 space-y-6">
            <?php echo csrf_field(); ?>
            <?php echo $__env->make('admin.hr.rubrics._form', ['defaultCriteria' => $defaultCriteria], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

            <div class="flex flex-wrap items-center gap-2 pt-4 border-t border-slate-200">
                <button type="submit" class="<?php echo e($hrBtnPrimary); ?>">
                    <i class="fas fa-save"></i>
                    إنشاء القالب
                </button>
                <a href="<?php echo e(route('admin.hr.rubrics.index')); ?>" class="<?php echo e($hrBtnSecondary); ?>">إلغاء</a>
            </div>
        </form>
    </section>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/cityphone/Documents/all Mindlytics Project/Mindlytics/resources/views/admin/hr/rubrics/create.blade.php ENDPATH**/ ?>