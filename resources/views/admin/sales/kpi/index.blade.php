@extends('layouts.admin')

@section('title', 'مراقبة KPIs المبيعات')
@section('header', 'مراقبة KPIs المبيعات')

@section('content')
<div class="p-4 md:p-6 space-y-6" style="background:#f8fafc;min-height:100vh;">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <p class="text-sm text-gray-600 max-w-2xl">مقارنة جميع موظفي المبيعات النشطين: المؤشر المركّب، الإيراد الشهري، التنبيهات الحادة (متابعات متأخرة، فرص راكدة، أعمدة ضعيفة).</p>
        <a href="{{ route('admin.sales.kpi.targets') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-sm font-bold">
            <i class="fas fa-sliders-h"></i> ضبط الأهداف
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="rounded-2xl border border-rose-200 bg-rose-50/70 p-5">
            <p class="text-xs font-bold text-rose-700 mb-1">SLA: متابعات متأخرة (إجمالي)</p>
            <p class="text-3xl font-black text-rose-800 tabular-nums">{{ (int) ($slaSummary['overdue_followups'] ?? 0) }}</p>
        </div>
        <div class="rounded-2xl border border-amber-200 bg-amber-50/70 p-5">
            <p class="text-xs font-bold text-amber-700 mb-1">SLA: فرص راكدة بلا تواصل</p>
            <p class="text-3xl font-black text-amber-800 tabular-nums">{{ (int) ($slaSummary['stale_open_leads'] ?? 0) }}</p>
        </div>
        <div class="rounded-2xl border border-blue-200 bg-blue-50/70 p-5">
            <p class="text-xs font-bold text-blue-700 mb-1">متوسط أول رد (دقيقة)</p>
            <p class="text-3xl font-black text-blue-800 tabular-nums">{{ ($slaSummary['avg_response_minutes'] ?? 0) > 0 ? number_format((float) $slaSummary['avg_response_minutes'], 1) : '—' }}</p>
        </div>
    </div>

    <div class="rounded-2xl border border-gray-200 bg-white shadow-sm p-5">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-base font-black text-gray-900">Loss Reasons Analytics (الشهر الحالي)</h2>
            <span class="text-xs text-gray-500">Top أسباب الخسارة</span>
        </div>
        @if(($lossReasons ?? collect())->isEmpty())
            <p class="text-sm text-gray-500">لا توجد صفقات خسارة بسبب مسجل خلال هذا الشهر.</p>
        @else
            @php $maxReason = max(1, (int) $lossReasons->max('total')); @endphp
            <div class="space-y-3">
                @foreach($lossReasons as $row)
                    @php $w = (int) round(($row->total / $maxReason) * 100); @endphp
                    <div>
                        <div class="flex justify-between gap-3 mb-1 text-sm">
                            <span class="font-semibold text-gray-800 truncate">{{ $row->lost_reason }}</span>
                            <span class="tabular-nums text-rose-700 font-bold">{{ $row->total }}</span>
                        </div>
                        <div class="h-2.5 rounded-full bg-gray-100 overflow-hidden">
                            <div class="h-full rounded-full bg-gradient-to-l from-rose-500 to-rose-400" style="width: {{ $w }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <div class="rounded-2xl border border-gray-200 bg-white shadow-sm p-5">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-base font-black text-gray-900">Source Performance Dashboard (الشهر الحالي)</h2>
            <span class="text-xs text-gray-500">Leads / Won / Conversion / Revenue</span>
        </div>
        @if(($sourcePerformance ?? collect())->isEmpty())
            <p class="text-sm text-gray-500">لا توجد بيانات مصادر كافية خلال هذا الشهر.</p>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-[700px] w-full text-sm">
                    <thead>
                        <tr class="bg-slate-100 text-slate-700">
                            <th class="px-3 py-2 text-right">المصدر</th>
                            <th class="px-3 py-2 text-center">Leads</th>
                            <th class="px-3 py-2 text-center">Won</th>
                            <th class="px-3 py-2 text-center">Conversion %</th>
                            <th class="px-3 py-2 text-left">Revenue</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($sourcePerformance as $row)
                            <tr>
                                <td class="px-3 py-2 font-semibold text-slate-800">{{ $row['label'] }}</td>
                                <td class="px-3 py-2 text-center tabular-nums">{{ $row['created'] }}</td>
                                <td class="px-3 py-2 text-center tabular-nums text-emerald-700 font-bold">{{ $row['won'] }}</td>
                                <td class="px-3 py-2 text-center tabular-nums {{ ($row['conversion'] ?? 0) >= 20 ? 'text-emerald-700 font-bold' : 'text-amber-700 font-semibold' }}">
                                    {{ $row['conversion'] !== null ? number_format((float) $row['conversion'], 1) . '%' : '—' }}
                                </td>
                                <td class="px-3 py-2 text-left tabular-nums">{{ number_format((float) $row['revenue'], 0) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <div class="rounded-2xl border border-gray-200 bg-white shadow-sm p-5 space-y-4">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <h2 class="text-base font-black text-gray-900">لوحة مراقبة إشعارات المتابعة اليومية</h2>
            <div class="flex items-center gap-2">
                <form method="GET" action="{{ route('admin.sales.kpi.index') }}" class="flex items-center gap-2">
                    <select name="period" onchange="this.form.submit()" class="rounded-lg border border-slate-300 px-2.5 py-1.5 text-xs font-semibold text-slate-700">
                        <option value="today" {{ ($period ?? 'today') === 'today' ? 'selected' : '' }}>اليوم</option>
                        <option value="7d" {{ ($period ?? 'today') === '7d' ? 'selected' : '' }}>آخر 7 أيام</option>
                        <option value="month" {{ ($period ?? 'today') === 'month' ? 'selected' : '' }}>هذا الشهر</option>
                    </select>
                </form>
                <span class="text-xs text-gray-500">{{ $periodLabel ?? 'اليوم' }}</span>
            </div>
        </div>
        <p class="text-xs text-slate-500">الفترة: {{ optional($rangeStart)->format('Y-m-d') }} إلى {{ optional($rangeEnd)->format('Y-m-d') }}</p>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
            <div class="rounded-xl border border-indigo-200 bg-indigo-50/60 p-4">
                <p class="text-xs font-bold text-indigo-700">إشعارات مرسلة (الفترة)</p>
                <p class="text-2xl font-black text-indigo-900 tabular-nums">{{ (int) ($reminderMonitoringSummary['sent_total'] ?? 0) }}</p>
            </div>
            <div class="rounded-xl border border-rose-200 bg-rose-50/60 p-4">
                <p class="text-xs font-bold text-rose-700">موظفون كان لديهم متأخرات</p>
                <p class="text-2xl font-black text-rose-900 tabular-nums">{{ (int) ($reminderMonitoringSummary['reps_with_alerts'] ?? 0) }}</p>
            </div>
            <div class="rounded-xl border border-emerald-200 bg-emerald-50/60 p-4">
                <p class="text-xs font-bold text-emerald-700">متوسط الالتزام بعد التنبيه</p>
                <p class="text-2xl font-black text-emerald-900 tabular-nums">{{ ($reminderMonitoringSummary['avg_compliance_pct'] ?? 0) > 0 ? number_format((float) $reminderMonitoringSummary['avg_compliance_pct'], 1).'%' : '—' }}</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-[900px] w-full text-sm">
                <thead>
                    <tr class="bg-slate-100 text-slate-700">
                        <th class="px-3 py-2 text-right">الموظف</th>
                        <th class="px-3 py-2 text-center">عدد التنبيهات (الفترة)</th>
                        <th class="px-3 py-2 text-center">متأخر وقت التنبيه</th>
                        <th class="px-3 py-2 text-center">متابعات اليوم وقت التنبيه</th>
                        <th class="px-3 py-2 text-center">راكد وقت التنبيه</th>
                        <th class="px-3 py-2 text-center">المتأخر الحالي</th>
                        <th class="px-3 py-2 text-center">نسبة الالتزام</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse(($reminderMonitoringRows ?? collect()) as $row)
                        <tr>
                            <td class="px-3 py-2 font-semibold text-slate-800">{{ $row['rep']->name }}</td>
                            <td class="px-3 py-2 text-center tabular-nums">{{ $row['sent_count'] ?? 0 }}</td>
                            <td class="px-3 py-2 text-center tabular-nums">{{ $row['overdue_at_reminder'] ?? 0 }}</td>
                            <td class="px-3 py-2 text-center tabular-nums">{{ $row['today_at_reminder'] ?? 0 }}</td>
                            <td class="px-3 py-2 text-center tabular-nums">{{ $row['stale_at_reminder'] ?? 0 }}</td>
                            <td class="px-3 py-2 text-center tabular-nums {{ ($row['current_overdue'] ?? 0) > 0 ? 'text-rose-700 font-bold' : 'text-emerald-700 font-bold' }}">{{ $row['current_overdue'] ?? 0 }}</td>
                            <td class="px-3 py-2 text-center tabular-nums">
                                @if($row['compliance_pct'] !== null)
                                    <span class="{{ $row['compliance_pct'] >= 70 ? 'text-emerald-700 font-bold' : 'text-amber-700 font-bold' }}">{{ number_format((float) $row['compliance_pct'], 1) }}%</span>
                                @else
                                    <span class="text-slate-500">—</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-slate-500">لا توجد بيانات إشعارات متابعة بعد.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="rounded-2xl border border-gray-200 bg-white shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-[1100px] w-full text-sm text-right">
                <thead>
                    <tr class="bg-slate-800 text-white text-xs font-bold">
                        <th class="px-4 py-3">الموظف</th>
                        <th class="px-4 py-3 text-center">مركّب</th>
                        <th class="px-4 py-3 text-center">نتائج</th>
                        <th class="px-4 py-3 text-center">نشاط</th>
                        <th class="px-4 py-3 text-center">جودة</th>
                        <th class="px-4 py-3 text-center">التزام</th>
                        <th class="px-4 py-3 text-left">إيراد الشهر</th>
                        <th class="px-4 py-3 text-center">فوز</th>
                        <th class="px-4 py-3 text-center">أنبوب</th>
                        <th class="px-4 py-3 text-center">SLA متأخر</th>
                        <th class="px-4 py-3 text-center">راكد</th>
                        <th class="px-4 py-3">مراقبة</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($rows as $r)
                        @php
                            $u = $r['user'];
                            $comp = $r['composite'];
                            $compClass = $comp < 45 ? 'text-rose-700 font-black' : ($comp < 65 ? 'text-amber-700 font-bold' : 'text-emerald-700 font-bold');
                        @endphp
                        <tr class="hover:bg-emerald-50/30">
                            <td class="px-4 py-3 font-semibold text-gray-900">{{ $u->name }}</td>
                            <td class="px-4 py-3 text-center tabular-nums {{ $compClass }}">{{ number_format($comp, 1) }}</td>
                            @foreach(['results','activity','quality','discipline'] as $pk)
                                <td class="px-4 py-3 text-center tabular-nums text-gray-700">{{ $r['pillars'][$pk]['score'] ?? '—' }}</td>
                            @endforeach
                            <td class="px-4 py-3 text-left tabular-nums font-medium">{{ number_format($r['month_revenue'], 0) }}</td>
                            <td class="px-4 py-3 text-center">{{ $r['month_won'] }}</td>
                            <td class="px-4 py-3 text-center">{{ $r['open_pipeline'] }}</td>
                            <td class="px-4 py-3 text-center tabular-nums {{ ($r['overdue_followups'] ?? 0) > 0 ? 'text-rose-700 font-bold' : 'text-gray-500' }}">{{ $r['overdue_followups'] ?? 0 }}</td>
                            <td class="px-4 py-3 text-center tabular-nums {{ ($r['stale_open_leads'] ?? 0) > 0 ? 'text-amber-700 font-bold' : 'text-gray-500' }}">{{ $r['stale_open_leads'] ?? 0 }}</td>
                            <td class="px-4 py-3 text-xs text-gray-700 max-w-xs">
                                @if(empty($r['flags']))
                                    <span class="text-emerald-600 font-medium">—</span>
                                @else
                                    <ul class="space-y-1">
                                        @foreach($r['flags'] as $f)
                                            <li class="text-rose-700 font-semibold"><i class="fas fa-circle text-[6px] align-middle ml-1"></i>{{ $f }}</li>
                                        @endforeach
                                    </ul>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="12" class="px-6 py-16 text-center text-gray-500">لا يوجد موظفو مبيعات نشطون.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
