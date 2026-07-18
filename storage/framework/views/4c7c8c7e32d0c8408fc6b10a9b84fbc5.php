

<?php $__env->startSection('title', 'تقارير الفريق للإدارة'); ?>
<?php $__env->startSection('header', 'تقارير الفريق للإدارة'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <p class="text-sm text-slate-600">فريق: <?php echo e($team->name); ?></p>
        <a href="<?php echo e(route('employee.sales-manager.team-reports.edit')); ?>" class="px-4 py-2 bg-slate-800 text-white rounded-lg text-sm font-semibold">تقرير اليوم</a>
    </div>
    <?php if(session('success')): ?><div class="rounded-lg bg-emerald-50 border border-emerald-200 px-4 py-3 text-sm"><?php echo e(session('success')); ?></div><?php endif; ?>
    <div class="bg-white rounded-xl border overflow-hidden">
        <table class="min-w-full text-sm">
            <thead class="bg-slate-50"><tr>
                <th class="px-4 py-3 text-right">التاريخ</th>
                <th class="px-4 py-3 text-right">تقارير مستلمة</th>
                <th class="px-4 py-3 text-right">الحالة</th>
                <th class="px-4 py-3 text-right">تسليم</th>
            </tr></thead>
            <tbody class="divide-y">
                <?php $__empty_1 = true; $__currentLoopData = $reports; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td class="px-4 py-3"><?php echo e($r->report_date?->format('Y-m-d')); ?></td>
                        <td class="px-4 py-3"><?php echo e($r->reports_received); ?>/<?php echo e($r->team_members_count); ?></td>
                        <td class="px-4 py-3"><?php echo e($r->isSubmitted() ? 'مُرسَل للإدارة' : 'مسودة'); ?></td>
                        <td class="px-4 py-3"><?php echo e($r->submitted_at?->format('Y-m-d H:i') ?? '—'); ?></td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="4" class="px-4 py-10 text-center text-slate-500">لم تُرفَع تقارير فريق بعد.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
        <?php if($reports->hasPages()): ?><div class="px-4 py-3"><?php echo e($reports->links()); ?></div><?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.employee', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\employee\sales-manager\team-reports\index.blade.php ENDPATH**/ ?>