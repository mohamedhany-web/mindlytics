@extends('layouts.employee')

@section('title', 'تقرير '.$report->user->name)
@section('header', 'تقرير يومي — '.$report->user->name)

@section('content')
@php
    $campaignEntries = $campaignEntries ?? collect();
@endphp
<div class="space-y-6 max-w-4xl">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="text-lg font-black text-slate-900">{{ $report->user->name }}</h1>
            <p class="text-sm text-slate-500">{{ $report->report_date?->format('Y-m-d') }} · {{ $report->status === 'submitted' ? 'مُسلَّم' : 'مسودة' }}</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('employee.sales-manager.kpi.targets', ['user_id' => $report->user_id]) }}"
               class="inline-flex items-center gap-1.5 rounded-xl border border-slate-300 px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50">
                <i class="fas fa-bullseye text-teal-600"></i> أهداف KPI
            </a>
            <a href="{{ route('employee.sales-manager.campaign-reports.index', ['user_id' => $report->user_id, 'from' => $report->report_date?->toDateString(), 'to' => $report->report_date?->toDateString()]) }}"
               class="inline-flex items-center gap-1.5 rounded-xl border border-slate-300 px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50">
                <i class="fas fa-bullhorn text-violet-600"></i> كامبين اليوم
            </a>
            <a href="{{ route('employee.sales-manager.daily-reports.index') }}" class="inline-flex items-center gap-1.5 rounded-xl bg-slate-800 text-white px-3 py-2 text-xs font-semibold">← العودة</a>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 sm:p-6">
        <h2 class="text-sm font-black text-slate-800 mb-4 flex items-center gap-2">
            <i class="fas fa-chart-simple text-teal-600"></i>
            ملخص النشاط اليومي
        </h2>
        <div class="grid grid-cols-2 md:grid-cols-3 gap-3 text-sm">
            <div class="rounded-xl bg-slate-50 border border-slate-100 p-3"><span class="text-slate-500 text-xs block">مكالمات</span><strong class="text-lg tabular-nums">{{ $report->calls_made ?? '—' }}</strong></div>
            <div class="rounded-xl bg-slate-50 border border-slate-100 p-3"><span class="text-slate-500 text-xs block">Leads مؤهلة</span><strong class="text-lg tabular-nums">{{ $report->leads_qualified ?? '—' }}</strong></div>
            <div class="rounded-xl bg-slate-50 border border-slate-100 p-3"><span class="text-slate-500 text-xs block">حجوزات</span><strong class="text-lg tabular-nums">{{ $report->bookings_from_leads ?? '—' }}</strong></div>
            <div class="rounded-xl bg-slate-50 border border-slate-100 p-3"><span class="text-slate-500 text-xs block">متابعات</span><strong class="text-lg tabular-nums">{{ $report->followups_done ?? '—' }}</strong></div>
            <div class="rounded-xl bg-slate-50 border border-slate-100 p-3"><span class="text-slate-500 text-xs block">رسائل</span><strong class="text-lg tabular-nums">{{ $report->messages_replied ?? '—' }}</strong></div>
        </div>
        @if($report->activity_notes)
            <div class="mt-4"><p class="text-sm font-semibold text-slate-700">ملاحظات النشاط</p><p class="text-sm text-slate-600 mt-1 whitespace-pre-wrap">{{ $report->activity_notes }}</p></div>
        @endif
        @if($report->productivity_notes)
            <div class="mt-4"><p class="text-sm font-semibold text-slate-700">ملاحظات الإنتاجية</p><p class="text-sm text-slate-600 mt-1 whitespace-pre-wrap">{{ $report->productivity_notes }}</p></div>
        @endif
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-5 py-3 border-b border-slate-100 flex items-center justify-between gap-2">
            <h2 class="text-sm font-black text-slate-800 flex items-center gap-2">
                <i class="fas fa-bullhorn text-violet-600"></i>
                تقارير الكامبين لهذا اليوم
            </h2>
            <span class="text-xs font-semibold text-slate-500">{{ $campaignEntries->count() }} سجل</span>
        </div>
        @if($campaignEntries->isEmpty())
            <div class="p-8 text-center text-sm text-slate-500">لا توجد إدخالات كامبين لهذا اليوم.</div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm text-right">
                    <thead class="bg-slate-50 text-xs text-slate-500">
                        <tr>
                            <th class="px-4 py-2">الحملة</th>
                            <th class="px-4 py-2">جديدة</th>
                            <th class="px-4 py-2">واتساب</th>
                            <th class="px-4 py-2">ماسنجر</th>
                            <th class="px-4 py-2">إنستجرام</th>
                            <th class="px-4 py-2">Qual</th>
                            <th class="px-4 py-2">Conv</th>
                            <th class="px-4 py-2">ملاحظات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($campaignEntries as $c)
                            <tr class="hover:bg-slate-50">
                                <td class="px-4 py-2.5 font-medium text-slate-800">{{ $c->campaign?->name ?? '—' }}</td>
                                <td class="px-4 py-2.5 tabular-nums">{{ $c->new_messages }}</td>
                                <td class="px-4 py-2.5 tabular-nums">{{ $c->whatsapp_messages }}</td>
                                <td class="px-4 py-2.5 tabular-nums">{{ $c->messenger_messages }}</td>
                                <td class="px-4 py-2.5 tabular-nums">{{ $c->instagram_messages }}</td>
                                <td class="px-4 py-2.5 tabular-nums text-indigo-700 font-semibold">{{ $c->qualified }}</td>
                                <td class="px-4 py-2.5 tabular-nums text-emerald-700 font-semibold">{{ $c->converted }}</td>
                                <td class="px-4 py-2.5 text-slate-500 max-w-[180px] truncate" title="{{ $c->notes }}">{{ $c->notes ?: '—' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    @if($report->contacts && $report->contacts->isNotEmpty())
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-5 py-3 border-b border-slate-100">
                <h2 class="text-sm font-black text-slate-800">جهات الاتصال المذكورة</h2>
            </div>
            <ul class="divide-y divide-slate-100 text-sm">
                @foreach($report->contacts as $contact)
                    <li class="px-5 py-3 flex flex-wrap justify-between gap-2">
                        <span class="font-medium text-slate-800">{{ $contact->lead?->name ?? $contact->name ?? '—' }}</span>
                        <span class="text-slate-500">{{ $contact->notes ?? '' }}</span>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif
</div>
@endsection
