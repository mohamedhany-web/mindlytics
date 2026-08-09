@extends('layouts.employee')

@section('title', 'Pipeline الفريق')
@section('header', 'Academy Pipeline — لوحة المدير')

@section('content')
@php
    $h = $highlights ?? [];
@endphp
<div class="space-y-5">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h2 class="text-xl font-black text-slate-900">رحلة العميل — {{ $team->name ?? 'الفريق' }}</h2>
            <p class="text-sm text-slate-500">أعداد لحظية · تحويل بين المراحل · زمن البقاء · أسباب الخسارة</p>
        </div>
        <a href="{{ route('employee.sales-manager.live-board') }}" class="px-4 py-2 rounded-xl border border-slate-200 text-sm font-semibold hover:bg-slate-50">اللوحة الحية SOS</a>
    </div>

    <div class="grid grid-cols-2 lg:grid-cols-4 xl:grid-cols-8 gap-3">
        @foreach([
            ['دخلوا السيستم', $h['entered_total'] ?? 0, 'sky'],
            ['دخول الشهر', $h['entered_month'] ?? 0, 'indigo'],
            ['تم التواصل', $h['contacted'] ?? 0, 'teal'],
            ['تواصل اليوم', $h['contacted_today'] ?? 0, 'cyan'],
            ['بدون تواصل', $h['no_contact'] ?? 0, 'amber'],
            ['مؤهل / مهتم', ($h['qualification'] ?? 0) + ($h['interested'] ?? 0), 'violet'],
            ['عروض مُرسلة', $h['offer_sent'] ?? 0, 'orange'],
            ['دفعوا اليوم', $h['paid_today'] ?? 0, 'emerald'],
        ] as [$label, $val, $color])
            <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                <p class="text-[11px] font-semibold text-slate-500">{{ $label }}</p>
                <p class="text-2xl font-black text-slate-900 tabular-nums mt-1">{{ number_format($val) }}</p>
            </div>
        @endforeach
    </div>

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
        @foreach([
            ['Payment Pending', $h['payment_pending'] ?? 0],
            ['تسجيلات / فوز', $h['won'] ?? 0],
            ['خسارة', $h['lost'] ?? 0],
            ['حجز بلا كورس (تحذير)', $h['without_course_at_payment'] ?? 0],
        ] as [$label, $val])
            <div class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3">
                <p class="text-[11px] font-semibold text-slate-500">{{ $label }}</p>
                <p class="text-xl font-black tabular-nums">{{ number_format($val) }}</p>
            </div>
        @endforeach
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm overflow-x-auto">
        <p class="text-sm font-bold text-slate-900 mb-3">توزيع المراحل</p>
        <div class="flex gap-2 min-w-max">
            @foreach($stage_counts as $key => $count)
                <div class="w-28 shrink-0 rounded-xl border border-slate-100 bg-slate-50 p-2 text-center">
                    <p class="text-[10px] font-semibold text-slate-600 leading-tight min-h-[2rem]">{{ \App\Models\SalesLead::stageLabel($key) }}</p>
                    <p class="text-lg font-black text-slate-900 tabular-nums">{{ $count }}</p>
                </div>
            @endforeach
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
            <p class="text-sm font-bold text-slate-900 mb-3">معدل التحويل (30 يوماً)</p>
            <ul class="space-y-2 text-sm">
                @foreach($conversions as $c)
                    <li class="flex items-center justify-between gap-2 border-b border-slate-100 pb-2">
                        <span class="text-slate-600">{{ $c['from_label'] }} → {{ $c['to_label'] }}</span>
                        <span class="font-bold tabular-nums">{{ $c['count'] }} <span class="text-xs text-slate-500">({{ $c['rate'] }}%)</span></span>
                    </li>
                @endforeach
            </ul>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
            <p class="text-sm font-bold text-slate-900 mb-3">متوسط زمن البقاء (ساعة)</p>
            <ul class="space-y-2 text-sm max-h-80 overflow-y-auto">
                @forelse($dwell_hours as $stage => $hours)
                    <li class="flex justify-between border-b border-slate-100 pb-1">
                        <span class="text-slate-600">{{ \App\Models\SalesLead::stageLabel($stage) }}</span>
                        <span class="font-bold tabular-nums">{{ $hours }} س</span>
                    </li>
                @empty
                    <li class="text-slate-500 text-sm">لا بيانات بعد.</li>
                @endforelse
            </ul>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <div class="rounded-2xl border border-rose-100 bg-rose-50/40 p-4">
            <p class="text-sm font-bold text-rose-900 mb-2">أسباب الخسارة الأكثر تكراراً</p>
            <ul class="space-y-1 text-sm">
                @forelse($loss_reasons as $r)
                    <li class="flex justify-between"><span>{{ $r['label'] }}</span><strong>{{ $r['count'] }}</strong></li>
                @empty
                    <li class="text-slate-500">لا بيانات</li>
                @endforelse
            </ul>
        </div>
        <div class="rounded-2xl border border-amber-100 bg-amber-50/40 p-4">
            <p class="text-sm font-bold text-amber-900 mb-2">أسباب الاعتراض</p>
            <ul class="space-y-1 text-sm">
                @forelse($objection_reasons as $r)
                    <li class="flex justify-between"><span>{{ $r['label'] }}</span><strong>{{ $r['count'] }}</strong></li>
                @empty
                    <li class="text-slate-500">لا بيانات</li>
                @endforelse
            </ul>
        </div>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        <div class="px-4 py-3 border-b border-slate-100 font-bold">أداء الموظفين اليوم</div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm text-right">
                <thead class="bg-slate-50 text-xs text-slate-500">
                    <tr>
                        <th class="px-3 py-2">الموظف</th>
                        <th class="px-3 py-2">مكالمات</th>
                        <th class="px-3 py-2">Enrollment</th>
                        <th class="px-3 py-2">مبيعات الشهر</th>
                        <th class="px-3 py-2">متوسط أول رد (د)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($reps as $row)
                        <tr>
                            <td class="px-3 py-2.5 font-semibold">{{ $row['user']->name }}</td>
                            <td class="px-3 py-2.5 tabular-nums">{{ $row['calls'] }}</td>
                            <td class="px-3 py-2.5 tabular-nums font-bold text-emerald-700">{{ $row['enrollments'] }}</td>
                            <td class="px-3 py-2.5 tabular-nums">{{ number_format($row['sales_value'], 0) }}</td>
                            <td class="px-3 py-2.5 tabular-nums">{{ $row['avg_response_minutes'] ?? '—' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
