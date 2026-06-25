<?php $__env->startSection('title', 'Insights — المبيعات'); ?>
<?php $__env->startSection('header', 'Insights — المبيعات'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <section class="rounded-2xl bg-white border border-amber-200 shadow-lg overflow-hidden">
        <div class="px-4 py-8 text-center">
            <div class="w-14 h-14 mx-auto mb-3 rounded-xl bg-amber-100 flex items-center justify-center text-amber-600">
                <i class="fas fa-users text-xl"></i>
            </div>
            <h2 class="text-lg font-black text-slate-900 mb-2">لا يوجد موظفو مبيعات</h2>
            <p class="text-sm text-slate-600 max-w-md mx-auto">أضف موظف بوظيفة <strong>sales</strong> ثم أعد فتح صفحة Insights لعرض الشارتات والاتجاهات.</p>
        </div>
    </section>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/cityphone/Documents/all Mindlytics Project/Mindlytics/resources/views/admin/sales/insights/empty.blade.php ENDPATH**/ ?>