

<?php $__env->startSection('title', 'فريق مبيعات جديد'); ?>
<?php $__env->startSection('header', 'المبيعات — فريق جديد'); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-3xl space-y-6">
    <?php if(session('error')): ?>
        <div class="rounded-xl border border-rose-200 bg-rose-50 text-rose-800 px-4 py-3 text-sm font-semibold">
            <i class="fas fa-exclamation-circle ml-1"></i><?php echo e(session('error')); ?>

        </div>
    <?php endif; ?>
    <?php if($errors->any()): ?>
        <div class="rounded-xl border border-rose-200 bg-rose-50 text-rose-800 px-4 py-3 text-sm">
            <p class="font-semibold mb-1"><i class="fas fa-exclamation-circle ml-1"></i> يوجد أخطاء:</p>
            <ul class="list-disc list-inside space-y-0.5">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($e); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
    <?php endif; ?>

    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-4 py-4 bg-slate-50 border-b border-slate-200 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-teal-500 to-emerald-600 flex items-center justify-center text-white shadow-md">
                    <i class="fas fa-plus"></i>
                </div>
                <div>
                    <h2 class="text-xl font-black text-slate-900">فريق مبيعات جديد</h2>
                    <p class="text-xs text-slate-600">اربط مدير مبيعات بأعضاء السيلز في فريق واحد.</p>
                </div>
            </div>
            <a href="<?php echo e(route('admin.sales.sales-teams.index')); ?>" class="inline-flex items-center gap-2 px-3 py-2 text-sm font-semibold text-slate-700 rounded-xl border border-slate-300 hover:bg-white">
                <i class="fas fa-arrow-right"></i>
                العودة للقائمة
            </a>
        </div>

        <form method="POST" action="<?php echo e(route('admin.sales.sales-teams.store')); ?>" class="p-6">
            <?php echo csrf_field(); ?>
            <?php echo $__env->make('admin.sales.sales-teams._form', ['team' => null], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            <div class="mt-6 pt-4 border-t border-slate-200 flex flex-wrap gap-3">
                <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-semibold text-white rounded-xl bg-emerald-600 hover:bg-emerald-700">
                    <i class="fas fa-save"></i>
                    حفظ الفريق
                </button>
                <a href="<?php echo e(route('admin.sales.sales-teams.index')); ?>" class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-semibold text-slate-700 rounded-xl border border-slate-300 hover:bg-slate-50">
                    إلغاء
                </a>
            </div>
        </form>
    </section>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\admin\sales\sales-teams\create.blade.php ENDPATH**/ ?>