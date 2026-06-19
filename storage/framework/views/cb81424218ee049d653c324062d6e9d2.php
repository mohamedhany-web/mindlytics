

<?php $__env->startSection('title', 'مجموعات العملاء'); ?>
<?php $__env->startSection('header', 'مجموعات العملاء'); ?>

<?php $__env->startSection('content'); ?>
<?php echo $__env->make('employee.sales.groups._styles', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<div class="space-y-4">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h2 class="text-xl font-bold text-slate-900">مجموعات العملاء</h2>
            <p class="text-sm text-slate-500 mt-0.5">قسّم عملاءك لحملات، مناطق، أو أي تصنيف يناسبك</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="<?php echo e(route('employee.sales.dashboard')); ?>" class="px-4 py-2 text-sm border border-slate-200 rounded-lg text-slate-700 hover:bg-slate-50">مركز المبيعات</a>
            <a href="<?php echo e(route('employee.sales.groups.create')); ?>" class="px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white rounded-lg text-sm font-semibold">
                <i class="fas fa-plus ml-1"></i> مجموعة جديدة
            </a>
        </div>
    </div>

    <?php if(session('success')): ?>
        <div class="rounded-lg border border-emerald-200 bg-emerald-50 text-emerald-800 px-4 py-2 text-sm"><?php echo e(session('success')); ?></div>
    <?php endif; ?>

    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
        <div class="stat-card">
            <p class="text-xs text-slate-500">المجموعات</p>
            <p class="text-2xl font-bold text-slate-900 tabular-nums"><?php echo e($stats['groups'] ?? 0); ?></p>
        </div>
        <div class="stat-card">
            <p class="text-xs text-slate-500">عملاء في مجموعات</p>
            <p class="text-2xl font-bold text-slate-900 tabular-nums"><?php echo e($stats['leads'] ?? 0); ?></p>
        </div>
        <div class="stat-card">
            <p class="text-xs text-slate-500">مجموعاتي</p>
            <p class="text-2xl font-bold text-slate-900 tabular-nums"><?php echo e($stats['mine'] ?? 0); ?></p>
        </div>
        <div class="stat-card">
            <p class="text-xs text-slate-500">من الإدارة</p>
            <p class="text-2xl font-bold text-slate-900 tabular-nums"><?php echo e($stats['admin'] ?? 0); ?></p>
        </div>
    </div>

    <?php if($groups->isEmpty()): ?>
        <div class="sales-panel p-8 text-center">
            <i class="fas fa-layer-group text-3xl text-slate-300 mb-3"></i>
            <p class="text-slate-600 mb-4">لا توجد مجموعات بعد — أنشئ مجموعة عند تسجيل العملاء أو من هنا.</p>
            <a href="<?php echo e(route('employee.sales.groups.create')); ?>" class="inline-flex px-5 py-2.5 bg-slate-800 text-white rounded-lg text-sm font-semibold">إنشاء أول مجموعة</a>
        </div>
    <?php else: ?>
        <div class="grid sm:grid-cols-2 xl:grid-cols-3 gap-4">
            <?php $__currentLoopData = $groups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <a href="<?php echo e(route('employee.sales.groups.show', $group)); ?>" class="group-card block">
                    <div class="flex items-start justify-between gap-2">
                        <h3 class="font-bold text-slate-900"><?php echo e($group->name); ?></h3>
                        <?php if($group->is_admin_managed): ?>
                            <span class="text-[10px] px-2 py-0.5 rounded-md bg-sky-100 text-sky-800 font-semibold shrink-0">إدارة</span>
                        <?php else: ?>
                            <span class="text-[10px] px-2 py-0.5 rounded-md bg-slate-100 text-slate-600 font-semibold shrink-0">خاصة</span>
                        <?php endif; ?>
                    </div>
                    <?php if($group->description): ?>
                        <p class="text-xs text-slate-500 mt-2 line-clamp-2"><?php echo e($group->description); ?></p>
                    <?php endif; ?>
                    <div class="flex items-center justify-between mt-4 pt-3 border-t border-slate-100 text-sm">
                        <span class="text-slate-600"><i class="fas fa-users ml-1 text-slate-400"></i> <?php echo e($group->leads_count); ?> عميل</span>
                        <span class="text-slate-800 font-semibold">إدارة ←</span>
                    </div>
                </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.employee', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views/employee/sales/groups/index.blade.php ENDPATH**/ ?>