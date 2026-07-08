

<?php $__env->startSection('title', 'الإطار القانوني والسياسات'); ?>
<?php $__env->startSection('header', 'الإطار القانوني'); ?>

<?php $__env->startSection('content'); ?>
<?php echo $__env->make('admin.investment._styles', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<?php
    $contactEmail = old('contact_email', $policy->contact_email ?: ($platformContact['email'] ?? ''));
    $contactPhone = old('contact_phone', $policy->contact_phone ?: ($platformContact['phone'] ?? ''));
?>

<div class="p-3 sm:p-4 md:p-6 space-y-4 sm:space-y-6" style="background: #f8fafc; min-height: 100vh;">
    <?php echo $__env->make('admin.investment._alerts', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <?php echo $__env->make('admin.investment._header', [
        'title' => 'الإطار القانوني والسياسات',
        'subtitle' => 'القوانين، الشروط، آلية الاستثمار، وإخلاء المسؤولية — تظهر في الصفحة العامة',
        'icon' => 'fas fa-gavel',
    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <?php echo $__env->make('admin.investment._nav', ['active' => 'policies'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <section class="<?php echo e($invSectionClass); ?>">
        <?php echo $__env->make('admin.investment._section-head', [
            'icon' => 'fas fa-gavel',
            'title' => 'محتوى السياسات والإطار القانوني',
            'subtitle' => 'راجع النصوص مع المستشار القانوني قبل النشر',
        ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <form method="POST" action="<?php echo e(route('admin.investment.policies.update')); ?>" class="p-6 space-y-5">
            <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
            <?php $__currentLoopData = [
                'overview' => 'نظرة عامة على قسم الاستثمار',
                'eligibility_rules' => 'قواعد الأهلية',
                'legal_framework' => 'الإطار القانوني والامتثال',
                'terms_conditions' => 'الشروط والأحكام',
                'privacy_notice' => 'خصوصية بيانات المستثمرين',
                'process_description' => 'كيفية الاستثمار (الخطوات)',
                'disclaimer' => 'إخلاء المسؤولية',
            ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $field => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div>
                    <label class="<?php echo e($invLabelClass); ?>"><?php echo e($label); ?></label>
                    <textarea name="<?php echo e($field); ?>" rows="5" class="<?php echo e($invTextareaClass); ?>"><?php echo e(old($field, $policy->$field)); ?></textarea>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-2 border-t border-slate-200">
                <div>
                    <label class="<?php echo e($invLabelClass); ?>">بريد التواصل</label>
                    <input type="email" name="contact_email" value="<?php echo e($contactEmail); ?>" class="<?php echo e($invInputClass); ?> dir-ltr" placeholder="info@mindlytics-academy.com">
                    <p class="text-xs text-slate-500 mt-1">يُستخدم في الصفحة العامة للاستفسارات</p>
                </div>
                <div>
                    <label class="<?php echo e($invLabelClass); ?>">هاتف التواصل</label>
                    <input type="text" name="contact_phone" value="<?php echo e($contactPhone); ?>" class="<?php echo e($invInputClass); ?> dir-ltr" placeholder="01044610507">
                </div>
            </div>
            <button type="submit" class="<?php echo e($invBtnPrimary); ?>"><i class="fas fa-save"></i> حفظ السياسات</button>
        </form>
    </section>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\admin\investment\policies\edit.blade.php ENDPATH**/ ?>