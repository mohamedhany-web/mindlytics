@extends('layouts.employee')

@section('title', 'تقارير الكامبين')
@section('header', 'تقارير الكامبين — الفريق')

@section('content')
@php
    $statCards = [
        ['label' => 'رسائل جديدة', 'value' => $totals['new_messages'] ?? 0, 'tone' => 'text-sky-700 bg-sky-50 border-sky-200'],
        ['label' => 'واتساب', 'value' => $totals['whatsapp_messages'] ?? 0, 'tone' => 'text-emerald-700 bg-emerald-50 border-emerald-200'],
        ['label' => 'ماسنجر', 'value' => $totals['messenger_messages'] ?? 0, 'tone' => 'text-blue-700 bg-blue-50 border-blue-200'],
        ['label' => 'إنستجرام', 'value' => $totals['instagram_messages'] ?? 0, 'tone' => 'text-pink-700 bg-pink-50 border-pink-200'],
        ['label' => 'Qualified', 'value' => $totals['qualified'] ?? 0, 'tone' => 'text-indigo-700 bg-indigo-50 border-indigo-200'],
        ['label' => 'Unqualified', 'value' => $totals['unqualified'] ?? 0, 'tone' => 'text-slate-700 bg-slate-50 border-slate-200'],
        ['label' => 'Converted', 'value' => $totals['converted'] ?? 0, 'tone' => 'text-amber-700 bg-amber-50 border-amber-200'],
    ];
@endphp

<div class="space-y-6">
    <div class="rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-4 py-4 sm:px-6 border-b border-slate-100 flex flex-wrap items-center justify-between gap-3">
            <div>
                <h1 class="text-lg font-black text-slate-900 flex items-center gap-2">
                    <i class="fas fa-bullhorn text-teal-600"></i>
                    تقارير الكامبين
                </h1>
                <p class="text-xs sm:text-sm text-slate-500 mt-1">
                    {{ $team->name }} — ما يرفعه فريق السيلز يومياً لكل حملة إعلانية
                </p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('employee.sales-manager.daily-reports.index') }}"
                   class="inline-flex items-center gap-2 rounded-xl border border-slate-300 px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                    <i class="fas fa-file-lines"></i> التقارير اليومية
                </a>
                @if($ready)
                    <a href="{{ route('employee.sales-manager.campaign-reports.export', request()->query()) }}"
                       class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 px-3 py-2 text-sm font-semibold text-white">
                        <i class="fas fa-file-csv"></i> تصدير CSV
                    </a>
                @endif
            </div>
        </div>

        @if(! $ready)
            <div class="p-8 text-center text-slate-500">
                <i class="fas fa-database text-3xl text-slate-300 mb-3 block"></i>
                <p class="font-semibold text-slate-800">جداول تقارير الكامبين غير جاهزة بعد</p>
                <p class="text-sm mt-1">راجع إعدادات الحملات الإعلانية أو نفّذ الترحيلات.</p>
            </div>
        @else
            <div class="p-4 sm:p-6">
                <form method="GET" action="{{ route('employee.sales-manager.campaign-reports.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 items-end">
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">من</label>
                        <input type="date" name="from" value="{{ $from->toDateString() }}" class="w-full rounded-xl border-slate-300 text-sm focus:ring-teal-500 focus:border-teal-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">إلى</label>
                        <input type="date" name="to" value="{{ $to->toDateString() }}" class="w-full rounded-xl border-slate-300 text-sm focus:ring-teal-500 focus:border-teal-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">الحملة</label>
                        <select name="campaign_id" class="w-full rounded-xl border-slate-300 text-sm focus:ring-teal-500 focus:border-teal-500">
                            <option value="">كل الحملات</option>
                            @foreach($campaigns as $c)
                                <option value="{{ $c->id }}" @selected($campaignId == $c->id)>{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">الموظف</label>
                        <select name="user_id" class="w-full rounded-xl border-slate-300 text-sm focus:ring-teal-500 focus:border-teal-500">
                            <option value="">كل الفريق</option>
                            @foreach($salesReps as $rep)
                                <option value="{{ $rep->id }}" @selected($userId == $rep->id)>{{ $rep->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex gap-2">
                        <button type="submit" class="flex-1 rounded-xl bg-slate-800 hover:bg-slate-900 text-white text-sm font-semibold px-4 py-2.5">تصفية</button>
                        <a href="{{ route('employee.sales-manager.campaign-reports.index') }}" class="rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-semibold px-4 py-2.5">مسح</a>
                    </div>
                </form>
            </div>
        @endif
    </div>

    @if($ready)
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-7 gap-3">
            @foreach($statCards as $card)
                <div class="rounded-2xl border p-4 {{ $card['tone'] }}">
                    <p class="text-xs font-semibold opacity-80">{{ $card['label'] }}</p>
                    <p class="text-2xl font-black mt-1 tabular-nums">{{ number_format($card['value']) }}</p>
                </div>
            @endforeach
        </div>

        @if($perRep->isNotEmpty())
            <div class="rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-4 py-3 sm:px-6 border-b border-slate-100">
                    <h2 class="text-base font-bold text-slate-900">أداء الموظفين على الكامبين</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm text-right">
                        <thead class="bg-slate-50 text-xs text-slate-500">
                            <tr>
                                <th class="px-4 py-3">الموظف</th>
                                <th class="px-4 py-3">رسائل</th>
                                <th class="px-4 py-3">Qualified</th>
                                <th class="px-4 py-3">Converted</th>
                                <th class="px-4 py-3">سجلات</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($perRep as $row)
                                <tr class="hover:bg-slate-50">
                                    <td class="px-4 py-3 font-semibold text-slate-800">{{ $row['user']?->name ?? '—' }}</td>
                                    <td class="px-4 py-3 tabular-nums">{{ number_format($row['messages']) }}</td>
                                    <td class="px-4 py-3 tabular-nums text-indigo-700 font-semibold">{{ number_format($row['qualified']) }}</td>
                                    <td class="px-4 py-3 tabular-nums text-emerald-700 font-semibold">{{ number_format($row['converted']) }}</td>
                                    <td class="px-4 py-3 tabular-nums text-slate-500">{{ $row['entries'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        @if($perCampaign->isNotEmpty())
            <div class="rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-4 py-3 sm:px-6 border-b border-slate-100">
                    <h2 class="text-base font-bold text-slate-900">ملخص لكل حملة</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm text-right">
                        <thead class="bg-slate-50 text-xs text-slate-500">
                            <tr>
                                <th class="px-4 py-3">الحملة</th>
                                <th class="px-4 py-3">التكلفة</th>
                                <th class="px-4 py-3">رسائل</th>
                                <th class="px-4 py-3">Qualified</th>
                                <th class="px-4 py-3">Converted</th>
                                <th class="px-4 py-3">تكلفة الرسالة</th>
                                <th class="px-4 py-3">تكلفة التحويل</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($perCampaign as $row)
                                <tr class="hover:bg-slate-50">
                                    <td class="px-4 py-3 font-medium text-slate-800">{{ $row['campaign']?->name ?? '—' }}</td>
                                    <td class="px-4 py-3 tabular-nums">{{ number_format($row['cost'], 2) }}</td>
                                    <td class="px-4 py-3 tabular-nums">{{ number_format($row['messages']) }}</td>
                                    <td class="px-4 py-3 tabular-nums text-indigo-700 font-semibold">{{ number_format($row['qualified']) }}</td>
                                    <td class="px-4 py-3 tabular-nums text-emerald-700 font-semibold">{{ number_format($row['converted']) }}</td>
                                    <td class="px-4 py-3 tabular-nums text-slate-600">{{ $row['cost_per_message'] !== null ? number_format($row['cost_per_message'], 2) : '—' }}</td>
                                    <td class="px-4 py-3 tabular-nums text-slate-600">{{ $row['cost_per_converted'] !== null ? number_format($row['cost_per_converted'], 2) : '—' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        <div class="rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-4 py-3 sm:px-6 border-b border-slate-100">
                <h2 class="text-base font-bold text-slate-900">التفاصيل اليومية ({{ number_format($rows->count()) }})</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm text-right">
                    <thead class="bg-slate-50 text-xs text-slate-500">
                        <tr>
                            <th class="px-3 py-3">التاريخ</th>
                            <th class="px-3 py-3">الحملة</th>
                            <th class="px-3 py-3">الموظف</th>
                            <th class="px-3 py-3">جديدة</th>
                            <th class="px-3 py-3">واتساب</th>
                            <th class="px-3 py-3">ماسنجر</th>
                            <th class="px-3 py-3">إنستجرام</th>
                            <th class="px-3 py-3">Qual</th>
                            <th class="px-3 py-3">Unqual</th>
                            <th class="px-3 py-3">Conv</th>
                            <th class="px-3 py-3">ملاحظات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($rows as $r)
                            <tr class="hover:bg-slate-50">
                                <td class="px-3 py-3 text-slate-600 whitespace-nowrap">{{ $r->report_date?->format('Y-m-d') }}</td>
                                <td class="px-3 py-3 font-medium text-slate-800">{{ $r->campaign?->name ?? '—' }}</td>
                                <td class="px-3 py-3 text-slate-600">{{ $r->user?->name ?? '—' }}</td>
                                <td class="px-3 py-3 tabular-nums">{{ $r->new_messages }}</td>
                                <td class="px-3 py-3 tabular-nums">{{ $r->whatsapp_messages }}</td>
                                <td class="px-3 py-3 tabular-nums">{{ $r->messenger_messages }}</td>
                                <td class="px-3 py-3 tabular-nums">{{ $r->instagram_messages }}</td>
                                <td class="px-3 py-3 tabular-nums text-indigo-700 font-semibold">{{ $r->qualified }}</td>
                                <td class="px-3 py-3 tabular-nums text-slate-500">{{ $r->unqualified }}</td>
                                <td class="px-3 py-3 tabular-nums text-emerald-700 font-semibold">{{ $r->converted }}</td>
                                <td class="px-3 py-3 text-slate-500 max-w-[200px] truncate" title="{{ $r->notes }}">{{ $r->notes ?: '—' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11" class="px-4 py-12 text-center text-slate-500">لا توجد تقارير كامبين في هذه الفترة.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>
@endsection
