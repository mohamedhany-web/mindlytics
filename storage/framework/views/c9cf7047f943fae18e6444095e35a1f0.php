

<?php $__env->startSection('title', 'مجموعات واتساب'); ?>
<?php $__env->startSection('header', 'مجموعات واتساب — المبيعات'); ?>

<?php $__env->startSection('content'); ?>
<?php echo $__env->make('employee.sales.whatsapp-groups._styles', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<?php
    $r = fn($name, ...$p) => route('admin.sales.whatsapp-groups.'.$name, ...$p);
    $settingsUrl = route('admin.whatsapp.settings');
?>

<div class="space-y-4">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h2 class="text-xl font-bold text-slate-900">مجموعات واتساب</h2>
            <p class="text-sm text-slate-500 mt-0.5">إنشاء وإدارة مجموعات Meta Cloud من المنصة</p>
        </div>
        <a href="<?php echo e($r('create')); ?>" class="btn-wa-primary">
            <i class="fas fa-plus"></i> مجموعة جديدة
        </a>
    </div>

    <?php if(session('success')): ?><div class="rounded-lg border border-emerald-200 bg-emerald-50 text-emerald-800 px-4 py-2 text-sm"><?php echo e(session('success')); ?></div><?php endif; ?>
    <?php if(session('error')): ?><div class="rounded-lg border border-rose-200 bg-rose-50 text-rose-800 px-4 py-2 text-sm"><?php echo e(session('error')); ?></div><?php endif; ?>

    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
        <div class="stat-card">
            <p class="text-xs text-slate-500">المجموعات</p>
            <p class="text-2xl font-bold text-slate-900 tabular-nums"><?php echo e($stats['total'] ?? 0); ?></p>
        </div>
        <div class="stat-card">
            <p class="text-xs text-slate-500">نشطة</p>
            <p class="text-2xl font-bold text-emerald-700 tabular-nums"><?php echo e($stats['active'] ?? 0); ?></p>
        </div>
        <div class="stat-card">
            <p class="text-xs text-slate-500">المدعوون</p>
            <p class="text-2xl font-bold text-slate-900 tabular-nums"><?php echo e($stats['participants'] ?? 0); ?></p>
        </div>
        <div class="stat-card">
            <p class="text-xs text-slate-500">Meta Cloud</p>
            <p class="text-sm font-bold <?php echo e(($cloud['connected'] ?? false) ? 'text-emerald-700' : 'text-amber-700'); ?> mt-1">
                <?php echo e(($cloud['connected'] ?? false) ? 'متصل' : 'غير جاهز'); ?>

            </p>
        </div>
    </div>

    <?php echo $__env->make('employee.sales.whatsapp-groups._cloud_status', ['cloud' => $cloud, 'settingsUrl' => $settingsUrl], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <?php if($groups->isEmpty()): ?>
        <div class="sales-panel p-10 text-center">
            <i class="fab fa-whatsapp text-3xl text-slate-300 mb-3"></i>
            <p class="text-slate-600 mb-4">لا توجد مجموعات واتساب</p>
            <a href="<?php echo e($r('create')); ?>" class="btn-wa-primary">إنشاء مجموعة</a>
        </div>
    <?php else: ?>
        <div class="sales-panel overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 border-b border-slate-100">
                    <tr>
                        <th class="text-right p-3 font-semibold text-slate-600">المجموعة</th>
                        <th class="text-right p-3 font-semibold text-slate-600">CRM</th>
                        <th class="text-right p-3 font-semibold text-slate-600">مدعوون</th>
                        <th class="text-right p-3 font-semibold text-slate-600">الحالة</th>
                        <th class="text-right p-3 font-semibold text-slate-600">أنشأها</th>
                        <th class="p-3"></th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $groups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr class="border-t border-slate-100 hover:bg-slate-50/50">
                            <td class="p-3">
                                <p class="font-semibold text-slate-900"><?php echo e($group->subject); ?></p>
                                <?php if($group->description): ?>
                                    <p class="text-xs text-slate-500 truncate max-w-xs"><?php echo e($group->description); ?></p>
                                <?php endif; ?>
                            </td>
                            <td class="p-3 text-xs text-slate-600"><?php echo e($group->salesLeadGroup?->name ?? '—'); ?></td>
                            <td class="p-3 tabular-nums"><?php echo e($group->participants_count); ?></td>
                            <td class="p-3">
                                <span class="text-[10px] px-2 py-0.5 rounded-md font-semibold <?php echo e($group->isActive() ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-600'); ?>"><?php echo e($group->statusLabel()); ?></span>
                            </td>
                            <td class="p-3 text-xs text-slate-600"><?php echo e($group->creator?->name ?? '—'); ?></td>
                            <td class="p-3 text-left">
                                <a href="<?php echo e($r('show', $group)); ?>" class="text-sky-700 font-semibold text-sm hover:underline">إدارة</a>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
            <?php if($groups->hasPages()): ?>
                <div class="p-3 border-t border-slate-100"><?php echo e($groups->links()); ?></div>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\admin\sales\whatsapp-groups\index.blade.php ENDPATH**/ ?>