<?php
    $monthNames = [
        1 => 'يناير', 2 => 'فبراير', 3 => 'مارس', 4 => 'أبريل', 5 => 'مايو', 6 => 'يونيو',
        7 => 'يوليو', 8 => 'أغسطس', 9 => 'سبتمبر', 10 => 'أكتوبر', 11 => 'نوفمبر', 12 => 'ديسمبر',
    ];
    $excelUrl = route('admin.design-task-cycles.performance-report.excel', ['year' => $year, 'month' => $month]);
    $direction = $dashboard['direction'] ?? [];
    $alerts = $dashboard['alerts'] ?? [];
    $charts = $dashboard['charts'] ?? [];
    $atRisk = $dashboard['at_risk'] ?? [];
    $topPerformers = $dashboard['top_performers'] ?? [];
    $teamHealth = $dashboard['team_health_score'] ?? null;
    $enrichedRows = $dashboard['enriched_rows'] ?? $rows;
    $dirStatus = $direction['status'] ?? 'stable';
    $dirBannerClass = match ($dirStatus) {
        'growth' => 'border-emerald-200 bg-gradient-to-l from-emerald-50 to-white',
        'decline' => 'border-rose-200 bg-gradient-to-l from-rose-50 to-white',
        default => 'border-sky-200 bg-gradient-to-l from-sky-50 to-white',
    };
    $dirIconClass = match ($dirStatus) {
        'growth' => 'text-emerald-600 bg-emerald-100',
        'decline' => 'text-rose-600 bg-rose-100',
        default => 'text-sky-600 bg-sky-100',
    };
    $inputClass = 'w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm focus:ring-2 focus:ring-violet-500 focus:border-violet-500';
?>

<?php $__env->startSection('title', 'تقرير الأداء — '.$monthNames[$month].' '.$year); ?>
<?php $__env->startSection('header', 'تحليل الأداء الشهري — '.$monthNames[$month].' '.$year); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    
    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-4 py-4 bg-slate-50 border-b flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-violet-500 to-fuchsia-600 flex items-center justify-center text-white shadow-md">
                    <i class="fas fa-chart-line text-lg"></i>
                </div>
                <div>
                    <h2 class="text-xl font-black text-slate-900">مركز تحليل أداء الموظفين</h2>
                    <p class="text-xs text-slate-600">مهام · تصميم · تقارير يومية · تنبيهات الالتزام</p>
                </div>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="<?php echo e($excelUrl); ?>" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold shadow-sm">
                    <i class="fas fa-file-excel"></i> Excel
                </a>
                <a href="<?php echo e(route('admin.employee-daily-reports.index')); ?>" class="inline-flex items-center gap-2 px-3 py-2 rounded-xl border border-slate-300 text-sm font-semibold hover:bg-white">
                    <i class="fas fa-clipboard-check text-sky-600"></i> التقارير اليومية
                </a>
                <a href="<?php echo e(route('admin.design-task-cycles.index')); ?>" class="inline-flex items-center gap-2 px-3 py-2 rounded-xl border border-slate-300 text-sm font-semibold hover:bg-white">
                    <i class="fas fa-palette text-fuchsia-600"></i> دورات التصميم
                </a>
            </div>
        </div>
        <div class="p-4 flex flex-col lg:flex-row lg:items-end gap-4">
            <form method="get" class="flex flex-wrap items-end gap-3 flex-1">
                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-1">السنة</label>
                    <input type="number" name="year" value="<?php echo e($year); ?>" min="2000" max="2100" class="w-28 <?php echo e($inputClass); ?>">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-1">الشهر</label>
                    <select name="month" class="<?php echo e($inputClass); ?> min-w-[140px]">
                        <?php for($m = 1; $m <= 12; $m++): ?>
                            <option value="<?php echo e($m); ?>" <?php echo e((int) $month === $m ? 'selected' : ''); ?>><?php echo e($monthNames[$m]); ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-violet-600 hover:bg-violet-700 text-white text-sm font-bold">
                    <i class="fas fa-sync-alt ml-1"></i> تحديث
                </button>
            </form>
            <p class="text-xs text-slate-500">
                <i class="fas fa-calendar-alt text-violet-500 ml-1"></i>
                <?php echo e($start->format('Y-m-d')); ?> — <?php echo e($end->format('Y-m-d')); ?>

            </p>
        </div>
    </section>

    
    <section class="rounded-2xl border shadow-lg overflow-hidden <?php echo e($dirBannerClass); ?>">
        <div class="px-5 py-5 flex flex-col xl:flex-row xl:items-start xl:justify-between gap-5">
            <div class="flex items-start gap-4 flex-1">
                <div class="w-12 h-12 rounded-xl flex items-center justify-center <?php echo e($dirIconClass); ?>">
                    <i class="fas fa-compass text-xl"></i>
                </div>
                <div>
                    <p class="text-xs font-bold text-slate-500 uppercase tracking-wide">إلى أين نتجه؟</p>
                    <p class="text-xl font-black text-slate-900 mt-1"><?php echo e($direction['label'] ?? '—'); ?></p>
                    <p class="text-sm text-slate-700 mt-2 max-w-3xl leading-relaxed"><?php echo e($direction['summary'] ?? ''); ?></p>
                    <p class="text-[11px] text-slate-500 mt-2">
                        مقارنة: <?php echo e($direction['previous_month_label'] ?? '—'); ?> ← <?php echo e($direction['current_month_label'] ?? '—'); ?>

                    </p>
                </div>
            </div>
            <div class="flex flex-col items-center justify-center rounded-2xl bg-white/80 border border-slate-200 px-8 py-4 min-w-[140px]">
                <p class="text-xs font-semibold text-slate-500">صحة الفريق</p>
                <p class="text-4xl font-black tabular-nums <?php echo e(($teamHealth ?? 0) >= 75 ? 'text-emerald-600' : (($teamHealth ?? 0) >= 55 ? 'text-amber-600' : 'text-rose-600')); ?>">
                    <?php echo e($teamHealth !== null ? $teamHealth.'%' : '—'); ?>

                </p>
                <p class="text-[10px] text-slate-500 mt-1">مؤشر مركّب</p>
            </div>
        </div>
        <?php if(!empty($direction['metrics'])): ?>
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-px bg-slate-200 border-t border-slate-200">
            <?php $__currentLoopData = $direction['metrics']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dm): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="bg-white px-4 py-3">
                    <p class="text-[11px] text-slate-500 font-semibold"><?php echo e($dm['label']); ?></p>
                    <p class="text-lg font-black text-slate-900 tabular-nums">
                        <?php echo e(is_numeric($dm['current']) ? number_format($dm['current']) : ($dm['current'] ?? '—')); ?>

                        <?php if(isset($dm['delta_pct']) && $dm['delta_pct'] !== null): ?>
                            <span class="text-xs font-bold <?php echo e($dm['delta_pct'] >= 0 ? 'text-emerald-600' : 'text-rose-600'); ?>">
                                <?php echo e($dm['delta_pct'] >= 0 ? '↑' : '↓'); ?><?php echo e(abs($dm['delta_pct'])); ?>%
                            </span>
                        <?php elseif(isset($dm['delta_pts']) && $dm['delta_pts'] !== null): ?>
                            <span class="text-xs font-bold <?php echo e($dm['delta_pts'] >= 0 ? 'text-emerald-600' : 'text-rose-600'); ?>">
                                <?php echo e($dm['delta_pts'] >= 0 ? '+' : ''); ?><?php echo e($dm['delta_pts']); ?> نقطة
                            </span>
                        <?php elseif(isset($dm['delta'])): ?>
                            <span class="text-xs font-bold <?php echo e($dm['delta'] <= 0 ? 'text-emerald-600' : 'text-rose-600'); ?>">
                                <?php echo e($dm['delta'] > 0 ? '+' : ''); ?><?php echo e($dm['delta']); ?>

                            </span>
                        <?php endif; ?>
                    </p>
                    <p class="text-[10px] text-slate-400">الشهر السابق: <?php echo e(is_numeric($dm['previous'] ?? null) ? number_format($dm['previous']) : ($dm['previous'] ?? '—')); ?></p>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <?php endif; ?>
    </section>

    
    <?php if(count($alerts) > 0): ?>
    <section class="space-y-3">
        <h3 class="text-sm font-black text-slate-900 flex items-center gap-2 px-1">
            <i class="fas fa-bell text-amber-500"></i>
            تنبيهات وملاحظات للإدارة
        </h3>
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-3">
            <?php $__currentLoopData = $alerts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $alert): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $alertClass = match($alert['level'] ?? 'info') {
                        'critical' => 'border-rose-300 bg-rose-50 text-rose-900',
                        'warning' => 'border-amber-300 bg-amber-50 text-amber-900',
                        'success' => 'border-emerald-300 bg-emerald-50 text-emerald-900',
                        default => 'border-sky-300 bg-sky-50 text-sky-900',
                    };
                    $alertIcon = match($alert['level'] ?? 'info') {
                        'critical' => 'text-rose-600',
                        'warning' => 'text-amber-600',
                        'success' => 'text-emerald-600',
                        default => 'text-sky-600',
                    };
                ?>
                <div class="rounded-xl border p-4 flex gap-3 <?php echo e($alertClass); ?>">
                    <div class="w-9 h-9 rounded-lg bg-white/70 flex items-center justify-center shrink-0 <?php echo e($alertIcon); ?>">
                        <i class="fas <?php echo e($alert['icon'] ?? 'fa-info-circle'); ?>"></i>
                    </div>
                    <div>
                        <p class="font-bold text-sm"><?php echo e($alert['title']); ?></p>
                        <p class="text-xs mt-1 leading-relaxed opacity-90"><?php echo e($alert['message']); ?></p>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </section>
    <?php endif; ?>

    
    <div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-3">
        <div class="rounded-2xl bg-gradient-to-br from-violet-600 to-violet-800 text-white p-4 shadow-lg">
            <p class="text-[10px] font-semibold text-violet-200 uppercase">مهام مكتملة</p>
            <p class="text-2xl font-black mt-1 tabular-nums"><?php echo e(number_format($summary['tasks_completed'])); ?></p>
            <p class="text-[10px] text-violet-200 mt-1">من <?php echo e(number_format($summary['tasks_assigned'])); ?> مسندة</p>
        </div>
        <div class="rounded-2xl bg-white border p-4 shadow-sm">
            <p class="text-[10px] font-bold text-slate-500 uppercase">التزام الموعد</p>
            <p class="text-2xl font-black text-slate-900 tabular-nums"><?php echo e($summary['tasks_on_time_rate_pct'] !== null ? $summary['tasks_on_time_rate_pct'].'%' : '—'); ?></p>
            <div class="mt-2 h-1.5 rounded-full bg-slate-100"><div class="h-full rounded-full bg-emerald-500" style="width:<?php echo e(min(100, $summary['tasks_on_time_rate_pct'] ?? 0)); ?>%"></div></div>
        </div>
        <div class="rounded-2xl bg-white border p-4 shadow-sm">
            <p class="text-[10px] font-bold text-slate-500 uppercase">التزام المصمم</p>
            <p class="text-2xl font-black text-fuchsia-700 tabular-nums"><?php echo e($summary['designer_on_time_rate_pct'] !== null ? $summary['designer_on_time_rate_pct'].'%' : '—'); ?></p>
            <p class="text-[10px] text-slate-500 mt-1"><?php echo e($summary['designer_submissions_month']); ?> تسليم</p>
        </div>
        <div class="rounded-2xl bg-white border p-4 shadow-sm">
            <p class="text-[10px] font-bold text-slate-500 uppercase">تسليمات ملفات</p>
            <p class="text-2xl font-black tabular-nums"><?php echo e(number_format($summary['deliverables'])); ?></p>
        </div>
        <div class="rounded-2xl bg-white border p-4 shadow-sm">
            <p class="text-[10px] font-bold text-slate-500 uppercase">دورات تصميم</p>
            <p class="text-2xl font-black text-violet-700 tabular-nums"><?php echo e(number_format($summary['design_cycles_touched_month'] ?? 0)); ?></p>
        </div>
        <div class="rounded-2xl bg-amber-50 border border-amber-200 p-4 shadow-sm">
            <p class="text-[10px] font-bold text-amber-800 uppercase">متأخرة مفتوحة</p>
            <p class="text-2xl font-black text-amber-900 tabular-nums"><?php echo e(number_format($summary['open_overdue_tasks'])); ?></p>
        </div>
    </div>

    
    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-4 py-3 border-b bg-slate-50">
            <h3 class="text-base font-black text-slate-900"><i class="fas fa-chart-area text-violet-600 ml-1"></i> المخططات التحليلية</h3>
        </div>
        <div class="p-4 grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6">
            <div class="rounded-xl border border-slate-200 p-4 lg:col-span-2 xl:col-span-1">
                <p class="text-xs font-bold text-slate-600 mb-3">إنجاز المهام أسبوعياً</p>
                <div class="h-56"><canvas id="chartWeekly"></canvas></div>
            </div>
            <div class="rounded-xl border border-slate-200 p-4">
                <p class="text-xs font-bold text-slate-600 mb-3">في الموعد vs متأخر (مهام)</p>
                <div class="h-56"><canvas id="chartOnTime"></canvas></div>
            </div>
            <div class="rounded-xl border border-slate-200 p-4">
                <p class="text-xs font-bold text-slate-600 mb-3">تسليمات المصممين</p>
                <div class="h-56"><canvas id="chartDesigner"></canvas></div>
            </div>
            <div class="rounded-xl border border-slate-200 p-4 lg:col-span-2">
                <p class="text-xs font-bold text-slate-600 mb-3">مقارنة الشهر الحالي vs السابق</p>
                <div class="h-64"><canvas id="chartMonthCompare"></canvas></div>
            </div>
            <div class="rounded-xl border border-slate-200 p-4">
                <p class="text-xs font-bold text-slate-600 mb-3">حالات دورات التصميم</p>
                <div class="h-56"><canvas id="chartDesignStatus"></canvas></div>
            </div>
            <div class="rounded-xl border border-slate-200 p-4">
                <p class="text-xs font-bold text-slate-600 mb-3">مهام مكتملة حسب النوع</p>
                <div class="h-56"><canvas id="chartTaskTypes"></canvas></div>
            </div>
            <div class="rounded-xl border border-slate-200 p-4 lg:col-span-2">
                <p class="text-xs font-bold text-slate-600 mb-3">أقل موظفين في مؤشر الصحة (يحتاجون متابعة)</p>
                <div class="h-64"><canvas id="chartHealth"></canvas></div>
            </div>
            <div class="rounded-xl border border-slate-200 p-4 lg:col-span-2 xl:col-span-1">
                <p class="text-xs font-bold text-slate-600 mb-3">التزام التقارير اليومية (الأضعف)</p>
                <div class="h-64"><canvas id="chartDaily"></canvas></div>
            </div>
            <div class="rounded-xl border border-slate-200 p-4 lg:col-span-2">
                <p class="text-xs font-bold text-slate-600 mb-3">نسبة إنجاز المهام (أكثر الموظفين تحميلاً)</p>
                <div class="h-64"><canvas id="chartCompletion"></canvas></div>
            </div>
        </div>
    </section>

    
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
        <section class="rounded-2xl border border-rose-200 bg-white shadow-lg overflow-hidden">
            <div class="px-4 py-3 bg-rose-50 border-b border-rose-100 flex items-center gap-2">
                <i class="fas fa-user-shield text-rose-600"></i>
                <h3 class="font-black text-rose-900 text-sm">يحتاجون متابعة فورية</h3>
                <span class="mr-auto text-xs bg-rose-200 text-rose-800 px-2 py-0.5 rounded-full font-bold"><?php echo e(count($atRisk)); ?></span>
            </div>
            <div class="divide-y divide-slate-100 max-h-[420px] overflow-y-auto">
                <?php $__empty_1 = true; $__currentLoopData = $atRisk; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="px-4 py-3 hover:bg-rose-50/30">
                        <div class="flex items-start justify-between gap-2">
                            <div>
                                <p class="font-bold text-slate-900"><?php echo e($row['user']->name); ?></p>
                                <p class="text-[11px] text-slate-500"><?php echo e($row['user']->employeeJob->name ?? '—'); ?></p>
                            </div>
                            <?php if($row['health_score'] !== null): ?>
                                <span class="shrink-0 px-2 py-0.5 rounded-lg text-xs font-black <?php echo e($row['health_score'] < 50 ? 'bg-rose-100 text-rose-800' : 'bg-amber-100 text-amber-800'); ?>">
                                    <?php echo e($row['health_score']); ?>%
                                </span>
                            <?php endif; ?>
                        </div>
                        <?php if(!empty($row['risk_flags'])): ?>
                            <ul class="mt-2 space-y-1">
                                <?php $__currentLoopData = $row['risk_flags']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $flag): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <li class="text-xs text-rose-800 flex items-center gap-1.5"><i class="fas fa-circle text-[5px]"></i><?php echo e($flag); ?></li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </ul>
                        <?php endif; ?>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <p class="px-4 py-8 text-center text-sm text-slate-500">لا يوجد موظفون في منطقة الخطر هذا الشهر.</p>
                <?php endif; ?>
            </div>
        </section>

        <section class="rounded-2xl border border-emerald-200 bg-white shadow-lg overflow-hidden">
            <div class="px-4 py-3 bg-emerald-50 border-b border-emerald-100 flex items-center gap-2">
                <i class="fas fa-star text-emerald-600"></i>
                <h3 class="font-black text-emerald-900 text-sm">أفضل الأداء</h3>
            </div>
            <div class="divide-y divide-slate-100">
                <?php $__empty_1 = true; $__currentLoopData = $topPerformers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="px-4 py-3 flex items-center justify-between gap-3 hover:bg-emerald-50/30">
                        <div>
                            <p class="font-bold text-slate-900"><?php echo e($row['user']->name); ?></p>
                            <p class="text-[11px] text-slate-500">
                                <?php echo e($row['tasks_completed_in_month']); ?> مهمة · <?php echo e($row['designer_submissions_in_month']); ?> تسليم تصميم
                            </p>
                        </div>
                        <span class="px-2 py-0.5 rounded-lg bg-emerald-100 text-emerald-800 text-xs font-black"><?php echo e($row['health_score']); ?>%</span>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <p class="px-4 py-8 text-center text-sm text-slate-500">لا بيانات كافية لتحديد الأفضل.</p>
                <?php endif; ?>
            </div>
        </section>
    </div>

    
    <section class="rounded-2xl border border-slate-200 bg-white shadow-lg overflow-hidden">
        <div class="px-4 py-3 border-b bg-slate-50 flex flex-wrap items-center justify-between gap-2">
            <h3 class="font-black text-slate-900">جدول الأداء التفصيلي</h3>
            <span class="text-xs text-slate-500"><?php echo e(count($enrichedRows)); ?> موظف</span>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-[1400px] w-full text-xs">
                <thead>
                    <tr class="bg-slate-800 text-white">
                        <th class="text-right px-3 py-2 font-bold sticky right-0 bg-slate-800 z-10 min-w-[140px]">الموظف</th>
                        <th class="text-center px-2 py-2 font-bold">الصحة %</th>
                        <th class="text-center px-2 py-2 font-bold">مسند</th>
                        <th class="text-center px-2 py-2 font-bold">مكتمل</th>
                        <th class="text-center px-2 py-2 font-bold">إنجاز %</th>
                        <th class="text-center px-2 py-2 font-bold">%موعد</th>
                        <th class="text-center px-2 py-2 font-bold">متأخرة</th>
                        <th class="text-center px-2 py-2 font-bold">تقرير يومي %</th>
                        <th class="text-center px-2 py-2 font-bold">تصميم</th>
                        <th class="text-center px-2 py-2 font-bold">%موعد مصمم</th>
                        <th class="text-right px-2 py-2 font-bold min-w-[180px]">تنبيهات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php $__currentLoopData = $enrichedRows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr class="hover:bg-violet-50/30 <?php echo e($i % 2 ? 'bg-slate-50/40' : ''); ?>">
                            <td class="px-3 py-2 font-bold sticky right-0 z-10 border-l border-slate-100 <?php echo e($i % 2 ? 'bg-slate-50' : 'bg-white'); ?>">
                                <?php echo e($row['user']->name); ?>

                                <span class="block text-[10px] font-normal text-slate-500"><?php echo e($row['user']->employeeJob->name ?? ''); ?></span>
                            </td>
                            <td class="text-center py-2 tabular-nums font-black
                                <?php echo e(($row['health_score'] ?? 100) < 50 ? 'text-rose-700' : (($row['health_score'] ?? 100) < 70 ? 'text-amber-700' : 'text-emerald-700')); ?>">
                                <?php echo e($row['health_score'] !== null ? $row['health_score'].'%' : '—'); ?>

                            </td>
                            <td class="text-center py-2 tabular-nums"><?php echo e($row['tasks_assigned_in_month']); ?></td>
                            <td class="text-center py-2 tabular-nums font-semibold text-violet-800"><?php echo e($row['tasks_completed_in_month']); ?></td>
                            <td class="text-center py-2 tabular-nums"><?php echo e($row['tasks_completion_rate_pct'] !== null ? $row['tasks_completion_rate_pct'].'%' : '—'); ?></td>
                            <td class="text-center py-2 tabular-nums"><?php echo e($row['tasks_on_time_rate_pct'] !== null ? $row['tasks_on_time_rate_pct'].'%' : '—'); ?></td>
                            <td class="text-center py-2 tabular-nums text-amber-800 font-medium"><?php echo e($row['open_overdue_tasks_end_of_month']); ?></td>
                            <td class="text-center py-2 tabular-nums <?php echo e(($row['daily_report_rate_pct'] ?? 100) < 70 ? 'text-rose-700 font-bold' : ''); ?>">
                                <?php echo e($row['daily_report_rate_pct'] !== null ? $row['daily_report_rate_pct'].'%' : '—'); ?>

                            </td>
                            <td class="text-center py-2 tabular-nums"><?php echo e($row['designer_submissions_in_month']); ?></td>
                            <td class="text-center py-2 tabular-nums"><?php echo e($row['designer_on_time_rate_pct'] !== null ? $row['designer_on_time_rate_pct'].'%' : '—'); ?></td>
                            <td class="px-2 py-2">
                                <?php if(!empty($row['risk_flags'])): ?>
                                    <div class="flex flex-wrap gap-1">
                                        <?php $__currentLoopData = array_slice($row['risk_flags'], 0, 2); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $flag): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <span class="inline-block px-1.5 py-0.5 rounded bg-rose-100 text-rose-800 text-[10px] font-semibold"><?php echo e($flag); ?></span>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </div>
                                <?php else: ?>
                                    <span class="text-emerald-600 text-[10px] font-semibold"><i class="fas fa-check"></i> جيد</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
    </section>

    <details class="rounded-2xl border border-slate-200 bg-slate-50/80 p-4 text-sm group">
        <summary class="font-bold text-slate-900 cursor-pointer list-none flex items-center gap-2">
            <i class="fas fa-info-circle text-violet-600"></i> منطق الحساب
            <i class="fas fa-chevron-down mr-auto text-slate-400 group-open:rotate-180 transition-transform"></i>
        </summary>
        <ul class="mt-3 space-y-1.5 list-disc list-inside text-xs text-slate-600 max-w-4xl">
            <li><strong>مؤشر الصحة:</strong> مركّب من إنجاز المهام، الالتزام بالموعد، تسليم التصميم، والتقارير اليومية — مع خصم للمهام المتأخرة المفتوحة.</li>
            <li><strong>الاتجاه:</strong> مقارنة الشهر الحالي بالسابق في الإنجاز والالتزام والمتأخرات.</li>
            <li><strong>التقارير اليومية:</strong> نسبة الأيام المُرسلة من إجمالي الأيام المطلوبة لكل موظف.</li>
            <li><strong>Excel:</strong> تصدير تفصيلي بأربع أوراق عمل.</li>
        </ul>
    </details>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    if (typeof Chart === 'undefined') return;

    const charts = <?php echo json_encode($charts, 15, 512) ?>;
    const fontFamily = 'inherit';
    Chart.defaults.font.family = fontFamily;

    const colors = {
        violet: 'rgba(124, 58, 237, 0.85)',
        emerald: 'rgba(16, 185, 129, 0.85)',
        rose: 'rgba(244, 63, 94, 0.85)',
        amber: 'rgba(245, 158, 11, 0.85)',
        sky: 'rgba(14, 165, 233, 0.85)',
        fuchsia: 'rgba(192, 38, 211, 0.85)',
    };

    if (charts.week_labels?.length) {
        new Chart(document.getElementById('chartWeekly'), {
            type: 'line',
            data: {
                labels: charts.week_labels,
                datasets: [{
                    label: 'مهام مكتملة',
                    data: charts.week_completed,
                    borderColor: colors.violet,
                    backgroundColor: 'rgba(124, 58, 237, 0.15)',
                    fill: true,
                    tension: 0.35,
                }],
            },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } },
        });
    }

    new Chart(document.getElementById('chartOnTime'), {
        type: 'doughnut',
        data: {
            labels: charts.on_time_late?.labels || [],
            datasets: [{ data: charts.on_time_late?.data || [], backgroundColor: [colors.emerald, colors.rose] }],
        },
        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom', labels: { boxWidth: 12, font: { size: 11 } } } } },
    });

    new Chart(document.getElementById('chartDesigner'), {
        type: 'doughnut',
        data: {
            labels: charts.designer_on_time_late?.labels || [],
            datasets: [{ data: charts.designer_on_time_late?.data || [], backgroundColor: [colors.fuchsia, colors.amber] }],
        },
        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom', labels: { boxWidth: 12, font: { size: 11 } } } } },
    });

    if (charts.month_compare?.labels?.length) {
        new Chart(document.getElementById('chartMonthCompare'), {
            type: 'bar',
            data: {
                labels: charts.month_compare.labels,
                datasets: [
                    { label: 'الشهر الحالي', data: charts.month_compare.current, backgroundColor: colors.violet },
                    { label: 'الشهر السابق', data: charts.month_compare.previous, backgroundColor: 'rgba(148, 163, 184, 0.7)' },
                ],
            },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'top' } }, scales: { y: { beginAtZero: true } } },
        });
    }

    if (charts.design_cycle_status?.labels?.length) {
        new Chart(document.getElementById('chartDesignStatus'), {
            type: 'pie',
            data: {
                labels: charts.design_cycle_status.labels,
                datasets: [{ data: charts.design_cycle_status.data, backgroundColor: [colors.amber, colors.sky, colors.violet, colors.fuchsia, colors.emerald, colors.rose] }],
            },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom', labels: { boxWidth: 10, font: { size: 10 } } } } },
        });
    }

    new Chart(document.getElementById('chartTaskTypes'), {
        type: 'bar',
        data: {
            labels: charts.task_types?.labels || [],
            datasets: [{ label: 'مكتملة', data: charts.task_types?.data || [], backgroundColor: [colors.violet, colors.sky, colors.emerald, colors.amber] }],
        },
        options: { responsive: true, maintainAspectRatio: false, indexAxis: 'y', plugins: { legend: { display: false } }, scales: { x: { beginAtZero: true } } },
    });

    if (charts.health_worst?.labels?.length) {
        new Chart(document.getElementById('chartHealth'), {
            type: 'bar',
            data: {
                labels: charts.health_worst.labels,
                datasets: [{
                    label: 'مؤشر الصحة %',
                    data: charts.health_worst.data,
                    backgroundColor: charts.health_worst.data.map(v => v < 50 ? colors.rose : (v < 70 ? colors.amber : colors.emerald)),
                }],
            },
            options: { responsive: true, maintainAspectRatio: false, indexAxis: 'y', plugins: { legend: { display: false } }, scales: { x: { max: 100, beginAtZero: true } } },
        });
    }

    if (charts.daily_compliance?.labels?.length) {
        new Chart(document.getElementById('chartDaily'), {
            type: 'bar',
            data: {
                labels: charts.daily_compliance.labels,
                datasets: [{
                    label: 'التزام %',
                    data: charts.daily_compliance.data,
                    backgroundColor: charts.daily_compliance.data.map(v => v < 70 ? colors.rose : colors.emerald),
                }],
            },
            options: { responsive: true, maintainAspectRatio: false, indexAxis: 'y', plugins: { legend: { display: false } }, scales: { x: { max: 100, beginAtZero: true } } },
        });
    }

    if (charts.completion_rates?.labels?.length) {
        new Chart(document.getElementById('chartCompletion'), {
            type: 'bar',
            data: {
                labels: charts.completion_rates.labels,
                datasets: [{
                    label: 'إنجاز %',
                    data: charts.completion_rates.data,
                    backgroundColor: colors.violet,
                }],
            },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { max: 100, beginAtZero: true } } },
        });
    }
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\admin\design-task-cycles\performance-report.blade.php ENDPATH**/ ?>