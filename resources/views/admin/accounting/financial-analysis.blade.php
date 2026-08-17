@extends('layouts.admin')

@section('title', 'التحليل المالي الشامل')
@section('header', 'التحليل المالي الشامل')

@push('styles')
<style>
@media print {
    .admin-sidebar-root, header, footer, .print\\:hidden { display: none !important; }
    main, .w-full { max-width: 100% !important; margin: 0 !important; padding: 0 !important; }
    body { background: #fff !important; }
}
</style>
@endpush

@section('content')
@php
    $summary = $report['summary'];
    $collections = $report['collections'];
    $reportQuery = ['period' => $period];
    if ($period === 'custom') {
        $reportQuery['start_date'] = $filter['filterStart'] ?? $startDate->format('Y-m-d');
        $reportQuery['end_date'] = $filter['filterEnd'] ?? $endDate->format('Y-m-d');
    }
    $totalRev = max(0.01, (float) $summary['total_revenue']);
    $totalExp = max(0.01, (float) $summary['total_expenses']);
@endphp

<div class="w-full space-y-6">
    {{-- شريط التنقل --}}
    <div class="flex flex-wrap items-center gap-3 rounded-2xl border border-sky-200 bg-sky-50/80 px-4 py-3 text-sm print:hidden">
        <a href="{{ route('admin.accounting.hub') }}" class="inline-flex items-center gap-2 font-semibold text-sky-800 hover:text-sky-950">
            <i class="fas fa-th-large"></i> مركز المحاسبة
        </a>
        <span class="text-slate-300">|</span>
        <a href="{{ route('admin.accounting.insights') }}" class="inline-flex items-center gap-2 font-semibold text-sky-800 hover:text-sky-950">
            <i class="fas fa-chart-bar"></i> المؤشرات
        </a>
        <span class="text-slate-300">|</span>
        <a href="{{ route('admin.accounting.reports') }}" class="inline-flex items-center gap-2 font-semibold text-sky-800 hover:text-sky-950">
            <i class="fas fa-file-alt"></i> التقارير
        </a>
    </div>

    {{-- الهيدر + التصدير --}}
    <section class="rounded-3xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-5 py-6 sm:px-8 border-b border-slate-200 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div>
                <h2 class="text-2xl font-black text-slate-900">التحليل المالي الشامل</h2>
                <p class="text-sm text-slate-500 mt-1">{{ $periodLabel }} — مذكرة تنفيذية، قوائم، نسب أ–و، تدفقات نقدية، توصيات</p>
                <p class="text-xs text-slate-400 mt-1">البيانات تبقى في النظام. التقرير طبقة عرض فقط — بلا نقل أو حذف للقيود.</p>
            </div>
            <div class="flex flex-wrap gap-2">
            <a href="{{ route('admin.accounting.financial-analysis.export', $reportQuery) }}"
               class="inline-flex items-center justify-center gap-2 rounded-2xl bg-emerald-600 px-6 py-3 text-sm font-bold text-white shadow-lg hover:bg-emerald-700 transition print:hidden">
                <i class="fas fa-file-excel text-lg"></i>
                تصدير Excel تحليلي (8 أوراق)
            </a>
            <button type="button" onclick="window.print()"
                    class="inline-flex items-center justify-center gap-2 rounded-2xl border border-slate-300 bg-white px-6 py-3 text-sm font-bold text-slate-800 hover:bg-slate-50 print:hidden">
                <i class="fas fa-print"></i>
                طباعة التقرير
            </button>
            </div>
        </div>
        <div class="px-5 py-6 sm:px-8 print:hidden">
            <form method="GET" action="{{ route('admin.accounting.financial-analysis') }}" class="space-y-5">
                @include('admin.accounting.partials.report-period-filter')
            </form>
        </div>
    </section>

    {{-- KPIs --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-5 gap-4">
        <div class="rounded-2xl border-2 border-emerald-200 bg-gradient-to-br from-white to-emerald-50 p-6 shadow-lg">
            <p class="text-xs font-bold uppercase tracking-widest text-emerald-600">إجمالي الإيرادات</p>
            <p class="mt-2 text-3xl font-black text-emerald-700 tabular-nums">{{ number_format($summary['total_revenue'], 2) }} <span class="text-base">ج.م</span></p>
            <p class="text-xs text-slate-500 mt-2">{{ $summary['payments_count'] }} عملية تحصيل</p>
        </div>
        <div class="rounded-2xl border-2 border-rose-200 bg-gradient-to-br from-white to-rose-50 p-6 shadow-lg">
            <p class="text-xs font-bold uppercase tracking-widest text-rose-600">إجمالي المصروفات</p>
            <p class="mt-2 text-3xl font-black text-rose-700 tabular-nums">{{ number_format($summary['total_expenses'], 2) }} <span class="text-base">ج.م</span></p>
            <p class="text-xs text-slate-500 mt-2">{{ $summary['expenses_count'] }} مصروف معتمد</p>
        </div>
        <div class="rounded-2xl border-2 {{ $summary['net_profit'] >= 0 ? 'border-sky-200' : 'border-rose-300' }} bg-gradient-to-br from-white {{ $summary['net_profit'] >= 0 ? 'to-sky-50' : 'to-rose-50' }} p-6 shadow-lg">
            <p class="text-xs font-bold uppercase tracking-widest text-slate-600">صافي الربح / الخسارة</p>
            <p class="mt-2 text-3xl font-black tabular-nums {{ $summary['net_profit'] >= 0 ? 'text-sky-700' : 'text-rose-700' }}">{{ number_format($summary['net_profit'], 2) }} <span class="text-base">ج.م</span></p>
            <p class="text-xs text-slate-500 mt-2">هامش: {{ $totalRev > 0 ? number_format(($summary['net_profit'] / $totalRev) * 100, 1) : 0 }}% — بعد التكلفة والعمولات</p>
        </div>
        <div class="rounded-2xl border-2 border-indigo-200 bg-gradient-to-br from-white to-indigo-50 p-6 shadow-lg">
            <p class="text-xs font-bold uppercase tracking-widest text-indigo-600">مجمل الربح</p>
            <p class="mt-2 text-3xl font-black text-indigo-700 tabular-nums">{{ number_format($summary['gross_profit'] ?? 0, 2) }} <span class="text-base">ج.م</span></p>
            <p class="text-xs text-slate-500 mt-2">بعد الرواتب وسحوبات المدربين</p>
        </div>
        <div class="rounded-2xl border-2 border-violet-200 bg-gradient-to-br from-white to-violet-50 p-6 shadow-lg">
            <p class="text-xs font-bold uppercase tracking-widest text-violet-600">الربح التشغيلي</p>
            <p class="mt-2 text-3xl font-black text-violet-700 tabular-nums">{{ number_format($summary['operating_profit'] ?? 0, 2) }} <span class="text-base">ج.م</span></p>
            <p class="text-xs text-slate-500 mt-2">بعد البيع والتشغيل وعمولات البوابات</p>
        </div>
    </div>

    @include('admin.accounting.partials.financial-statements')

    <div class="print:hidden space-y-6">
    {{-- نوع المنتج: مسجّل vs جروبات --}}
    <section class="rounded-2xl border border-slate-200 bg-white shadow-lg p-6">
        <h3 class="text-lg font-black text-slate-900 mb-1"><i class="fas fa-layer-group text-emerald-600 ml-1"></i> الإيراد حسب نوع المنتج</h3>
        <p class="text-xs text-slate-500 mb-4">الجروب الواحد ممكن يكون أونلاين أو أوفلاين حسب قناة حضور الطالب — مش حسب نوع الفاتورة فقط.</p>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="rounded-xl border border-sky-200 bg-sky-50 p-4">
                <p class="text-xs font-bold text-sky-700">كورسات مسجّلة</p>
                <p class="mt-1 text-2xl font-black text-sky-900 tabular-nums">{{ number_format($summary['recorded_course'] ?? 0, 2) }}</p>
                <p class="text-xs text-sky-700 mt-1">{{ $summary['recorded_course_count'] ?? 0 }} عملية — {{ number_format((($summary['recorded_course'] ?? 0) / $totalRev) * 100, 1) }}%</p>
            </div>
            <div class="rounded-xl border border-indigo-200 bg-indigo-50 p-4">
                <p class="text-xs font-bold text-indigo-700">جروبات أونلاين (لايف)</p>
                <p class="mt-1 text-2xl font-black text-indigo-900 tabular-nums">{{ number_format($summary['live_online_group'] ?? 0, 2) }}</p>
                <p class="text-xs text-indigo-700 mt-1">{{ $summary['live_online_group_count'] ?? 0 }} عملية — {{ number_format((($summary['live_online_group'] ?? 0) / $totalRev) * 100, 1) }}%</p>
            </div>
            <div class="rounded-xl border border-amber-200 bg-amber-50 p-4">
                <p class="text-xs font-bold text-amber-800">جروبات أوفلاين (حضور)</p>
                <p class="mt-1 text-2xl font-black text-amber-900 tabular-nums">{{ number_format($summary['live_offline_group'] ?? 0, 2) }}</p>
                <p class="text-xs text-amber-800 mt-1">{{ $summary['live_offline_group_count'] ?? 0 }} عملية — {{ number_format((($summary['live_offline_group'] ?? 0) / $totalRev) * 100, 1) }}%</p>
            </div>
        </div>
    </section>

    {{-- التحصيلات أونلاين vs أوفلاين --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <section class="rounded-2xl border border-slate-200 bg-white shadow-lg p-6">
            <h3 class="text-lg font-black text-slate-900 mb-4"><i class="fas fa-globe text-blue-600 ml-1"></i> طريقة التحصيل — بوابة vs نقدي/تحويل</h3>
            <p class="text-xs text-slate-500 mb-4">ده مش نوع الكورس. أونلاين هنا = دفع ببوابة (كاشير/فواتيرك). أوفلاين = نقدي أو تحويل أو محفظة.</p>
            <div class="grid grid-cols-2 gap-4 mb-6">
                <div class="rounded-xl bg-blue-50 border border-blue-200 p-4 text-center">
                    <p class="text-xs text-blue-700 font-semibold">أونلاين (بوابات)</p>
                    <p class="text-2xl font-black text-blue-800 tabular-nums mt-1">{{ number_format($summary['online_collections'], 2) }}</p>
                    <p class="text-xs text-blue-600 mt-1">{{ $summary['online_pct'] }}% من الإيراد</p>
                </div>
                <div class="rounded-xl bg-violet-50 border border-violet-200 p-4 text-center">
                    <p class="text-xs text-violet-700 font-semibold">أوفلاين / يدوي</p>
                    <p class="text-2xl font-black text-violet-800 tabular-nums mt-1">{{ number_format($summary['offline_collections'], 2) }}</p>
                    <p class="text-xs text-violet-600 mt-1">{{ $summary['offline_pct'] }}% من الإيراد</p>
                </div>
            </div>
            <div class="relative h-56">
                <canvas id="collectionsChart"></canvas>
            </div>
            @if($summary['gateway_fees'] > 0)
                <p class="text-xs text-amber-700 mt-4 bg-amber-50 border border-amber-200 rounded-lg px-3 py-2">
                    <i class="fas fa-info-circle"></i>
                    عمولات بوابات الدفع: <strong>{{ number_format($summary['gateway_fees'], 2) }} ج.م</strong>
                </p>
            @endif
        </section>

        <section class="rounded-2xl border border-slate-200 bg-white shadow-lg p-6">
            <h3 class="text-lg font-black text-slate-900 mb-4"><i class="fas fa-sitemap text-emerald-600 ml-1"></i> الإيرادات حسب نوع المنتج</h3>
            <div class="relative h-56 mb-4">
                <canvas id="revenueTypeChart"></canvas>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-slate-200 text-slate-600">
                            <th class="text-right py-2 font-semibold">المصدر</th>
                            <th class="text-center py-2 font-semibold">عدد</th>
                            <th class="text-left py-2 font-semibold">المبلغ</th>
                            <th class="text-left py-2 font-semibold">%</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($report['revenue_by_type'] as $item)
                        <tr class="border-b border-slate-100 hover:bg-slate-50">
                            <td class="py-2 font-medium text-slate-800">{{ $item['label'] }}</td>
                            <td class="py-2 text-center tabular-nums">{{ $item['count'] }}</td>
                            <td class="py-2 text-left tabular-nums font-semibold text-emerald-700">{{ number_format($item['total'], 2) }}</td>
                            <td class="py-2 text-left tabular-nums text-slate-500">{{ number_format(($item['total'] / $totalRev) * 100, 1) }}%</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>
    </div>

    {{-- تفصيل الإيرادات بالمنتج --}}
    <section class="rounded-2xl border border-slate-200 bg-white shadow-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200 bg-slate-50">
            <h3 class="text-lg font-black text-slate-900"><i class="fas fa-search-dollar text-emerald-600 ml-1"></i> من أين جاء كل إيراد — تفصيل دقيق</h3>
            <p class="text-xs text-slate-500 mt-1">مسجّل / جروب أونلاين / جروب أوفلاين — وأعمدة أونلاين/أوفلاين هنا تعني طريقة الدفع</p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-800 text-white">
                    <tr>
                        <th class="px-4 py-3 text-right font-semibold">نوع المنتج</th>
                        <th class="px-4 py-3 text-right font-semibold">المنتج / الجروب</th>
                        <th class="px-4 py-3 text-center font-semibold">عمليات</th>
                        <th class="px-4 py-3 text-left font-semibold">إجمالي</th>
                        <th class="px-4 py-3 text-left font-semibold">تحصيل بوابة</th>
                        <th class="px-4 py-3 text-left font-semibold">تحصيل نقدي/تحويل</th>
                        <th class="px-4 py-3 text-left font-semibold">%</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($report['revenue_by_product'] as $i => $item)
                    <tr class="{{ $i % 2 === 0 ? 'bg-white' : 'bg-slate-50' }} hover:bg-emerald-50/50">
                        <td class="px-4 py-3 font-medium text-slate-800">{{ $item['type_label'] }}</td>
                        <td class="px-4 py-3 text-slate-700 max-w-xs truncate" title="{{ $item['product_name'] }}">{{ $item['product_name'] }}</td>
                        <td class="px-4 py-3 text-center tabular-nums">{{ $item['count'] }}</td>
                        <td class="px-4 py-3 text-left tabular-nums font-bold text-emerald-700">{{ number_format($item['total'], 2) }}</td>
                        <td class="px-4 py-3 text-left tabular-nums text-blue-700">{{ number_format($item['online'], 2) }}</td>
                        <td class="px-4 py-3 text-left tabular-nums text-violet-700">{{ number_format($item['offline'], 2) }}</td>
                        <td class="px-4 py-3 text-left tabular-nums text-slate-500">{{ number_format(($item['total'] / $totalRev) * 100, 1) }}%</td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="px-4 py-8 text-center text-slate-500">لا توجد تحصيلات في هذه الفترة</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    {{-- تفصيل التحصيلات + المصروفات --}}
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
        {{-- أونلاين gateways --}}
        <section class="rounded-2xl border border-blue-200 bg-white shadow-lg p-6">
            <h3 class="text-base font-black text-blue-900 mb-4"><i class="fas fa-credit-card ml-1"></i> تفصيل البوابات (أونلاين)</h3>
            <ul class="space-y-2">
                @forelse($collections['online']['by_gateway'] as $gw)
                <li class="flex items-center justify-between rounded-xl bg-blue-50 border border-blue-100 px-4 py-3">
                    <span class="font-semibold text-blue-900">{{ $gw['label'] }}</span>
                    <div class="text-left">
                        <span class="font-black text-blue-800 tabular-nums">{{ number_format($gw['total'], 2) }}</span>
                        <span class="text-xs text-blue-600 mr-2">({{ $gw['count'] }} عملية)</span>
                    </div>
                </li>
                @empty
                <li class="text-sm text-slate-500">لا توجد تحصيلات أونلاين</li>
                @endforelse
            </ul>
        </section>

        {{-- أوفلاين methods --}}
        <section class="rounded-2xl border border-violet-200 bg-white shadow-lg p-6">
            <h3 class="text-base font-black text-violet-900 mb-4"><i class="fas fa-money-bill-wave ml-1"></i> تفصيل الأوفلاين / اليدوي</h3>
            <ul class="space-y-2">
                @forelse($collections['offline']['by_method'] as $method)
                <li class="flex items-center justify-between rounded-xl bg-violet-50 border border-violet-100 px-4 py-3">
                    <span class="font-semibold text-violet-900">{{ $method['label'] }}</span>
                    <div class="text-left">
                        <span class="font-black text-violet-800 tabular-nums">{{ number_format($method['total'], 2) }}</span>
                        <span class="text-xs text-violet-600 mr-2">({{ $method['count'] }} عملية)</span>
                    </div>
                </li>
                @empty
                <li class="text-sm text-slate-500">لا توجد تحصيلات أوفلاين</li>
                @endforelse
            </ul>
        </section>
    </div>

    @php $groupChannels = $collections['groups']['by_channel'] ?? []; @endphp
    @if(count($groupChannels))
    <section class="rounded-2xl border border-indigo-200 bg-white shadow-lg p-6">
        <h3 class="text-base font-black text-indigo-900 mb-4"><i class="fas fa-users ml-1"></i> تحصيلات الجروبات حسب قناة الحضور</h3>
        <ul class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            @foreach($groupChannels as $ch)
            <li class="flex items-center justify-between rounded-xl bg-indigo-50 border border-indigo-100 px-4 py-3">
                <span class="font-semibold text-indigo-900">{{ $ch['label'] ?? 'غير محدد' }}</span>
                <div class="text-left">
                    <span class="font-black text-indigo-800 tabular-nums">{{ number_format($ch['total'], 2) }}</span>
                    <span class="text-xs text-indigo-600 mr-2">({{ $ch['count'] }} عملية)</span>
                </div>
            </li>
            @endforeach
        </ul>
    </section>
    @endif

    {{-- المصروفات --}}
    <section class="rounded-2xl border border-rose-200 bg-white shadow-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-rose-100 bg-rose-50/50 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
            <div>
                <h3 class="text-lg font-black text-rose-900"><i class="fas fa-receipt ml-1"></i> المصروفات — إجمالي {{ number_format($summary['total_expenses'], 2) }} ج.م</h3>
                <p class="text-xs text-rose-700/70 mt-1">مصروفات معتمدة فقط — حسب الفئة ومصدر التمويل</p>
            </div>
        </div>
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 p-6">
            <div>
                <h4 class="text-sm font-bold text-slate-700 mb-3">حسب الفئة</h4>
                <div class="relative h-48 mb-4"><canvas id="expenseCategoryChart"></canvas></div>
                <ul class="space-y-1 text-sm">
                    @foreach($report['expenses_by_category'] as $item)
                    <li class="flex justify-between py-1 border-b border-slate-100">
                        <span>{{ $item['label'] }}</span>
                        <span class="font-semibold tabular-nums">{{ number_format($item['total'], 2) }} <span class="text-slate-400 text-xs">({{ number_format(($item['total'] / $totalExp) * 100, 1) }}%)</span></span>
                    </li>
                    @endforeach
                </ul>
            </div>
            <div>
                <h4 class="text-sm font-bold text-slate-700 mb-3">حسب مصدر التمويل</h4>
                <ul class="space-y-2">
                    @foreach($report['expenses_by_funding'] as $item)
                    <li class="rounded-xl border border-slate-200 px-4 py-3 flex justify-between items-center {{ str_contains($item['label'], 'جيب') ? 'bg-amber-50 border-amber-200' : 'bg-slate-50' }}">
                        <span class="font-medium text-slate-800">{{ $item['label'] }}</span>
                        <span class="font-black tabular-nums text-rose-700">{{ number_format($item['total'], 2) }}</span>
                    </li>
                    @endforeach
                </ul>
                @php $be = $report['break_even']; @endphp
                <div class="mt-4 rounded-xl border border-sky-200 bg-sky-50 p-4 text-sm">
                    <p class="font-bold text-sky-900">{{ $be['label'] }}</p>
                    <p class="text-xs text-sky-800 mt-1">{{ $be['detail'] }}</p>
                </div>
            </div>
        </div>
    </section>

    {{-- الاتجاه الشهري --}}
    <section class="rounded-2xl border border-slate-200 bg-white shadow-lg p-6">
        <h3 class="text-lg font-black text-slate-900 mb-4"><i class="fas fa-chart-area text-sky-600 ml-1"></i> الاتجاه الشهري</h3>
        <div class="relative h-72"><canvas id="monthlyTrendChart"></canvas></div>
    </section>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const chartDefaults = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { labels: { font: { family: 'inherit' } } } }
    };

    new Chart(document.getElementById('collectionsChart'), {
        type: 'doughnut',
        data: {
            labels: ['تحصيل بوابة', 'تحصيل نقدي/تحويل'],
            datasets: [{
                data: [{{ $summary['online_collections'] }}, {{ $summary['offline_collections'] }}],
                backgroundColor: ['#2563EB', '#7C3AED'],
                borderWidth: 2,
                borderColor: '#fff'
            }]
        },
        options: { ...chartDefaults, plugins: { ...chartDefaults.plugins, legend: { position: 'bottom' } } }
    });

    new Chart(document.getElementById('revenueTypeChart'), {
        type: 'bar',
        data: {
            labels: @json(array_column($report['revenue_by_type'], 'label')),
            datasets: [{
                label: 'الإيراد (ج.م)',
                data: @json(array_column($report['revenue_by_type'], 'total')),
                backgroundColor: '#059669',
                borderRadius: 6
            }]
        },
        options: {
            ...chartDefaults,
            indexAxis: 'y',
            scales: { x: { ticks: { callback: v => Number(v).toLocaleString() } } },
            plugins: { legend: { display: false } }
        }
    });

    new Chart(document.getElementById('expenseCategoryChart'), {
        type: 'doughnut',
        data: {
            labels: @json(array_column($report['expenses_by_category'], 'label')),
            datasets: [{
                data: @json(array_column($report['expenses_by_category'], 'total')),
                backgroundColor: ['#DC2626', '#F97316', '#EAB308', '#84CC16', '#06B6D4', '#8B5CF6', '#64748B']
            }]
        },
        options: { ...chartDefaults, plugins: { ...chartDefaults.plugins, legend: { position: 'bottom', labels: { boxWidth: 12 } } } }
    });

    new Chart(document.getElementById('monthlyTrendChart'), {
        type: 'line',
        data: {
            labels: @json($report['monthly']['labels']),
            datasets: [
                { label: 'إيراد', data: @json($report['monthly']['revenue']), borderColor: '#059669', backgroundColor: 'rgba(5,150,105,0.1)', fill: true, tension: 0.3 },
                { label: 'مصروف', data: @json($report['monthly']['expenses']), borderColor: '#DC2626', backgroundColor: 'rgba(220,38,38,0.08)', fill: true, tension: 0.3 },
                { label: 'صافي', data: @json($report['monthly']['net']), borderColor: '#2563EB', borderDash: [5, 5], tension: 0.3 }
            ]
        },
        options: {
            ...chartDefaults,
            interaction: { mode: 'index', intersect: false },
            scales: { y: { ticks: { callback: v => Number(v).toLocaleString() } } }
        }
    });
});
</script>
@endpush
