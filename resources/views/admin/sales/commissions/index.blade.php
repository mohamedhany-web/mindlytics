@extends('layouts.admin')

@section('title', 'كوميشن المبيعات')
@section('header', 'كوميشن المبيعات')

@section('content')
@php
    $repCount = count($rows);
    $mismatchCount = collect($rows)->where('mismatch', true)->count();
    $hasFilters = request()->hasAny(['view', 'year_month']) && (request('view', 'month') !== 'month' || request('year_month') !== now()->format('Y-m'));
    $inputClass = 'w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500 focus:border-sky-500';
    $statCards = [
        ['label' => 'كوميشن محقّق', 'value' => number_format($totals['commission_from_leads'], 2) . ' ج.م', 'icon' => 'fas fa-coins', 'bg' => 'bg-emerald-100', 'text' => 'text-emerald-600', 'description' => $periodLabel],
        ['label' => 'قيود المعاملات', 'value' => number_format($totals['txn_commission'], 2) . ' ج.م', 'icon' => 'fas fa-receipt', 'bg' => 'bg-sky-100', 'text' => 'text-sky-600', 'description' => 'للمقارنة مع الـ leads'],
        ['label' => 'expected معتمد', 'value' => number_format($totals['expected_confirmed'], 2) . ' ج.م', 'icon' => 'fas fa-chart-line', 'bg' => 'bg-violet-100', 'text' => 'text-violet-600', 'description' => 'قيمة الصفقات المعتمدة'],
        ['label' => 'wins / معلّقة', 'value' => $totals['confirmed_wins'] . ' / ' . $totals['pending_wins'], 'icon' => 'fas fa-trophy', 'bg' => 'bg-amber-100', 'text' => 'text-amber-600', 'description' => $teamRatePct !== null ? 'نسبة الفريق ' . number_format($teamRatePct, 2) . '%' : '—'],
    ];
@endphp

<div class="space-y-6">
    {{-- الهيدر --}}
    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-4 py-4 bg-slate-50 border-b border-slate-200 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-amber-500 to-amber-600 flex items-center justify-center text-white shadow-md">
                    <i class="fas fa-coins"></i>
                </div>
                <div>
                    <h2 class="text-xl font-black text-slate-900">كوميشن المبيعات</h2>
                    <p class="text-xs text-slate-600">المعتمد من تاريخ اعتماد الفوز — المعلّق = won بدون اعتماد إداري.</p>
                </div>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('admin.sales.leads.index') }}" class="inline-flex items-center gap-2 px-3 py-2 text-sm font-semibold text-slate-700 rounded-xl border border-slate-300 hover:bg-white">
                    <i class="fas fa-user-tag text-emerald-600"></i>
                    العملاء المحتملون
                </a>
                <a href="{{ route('admin.sales.kpi.targets') }}" class="inline-flex items-center gap-2 px-3 py-2 text-sm font-semibold text-slate-700 rounded-xl border border-slate-300 hover:bg-white">
                    <i class="fas fa-sliders-h text-violet-600"></i>
                    أهداف KPIs
                </a>
                <a href="{{ route('admin.sales.insights.index') }}" class="inline-flex items-center gap-2 px-3 py-2 text-sm font-semibold text-slate-700 rounded-xl border border-slate-300 hover:bg-white">
                    <i class="fas fa-chart-pie text-indigo-600"></i>
                    Insights
                </a>
            </div>
        </div>
        <div class="grid grid-cols-2 xl:grid-cols-4 gap-3 p-4">
            @foreach($statCards as $card)
                <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                    <div class="flex items-center justify-between gap-2">
                        <div class="min-w-0">
                            <p class="text-xs font-semibold text-slate-600 truncate">{{ $card['label'] }}</p>
                            <p class="text-lg font-black text-slate-900 truncate tabular-nums">{{ $card['value'] }}</p>
                        </div>
                        <div class="w-9 h-9 rounded-lg {{ $card['bg'] }} flex items-center justify-center {{ $card['text'] }} flex-shrink-0">
                            <i class="{{ $card['icon'] }} text-sm"></i>
                        </div>
                    </div>
                    <p class="text-[11px] text-slate-500 mt-1 truncate">{{ $card['description'] }}</p>
                </div>
            @endforeach
        </div>
        @if($mismatchCount > 0)
            <div class="px-4 pb-4">
                <p class="text-xs text-rose-800 bg-rose-50 border border-rose-200 rounded-xl px-3 py-2">
                    <i class="fas fa-exclamation-triangle text-rose-600 ml-1"></i>
                    يوجد <strong>{{ $mismatchCount }}</strong> موظف/ين بفرق بين كوميشن الـ leads وقيود المعاملات — راجع عمود «ملاحظة».
                </p>
            </div>
        @endif
        @if($totals['pending_estimated'] > 0)
            <div class="px-4 pb-4">
                <p class="text-xs text-amber-800 bg-amber-50 border border-amber-200 rounded-xl px-3 py-2">
                    <i class="fas fa-clock text-amber-600 ml-1"></i>
                    تقدير الكوميشن المعلّق للفريق: <strong>{{ number_format($totals['pending_estimated'], 2) }} ج.م</strong>
                </p>
            </div>
        @endif
    </section>

    {{-- الفلاتر --}}
    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-4 py-3 border-b border-slate-200 bg-slate-50">
            <h3 class="text-base font-black text-slate-900 flex items-center gap-2">
                <i class="fas fa-filter text-sky-600"></i>
                الفترة والعرض
            </h3>
        </div>
        <div class="p-4">
            <form method="get" action="{{ route('admin.sales.commissions.index') }}" class="flex flex-col gap-3">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 items-end">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">العرض</label>
                        <select name="view" id="view_sel" class="{{ $inputClass }}">
                            <option value="month" @selected($view === 'month')>حسب شهر الاعتماد</option>
                            <option value="all" @selected($view === 'all')>كل الفترات (تراكمي)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">الشهر</label>
                        <input type="month" name="year_month" id="year_month" value="{{ $yearMonth }}" class="{{ $inputClass }}" {{ $view === 'all' ? 'disabled' : '' }}>
                    </div>
                    <div class="flex items-center gap-2 sm:col-span-2">
                        <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-sky-600 hover:bg-sky-700 px-4 py-2 text-sm font-semibold text-white">
                            <i class="fas fa-search"></i>
                            تطبيق
                        </button>
                        @if($hasFilters)
                            <a href="{{ route('admin.sales.commissions.index') }}" class="inline-flex items-center justify-center rounded-xl border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                                <i class="fas fa-times"></i>
                            </a>
                        @endif
                    </div>
                </div>
            </form>
            <p class="text-xs text-slate-500 mt-3">
                الفترة المعروضة: <strong>{{ $periodLabel }}</strong>
                @if($view === 'month')
                    — wins معتمدة بتاريخ اعتماد داخل الشهر
                @else
                    — تراكمي لكل wins معتمدة
                @endif
            </p>
        </div>
    </section>

    {{-- الجدول --}}
    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-4 py-3 border-b border-slate-200 bg-slate-50 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
            <div>
                <h3 class="text-base font-black text-slate-900">تفصيل الموظفين</h3>
                <p class="text-xs text-slate-600">تقدير المعلّق = إعداد كوميشن الموظف × expected لكل lead.</p>
            </div>
            <span class="text-xs font-semibold text-sky-700 bg-sky-50 px-2.5 py-1 rounded-lg border border-sky-200">{{ $repCount }} موظف</span>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-[1100px] w-full text-sm">
                <thead>
                    <tr class="bg-slate-50 text-slate-700 border-b border-slate-200">
                        <th class="px-4 py-3 text-right font-semibold">الموظف</th>
                        <th class="px-4 py-3 text-center font-semibold">إعداد الكوميشن</th>
                        <th class="px-4 py-3 text-center font-semibold">wins معتمدة</th>
                        <th class="px-4 py-3 text-center font-semibold">expected معتمد</th>
                        <th class="px-4 py-3 text-center font-semibold">كوميشن محقّق</th>
                        <th class="px-4 py-3 text-center font-semibold">نسبة %</th>
                        <th class="px-4 py-3 text-center font-semibold">قيود المعاملات</th>
                        <th class="px-4 py-3 text-center font-semibold">wins معلّقة</th>
                        <th class="px-4 py-3 text-center font-semibold">تقدير معلّق</th>
                        <th class="px-4 py-3 text-center font-semibold">ملاحظة</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($rows as $r)
                        @php $u = $r['rep']; @endphp
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-3 font-semibold text-slate-900 whitespace-nowrap">{{ $u->name }}</td>
                            <td class="px-4 py-3 text-center">
                                <span class="inline-flex items-center rounded-md px-2 py-0.5 text-xs font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                                    {{ $u->salesCommissionLabel() }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center tabular-nums font-semibold">{{ $r['confirmed_wins'] }}</td>
                            <td class="px-4 py-3 text-center tabular-nums">{{ number_format($r['expected_confirmed'], 2) }}</td>
                            <td class="px-4 py-3 text-center tabular-nums font-bold text-emerald-700">{{ number_format($r['commission_from_leads'], 2) }}</td>
                            <td class="px-4 py-3 text-center tabular-nums">
                                @if($r['commission_rate_pct'] !== null)
                                    <span class="font-semibold text-sky-700">{{ number_format($r['commission_rate_pct'], 2) }}%</span>
                                @else
                                    <span class="text-slate-400">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center tabular-nums text-slate-700">{{ number_format($r['txn_commission'], 2) }}</td>
                            <td class="px-4 py-3 text-center tabular-nums">
                                @if($r['pending_wins'] > 0)
                                    <span class="font-bold text-amber-700">{{ $r['pending_wins'] }}</span>
                                @else
                                    <span class="text-slate-400">0</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center tabular-nums">
                                @if($r['pending_estimated'] > 0)
                                    <span class="font-semibold text-amber-800">{{ number_format($r['pending_estimated'], 2) }}</span>
                                @else
                                    <span class="text-slate-400">0.00</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($r['mismatch'])
                                    <span class="inline-flex items-center rounded-md px-2 py-0.5 text-xs font-semibold bg-rose-100 text-rose-700 border border-rose-200" title="فرق بين مجموع الـ leads وقيود المعاملات">
                                        <i class="fas fa-exclamation-circle ml-1"></i>
                                        مراجعة
                                    </span>
                                @else
                                    <span class="text-emerald-600 text-xs font-semibold">—</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="px-4 py-12 text-center">
                                <div class="w-14 h-14 mx-auto mb-3 rounded-xl bg-slate-100 flex items-center justify-center text-slate-400">
                                    <i class="fas fa-users text-xl"></i>
                                </div>
                                <p class="text-sm font-semibold text-slate-900">لا موظفو مبيعات</p>
                                <p class="text-xs text-slate-500 mt-1">لا يوجد موظفو مبيعات مفعّلون.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                @if($rows !== [])
                    <tfoot>
                        <tr class="bg-slate-50 border-t border-slate-200 font-bold text-slate-900">
                            <td class="px-4 py-3">الإجمالي</td>
                            <td class="px-4 py-3"></td>
                            <td class="px-4 py-3 text-center tabular-nums">{{ $totals['confirmed_wins'] }}</td>
                            <td class="px-4 py-3 text-center tabular-nums">{{ number_format($totals['expected_confirmed'], 2) }}</td>
                            <td class="px-4 py-3 text-center tabular-nums text-emerald-700">{{ number_format($totals['commission_from_leads'], 2) }}</td>
                            <td class="px-4 py-3 text-center tabular-nums">{{ $teamRatePct !== null ? number_format($teamRatePct, 2) . '%' : '—' }}</td>
                            <td class="px-4 py-3 text-center tabular-nums">{{ number_format($totals['txn_commission'], 2) }}</td>
                            <td class="px-4 py-3 text-center tabular-nums text-amber-700">{{ $totals['pending_wins'] }}</td>
                            <td class="px-4 py-3 text-center tabular-nums text-amber-800">{{ number_format($totals['pending_estimated'], 2) }}</td>
                            <td class="px-4 py-3"></td>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>
    </section>
</div>

@push('scripts')
<script>
(function () {
    const viewSel = document.getElementById('view_sel');
    const monthInput = document.getElementById('year_month');
    if (!viewSel || !monthInput) return;

    function toggleMonth() {
        monthInput.disabled = viewSel.value === 'all';
    }

    viewSel.addEventListener('change', toggleMonth);
    toggleMonth();
})();
</script>
@endpush
@endsection
