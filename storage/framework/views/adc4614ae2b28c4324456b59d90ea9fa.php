

<?php $__env->startSection('title', $employee->name); ?>
<?php $__env->startSection('header', 'ملف الموظف'); ?>

<?php
    $modeLabels = [
        'working' => 'يعمل الآن',
        'manager_unlocked_working' => 'يعمل بتصريح فتح',
        'manager_unlocked' => 'مفتوح — بانتظار الحضور',
        'awaiting_clock_in' => 'بانتظار تسجيل الحضور',
        'locked_before_shift' => 'قبل موعد العمل',
        'missed_shift' => 'فات موعد العمل',
        'completed' => 'انتهى يوم العمل',
        'off_day' => 'يوم راحة',
        'on_leave' => 'إجازة معتمدة',
        'exempt' => 'غير خاضع للمواعيد',
        'no_schedule' => 'بدون موعد عمل',
    ];
    $mode = $attendanceState['mode'] ?? '';
    $schedule = $employee->workSchedule;
    $statusColors = [
        'pending' => 'bg-amber-50 text-amber-800 border-amber-200',
        'approved' => 'bg-emerald-50 text-emerald-800 border-emerald-200',
        'rejected' => 'bg-rose-50 text-rose-800 border-rose-200',
        'cancelled' => 'bg-slate-100 text-slate-600 border-slate-200',
    ];
?>

<?php $__env->startSection('content'); ?>
<div class="space-y-5 pb-10">
    
    <section class="rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-4 sm:px-5 py-4 bg-gradient-to-l from-slate-50 via-white to-indigo-50/40 border-b border-slate-200 flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
            <div class="flex items-start gap-3 min-w-0">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-indigo-500 to-slate-800 flex items-center justify-center text-white shadow-md flex-shrink-0 text-lg font-black">
                    <?php echo e(mb_substr($employee->name, 0, 1)); ?>

                </div>
                <div class="min-w-0">
                    <h1 class="text-xl sm:text-2xl font-black text-slate-900 truncate"><?php echo e($employee->name); ?></h1>
                    <div class="flex flex-wrap items-center gap-2 mt-2">
                        <?php if($employee->employeeJob): ?>
                            <span class="inline-flex items-center rounded-md px-2 py-0.5 text-xs font-bold bg-indigo-100 text-indigo-800 border border-indigo-200">
                                <?php echo e($employee->employeeJob->name); ?>

                            </span>
                        <?php endif; ?>
                        <span class="inline-flex items-center rounded-md px-2 py-0.5 text-xs font-bold border
                            <?php echo e(!empty($attendanceState['unlock']) ? 'bg-teal-50 border-teal-200 text-teal-800' : 'bg-slate-50 border-slate-200 text-slate-600'); ?>">
                            <?php echo e($modeLabels[$mode] ?? ($mode ?: '—')); ?>

                        </span>
                        <?php if($activeLeave): ?>
                            <span class="inline-flex items-center rounded-md px-2 py-0.5 text-xs font-bold bg-violet-100 text-violet-800 border border-violet-200">
                                في إجازة الآن
                            </span>
                        <?php endif; ?>
                    </div>
                    <p class="text-xs text-slate-600 mt-2">
                        فريق <strong><?php echo e($team->name); ?></strong>
                        <?php if($employee->email): ?> · <?php echo e($employee->email); ?> <?php endif; ?>
                        <?php if($employee->phone): ?> · <?php echo e($employee->phone); ?> <?php endif; ?>
                    </p>
                </div>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <a href="<?php echo e(route('employee.sales-manager.dashboard')); ?>"
                   class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-bold text-slate-700 rounded-lg border border-slate-300 bg-white hover:bg-slate-50">
                    <i class="fas fa-arrow-right"></i> لوحة الفريق
                </a>
                <a href="<?php echo e(route('employee.sales-manager.team.report', $employee)); ?>"
                   class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-bold text-white rounded-lg bg-emerald-600 hover:bg-emerald-700">
                    <i class="fas fa-chart-line"></i> تقرير الأداء
                </a>
                <a href="<?php echo e(route('employee.sales-manager.attendance.employee', $employee)); ?>"
                   class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-bold text-white rounded-lg bg-violet-600 hover:bg-violet-700">
                    <i class="fas fa-clock"></i> الحضور والفتح
                </a>
                <a href="<?php echo e(route('employee.sales-manager.leads.index', ['assignee' => $employee->id])); ?>"
                   class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-bold text-slate-700 rounded-lg border border-slate-300 bg-white hover:bg-slate-50">
                    <i class="fas fa-users"></i> عملاؤه
                </a>
            </div>
        </div>

        <div class="grid grid-cols-2 xl:grid-cols-5 gap-3 p-4">
            <?php $__currentLoopData = [
                ['label' => 'إجمالي العملاء', 'value' => $leadStats['total'], 'text' => 'text-slate-900'],
                ['label' => 'مفتوحة', 'value' => $leadStats['open'], 'text' => 'text-sky-700'],
                ['label' => 'فوز', 'value' => $leadStats['won'], 'text' => 'text-emerald-700'],
                ['label' => 'متابعات اليوم', 'value' => $leadStats['followups_today'], 'text' => 'text-amber-700'],
                ['label' => 'متابعات متأخرة', 'value' => $leadStats['followups_overdue'], 'text' => 'text-rose-700'],
            ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $card): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="rounded-xl border border-slate-200 bg-white p-3.5 shadow-sm">
                    <p class="text-[11px] font-semibold text-slate-500"><?php echo e($card['label']); ?></p>
                    <p class="text-xl font-black tabular-nums <?php echo e($card['text']); ?>"><?php echo e($card['value']); ?></p>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </section>

    <div class="grid grid-cols-1 xl:grid-cols-12 gap-5 items-start">
        
        <div class="xl:col-span-7 space-y-5">
            
            <section class="rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-4 sm:px-5 py-3 border-b border-slate-100 bg-slate-50/80">
                    <h2 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                        <i class="fas fa-business-time text-indigo-600"></i>
                        مواعيد العمل
                    </h2>
                </div>
                <div class="p-4 sm:p-5">
                    <?php if($schedule): ?>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div class="rounded-xl border border-indigo-100 bg-indigo-50/50 px-4 py-3">
                                <p class="text-[11px] font-bold text-indigo-700 mb-1">الجدول</p>
                                <p class="text-base font-black text-slate-900"><?php echo e($schedule->name); ?></p>
                            </div>
                            <div class="rounded-xl border border-slate-200 bg-slate-50/60 px-4 py-3">
                                <p class="text-[11px] font-bold text-slate-500 mb-1">ساعات العمل</p>
                                <p class="text-base font-black text-slate-900 tabular-nums"><?php echo e($schedule->timeRangeLabel()); ?></p>
                            </div>
                            <div class="rounded-xl border border-slate-200 bg-slate-50/60 px-4 py-3">
                                <p class="text-[11px] font-bold text-slate-500 mb-1">الساعات المطلوبة</p>
                                <p class="text-base font-black text-slate-900 tabular-nums"><?php echo e($schedule->required_hours ?? '—'); ?> ساعة</p>
                            </div>
                            <div class="rounded-xl border border-slate-200 bg-slate-50/60 px-4 py-3">
                                <p class="text-[11px] font-bold text-slate-500 mb-1">سماح التأخير</p>
                                <p class="text-base font-black text-slate-900 tabular-nums"><?php echo e($schedule->grace_minutes ?? 0); ?> دقيقة</p>
                            </div>
                            <div class="rounded-xl border border-amber-100 bg-amber-50/60 px-4 py-3 sm:col-span-2">
                                <p class="text-[11px] font-bold text-amber-800 mb-1">يوم الإجازة الأسبوعية</p>
                                <p class="text-base font-black text-slate-900">
                                    <?php echo e($employee->weeklyOffDayLabel() ?? 'غير محدد (سبت/أحد افتراضياً)'); ?>

                                </p>
                            </div>
                        </div>
                        <?php if($schedule->description): ?>
                            <p class="text-xs text-slate-600 mt-3 leading-relaxed"><?php echo e($schedule->description); ?></p>
                        <?php endif; ?>
                    <?php else: ?>
                        <div class="rounded-xl border-2 border-dashed border-slate-200 bg-slate-50/50 px-4 py-8 text-center">
                            <p class="text-sm font-semibold text-slate-700">لا يوجد جدول عمل مخصص</p>
                            <p class="text-xs text-slate-500 mt-1">يوم الراحة: <?php echo e($employee->weeklyOffDayLabel() ?? 'غير محدد'); ?></p>
                        </div>
                    <?php endif; ?>
                </div>
            </section>

            
            <section class="rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-4 sm:px-5 py-3 border-b border-slate-100 bg-slate-50/80 flex flex-wrap items-center justify-between gap-2">
                    <h2 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                        <i class="fas fa-umbrella-beach text-violet-600"></i>
                        الإجازات
                    </h2>
                    <span class="text-[11px] font-semibold text-slate-500"><?php echo e($leaves->count()); ?> سجل أخير</span>
                </div>

                <div class="p-4 sm:p-5 space-y-4">
                    <?php if($activeLeave): ?>
                        <div class="rounded-xl border border-violet-200 bg-violet-50 px-4 py-3">
                            <p class="text-sm font-bold text-violet-900">إجازة نشطة الآن</p>
                            <p class="text-xs text-violet-800 mt-1">
                                <?php echo e($activeLeave->type_label); ?> ·
                                <?php echo e($activeLeave->start_date->format('Y-m-d')); ?> → <?php echo e($activeLeave->end_date->format('Y-m-d')); ?>

                                (<?php echo e($activeLeave->days); ?> يوم)
                            </p>
                            <?php if($activeLeave->reason): ?>
                                <p class="text-xs text-slate-600 mt-1"><?php echo e($activeLeave->reason); ?></p>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <?php if($pendingLeaves->isNotEmpty()): ?>
                        <div>
                            <p class="text-[11px] font-bold text-amber-800 mb-2">طلبات قيد المراجعة (<?php echo e($pendingLeaves->count()); ?>)</p>
                            <ul class="space-y-2">
                                <?php $__currentLoopData = $pendingLeaves; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $leave): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <li class="rounded-lg border border-amber-200 bg-amber-50/50 px-3 py-2 text-sm flex flex-wrap justify-between gap-2">
                                        <span>
                                            <strong><?php echo e($leave->type_label); ?></strong>
                                            · <?php echo e($leave->start_date->format('Y-m-d')); ?> → <?php echo e($leave->end_date->format('Y-m-d')); ?>

                                        </span>
                                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full border <?php echo e($statusColors['pending']); ?>">قيد المراجعة</span>
                                    </li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <?php if($upcomingLeaves->isNotEmpty()): ?>
                        <div>
                            <p class="text-[11px] font-bold text-emerald-800 mb-2">إجازات قادمة معتمدة</p>
                            <ul class="space-y-2">
                                <?php $__currentLoopData = $upcomingLeaves; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $leave): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <li class="rounded-lg border border-emerald-100 bg-emerald-50/40 px-3 py-2 text-sm">
                                        <strong><?php echo e($leave->type_label); ?></strong>
                                        · <?php echo e($leave->start_date->format('Y-m-d')); ?> → <?php echo e($leave->end_date->format('Y-m-d')); ?>

                                        (<?php echo e($leave->days); ?> يوم)
                                    </li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <div class="overflow-x-auto rounded-xl border border-slate-200">
                        <table class="min-w-full text-sm">
                            <thead class="bg-slate-50 text-slate-600">
                                <tr>
                                    <th class="px-3 py-2.5 text-right font-semibold">النوع</th>
                                    <th class="px-3 py-2.5 text-right font-semibold">من</th>
                                    <th class="px-3 py-2.5 text-right font-semibold">إلى</th>
                                    <th class="px-3 py-2.5 text-right font-semibold">الأيام</th>
                                    <th class="px-3 py-2.5 text-right font-semibold">الحالة</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <?php $__empty_1 = true; $__currentLoopData = $leaves; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $leave): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr>
                                        <td class="px-3 py-2.5">
                                            <p class="font-semibold text-slate-900"><?php echo e($leave->type_label); ?></p>
                                            <?php if($leave->reason): ?>
                                                <p class="text-[11px] text-slate-500 max-w-[14rem] truncate" title="<?php echo e($leave->reason); ?>"><?php echo e($leave->reason); ?></p>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-3 py-2.5 tabular-nums whitespace-nowrap"><?php echo e($leave->start_date->format('Y-m-d')); ?></td>
                                        <td class="px-3 py-2.5 tabular-nums whitespace-nowrap"><?php echo e($leave->end_date->format('Y-m-d')); ?></td>
                                        <td class="px-3 py-2.5 tabular-nums"><?php echo e($leave->days); ?></td>
                                        <td class="px-3 py-2.5">
                                            <span class="text-[10px] font-bold px-2 py-0.5 rounded-full border <?php echo e($statusColors[$leave->status] ?? $statusColors['cancelled']); ?>">
                                                <?php echo e($leave->status_label); ?>

                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr>
                                        <td colspan="5" class="px-3 py-10 text-center text-slate-500 text-sm">لا توجد طلبات إجازة مسجّلة</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>
        </div>

        
        <aside class="xl:col-span-5 space-y-4 xl:sticky xl:top-4">
            <section class="rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-4 py-3 border-b border-slate-100 bg-slate-50/80">
                    <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                        <i class="fas fa-id-card text-sky-600"></i>
                        بيانات الموظف
                    </h3>
                </div>
                <dl class="p-4 space-y-2.5 text-sm">
                    <div class="rounded-lg border border-slate-200 bg-slate-50/50 px-3 py-2.5 flex justify-between gap-2">
                        <dt class="text-slate-500">الاسم</dt>
                        <dd class="font-semibold text-slate-900 text-left"><?php echo e($employee->name); ?></dd>
                    </div>
                    <div class="rounded-lg border border-slate-200 bg-slate-50/50 px-3 py-2.5 flex justify-between gap-2">
                        <dt class="text-slate-500 shrink-0">البريد</dt>
                        <dd class="font-semibold text-slate-900 text-left break-all"><?php echo e($employee->email ?? '—'); ?></dd>
                    </div>
                    <div class="rounded-lg border border-slate-200 bg-slate-50/50 px-3 py-2.5 flex justify-between gap-2">
                        <dt class="text-slate-500">الهاتف</dt>
                        <dd class="font-semibold text-slate-900"><?php echo e($employee->phone ?? '—'); ?></dd>
                    </div>
                    <div class="rounded-lg border border-slate-200 bg-slate-50/50 px-3 py-2.5 flex justify-between gap-2">
                        <dt class="text-slate-500">الوظيفة</dt>
                        <dd class="font-semibold text-slate-900"><?php echo e($employee->employeeJob->name ?? '—'); ?></dd>
                    </div>
                    <div class="rounded-lg border border-slate-200 bg-slate-50/50 px-3 py-2.5 flex justify-between gap-2">
                        <dt class="text-slate-500">يوم الراحة</dt>
                        <dd class="font-semibold text-slate-900"><?php echo e($employee->weeklyOffDayLabel() ?? '—'); ?></dd>
                    </div>
                </dl>
            </section>

            <section class="rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-4 py-3 border-b border-slate-100 bg-slate-50/80 flex items-center justify-between gap-2">
                    <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                        <i class="fas fa-calendar-check text-violet-600"></i>
                        آخر أيام الحضور
                    </h3>
                    <a href="<?php echo e(route('employee.sales-manager.attendance.employee', $employee)); ?>" class="text-[11px] font-bold text-violet-700 hover:underline">التفاصيل</a>
                </div>
                <ul class="divide-y divide-slate-100">
                    <?php $__empty_1 = true; $__currentLoopData = $recentAttendance; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rec): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <li class="px-4 py-2.5 flex items-center justify-between gap-2 text-sm">
                            <span class="font-medium text-slate-800 tabular-nums"><?php echo e($rec->work_date?->format('Y-m-d')); ?></span>
                            <span class="text-xs text-slate-500 tabular-nums">
                                <?php echo e($rec->clock_in_at?->format('H:i') ?? '—'); ?>

                                →
                                <?php echo e($rec->clock_out_at?->format('H:i') ?? '—'); ?>

                            </span>
                            <span class="text-[10px] font-bold text-slate-600">
                                <?php echo e(\App\Models\EmployeeAttendanceRecord::statusLabels()[$rec->status] ?? $rec->status); ?>

                            </span>
                        </li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <li class="px-4 py-8 text-center text-sm text-slate-500">لا سجلات حضور بعد</li>
                    <?php endif; ?>
                </ul>
            </section>
        </aside>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.employee', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\employee\sales-manager\team\show.blade.php ENDPATH**/ ?>