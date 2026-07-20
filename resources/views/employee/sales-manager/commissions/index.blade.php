@extends('layouts.employee')

@section('title', 'كوميشن الفريق')
@section('header', 'كوميشن الفريق')

@section('content')
@php
    $inputClass = 'w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm';
@endphp
<div class="space-y-5 pb-10">
    <section class="rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-4 py-4 border-b border-slate-200 bg-slate-50 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h1 class="text-xl font-black text-slate-900">كوميشن فريق {{ $team->name }}</h1>
                <p class="text-xs text-slate-600 mt-1">اتفاقيات الكورسات والـ wins المعتمدة — الفترة: {{ $periodLabel }}</p>
            </div>
            <a href="{{ route('employee.sales-manager.dashboard') }}" class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-bold text-slate-700 rounded-lg border border-slate-300 bg-white hover:bg-slate-50">
                <i class="fas fa-arrow-right"></i> لوحة الفريق
            </a>
        </div>
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 p-4">
            <div class="rounded-xl border border-slate-200 p-3">
                <p class="text-[11px] text-slate-500">Wins معتمدة</p>
                <p class="text-lg font-black tabular-nums">{{ number_format($totals['confirmed_wins']) }}</p>
            </div>
            <div class="rounded-xl border border-slate-200 p-3">
                <p class="text-[11px] text-slate-500">كوميشن محقّق</p>
                <p class="text-lg font-black text-emerald-700 tabular-nums">{{ number_format($totals['commission_from_leads'], 2) }} ج.م</p>
            </div>
            <div class="rounded-xl border border-slate-200 p-3">
                <p class="text-[11px] text-slate-500">معلّق</p>
                <p class="text-lg font-black text-amber-700 tabular-nums">{{ number_format($totals['pending_wins']) }}</p>
            </div>
            <div class="rounded-xl border border-slate-200 p-3">
                <p class="text-[11px] text-slate-500">اتفاقيات كورس نشطة</p>
                <p class="text-lg font-black text-violet-700 tabular-nums">{{ number_format($totals['agreements']) }}</p>
            </div>
        </div>
    </section>

    <section class="rounded-2xl bg-white border border-slate-200 shadow-sm p-4">
        <form method="get" class="grid grid-cols-1 sm:grid-cols-3 gap-3 items-end">
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">العرض</label>
                <select name="view" class="{{ $inputClass }}">
                    <option value="month" @selected($view === 'month')>شهري</option>
                    <option value="all" @selected($view === 'all')>كل الفترات</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">الشهر</label>
                <input type="month" name="year_month" value="{{ $yearMonth }}" class="{{ $inputClass }}" {{ $view === 'all' ? 'disabled' : '' }}>
            </div>
            <button type="submit" class="rounded-xl bg-sky-600 hover:bg-sky-700 text-white text-sm font-semibold px-4 py-2">تطبيق</button>
        </form>
    </section>

    <section class="rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="bg-slate-50 text-slate-700 border-b border-slate-200">
                        <th class="px-4 py-3 text-right">الموظف</th>
                        <th class="px-4 py-3 text-center">إعداد عام</th>
                        <th class="px-4 py-3 text-center">اتفاقيات</th>
                        <th class="px-4 py-3 text-center">Wins</th>
                        <th class="px-4 py-3 text-center">كوميشن</th>
                        <th class="px-4 py-3 text-center">معلّق</th>
                        <th class="px-4 py-3 text-center">عرض</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($rows as $row)
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-3 font-bold">{{ $row['rep']->name }}</td>
                            <td class="px-4 py-3 text-center text-xs">{{ $row['label'] }}</td>
                            <td class="px-4 py-3 text-center tabular-nums">{{ $row['agreements'] }}</td>
                            <td class="px-4 py-3 text-center tabular-nums">{{ $row['confirmed_wins'] }}</td>
                            <td class="px-4 py-3 text-center tabular-nums font-semibold text-emerald-700">{{ number_format($row['commission_from_leads'], 2) }}</td>
                            <td class="px-4 py-3 text-center tabular-nums text-amber-700">{{ $row['pending_wins'] }}</td>
                            <td class="px-4 py-3 text-center">
                                <a href="{{ route('employee.sales-manager.commissions.show', $row['rep']) }}" class="text-sky-700 font-semibold text-xs hover:underline">التفاصيل</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-4 py-10 text-center text-slate-500">لا أعضاء في الفريق</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>
@endsection
