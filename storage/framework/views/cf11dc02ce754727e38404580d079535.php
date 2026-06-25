<?php $__env->startSection('title', 'تفاصيل التقرير اليومي'); ?>
<?php $__env->startSection('header', 'تفاصيل التقرير اليومي'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $statusBadges = [
        'submitted' => ['label' => 'مسلّم', 'classes' => 'bg-emerald-100 text-emerald-700 border border-emerald-200'],
        'draft' => ['label' => 'مسودة', 'classes' => 'bg-amber-100 text-amber-700 border border-amber-200'],
    ];
    $statusKey = $report->isSubmitted() ? 'submitted' : 'draft';
    $statusMeta = $statusBadges[$statusKey];

    $activityStats = [
        ['label' => 'ردود رسائل', 'value' => $report->messages_replied ?? '—', 'icon' => 'fas fa-comment-dots', 'bg' => 'bg-sky-100', 'text' => 'text-sky-600'],
        ['label' => 'مؤهلون', 'value' => $report->leads_qualified ?? '—', 'icon' => 'fas fa-user-check', 'bg' => 'bg-emerald-100', 'text' => 'text-emerald-600'],
        ['label' => 'حجوزات', 'value' => $report->bookings_from_leads ?? '—', 'icon' => 'fas fa-calendar-check', 'bg' => 'bg-violet-100', 'text' => 'text-violet-600'],
    ];
    $productivityStats = [
        ['label' => 'أرقام', 'value' => $report->numbers_worked ?? '—', 'icon' => 'fas fa-phone', 'bg' => 'bg-blue-100', 'text' => 'text-blue-600'],
        ['label' => 'متابعات', 'value' => $report->followups_done ?? '—', 'icon' => 'fas fa-redo', 'bg' => 'bg-amber-100', 'text' => 'text-amber-600'],
        ['label' => 'مكالمات / اجتماعات / ردود', 'value' => ($report->calls_made ?? '—') . ' / ' . ($report->meetings_held ?? '—') . ' / ' . ($report->calls_answered ?? '—'), 'icon' => 'fas fa-headset', 'bg' => 'bg-slate-100', 'text' => 'text-slate-600'],
    ];
?>

<div class="space-y-6">
    
    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-4 py-4 bg-slate-50 border-b border-slate-200 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-sky-500 to-sky-600 flex items-center justify-center text-white shadow-md">
                    <i class="fas fa-file-alt"></i>
                </div>
                <div>
                    <h2 class="text-xl font-black text-slate-900">تقرير يومي — <?php echo e($report->user->name ?? '—'); ?></h2>
                    <p class="text-xs text-slate-600">
                        <i class="fas fa-calendar ml-0.5"></i>
                        <?php echo e($report->report_date->format('Y-m-d')); ?>

                    </p>
                </div>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <span class="inline-flex items-center gap-1 rounded-lg px-2.5 py-1 text-xs font-semibold <?php echo e($statusMeta['classes']); ?>">
                    <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                    <?php echo e($statusMeta['label']); ?>

                </span>
                <?php if($report->autoDeduction): ?>
                    <span class="inline-flex items-center gap-1 rounded-lg px-2.5 py-1 text-xs font-semibold bg-rose-100 text-rose-700 border border-rose-200">
                        <i class="fas fa-gavel text-[10px]"></i>
                        خصم: <?php echo e($report->autoDeduction->deduction_number); ?> (<?php echo e(number_format($report->autoDeduction->amount, 2)); ?> ج.م)
                    </span>
                <?php endif; ?>
                <a href="<?php echo e(route('admin.sales.daily-reports.index')); ?>" class="inline-flex items-center gap-2 px-3 py-2 text-sm font-semibold text-slate-700 rounded-xl border border-slate-300 hover:bg-slate-50">
                    <i class="fas fa-arrow-right"></i>
                    العودة للقائمة
                </a>
            </div>
        </div>
    </section>

    <?php if($kpiComparison ?? null): ?>
        <?php
            $kc = $kpiComparison;
            $kcClass = match ($kc['status'] ?? '') {
                'met' => 'border-emerald-200 bg-emerald-50',
                'near' => 'border-amber-200 bg-amber-50',
                default => 'border-rose-200 bg-rose-50',
            };
        ?>
        <section class="rounded-2xl border <?php echo e($kcClass); ?> p-5">
            <h3 class="font-black text-slate-900 mb-2"><i class="fas fa-bullseye ml-1"></i> مقارنة KPI ليوم التقرير</h3>
            <p class="text-sm font-semibold mb-3"><?php echo e($kc['status_label'] ?? ''); ?> — <?php echo e($kc['overall_pct'] ?? 0); ?>%</p>
            <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-3 text-sm">
                <?php $__currentLoopData = $kc['lines'] ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $line): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="rounded-lg bg-white/80 border border-white px-3 py-2">
                        <p class="text-slate-600 text-xs"><?php echo e($line['label']); ?></p>
                        <p class="font-bold"><?php echo e($line['actual']); ?> / <?php echo e($line['target']); ?> (<?php echo e($line['pct']); ?>%)</p>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </section>
    <?php endif; ?>

    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6 items-start">
    
    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden h-full">
        <div class="px-4 py-3 border-b border-slate-200 bg-slate-50">
            <h3 class="text-base font-black text-slate-900 flex items-center gap-2">
                <i class="fas fa-bolt text-amber-500"></i>
                نشاط اليوم
            </h3>
        </div>
        <div class="p-4">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-4">
                <?php $__currentLoopData = $activityStats; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $stat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="rounded-xl border border-slate-200 bg-white p-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs font-semibold text-slate-600"><?php echo e($stat['label']); ?></p>
                                <p class="text-xl font-black text-slate-900 tabular-nums"><?php echo e($stat['value']); ?></p>
                            </div>
                            <div class="w-9 h-9 rounded-lg <?php echo e($stat['bg']); ?> flex items-center justify-center <?php echo e($stat['text']); ?>">
                                <i class="<?php echo e($stat['icon']); ?> text-xs"></i>
                            </div>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            <?php if($report->activity_notes): ?>
                <div class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3">
                    <p class="text-xs font-semibold text-slate-600 mb-1">ملاحظات النشاط</p>
                    <p class="text-sm text-slate-700 whitespace-pre-wrap"><?php echo e($report->activity_notes); ?></p>
                </div>
            <?php endif; ?>
        </div>
    </section>

    
    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden h-full">
        <div class="px-4 py-3 border-b border-slate-200 bg-slate-50">
            <h3 class="text-base font-black text-slate-900 flex items-center gap-2">
                <i class="fas fa-chart-line text-emerald-600"></i>
                الإنتاجية
            </h3>
        </div>
        <div class="p-4">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-4">
                <?php $__currentLoopData = $productivityStats; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $stat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="rounded-xl border border-slate-200 bg-white p-4">
                        <div class="flex items-center justify-between gap-2">
                            <div class="min-w-0">
                                <p class="text-xs font-semibold text-slate-600"><?php echo e($stat['label']); ?></p>
                                <p class="text-lg font-black text-slate-900 tabular-nums truncate"><?php echo e($stat['value']); ?></p>
                            </div>
                            <div class="w-9 h-9 rounded-lg <?php echo e($stat['bg']); ?> flex items-center justify-center <?php echo e($stat['text']); ?> flex-shrink-0">
                                <i class="<?php echo e($stat['icon']); ?> text-xs"></i>
                            </div>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            <?php if($report->productivity_notes): ?>
                <div class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3">
                    <p class="text-xs font-semibold text-slate-600 mb-1">ملاحظات الإنتاجية</p>
                    <p class="text-sm text-slate-700 whitespace-pre-wrap"><?php echo e($report->productivity_notes); ?></p>
                </div>
            <?php endif; ?>
        </div>
    </section>
    </div>

    
    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-4 py-3 border-b border-slate-200 bg-slate-50 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
            <div>
                <h3 class="text-base font-black text-slate-900">المكالمات والاجتماعات</h3>
                <p class="text-xs text-slate-600">حالة العميل والمشاكل المسجّلة.</p>
            </div>
            <span class="text-xs font-semibold text-sky-700 bg-sky-50 px-2.5 py-1 rounded-lg border border-sky-200"><?php echo e($report->contacts->count()); ?> سجل</span>
        </div>
        <div class="divide-y divide-slate-100">
            <?php $__empty_1 = true; $__currentLoopData = $report->contacts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="p-4 sm:p-5">
                    <div class="flex flex-wrap items-start justify-between gap-2 mb-3">
                        <div>
                            <p class="text-sm font-bold text-slate-900">
                                <?php echo e($c->interactionTypeLabel()); ?>

                                <?php if($c->contact_name || $c->contact_phone): ?>
                                    — <?php echo e($c->contact_name ?: '—'); ?>

                                    <?php if($c->contact_phone): ?>
                                        <span class="text-slate-500 font-medium">(<?php echo e($c->contact_phone); ?>)</span>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </p>
                            <?php if($c->lead): ?>
                                <p class="text-xs text-emerald-700 mt-1">
                                    <i class="fas fa-user-tag ml-0.5"></i>
                                    Lead: <?php echo e($c->lead->name); ?>

                                </p>
                            <?php endif; ?>
                        </div>
                    </div>
                    <dl class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
                        <div class="rounded-lg border border-slate-200 bg-slate-50/50 px-3 py-2">
                            <dt class="text-xs font-semibold text-slate-500 mb-1">حالة العميل</dt>
                            <dd class="text-slate-800"><?php echo e($c->client_status ?: '—'); ?></dd>
                        </div>
                        <div class="rounded-lg border border-rose-200 bg-rose-50/30 px-3 py-2">
                            <dt class="text-xs font-semibold text-rose-700 mb-1">المشاكل</dt>
                            <dd class="text-slate-800"><?php echo e($c->client_problems ?: '—'); ?></dd>
                        </div>
                    </dl>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="p-10 text-center">
                    <div class="w-14 h-14 mx-auto mb-3 rounded-xl bg-slate-100 flex items-center justify-center text-slate-400">
                        <i class="fas fa-phone-slash text-xl"></i>
                    </div>
                    <p class="text-sm font-semibold text-slate-900">لا توجد سجلات تواصل</p>
                    <p class="text-xs text-slate-500 mt-1">لم يُسجّل أي تواصل في هذا التقرير.</p>
                </div>
            <?php endif; ?>
        </div>
    </section>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/cityphone/Documents/all Mindlytics Project/Mindlytics/resources/views/admin/sales/daily-reports/show.blade.php ENDPATH**/ ?>