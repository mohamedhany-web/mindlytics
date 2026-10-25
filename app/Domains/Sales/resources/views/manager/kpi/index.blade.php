@extends('layouts.employee')

@section('title', 'مؤشرات أداء الفريق')
@section('header', 'مؤشرات أداء الفريق (KPIs)')

@section('content')
@php
    $scoreTone = function ($score) {
        $score = (float) $score;
        if ($score >= 85) return ['bg' => 'bg-emerald-50', 'border' => 'border-emerald-200', 'text' => 'text-emerald-700', 'bar' => 'bg-emerald-500', 'label' => 'ممتاز'];
        if ($score >= 65) return ['bg' => 'bg-sky-50', 'border' => 'border-sky-200', 'text' => 'text-sky-700', 'bar' => 'bg-sky-500', 'label' => 'جيد'];
        if ($score >= 45) return ['bg' => 'bg-amber-50', 'border' => 'border-amber-200', 'text' => 'text-amber-700', 'bar' => 'bg-amber-500', 'label' => 'تحت المتابعة'];
        return ['bg' => 'bg-rose-50', 'border' => 'border-rose-200', 'text' => 'text-rose-700', 'bar' => 'bg-rose-500', 'label' => 'حرج'];
    };

    $fmt = function ($value) {
        if ($value === null) return '—';
        if (is_float($value)) return rtrim(rtrim(number_format($value, 2), '0'), '.');
        return is_numeric($value) ? number_format((float) $value, 0) : (string) $value;
    };

    $achievement = function ($actual, $target) {
        if ($target === null || (float) $target <= 0 || $actual === null) return null;
        return min(150, round(((float) $actual / (float) $target) * 100, 1));
    };

    $summaryCards = [
        ['label' => 'أعضاء الفريق', 'value' => $summary['members'], 'icon' => 'fas fa-users', 'tone' => 'text-slate-700 bg-slate-100'],
        ['label' => 'متوسط المؤشر المركّب', 'value' => $summary['avg_composite'] . '/100', 'icon' => 'fas fa-gauge-high', 'tone' => 'text-indigo-700 bg-indigo-100'],
        ['label' => 'إيراد ' . $periodLabel, 'value' => number_format($summary['revenue'], 0) . ' ج.م', 'icon' => 'fas fa-sack-dollar', 'tone' => 'text-emerald-700 bg-emerald-100'],
        ['label' => 'صفقات مغلقة', 'value' => $summary['won'], 'icon' => 'fas fa-handshake', 'tone' => 'text-teal-700 bg-teal-100'],
        ['label' => 'Leads جديدة', 'value' => $summary['new_leads'], 'icon' => 'fas fa-user-plus', 'tone' => 'text-sky-700 bg-sky-100'],
        ['label' => 'مكالمات', 'value' => $summary['calls'], 'icon' => 'fas fa-phone', 'tone' => 'text-violet-700 bg-violet-100'],
        ['label' => 'متابعات متأخرة', 'value' => $summary['overdue_followups'], 'icon' => 'fas fa-clock-rotate-left', 'tone' => 'text-amber-700 bg-amber-100'],
        ['label' => 'دون المستوى', 'value' => $summary['below_target'], 'icon' => 'fas fa-triangle-exclamation', 'tone' => 'text-rose-700 bg-rose-100'],
    ];
@endphp

<div class="space-y-5">

    {{-- رأس الصفحة --}}
    <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-4 sm:p-5">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div>
                <h1 class="text-lg sm:text-xl font-black text-slate-900 flex items-center gap-2">
                    <i class="fas fa-chart-line text-teal-600"></i>
                    مؤشرات أداء الفريق
                </h1>
                <p class="text-xs sm:text-sm text-slate-500 mt-1">
                    {{ $team->name }} — {{ $periodLabel }}:
                    <span class="font-semibold text-slate-700 tabular-nums">{{ $start->format('Y-m-d') }}</span>
                    @if($start->toDateString() !== $end->toDateString())
                        → <span class="font-semibold text-slate-700 tabular-nums">{{ $end->format('Y-m-d') }}</span>
                    @endif
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('employee.sales-manager.kpi.targets') }}"
                   class="inline-flex items-center gap-2 rounded-xl bg-teal-600 hover:bg-teal-700 px-4 py-2 text-sm font-semibold text-white">
                    <i class="fas fa-bullseye"></i> ضبط الأهداف
                </a>
                <a href="{{ route('employee.sales-manager.campaign-reports.index') }}"
                   class="inline-flex items-center gap-2 rounded-xl border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                    <i class="fas fa-bullhorn"></i> الكامبين
                </a>
                <a href="{{ route('employee.sales-manager.daily-reports.index') }}"
                   class="inline-flex items-center gap-2 rounded-xl border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                    <i class="fas fa-file-lines"></i> التقارير اليومية
                </a>
            </div>
        </div>

        {{-- الفلاتر --}}
        <form method="GET" class="mt-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 border-t border-slate-100 pt-4">
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1.5">الفترة</label>
                <select name="period" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                    @foreach(['day' => 'يوم واحد', 'week' => 'الأسبوع', 'month' => 'الشهر'] as $val => $label)
                        <option value="{{ $val }}" @selected($period === $val)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1.5">التاريخ</label>
                <input type="date" name="date" value="{{ $date->toDateString() }}" max="{{ now()->toDateString() }}"
                       class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1.5">الموظف</label>
                <select name="user_id" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                    <option value="">كل الفريق</option>
                    @foreach($members as $member)
                        <option value="{{ $member->id }}" @selected($selectedId === (int) $member->id)>{{ $member->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-teal-600 hover:bg-teal-700 text-white font-semibold px-5 py-2 text-sm">
                    <i class="fas fa-filter"></i> عرض
                </button>
                <a href="{{ route('employee.sales-manager.kpi.index') }}"
                   class="inline-flex items-center gap-2 rounded-xl border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-50">
                    اليوم
                </a>
            </div>
        </form>
    </div>

    {{-- ملخص الفريق --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
        @foreach($summaryCards as $card)
            <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-4">
                <div class="flex items-center gap-3">
                    <span class="w-9 h-9 rounded-xl flex items-center justify-center {{ $card['tone'] }}">
                        <i class="{{ $card['icon'] }} text-sm"></i>
                    </span>
                    <div class="min-w-0">
                        <p class="text-[11px] font-semibold text-slate-500 truncate">{{ $card['label'] }}</p>
                        <p class="text-base font-black text-slate-900 tabular-nums truncate">{{ $card['value'] }}</p>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- بطاقات الموظفين --}}
    @forelse($rows as $row)
        @php
            $rep = $row['user'];
            $report = $row['report'];
            $tone = $scoreTone($report['composite']);
            $metrics = $report['metrics'];
        @endphp
        <div class="rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden">

            <div class="px-4 sm:px-5 py-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 border-b border-slate-100 {{ $tone['bg'] }}">
                <div class="flex items-center gap-3 min-w-0">
                    <span class="w-11 h-11 rounded-xl bg-white border {{ $tone['border'] }} flex items-center justify-center font-black {{ $tone['text'] }}">
                        {{ mb_substr($rep->name, 0, 1) }}
                    </span>
                    <div class="min-w-0">
                        <p class="font-black text-slate-900 truncate">{{ $rep->name }}</p>
                        <p class="text-xs text-slate-500 truncate">{{ $rep->employeeJob->title ?? 'مبيعات' }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <div class="text-left">
                        <p class="text-[11px] font-semibold text-slate-500">المؤشر المركّب</p>
                        <p class="text-2xl font-black tabular-nums {{ $tone['text'] }}">{{ $report['composite'] }}<span class="text-sm text-slate-400">/100</span></p>
                    </div>
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-white border {{ $tone['border'] }} {{ $tone['text'] }}">
                        {{ $tone['label'] }}
                    </span>
                    <a href="{{ route('employee.sales-manager.team.show', $rep) }}"
                       class="inline-flex items-center gap-1.5 rounded-xl border border-slate-300 bg-white px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50">
                        <i class="fas fa-user"></i> الملف
                    </a>
                </div>
            </div>

            {{-- الأعمدة الأربعة --}}
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 px-4 sm:px-5 py-4">
                @foreach($report['pillars'] as $key => $pillar)
                    @php $pTone = $scoreTone($pillar['score']); @endphp
                    <div class="rounded-xl border border-slate-200 p-3">
                        <div class="flex items-center justify-between gap-2">
                            <p class="text-[11px] font-bold text-slate-600">
                                {{ ['results' => 'النتائج', 'activity' => 'النشاط', 'quality' => 'الجودة', 'discipline' => 'الالتزام'][$key] ?? $key }}
                            </p>
                            <p class="text-sm font-black tabular-nums {{ $pTone['text'] }}">{{ $pillar['score'] }}</p>
                        </div>
                        <div class="h-1.5 rounded-full bg-slate-100 mt-2 overflow-hidden">
                            <div class="h-full rounded-full {{ $pTone['bar'] }}" style="width: {{ min(100, (float) $pillar['score']) }}%"></div>
                        </div>
                        <p class="text-[10px] text-slate-400 mt-1.5 leading-tight">{{ $pillar['label'] }}</p>
                    </div>
                @endforeach
            </div>

            {{-- تنبيهات --}}
            @if(! empty($report['alert_flags']))
                <div class="px-4 sm:px-5 pb-4">
                    <div class="rounded-xl border border-amber-200 bg-amber-50 px-3 py-2.5">
                        <p class="text-[11px] font-bold text-amber-900 mb-1.5">
                            <i class="fas fa-triangle-exclamation"></i> نقاط تحتاج تدخّل
                        </p>
                        <ul class="space-y-1">
                            @foreach($report['alert_flags'] as $flag)
                                <li class="text-xs text-amber-800">• {{ $flag }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            {{-- تفاصيل المؤشرات: المحقق مقابل الهدف --}}
            <details class="group border-t border-slate-100">
                <summary class="cursor-pointer list-none px-4 sm:px-5 py-3 flex items-center justify-between text-sm font-semibold text-slate-700 hover:bg-slate-50">
                    <span><i class="fas fa-list-check text-teal-600 ml-1"></i> تفاصيل المؤشرات ونسبة التحقيق</span>
                    <i class="fas fa-chevron-down text-xs text-slate-400 group-open:rotate-180 transition-transform"></i>
                </summary>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-slate-50 text-slate-600">
                            <tr>
                                <th class="text-right px-4 py-2.5 text-xs font-bold">المؤشر</th>
                                <th class="text-center px-3 py-2.5 text-xs font-bold">المحقق</th>
                                <th class="text-center px-3 py-2.5 text-xs font-bold">الهدف</th>
                                <th class="text-center px-3 py-2.5 text-xs font-bold w-40">نسبة التحقيق</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($report['kpi_lines'] as $line)
                                @php
                                    $pct = $achievement($line['actual'], $line['target']);
                                    $lineTone = $pct === null ? null : $scoreTone(min(100, $pct));
                                @endphp
                                <tr class="hover:bg-slate-50/60">
                                    <td class="px-4 py-2.5 text-slate-700">{{ $line['label'] }}</td>
                                    <td class="px-3 py-2.5 text-center font-bold text-slate-900 tabular-nums">{{ $fmt($line['actual']) }}</td>
                                    <td class="px-3 py-2.5 text-center text-slate-500 tabular-nums">{{ $fmt($line['target']) }}</td>
                                    <td class="px-3 py-2.5">
                                        @if($pct === null)
                                            <span class="block text-center text-xs text-slate-400">—</span>
                                        @else
                                            <div class="flex items-center gap-2">
                                                <div class="flex-1 h-1.5 rounded-full bg-slate-100 overflow-hidden">
                                                    <div class="h-full rounded-full {{ $lineTone['bar'] }}" style="width: {{ min(100, $pct) }}%"></div>
                                                </div>
                                                <span class="text-xs font-bold tabular-nums {{ $lineTone['text'] }} w-12 text-left">{{ $pct }}%</span>
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 px-4 sm:px-5 py-4 bg-slate-50/60 border-t border-slate-100">
                    <div>
                        <p class="text-[11px] font-semibold text-slate-500">أنبوب مفتوح</p>
                        <p class="text-sm font-black text-slate-900 tabular-nums">{{ number_format((float) $metrics['pipeline_value'], 0) }} ج.م</p>
                    </div>
                    <div>
                        <p class="text-[11px] font-semibold text-slate-500">متابعات متأخرة</p>
                        <p class="text-sm font-black {{ $metrics['overdue_followups'] > 0 ? 'text-rose-600' : 'text-slate-900' }} tabular-nums">{{ $metrics['overdue_followups'] }}</p>
                    </div>
                    <div>
                        <p class="text-[11px] font-semibold text-slate-500">عملاء راكدون</p>
                        <p class="text-sm font-black {{ $metrics['stale_open_leads'] > 0 ? 'text-amber-600' : 'text-slate-900' }} tabular-nums">{{ $metrics['stale_open_leads'] }}</p>
                    </div>
                    <div>
                        <p class="text-[11px] font-semibold text-slate-500">تقارير يومية مسلّمة</p>
                        <p class="text-sm font-black text-slate-900 tabular-nums">
                            {{ $metrics['daily_report_submission_pct'] === null ? '—' : $metrics['daily_report_submission_pct'] . '%' }}
                        </p>
                    </div>
                </div>
            </details>
        </div>
    @empty
        <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-10 text-center">
            <i class="fas fa-users-slash text-3xl text-slate-300"></i>
            <p class="text-slate-600 font-semibold mt-3">لا يوجد أعضاء نشطون في الفريق لعرض مؤشراتهم.</p>
        </div>
    @endforelse
</div>
@endsection
