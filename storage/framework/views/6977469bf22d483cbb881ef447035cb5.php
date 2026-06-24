

<?php $__env->startSection('title', 'مجموعات العملاء'); ?>
<?php $__env->startSection('header', 'مجموعات العملاء — المبيعات'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-4">
    <div class="flex justify-between items-center">
        <p class="text-sm text-slate-600">إنشاء مجموعات مشتركة لموظف واحد أو أكثر — كل موظف يرى عملاءه ضمن المجموعة</p>
        <a href="<?php echo e(route('admin.sales.groups.create')); ?>" class="px-4 py-2 bg-slate-800 text-white rounded-lg text-sm font-semibold">+ مجموعة</a>
    </div>
    <?php if(session('success')): ?><div class="text-sm text-emerald-700"><?php echo e(session('success')); ?></div><?php endif; ?>
    <div class="bg-white border rounded-xl overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-slate-50"><tr>
                <th class="text-right p-3">المجموعة</th>
                <th class="text-right p-3">الموظفون</th>
                <th class="text-right p-3">عملاء</th>
                <th class="text-right p-3"></th>
            </tr></thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $groups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $g): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr class="border-t">
                    <td class="p-3 font-semibold"><?php echo e($g->name); ?></td>
                    <td class="p-3 text-xs text-slate-600">
                        <?php if($g->members->isNotEmpty()): ?>
                            <?php echo e($g->members->pluck('name')->implode('، ')); ?>

                        <?php else: ?>
                            <?php echo e($g->assignee->name ?? '—'); ?>

                        <?php endif; ?>
                    </td>
                    <td class="p-3"><?php echo e($g->leads_count); ?></td>
                    <td class="p-3"><a href="<?php echo e(route('admin.sales.groups.show', $g)); ?>" class="text-sky-700 font-semibold">إدارة</a></td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr><td colspan="4" class="p-6 text-center text-slate-500">لا توجد مجموعات</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
        <div class="p-3"><?php echo e($groups->links()); ?></div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\admin\sales\groups\index.blade.php ENDPATH**/ ?>