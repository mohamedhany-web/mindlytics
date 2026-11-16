@extends('sales::crm.layout')

@section('title', 'دقة التقارير اليومية')
@section('header', 'دقة التقارير اليومية')

@section('content')
@php
    $summary = $board['summary'] ?? [];
    $rows = $board['rows'] ?? [];
@endphp
<div class="space-y-6">
    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-4 py-4 bg-slate-50 border-b border-slate-200 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3">
            <div>
                <h2 class="text-xl font-black text-slate-900">مطابقة الفعلي مع المسلَّم</h2>
                <p class="text-xs text-slate-600 mt-1">فريق {{ $team->name ?? '—' }} · من النشاط الموثّق في CRM / واتساب / ميتا</p>
            </div>
            <a href="{{ route('employee.sales-manager.daily-reports.index') }}"
               class="inline-flex items-center gap-2 px-3 py-2 text-sm font-semibold text-slate-700 rounded-xl border border-slate-300 hover:bg-white">
                <i class="fas fa-clipboard-list"></i> التقارير اليومية
            </a>
        </div>

        <form method="get" class="p-4 grid grid-cols-1 sm:grid-cols-4 gap-3 items-end border-b border-slate-100">
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">من</label>
                <input type="date" name="from" value="{{ $from->toDateString() }}" class="w-full rounded-xl border-slate-300 text-sm">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">إلى</label>
                <input type="date" name="to" value="{{ $to->toDateString() }}" class="w-full rounded-xl border-slate-300 text-sm">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">الموظف</label>
                <select name="user_id" class="w-full rounded-xl border-slate-300 text-sm">
                    <option value="">كل الفريق</option>
                    @foreach($members as $m)
                        <option value="{{ $m->id }}" @selected((int) $userId === (int) $m->id)>{{ $m->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <button type="submit" class="w-full rounded-xl bg-teal-600 hover:bg-teal-700 text-white font-semibold px-4 py-2 text-sm">عرض</button>
            </div>
        </form>

        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 p-4">
            <div class="rounded-xl border border-slate-200 bg-slate-50 px-3 py-3">
                <p class="text-[11px] text-slate-500 font-semibold">تقارير مسلَّمة</p>
                <p class="text-2xl font-black text-slate-900">{{ number_format((int) ($summary['submitted_reports'] ?? 0)) }}</p>
            </div>
            <div class="rounded-xl border border-teal-200 bg-teal-50 px-3 py-3">
                <p class="text-[11px] text-teal-700 font-semibold">متوسط الدقة</p>
                <p class="text-2xl font-black text-teal-900">{{ $summary['accuracy_pct'] !== null ? number_format((float) $summary['accuracy_pct'], 1).'%' : '—' }}</p>
            </div>
            <div class="rounded-xl border border-rose-200 bg-rose-50 px-3 py-3">
                <p class="text-[11px] text-rose-700 font-semibold">أيام بتضخيم</p>
                <p class="text-2xl font-black text-rose-900">{{ number_format((int) ($summary['inflated_days'] ?? 0)) }}</p>
            </div>
            <div class="rounded-xl border border-amber-200 bg-amber-50 px-3 py-3">
                <p class="text-[11px] text-amber-700 font-semibold">أيام باختلاف أسماء</p>
                <p class="text-2xl font-black text-amber-900">{{ number_format((int) ($summary['mismatch_days'] ?? 0)) }}</p>
            </div>
        </div>
    </section>

    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50 text-xs text-slate-600">
                <tr>
                    <th class="px-3 py-3 text-right font-bold">التاريخ</th>
                    <th class="px-3 py-3 text-right font-bold">الموظف</th>
                    <th class="px-3 py-3 text-right font-bold">الدقة</th>
                    <th class="px-3 py-3 text-right font-bold">مكالمات س/ف</th>
                    <th class="px-3 py-3 text-right font-bold">ردود س/ف</th>
                    <th class="px-3 py-3 text-right font-bold">أسماء</th>
                    <th class="px-3 py-3 text-right font-bold">تنبيهات</th>
                    <th class="px-3 py-3 text-right font-bold"></th>
                </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                @forelse($rows as $row)
                    <tr class="hover:bg-slate-50/80">
                        <td class="px-3 py-3 whitespace-nowrap font-mono text-xs">{{ $row['report_date'] }}</td>
                        <td class="px-3 py-3 font-semibold text-slate-900">{{ $row['user_name'] }}</td>
                        <td class="px-3 py-3 font-black tabular-nums">{{ number_format((float) $row['accuracy_pct'], 1) }}%</td>
                        <td class="px-3 py-3 tabular-nums text-xs">
                            {{ $row['submitted']['calls_made'] }} / {{ $row['actual']['calls_made'] }}
                        </td>
                        <td class="px-3 py-3 tabular-nums text-xs">
                            {{ $row['submitted']['messages_replied'] }} / {{ $row['actual']['messages_replied'] }}
                        </td>
                        <td class="px-3 py-3 text-xs text-slate-600">
                            متطابق {{ $row['matched_contacts'] }}
                            · تقرير فقط {{ $row['report_only_contacts'] }}
                            · فعلي فقط {{ $row['actual_only_contacts'] }}
                        </td>
                        <td class="px-3 py-3">
                            <div class="flex flex-wrap gap-1">
                                @if($row['flags']['inflated'])
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-800">تضخيم</span>
                                @endif
                                @if($row['flags']['under_reported'])
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-sky-100 text-sky-800">أقل</span>
                                @endif
                                @if($row['flags']['contacts_mismatch'])
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-900">أسماء</span>
                                @endif
                                @if(empty($row['meta_linked']))
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-700">بدون Meta</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-3 py-3 whitespace-nowrap">
                            <a href="{{ route('employee.sales-manager.report-accuracy.show', $row['report_id']) }}"
                               class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-xs font-bold bg-teal-50 text-teal-800 hover:bg-teal-100">
                                تفاصيل
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-4 py-10 text-center text-slate-500">لا توجد تقارير مسلَّمة في الفترة المحددة</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>
@endsection
