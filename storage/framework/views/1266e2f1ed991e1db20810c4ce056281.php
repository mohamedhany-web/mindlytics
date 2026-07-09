

<?php $__env->startSection('title', __('admin.about_page')); ?>
<?php $__env->startSection('header', __('admin.about_page')); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-3xl mx-auto py-12">
    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-8 md:p-12">
        <p class="text-gray-600 mb-6">إدارة محتوى صفحة «من نحن» المعروضة للزوار. يمكنك معاينة الصفحة أو تعديل النصوص لاحقاً عند تفعيل التحرير.</p>
        <div class="flex flex-wrap gap-4">
            <a href="<?php echo e(route('public.about')); ?>" target="_blank" rel="noopener" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-blue-600 text-white font-bold hover:bg-blue-700 transition-colors">
                <i class="fas fa-external-link-alt"></i>
                <span>معاينة صفحة من نحن</span>
            </a>
            <a href="<?php echo e(route('admin.dashboard')); ?>" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-gray-100 text-gray-800 font-bold hover:bg-gray-200 transition-colors">
                <i class="fas fa-arrow-right"></i>
                <span>لوحة التحكم</span>
            </a>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\admin\about\index.blade.php ENDPATH**/ ?>