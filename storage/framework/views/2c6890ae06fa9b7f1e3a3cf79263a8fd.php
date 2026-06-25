<?php $__env->startSection('title', 'Insights — المبيعات'); ?>
<?php $__env->startSection('header', 'Insights — تحليلات المبيعات'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $direction = $teamDashboard['direction'] ?? [];
    $monthly = $teamDashboard['monthly'] ?? [];
    $dirMetrics = $direction['metrics'] ?? [];
    $dirStatus = $direction['status'] ?? 'neutral';
    $dirBannerClass = match ($dirStatus) {
        'growth' => 'border-emerald-200 bg-emerald-50',
        'stable' => 'border-sky-200 bg-sky-50',
        'decline' => 'border-rose-200 bg-rose-50',
        default => 'border-slate-200 bg-slate-50',
    };
    $dirIconClass = match ($dirStatus) {
        'growth' => 'text-emerald-600',
        'stable' => 'text-sky-600',
        'decline' => 'text-rose-600',
        default => 'text-slate-600',
    };
    $statusClass = match($decision['status'] ?? 'good') {
        'excellent' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
        'good' => 'bg-sky-100 text-sky-800 border-sky-200',
        'warning' => 'bg-amber-100 text-amber-800 border-amber-200',
        default => 'bg-rose-100 text-rose-800 border-rose-200',
    };
    $inputClass = 'w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500 focus:border-sky-500';
    $conversionArr = $monthly['conversion'] ?? [];
    $academyArr = $monthly['academyRate'] ?? [];
    $revenueArr = $monthly['revenue'] ?? [];
    $latestConversion = $conversionArr !== [] ? (float) end($conversionArr) : 0;
    $latestAcademy = $academyArr !== [] ? (float) end($academyArr) : 0;
    $latestRevenue = $revenueArr !== [] ? (float) end($revenueArr) : 0;
?>

<div class="space-y-6">
    
    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-4 py-4 bg-slate-50 border-b border-slate-200 flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-indigo-500 to-indigo-600 flex items-center justify-center text-white shadow-md">
                    <i class="fas fa-chart-pie"></i>
                </div>
                <div>
                    <h2 class="text-xl font-black text-slate-900">تحليلات المبيعات</h2>
                    <p class="text-xs text-slate-600">اتجاه الفريق، تحويل الأكاديمية، ومقارنة أداء الموظفين.</p>
                </div>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <a href="<?php echo e(route('admin.sales.kpi.index')); ?>" class="inline-flex items-center gap-2 px-3 py-2 text-sm font-semibold text-slate-700 rounded-xl border border-slate-300 hover:bg-white">
                    <i class="fas fa-chart-line text-emerald-600"></i>
                    KPIs
                </a>
                <a href="<?php echo e(route('admin.sales.commissions.index')); ?>" class="inline-flex items-center gap-2 px-3 py-2 text-sm font-semibold text-slate-700 rounded-xl border border-slate-300 hover:bg-white">
                    <i class="fas fa-coins text-amber-600"></i>
                    الكوميشن
                </a>
            </div>
        </div>
        <div class="p-4">
            <form method="get" action="<?php echo e(route('admin.sales.insights.index')); ?>" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-3 items-end">
                <div class="lg:col-span-2">
                    <label class="block text-xs font-semibold text-slate-700 mb-1">الموظف (تفصيل)</label>
                    <select name="user_id" class="<?php echo e($inputClass); ?>" required>
                        <?php $__currentLoopData = $salesReps; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($r->id); ?>" <?php if((string) $rep->id === (string) $r->id): echo 'selected'; endif; ?>><?php echo e($r->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">الفترة</label>
                    <select name="period" id="period_sel" class="<?php echo e($inputClass); ?>">
                        <option value="day" <?php if(request('period', 'week') === 'day'): echo 'selected'; endif; ?>>يومي</option>
                        <option value="week" <?php if(request('period', 'week') === 'week'): echo 'selected'; endif; ?>>أسبوعي</option>
                        <option value="month" <?php if(request('period', 'week') === 'month'): echo 'selected'; endif; ?>>شهري</option>
                        <option value="custom" <?php if(request('period', 'week') === 'custom'): echo 'selected'; endif; ?>>مخصص</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">من</label>
                    <input type="date" name="date_from" id="date_from" value="<?php echo e($start->toDateString()); ?>" class="<?php echo e($inputClass); ?>">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">إلى</label>
                    <input type="date" name="date_to" id="date_to" value="<?php echo e($end->toDateString()); ?>" class="<?php echo e($inputClass); ?>">
                </div>
                <div class="flex flex-wrap gap-2 lg:col-span-6">
                    <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-sky-600 hover:bg-sky-700 text-white text-sm font-semibold">
                        <i class="fas fa-sync-alt"></i>
                        تحديث
                    </button>
                    <button type="button" onclick="downloadInsightsPdf()" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-900 text-white text-sm font-semibold">
                        <i class="fas fa-file-pdf"></i>
                        PDF للموظف
                    </button>
                </div>
            </form>
            <p class="text-xs text-slate-500 mt-3">فترة تفصيل الموظف: <strong><?php echo e($start->format('Y-m-d')); ?></strong> → <strong><?php echo e($end->format('Y-m-d')); ?></strong> (<?php echo e($periodLabel); ?>)</p>
        </div>
    </section>

    
    <section class="rounded-2xl border shadow-lg overflow-hidden <?php echo e($dirBannerClass); ?>">
        <div class="px-4 py-4 flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">
            <div class="flex items-start gap-3">
                <div class="w-10 h-10 rounded-xl bg-white border border-slate-200 flex items-center justify-center <?php echo e($dirIconClass); ?>">
                    <i class="fas fa-compass text-lg"></i>
                </div>
                <div>
                    <p class="text-xs font-semibold text-slate-600">اتجاه قسم المبيعات</p>
                    <p class="text-lg font-black text-slate-900"><?php echo e($direction['label'] ?? '—'); ?></p>
                    <p class="text-sm text-slate-700 mt-1 max-w-3xl"><?php echo e($direction['summary'] ?? ''); ?></p>
                    <p class="text-[11px] text-slate-500 mt-2">
                        مقارنة: <?php echo e($direction['previous_month_label'] ?? '—'); ?> ← <?php echo e($direction['current_month_label'] ?? '—'); ?>

                    </p>
                </div>
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-5 gap-2 min-w-0">
                <?php $__currentLoopData = $dirMetrics; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                        $pct = $m['pct'] ?? null;
                        $isPctMetric = in_array($key, ['conversion', 'academy'], true);
                        $trendUp = $pct !== null && $pct > 0;
                        $trendDown = $pct !== null && $pct < 0;
                        $trendClass = $trendUp ? 'text-emerald-700' : ($trendDown ? 'text-rose-700' : 'text-slate-600');
                    ?>
                    <div class="rounded-xl bg-white/80 border border-white p-3 shadow-sm">
                        <p class="text-[10px] font-semibold text-slate-500 truncate"><?php echo e($m['label'] ?? ''); ?></p>
                        <p class="text-lg font-black text-slate-900 tabular-nums">
                            <?php if($isPctMetric): ?>
                                <?php echo e(number_format((float) ($m['current'] ?? 0), 1)); ?>%
                            <?php elseif($key === 'revenue'): ?>
                                <?php echo e(number_format((float) ($m['current'] ?? 0), 0)); ?>

                            <?php else: ?>
                                <?php echo e(number_format((int) ($m['current'] ?? 0))); ?>

                            <?php endif; ?>
                        </p>
                        <?php if($pct !== null): ?>
                            <p class="text-[10px] font-bold <?php echo e($trendClass); ?> tabular-nums">
                                <?php echo e($trendUp ? '↑' : ($trendDown ? '↓' : '→')); ?>

                                <?php echo e($isPctMetric ? number_format(abs((float) $pct), 1).' نقطة' : number_format(abs((float) $pct), 1).'%'); ?>

                            </p>
                        <?php endif; ?>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
    </section>

    
    <div class="grid grid-cols-2 xl:grid-cols-4 gap-3">
        <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
            <p class="text-xs font-semibold text-slate-600">تحويل Leads (الشهر الحالي)</p>
            <p class="text-2xl font-black text-emerald-700 tabular-nums"><?php echo e(number_format((float) $latestConversion, 1)); ?>%</p>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
            <p class="text-xs font-semibold text-slate-600">تحويل الأكاديمية</p>
            <p class="text-2xl font-black text-indigo-700 tabular-nums"><?php echo e(number_format((float) $latestAcademy, 1)); ?>%</p>
            <p class="text-[10px] text-slate-500">فوز → تسجيل كورس خلال 90 يوم</p>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
            <p class="text-xs font-semibold text-slate-600">إيراد المبيعات (الشهر)</p>
            <p class="text-2xl font-black text-slate-900 tabular-nums"><?php echo e(number_format((float) $latestRevenue, 0)); ?></p>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
            <p class="text-xs font-semibold text-slate-600">مؤشر الموظف المحدد</p>
            <p class="text-2xl font-black text-sky-700 tabular-nums"><?php echo e($decision['composite'] ?? ($periodReport['composite'] ?? '—')); ?></p>
            <p class="text-[10px] text-slate-500"><?php echo e($rep->name); ?></p>
        </div>
    </div>

    
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
        <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden xl:col-span-2">
            <div class="px-4 py-3 border-b border-slate-200 bg-slate-50">
                <h3 class="text-base font-black text-slate-900">اتجاه المبيعات — آخر 6 أشهر</h3>
                <p class="text-xs text-slate-600">Leads جديدة، صفقات فوز، ومعدل التحويل.</p>
            </div>
            <div class="p-4">
                <div class="relative w-full" style="height: 360px;">
                    <canvas id="chartTeamTrend"></canvas>
                </div>
            </div>
        </section>

        <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
            <div class="px-4 py-3 border-b border-slate-200 bg-slate-50">
                <h3 class="text-base font-black text-slate-900">تحويل مبيعات الأكاديمية</h3>
                <p class="text-xs text-slate-600">نسبة الفوز → تسجيل فعلي في كورس (أونلاين/أوفلاين).</p>
            </div>
            <div class="p-4">
                <div class="relative w-full" style="height: 300px;">
                    <canvas id="chartAcademy"></canvas>
                </div>
            </div>
        </section>

        <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
            <div class="px-4 py-3 border-b border-slate-200 bg-slate-50">
                <h3 class="text-base font-black text-slate-900">الإيراد الشهري</h3>
                <p class="text-xs text-slate-600">قيمة الصفقات المكتملة (expected value).</p>
            </div>
            <div class="p-4">
                <div class="relative w-full" style="height: 300px;">
                    <canvas id="chartRevenue"></canvas>
                </div>
            </div>
        </section>

        <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
            <div class="px-4 py-3 border-b border-slate-200 bg-slate-50">
                <h3 class="text-base font-black text-slate-900">قمع المبيعات (الأنبوب)</h3>
                <p class="text-xs text-slate-600">الفرص المفتوحة حسب المرحلة + فوز الشهر.</p>
            </div>
            <div class="p-4">
                <div class="relative w-full" style="height: 300px;">
                    <canvas id="chartFunnel"></canvas>
                </div>
            </div>
        </section>

        <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
            <div class="px-4 py-3 border-b border-slate-200 bg-slate-50">
                <h3 class="text-base font-black text-slate-900">مصادر Leads (الشهر)</h3>
            </div>
            <div class="p-4">
                <div class="relative w-full" style="height: 300px;">
                    <canvas id="chartSources"></canvas>
                </div>
            </div>
        </section>

        <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden xl:col-span-2">
            <div class="px-4 py-3 border-b border-slate-200 bg-slate-50">
                <h3 class="text-base font-black text-slate-900">مقارنة الموظفين — هذا الشهر</h3>
                <p class="text-xs text-slate-600">المؤشر المركّب، عدد الفوز، والإيراد.</p>
            </div>
            <div class="p-4">
                <div class="relative w-full" style="height: 320px;">
                    <canvas id="chartRepComparison"></canvas>
                </div>
            </div>
        </section>
    </div>

    
    <div id="insights-pdf-root" class="space-y-6">
        <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
            <div class="px-4 py-4 bg-slate-50 border-b border-slate-200 flex flex-wrap items-center justify-between gap-3">
                <div>
                    <h3 class="text-base font-black text-slate-900">تفصيل: <?php echo e($rep->name); ?></h3>
                    <p class="text-xs text-slate-600"><?php echo e($periodLabel); ?> — <?php echo e($start->format('Y-m-d')); ?> → <?php echo e($end->format('Y-m-d')); ?></p>
                </div>
                <span class="inline-flex items-center rounded-xl px-3 py-1.5 text-xs font-bold border <?php echo e($statusClass); ?>">
                    <?php echo e($decision['status_label'] ?? '—'); ?>

                </span>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 p-4">
                <div class="rounded-xl border border-slate-200 p-3">
                    <p class="text-xs text-slate-500">Leads</p>
                    <p class="text-xl font-black tabular-nums"><?php echo e($counts['leads'] ?? 0); ?></p>
                </div>
                <div class="rounded-xl border border-slate-200 p-3">
                    <p class="text-xs text-slate-500">أنشطة CRM</p>
                    <p class="text-xl font-black tabular-nums"><?php echo e($counts['activities'] ?? 0); ?></p>
                </div>
                <div class="rounded-xl border border-slate-200 p-3">
                    <p class="text-xs text-slate-500">Wins معتمدة</p>
                    <p class="text-xl font-black tabular-nums"><?php echo e($commission['confirmed_wins'] ?? 0); ?></p>
                </div>
                <div class="rounded-xl border border-slate-200 p-3">
                    <p class="text-xs text-slate-500">كوميشن</p>
                    <p class="text-xl font-black tabular-nums"><?php echo e(number_format((float) ($commission['commission_from_leads'] ?? 0), 0)); ?></p>
                </div>
            </div>
            <?php if(!empty($decision['recommendations'])): ?>
                <div class="px-4 pb-4">
                    <div class="rounded-xl border border-slate-200 bg-slate-50 p-3">
                        <p class="text-xs font-bold text-slate-600 mb-2">توصيات</p>
                        <ul class="text-sm text-slate-800 space-y-1 list-disc list-inside">
                            <?php $__currentLoopData = $decision['recommendations']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rec): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <li><?php echo e($rec); ?></li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </ul>
                    </div>
                </div>
            <?php endif; ?>
        </section>

        <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
            <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden xl:col-span-2">
                <div class="px-4 py-3 border-b border-slate-200 bg-slate-50">
                    <h3 class="text-base font-black text-slate-900">نشاط الموظف اليومي</h3>
                </div>
                <div class="p-4">
                    <div class="relative w-full" style="height: 300px;">
                        <canvas id="chartRepDaily"></canvas>
                    </div>
                </div>
            </section>

            <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
                <div class="px-4 py-3 border-b border-slate-200 bg-slate-50">
                    <h3 class="text-base font-black text-slate-900">توزيع المراحل</h3>
                </div>
                <div class="p-4">
                    <div class="relative w-full" style="height: 280px;">
                        <canvas id="chartRepStages"></canvas>
                    </div>
                </div>
            </section>

            <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
                <div class="px-4 py-3 border-b border-slate-200 bg-slate-50">
                    <h3 class="text-base font-black text-slate-900">أنواع الأنشطة</h3>
                </div>
                <div class="p-4">
                    <div class="relative w-full" style="height: 280px;">
                        <canvas id="chartRepActivities"></canvas>
                    </div>
                </div>
            </section>

            <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden xl:col-span-2">
                <div class="px-4 py-3 border-b border-slate-200 bg-slate-50">
                    <h3 class="text-base font-black text-slate-900">محاور KPI (الفترة)</h3>
                </div>
                <div class="p-4">
                    <div class="relative w-full" style="height: 280px;">
                        <canvas id="chartRepPillars"></canvas>
                    </div>
                </div>
            </section>
        </div>

        <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
            <div class="px-4 py-3 border-b border-slate-200 bg-slate-50">
                <h3 class="text-base font-black text-slate-900">تفصيل KPIs</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-[740px] w-full text-sm">
                    <thead>
                        <tr class="bg-slate-50 text-slate-700 border-b border-slate-200">
                            <th class="px-4 py-3 text-right font-semibold">المؤشر</th>
                            <th class="px-4 py-3 text-center font-semibold">الفعلي</th>
                            <th class="px-4 py-3 text-center font-semibold">الهدف</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php $__empty_1 = true; $__currentLoopData = $periodReport['kpi_lines'] ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $line): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr class="hover:bg-slate-50">
                                <td class="px-4 py-2 font-medium text-slate-800"><?php echo e($line['label'] ?? ''); ?></td>
                                <td class="px-4 py-2 text-center tabular-nums"><?php echo e($line['actual'] ?? '—'); ?></td>
                                <td class="px-4 py-2 text-center tabular-nums"><?php echo e($line['target'] ?? '—'); ?></td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr><td colspan="3" class="px-4 py-8 text-center text-slate-500">لا بيانات KPI.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>

        <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
            <div class="px-4 py-3 border-b border-slate-200 bg-slate-50 flex items-center justify-between">
                <h3 class="text-base font-black text-slate-900">ربط الأكاديمية (حسب الإيميل)</h3>
                <span class="text-xs font-semibold text-indigo-700 bg-indigo-50 px-2 py-1 rounded-lg border border-indigo-200"><?php echo e((int) ($courses['matched_users'] ?? 0)); ?> حساب</span>
            </div>
            <div class="p-4 grid grid-cols-2 md:grid-cols-4 gap-3">
                <div class="rounded-xl border border-slate-200 p-3 text-center">
                    <p class="text-xs text-slate-500">تسجيلات أونلاين</p>
                    <p class="text-xl font-black tabular-nums"><?php echo e((int) data_get($courses, 'online_enrollments.count', 0)); ?></p>
                </div>
                <div class="rounded-xl border border-slate-200 p-3 text-center">
                    <p class="text-xs text-slate-500">Orders</p>
                    <p class="text-xl font-black tabular-nums"><?php echo e((int) data_get($courses, 'orders.count', 0)); ?></p>
                </div>
                <div class="rounded-xl border border-slate-200 p-3 text-center">
                    <p class="text-xs text-slate-500">حجوزات أوفلاين</p>
                    <p class="text-xl font-black tabular-nums"><?php echo e((int) data_get($courses, 'offline_bookings.count', 0)); ?></p>
                </div>
                <div class="rounded-xl border border-slate-200 p-3 text-center">
                    <p class="text-xs text-slate-500">تسجيلات أوفلاين</p>
                    <p class="text-xl font-black tabular-nums"><?php echo e((int) data_get($courses, 'offline_enrollments.count', 0)); ?></p>
                </div>
            </div>
            <?php if(!empty($courses['note'])): ?>
                <p class="px-4 pb-4 text-xs text-amber-800"><?php echo e($courses['note']); ?></p>
            <?php endif; ?>
        </section>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script>
(function () {
    const team = <?php echo json_encode($teamDashboard, 15, 512) ?>;
    const rep = <?php echo json_encode($repCharts, 15, 512) ?>;

    const palette = {
        emerald: 'rgb(16, 185, 129)',
        emeraldSoft: 'rgba(16, 185, 129, 0.15)',
        sky: 'rgb(14, 165, 233)',
        skySoft: 'rgba(14, 165, 233, 0.15)',
        indigo: 'rgb(99, 102, 241)',
        indigoSoft: 'rgba(99, 102, 241, 0.15)',
        amber: 'rgb(245, 158, 11)',
        rose: 'rgb(244, 63, 94)',
        slate: 'rgb(100, 116, 139)',
        violet: 'rgb(139, 92, 246)',
    };

    const doughnutColors = [
        palette.emerald, palette.sky, palette.indigo, palette.amber,
        palette.rose, palette.violet, palette.slate, '#0ea5e9', '#84cc16', '#f97316'
    ];

    const baseOptions = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { position: 'bottom', labels: { boxWidth: 12, font: { size: 11 } } },
        },
    };

    function hasData(arr) {
        return Array.isArray(arr) && arr.some(v => Number(v) > 0);
    }

    function emptyChartMessage(canvasId, msg) {
        const canvas = document.getElementById(canvasId);
        if (!canvas) return;
        const wrap = canvas.parentElement;
        wrap.innerHTML = '<p class="text-sm text-slate-500 text-center py-16">' + msg + '</p>';
    }

    const monthly = team.monthly || {};

    if (hasData(monthly.created) || hasData(monthly.won)) {
        new Chart(document.getElementById('chartTeamTrend'), {
            type: 'line',
            data: {
                labels: monthly.labels || [],
                datasets: [
                    {
                        label: 'Leads جديدة',
                        data: monthly.created || [],
                        borderColor: palette.sky,
                        backgroundColor: palette.skySoft,
                        tension: 0.35,
                        fill: true,
                        yAxisID: 'y',
                    },
                    {
                        label: 'صفقات فوز',
                        data: monthly.won || [],
                        borderColor: palette.emerald,
                        backgroundColor: palette.emeraldSoft,
                        tension: 0.35,
                        fill: true,
                        yAxisID: 'y',
                    },
                    {
                        label: 'معدل التحويل %',
                        data: monthly.conversion || [],
                        borderColor: palette.amber,
                        borderDash: [6, 4],
                        tension: 0.35,
                        yAxisID: 'y1',
                    },
                ],
            },
            options: {
                ...baseOptions,
                interaction: { mode: 'index', intersect: false },
                scales: {
                    y: { beginAtZero: true, position: 'right', title: { display: true, text: 'العدد' } },
                    y1: { beginAtZero: true, position: 'left', grid: { drawOnChartArea: false }, title: { display: true, text: '%' } },
                },
            },
        });
    } else {
        emptyChartMessage('chartTeamTrend', 'لا بيانات Leads/فوز كافية لعرض الاتجاه.');
    }

    if (hasData(monthly.academyRate) || hasData(monthly.academyConverted)) {
        new Chart(document.getElementById('chartAcademy'), {
            type: 'line',
            data: {
                labels: monthly.labels || [],
                datasets: [
                    {
                        label: 'نسبة تحويل الأكاديمية %',
                        data: monthly.academyRate || [],
                        borderColor: palette.indigo,
                        backgroundColor: palette.indigoSoft,
                        tension: 0.35,
                        fill: true,
                        yAxisID: 'y1',
                    },
                    {
                        label: 'تسجيلات من فوز',
                        data: monthly.academyConverted || [],
                        type: 'bar',
                        backgroundColor: 'rgba(99, 102, 241, 0.55)',
                        yAxisID: 'y',
                    },
                ],
            },
            options: {
                ...baseOptions,
                scales: {
                    y: { beginAtZero: true, position: 'right', title: { display: true, text: 'تسجيلات' } },
                    y1: { beginAtZero: true, position: 'left', grid: { drawOnChartArea: false }, max: 100, title: { display: true, text: '%' } },
                },
            },
        });
    } else {
        emptyChartMessage('chartAcademy', 'لا بيانات تحويل أكاديمية — تأكد من إيميل الـ leads وربطها بحسابات الطلاب.');
    }

    if (hasData(monthly.revenue)) {
        new Chart(document.getElementById('chartRevenue'), {
            type: 'bar',
            data: {
                labels: monthly.labels || [],
                datasets: [{
                    label: 'الإيراد (ج.م)',
                    data: monthly.revenue || [],
                    backgroundColor: 'rgba(16, 185, 129, 0.7)',
                    borderRadius: 8,
                }],
            },
            options: {
                ...baseOptions,
                plugins: { ...baseOptions.plugins, legend: { display: false } },
                scales: { y: { beginAtZero: true } },
            },
        });
    } else {
        emptyChartMessage('chartRevenue', 'لا إيراد مسجّل في آخر 6 أشهر.');
    }

    const funnel = team.funnel || {};
    if (hasData(funnel.values)) {
        new Chart(document.getElementById('chartFunnel'), {
            type: 'bar',
            data: {
                labels: funnel.labels || [],
                datasets: [{
                    label: 'العدد',
                    data: funnel.values || [],
                    backgroundColor: doughnutColors,
                    borderRadius: 6,
                }],
            },
            options: {
                indexAxis: 'y',
                ...baseOptions,
                plugins: { ...baseOptions.plugins, legend: { display: false } },
                scales: { x: { beginAtZero: true } },
            },
        });
    } else {
        emptyChartMessage('chartFunnel', 'الأنبوب فارغ حالياً.');
    }

    const sources = team.sources || {};
    if (hasData(sources.values)) {
        new Chart(document.getElementById('chartSources'), {
            type: 'doughnut',
            data: {
                labels: sources.labels || [],
                datasets: [{
                    data: sources.values || [],
                    backgroundColor: doughnutColors,
                    borderWidth: 2,
                }],
            },
            options: { ...baseOptions },
        });
    } else {
        emptyChartMessage('chartSources', 'لا leads بمصادر مسجّلة هذا الشهر.');
    }

    const comp = team.rep_comparison || {};
    if ((comp.labels || []).length > 0) {
        new Chart(document.getElementById('chartRepComparison'), {
            type: 'bar',
            data: {
                labels: comp.labels || [],
                datasets: [
                    {
                        label: 'المؤشر المركّب',
                        data: comp.composite || [],
                        backgroundColor: 'rgba(14, 165, 233, 0.75)',
                        borderRadius: 6,
                        yAxisID: 'y',
                    },
                    {
                        label: 'فوز الشهر',
                        data: comp.won || [],
                        backgroundColor: 'rgba(16, 185, 129, 0.75)',
                        borderRadius: 6,
                        yAxisID: 'y1',
                    },
                ],
            },
            options: {
                ...baseOptions,
                scales: {
                    y: { beginAtZero: true, max: 100, position: 'right', title: { display: true, text: 'مركّب' } },
                    y1: { beginAtZero: true, position: 'left', grid: { drawOnChartArea: false }, title: { display: true, text: 'فوز' } },
                },
            },
        });
    } else {
        emptyChartMessage('chartRepComparison', 'لا موظفين للمقارنة.');
    }

    const daily = rep.daily || {};
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
    } else {
        emptyChartMessage('chartRepDaily', 'لا نشاط يومي في الفترة المحددة.');
    }

    const stages = rep.stages || {};
    if (hasData(stages.values)) {
        new Chart(document.getElementById('chartRepStages'), {
            type: 'doughnut',
            data: {
                labels: stages.labels || [],
                datasets: [{ data: stages.values || [], backgroundColor: doughnutColors, borderWidth: 2 }],
            },
            options: { ...baseOptions },
        });
    } else {
        emptyChartMessage('chartRepStages', 'لا leads لهذا الموظف.');
    }

    const act = rep.activities_by_type || {};
    if (hasData(act.values)) {
        new Chart(document.getElementById('chartRepActivities'), {
            type: 'doughnut',
            data: {
                labels: act.labels || [],
                datasets: [{ data: act.values || [], backgroundColor: doughnutColors, borderWidth: 2 }],
            },
            options: { ...baseOptions },
        });
    } else {
        emptyChartMessage('chartRepActivities', 'لا أنشطة CRM في الفترة.');
    }

    const pillars = rep.pillars || {};
    if ((pillars.labels || []).length > 0) {
        new Chart(document.getElementById('chartRepPillars'), {
            type: 'bar',
            data: {
                labels: pillars.labels || [],
                datasets: [
                    { label: 'النتيجة', data: pillars.scores || [], backgroundColor: 'rgba(14, 165, 233, 0.75)', borderRadius: 6 },
                    { label: 'معيار 70', data: pillars.targets || [], type: 'line', borderColor: palette.amber, borderDash: [4, 4], pointRadius: 0, fill: false },
                ],
            },
            options: {
                ...baseOptions,
                scales: { y: { beginAtZero: true, max: 100 } },
            },
        });
    } else {
        emptyChartMessage('chartRepPillars', 'لا محاور KPI.');
    }
})();

function downloadInsightsPdf() {
    const element = document.getElementById('insights-pdf-root');
    const opt = {
        margin: 8,
        filename: 'sales-insights-<?php echo e($rep->id); ?>-<?php echo e($start->format('Y-m-d')); ?>-<?php echo e($end->format('Y-m-d')); ?>.pdf',
        image: { type: 'jpeg', quality: 0.98 },
        html2canvas: { scale: 2, useCORS: true },
        jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
    };
    html2pdf().set(opt).from(element).save();
}

(function () {
    const sel = document.getElementById('period_sel');
    const from = document.getElementById('date_from');
    const to = document.getElementById('date_to');
    if (!sel || !from || !to) return;
    function toggleCustom() {
        const isCustom = sel.value === 'custom';
        from.disabled = !isCustom;
        to.disabled = !isCustom;
    }
    sel.addEventListener('change', toggleCustom);
    toggleCustom();
})();
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/cityphone/Documents/all Mindlytics Project/Mindlytics/resources/views/admin/sales/insights/index.blade.php ENDPATH**/ ?>