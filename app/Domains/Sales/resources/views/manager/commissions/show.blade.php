@extends('layouts.employee')

@section('title', 'كوميشن — '.$employee->name)
@section('header', 'كوميشن — '.$employee->name)

@section('content')
@php $inputClass = 'w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm'; @endphp
<div class="space-y-5 pb-10">
    <section class="rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-4 py-4 border-b border-slate-200 bg-slate-50 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h1 class="text-xl font-black text-slate-900">{{ $employee->name }}</h1>
                <p class="text-xs text-slate-600 mt-1">
                    إعداد عام: <strong>{{ $employee->salesCommissionLabel() }}</strong>
                    · {{ $stats['agreements'] }} اتفاقية كورس نشطة
                    · {{ $periodLabel }}
                </p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('employee.sales-manager.commissions.index') }}" class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-bold text-slate-700 rounded-lg border border-slate-300 bg-white hover:bg-slate-50">رجوع</a>
                <a href="{{ route('employee.sales-manager.team.show', $employee) }}" class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-bold text-indigo-700 rounded-lg border border-indigo-200 bg-indigo-50 hover:bg-indigo-100">ملف الموظف</a>
            </div>
        </div>
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 p-4">
            <div class="rounded-xl border p-3"><p class="text-[11px] text-slate-500">Wins</p><p class="text-lg font-black">{{ $stats['confirmed_wins'] }}</p></div>
            <div class="rounded-xl border p-3"><p class="text-[11px] text-slate-500">كوميشن</p><p class="text-lg font-black text-emerald-700">{{ number_format($stats['commission_from_leads'], 2) }}</p></div>
            <div class="rounded-xl border p-3"><p class="text-[11px] text-slate-500">معلّق</p><p class="text-lg font-black text-amber-700">{{ $stats['pending_wins'] }}</p></div>
            <div class="rounded-xl border p-3"><p class="text-[11px] text-slate-500">تقدير معلّق</p><p class="text-lg font-black">{{ number_format($stats['pending_estimated'], 2) }}</p></div>
        </div>
    </section>

    <section class="rounded-2xl bg-white border border-violet-200 shadow-sm overflow-hidden">
        <div class="px-4 py-3 border-b border-violet-200 bg-violet-50">
            <h2 class="text-base font-black text-violet-950">اتفاقيات الكورسات</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="bg-slate-50 border-b">
                        <th class="px-3 py-2 text-right">النوع</th>
                        <th class="px-3 py-2 text-right">الكورس</th>
                        <th class="px-3 py-2 text-center">السعر</th>
                        <th class="px-3 py-2 text-right">وضع الحساب</th>
                        <th class="px-3 py-2 text-center">حالة</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($agreements as $agr)
                        <tr>
                            <td class="px-3 py-2">{{ $agr->courseTypeLabel() }}</td>
                            <td class="px-3 py-2 font-semibold">{{ $agr->courseTitle() }}</td>
                            <td class="px-3 py-2 text-center tabular-nums">{{ $agr->coursePrice() !== null ? number_format($agr->coursePrice(), 2) : '—' }}</td>
                            <td class="px-3 py-2 text-xs">{{ $agr->calcModeLabel() }}</td>
                            <td class="px-3 py-2 text-center text-xs font-bold {{ $agr->is_active ? 'text-emerald-700' : 'text-slate-500' }}">{{ $agr->is_active ? 'نشطة' : 'موقوفة' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-3 py-8 text-center text-slate-500">لا اتفاقيات كورس</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <section class="rounded-2xl bg-white border border-slate-200 shadow-sm p-4">
        <form method="get" class="grid grid-cols-1 sm:grid-cols-3 gap-3 items-end">
            <div>
                <label class="block text-xs font-semibold mb-1">العرض</label>
                <select name="view" class="{{ $inputClass }}">
                    <option value="all" @selected($view === 'all')>كل الفترات</option>
                    <option value="month" @selected($view === 'month')>شهري</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold mb-1">الشهر</label>
                <input type="month" name="year_month" value="{{ $yearMonth }}" class="{{ $inputClass }}">
            </div>
            <button class="rounded-xl bg-sky-600 text-white text-sm font-semibold px-4 py-2">تطبيق</button>
        </form>
    </section>

    <section class="rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-4 py-3 border-b bg-slate-50"><h2 class="font-black">العملاء المعتمدون</h2></div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="bg-slate-50 border-b">
                        <th class="px-3 py-2 text-right">العميل</th>
                        <th class="px-3 py-2 text-center">الكورس</th>
                        <th class="px-3 py-2 text-center">القيمة</th>
                        <th class="px-3 py-2 text-center">الكوميشن</th>
                        <th class="px-3 py-2 text-center">الاعتماد</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($confirmedLeads as $lead)
                        <tr>
                            <td class="px-3 py-2">
                                <a href="{{ route('employee.sales-manager.leads.show', $lead) }}" class="font-semibold text-emerald-700 hover:underline">{{ $lead->name }}</a>
                            </td>
                            <td class="px-3 py-2 text-center text-xs">
                                @if($lead->linkedCourseTitle())
                                    {{ $lead->linkedCourseTypeLabel() }}: {{ $lead->linkedCourseTitle() }}
                                @else — @endif
                            </td>
                            <td class="px-3 py-2 text-center tabular-nums">{{ number_format((float) ($lead->expected_value ?? 0), 2) }}</td>
                            <td class="px-3 py-2 text-center tabular-nums font-bold text-emerald-700">{{ number_format((float) ($lead->commission_amount ?? 0), 2) }}</td>
                            <td class="px-3 py-2 text-center text-xs">{{ $lead->won_confirmed_at?->format('Y-m-d H:i') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-3 py-8 text-center text-slate-500">لا يوجد</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    @if($pendingLeads->isNotEmpty())
        <section class="rounded-2xl bg-white border border-amber-200 shadow-sm overflow-hidden">
            <div class="px-4 py-3 border-b border-amber-200 bg-amber-50"><h2 class="font-black text-amber-950">بانتظار الاعتماد</h2></div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="border-b border-amber-100">
                            <th class="px-3 py-2 text-right">العميل</th>
                            <th class="px-3 py-2 text-center">الكورس</th>
                            <th class="px-3 py-2 text-center">تقدير</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @foreach($pendingLeads as $pl)
                            <tr>
                                <td class="px-3 py-2"><a href="{{ route('employee.sales-manager.leads.show', $pl) }}" class="font-semibold hover:underline">{{ $pl->name }}</a></td>
                                <td class="px-3 py-2 text-center text-xs">{{ $pl->linkedCourseTitle() ?? '—' }}</td>
                                <td class="px-3 py-2 text-center tabular-nums">{{ number_format($pendingEstimates[$pl->id] ?? 0, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>
    @endif
</div>
@endsection
