@extends('layouts.employee')

@section('title', 'أهداف KPIs الفريق')
@section('header', 'أهداف KPIs — ضبط أهداف السيلز')

@section('content')
@php
    $inputClass = 'w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500';
    $filledCount = collect($labels)->filter(fn ($_, $key) => ($targets[$key] ?? '') !== '' && ($targets[$key] ?? null) !== null)->count();
@endphp

<div class="space-y-6">
    <section class="rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-4 py-4 sm:px-6 border-b border-slate-100 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-xl bg-teal-100 flex items-center justify-center text-teal-700">
                    <i class="fas fa-bullseye"></i>
                </div>
                <div>
                    <h1 class="text-lg font-black text-slate-900">أهداف KPIs للفريق</h1>
                    <p class="text-xs text-slate-500 mt-0.5">{{ $team->name }} — ضبط أهداف شهرية ملزمة ومتابعة الإنجاز</p>
                </div>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('employee.sales-manager.kpi.index') }}"
                   class="inline-flex items-center gap-2 rounded-xl border border-slate-300 px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                    <i class="fas fa-chart-line text-teal-600"></i> لوحة المؤشرات
                </a>
                <a href="{{ route('employee.sales-manager.campaign-reports.index') }}"
                   class="inline-flex items-center gap-2 rounded-xl border border-slate-300 px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                    <i class="fas fa-bullhorn text-amber-600"></i> تقارير الكامبين
                </a>
            </div>
        </div>
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 p-4 sm:p-6">
            <div class="rounded-xl border border-slate-200 p-3">
                <p class="text-xs text-slate-500">موظفو الفريق</p>
                <p class="text-xl font-black text-slate-900 tabular-nums">{{ $salesReps->count() }}</p>
            </div>
            <div class="rounded-xl border border-slate-200 p-3">
                <p class="text-xs text-slate-500">حقول الأهداف</p>
                <p class="text-xl font-black text-slate-900 tabular-nums">{{ count($labels) }}</p>
            </div>
            <div class="rounded-xl border border-slate-200 p-3">
                <p class="text-xs text-slate-500">معبّأة للموظف</p>
                <p class="text-xl font-black text-slate-900 tabular-nums">{{ $filledCount }}</p>
            </div>
            <div class="rounded-xl border border-slate-200 p-3">
                <p class="text-xs text-slate-500">الشهر</p>
                <p class="text-xl font-black text-slate-900 tabular-nums">{{ $yearMonth }}</p>
                <p class="text-[11px] text-slate-500 truncate">{{ $rep?->name ?? '—' }}</p>
            </div>
        </div>
    </section>

    @if(session('success'))
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-emerald-900 text-sm font-semibold flex items-center gap-2">
            <i class="fas fa-check-circle text-emerald-600"></i>
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-rose-900 text-sm">
            <p class="font-semibold mb-1">تحقق من البيانات</p>
            <ul class="list-disc list-inside space-y-0.5 text-xs">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if($salesReps->isEmpty())
        <div class="rounded-2xl bg-white border border-slate-200 p-10 text-center text-slate-500">
            لا يوجد موظفون نشطون في نطاق إدارتك لضبط الأهداف.
        </div>
    @else
        <section class="rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-4 py-3 border-b border-slate-100 bg-slate-50">
                <h2 class="text-sm font-black text-slate-900">اختيار الموظف والشهر</h2>
            </div>
            <div class="p-4">
                <form method="get" action="{{ route('employee.sales-manager.kpi.targets') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 items-end">
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-semibold text-slate-700 mb-1">الموظف</label>
                        <select name="user_id" class="{{ $inputClass }}">
                            @foreach($salesReps as $sr)
                                <option value="{{ $sr->id }}" @selected((int) $userId === (int) $sr->id)>{{ $sr->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">الشهر</label>
                        <input type="month" name="year_month" value="{{ $yearMonth }}" class="{{ $inputClass }}">
                    </div>
                    <div>
                        <button type="submit" class="w-full inline-flex items-center justify-center gap-2 rounded-xl bg-teal-600 hover:bg-teal-700 px-4 py-2 text-sm font-semibold text-white">
                            <i class="fas fa-search"></i> عرض
                        </button>
                    </div>
                </form>
            </div>
        </section>

        <section class="rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-4 py-3 border-b border-slate-100 bg-slate-50 flex flex-wrap items-center justify-between gap-2">
                <h2 class="text-sm font-black text-slate-900">تغطية الأهداف — {{ $yearMonth }}</h2>
                <span class="text-xs font-semibold text-slate-600">
                    {{ $teamTargetStatus->where('configured', true)->count() }} / {{ $teamTargetStatus->count() }} محفوظة
                </span>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="bg-slate-50 text-slate-600 text-xs">
                            <th class="text-start px-3 py-2">الموظف</th>
                            <th class="text-start px-3 py-2">الحالة</th>
                            <th class="text-start px-3 py-2">المؤشر</th>
                            <th class="text-start px-3 py-2"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($teamTargetStatus as $row)
                            <tr class="{{ (int) $userId === (int) $row['user']->id ? 'bg-teal-50/60' : '' }}">
                                <td class="px-3 py-2.5 font-semibold text-slate-900">{{ $row['user']->name }}</td>
                                <td class="px-3 py-2.5">
                                    @if($row['configured'])
                                        <span class="inline-flex rounded-lg bg-emerald-50 text-emerald-800 border border-emerald-200 px-2 py-0.5 text-[11px] font-bold">محفوظة</span>
                                    @else
                                        <span class="inline-flex rounded-lg bg-amber-50 text-amber-900 border border-amber-200 px-2 py-0.5 text-[11px] font-bold">افتراضي</span>
                                    @endif
                                </td>
                                <td class="px-3 py-2.5 tabular-nums font-bold {{ $row['composite'] >= 65 ? 'text-emerald-700' : ($row['composite'] >= 45 ? 'text-amber-700' : 'text-rose-700') }}">
                                    {{ $row['composite'] }}/100
                                </td>
                                <td class="px-3 py-2.5">
                                    <a href="{{ route('employee.sales-manager.kpi.targets', ['user_id' => $row['user']->id, 'year_month' => $yearMonth]) }}"
                                       class="text-xs font-bold text-teal-700 hover:underline">ضبط</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>

        @if($achievement)
            <section class="rounded-2xl bg-white border border-emerald-200 shadow-sm overflow-hidden">
                <div class="px-4 py-3 border-b border-emerald-100 bg-emerald-50/80 flex flex-wrap items-center justify-between gap-2">
                    <div>
                        <h2 class="text-sm font-black text-emerald-950">ما حقّقه {{ $rep?->name }} مقابل الأهداف</h2>
                        <p class="text-xs text-emerald-900/80 mt-0.5">
                            {{ $hasCustomTargets ? 'أهداف محفوظة لهذا الشهر' : 'حالياً يعتمد على افتراضيات النظام' }}
                        </p>
                    </div>
                    <div class="text-end">
                        <p class="text-[11px] text-emerald-800 font-semibold">المؤشر المركّب</p>
                        <p class="text-3xl font-black text-emerald-900 tabular-nums">{{ $achievement['composite_month'] ?? $achievement['composite'] ?? 0 }}<span class="text-lg">/100</span></p>
                    </div>
                </div>
                <div class="p-4 grid grid-cols-1 xl:grid-cols-2 gap-4">
                    @if($dailyResults)
                        <div class="rounded-xl border border-slate-200 overflow-hidden">
                            <div class="px-3 py-2 bg-slate-50 border-b border-slate-100 text-xs font-bold text-slate-700">
                                نتائج اليوم (SOS) — {{ number_format($dailyResults['overall_pct'] ?? 0, 0) }}%
                            </div>
                            <div class="divide-y divide-slate-100">
                                @foreach(($dailyResults['lines'] ?? []) as $line)
                                    <div class="px-3 py-2 flex items-center justify-between gap-3 text-sm">
                                        <div class="min-w-0">
                                            <p class="font-semibold text-slate-800 truncate">{{ $line['label'] }}</p>
                                            <p class="text-[11px] text-slate-500 tabular-nums">{{ $line['actual'] }} / {{ number_format($line['target'], 0) }}</p>
                                        </div>
                                        <div class="w-28 shrink-0">
                                            <div class="h-2 rounded-full bg-slate-100 overflow-hidden">
                                                <div class="h-full rounded-full {{ $line['pct'] >= 100 ? 'bg-emerald-500' : ($line['pct'] >= 70 ? 'bg-amber-500' : 'bg-rose-500') }}"
                                                     style="width: {{ min(100, $line['pct']) }}%"></div>
                                            </div>
                                            <p class="text-[10px] font-bold text-end mt-0.5 tabular-nums">{{ $line['pct'] }}%</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                    <div class="rounded-xl border border-slate-200 overflow-hidden">
                        <div class="px-3 py-2 bg-slate-50 border-b border-slate-100 text-xs font-bold text-slate-700">مؤشرات الشهر مقابل الهدف</div>
                        <div class="max-h-80 overflow-y-auto divide-y divide-slate-100">
                            @foreach(($achievement['month']['kpi_lines'] ?? []) as $line)
                                @continue(($line['target'] ?? null) === null)
                                <div class="px-3 py-2 flex items-center justify-between gap-3 text-sm">
                                    <div class="min-w-0">
                                        <p class="font-semibold text-slate-800 truncate">{{ $line['label'] }}</p>
                                        <p class="text-[11px] text-slate-500 tabular-nums">
                                            {{ is_numeric($line['actual'] ?? null) ? number_format((float) $line['actual'], is_float($line['actual'] ?? null) ? 1 : 0) : '—' }}
                                            /
                                            {{ is_numeric($line['target'] ?? null) ? number_format((float) $line['target'], 0) : '—' }}
                                        </p>
                                    </div>
                                    <div class="w-28 shrink-0">
                                        @if(($line['pct'] ?? null) !== null)
                                            <div class="h-2 rounded-full bg-slate-100 overflow-hidden">
                                                <div class="h-full rounded-full {{ $line['pct'] >= 100 ? 'bg-emerald-500' : ($line['pct'] >= 70 ? 'bg-amber-500' : 'bg-rose-500') }}"
                                                     style="width: {{ min(100, (float) $line['pct']) }}%"></div>
                                            </div>
                                            <p class="text-[10px] font-bold text-end mt-0.5 tabular-nums">{{ $line['pct'] }}%</p>
                                        @else
                                            <p class="text-[10px] text-slate-400 text-end">—</p>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </section>
        @endif

        <form method="post" action="{{ route('employee.sales-manager.kpi.targets.update') }}" class="space-y-4">
            @csrf
            @method('PUT')
            <input type="hidden" name="user_id" value="{{ $userId }}">
            <input type="hidden" name="year_month" value="{{ $yearMonth }}">

            @foreach($groups as $groupTitle => $group)
                <section class="rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden">
                    <div class="px-4 py-3 border-b border-slate-100 bg-slate-50">
                        <h3 class="text-sm font-black text-slate-900 flex items-center gap-2">
                            <i class="{{ $group['icon'] }}"></i>
                            {{ $groupTitle }}
                        </h3>
                    </div>
                    <div class="p-4 grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                        @foreach($group['keys'] as $key)
                            <div class="rounded-xl border border-slate-200 bg-white p-3">
                                <label class="block text-sm font-semibold text-slate-700 mb-2" for="t_{{ $key }}">
                                    {{ $labels[$key] ?? $key }}
                                    @if(in_array($key, $requiredKeys, true))
                                        <span class="text-rose-600">*</span>
                                    @endif
                                </label>
                                <input type="number" step="any" name="{{ $key }}" id="t_{{ $key }}"
                                       value="{{ old($key, $targets[$key] ?? '') }}"
                                       @if(in_array($key, $requiredKeys, true)) required @endif
                                       class="{{ $inputClass }} @error($key) border-rose-400 @enderror">
                                @error($key)
                                    <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        @endforeach
                    </div>
                </section>
            @endforeach

            <section class="rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-4 py-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div class="space-y-2">
                        <p class="text-xs text-slate-600">
                            الحفظ يطبّق على <strong>{{ $rep?->name }}</strong> — {{ $yearMonth }}
                        </p>
                        <label class="inline-flex items-center gap-2 text-xs font-semibold text-teal-900 bg-teal-50 border border-teal-200 rounded-lg px-3 py-2">
                            <input type="checkbox" name="apply_to_all_team" value="1" class="rounded border-teal-300">
                            تطبيق نفس الأهداف على كل أعضاء الفريق لهذا الشهر
                        </label>
                    </div>
                    <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 bg-teal-600 hover:bg-teal-700 text-white rounded-xl text-sm font-semibold">
                        <i class="fas fa-save"></i>
                        حفظ الأهداف
                    </button>
                </div>
            </section>
        </form>
    @endif
</div>
@endsection
