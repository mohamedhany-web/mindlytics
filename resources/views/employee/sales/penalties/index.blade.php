@extends('layouts.employee')

@section('title', 'خصومات KPI والتقرير')
@section('header', 'خصومات KPI والتقرير')

@push('styles')
@include('employee.sales._styles')
@endpush

@section('content')
<div class="space-y-6 pb-6">
    @include('employee.sales._hero', [
        'heroTitle' => 'خصوماتك السريعة',
        'heroSubtitle' => 'كل خصومات KPI اليومي وعدم تسليم التقرير في مكان واحد — مع وضعك الحالي مقابل الهدف.',
        'heroIcon' => 'fa-receipt',
        'backUrl' => route('employee.sales.dashboard'),
    ])

    <div class="flex flex-wrap items-end gap-3">
        <form method="get" class="flex items-end gap-2">
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">الشهر</label>
                <input type="month" name="month" value="{{ $month }}" class="rounded-xl border border-slate-300 px-3 py-2 text-sm">
            </div>
            <button type="submit" class="rounded-xl bg-slate-900 text-white px-4 py-2 text-sm font-bold">عرض</button>
        </form>
        <a href="{{ route('employee.accounting.index') }}" class="text-sm font-semibold text-sky-700 hover:underline">كل الخصومات في المحاسبة ←</a>
    </div>

    <section class="rounded-2xl border border-rose-200 bg-rose-50/50 p-5">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <p class="text-xs font-bold text-rose-800 uppercase">إجمالي خصومات KPI + التقرير هذا الشهر</p>
                <p class="text-3xl font-black text-rose-950 tabular-nums mt-1">{{ number_format($hub['total_amount'], 2) }} <span class="text-base">ج.م</span></p>
                <p class="text-xs text-rose-800/80 mt-1">{{ $hub['count'] }} خصم · من {{ $hub['from'] }} إلى {{ $hub['to'] }}</p>
            </div>
            @if($penaltyEnabled)
                <p class="text-xs font-semibold text-rose-900 bg-white border border-rose-200 rounded-lg px-3 py-2">
                    الحد الأدنى للتحقيق اليومي: {{ number_format($threshold, 0) }}٪ لكل مؤشر ملزم
                </p>
            @endif
        </div>
    </section>

    <section class="rounded-2xl border border-teal-200 bg-white shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-teal-100 bg-teal-50/70 flex flex-wrap items-center justify-between gap-2">
            <div>
                <h2 class="text-lg font-bold text-teal-950">KPI اليوم (موثّق من CRM)</h2>
                <p class="text-xs text-teal-800/80">بدون اجتماعات — لو أقل من {{ number_format($threshold, 0) }}٪ على مؤشر بعد نهاية اليوم → خصم تلقائي</p>
            </div>
            <p class="text-2xl font-black tabular-nums {{ ($sosToday['status'] ?? '') === 'met' ? 'text-emerald-700' : (($sosToday['status'] ?? '') === 'near' ? 'text-amber-700' : 'text-rose-700') }}">
                {{ number_format($sosToday['overall_pct'] ?? 0, 0) }}%
            </p>
        </div>
        <div class="p-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
            @foreach($sosToday['lines'] ?? [] as $line)
                <div class="rounded-xl border border-slate-200 p-3">
                    <div class="flex justify-between gap-2">
                        <p class="text-sm font-bold text-slate-900">{{ $line['label'] }}</p>
                        <span class="text-xs font-black {{ $line['pct'] >= 100 ? 'text-emerald-700' : ($line['pct'] >= $threshold ? 'text-amber-700' : 'text-rose-700') }}">{{ $line['pct'] }}%</span>
                    </div>
                    <p class="text-xs text-slate-500 mt-1 tabular-nums">{{ $line['actual'] }} / {{ number_format($line['target'], 0) }}</p>
                    <div class="mt-2 h-2 rounded-full bg-slate-100 overflow-hidden">
                        <div class="h-full rounded-full {{ $line['pct'] >= 100 ? 'bg-emerald-500' : ($line['pct'] >= $threshold ? 'bg-amber-500' : 'bg-rose-500') }}"
                             style="width: {{ min(100, $line['pct']) }}%"></div>
                    </div>
                    @if(($line['pct'] < $threshold) && isset($chargeable[$line['key']]))
                        <p class="text-[10px] text-rose-700 font-semibold mt-1">خصم محتمل: {{ number_format($chargeable[$line['key']]['amount'], 0) }} ج.م إن لم يتحسّن قبل نهاية اليوم</p>
                    @endif
                </div>
            @endforeach
        </div>
    </section>

    <section class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100 bg-slate-50">
            <h2 class="text-lg font-bold text-slate-900">سجل الخصومات</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="bg-slate-100 text-xs text-slate-600 font-bold">
                        <th class="px-4 py-3 text-right">التاريخ</th>
                        <th class="px-4 py-3 text-right">النوع</th>
                        <th class="px-4 py-3 text-right">البيان</th>
                        <th class="px-4 py-3 text-left">المبلغ</th>
                        <th class="px-4 py-3 text-left">الحالة</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($hub['items'] as $item)
                        <tr>
                            <td class="px-4 py-2.5 tabular-nums">{{ $item['date'] }}</td>
                            <td class="px-4 py-2.5">
                                <span class="inline-flex rounded-lg px-2 py-0.5 text-[11px] font-bold {{ $item['kind'] === 'kpi' ? 'bg-violet-50 text-violet-800 border border-violet-200' : 'bg-amber-50 text-amber-900 border border-amber-200' }}">
                                    {{ $item['kind_label'] }}
                                </span>
                            </td>
                            <td class="px-4 py-2.5 font-medium text-slate-800">{{ $item['title'] }}</td>
                            <td class="px-4 py-2.5 font-black text-rose-700 tabular-nums">{{ number_format($item['amount'], 2) }}</td>
                            <td class="px-4 py-2.5 text-xs text-slate-500">{{ $item['status'] }} · {{ $item['number'] }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-10 text-center text-slate-400">لا خصومات KPI/تقرير في هذا الشهر — ممتاز.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>
@endsection
