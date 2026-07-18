

<?php $__env->startSection('title', 'تقرير فريق'); ?>
<?php $__env->startSection('header', 'تقرير فريق — '.$report->team->name); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-3xl space-y-6">
    <div class="bg-white rounded-xl border p-6">
        <p class="text-sm text-gray-500">التاريخ: <?php echo e($report->report_date?->format('Y-m-d')); ?> · المدير: <?php echo e($report->manager->name ?? '—'); ?></p>
        <div class="grid grid-cols-3 gap-4 mt-4 text-sm">
            <div>أعضاء: <strong><?php echo e($report->team_members_count); ?></strong></div>
            <div>تقارير مستلمة: <strong><?php echo e($report->reports_received); ?></strong></div>
            <div>مكالمات: <strong><?php echo e($report->total_calls); ?></strong></div>
        </div>
        <div class="mt-6 space-y-4">
            <div><p class="font-semibold">ملخص الفريق</p><p class="text-gray-700 mt-1 whitespace-pre-wrap"><?php echo e($report->team_summary); ?></p></div>
            <?php if($report->performance_notes): ?><div><p class="font-semibold">الأداء</p><p class="text-gray-700 mt-1 whitespace-pre-wrap"><?php echo e($report->performance_notes); ?></p></div><?php endif; ?>
            <?php if($report->challenges): ?><div><p class="font-semibold">التحديات</p><p class="text-gray-700 mt-1 whitespace-pre-wrap"><?php echo e($report->challenges); ?></p></div><?php endif; ?>
            <?php if($report->recommendations): ?><div><p class="font-semibold">توصيات</p><p class="text-gray-700 mt-1 whitespace-pre-wrap"><?php echo e($report->recommendations); ?></p></div><?php endif; ?>
        </div>
    </div>
    <a href="<?php echo e(route('admin.sales.team-daily-reports.index')); ?>" class="text-emerald-700 font-semibold text-sm">← العودة</a>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\admin\sales\team-daily-reports\show.blade.php ENDPATH**/ ?>