

<?php $__env->startSection('title', 'عميل محتمل جديد'); ?>
<?php $__env->startSection('header', 'إضافة عميل محتمل'); ?>

<?php $__env->startSection('content'); ?>
<div class="p-4 md:p-6 max-w-3xl mx-auto space-y-6" style="background:#f8fafc;min-height:100vh;">
    <a href="<?php echo e(route('admin.sales.leads.index')); ?>" class="text-sm text-gray-600 hover:text-emerald-600"><i class="fas fa-arrow-right ml-1"></i> القائمة</a>

    <?php if($salesReps->isEmpty()): ?>
        <div class="bg-amber-50 border border-amber-200 text-amber-900 px-4 py-3 rounded-lg text-sm">
            لا يوجد موظف مبيعات نشط. أضف وظيفة برمز <code class="bg-amber-100 px-1 rounded">sales</code> وعيّنها لموظف من «الموظفين».
        </div>
    <?php else: ?>
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
        <form method="post" action="<?php echo e(route('admin.sales.leads.store')); ?>">
            <?php echo csrf_field(); ?>
            <?php echo $__env->make('admin.sales.leads._lead_fields', ['lead' => null, 'salesReps' => $salesReps], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            <div class="mt-6 flex gap-3">
                <button type="submit" class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg font-bold">حفظ</button>
                <a href="<?php echo e(route('admin.sales.leads.index')); ?>" class="px-6 py-2.5 border border-gray-300 rounded-lg text-gray-700">إلغاء</a>
            </div>
        </form>
    </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/cityphone/Documents/all Mindlytics Project/Mindlytics/resources/views/admin/sales/leads/create.blade.php ENDPATH**/ ?>