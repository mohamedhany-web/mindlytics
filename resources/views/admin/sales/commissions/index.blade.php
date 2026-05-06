@extends('layouts.admin')

@section('title', 'كوميشن المبيعات')
@section('header', 'كوميشن المبيعات')

@section('content')
<div class="w-full p-3 sm:p-6 space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div class="flex flex-wrap gap-2 text-sm">
            <a href="{{ route('admin.sales.leads.index') }}" class="text-emerald-700 hover:underline font-semibold"><i class="fas fa-arrow-right ml-1"></i> العملاء المحتملون</a>
            <span class="text-slate-300">|</span>
            <a href="{{ route('admin.sales.kpi.targets') }}" class="text-emerald-700 hover:underline font-semibold">أهداف KPIs</a>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 sm:p-6">
        <form method="get" action="{{ route('admin.sales.commissions.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
            <div>
                <label class="block text-xs font-bold text-slate-600 mb-1">العرض</label>
                <select name="view" class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm">
                    <option value="month" @selected($view === 'month')>حسب شهر الاعتماد</option>
                    <option value="all" @selected($view === 'all')>كل الفترات (تراكمي)</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-600 mb-1">الشهر (عند «حسب شهر»)</label>
                <input type="month" name="year_month" value="{{ $yearMonth }}" class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm" {{ $view === 'all' ? 'disabled' : '' }}>
            </div>
            <div class="md:col-span-2 flex gap-2">
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold">تطبيق</button>
                <a href="{{ route('admin.sales.commissions.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-300 text-slate-700 text-sm font-semibold hover:bg-slate-50">إعادة ضبط</a>
            </div>
        </form>
        <p class="text-xs text-slate-500 mt-3">يُحسب «المعتمد» من تاريخ <strong>اعتماد الفوز</strong> على الـ lead. «المعلّق» = won بدون اعتماد إداري بعد.</p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50/60 p-4">
            <p class="text-xs font-bold text-emerald-900">إجمالي الكوميشن (من الـ leads)</p>
            <p class="text-2xl font-black text-emerald-950 tabular-nums mt-1">{{ number_format($totals['commission_from_leads'], 2) }} ج.م</p>
            <p class="text-[11px] text-emerald-800 mt-1">{{ $periodLabel }}</p>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
            <p class="text-xs font-bold text-slate-700">إجمالي من قيود المعاملات</p>
            <p class="text-2xl font-black text-slate-900 tabular-nums mt-1">{{ number_format($totals['txn_commission'], 2) }} ج.م</p>
            <p class="text-[11px] text-slate-500 mt-1">للمقارنة مع أعمدة الـ leads</p>
        </div>
        <div class="rounded-2xl border border-sky-200 bg-sky-50/70 p-4">
            <p class="text-xs font-bold text-sky-900">قيمة expected للمعتمدة</p>
            <p class="text-2xl font-black text-sky-950 tabular-nums mt-1">{{ number_format($totals['expected_confirmed'], 2) }} ج.م</p>
        </div>
        <div class="rounded-2xl border border-amber-200 bg-amber-50/70 p-4">
            <p class="text-xs font-bold text-amber-900">نسبة الكوميشن على expected (الفريق)</p>
            <p class="text-2xl font-black text-amber-950 tabular-nums mt-1">{{ $teamRatePct !== null ? number_format($teamRatePct, 2) . '٪' : '—' }}</p>
            <p class="text-[11px] text-amber-800 mt-1">wins معتمدة: {{ $totals['confirmed_wins'] }} — معلّقة: {{ $totals['pending_wins'] }}</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-4 py-3 border-b border-slate-100 flex flex-wrap justify-between gap-2">
            <h2 class="text-lg font-black text-slate-900">تفصيل الموظفين</h2>
            <span class="text-xs font-semibold text-slate-500">تقدير المعلّق = إعداد كوميشن الموظف × expected لكل lead</span>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50 text-slate-600 text-xs font-bold uppercase tracking-wide">
                    <tr>
                        <th class="px-4 py-3 text-right">الموظف</th>
                        <th class="px-4 py-3 text-center">إعداد الكوميشن</th>
                        <th class="px-4 py-3 text-center tabular-nums">wins معتمدة</th>
                        <th class="px-4 py-3 text-center tabular-nums">expected معتمد</th>
                        <th class="px-4 py-3 text-center tabular-nums">كوميشن محقّق</th>
                        <th class="px-4 py-3 text-center tabular-nums">نسبة على expected</th>
                        <th class="px-4 py-3 text-center tabular-nums">قيود المعاملات</th>
                        <th class="px-4 py-3 text-center tabular-nums">wins معلّقة</th>
                        <th class="px-4 py-3 text-center tabular-nums">تقدير معلّق</th>
                        <th class="px-4 py-3 text-center">ملاحظة</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($rows as $r)
                        @php $u = $r['rep']; @endphp
                        <tr class="hover:bg-slate-50/80">
                            <td class="px-4 py-3 font-bold text-slate-900 whitespace-nowrap">{{ $u->name }}</td>
                            <td class="px-4 py-3 text-center text-xs text-slate-700">{{ $u->salesCommissionLabel() }}</td>
                            <td class="px-4 py-3 text-center tabular-nums font-semibold">{{ $r['confirmed_wins'] }}</td>
                            <td class="px-4 py-3 text-center tabular-nums">{{ number_format($r['expected_confirmed'], 2) }}</td>
                            <td class="px-4 py-3 text-center tabular-nums font-bold text-emerald-800">{{ number_format($r['commission_from_leads'], 2) }}</td>
                            <td class="px-4 py-3 text-center tabular-nums">{{ $r['commission_rate_pct'] !== null ? number_format($r['commission_rate_pct'], 2) . '٪' : '—' }}</td>
                            <td class="px-4 py-3 text-center tabular-nums text-slate-700">{{ number_format($r['txn_commission'], 2) }}</td>
                            <td class="px-4 py-3 text-center tabular-nums text-amber-800 font-semibold">{{ $r['pending_wins'] }}</td>
                            <td class="px-4 py-3 text-center tabular-nums text-amber-900">{{ number_format($r['pending_estimated'], 2) }}</td>
                            <td class="px-4 py-3 text-center text-xs">
                                @if($r['mismatch'])
                                    <span class="text-rose-700 font-bold" title="فرق بين مجموع الـ leads وقيود المعاملات">مراجعة</span>
                                @else
                                    <span class="text-slate-400">—</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="px-4 py-10 text-center text-slate-500">لا يوجد موظفو مبيعات مفعّلون.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
