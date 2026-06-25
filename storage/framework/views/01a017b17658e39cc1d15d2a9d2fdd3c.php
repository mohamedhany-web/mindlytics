<?php $__env->startSection('title', 'مؤشرات المحاسبة'); ?>
<?php $__env->startSection('header', 'مؤشرات المحاسبة'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6" x-data="accountingInsights" x-init="init()">

    
    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-4 py-4 bg-slate-50 border-b border-slate-200 flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-sky-500 to-sky-600 flex items-center justify-center text-white shadow-md">
                    <i class="fas fa-chart-bar"></i>
                </div>
                <div>
                    <h2 class="text-xl font-black text-slate-900">مؤشرات الشركة</h2>
                    <p class="text-xs text-slate-600">إيراد، مصروفات، صافي ربح/خسارة، واتجاهات لحظية من بيانات النظام.</p>
                    <p class="text-[11px] text-slate-500 mt-1">
                        آخر تحديث:
                        <span class="font-semibold text-slate-700" x-text="asOf || '—'"></span>
                        <span x-show="loading" class="mr-2 text-sky-600"><i class="fas fa-circle-notch fa-spin"></i></span>
                    </p>
                </div>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <a href="<?php echo e(route('admin.dashboard')); ?>" class="inline-flex items-center gap-2 px-3 py-2 text-sm font-semibold text-slate-700 rounded-xl border border-slate-300 hover:bg-white">
                    <i class="fas fa-home text-slate-500"></i>
                    لوحة التحكم
                </a>
                <a href="<?php echo e(route('admin.accounting.hub')); ?>" class="inline-flex items-center gap-2 px-3 py-2 text-sm font-semibold text-slate-700 rounded-xl border border-slate-300 hover:bg-white">
                    <i class="fas fa-calculator text-sky-600"></i>
                    مركز المحاسبة
                </a>
                <a href="<?php echo e(route('admin.accounting.receivables')); ?>" class="inline-flex items-center gap-2 px-3 py-2 text-sm font-semibold text-amber-800 rounded-xl border border-amber-300 bg-amber-50 hover:bg-amber-100">
                    <i class="fas fa-hand-holding-usd"></i>
                    المديونية
                </a>
                <a href="<?php echo e(route('admin.accounting.reports')); ?>" class="inline-flex items-center gap-2 px-3 py-2 text-sm font-semibold text-emerald-800 rounded-xl border border-emerald-300 bg-emerald-50 hover:bg-emerald-100">
                    <i class="fas fa-file-excel"></i>
                    التقارير
                </a>
                <button type="button" @click="refresh(true)" :disabled="loading"
                        class="inline-flex items-center gap-2 px-4 py-2 text-sm font-bold text-white rounded-xl bg-sky-600 hover:bg-sky-700 disabled:opacity-60">
                    <i class="fas fa-sync-alt" :class="loading ? 'fa-spin' : ''"></i>
                    تحديث الآن
                </button>
            </div>
        </div>
    </section>

    <template x-if="errorMsg">
        <div class="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-rose-900 text-sm font-semibold flex items-center gap-2">
            <i class="fas fa-exclamation-triangle"></i>
            <span x-text="errorMsg"></span>
        </div>
    </template>
    <template x-if="chartLoadError">
        <div class="rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-amber-900 text-sm font-semibold flex items-center gap-2">
            <i class="fas fa-chart-line"></i>
            <span x-text="chartLoadError"></span>
        </div>
    </template>

    
    <section class="rounded-2xl border shadow-lg overflow-hidden"
             :class="healthBannerClass">
        <div class="px-4 py-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-white/80 border border-white/60 flex items-center justify-center"
                     :class="healthIconClass">
                    <i class="fas fa-heartbeat text-lg"></i>
                </div>
                <div>
                    <p class="text-xs font-semibold text-slate-600">الحالة العامة / بر الأمان</p>
                    <p class="text-lg font-black text-slate-900" x-text="healthLabel || '—'"></p>
                    <p class="text-xs text-slate-600 mt-1 max-w-xl" x-show="healthDetail" x-text="healthDetail"></p>
                </div>
            </div>
            <p class="text-xs text-slate-600">المقارنات: اليوم ↔ أمس — الشهر ↔ الشهر السابق — بر الأمان = إيرادات تغطي مصروفات التشغيل</p>
        </div>
    </section>

    
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <section class="lg:col-span-2 rounded-2xl border border-slate-200 bg-white shadow-lg p-5">
            <h3 class="text-base font-black text-slate-900 mb-3"><i class="fas fa-shield-alt text-sky-600 ml-1"></i> تحليل بر الأمان (الشهر)</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 text-sm">
                <div class="rounded-xl bg-emerald-50 border border-emerald-200 p-3">
                    <p class="text-xs text-emerald-700">إيرادات محصّلة</p>
                    <p class="font-black text-emerald-800 tabular-nums" x-text="fmt(breakEvenMonth.revenue)"></p>
                </div>
                <div class="rounded-xl bg-slate-50 border border-slate-200 p-3">
                    <p class="text-xs text-slate-600">مصروف من الإيراد</p>
                    <p class="font-black tabular-nums" x-text="fmt(breakEvenMonth.expenses_from_revenue)"></p>
                </div>
                <div class="rounded-xl bg-amber-50 border border-amber-200 p-3">
                    <p class="text-xs text-amber-800">من جيب الشركة</p>
                    <p class="font-black text-amber-900 tabular-nums" x-text="fmt(breakEvenMonth.expenses_out_of_pocket)"></p>
                </div>
                <div class="rounded-xl border p-3" :class="(breakEvenMonth.operational_net ?? 0) >= 0 ? 'bg-emerald-50 border-emerald-200' : 'bg-rose-50 border-rose-200'">
                    <p class="text-xs text-slate-600">صافي تشغيلي</p>
                    <p class="font-black tabular-nums" :class="(breakEvenMonth.operational_net ?? 0) >= 0 ? 'text-emerald-800' : 'text-rose-800'" x-text="fmt(breakEvenMonth.operational_net)"></p>
                </div>
            </div>
            <p class="text-xs text-slate-600 mt-3" x-text="breakEvenMonth.detail || ''"></p>
            <p class="text-[11px] text-slate-500 mt-2">
                كل الفترات:
                <span class="font-semibold" x-text="breakEvenAllTime.label || '—'"></span>
                — تمويل ذاتي تراكمي:
                <span class="font-bold text-amber-700" x-text="fmt(breakEvenAllTime.expenses_out_of_pocket)"></span>
            </p>
        </section>
        <section class="rounded-2xl border border-violet-200 bg-violet-50/50 shadow-lg p-5">
            <h3 class="text-base font-black text-violet-900 mb-3"><i class="fas fa-hand-holding-usd ml-1"></i> المديونية (لنا)</h3>
            <p class="text-2xl font-black text-violet-800 tabular-nums" x-text="fmt(receivables.receivables?.total)"></p>
            <ul class="mt-3 space-y-1 text-xs text-slate-700">
                <li class="flex justify-between"><span>فواتير معلقة</span><span x-text="fmt(receivables.receivables?.invoices_amount)"></span></li>
                <li class="flex justify-between"><span>متبقي أوفلاين</span><span x-text="fmt(receivables.receivables?.offline_remaining)"></span></li>
                <li class="flex justify-between"><span>أقساط</span><span x-text="fmt((receivables.receivables?.installments_pending || 0) + (receivables.receivables?.installments_overdue || 0))"></span></li>
            </ul>
            <a href="<?php echo e(route('admin.accounting.receivables')); ?>" class="inline-block mt-3 text-xs font-bold text-violet-700 hover:underline">تفاصيل المديونية ←</a>
        </section>
    </div>

    
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
        
        <div class="dashboard-stat-card rounded-2xl border-2 border-emerald-200/70 bg-gradient-to-br from-white via-white to-emerald-50/60 p-5 shadow-lg">
            <div class="flex items-center justify-between gap-3 mb-3">
                <div class="min-w-0 flex-1">
                    <p class="text-sm font-bold text-emerald-800/80 mb-1">إيراد اليوم</p>
                    <p class="text-2xl font-black bg-gradient-to-r from-emerald-700 to-teal-600 bg-clip-text text-transparent tabular-nums" x-text="fmt(snapshot.revenue_today)"></p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center text-white shadow-md flex-shrink-0">
                    <i class="fas fa-arrow-down text-sm"></i>
                </div>
            </div>
            <p class="text-xs font-bold" :class="trendClass(trend.revenue_today_pct)" x-text="trendLabel(trend.revenue_today_pct)"></p>
        </div>

        
        <div class="dashboard-stat-card rounded-2xl border-2 border-rose-200/70 bg-gradient-to-br from-white via-white to-rose-50/60 p-5 shadow-lg">
            <div class="flex items-center justify-between gap-3 mb-3">
                <div class="min-w-0 flex-1">
                    <p class="text-sm font-bold text-rose-800/80 mb-1">مصروفات اليوم</p>
                    <p class="text-2xl font-black bg-gradient-to-r from-rose-700 to-red-600 bg-clip-text text-transparent tabular-nums" x-text="fmt(snapshot.expenses_today)"></p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-rose-500 to-red-500 flex items-center justify-center text-white shadow-md flex-shrink-0">
                    <i class="fas fa-arrow-up text-sm"></i>
                </div>
            </div>
            <p class="text-xs font-bold" :class="trendClass(-1 * (trend.expenses_today_pct ?? 0), true)" x-text="trendLabel(trend.expenses_today_pct, true)"></p>
        </div>

        
        <div class="dashboard-stat-card rounded-2xl border-2 border-sky-200/70 bg-gradient-to-br from-white via-white to-sky-50/60 p-5 shadow-lg">
            <div class="flex items-center justify-between gap-3 mb-3">
                <div class="min-w-0 flex-1">
                    <p class="text-sm font-bold text-sky-800/80 mb-1">صافي اليوم</p>
                    <p class="text-2xl font-black tabular-nums"
                       :class="(snapshot.net_today ?? 0) >= 0 ? 'text-emerald-700' : 'text-rose-700'"
                       x-text="fmt(snapshot.net_today)"></p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-sky-500 to-blue-600 flex items-center justify-center text-white shadow-md flex-shrink-0">
                    <i class="fas fa-balance-scale text-sm"></i>
                </div>
            </div>
            <p class="text-xs font-bold" :class="trendClass(trend.net_today_pct)" x-text="trendLabel(trend.net_today_pct)"></p>
        </div>

        
        <div class="dashboard-stat-card rounded-2xl border-2 border-green-200/70 bg-gradient-to-br from-white via-white to-green-50/60 p-5 shadow-lg">
            <div class="flex items-center justify-between gap-3 mb-3">
                <div class="min-w-0 flex-1">
                    <p class="text-sm font-bold text-green-800/80 mb-1">إيراد الشهر</p>
                    <p class="text-2xl font-black bg-gradient-to-r from-green-700 to-emerald-600 bg-clip-text text-transparent tabular-nums" x-text="fmt(snapshot.revenue_month)"></p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-green-500 to-emerald-600 flex items-center justify-center text-white shadow-md flex-shrink-0">
                    <i class="fas fa-money-bill-wave text-sm"></i>
                </div>
            </div>
            <p class="text-xs font-bold" :class="trendClass(trend.revenue_month_pct)" x-text="trendLabel(trend.revenue_month_pct)"></p>
        </div>

        
        <div class="dashboard-stat-card rounded-2xl border-2 border-orange-200/70 bg-gradient-to-br from-white via-white to-orange-50/60 p-5 shadow-lg">
            <div class="flex items-center justify-between gap-3 mb-3">
                <div class="min-w-0 flex-1">
                    <p class="text-sm font-bold text-orange-800/80 mb-1">مصروفات الشهر</p>
                    <p class="text-2xl font-black bg-gradient-to-r from-orange-700 to-amber-600 bg-clip-text text-transparent tabular-nums" x-text="fmt(snapshot.expenses_month)"></p>
                    <p class="text-[11px] text-slate-600 mt-1">
                        من الإيراد: <span class="font-bold text-emerald-700" x-text="fmt(snapshot.expenses_month_revenue)"></span>
                        · من جيبنا: <span class="font-bold text-amber-700" x-text="fmt(snapshot.expenses_month_pocket)"></span>
                    </p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-orange-500 to-amber-500 flex items-center justify-center text-white shadow-md flex-shrink-0">
                    <i class="fas fa-receipt text-sm"></i>
                </div>
            </div>
            <p class="text-xs font-bold" :class="trendClass(-1 * (trend.expenses_month_pct ?? 0), true)" x-text="trendLabel(trend.expenses_month_pct, true)"></p>
        </div>

        
        <div class="dashboard-stat-card rounded-2xl border-2 border-indigo-200/70 bg-gradient-to-br from-white via-white to-indigo-50/60 p-5 shadow-lg">
            <div class="flex items-center justify-between gap-3 mb-3">
                <div class="min-w-0 flex-1">
                    <p class="text-sm font-bold text-indigo-800/80 mb-1">صافي الشهر</p>
                    <p class="text-2xl font-black tabular-nums"
                       :class="(snapshot.net_month ?? 0) >= 0 ? 'text-emerald-700' : 'text-rose-700'"
                       x-text="fmt(snapshot.net_month)"></p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-indigo-500 to-violet-600 flex items-center justify-center text-white shadow-md flex-shrink-0">
                    <i class="fas fa-chart-pie text-sm"></i>
                </div>
            </div>
            <p class="text-xs font-bold" :class="trendClass(trend.net_month_pct)" x-text="trendLabel(trend.net_month_pct)"></p>
        </div>

        
        <div class="dashboard-stat-card rounded-2xl border-2 border-teal-200/70 bg-gradient-to-br from-white via-white to-teal-50/60 p-5 shadow-lg">
            <div class="flex items-center justify-between gap-3 mb-3">
                <div class="min-w-0 flex-1">
                    <p class="text-sm font-bold text-teal-800/80 mb-1">تدفق داخلي (الشهر)</p>
                    <p class="text-2xl font-black bg-gradient-to-r from-teal-700 to-emerald-600 bg-clip-text text-transparent tabular-nums" x-text="fmt(snapshot.cash_in_month)"></p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-teal-500 to-emerald-600 flex items-center justify-center text-white shadow-md flex-shrink-0">
                    <i class="fas fa-sign-in-alt text-sm"></i>
                </div>
            </div>
            <p class="text-xs text-teal-700/70">من معاملات credit</p>
        </div>

        
        <div class="dashboard-stat-card rounded-2xl border-2 border-red-200/70 bg-gradient-to-br from-white via-white to-red-50/60 p-5 shadow-lg">
            <div class="flex items-center justify-between gap-3 mb-3">
                <div class="min-w-0 flex-1">
                    <p class="text-sm font-bold text-red-800/80 mb-1">تدفق خارجي (الشهر)</p>
                    <p class="text-2xl font-black bg-gradient-to-r from-red-700 to-rose-600 bg-clip-text text-transparent tabular-nums" x-text="fmt(snapshot.cash_out_month)"></p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-red-500 to-rose-500 flex items-center justify-center text-white shadow-md flex-shrink-0">
                    <i class="fas fa-sign-out-alt text-sm"></i>
                </div>
            </div>
            <p class="text-xs text-red-700/70">من معاملات debit</p>
        </div>
    </div>

    
    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-4 py-3 border-b border-slate-200 bg-slate-50 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
            <div>
                <h3 class="text-base font-black text-slate-900">اتجاه آخر 14 يوم</h3>
                <p class="text-xs text-slate-600 mt-0.5">إيراد، مصروفات، وصافي يومي.</p>
            </div>
        </div>
        <div class="p-4">
            <div class="relative w-full" style="height: 320px;">
                <canvas id="dailyTrendChart"></canvas>
            </div>
            <p x-show="!hasDailyChartData" class="text-center text-sm text-slate-500 py-4">لا توجد حركة مالية مسجّلة في آخر 14 يوم.</p>
        </div>
    </section>

    
    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-4 py-3 border-b border-slate-200 bg-slate-50 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
            <div>
                <h3 class="text-base font-black text-slate-900">التدفق النقدي اللحظي</h3>
                <p class="text-xs text-slate-600 mt-0.5">
                    آخر 6 ساعات — كل <span class="font-bold" x-text="realtime.bucket_minutes || 5"></span> دقائق
                </p>
            </div>
            <span class="text-[11px] font-semibold text-slate-500 bg-white px-2.5 py-1 rounded-lg border border-slate-200">مصدر: transactions</span>
        </div>
        <div class="p-4">
            <div class="relative w-full" style="height: 360px;">
                <canvas id="companyRealtimeChart"></canvas>
            </div>
            <p x-show="!hasRealtimeChartData" class="text-center text-sm text-slate-500 py-4">لا توجد معاملات في آخر 6 ساعات.</p>
        </div>
    </section>

    
    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-4 py-3 border-b border-slate-200 bg-slate-50 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
            <div>
                <h3 class="text-base font-black text-slate-900">اتجاه آخر 14 يوم</h3>
                <p class="text-xs text-slate-600 mt-0.5">ملخص يومي — إيراد، مصروفات، وصافي.</p>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="bg-slate-50 text-slate-700 border-b border-slate-200">
                        <th class="px-4 py-3 text-right font-semibold">اليوم</th>
                        <th class="px-4 py-3 text-center font-semibold">إيراد</th>
                        <th class="px-4 py-3 text-center font-semibold">مصروفات</th>
                        <th class="px-4 py-3 text-center font-semibold">صافي</th>
                        <th class="px-4 py-3 text-center font-semibold">الاتجاه</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <template x-for="(lbl, idx) in (daily.labels || [])" :key="idx">
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-3 font-semibold text-slate-900" x-text="lbl"></td>
                            <td class="px-4 py-3 text-center tabular-nums text-slate-700" x-text="fmt((daily.revenue || [])[idx] || 0)"></td>
                            <td class="px-4 py-3 text-center tabular-nums text-slate-700" x-text="fmt((daily.expenses || [])[idx] || 0)"></td>
                            <td class="px-4 py-3 text-center tabular-nums font-bold"
                                :class="(((daily.net || [])[idx] || 0) >= 0) ? 'text-emerald-700' : 'text-rose-700'"
                                x-text="fmt((daily.net || [])[idx] || 0)"></td>
                            <td class="px-4 py-3 text-center text-xs font-bold"
                                :class="(((daily.net || [])[idx] || 0) >= 0) ? 'text-emerald-700' : 'text-rose-700'">
                                <span x-text="(((daily.net || [])[idx] || 0) >= 0) ? '↑ ربح' : '↓ خسارة'"></span>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </section>
</div>

<?php $__env->startPush('scripts'); ?>
<script>window.__accountingInsightsInitial = <?php echo json_encode($initialPayload ?? [], 15, 512) ?>;</script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script>
(function () {
    // Chart.js instances must stay outside Alpine reactive state (Proxy breaks Chart.js).
    let dailyChart = null;
    let realtimeChart = null;

    function toNumbers(arr) {
        return (Array.isArray(arr) ? arr : []).map(v => Number(v) || 0);
    }

    function destroyChartInstance(chart) {
        if (chart && typeof chart.destroy === 'function') {
            try {
                chart.destroy();
            } catch (_) { /* already destroyed */ }
        }
    }

    function destroyChartOnCanvas(canvas) {
        if (!canvas || typeof Chart === 'undefined') return;
        const existing = Chart.getChart(canvas);
        if (existing) {
            try {
                existing.destroy();
            } catch (_) { /* ignore */ }
        }
    }

    function chartOptions() {
        return {
            responsive: true,
            maintainAspectRatio: false,
            animation: false,
            interaction: { mode: 'index', intersect: false },
            plugins: {
                legend: { display: true, position: 'bottom', labels: { boxWidth: 12, font: { size: 11 } } },
                tooltip: {
                    callbacks: {
                        label: (ctx) => {
                            const v = Number(ctx.raw || 0);
                            return `${ctx.dataset.label}: ${v.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })} ج.م`;
                        }
                    }
                }
            },
            scales: {
                x: { ticks: { maxRotation: 0, autoSkip: true, maxTicksLimit: 14 } },
                y: { ticks: { callback: (v) => Number(v).toLocaleString('en-US') } }
            }
        };
    }

    function createChart(canvas, config) {
        if (!canvas || typeof Chart === 'undefined') return null;
        destroyChartOnCanvas(canvas);
        return new Chart(canvas, config);
    }

    function buildDailyChart() {
        const el = document.getElementById('dailyTrendChart');
        if (!el || typeof Chart === 'undefined') return false;
        if (dailyChart && dailyChart.canvas === el) return true;

        destroyChartInstance(dailyChart);
        dailyChart = null;

        dailyChart = createChart(el, {
            type: 'line',
            data: {
                labels: [],
                datasets: [
                    { label: 'إيراد', data: [], borderColor: '#10b981', backgroundColor: 'rgba(16, 185, 129, 0.12)', tension: 0.3, pointRadius: 2, borderWidth: 2, fill: true },
                    { label: 'مصروف (إيراد)', data: [], borderColor: '#f97316', backgroundColor: 'rgba(249, 115, 22, 0.1)', tension: 0.3, pointRadius: 2, borderWidth: 2, fill: true },
                    { label: 'من جيب الشركة', data: [], borderColor: '#d97706', borderDash: [4, 4], backgroundColor: 'rgba(217, 119, 6, 0.08)', tension: 0.3, pointRadius: 2, borderWidth: 2, fill: false },
                    { label: 'صافي', data: [], borderColor: '#0ea5e9', backgroundColor: 'rgba(14, 165, 233, 0.08)', tension: 0.3, pointRadius: 2, borderWidth: 2, fill: false },
                ]
            },
            options: chartOptions(),
        });
        return !!dailyChart;
    }

    function buildRealtimeChart() {
        const el = document.getElementById('companyRealtimeChart');
        if (!el || typeof Chart === 'undefined') return false;
        if (realtimeChart && realtimeChart.canvas === el) return true;

        destroyChartInstance(realtimeChart);
        realtimeChart = null;

        realtimeChart = createChart(el, {
            type: 'line',
            data: {
                labels: [],
                datasets: [
                    { label: 'تدفق داخلي', data: [], borderColor: '#10b981', backgroundColor: 'rgba(16, 185, 129, 0.08)', tension: 0.3, pointRadius: 0, borderWidth: 2, fill: true },
                    { label: 'تدفق خارجي', data: [], borderColor: '#f43f5e', backgroundColor: 'rgba(244, 63, 94, 0.08)', tension: 0.3, pointRadius: 0, borderWidth: 2, fill: true },
                    { label: 'الصافي', data: [], borderColor: '#0ea5e9', backgroundColor: 'rgba(14, 165, 233, 0.08)', tension: 0.3, pointRadius: 0, borderWidth: 2, fill: false },
                ]
            },
            options: chartOptions(),
        });
        return !!realtimeChart;
    }

    function updateDailyChart(daily) {
        if (!buildDailyChart()) return;
        const d = daily || {};
        dailyChart.data.labels = Array.isArray(d.labels) ? [...d.labels] : [];
        const ds = dailyChart.data.datasets;
        if (ds[0]) ds[0].data = toNumbers(d.revenue);
        if (ds[1]) ds[1].data = toNumbers(d.expenses_revenue);
        if (ds[2]) ds[2].data = toNumbers(d.expenses_pocket);
        if (ds[3]) ds[3].data = toNumbers(d.net);
        dailyChart.update('none');
    }

    function updateRealtimeChart(realtime) {
        if (!buildRealtimeChart()) return;
        const rt = realtime || {};
        realtimeChart.data.labels = Array.isArray(rt.labels) ? [...rt.labels] : [];
        const ds = realtimeChart.data.datasets;
        if (ds[0]) ds[0].data = toNumbers(rt.cash_in);
        if (ds[1]) ds[1].data = toNumbers(rt.cash_out);
        if (ds[2]) ds[2].data = toNumbers(rt.net);
        realtimeChart.update('none');
    }

    function renderCharts(daily, realtime) {
        if (typeof Chart === 'undefined') {
            throw new Error('Chart.js not loaded');
        }
        updateDailyChart(daily);
        updateRealtimeChart(realtime);
    }

    const factory = () => ({
        loading: false,
        asOf: null,
        errorMsg: null,
        chartLoadError: null,
        snapshot: {},
        trend: {},
        daily: {},
        realtime: { labels: [], cash_in: [], cash_out: [], net: [], bucket_minutes: 5 },
        breakEvenMonth: {},
        breakEvenAllTime: {},
        receivables: { receivables: {}, payables: {} },
        healthLabel: null,
        healthDetail: null,
        healthTone: 'good',

        get healthBannerClass() {
            if (this.healthTone === 'bad') return 'border-rose-200 bg-rose-50';
            if (this.healthTone === 'warn') return 'border-amber-200 bg-amber-50';
            return 'border-emerald-200 bg-emerald-50';
        },

        get healthIconClass() {
            if (this.healthTone === 'bad') return 'text-rose-600';
            if (this.healthTone === 'warn') return 'text-amber-600';
            return 'text-emerald-600';
        },

        get hasRealtimeChartData() {
            const rt = this.realtime || {};
            const values = [...(rt.cash_in || []), ...(rt.cash_out || []), ...(rt.net || [])];
            return values.some(v => Math.abs(Number(v || 0)) > 0.001);
        },

        get hasDailyChartData() {
            const d = this.daily || {};
            const values = [...(d.revenue || []), ...(d.expenses || []), ...(d.net || [])];
            return values.some(v => Math.abs(Number(v || 0)) > 0.001);
        },

        fmt(v) {
            const n = Number(v || 0);
            return n.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' ج.م';
        },

        trendLabel(pct, inverseBad = false) {
            if (pct === null || pct === undefined) return '— لا يوجد أساس للمقارنة';
            const p = Number(pct);
            const arrow = p > 0 ? '↑' : (p < 0 ? '↓' : '→');
            const val = Math.abs(p).toLocaleString('en-US', { maximumFractionDigits: 1 });
            const good = inverseBad ? (p <= 0) : (p >= 0);
            return `${arrow} ${val}% ${good ? 'تحسن' : 'تراجع'}`;
        },

        trendClass(pct, inverseBad = false) {
            if (pct === null || pct === undefined) return 'text-slate-400';
            const p = Number(pct);
            const good = inverseBad ? (p <= 0) : (p >= 0);
            return good ? 'text-emerald-700' : 'text-rose-700';
        },

        applyPayload(data) {
            if (!data || typeof data !== 'object') return;
            this.snapshot = data.snapshot || {};
            this.trend = data.trend || {};
            this.daily = data.daily || {};
            this.realtime = data.realtime || this.realtime;
            this.breakEvenMonth = data.break_even_month || {};
            this.breakEvenAllTime = data.break_even_all_time || {};
            this.receivables = data.receivables || this.receivables;
            this.healthLabel = data.health?.label || null;
            this.healthDetail = data.health?.detail || null;
            this.healthTone = data.health?.tone || 'good';
            this.asOf = this.snapshot.as_of || null;
        },

        async refresh(force = false) {
            if (this.loading && !force) return;
            this.loading = true;
            try {
                const res = await fetch("<?php echo e(route('admin.accounting.insights.metrics')); ?>", {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                    credentials: 'same-origin',
                });
                const ct = res.headers.get('content-type') || '';
                if (!ct.includes('application/json')) {
                    this.errorMsg = 'تعذر تحميل المؤشرات (قد تكون الجلسة منتهية).';
                    return;
                }
                const data = await res.json();
                if (!res.ok) {
                    this.errorMsg = data.message || 'تعذر تحميل المؤشرات.';
                    return;
                }
                this.applyPayload(data);
                this.errorMsg = null;
                this.safeRenderCharts();
            } catch (e) {
                console.error('accounting insights refresh failed', e);
                this.errorMsg = 'حدث خطأ أثناء تحديث المؤشرات.';
            } finally {
                this.loading = false;
            }
        },

        waitForChartJs() {
            return new Promise((resolve) => {
                const tick = (attempt = 0) => {
                    if (typeof Chart !== 'undefined') {
                        resolve(true);
                        return;
                    }
                    if (attempt >= 50) {
                        resolve(false);
                        return;
                    }
                    setTimeout(() => tick(attempt + 1), 50);
                };
                tick();
            });
        },

        safeRenderCharts() {
            if (typeof Chart === 'undefined') {
                this.chartLoadError = 'تعذر تحميل مكتبة الرسوم البيانية.';
                return;
            }
            try {
                renderCharts(this.daily, this.realtime);
                this.chartLoadError = null;
            } catch (e) {
                console.error('accounting insights chart render failed', e);
                this.chartLoadError = 'حدث خطأ أثناء رسم المؤشرات: ' + (e?.message || e);
            }
        },

        waitForDom() {
            return new Promise(resolve => {
                requestAnimationFrame(() => requestAnimationFrame(resolve));
            });
        },

        async init() {
            this.applyPayload(window.__accountingInsightsInitial || {});
            await this.waitForDom();

            const ok = typeof Chart !== 'undefined' || await this.waitForChartJs();

            if (!ok) {
                this.chartLoadError = 'تعذر تحميل Chart.js.';
            } else {
                this.safeRenderCharts();
            }

            await this.refresh(true);
            setInterval(() => this.refresh(false), 30000);
        }
    });

    const register = () => Alpine.data('accountingInsights', factory);

    if (window.Alpine) {
        register();
    } else {
        document.addEventListener('alpine:init', register);
    }
})();
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/cityphone/Documents/all Mindlytics Project/Mindlytics/resources/views/admin/accounting/insights.blade.php ENDPATH**/ ?>