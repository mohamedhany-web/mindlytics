@extends('layouts.admin')

@section('title', 'مؤشرات المحاسبة')
@section('header', 'مؤشرات المحاسبة')

@section('content')
<div class="space-y-6" x-data="accountingInsights()">

    {{-- الهيدر --}}
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
                <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center gap-2 px-3 py-2 text-sm font-semibold text-slate-700 rounded-xl border border-slate-300 hover:bg-white">
                    <i class="fas fa-home text-slate-500"></i>
                    لوحة التحكم
                </a>
                <a href="{{ route('admin.accounting.hub') }}" class="inline-flex items-center gap-2 px-3 py-2 text-sm font-semibold text-slate-700 rounded-xl border border-slate-300 hover:bg-white">
                    <i class="fas fa-calculator text-sky-600"></i>
                    مركز المحاسبة
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

    {{-- الحالة العامة --}}
    <section class="rounded-2xl border shadow-lg overflow-hidden"
             :class="healthBannerClass">
        <div class="px-4 py-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-white/80 border border-white/60 flex items-center justify-center"
                     :class="healthIconClass">
                    <i class="fas fa-heartbeat text-lg"></i>
                </div>
                <div>
                    <p class="text-xs font-semibold text-slate-600">الحالة العامة</p>
                    <p class="text-lg font-black text-slate-900" x-text="healthLabel || '—'"></p>
                </div>
            </div>
            <p class="text-xs text-slate-600">المقارنات: اليوم ↔ أمس — الشهر ↔ الشهر السابق</p>
        </div>
    </section>

    {{-- مؤشرات اليوم والشهر --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
        {{-- إيراد اليوم --}}
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

        {{-- مصروفات اليوم --}}
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

        {{-- صافي اليوم --}}
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

        {{-- إيراد الشهر --}}
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

        {{-- مصروفات الشهر --}}
        <div class="dashboard-stat-card rounded-2xl border-2 border-orange-200/70 bg-gradient-to-br from-white via-white to-orange-50/60 p-5 shadow-lg">
            <div class="flex items-center justify-between gap-3 mb-3">
                <div class="min-w-0 flex-1">
                    <p class="text-sm font-bold text-orange-800/80 mb-1">مصروفات الشهر</p>
                    <p class="text-2xl font-black bg-gradient-to-r from-orange-700 to-amber-600 bg-clip-text text-transparent tabular-nums" x-text="fmt(snapshot.expenses_month)"></p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-orange-500 to-amber-500 flex items-center justify-center text-white shadow-md flex-shrink-0">
                    <i class="fas fa-receipt text-sm"></i>
                </div>
            </div>
            <p class="text-xs font-bold" :class="trendClass(-1 * (trend.expenses_month_pct ?? 0), true)" x-text="trendLabel(trend.expenses_month_pct, true)"></p>
        </div>

        {{-- صافي الشهر --}}
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

        {{-- تدفق داخلي --}}
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

        {{-- تدفق خارجي --}}
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

    {{-- شارت 14 يوم --}}
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

    {{-- الشارت اللحظي --}}
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

    {{-- اتجاه 14 يوم --}}
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

@push('scripts')
<script>window.__accountingInsightsInitial = @json($initialPayload ?? []);</script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script>
(function () {
    const factory = () => ({
        loading: false,
        asOf: null,
        errorMsg: null,
        snapshot: {},
        trend: {},
        daily: {},
        realtime: { labels: [], cash_in: [], cash_out: [], net: [], bucket_minutes: 5 },
        healthLabel: null,
        healthTone: 'good',
        realtimeChart: null,
        dailyChart: null,

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
            this.healthLabel = data.health?.label || null;
            this.healthTone = data.health?.tone || 'good';
            this.asOf = this.snapshot.as_of || null;
        },

        async refresh(force = false) {
            if (this.loading && !force) return;
            this.loading = true;
            try {
                const res = await fetch("{{ route('admin.accounting.insights.metrics') }}", {
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
                        resolve();
                        return;
                    }
                    if (attempt >= 50) {
                        resolve();
                        return;
                    }
                    setTimeout(() => tick(attempt + 1), 100);
                };
                tick();
            });
        },

        createChart(canvas, config) {
            if (!canvas || typeof Chart === 'undefined') return null;
            const existing = Chart.getChart(canvas);
            if (existing) {
                existing.destroy();
            }
            return new Chart(canvas, config);
        },

        chartOptions() {
            return {
                responsive: true,
                maintainAspectRatio: false,
                animation: { duration: 300 },
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
                    x: { ticks: { maxRotation: 0, autoSkip: true, maxTicksLimit: 12 } },
                    y: { ticks: { callback: (v) => Number(v).toLocaleString('en-US') } }
                }
            };
        },

        ensureDailyChart() {
            if (this.dailyChart) return;
            const el = document.getElementById('dailyTrendChart');
            if (!el) return;
            this.dailyChart = this.createChart(el, {
                type: 'line',
                data: {
                    labels: [],
                    datasets: [
                        { label: 'إيراد', data: [], borderColor: '#10b981', backgroundColor: 'rgba(16, 185, 129, 0.12)', tension: 0.3, pointRadius: 2, borderWidth: 2, fill: true },
                        { label: 'مصروفات', data: [], borderColor: '#f43f5e', backgroundColor: 'rgba(244, 63, 94, 0.12)', tension: 0.3, pointRadius: 2, borderWidth: 2, fill: true },
                        { label: 'صافي', data: [], borderColor: '#0ea5e9', backgroundColor: 'rgba(14, 165, 233, 0.08)', tension: 0.3, pointRadius: 2, borderWidth: 2, fill: false },
                    ]
                },
                options: this.chartOptions(),
            });
        },

        ensureRealtimeChart() {
            if (this.realtimeChart) return;
            const el = document.getElementById('companyRealtimeChart');
            if (!el) return;
            this.realtimeChart = this.createChart(el, {
                type: 'line',
                data: {
                    labels: [],
                    datasets: [
                        { label: 'تدفق داخلي', data: [], borderColor: '#10b981', backgroundColor: 'rgba(16, 185, 129, 0.08)', tension: 0.3, pointRadius: 0, borderWidth: 2, fill: true },
                        { label: 'تدفق خارجي', data: [], borderColor: '#f43f5e', backgroundColor: 'rgba(244, 63, 94, 0.08)', tension: 0.3, pointRadius: 0, borderWidth: 2, fill: true },
                        { label: 'الصافي', data: [], borderColor: '#0ea5e9', backgroundColor: 'rgba(14, 165, 233, 0.08)', tension: 0.3, pointRadius: 0, borderWidth: 2, fill: false },
                    ]
                },
                options: this.chartOptions(),
            });
        },

        safeRenderCharts() {
            if (typeof Chart === 'undefined') return;
            try {
                this.ensureDailyChart();
                this.ensureRealtimeChart();

                const d = this.daily || {};
                if (this.dailyChart) {
                    this.dailyChart.data.labels = d.labels || [];
                    this.dailyChart.data.datasets[0].data = d.revenue || [];
                    this.dailyChart.data.datasets[1].data = d.expenses || [];
                    this.dailyChart.data.datasets[2].data = d.net || [];
                    this.dailyChart.update('none');
                }

                const rt = this.realtime || {};
                if (this.realtimeChart) {
                    this.realtimeChart.data.labels = rt.labels || [];
                    this.realtimeChart.data.datasets[0].data = rt.cash_in || [];
                    this.realtimeChart.data.datasets[1].data = rt.cash_out || [];
                    this.realtimeChart.data.datasets[2].data = rt.net || [];
                    this.realtimeChart.update('none');
                }
            } catch (e) {
                console.error('accounting insights chart render failed', e);
            }
        },

        async init() {
            this.applyPayload(window.__accountingInsightsInitial || {});
            await this.waitForChartJs();
            this.safeRenderCharts();
            await this.refresh(true);
            setInterval(() => this.refresh(false), 10_000);
        }
    });

    if (window.Alpine) {
        Alpine.data('accountingInsights', factory);
    } else {
        document.addEventListener('alpine:init', () => Alpine.data('accountingInsights', factory));
    }
})();
</script>
@endpush
@endsection
