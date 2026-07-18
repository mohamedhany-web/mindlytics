

<?php $__env->startSection('title', 'تقرير يومي'); ?>
<?php $__env->startSection('header', 'تقرير يومي — ' . ($report->user->name ?? '')); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-3xl space-y-4">
    <a href="<?php echo e(route('admin.employee-daily-reports.index')); ?>" class="text-sm text-slate-600 hover:text-slate-900"><i class="fas fa-arrow-right ml-1"></i> العودة</a>
    <div class="rounded-2xl bg-white border p-6 space-y-4">
        <p><strong>الموظف:</strong> <?php echo e($report->user->name ?? '—'); ?></p>
        <p><strong>التاريخ:</strong> <?php echo e($report->report_date->format('Y-m-d')); ?></p>
        <p><strong>الحالة:</strong> <?php echo e($report->isSubmitted() ? 'مُرسل' : 'مسودة'); ?></p>
        <?php if($report->hours_worked): ?><p><strong>ساعات:</strong> <?php echo e($report->hours_worked); ?></p><?php endif; ?>
        <div><h3 class="font-bold mb-1">ملخص</h3><p class="whitespace-pre-wrap text-slate-800"><?php echo e($report->summary ?: '—'); ?></p></div>
        <div><h3 class="font-bold mb-1">المهام المنجزة</h3><p class="whitespace-pre-wrap text-slate-800"><?php echo e($report->tasks_done ?: '—'); ?></p></div>
        <?php if($report->tomorrow_plan): ?><div><h3 class="font-bold mb-1">خطة الغد</h3><p class="whitespace-pre-wrap"><?php echo e($report->tomorrow_plan); ?></p></div><?php endif; ?>
        <?php if($report->blockers): ?><div><h3 class="font-bold mb-1">معوقات</h3><p class="whitespace-pre-wrap text-rose-800"><?php echo e($report->blockers); ?></p></div><?php endif; ?>
        <?php if($report->autoDeduction): ?>
            <div class="rounded-xl border border-rose-200 bg-rose-50 p-3 text-sm text-rose-900">
                <i class="fas fa-minus-circle ml-1"></i> غرامة مسجلة: <?php echo e(number_format((float) $report->autoDeduction->amount, 2)); ?> ج.م
            </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\admin\employee-daily-reports\show.blade.php ENDPATH**/ ?>