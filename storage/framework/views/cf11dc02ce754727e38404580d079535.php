

<?php $__env->startSection('title', 'تفاصيل التقرير اليومي'); ?>
<?php $__env->startSection('header', 'تفاصيل التقرير اليومي'); ?>

<?php $__env->startSection('content'); ?>
<div class="p-4 md:p-6 space-y-6 max-w-5xl">
    <a href="<?php echo e(route('admin.sales.daily-reports.index')); ?>" class="text-sm text-emerald-700 font-semibold"><i class="fas fa-arrow-right ml-1"></i> العودة</a>

    <div class="bg-white rounded-2xl border p-6">
        <p class="text-sm text-slate-500"><?php echo e($report->report_date->format('Y-m-d')); ?> — <?php echo e($report->user->name ?? ''); ?></p>
        <p class="mt-1 font-bold <?php if($report->isSubmitted()): ?> text-emerald-700 <?php else: ?> text-amber-700 <?php endif; ?>">
            <?php echo e($report->isSubmitted() ? 'مسلّم' : 'مسودة'); ?>

            <?php if($report->autoDeduction): ?>
                <span class="text-rose-600 text-sm mr-2">| خصم: <?php echo e($report->autoDeduction->deduction_number); ?> (<?php echo e(number_format($report->autoDeduction->amount, 2)); ?> ج.م)</span>
            <?php endif; ?>
        </p>
    </div>

    <div class="grid md:grid-cols-2 gap-6">
        <section class="bg-white rounded-2xl border p-5">
            <h3 class="font-bold mb-3">نشاط اليوم</h3>
            <dl class="text-sm space-y-1">
                <div class="flex justify-between"><dt>ردود رسائل</dt><dd class="font-bold"><?php echo e($report->messages_replied); ?></dd></div>
                <div class="flex justify-between"><dt>مؤهلون</dt><dd class="font-bold"><?php echo e($report->leads_qualified); ?></dd></div>
                <div class="flex justify-between"><dt>حجوزات</dt><dd class="font-bold"><?php echo e($report->bookings_from_leads); ?></dd></div>
            </dl>
            <?php if($report->activity_notes): ?><p class="mt-3 text-xs text-slate-600 whitespace-pre-wrap"><?php echo e($report->activity_notes); ?></p><?php endif; ?>
        </section>
        <section class="bg-white rounded-2xl border p-5">
            <h3 class="font-bold mb-3">الإنتاجية</h3>
            <dl class="text-sm space-y-1">
                <div class="flex justify-between"><dt>أرقام</dt><dd class="font-bold"><?php echo e($report->numbers_worked); ?></dd></div>
                <div class="flex justify-between"><dt>متابعات</dt><dd class="font-bold"><?php echo e($report->followups_done); ?></dd></div>
                <div class="flex justify-between"><dt>مكالمات / اجتماعات / ردود</dt><dd class="font-bold"><?php echo e($report->calls_made); ?> / <?php echo e($report->meetings_held); ?> / <?php echo e($report->calls_answered); ?></dd></div>
            </dl>
            <?php if($report->productivity_notes): ?><p class="mt-3 text-xs text-slate-600 whitespace-pre-wrap"><?php echo e($report->productivity_notes); ?></p><?php endif; ?>
        </section>
    </div>

    <section class="bg-white rounded-2xl border overflow-hidden">
        <h3 class="px-5 py-3 font-bold border-b bg-slate-50">المكالمات والاجتماعات (حالة العميل والمشاكل)</h3>
        <div class="divide-y">
            <?php $__empty_1 = true; $__currentLoopData = $report->contacts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="p-5 text-sm">
                    <p class="font-bold"><?php echo e($c->interactionTypeLabel()); ?> — <?php echo e($c->contact_name ?: '—'); ?> — <?php echo e($c->contact_phone); ?></p>
                    <?php if($c->lead): ?><p class="text-xs text-emerald-700">Lead: <?php echo e($c->lead->name); ?></p><?php endif; ?>
                    <p class="mt-2"><span class="font-semibold text-slate-600">الحالة:</span> <?php echo e($c->client_status); ?></p>
                    <p class="mt-1"><span class="font-semibold text-rose-700">المشاكل:</span> <?php echo e($c->client_problems); ?></p>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <p class="p-5 text-slate-500">لا توجد سجلات تواصل</p>
            <?php endif; ?>
        </div>
    </section>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/cityphone/Documents/all Mindlytics Project/Mindlytics/resources/views/admin/sales/daily-reports/show.blade.php ENDPATH**/ ?>