<?php $__env->startSection('title', 'مراقبة KPIs المبيعات'); ?>
<?php $__env->startSection('header', 'مراقبة KPIs المبيعات'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $repCount = count($rows ?? []);
    $statCards = [
        ['label' => 'متابعات متأخرة', 'value' => number_format((int) ($slaSummary['overdue_followups'] ?? 0)), 'icon' => 'fas fa-clock', 'bg' => 'bg-rose-100', 'text' => 'text-rose-600', 'description' => 'SLA — إجمالي'],
        ['label' => 'فرص راكدة', 'value' => number_format((int) ($slaSummary['stale_open_leads'] ?? 0)), 'icon' => 'fas fa-pause-circle', 'bg' => 'bg-amber-100', 'text' => 'text-amber-600', 'description' => 'بلا تواصل'],
        ['label' => 'متوسط أول رد', 'value' => ($slaSummary['avg_response_minutes'] ?? 0) > 0 ? number_format((float) $slaSummary['avg_response_minutes'], 1) . ' د' : '—', 'icon' => 'fas fa-reply', 'bg' => 'bg-sky-100', 'text' => 'text-sky-600', 'description' => 'بالدقائق'],
        ['label' => 'موظفو مبيعات', 'value' => number_format($repCount), 'icon' => 'fas fa-users', 'bg' => 'bg-emerald-100', 'text' => 'text-emerald-600', 'description' => 'نشطون'],
    ];
    $selectClass = 'rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500 focus:border-sky-500';
?>

<div class="space-y-6">
    
    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-4 py-4 bg-slate-50 border-b border-slate-200 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-emerald-500 to-emerald-600 flex items-center justify-center text-white shadow-md">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div>
                    <h2 class="text-xl font-black text-slate-900">مراقبة KPIs المبيعات</h2>
                    <p class="text-xs text-slate-600">المؤشر المركّب، الإيراد الشهري، التنبيهات، وتحليل المصادر.</p>
                </div>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <a href="<?php echo e(route('admin.sales.kpi.targets')); ?>" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-white rounded-xl bg-emerald-600 hover:bg-emerald-700">
                    <i class="fas fa-sliders-h"></i>
                    ضبط الأهداف
                </a>
                <a href="<?php echo e(route('admin.sales.leads.index')); ?>" class="inline-flex items-center gap-2 px-3 py-2 text-sm font-semibold text-slate-700 rounded-xl border border-slate-300 hover:bg-white">
                    <i class="fas fa-user-tag text-emerald-600"></i>
                    العملاء المحتملون
                </a>
            </div>
        </div>
        <div class="grid grid-cols-2 xl:grid-cols-4 gap-3 p-4">
            <?php $__currentLoopData = $statCards; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $card): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                    <div class="flex items-center justify-between gap-2">
                        <div class="min-w-0">
                            <p class="text-xs font-semibold text-slate-600 truncate"><?php echo e($card['label']); ?></p>
                            <p class="text-xl font-black text-slate-900 truncate tabular-nums"><?php echo e($card['value']); ?></p>
                        </div>
                        <div class="w-9 h-9 rounded-lg <?php echo e($card['bg']); ?> flex items-center justify-center <?php echo e($card['text']); ?> flex-shrink-0">
                            <i class="<?php echo e($card['icon']); ?> text-sm"></i>
                        </div>
                    </div>
                    <p class="text-[11px] text-slate-500 mt-1 truncate"><?php echo e($card['description']); ?></p>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </section>

    
    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-4 py-3 border-b border-slate-200 bg-slate-50 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
            <div>
                <h3 class="text-base font-black text-slate-900 flex items-center gap-2">
                    <i class="fas fa-chart-bar text-rose-600"></i>
                    أسباب الخسارة
                </h3>
                <p class="text-xs text-slate-600">الشهر الحالي — أبرز الأسباب.</p>
            </div>
            <span class="text-xs font-semibold text-rose-700 bg-rose-50 px-2.5 py-1 rounded-lg border border-rose-200"><?php echo e(($lossReasons ?? collect())->count()); ?> سبب</span>
        </div>
        <div class="p-4">
            <?php if(($lossReasons ?? collect())->isEmpty()): ?>
                <div class="text-center py-8">
                    <div class="w-14 h-14 mx-auto mb-3 rounded-xl bg-slate-100 flex items-center justify-center text-slate-400">
                        <i class="fas fa-chart-bar text-xl"></i>
                    </div>
                    <p class="text-sm font-semibold text-slate-900">لا صفقات خسارة مسجلة</p>
                    <p class="text-xs text-slate-500 mt-1">لا توجد أسباب خسارة مسجلة خلال هذا الشهر.</p>
                </div>
            <?php else: ?>
                <?php $maxReason = max(1, (int) $lossReasons->max('total')); ?>
                <div class="space-y-3">
                    <?php $__currentLoopData = $lossReasons; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php $w = (int) round(($row->total / $maxReason) * 100); ?>
                        <div>
                            <div class="flex justify-between gap-3 mb-1 text-sm">
                                <span class="font-semibold text-slate-800 truncate"><?php echo e($row->lost_reason); ?></span>
                                <span class="tabular-nums text-rose-700 font-bold"><?php echo e($row->total); ?></span>
                            </div>
                            <div class="h-2 rounded-full bg-slate-100 overflow-hidden">
                                <div class="h-full rounded-full bg-rose-500" style="width: <?php echo e($w); ?>%"></div>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            <?php endif; ?>
        </div>
    </section>

    
    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-4 py-3 border-b border-slate-200 bg-slate-50 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
            <div>
                <h3 class="text-base font-black text-slate-900 flex items-center gap-2">
                    <i class="fas fa-bullseye text-violet-600"></i>
                    أداء المصادر
                </h3>
                <p class="text-xs text-slate-600">Leads / Won / Conversion / Revenue — الشهر الحالي.</p>
            </div>
        </div>
        <div class="overflow-x-auto">
            <?php if(($sourcePerformance ?? collect())->isEmpty()): ?>
                <div class="text-center py-8 px-4">
                    <p class="text-sm text-slate-500">لا توجد بيانات مصادر كافية خلال هذا الشهر.</p>
                </div>
            <?php else: ?>
                <table class="min-w-[700px] w-full text-sm">
                    <thead>
                        <tr class="bg-slate-50 text-slate-700 border-b border-slate-200">
                            <th class="px-4 py-3 text-right font-semibold">المصدر</th>
                            <th class="px-4 py-3 text-center font-semibold">Leads</th>
                            <th class="px-4 py-3 text-center font-semibold">Won</th>
                            <th class="px-4 py-3 text-center font-semibold">Conversion %</th>
                            <th class="px-4 py-3 text-left font-semibold">Revenue</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php $__currentLoopData = $sourcePerformance; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr class="hover:bg-slate-50">
                                <td class="px-4 py-3 font-semibold text-slate-800"><?php echo e($row['label']); ?></td>
                                <td class="px-4 py-3 text-center tabular-nums"><?php echo e($row['created']); ?></td>
                                <td class="px-4 py-3 text-center tabular-nums text-emerald-700 font-bold"><?php echo e($row['won']); ?></td>
                                <td class="px-4 py-3 text-center tabular-nums <?php echo e(($row['conversion'] ?? 0) >= 20 ? 'text-emerald-700 font-bold' : 'text-amber-700 font-semibold'); ?>">
                                    <?php echo e($row['conversion'] !== null ? number_format((float) $row['conversion'], 1) . '%' : '—'); ?>

                                </td>
                                <td class="px-4 py-3 text-left tabular-nums"><?php echo e(number_format((float) $row['revenue'], 0)); ?></td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </section>

    
    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-4 py-3 border-b border-slate-200 bg-slate-50 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
            <div>
                <h3 class="text-base font-black text-slate-900 flex items-center gap-2">
                    <i class="fas fa-tags text-emerald-600"></i>
                    أداء التصنيفات
                </h3>
                <p class="text-xs text-slate-600">Leads / فوز / إيراد / متابعات متأخرة — الشهر الحالي.</p>
            </div>
            <a href="<?php echo e(route('admin.sales.categories.index')); ?>" class="text-xs font-semibold text-emerald-700 hover:underline">إدارة التصنيفات</a>
        </div>
        <div class="overflow-x-auto">
            <?php if(($categoryPerformance ?? collect())->isEmpty()): ?>
                <div class="text-center py-8 px-4">
                    <p class="text-sm text-slate-500">لا توجد بيانات تصنيفات بعد.</p>
                </div>
            <?php else: ?>
                <table class="min-w-[760px] w-full text-sm">
                    <thead>
                        <tr class="bg-slate-50 text-slate-700 border-b border-slate-200">
                            <th class="px-4 py-3 text-right font-semibold">التصنيف</th>
                            <th class="px-4 py-3 text-center font-semibold">إجمالي</th>
                            <th class="px-4 py-3 text-center font-semibold">مفتوح</th>
                            <th class="px-4 py-3 text-center font-semibold">جديد الشهر</th>
                            <th class="px-4 py-3 text-center font-semibold">فوز</th>
                            <th class="px-4 py-3 text-center font-semibold">Conversion</th>
                            <th class="px-4 py-3 text-left font-semibold">إيراد</th>
                            <th class="px-4 py-3 text-center font-semibold">متأخر</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php $__currentLoopData = $categoryPerformance; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr class="hover:bg-slate-50">
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center rounded-md px-2 py-0.5 text-xs font-bold border" style="color: <?php echo e($row['category']->color); ?>; border-color: <?php echo e($row['category']->color); ?>44; background: <?php echo e($row['category']->color); ?>12"><?php echo e($row['category']->name); ?></span>
                                </td>
                                <td class="px-4 py-3 text-center tabular-nums"><?php echo e($row['total']); ?></td>
                                <td class="px-4 py-3 text-center tabular-nums"><?php echo e($row['open']); ?></td>
                                <td class="px-4 py-3 text-center tabular-nums"><?php echo e($row['created_month']); ?></td>
                                <td class="px-4 py-3 text-center tabular-nums text-emerald-700 font-bold"><?php echo e($row['won_month']); ?></td>
                                <td class="px-4 py-3 text-center tabular-nums"><?php echo e($row['conversion'] !== null ? number_format($row['conversion'], 1).'%' : '—'); ?></td>
                                <td class="px-4 py-3 text-left tabular-nums"><?php echo e(number_format($row['revenue_month'], 0)); ?></td>
                                <td class="px-4 py-3 text-center tabular-nums <?php echo e($row['overdue'] > 0 ? 'text-rose-600 font-bold' : ''); ?>"><?php echo e($row['overdue']); ?></td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </section>

    
    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-4 py-3 border-b border-slate-200 bg-slate-50 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h3 class="text-base font-black text-slate-900 flex items-center gap-2">
                    <i class="fas fa-bell text-indigo-600"></i>
                    مراقبة إشعارات المتابعة
                </h3>
                <p class="text-xs text-slate-600">
                    <?php echo e(optional($rangeStart)->format('Y-m-d')); ?> → <?php echo e(optional($rangeEnd)->format('Y-m-d')); ?>

                    <span class="text-slate-500">(<?php echo e($periodLabel ?? 'اليوم'); ?>)</span>
                </p>
            </div>
            <form method="GET" action="<?php echo e(route('admin.sales.kpi.index')); ?>">
                <select name="period" onchange="this.form.submit()" class="<?php echo e($selectClass); ?> text-xs font-semibold">
                    <option value="today" <?php echo e(($period ?? 'today') === 'today' ? 'selected' : ''); ?>>اليوم</option>
                    <option value="7d" <?php echo e(($period ?? 'today') === '7d' ? 'selected' : ''); ?>>آخر 7 أيام</option>
                    <option value="month" <?php echo e(($period ?? 'today') === 'month' ? 'selected' : ''); ?>>هذا الشهر</option>
                </select>
            </form>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 p-4 border-b border-slate-100">
            <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                <div class="flex items-center justify-between gap-2">
                    <div>
                        <p class="text-xs font-semibold text-slate-600">إشعارات مرسلة</p>
                        <p class="text-xl font-black text-slate-900 tabular-nums"><?php echo e((int) ($reminderMonitoringSummary['sent_total'] ?? 0)); ?></p>
                    </div>
                    <div class="w-9 h-9 rounded-lg bg-indigo-100 flex items-center justify-center text-indigo-600">
                        <i class="fas fa-paper-plane text-sm"></i>
                    </div>
                </div>
            </div>
            <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                <div class="flex items-center justify-between gap-2">
                    <div>
                        <p class="text-xs font-semibold text-slate-600">موظفون بمتأخرات</p>
                        <p class="text-xl font-black text-slate-900 tabular-nums"><?php echo e((int) ($reminderMonitoringSummary['reps_with_alerts'] ?? 0)); ?></p>
                    </div>
                    <div class="w-9 h-9 rounded-lg bg-rose-100 flex items-center justify-center text-rose-600">
                        <i class="fas fa-exclamation-triangle text-sm"></i>
                    </div>
                </div>
            </div>
            <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                <div class="flex items-center justify-between gap-2">
                    <div>
                        <p class="text-xs font-semibold text-slate-600">متوسط الالتزام</p>
                        <p class="text-xl font-black text-slate-900 tabular-nums">
                            <?php echo e(($reminderMonitoringSummary['avg_compliance_pct'] ?? 0) > 0 ? number_format((float) $reminderMonitoringSummary['avg_compliance_pct'], 1) . '%' : '—'); ?>

                        </p>
                    </div>
                    <div class="w-9 h-9 rounded-lg bg-emerald-100 flex items-center justify-center text-emerald-600">
                        <i class="fas fa-check-circle text-sm"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-[900px] w-full text-sm">
                <thead>
                    <tr class="bg-slate-50 text-slate-700 border-b border-slate-200">
                        <th class="px-4 py-3 text-right font-semibold">الموظف</th>
                        <th class="px-4 py-3 text-center font-semibold">التنبيهات</th>
                        <th class="px-4 py-3 text-center font-semibold">متأخر وقت التنبيه</th>
                        <th class="px-4 py-3 text-center font-semibold">متابعات اليوم</th>
                        <th class="px-4 py-3 text-center font-semibold">راكد وقت التنبيه</th>
                        <th class="px-4 py-3 text-center font-semibold">المتأخر الحالي</th>
                        <th class="px-4 py-3 text-center font-semibold">نسبة الالتزام</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php $__empty_1 = true; $__currentLoopData = ($reminderMonitoringRows ?? collect()); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-3 font-semibold text-slate-800"><?php echo e($row['rep']->name); ?></td>
                            <td class="px-4 py-3 text-center tabular-nums"><?php echo e($row['sent_count'] ?? 0); ?></td>
                            <td class="px-4 py-3 text-center tabular-nums"><?php echo e($row['overdue_at_reminder'] ?? 0); ?></td>
                            <td class="px-4 py-3 text-center tabular-nums"><?php echo e($row['today_at_reminder'] ?? 0); ?></td>
                            <td class="px-4 py-3 text-center tabular-nums"><?php echo e($row['stale_at_reminder'] ?? 0); ?></td>
                            <td class="px-4 py-3 text-center tabular-nums <?php echo e(($row['current_overdue'] ?? 0) > 0 ? 'text-rose-700 font-bold' : 'text-emerald-700 font-bold'); ?>"><?php echo e($row['current_overdue'] ?? 0); ?></td>
                            <td class="px-4 py-3 text-center tabular-nums">
                                <?php if($row['compliance_pct'] !== null): ?>
                                    <span class="<?php echo e($row['compliance_pct'] >= 70 ? 'text-emerald-700 font-bold' : 'text-amber-700 font-bold'); ?>"><?php echo e(number_format((float) $row['compliance_pct'], 1)); ?>%</span>
                                <?php else: ?>
                                    <span class="text-slate-500">—</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="7" class="px-4 py-12 text-center">
                                <div class="w-14 h-14 mx-auto mb-3 rounded-xl bg-slate-100 flex items-center justify-center text-slate-400">
                                    <i class="fas fa-bell text-xl"></i>
                                </div>
                                <p class="text-sm font-semibold text-slate-900">لا بيانات إشعارات</p>
                                <p class="text-xs text-slate-500 mt-1">لا توجد بيانات إشعارات متابعة بعد.</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>

    
    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-4 py-3 border-b border-slate-200 bg-slate-50 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
            <div>
                <h3 class="text-base font-black text-slate-900 flex items-center gap-2">
                    <i class="fas fa-table text-sky-600"></i>
                    مقارنة الموظفين
                </h3>
                <p class="text-xs text-slate-600">المؤشر المركّب، الأعمدة، الإيراد، والتنبيهات.</p>
            </div>
            <span class="text-xs font-semibold text-sky-700 bg-sky-50 px-2.5 py-1 rounded-lg border border-sky-200"><?php echo e($repCount); ?> موظف</span>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-[1100px] w-full text-sm text-right">
                <thead>
                    <tr class="bg-slate-50 text-slate-700 border-b border-slate-200">
                        <th class="px-4 py-3 font-semibold">الموظف</th>
                        <th class="px-4 py-3 text-center font-semibold">مركّب</th>
                        <th class="px-4 py-3 text-center font-semibold">نتائج</th>
                        <th class="px-4 py-3 text-center font-semibold">نشاط</th>
                        <th class="px-4 py-3 text-center font-semibold">جودة</th>
                        <th class="px-4 py-3 text-center font-semibold">التزام</th>
                        <th class="px-4 py-3 text-left font-semibold">إيراد الشهر</th>
                        <th class="px-4 py-3 text-center font-semibold">فوز</th>
                        <th class="px-4 py-3 text-center font-semibold">أنبوب</th>
                        <th class="px-4 py-3 text-center font-semibold">SLA متأخر</th>
                        <th class="px-4 py-3 text-center font-semibold">راكد</th>
                        <th class="px-4 py-3 font-semibold">مراقبة</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php $__empty_1 = true; $__currentLoopData = $rows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php
                            $u = $r['user'];
                            $comp = $r['composite'];
                            $compClass = $comp < 45 ? 'text-rose-700 font-black' : ($comp < 65 ? 'text-amber-700 font-bold' : 'text-emerald-700 font-bold');
                        ?>
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-3 font-semibold text-slate-900"><?php echo e($u->name); ?></td>
                            <td class="px-4 py-3 text-center tabular-nums <?php echo e($compClass); ?>"><?php echo e(number_format($comp, 1)); ?></td>
                            <?php $__currentLoopData = ['results','activity','quality','discipline']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pk): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <td class="px-4 py-3 text-center tabular-nums text-slate-700"><?php echo e($r['pillars'][$pk]['score'] ?? '—'); ?></td>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <td class="px-4 py-3 text-left tabular-nums font-medium"><?php echo e(number_format($r['month_revenue'], 0)); ?></td>
                            <td class="px-4 py-3 text-center tabular-nums"><?php echo e($r['month_won']); ?></td>
                            <td class="px-4 py-3 text-center tabular-nums"><?php echo e($r['open_pipeline']); ?></td>
                            <td class="px-4 py-3 text-center tabular-nums <?php echo e(($r['overdue_followups'] ?? 0) > 0 ? 'text-rose-700 font-bold' : 'text-slate-500'); ?>"><?php echo e($r['overdue_followups'] ?? 0); ?></td>
                            <td class="px-4 py-3 text-center tabular-nums <?php echo e(($r['stale_open_leads'] ?? 0) > 0 ? 'text-amber-700 font-bold' : 'text-slate-500'); ?>"><?php echo e($r['stale_open_leads'] ?? 0); ?></td>
                            <td class="px-4 py-3 text-xs text-slate-700 max-w-xs">
                                <?php if(empty($r['flags'])): ?>
                                    <span class="text-emerald-600 font-medium">—</span>
                                <?php else: ?>
                                    <ul class="space-y-1">
                                        <?php $__currentLoopData = $r['flags']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $f): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <li class="text-rose-700 font-semibold"><i class="fas fa-circle text-[6px] align-middle ml-1"></i><?php echo e($f); ?></li>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </ul>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="12" class="px-4 py-12 text-center">
                                <div class="w-14 h-14 mx-auto mb-3 rounded-xl bg-slate-100 flex items-center justify-center text-slate-400">
                                    <i class="fas fa-users text-xl"></i>
                                </div>
                                <p class="text-sm font-semibold text-slate-900">لا موظفو مبيعات</p>
                                <p class="text-xs text-slate-500 mt-1">لا يوجد موظفو مبيعات نشطون.</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\admin\sales\kpi\index.blade.php ENDPATH**/ ?>