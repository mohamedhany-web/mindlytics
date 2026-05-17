

<?php $__env->startSection('title', 'مؤشرات الشركة (Real‑time)'); ?>
<?php $__env->startSection('header', 'مؤشرات الشركة (Real‑time)'); ?>

<?php $__env->startSection('content'); ?>
<div class="p-4 md:p-6 space-y-6" style="background:#f8fafc;min-height:100vh;" x-data="accountingInsights(<?php echo \Illuminate\Support\Js::from($initialPayload ?? [])->toHtml() ?>)">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <p class="text-sm text-gray-600 max-w-2xl">لوحة لحظية مبنية على بيانات النظام: إيراد، مصروفات، صافي ربح/خسارة، واتجاهات مقارنة بالفترة السابقة.</p>
            <p class="text-xs text-gray-500 mt-1">آخر تحديث: <span class="font-semibold" x-text="asOf || '—'"></span></p>
        </div>
        <div class="flex gap-2">
            <a href="<?php echo e(route('admin.accounting.hub')); ?>" class="px-4 py-2 rounded-xl bg-white border border-gray-200 text-sm font-semibold hover:bg-gray-50">
                <i class="fas fa-arrow-right ml-1"></i> مركز المحاسبة
            </a>
            <button type="button" @click="refresh(true)" class="px-4 py-2 rounded-xl bg-slate-900 text-white text-sm font-bold hover:bg-slate-950">
                <i class="fas fa-sync-alt ml-1"></i> تحديث الآن
            </button>
        </div>
    </div>

    <template x-if="errorMsg">
        <div class="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-rose-900 text-sm font-semibold">
            <i class="fas fa-exclamation-triangle ml-1"></i>
            <span x-text="errorMsg"></span>
        </div>
    </template>

    <div class="rounded-3xl bg-gradient-to-br from-slate-900 via-slate-800 to-sky-900 text-white shadow-xl overflow-hidden">
        <div class="px-6 py-6 border-b border-white/10 flex flex-wrap items-center justify-between gap-3">
            <div>
                <p class="text-xs font-semibold text-white/60">الحالة العامة</p>
                <p class="text-xl font-black" :class="healthToneClass" x-text="healthLabel || '—'"></p>
            </div>
            <div class="text-xs text-white/60">المقارنات: اليوم مقابل أمس — الشهر مقابل الشهر السابق</div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-px bg-white/10">
            <div class="bg-slate-900/80 p-5">
                <p class="text-xs font-semibold text-white/60">إيراد اليوم</p>
                <p class="mt-2 text-2xl font-black text-emerald-300 tabular-nums" x-text="fmt(snapshot.revenue_today)"></p>
                <p class="text-[11px] mt-1" :class="trendClass(trend.revenue_today_pct)" x-text="trendLabel(trend.revenue_today_pct)"></p>
            </div>
            <div class="bg-slate-900/80 p-5">
                <p class="text-xs font-semibold text-white/60">مصروفات اليوم</p>
                <p class="mt-2 text-2xl font-black text-rose-200 tabular-nums" x-text="fmt(snapshot.expenses_today)"></p>
                <p class="text-[11px] mt-1" :class="trendClass(-1 * (trend.expenses_today_pct ?? 0), true)" x-text="trendLabel(trend.expenses_today_pct, true)"></p>
            </div>
            <div class="bg-slate-900/80 p-5">
                <p class="text-xs font-semibold text-white/60">صافي اليوم</p>
                <p class="mt-2 text-2xl font-black tabular-nums" :class="(snapshot.net_today ?? 0) >= 0 ? 'text-sky-200' : 'text-amber-200'" x-text="fmt(snapshot.net_today)"></p>
                <p class="text-[11px] mt-1" :class="trendClass(trend.net_today_pct)" x-text="trendLabel(trend.net_today_pct)"></p>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-px bg-white/10 border-t border-white/10">
            <div class="bg-slate-900/60 p-5">
                <p class="text-xs font-semibold text-white/60">إيراد الشهر</p>
                <p class="mt-2 text-xl font-black text-emerald-200 tabular-nums" x-text="fmt(snapshot.revenue_month)"></p>
                <p class="text-[11px] mt-1" :class="trendClass(trend.revenue_month_pct)" x-text="trendLabel(trend.revenue_month_pct)"></p>
            </div>
            <div class="bg-slate-900/60 p-5">
                <p class="text-xs font-semibold text-white/60">مصروفات الشهر</p>
                <p class="mt-2 text-xl font-black text-rose-100 tabular-nums" x-text="fmt(snapshot.expenses_month)"></p>
                <p class="text-[11px] mt-1" :class="trendClass(-1 * (trend.expenses_month_pct ?? 0), true)" x-text="trendLabel(trend.expenses_month_pct, true)"></p>
            </div>
            <div class="bg-slate-900/60 p-5">
                <p class="text-xs font-semibold text-white/60">صافي الشهر</p>
                <p class="mt-2 text-xl font-black tabular-nums" :class="(snapshot.net_month ?? 0) >= 0 ? 'text-sky-100' : 'text-amber-200'" x-text="fmt(snapshot.net_month)"></p>
                <p class="text-[11px] mt-1" :class="trendClass(trend.net_month_pct)" x-text="trendLabel(trend.net_month_pct)"></p>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-px bg-white/10 border-t border-white/10">
            <div class="bg-slate-900/50 p-5">
                <p class="text-xs font-semibold text-white/60">تدفق داخلي (الشهر) — من المعاملات</p>
                <p class="mt-2 text-lg font-black text-emerald-100 tabular-nums" x-text="fmt(snapshot.cash_in_month)"></p>
            </div>
            <div class="bg-slate-900/50 p-5">
                <p class="text-xs font-semibold text-white/60">تدفق خارجي (الشهر) — من المعاملات</p>
                <p class="mt-2 text-lg font-black text-rose-100 tabular-nums" x-text="fmt(snapshot.cash_out_month)"></p>
            </div>
        </div>
    </div>

    <div class="rounded-2xl border border-gray-200 bg-white shadow-sm p-5">
        <div class="flex flex-wrap items-end justify-between gap-3">
            <div>
                <h2 class="text-lg font-black text-gray-900">الشارت الرئيسي (Real‑time)</h2>
                <p class="text-xs text-gray-500 mt-1">آخر 6 ساعات — كل <span class="font-bold" x-text="realtime.bucket_minutes || 5"></span> دقائق — Cash In / Cash Out / Net</p>
            </div>
            <div class="text-xs text-gray-500">
                <span class="font-semibold">مصدر البيانات:</span> معاملات النظام `transactions`
            </div>
        </div>
        <div class="mt-4">
            <div class="relative w-full" style="height: 420px;">
                <canvas id="companyRealtimeChart"></canvas>
            </div>
        </div>
    </div>

    <div class="rounded-2xl border border-gray-200 bg-white shadow-sm p-5">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <h2 class="text-base font-black text-gray-900">اتجاه آخر 14 يوم (مبسّط)</h2>
            <p class="text-xs text-gray-500">يتم تحديثه تلقائياً — ليس مخططاً تفصيلياً، لكنه يعطي اتجاه سريع.</p>
        </div>
        <div class="mt-4 overflow-x-auto">
            <table class="min-w-[860px] w-full text-sm">
                <thead>
                    <tr class="bg-slate-50 text-slate-600 text-xs font-bold">
                        <th class="px-3 py-2 text-right">اليوم</th>
                        <th class="px-3 py-2 text-center">إيراد</th>
                        <th class="px-3 py-2 text-center">مصروفات</th>
                        <th class="px-3 py-2 text-center">صافي</th>
                        <th class="px-3 py-2 text-left">الاتجاه</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <template x-for="(lbl, idx) in (daily.labels || [])" :key="idx">
                        <tr>
                            <td class="px-3 py-2 font-semibold text-gray-800" x-text="lbl"></td>
                            <td class="px-3 py-2 text-center tabular-nums" x-text="fmt((daily.revenue || [])[idx] || 0)"></td>
                            <td class="px-3 py-2 text-center tabular-nums" x-text="fmt((daily.expenses || [])[idx] || 0)"></td>
                            <td class="px-3 py-2 text-center tabular-nums font-bold" :class="(((daily.net || [])[idx] || 0) >= 0) ? 'text-emerald-700' : 'text-rose-700'" x-text="fmt((daily.net || [])[idx] || 0)"></td>
                            <td class="px-3 py-2 text-left text-xs">
                                <span :class="(((daily.net || [])[idx] || 0) >= 0) ? 'text-emerald-700 font-bold' : 'text-rose-700 font-bold'">
                                    <span x-text="(((daily.net || [])[idx] || 0) >= 0) ? '↑ ربح' : '↓ خسارة'"></span>
                                </span>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script>
function accountingInsights(initial) {
    return {
        loading: false,
        asOf: null,
        errorMsg: null,
        snapshot: {},
        trend: {},
        daily: {},
        realtime: { labels: [], cash_in: [], cash_out: [], net: [], bucket_minutes: 5 },
        healthLabel: null,
        healthTone: 'good',
        chart: null,

        get healthToneClass() {
            return this.healthTone === 'bad'
                ? 'text-rose-200'
                : this.healthTone === 'warn'
                    ? 'text-amber-200'
                    : 'text-emerald-200';
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
            const tag = good ? 'تحسن' : 'تراجع';
            return `${arrow} ${val}% ${tag}`;
        },

        trendClass(pct, inverseBad = false) {
            if (pct === null || pct === undefined) return 'text-white/45';
            const p = Number(pct);
            const good = inverseBad ? (p <= 0) : (p >= 0);
            return good ? 'text-emerald-200' : 'text-amber-200';
        },

        async refresh(force = false) {
            if (this.loading && !force) return;
            this.loading = true;
            try {
                const res = await fetch("<?php echo e(route('admin.accounting.insights.metrics')); ?>", { headers: { 'Accept': 'application/json' }});
                const ct = res.headers.get('content-type') || '';
                if (!res.ok || !ct.includes('application/json')) {
                    this.errorMsg = 'تعذر تحميل المؤشرات (قد تكون الجلسة منتهية أو تم تحويلك لصفحة تسجيل الدخول).';
                    return;
                }
                const data = await res.json();
                this.asOf = data.snapshot?.as_of || null;
                this.snapshot = data.snapshot || {};
                this.trend = data.trend || {};
                this.daily = data.daily || {};
                this.realtime = data.realtime || this.realtime;
                this.healthLabel = data.health?.label || null;
                this.healthTone = data.health?.tone || 'good';
                this.updateChart();
                this.errorMsg = null;
            } catch (e) {
                this.errorMsg = 'حدث خطأ أثناء تحديث المؤشرات.';
            } finally {
                this.loading = false;
            }
        },

        ensureChart() {
            if (this.chart) return;
            const el = document.getElementById('companyRealtimeChart');
            if (!el) return;

            const ctx = el.getContext('2d');
            this.chart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: [],
                    datasets: [
                        {
                            label: 'Cash In',
                            data: [],
                            borderColor: '#10b981',
                            backgroundColor: 'rgba(16, 185, 129, 0.10)',
                            tension: 0.25,
                            pointRadius: 0,
                            borderWidth: 2,
                        },
                        {
                            label: 'Cash Out',
                            data: [],
                            borderColor: '#f43f5e',
                            backgroundColor: 'rgba(244, 63, 94, 0.10)',
                            tension: 0.25,
                            pointRadius: 0,
                            borderWidth: 2,
                        },
                        {
                            label: 'Net',
                            data: [],
                            borderColor: '#0ea5e9',
                            backgroundColor: 'rgba(14, 165, 233, 0.10)',
                            tension: 0.25,
                            pointRadius: 0,
                            borderWidth: 2,
                        },
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: { mode: 'index', intersect: false },
                    plugins: {
                        legend: { display: true, position: 'top' },
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
                        y: {
                            ticks: {
                                callback: (v) => Number(v).toLocaleString('en-US'),
                            }
                        }
                    }
                }
            });
        },

        updateChart() {
            this.ensureChart();
            if (!this.chart) return;
            const rt = this.realtime || {};
            this.chart.data.labels = rt.labels || [];
            this.chart.data.datasets[0].data = rt.cash_in || [];
            this.chart.data.datasets[1].data = rt.cash_out || [];
            this.chart.data.datasets[2].data = rt.net || [];
            this.chart.update('none');
        },

        init() {
            this.ensureChart();
            // preload server-side data
            if (initial && typeof initial === 'object') {
                this.snapshot = initial.snapshot || {};
                this.trend = initial.trend || {};
                this.daily = initial.daily || {};
                this.realtime = initial.realtime || this.realtime;
                this.healthLabel = initial.health?.label || null;
                this.healthTone = initial.health?.tone || 'good';
                this.asOf = this.snapshot.as_of || null;
                this.updateChart();
            }

            this.refresh(true);
            setInterval(() => this.refresh(false), 10_000);
        }
    }
}
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/cityphone/Documents/all Mindlytics Project/Mindlytics/resources/views/admin/accounting/insights.blade.php ENDPATH**/ ?>