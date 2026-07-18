

<?php $__env->startSection('title', 'مركز مدير المبيعات'); ?>
<?php $__env->startSection('header', 'مركز مدير المبيعات'); ?>

<?php $__env->startPush('styles'); ?>
<?php echo $__env->make('employee.sales._styles', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<style>
    .sales-hub .dashboard-card {
        background: #fff; border: 1px solid #e2e8f0; border-radius: 12px;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04); padding: 1rem 1.25rem;
    }
    .sales-hub .panel-card { border-radius: 12px; border: 1px solid #e2e8f0; }
    .sales-hub .panel-card-head { background: #f8fafc; border-bottom: 1px solid #e2e8f0; padding: 1rem 1.25rem; }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<?php $user = auth()->user(); ?>
<div class="space-y-6 sales-hub">
    <div class="dashboard-card flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div class="flex items-start gap-4">
            <div class="w-12 h-12 rounded-xl bg-emerald-100 flex items-center justify-center text-emerald-700 shrink-0">
                <i class="fas fa-users-cog text-xl"></i>
            </div>
            <div>
                <h2 class="text-2xl font-bold text-slate-900">مرحباً، <?php echo e($user->name); ?></h2>
                <p class="text-slate-600 text-sm mt-1">فريق: <strong><?php echo e($team->name); ?></strong> — <?php echo e($stats['team_members']); ?> موظف مبيعات</p>
            </div>
        </div>
        <div class="flex flex-wrap gap-2 shrink-0">
            <a href="<?php echo e(route('employee.sales-manager.leads.index')); ?>" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg bg-slate-800 hover:bg-slate-900 text-white text-sm font-semibold"><i class="fas fa-user-plus"></i> عملاء الفريق</a>
            <a href="<?php echo e(route('employee.sales-manager.team-reports.edit')); ?>" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg border border-slate-200 bg-white text-slate-700 text-sm font-semibold hover:bg-slate-50"><i class="fas fa-clipboard-check"></i> تقرير الفريق</a>
        </div>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-6 gap-4">
        <?php
            $statCards = [
                ['label' => 'إجمالي Leads', 'value' => $stats['total'], 'icon' => 'fa-users'],
                ['label' => 'نشط', 'value' => $stats['active'], 'icon' => 'fa-fire'],
                ['label' => 'متابعات متأخرة', 'value' => $stats['followups_overdue'], 'icon' => 'fa-bell'],
                ['label' => 'متابعات اليوم', 'value' => $stats['followups_today'], 'icon' => 'fa-calendar-day'],
                ['label' => 'بلا تواصل', 'value' => $stats['stale'], 'icon' => 'fa-hourglass-end'],
                ['label' => 'أعضاء الفريق', 'value' => $stats['team_members'], 'icon' => 'fa-user-friends'],
            ];
        ?>
        <?php $__currentLoopData = $statCards; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $card): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="dashboard-card">
                <p class="text-xs font-medium text-slate-500 mb-1"><?php echo e($card['label']); ?></p>
                <div class="flex items-center justify-between">
                    <p class="text-2xl font-bold text-slate-800 tabular-nums"><?php echo e($card['value']); ?></p>
                    <div class="w-10 h-10 bg-slate-100 rounded-lg flex items-center justify-center text-slate-500"><i class="fas <?php echo e($card['icon']); ?>"></i></div>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="panel-card lg:col-span-2">
            <div class="panel-card-head flex flex-wrap items-center justify-between gap-2">
                <h2 class="font-bold text-slate-900">أعضاء الفريق</h2>
                <a href="<?php echo e(route('employee.sales-manager.attendance.index')); ?>" class="text-xs text-emerald-700 font-semibold hover:underline">حضور الفريق</a>
            </div>
            <ul class="divide-y divide-slate-100">
                <?php $__empty_1 = true; $__currentLoopData = $members; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $member): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <?php
                        $u = $member->user;
                        $uid = (int) ($u->id ?? $member->user_id);
                        $onLeave = in_array($uid, $onLeaveIds ?? [], true);
                        $schedule = $u?->workSchedule;
                    ?>
                    <li class="px-5 py-3.5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                        <div class="min-w-0">
                            <div class="flex flex-wrap items-center gap-2">
                                <p class="font-semibold text-slate-900"><?php echo e($u->name ?? '—'); ?></p>
                                <?php if($onLeave): ?>
                                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-violet-50 text-violet-800 border border-violet-200">إجازة</span>
                                <?php endif; ?>
                            </div>
                            <p class="text-xs text-slate-500 mt-0.5"><?php echo e($u->email ?? ''); ?></p>
                            <p class="text-[11px] text-slate-500 mt-1.5 flex flex-wrap gap-x-3 gap-y-1">
                                <span>
                                    <i class="fas fa-clock text-indigo-500 ml-0.5"></i>
                                    <?php if($schedule): ?>
                                        <?php echo e($schedule->timeRangeLabel()); ?>

                                    <?php else: ?>
                                        بدون جدول
                                    <?php endif; ?>
                                </span>
                                <span>
                                    <i class="fas fa-calendar-day text-amber-500 ml-0.5"></i>
                                    راحة: <?php echo e($u?->weeklyOffDayLabel() ?? '—'); ?>

                                </span>
                                <span>
                                    <i class="fas fa-users text-slate-400 ml-0.5"></i>
                                    <?php echo e((int) ($leadCounts[$uid] ?? 0)); ?> عميل
                                </span>
                            </p>
                        </div>
                        <div class="flex flex-wrap items-center gap-2 shrink-0">
                            <a href="<?php echo e(route('employee.sales-manager.team.show', $uid)); ?>"
                               class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-slate-800 text-white text-xs font-bold hover:bg-slate-900">
                                <i class="fas fa-id-card"></i> عرض الملف
                            </a>
                            <a href="<?php echo e(route('employee.sales-manager.team.report', $uid)); ?>"
                               class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-emerald-600 text-white text-xs font-bold hover:bg-emerald-700">
                                <i class="fas fa-chart-line"></i> تقرير الأداء
                            </a>
                            <a href="<?php echo e(route('employee.sales-manager.attendance.employee', $uid)); ?>"
                               class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg border border-slate-200 text-slate-700 text-xs font-bold hover:bg-slate-50">
                                الحضور
                            </a>
                        </div>
                    </li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <li class="px-5 py-8 text-center text-slate-500">لا يوجد أعضاء في الفريق بعد.</li>
                <?php endif; ?>
            </ul>
        </div>
        <div class="panel-card">
            <div class="panel-card-head"><h2 class="font-bold text-slate-900">اختصارات</h2></div>
            <div class="p-5 space-y-2">
                <a href="<?php echo e(route('employee.sales-manager.whatsapp.inbox.index')); ?>" class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-slate-50 text-sm font-medium text-slate-700"><i class="fab fa-whatsapp text-emerald-600"></i> محادثات الفريق</a>
                <a href="<?php echo e(route('employee.sales-manager.follow-ups.index')); ?>" class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-slate-50 text-sm font-medium text-slate-700"><i class="fas fa-clipboard-list text-teal-600"></i> رقابة المتابعات</a>
                <a href="<?php echo e(route('employee.sales-manager.daily-reports.index')); ?>" class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-slate-50 text-sm font-medium text-slate-700"><i class="fas fa-clipboard-list text-sky-600"></i> تقارير الأعضاء</a>
                <a href="<?php echo e(route('employee.sales-manager.presence.index')); ?>" class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-slate-50 text-sm font-medium text-slate-700"><i class="fas fa-satellite-dish text-rose-600"></i> مراقبة التواجد (Live)</a>
                <a href="<?php echo e(route('employee.sales-manager.transfer.index')); ?>" class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-slate-50 text-sm font-medium text-slate-700"><i class="fas fa-exchange-alt text-amber-600"></i> تحويل Leads</a>
                <a href="<?php echo e(route('employee.sales-manager.attendance.index')); ?>" class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-slate-50 text-sm font-medium text-slate-700"><i class="fas fa-clock text-violet-600"></i> حضور الفريق</a>
            </div>
        </div>
    </div>

    <div class="panel-card">
        <div class="panel-card-head flex justify-between items-center">
            <h2 class="font-bold text-slate-900">Task Queue — الفريق</h2>
            <a href="<?php echo e(route('employee.sales-manager.follow-ups.index', ['filter' => 'overdue'])); ?>" class="text-xs text-emerald-700 font-semibold">عرض الكل</a>
        </div>
        <ul class="divide-y divide-slate-100">
            <?php $__empty_1 = true; $__currentLoopData = ($taskQueue ?? collect()); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <?php $lead = $item['lead']; ?>
                <li class="px-5 py-3 hover:bg-slate-50">
                    <a href="<?php echo e(route('employee.sales-manager.leads.show', $lead)); ?>" class="block">
                        <div class="flex items-center justify-between gap-2">
                            <span class="font-semibold text-slate-900"><?php echo e($lead->name); ?></span>
                            <span class="text-xs text-slate-500"><?php echo e($lead->assignee->name ?? '—'); ?></span>
                        </div>
                        <p class="text-xs text-slate-500 mt-1"><?php echo e($item['reason']); ?> — <?php echo e($item['next_action']); ?></p>
                    </a>
                </li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <li class="px-5 py-8 text-center text-slate-500">لا توجد مهام عاجلة للفريق اليوم.</li>
            <?php endif; ?>
        </ul>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.employee', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\employee\sales-manager\dashboard.blade.php ENDPATH**/ ?>