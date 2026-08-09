@extends('layouts.employee')

@section('title', 'KPIs والأداء — المبيعات')
@section('header', 'KPIs والأداء')

@push('styles')
@include('employee.sales._styles')
@endpush

@section('content')
@php
    $w = config('sales_kpi.weights', []);
    $d = $report['day'];
    $wk = $report['week'];
    $mo = $report['month'];
    $sosToday = $sosToday ?? null;
    $hasCustomTargets = $hasCustomTargets ?? false;
    $yearMonth = $yearMonth ?? now()->format('Y-m');
@endphp
<div class="space-y-6 pb-6">
    @include('employee.sales._hero', [
        'heroTitle' => 'لوحة مؤشرات الأداء (KPIs)',
        'heroSubtitle' => 'أهدافك الملزمة لهذا الشهر مقابل ما حقّقته فعليًا — نتائج '.(int)(($w['results'] ?? 0) * 100).'٪ · نشاط '.(int)(($w['activity'] ?? 0) * 100).'٪ · جودة '.(int)(($w['quality'] ?? 0) * 100).'٪ · التزام '.(int)(($w['discipline'] ?? 0) * 100).'٪',
        'heroIcon' => 'fa-bullseye',
        'backUrl' => route('employee.sales.dashboard'),
    ])

    <section class="rounded-xl border {{ $hasCustomTargets ? 'border-emerald-200 bg-emerald-50' : 'border-amber-200 bg-amber-50' }} px-4 py-3 text-sm">
        @if($hasCustomTargets)
            <p class="font-bold text-emerald-950"><i class="fas fa-check-circle text-emerald-600 ml-1"></i> أهدافك لشهر {{ $yearMonth }} محفوظة وملزمة من الإدارة.</p>
        @else
            <p class="font-bold text-amber-950"><i class="fas fa-exclamation-triangle text-amber-600 ml-1"></i> لم تُضبط أهداف مخصّصة بعد لهذا الشهر — التقييم يعتمد على افتراضيات النظام حتى تحفظ الإدارة أهدافك.</p>
        @endif
    </section>

    <section class="rounded-2xl border-2 border-emerald-600/30 bg-gradient-to-br from-emerald-50 to-white p-6 shadow-lg">
        <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4">
            <div>
                <p class="text-xs font-bold text-emerald-800 uppercase tracking-wide">المؤشر المركّب — شهر {{ $report['reference']->translatedFormat('F Y') }}</p>
                <p class="text-5xl font-black text-emerald-900 tabular-nums mt-1">{{ $report['composite_month'] }}<span class="text-2xl text-emerald-700/80">/100</span></p>
            </div>
            <div class="flex flex-wrap gap-2">
                @foreach($mo['pillars'] ?? [] as $key => $p)
                    <div class="rounded-xl border border-emerald-200 bg-white px-4 py-2 shadow-sm">
                        <p class="text-[10px] text-gray-500 uppercase">{{ $p['label'] ?? $key }}</p>
                        <p class="text-xl font-black text-gray-900">{{ $p['score'] ?? '—' }}</p>
                    </div>
                @endforeach
            </div>
        </div>
        @if(!empty($report['alert_flags']))
            <div class="mt-4 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-900 space-y-1">
                @foreach($report['alert_flags'] as $f)<p><i class="fas fa-shield-halved ml-1 text-rose-600"></i>{{ $f }}</p>@endforeach
            </div>
        @endif
    </section>

    @if($sosToday)
        <section class="rounded-2xl border border-teal-200 bg-white shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-teal-100 bg-teal-50/70 flex flex-wrap items-center justify-between gap-2">
                <div>
                    <h2 class="text-lg font-bold text-teal-950 flex items-center gap-2"><i class="fas fa-phone-volume text-teal-600"></i> نتائج اليوم مقابل هدفك</h2>
                    <p class="text-xs text-teal-800/80 mt-0.5">ما حقّقته اليوم من أهداف SOS الملزمة</p>
                </div>
                <p class="text-2xl font-black text-teal-900 tabular-nums">{{ number_format($sosToday['overall_pct'], 0) }}%</p>
            </div>
            <div class="p-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                @foreach($sosToday['lines'] as $line)
                    <div class="rounded-xl border border-slate-200 p-3">
                        <div class="flex items-start justify-between gap-2">
                            <p class="text-sm font-bold text-slate-900">{{ $line['label'] }}</p>
                            <span @class([
                                'text-xs font-black tabular-nums',
                                'text-emerald-700' => $line['pct'] >= 100,
                                'text-amber-700' => $line['pct'] >= 70 && $line['pct'] < 100,
                                'text-rose-700' => $line['pct'] < 70,
                            ])>{{ $line['pct'] }}%</span>
                        </div>
                        <p class="text-xs text-slate-500 mt-1 tabular-nums">{{ $line['actual'] }} من {{ number_format($line['target'], 0) }}</p>
                        <div class="mt-2 h-2 rounded-full bg-slate-100 overflow-hidden">
                            <div class="h-full rounded-full {{ $line['pct'] >= 100 ? 'bg-emerald-500' : ($line['pct'] >= 70 ? 'bg-amber-500' : 'bg-rose-500') }}"
                                 style="width: {{ min(100, $line['pct']) }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <section class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
            <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2"><i class="fas fa-sun text-amber-500"></i> اليوم</h2>
            <dl class="space-y-2 text-sm">
                <div class="flex justify-between"><dt class="text-gray-500">Leads جديدة</dt><dd class="font-bold">{{ $d['new_leads'] }}</dd></div>
                <div class="flex justify-between"><dt class="text-gray-500">مكالمات</dt><dd class="font-bold">{{ $d['calls'] }}</dd></div>
                <div class="flex justify-between"><dt class="text-gray-500">اجتماعات (غير محاسب)</dt><dd class="font-bold">{{ $d['meetings'] }}</dd></div>
                <div class="flex justify-between"><dt class="text-gray-500">متابعات</dt><dd class="font-bold">{{ $d['followups'] }}</dd></div>
            </dl>
            @if(!empty($d['scores']))
                <p class="text-xs font-bold text-gray-500 mt-4 mb-2">إنجاز مقارنة بالهدف اليومي</p>
                <ul class="text-xs space-y-1">
                    @foreach($d['scores'] as $sk => $sv)
                        <li class="flex justify-between border-t border-gray-100 pt-1"><span>{{ $sk }}</span><span class="font-mono font-bold @if($sv>=80) text-emerald-600 @elseif($sv>=50) text-amber-600 @else text-rose-600 @endif">{{ $sv }}%</span></li>
                    @endforeach
                </ul>
            @endif
        </section>

        <section class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
            <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2"><i class="fas fa-calendar-week text-violet-500"></i> الأسبوع</h2>
            <dl class="space-y-2 text-sm">
                <div class="flex justify-between"><dt class="text-gray-500">Leads جديدة</dt><dd class="font-bold">{{ $wk['new_leads'] }}</dd></div>
                <div class="flex justify-between"><dt class="text-gray-500">صفقات فوز</dt><dd class="font-bold">{{ $wk['won_closed'] }}</dd></div>
                <div class="flex justify-between"><dt class="text-gray-500">إيراد (قيمة متوقعة)</dt><dd class="font-bold">{{ number_format($wk['revenue_closed'], 0) }} ج.م</dd></div>
            </dl>
            @if(!empty($wk['scores']))
                <p class="text-xs font-bold text-gray-500 mt-4 mb-2">إنجاز أسبوعي</p>
                <ul class="text-xs space-y-1">
                    @foreach($wk['scores'] as $sk => $sv)
                        <li class="flex justify-between border-t border-gray-100 pt-1"><span>{{ $sk }}</span><span class="font-mono font-bold">{{ $sv }}%</span></li>
                    @endforeach
                </ul>
            @endif
        </section>

        <section class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
            <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2"><i class="fas fa-calendar-alt text-blue-500"></i> الشهر</h2>
            <dl class="space-y-2 text-sm">
                <div class="flex justify-between"><dt class="text-gray-500">تحويل %</dt><dd class="font-bold">{{ $mo['conversion_pct'] ?? '—' }}@if($mo['conversion_pct'] !== null)%@endif</dd></div>
                <div class="flex justify-between"><dt class="text-gray-500">متوسط أول رد (دقيقة)</dt><dd class="font-bold">{{ $mo['avg_response_minutes'] ?? '—' }}</dd></div>
                <div class="flex justify-between"><dt class="text-gray-500">نسبة إغلاق</dt><dd class="font-bold">{{ $mo['closing_ratio_pct'] ?? '—' }}@if($mo['closing_ratio_pct'] !== null)%@endif</dd></div>
                <div class="flex justify-between"><dt class="text-gray-500">CSAT متوسط</dt><dd class="font-bold">{{ $mo['csat_avg'] ?? '—' }}</dd></div>
                <div class="flex justify-between"><dt class="text-gray-500">فرص مفتوحة</dt><dd class="font-bold">{{ $mo['open_opportunities'] }}</dd></div>
                <div class="flex justify-between"><dt class="text-gray-500">متوسط دورة البيع (يوم)</dt><dd class="font-bold">{{ $mo['sales_cycle_avg_days'] ?? '—' }}</dd></div>
                <div class="flex justify-between"><dt class="text-gray-500">متابعات متأخرة</dt><dd class="font-bold text-rose-600">{{ $mo['overdue_followups'] }}</dd></div>
                <div class="flex justify-between"><dt class="text-gray-500">عملاء بلا تواصل كافٍ</dt><dd class="font-bold text-amber-700">{{ $mo['stale_open_leads'] }}</dd></div>
            </dl>
        </section>
    </div>

    <section class="rounded-2xl border border-gray-200 bg-white shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 bg-gray-50">
            <h2 class="text-lg font-bold text-gray-900">ما حقّقته مقابل أهدافك (الشهر)</h2>
            <p class="text-xs text-gray-500 mt-1">كل مؤشر: الفعلي / الهدف / نسبة الإنجاز. الإيراد = مجموع «القيمة المتوقعة» للصفقات الفائزة.</p>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="bg-gray-100 text-gray-600 text-xs font-bold">
                        <th class="px-4 py-3 text-right">المؤشر</th>
                        <th class="px-4 py-3 text-left">الفعلي</th>
                        <th class="px-4 py-3 text-left">الهدف</th>
                        <th class="px-4 py-3 text-left min-w-[9rem]">الإنجاز</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($mo['kpi_lines'] ?? [] as $row)
                        <tr class="hover:bg-gray-50/80">
                            <td class="px-4 py-2.5 font-medium text-gray-900">{{ $row['label'] }}</td>
                            <td class="px-4 py-2.5 tabular-nums text-gray-800">{{ $row['actual'] ?? '—' }}</td>
                            <td class="px-4 py-2.5 tabular-nums text-gray-500">{{ $row['target'] ?? '—' }}</td>
                            <td class="px-4 py-2.5">
                                @if(($row['pct'] ?? null) !== null)
                                    <div class="flex items-center gap-2">
                                        <div class="flex-1 h-2 rounded-full bg-slate-100 overflow-hidden">
                                            <div class="h-full rounded-full {{ $row['pct'] >= 100 ? 'bg-emerald-500' : ($row['pct'] >= 70 ? 'bg-amber-500' : 'bg-rose-500') }}"
                                                 style="width: {{ min(100, (float) $row['pct']) }}%"></div>
                                        </div>
                                        <span class="text-xs font-black tabular-nums w-12 text-end {{ $row['pct'] >= 100 ? 'text-emerald-700' : ($row['pct'] >= 70 ? 'text-amber-700' : 'text-rose-700') }}">{{ $row['pct'] }}%</span>
                                    </div>
                                @else
                                    <span class="text-xs text-slate-400">—</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>

    <p class="text-xs text-gray-500 text-center max-w-3xl mx-auto">سجّل المكالمات والاجتماعات والمتابعات من صفحة كل عميل لرفع دقة النشاط. بعد الفوز سجّل CSAT لتحسين مؤشر الجودة.</p>
</div>
@endsection
