

<?php $__env->startSection('title', 'تقرير '.$report->user->name); ?>
<?php $__env->startSection('header', 'تقرير يومي — '.$report->user->name); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6 max-w-3xl">
    <div class="bg-white rounded-xl border p-6">
        <p class="text-sm text-slate-500">التاريخ: <?php echo e($report->report_date?->format('Y-m-d')); ?></p>
        <div class="grid grid-cols-2 md:grid-cols-3 gap-4 mt-4 text-sm">
            <div><span class="text-slate-500">مكالمات:</span> <strong><?php echo e($report->calls_made ?? '—'); ?></strong></div>
            <div><span class="text-slate-500">Leads مؤهلة:</span> <strong><?php echo e($report->leads_qualified ?? '—'); ?></strong></div>
            <div><span class="text-slate-500">حجوزات:</span> <strong><?php echo e($report->bookings_from_leads ?? '—'); ?></strong></div>
            <div><span class="text-slate-500">متابعات:</span> <strong><?php echo e($report->followups_done ?? '—'); ?></strong></div>
            <div><span class="text-slate-500">رسائل:</span> <strong><?php echo e($report->messages_replied ?? '—'); ?></strong></div>
        </div>
        <?php if($report->activity_notes): ?>
            <div class="mt-4"><p class="text-sm font-semibold text-slate-700">ملاحظات النشاط</p><p class="text-sm text-slate-600 mt-1"><?php echo e($report->activity_notes); ?></p></div>
        <?php endif; ?>
        <?php if($report->productivity_notes): ?>
            <div class="mt-4"><p class="text-sm font-semibold text-slate-700">ملاحظات الإنتاجية</p><p class="text-sm text-slate-600 mt-1"><?php echo e($report->productivity_notes); ?></p></div>
        <?php endif; ?>
    </div>
    <a href="<?php echo e(route('employee.sales-manager.daily-reports.index')); ?>" class="text-sm text-emerald-700 font-semibold">← العودة</a>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.employee', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\employee\sales-manager\daily-reports\show.blade.php ENDPATH**/ ?>