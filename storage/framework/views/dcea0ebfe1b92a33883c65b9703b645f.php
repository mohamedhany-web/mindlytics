

<?php $__env->startSection('title', 'تقرير أداء — '.$employee->name); ?>
<?php $__env->startSection('header', 'تقرير أداء الموظف'); ?>

<?php
    $summary = $employeeReport['summary'];
    $dailyRows = $employeeReport['daily_rows'];
    $groupBreakdown = $employeeReport['group_breakdown'] ?? [];
    $leadsWithContact = $employeeReport['leads_with_contact'] ?? collect();
    $period = $employeeReport['period_report'] ?? [];
    $pillars = $period['pillars'] ?? [];
    $insights = $employeeReport['insights'] ?? [];
    $deductions = $employeeReport['deductions'] ?? ['items' => collect(), 'total_amount' => 0, 'count' => 0];
    $whatsapp = $employeeReport['whatsapp'] ?? [];
    $attendanceIssues = $employeeReport['attendance_issues'] ?? [];
    $auditTrail = $employeeReport['audit_trail'] ?? collect();
    $charts = $employeeReport['charts'] ?? [];
    $toneMap = [
        'emerald' => 'border-emerald-200 bg-emerald-50 text-emerald-900',
        'amber' => 'border-amber-200 bg-amber-50 text-amber-900',
        'rose' => 'border-rose-200 bg-rose-50 text-rose-900',
        'sky' => 'border-sky-200 bg-sky-50 text-sky-900',
        'violet' => 'border-violet-200 bg-violet-50 text-violet-900',
        'slate' => 'border-slate-200 bg-slate-50 text-slate-800',
    ];
?>

<?php $__env->startSection('content'); ?>
<div class="space-y-5 pb-10">
    
    <section class="rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-4 sm:px-5 py-4 bg-gradient-to-l from-slate-50 via-white to-emerald-50/50 border-b border-slate-200 flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
            <div class="min-w-0">
                <p class="text-[11px] font-bold text-emerald-700 mb-1">تقرير أداء قوي · Insights</p>
                <h1 class="text-xl sm:text-2xl font-black text-slate-900 truncate"><?php echo e($employee->name); ?></h1>
                <p class="text-xs text-slate-600 mt-1">
                    فريق <strong><?php echo e($team->name); ?></strong>
                    · من <?php echo e($employeeReport['start']->format('Y-m-d')); ?>

                    إلى <?php echo e($employeeReport['end']->format('Y-m-d')); ?>

                    · <?php echo e($employeeReport['lead_scope_label']); ?>

                </p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <a href="<?php echo e(route('employee.sales-manager.team.show', $employee)); ?>"
                   class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-bold text-slate-700 rounded-lg border border-slate-300 bg-white hover:bg-slate-50">
                    <i class="fas fa-user"></i> ملف الموظف
                </a>
                <a href="<?php echo e(route('employee.sales-manager.team.report.pdf', array_merge(['employee' => $employee->id], $filters))); ?>"
                   class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-bold text-white rounded-lg bg-rose-600 hover:bg-rose-700">
                    <i class="fas fa-file-pdf"></i> تحميل PDF
                </a>
            </div>
        </div>

        <form method="get" action="<?php echo e(route('employee.sales-manager.team.report', $employee)); ?>" class="p-4 grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-5 gap-3 items-end">
            <div>
                <label class="block text-[11px] font-bold text-slate-600 mb-1">من تاريخ</label>
                <input type="date" name="date_from" value="<?php echo e($filters['date_from']); ?>"
                       class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-[11px] font-bold text-slate-600 mb-1">إلى تاريخ</label>
                <input type="date" name="date_to" value="<?php echo e($filters['date_to']); ?>"
                       class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-[11px] font-bold text-slate-600 mb-1">نطاق العملاء</label>
                <select name="lead_scope" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                    <option value="touched" <?php if($filters['lead_scope'] === 'touched'): echo 'selected'; endif; ?>>تم التعامل معهم في الفترة</option>
                    <option value="new" <?php if($filters['lead_scope'] === 'new'): echo 'selected'; endif; ?>>عملاء جدد في الفترة</option>
                    <option value="transferred_from_admin" <?php if($filters['lead_scope'] === 'transferred_from_admin'): echo 'selected'; endif; ?>>محوّلون من الإدارة</option>
                    <option value="in_groups" <?php if($filters['lead_scope'] === 'in_groups'): echo 'selected'; endif; ?>>كل مجموعات العملاء</option>
                </select>
            </div>
            <div>
                <label class="block text-[11px] font-bold text-slate-600 mb-1">مجموعة محددة</label>
                <select name="group_id" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                    <option value="">كل المجموعات</option>
                    <?php $__currentLoopData = $repGroups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $g): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($g->id); ?>" <?php if((int)($filters['group_id'] ?? 0) === (int)$g->id): echo 'selected'; endif; ?>><?php echo e($g->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-lg bg-emerald-600 hover:bg-emerald-700 px-4 py-2.5 text-sm font-bold text-white">
                <i class="fas fa-sync-alt"></i> تحديث التقرير
            </button>
        </form>
    </section>

    
    <section class="rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-4 py-3 border-b border-slate-200 bg-gradient-to-l from-indigo-50 to-white">
            <h2 class="text-base font-black text-slate-900"><i class="fas fa-lightbulb text-amber-500 ml-1"></i> Insights النظام</h2>
            <p class="text-xs text-slate-600">قراءة تلقائية للأداء والمشاكل والخصومات وKPI من بيانات الفترة.</p>
        </div>
        <div class="p-4 grid grid-cols-1 md:grid-cols-2 gap-3">
            <?php $__currentLoopData = $insights; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $insight): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="rounded-xl border p-3 <?php echo e($toneMap[$insight['tone'] ?? 'slate'] ?? $toneMap['slate']); ?>">
                    <p class="text-xs font-black mb-1"><?php echo e($insight['title']); ?></p>
                    <p class="text-sm leading-relaxed"><?php echo e($insight['body']); ?></p>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </section>

    
    <section class="grid grid-cols-2 lg:grid-cols-4 xl:grid-cols-6 gap-3">
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-3">
            <p class="text-[11px] text-emerald-800 font-semibold">درجة KPI</p>
            <p class="text-2xl font-black text-emerald-900 tabular-nums"><?php echo e($summary['composite_score'] ?? '—'); ?></p>
        </div>
        <div class="rounded-xl border border-violet-200 bg-violet-50 p-3">
            <p class="text-[11px] text-violet-800 font-semibold">Leads / تواصل</p>
            <p class="text-2xl font-black text-violet-900 tabular-nums"><?php echo e($summary['leads_in_scope']); ?> <span class="text-sm">/ <?php echo e($summary['leads_contacted_in_period'] ?? 0); ?></span></p>
        </div>
        <div class="rounded-xl border border-amber-200 bg-amber-50 p-3">
            <p class="text-[11px] text-amber-800 font-semibold">أنشطة CRM</p>
            <p class="text-2xl font-black text-amber-900 tabular-nums"><?php echo e($summary['total_activities']); ?></p>
            <p class="text-[10px] text-amber-700 mt-1">مكالمات <?php echo e($summary['calls']); ?> · متابعات <?php echo e($summary['followups']); ?></p>
        </div>
        <div class="rounded-xl border border-sky-200 bg-sky-50 p-3">
            <p class="text-[11px] text-sky-800 font-semibold">واتساب</p>
            <p class="text-2xl font-black text-sky-900 tabular-nums"><?php echo e($whatsapp['outbound'] ?? 0); ?></p>
            <p class="text-[10px] text-sky-700 mt-1">وارد <?php echo e($whatsapp['inbound'] ?? 0); ?> · محادثات <?php echo e($whatsapp['conversations'] ?? 0); ?></p>
        </div>
        <div class="rounded-xl border border-rose-200 bg-rose-50 p-3">
            <p class="text-[11px] text-rose-800 font-semibold">خصومات</p>
            <p class="text-2xl font-black text-rose-900 tabular-nums"><?php echo e(number_format((float)($deductions['total_amount'] ?? 0), 0)); ?></p>
            <p class="text-[10px] text-rose-700 mt-1"><?php echo e($deductions['count'] ?? 0); ?> خصم</p>
        </div>
        <div class="rounded-xl border border-slate-200 bg-slate-50 p-3">
            <p class="text-[11px] text-slate-700 font-semibold">دخول النظام</p>
            <p class="text-2xl font-black text-slate-900 tabular-nums"><?php echo e($summary['days_with_login']); ?> <span class="text-sm">/ <?php echo e($summary['work_days']); ?></span></p>
            <p class="text-[10px] text-slate-600 mt-1">تقارير ناقصة: <?php echo e($summary['daily_reports_missing']); ?></p>
        </div>
    </section>

    
    <?php if(count($pillars) > 0): ?>
    <section class="rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-4 py-3 border-b border-slate-200 bg-slate-50">
            <h2 class="text-base font-black text-slate-900">أركان KPI</h2>
            <p class="text-xs text-slate-600">تفصيل الدرجات التي تكوّن المؤشر المركّب.</p>
        </div>
        <div class="p-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
            <?php $__currentLoopData = $pillars; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $pillar): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php $score = (float) ($pillar['score'] ?? 0); ?>
                <div class="rounded-xl border border-slate-200 p-3">
                    <div class="flex items-center justify-between gap-2 mb-2">
                        <span class="text-xs font-bold text-slate-800"><?php echo e($pillar['label'] ?? $key); ?></span>
                        <span class="text-lg font-black tabular-nums <?php echo e($score >= 70 ? 'text-emerald-700' : ($score >= 50 ? 'text-amber-700' : 'text-rose-700')); ?>"><?php echo e(round($score, 1)); ?></span>
                    </div>
                    <div class="h-2 rounded-full bg-slate-100 overflow-hidden">
                        <div class="h-full rounded-full <?php echo e($score >= 70 ? 'bg-emerald-500' : ($score >= 50 ? 'bg-amber-500' : 'bg-rose-500')); ?>" style="width: <?php echo e(min(100, max(0, $score))); ?>%"></div>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </section>
    <?php endif; ?>

    
    <section class="rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-4 py-3 border-b border-slate-200 bg-slate-50">
            <h2 class="text-base font-black text-slate-900">رسوم بيانية</h2>
        </div>
        <div class="p-4 grid grid-cols-1 lg:grid-cols-2 gap-4">
            <div class="rounded-xl border border-slate-100 p-3">
                <p class="text-xs font-bold text-slate-700 mb-2">النشاط اليومي</p>
                <canvas id="chartRepDaily" height="160"></canvas>
            </div>
            <div class="rounded-xl border border-slate-100 p-3">
                <p class="text-xs font-bold text-slate-700 mb-2">توزيع المراحل</p>
                <canvas id="chartRepStages" height="160"></canvas>
            </div>
            <div class="rounded-xl border border-slate-100 p-3">
                <p class="text-xs font-bold text-slate-700 mb-2">أنواع الأنشطة</p>
                <canvas id="chartRepActivities" height="160"></canvas>
            </div>
            <div class="rounded-xl border border-slate-100 p-3">
                <p class="text-xs font-bold text-slate-700 mb-2">أركان KPI</p>
                <canvas id="chartRepPillars" height="160"></canvas>
            </div>
        </div>
    </section>

    
    <section class="rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-4 py-3 border-b border-slate-200 bg-rose-50 flex flex-wrap items-center justify-between gap-2">
            <div>
                <h2 class="text-base font-black text-slate-900">الخصومات اليومية في الفترة</h2>
                <p class="text-xs text-slate-600">كل خصم راتب مسجّل على الموظف (تقارير / حضور / أخرى).</p>
            </div>
            <span class="text-xs font-bold text-rose-800 bg-white border border-rose-200 rounded-lg px-2.5 py-1">
                الإجمالي: <?php echo e(number_format((float)($deductions['total_amount'] ?? 0), 2)); ?> ج.م
            </span>
        </div>
        <div class="px-4 py-3 grid grid-cols-2 sm:grid-cols-4 gap-2 text-xs">
            <div class="rounded-lg bg-slate-50 border border-slate-200 px-3 py-2">خصومات تقارير يومية: <strong><?php echo e($deductions['daily_report_penalties'] ?? 0); ?></strong></div>
            <div class="rounded-lg bg-slate-50 border border-slate-200 px-3 py-2">خصومات حضور: <strong><?php echo e($deductions['attendance_penalties'] ?? 0); ?></strong></div>
            <div class="rounded-lg bg-slate-50 border border-slate-200 px-3 py-2">تأخير: <strong><?php echo e($attendanceIssues['late_days'] ?? 0); ?></strong> يوم</div>
            <div class="rounded-lg bg-slate-50 border border-slate-200 px-3 py-2">غياب سجل حضور: <strong><?php echo e($attendanceIssues['absent_days'] ?? 0); ?></strong></div>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-xs">
                <thead class="bg-rose-50 text-rose-900 border-b border-rose-200">
                    <tr>
                        <th class="px-3 py-2 text-right">التاريخ</th>
                        <th class="px-3 py-2 text-right">العنوان</th>
                        <th class="px-3 py-2 text-center">النوع</th>
                        <th class="px-3 py-2 text-center">المبلغ</th>
                        <th class="px-3 py-2 text-right">ملاحظات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php $__empty_1 = true; $__currentLoopData = $deductions['items']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="hover:bg-rose-50/40">
                            <td class="px-3 py-2 tabular-nums whitespace-nowrap"><?php echo e($row->deduction_date?->format('Y-m-d')); ?></td>
                            <td class="px-3 py-2 font-semibold text-slate-900"><?php echo e($row->title); ?></td>
                            <td class="px-3 py-2 text-center"><?php echo e($row->type ?: '—'); ?></td>
                            <td class="px-3 py-2 text-center tabular-nums font-bold text-rose-700"><?php echo e(number_format((float)$row->amount, 2)); ?></td>
                            <td class="px-3 py-2 text-slate-600"><?php echo e(\Illuminate\Support\Str::limit($row->notes ?: ($row->description ?? '—'), 80)); ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr><td colspan="5" class="px-4 py-8 text-center text-slate-500">لا توجد خصومات في هذه الفترة.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>

    
    <section class="rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-4 py-3 border-b border-slate-200 bg-slate-50">
            <h2 class="text-base font-black text-slate-900">الجدول اليومي التفصيلي</h2>
            <p class="text-xs text-slate-600">كل صف = يوم واحد: دخول النظام، نشاط CRM، واتساب، تقرير يومي.</p>
        </div>
        <div class="overflow-x-auto max-h-[520px] overflow-y-auto">
            <table class="min-w-[960px] w-full text-xs">
                <thead class="sticky top-0 z-10 bg-slate-800 text-white">
                    <tr>
                        <th class="px-3 py-2 text-right font-semibold">التاريخ</th>
                        <th class="px-3 py-2 text-center font-semibold">اليوم</th>
                        <th class="px-3 py-2 text-center font-semibold">الحالة</th>
                        <th class="px-3 py-2 text-center font-semibold">دخول</th>
                        <th class="px-3 py-2 text-center font-semibold">مكالمات</th>
                        <th class="px-3 py-2 text-center font-semibold">اجتماعات</th>
                        <th class="px-3 py-2 text-center font-semibold">متابعات</th>
                        <th class="px-3 py-2 text-center font-semibold">واتساب</th>
                        <th class="px-3 py-2 text-center font-semibold">Leads جديدة</th>
                        <th class="px-3 py-2 text-center font-semibold">من الإدارة</th>
                        <th class="px-3 py-2 text-center font-semibold">تقرير يومي</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php $__currentLoopData = $dailyRows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            $rowBg = match($row['status_tone']) {
                                'emerald' => 'bg-emerald-50/60',
                                'rose' => 'bg-rose-50/70',
                                'amber' => 'bg-amber-50/70',
                                default => 'bg-slate-50/40',
                            };
                            $badge = match($row['status_tone']) {
                                'emerald' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                                'rose' => 'bg-rose-100 text-rose-800 border-rose-200',
                                'amber' => 'bg-amber-100 text-amber-800 border-amber-200',
                                default => 'bg-slate-100 text-slate-600 border-slate-200',
                            };
                        ?>
                        <tr class="<?php echo e($rowBg); ?>">
                            <td class="px-3 py-2 font-mono text-slate-700"><?php echo e($row['date']); ?></td>
                            <td class="px-3 py-2 text-center text-slate-600"><?php echo e($row['day_name']); ?></td>
                            <td class="px-3 py-2 text-center">
                                <span class="inline-flex px-2 py-0.5 rounded-full border text-[10px] font-bold <?php echo e($badge); ?>"><?php echo e($row['status_label']); ?></span>
                            </td>
                            <td class="px-3 py-2 text-center"><?php echo e($row['logged_in'] ? '✓' : '—'); ?></td>
                            <td class="px-3 py-2 text-center tabular-nums"><?php echo e($row['calls'] ?: '—'); ?></td>
                            <td class="px-3 py-2 text-center tabular-nums"><?php echo e($row['meetings'] ?: '—'); ?></td>
                            <td class="px-3 py-2 text-center tabular-nums"><?php echo e($row['followups'] ?: '—'); ?></td>
                            <td class="px-3 py-2 text-center tabular-nums"><?php echo e($row['whatsapp'] ?: '—'); ?></td>
                            <td class="px-3 py-2 text-center tabular-nums"><?php echo e($row['leads_created'] ?: '—'); ?></td>
                            <td class="px-3 py-2 text-center tabular-nums"><?php echo e($row['leads_from_admin'] ?: '—'); ?></td>
                            <td class="px-3 py-2 text-center"><?php echo e($row['daily_report_label']); ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
        <?php if(count($employeeReport['absent_work_days']) > 0): ?>
            <div class="mx-4 mb-4 mt-2 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-900">
                <p class="font-bold mb-1"><i class="fas fa-user-clock ml-1"></i> أيام عمل بدون دخول للنظام:</p>
                <p class="text-xs leading-relaxed"><?php echo e(implode('، ', $employeeReport['absent_work_days'])); ?></p>
            </div>
        <?php endif; ?>
    </section>

    
    <?php if(count($groupBreakdown) > 0): ?>
    <section class="rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-4 py-3 border-b border-slate-200 bg-slate-50">
            <h2 class="text-base font-black text-slate-900">مجموعات العملاء</h2>
            <p class="text-xs text-slate-600">كم Lead في كل مجموعة وحالة التواصل.</p>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-[900px] w-full text-xs">
                <thead class="bg-violet-50 text-violet-900 border-b border-violet-200">
                    <tr>
                        <th class="px-3 py-2 text-right font-semibold">المجموعة</th>
                        <th class="px-3 py-2 text-center font-semibold">إجمالي</th>
                        <th class="px-3 py-2 text-center font-semibold">تواصل في الفترة</th>
                        <th class="px-3 py-2 text-center font-semibold">لم يُتواصل في الفترة</th>
                        <th class="px-3 py-2 text-center font-semibold">لم يُتواصل أبداً</th>
                        <th class="px-3 py-2 text-center font-semibold">تفصيل</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php $__currentLoopData = $groupBreakdown; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php $g = $row['group']; ?>
                        <tr class="hover:bg-violet-50/40">
                            <td class="px-3 py-2 font-bold text-slate-900"><?php echo e($g->name); ?></td>
                            <td class="px-3 py-2 text-center tabular-nums font-bold"><?php echo e($row['total']); ?></td>
                            <td class="px-3 py-2 text-center tabular-nums text-emerald-700 font-bold"><?php echo e($row['contacted_in_period']); ?></td>
                            <td class="px-3 py-2 text-center tabular-nums text-amber-700"><?php echo e($row['not_contacted_in_period']); ?></td>
                            <td class="px-3 py-2 text-center tabular-nums text-rose-700"><?php echo e($row['not_contacted']); ?></td>
                            <td class="px-3 py-2 text-center">
                                <a href="<?php echo e(route('employee.sales-manager.team.report', [
                                    'employee' => $employee->id,
                                    'date_from' => $filters['date_from'],
                                    'date_to' => $filters['date_to'],
                                    'lead_scope' => 'in_groups',
                                    'group_id' => $g->id,
                                ])); ?>" class="text-emerald-700 font-semibold hover:underline">عرض</a>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
    </section>
    <?php endif; ?>

    
    <section class="rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-4 py-3 border-b border-slate-200 bg-slate-50">
            <h2 class="text-base font-black text-slate-900">العملاء المحتملون (<?php echo e($employeeReport['leads']->count()); ?>)</h2>
            <p class="text-xs text-slate-600">حالة التواصل لكل Lead حسب الفلتر.</p>
        </div>
        <div class="overflow-x-auto max-h-96 overflow-y-auto">
            <table class="min-w-full text-xs">
                <thead class="sticky top-0 bg-violet-50 text-violet-900 border-b border-violet-200">
                    <tr>
                        <th class="px-3 py-2 text-right">الاسم</th>
                        <th class="px-3 py-2 text-right">الهاتف</th>
                        <th class="px-3 py-2 text-center">المجموعة</th>
                        <th class="px-3 py-2 text-center">المرحلة</th>
                        <th class="px-3 py-2 text-center">حالة التواصل</th>
                        <th class="px-3 py-2 text-center">آخر تواصل</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php $__empty_1 = true; $__currentLoopData = $leadsWithContact; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php
                            $lead = $item['lead'];
                            $contactBadge = match($item['contact_tone']) {
                                'emerald' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                                'amber' => 'bg-amber-100 text-amber-800 border-amber-200',
                                default => 'bg-rose-100 text-rose-800 border-rose-200',
                            };
                        ?>
                        <tr class="hover:bg-violet-50/40">
                            <td class="px-3 py-2 font-semibold">
                                <a href="<?php echo e(route('employee.sales-manager.leads.show', $lead)); ?>" class="hover:text-emerald-700 hover:underline"><?php echo e($lead->name); ?></a>
                            </td>
                            <td class="px-3 py-2 font-mono dir-ltr text-right"><?php echo e($lead->phone ?? '—'); ?></td>
                            <td class="px-3 py-2 text-center"><?php echo e($lead->group?->name ?? '—'); ?></td>
                            <td class="px-3 py-2 text-center"><?php echo e(\App\Models\SalesLead::stageLabel($lead->stage)); ?></td>
                            <td class="px-3 py-2 text-center">
                                <span class="inline-flex px-2 py-0.5 rounded-full border text-[10px] font-bold <?php echo e($contactBadge); ?>"><?php echo e($item['contact_label']); ?></span>
                            </td>
                            <td class="px-3 py-2 text-center tabular-nums"><?php echo e($lead->last_contacted_at?->format('Y-m-d') ?? '—'); ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr><td colspan="6" class="px-4 py-8 text-center text-slate-500">لا توجد Leads حسب الفلتر.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>

    
    <section class="rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-4 py-3 border-b border-slate-200 bg-slate-50">
            <h2 class="text-base font-black text-slate-900">سجل أنشطة CRM (<?php echo e($employeeReport['activities']->count()); ?>)</h2>
        </div>
        <div class="overflow-x-auto max-h-96 overflow-y-auto">
            <table class="min-w-full text-xs">
                <thead class="sticky top-0 bg-amber-50 text-amber-900 border-b border-amber-200">
                    <tr>
                        <th class="px-3 py-2 text-right">التاريخ</th>
                        <th class="px-3 py-2 text-center">النوع</th>
                        <th class="px-3 py-2 text-right">العميل</th>
                        <th class="px-3 py-2 text-right">التفاصيل</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php $__empty_1 = true; $__currentLoopData = $employeeReport['activities']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $activity): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="hover:bg-amber-50/40">
                            <td class="px-3 py-2 tabular-nums whitespace-nowrap"><?php echo e($activity->created_at?->format('Y-m-d H:i')); ?></td>
                            <td class="px-3 py-2 text-center font-semibold"><?php echo e(\App\Models\SalesActivity::typeLabel($activity->type)); ?></td>
                            <td class="px-3 py-2"><?php echo e($activity->lead?->name ?? '—'); ?></td>
                            <td class="px-3 py-2 text-slate-600"><?php echo e(\Illuminate\Support\Str::limit($activity->title ?: ($activity->body ?? '—'), 100)); ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr><td colspan="4" class="px-4 py-8 text-center text-slate-500">لا توجد أنشطة مسجّلة.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>

    
    <section class="rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-4 py-3 border-b border-slate-200 bg-slate-50">
            <h2 class="text-base font-black text-slate-900">تتبع أحداث النظام (Audit)</h2>
            <p class="text-xs text-slate-600">آخر عمليات المبيعات وتسجيل الدخول المسجّلة على حساب الموظف.</p>
        </div>
        <div class="overflow-x-auto max-h-80 overflow-y-auto">
            <table class="min-w-full text-xs">
                <thead class="sticky top-0 bg-slate-100 text-slate-800 border-b border-slate-200">
                    <tr>
                        <th class="px-3 py-2 text-right">الوقت</th>
                        <th class="px-3 py-2 text-center">الإجراء</th>
                        <th class="px-3 py-2 text-right">الوصف</th>
                        <th class="px-3 py-2 text-center">IP</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php $__empty_1 = true; $__currentLoopData = $auditTrail; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td class="px-3 py-2 tabular-nums whitespace-nowrap"><?php echo e($log->created_at?->format('Y-m-d H:i')); ?></td>
                            <td class="px-3 py-2 text-center font-mono text-[10px]"><?php echo e($log->action); ?></td>
                            <td class="px-3 py-2 text-slate-700"><?php echo e($log->action_description); ?></td>
                            <td class="px-3 py-2 text-center font-mono dir-ltr"><?php echo e($log->ip_address ?? '—'); ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr><td colspan="4" class="px-4 py-8 text-center text-slate-500">لا يوجد سجل أحداث في الفترة.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>

    <p class="text-[11px] text-slate-500 text-center">
        تم توليد التقرير <?php echo e($employeeReport['generated_at']->format('Y-m-d H:i')); ?>

        <?php if(!empty($employeeReport['exported_by'])): ?> بواسطة <?php echo e($employeeReport['exported_by']); ?> <?php endif; ?>
    </p>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
(() => {
    const charts = <?php echo json_encode($charts, 15, 512) ?>;
    const palette = { sky: '#0ea5e9', indigo: '#6366f1', emerald: '#10b981', amber: '#f59e0b', rose: '#f43f5e', violet: '#8b5cf6' };
    const doughnutColors = [palette.sky, palette.indigo, palette.emerald, palette.amber, palette.rose, palette.violet, '#64748b'];
    const baseOptions = { responsive: true, plugins: { legend: { position: 'bottom', labels: { boxWidth: 12, font: { size: 10 } } } } };
    const hasData = (arr) => Array.isArray(arr) && arr.some(v => Number(v) > 0);

    const daily = charts.daily || {};
    if (hasData(daily.leads) || hasData(daily.activities) || hasData(daily.wins)) {
        new Chart(document.getElementById('chartRepDaily'), {
            type: 'line',
            data: {
                labels: daily.labels || [],
                datasets: [
                    { label: 'Leads', data: daily.leads || [], borderColor: palette.sky, tension: 0.3 },
                    { label: 'أنشطة', data: daily.activities || [], borderColor: palette.indigo, tension: 0.3 },
                    { label: 'فوز', data: daily.wins || [], borderColor: palette.emerald, tension: 0.3 },
                ],
            },
            options: { ...baseOptions, scales: { y: { beginAtZero: true } } },
        });
    }

    const stages = charts.stages || {};
    if (hasData(stages.values)) {
        new Chart(document.getElementById('chartRepStages'), {
            type: 'doughnut',
            data: { labels: stages.labels || [], datasets: [{ data: stages.values || [], backgroundColor: doughnutColors, borderWidth: 2 }] },
            options: baseOptions,
        });
    }

    const act = charts.activities_by_type || {};
    if (hasData(act.values)) {
        new Chart(document.getElementById('chartRepActivities'), {
            type: 'doughnut',
            data: { labels: act.labels || [], datasets: [{ data: act.values || [], backgroundColor: doughnutColors, borderWidth: 2 }] },
            options: baseOptions,
        });
    }

    const pillars = charts.pillars || {};
    if (hasData(pillars.scores || pillars.values)) {
        new Chart(document.getElementById('chartRepPillars'), {
            type: 'bar',
            data: {
                labels: pillars.labels || [],
                datasets: [{ label: 'درجة', data: pillars.scores || pillars.values || [], backgroundColor: palette.indigo }],
            },
            options: { ...baseOptions, scales: { y: { beginAtZero: true, max: 100 } } },
        });
    }
})();
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.employee', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\employee\sales-manager\team\report.blade.php ENDPATH**/ ?>