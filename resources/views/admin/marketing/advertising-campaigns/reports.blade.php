@extends('layouts.admin')
@section('title', 'تقارير الكامبين من السيلز')
@section('header', 'تقارير الكامبين من السيلز')
@section('content')
@php
    $statCards = [
        ['label' => 'رسائل جديدة', 'value' => $totals['new_messages'], 'color' => 'sky', 'icon' => 'fa-comment-dots'],
        ['label' => 'واتساب', 'value' => $totals['whatsapp_messages'], 'color' => 'emerald', 'icon' => 'fa-whatsapp'],
        ['label' => 'ماسنجر', 'value' => $totals['messenger_messages'], 'color' => 'blue', 'icon' => 'fa-facebook-messenger'],
        ['label' => 'إنستجرام', 'value' => $totals['instagram_messages'], 'color' => 'pink', 'icon' => 'fa-instagram'],
        ['label' => 'Qualified', 'value' => $totals['qualified'], 'color' => 'indigo', 'icon' => 'fa-user-check'],
        ['label' => 'Unqualified', 'value' => $totals['unqualified'], 'color' => 'slate', 'icon' => 'fa-user-xmark'],
        ['label' => 'Converted', 'value' => $totals['converted'], 'color' => 'amber', 'icon' => 'fa-trophy'],
    ];
    $colorMap = [
        'sky' => 'bg-sky-50 text-sky-700 border-sky-200',
        'emerald' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
        'blue' => 'bg-blue-50 text-blue-700 border-blue-200',
        'pink' => 'bg-pink-50 text-pink-700 border-pink-200',
        'indigo' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
        'slate' => 'bg-slate-50 text-slate-700 border-slate-200',
        'amber' => 'bg-amber-50 text-amber-700 border-amber-200',
    ];
@endphp
<div class="w-full space-y-6">
    {{-- Filters --}}
    <div class="rounded-3xl bg-white/95 backdrop-blur border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-5 py-6 sm:px-8 border-b border-slate-200 flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">تقارير الكامبين من السيلز</h1>
                <p class="text-slate-500 mt-1">البيانات اليومية التي يرفعها موظفو السيلز لكل حملة إعلانية.</p>
            </div>
            <a href="{{ route('admin.advertising-campaigns.reports.export', request()->query()) }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-semibold shadow-lg shadow-emerald-500/30 transition-all">
                <i class="fas fa-file-csv"></i> تصدير CSV
            </a>
        </div>
        <div class="p-5 sm:p-8">
            <form method="GET" action="{{ route('admin.advertising-campaigns.reports') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 items-end">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">من تاريخ</label>
                    <input type="date" name="from" value="{{ $from->toDateString() }}" class="w-full rounded-xl border-slate-200 focus:border-sky-500 focus:ring-sky-500 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">إلى تاريخ</label>
                    <input type="date" name="to" value="{{ $to->toDateString() }}" class="w-full rounded-xl border-slate-200 focus:border-sky-500 focus:ring-sky-500 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">الحملة</label>
                    <select name="campaign_id" class="w-full rounded-xl border-slate-200 focus:border-sky-500 focus:ring-sky-500 text-sm">
                        <option value="">كل الحملات</option>
                        @foreach($campaigns as $c)
                            <option value="{{ $c->id }}" @selected($campaignId == $c->id)>{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">الموظف</label>
                    <select name="user_id" class="w-full rounded-xl border-slate-200 focus:border-sky-500 focus:ring-sky-500 text-sm">
                        <option value="">كل الموظفين</option>
                        @foreach($salesReps as $rep)
                            <option value="{{ $rep->id }}" @selected($userId == $rep->id)>{{ $rep->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="flex-1 px-4 py-2.5 bg-slate-800 hover:bg-slate-900 text-white rounded-xl font-semibold text-sm">تصفية</button>
                    <a href="{{ route('admin.advertising-campaigns.reports') }}" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-semibold text-sm">مسح</a>
                </div>
            </form>
        </div>
    </div>

    {{-- Totals --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-7 gap-3">
        @foreach($statCards as $card)
            <div class="rounded-2xl border {{ $colorMap[$card['color']] }} p-4">
                <p class="text-xs font-semibold opacity-80">{{ $card['label'] }}</p>
                <p class="text-2xl font-black mt-1">{{ number_format($card['value']) }}</p>
            </div>
        @endforeach
    </div>

    {{-- Per-campaign summary with cost --}}
    @if($perCampaign->isNotEmpty())
    <div class="rounded-3xl bg-white/95 backdrop-blur border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-5 py-4 sm:px-8 border-b border-slate-200">
            <h2 class="text-lg font-bold text-slate-900">ملخص لكل حملة (التكلفة مقابل النتائج)</h2>
        </div>
        <div class="p-5 sm:p-8 overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-right text-sm">
                <thead class="bg-slate-50">
                    <tr class="text-xs font-semibold uppercase text-slate-500">
                        <th class="px-4 py-3">الحملة</th>
                        <th class="px-4 py-3">التكلفة</th>
                        <th class="px-4 py-3">رسائل</th>
                        <th class="px-4 py-3">Qualified</th>
                        <th class="px-4 py-3">Converted</th>
                        <th class="px-4 py-3">تكلفة الرسالة</th>
                        <th class="px-4 py-3">تكلفة التحويل</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @foreach($perCampaign as $row)
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-3 font-medium text-slate-800">{{ $row['campaign']?->name ?? '—' }}</td>
                            <td class="px-4 py-3 text-slate-700">{{ number_format($row['cost'], 2) }}</td>
                            <td class="px-4 py-3">{{ number_format($row['messages']) }}</td>
                            <td class="px-4 py-3 text-indigo-700 font-semibold">{{ number_format($row['qualified']) }}</td>
                            <td class="px-4 py-3 text-emerald-700 font-semibold">{{ number_format($row['converted']) }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ $row['cost_per_message'] !== null ? number_format($row['cost_per_message'], 2) : '—' }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ $row['cost_per_converted'] !== null ? number_format($row['cost_per_converted'], 2) : '—' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- Detailed daily rows --}}
    <div class="rounded-3xl bg-white/95 backdrop-blur border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-5 py-4 sm:px-8 border-b border-slate-200">
            <h2 class="text-lg font-bold text-slate-900">التفاصيل اليومية ({{ number_format($rows->count()) }} سجل)</h2>
        </div>
        <div class="p-5 sm:p-8 overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-right text-sm">
                <thead class="bg-slate-50">
                    <tr class="text-xs font-semibold uppercase text-slate-500">
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
                <tbody class="divide-y divide-slate-200">
                    @forelse($rows as $r)
                        <tr class="hover:bg-slate-50">
                            <td class="px-3 py-3 text-slate-600 whitespace-nowrap">{{ $r->report_date?->format('Y-m-d') }}</td>
                            <td class="px-3 py-3 font-medium text-slate-800">{{ $r->campaign?->name ?? '—' }}</td>
                            <td class="px-3 py-3 text-slate-600">{{ $r->user?->name ?? '—' }}</td>
                            <td class="px-3 py-3">{{ $r->new_messages }}</td>
                            <td class="px-3 py-3">{{ $r->whatsapp_messages }}</td>
                            <td class="px-3 py-3">{{ $r->messenger_messages }}</td>
                            <td class="px-3 py-3">{{ $r->instagram_messages }}</td>
                            <td class="px-3 py-3 text-indigo-700 font-semibold">{{ $r->qualified }}</td>
                            <td class="px-3 py-3 text-slate-500">{{ $r->unqualified }}</td>
                            <td class="px-3 py-3 text-emerald-700 font-semibold">{{ $r->converted }}</td>
                            <td class="px-3 py-3 text-slate-500 max-w-[220px] truncate" title="{{ $r->notes }}">{{ $r->notes ?: '—' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="px-4 py-12 text-center text-slate-500">
                                <i class="fas fa-chart-column text-4xl text-slate-300 mb-3 block"></i>
                                <p>لا توجد تقارير كامبين في هذه الفترة.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
