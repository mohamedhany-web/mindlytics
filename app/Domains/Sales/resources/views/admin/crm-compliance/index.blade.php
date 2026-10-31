@extends('layouts.admin')

@section('title', 'تدقيق استخدام CRM')
@section('header', 'تدقيق استخدام CRM')

@section('content')
@php
    $summary = $board['summary'] ?? [];
    $rows = $board['rows'] ?? [];
    $insights = $board['insights'] ?? [];
    $exceptions = $board['exceptions'] ?? [];
    $toneClass = function ($tone) {
        return match ($tone) {
            'excellent' => ['bg' => 'bg-emerald-50', 'text' => 'text-emerald-700', 'bar' => 'bg-emerald-500', 'border' => 'border-emerald-200', 'label' => 'ممتاز'],
            'good' => ['bg' => 'bg-sky-50', 'text' => 'text-sky-700', 'bar' => 'bg-sky-500', 'border' => 'border-sky-200', 'label' => 'جيد'],
            'warning' => ['bg' => 'bg-amber-50', 'text' => 'text-amber-700', 'bar' => 'bg-amber-500', 'border' => 'border-amber-200', 'label' => 'تحت المتابعة'],
            default => ['bg' => 'bg-rose-50', 'text' => 'text-rose-700', 'bar' => 'bg-rose-500', 'border' => 'border-rose-200', 'label' => 'حرج'],
        };
    };
    $selectClass = 'rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500 focus:border-sky-500';
    $statCards = [
        ['label' => 'متوسط الالتزام', 'value' => number_format((float) ($summary['avg_compliance'] ?? 0), 1) . '%', 'icon' => 'fas fa-shield-halved', 'bg' => 'bg-emerald-100', 'text' => 'text-emerald-600', 'description' => 'درجة مركّبة'],
        ['label' => 'استخدام CRM', 'value' => number_format((float) ($summary['avg_usage'] ?? 0), 1) . '%', 'icon' => 'fas fa-database', 'bg' => 'bg-sky-100', 'text' => 'text-sky-600', 'description' => 'أيام تسجيل فعلي'],
        ['label' => 'جودة التسجيل', 'value' => number_format((float) ($summary['avg_quality'] ?? 0), 1) . '%', 'icon' => 'fas fa-clipboard-check', 'bg' => 'bg-violet-100', 'text' => 'text-violet-600', 'description' => 'نتائج + Qualification'],
        ['label' => 'استثناءات', 'value' => number_format((int) ($summary['total_exceptions'] ?? 0)), 'icon' => 'fas fa-triangle-exclamation', 'bg' => 'bg-rose-100', 'text' => 'text-rose-600', 'description' => ($summary['critical_count'] ?? 0) . ' حرج · ' . ($summary['warning_count'] ?? 0) . ' تحذير'],
    ];
@endphp

<div class="space-y-6">
    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-4 py-4 bg-slate-50 border-b border-slate-200 flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-sky-500 to-emerald-600 flex items-center justify-center text-white shadow-md">
                    <i class="fas fa-user-check"></i>
                </div>
                <div>
                    <h2 class="text-xl font-black text-slate-900">تدقيق استخدام CRM</h2>
                    <p class="text-xs text-slate-600">
                        هل يسجّل السيلز البيانات صح؟ المحاسبة على نشاط مربوط بعميل فقط —
                        {{ $dateFrom }} ← {{ $dateTo }}
                    </p>
                </div>
            </div>
            <a href="{{ route('admin.sales.insights.index') }}"
               class="inline-flex items-center gap-2 px-3 py-2 text-sm font-semibold text-slate-700 rounded-xl border border-slate-300 hover:bg-white">
                <i class="fas fa-lightbulb text-amber-500"></i>
                Insights الإيراد
            </a>
        </div>
        <div class="grid grid-cols-2 xl:grid-cols-4 gap-3 p-4">
            @foreach($statCards as $card)
                <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                    <div class="flex items-center justify-between gap-2">
                        <div class="min-w-0">
                            <p class="text-xs font-semibold text-slate-600 truncate">{{ $card['label'] }}</p>
                            <p class="text-xl font-black text-slate-900 truncate tabular-nums">{{ $card['value'] }}</p>
                        </div>
                        <div class="w-9 h-9 rounded-lg {{ $card['bg'] }} flex items-center justify-center {{ $card['text'] }} flex-shrink-0">
                            <i class="{{ $card['icon'] }} text-sm"></i>
                        </div>
                    </div>
                    <p class="text-[11px] text-slate-500 mt-1 truncate">{{ $card['description'] }}</p>
                </div>
            @endforeach
        </div>
    </section>

    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg p-4">
        <form method="get" class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-6 gap-3 items-end">
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">الفترة</label>
                <select name="period" class="{{ $selectClass }} w-full" onchange="this.form.submit()">
                    <option value="day" @selected($period === 'day')>اليوم</option>
                    <option value="week" @selected($period === 'week')>آخر 7 أيام</option>
                    <option value="month" @selected($period === 'month')>هذا الشهر</option>
                    <option value="custom" @selected($period === 'custom')>مخصص</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">من</label>
                <input type="date" name="date_from" value="{{ $dateFrom }}" class="{{ $selectClass }} w-full">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">إلى</label>
                <input type="date" name="date_to" value="{{ $dateTo }}" class="{{ $selectClass }} w-full">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">الموظف</label>
                <select name="user_id" class="{{ $selectClass }} w-full">
                    <option value="">الكل</option>
                    @foreach($salesReps as $rep)
                        <option value="{{ $rep->id }}" @selected(($filters['user_id'] ?? null) == $rep->id)>{{ $rep->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">الفريق</label>
                <select name="team_id" class="{{ $selectClass }} w-full">
                    <option value="">كل الفرق</option>
                    @foreach($teams as $team)
                        <option value="{{ $team->id }}" @selected(($filters['team_id'] ?? null) == $team->id)>{{ $team->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <button type="submit" class="w-full inline-flex items-center justify-center gap-2 rounded-xl bg-sky-600 hover:bg-sky-700 text-white font-semibold px-4 py-2 text-sm">
                    <i class="fas fa-filter"></i> تطبيق
                </button>
            </div>
        </form>
    </section>

    @if(count($insights) > 0)
        <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
            <div class="px-4 py-3 border-b border-slate-200 bg-slate-50">
                <h3 class="text-base font-black text-slate-900 flex items-center gap-2">
                    <i class="fas fa-lightbulb text-amber-500"></i>
                    Insights الالتزام
                </h3>
            </div>
            <div class="p-4 grid grid-cols-1 md:grid-cols-2 gap-3">
                @foreach($insights as $insight)
                    @php
                        $sev = match ($insight['severity'] ?? 'info') {
                            'critical' => 'border-rose-200 bg-rose-50 text-rose-800',
                            'warning' => 'border-amber-200 bg-amber-50 text-amber-800',
                            'good' => 'border-emerald-200 bg-emerald-50 text-emerald-800',
                            default => 'border-slate-200 bg-slate-50 text-slate-700',
                        };
                    @endphp
                    <div class="rounded-xl border p-3 {{ $sev }}">
                        <p class="text-sm font-bold">{{ $insight['title'] }}</p>
                        <p class="text-xs mt-1 opacity-90">{{ $insight['detail'] }}</p>
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-4 py-3 border-b border-slate-200 bg-slate-50 flex items-center justify-between">
            <h3 class="text-base font-black text-slate-900">موظفو المبيعات</h3>
            <span class="text-xs font-semibold text-slate-600 bg-slate-100 px-2.5 py-1 rounded-lg">{{ count($rows) }} موظف</span>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50 text-slate-600 text-xs">
                    <tr>
                        <th class="text-right px-4 py-3 font-semibold">الموظف</th>
                        <th class="text-right px-4 py-3 font-semibold">الالتزام</th>
                        <th class="text-right px-4 py-3 font-semibold">استخدام CRM</th>
                        <th class="text-right px-4 py-3 font-semibold">جودة</th>
                        <th class="text-right px-4 py-3 font-semibold">ربط سوشيال</th>
                        <th class="text-right px-4 py-3 font-semibold">دقة التقرير</th>
                        <th class="text-right px-4 py-3 font-semibold">Pipeline</th>
                        <th class="text-right px-4 py-3 font-semibold">استثناءات</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($rows as $row)
                        @php $t = $toneClass($row['tone'] ?? 'critical'); @endphp
                        <tr class="hover:bg-slate-50/80">
                            <td class="px-4 py-3">
                                <p class="font-bold text-slate-900">{{ $row['name'] }}</p>
                                <p class="text-[11px] text-slate-500">{{ $row['job_title'] }}</p>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-xs font-black {{ $t['bg'] }} {{ $t['text'] }} border {{ $t['border'] }}">
                                        {{ number_format((float) $row['compliance_score'], 1) }}%
                                    </span>
                                    <span class="text-[10px] text-slate-500">{{ $t['label'] }}</span>
                                </div>
                                <div class="mt-1 h-1.5 w-24 rounded-full bg-slate-100 overflow-hidden">
                                    <div class="h-full {{ $t['bar'] }}" style="width: {{ min(100, (float) $row['compliance_score']) }}%"></div>
                                </div>
                            </td>
                            <td class="px-4 py-3 tabular-nums">
                                {{ $row['usage']['usage_pct'] !== null ? number_format((float) $row['usage']['usage_pct'], 1).'%' : '—' }}
                                <p class="text-[10px] text-slate-500">{{ $row['usage']['engaged_work_days'] }}/{{ $row['usage']['work_days'] }} يوم</p>
                            </td>
                            <td class="px-4 py-3 tabular-nums">
                                {{ $row['quality']['quality_score'] !== null ? number_format((float) $row['quality']['quality_score'], 1).'%' : '—' }}
                            </td>
                            <td class="px-4 py-3 tabular-nums">
                                {{ $row['social']['link_pct'] !== null ? number_format((float) $row['social']['link_pct'], 1).'%' : '—' }}
                                @if(($row['social']['unlinked_total'] ?? 0) > 0)
                                    <p class="text-[10px] text-rose-600">{{ $row['social']['unlinked_total'] }} غير مربوط</p>
                                @endif
                            </td>
                            <td class="px-4 py-3 tabular-nums">
                                {{ $row['report']['accuracy_pct'] !== null ? number_format((float) $row['report']['accuracy_pct'], 1).'%' : '—' }}
                                @if(($row['report']['inflated_days'] ?? 0) > 0)
                                    <p class="text-[10px] text-rose-600">تضخيم {{ $row['report']['inflated_days'] }} يوم</p>
                                @endif
                            </td>
                            <td class="px-4 py-3 tabular-nums">
                                {{ (int) ($row['pipeline']['stage_changes'] ?? 0) }}
                                <p class="text-[10px] text-slate-500">{{ (int) ($row['pipeline']['unique_leads_moved'] ?? 0) }} عميل</p>
                            </td>
                            <td class="px-4 py-3">
                                <span class="text-xs font-semibold {{ count($row['exceptions']) > 0 ? 'text-rose-600' : 'text-emerald-600' }}">
                                    {{ count($row['exceptions']) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-left">
                                <a href="{{ route('admin.sales.crm-compliance.show', array_merge(['employee' => $row['employee_id']], request()->only(['period', 'date_from', 'date_to']))) }}"
                                   class="inline-flex items-center gap-1 text-xs font-semibold text-sky-700 hover:text-sky-900">
                                    التفاصيل <i class="fas fa-arrow-left"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-4 py-10 text-center text-slate-500 text-sm">لا يوجد موظفو مبيعات نشطون في الفلتر الحالي.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    @if(count($exceptions) > 0)
        <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
            <div class="px-4 py-3 border-b border-slate-200 bg-slate-50">
                <h3 class="text-base font-black text-slate-900 flex items-center gap-2">
                    <i class="fas fa-triangle-exclamation text-rose-500"></i>
                    استثناءات الفريق
                </h3>
            </div>
            <ul class="divide-y divide-slate-100">
                @foreach(array_slice($exceptions, 0, 40) as $ex)
                    <li class="px-4 py-3 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-1">
                        <div>
                            <p class="text-sm font-bold text-slate-900">{{ $ex['employee_name'] ?? '' }} — {{ $ex['title'] }}</p>
                            <p class="text-xs text-slate-600">{{ $ex['detail'] }}</p>
                        </div>
                        <span class="text-[10px] font-bold uppercase tracking-wide
                            {{ ($ex['severity'] ?? '') === 'critical' ? 'text-rose-600' : (($ex['severity'] ?? '') === 'warning' ? 'text-amber-600' : 'text-slate-500') }}">
                            {{ $ex['severity'] ?? 'info' }}
                        </span>
                    </li>
                @endforeach
            </ul>
        </section>
    @endif
</div>
@endsection
