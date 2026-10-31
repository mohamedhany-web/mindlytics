@extends('layouts.employee')

@section('title', 'مركز الرقابة اليومية')
@section('header', 'مركز رقابة مدير المبيعات')

@section('content')
@php
    $summary = $board['summary'];
    $rows = $board['rows'];
    $exceptions = $board['exceptions'];
    $toneClass = function ($tone) {
        return match ($tone) {
            'excellent' => ['bg' => 'bg-emerald-50', 'text' => 'text-emerald-700', 'bar' => 'bg-emerald-500', 'border' => 'border-emerald-200', 'label' => 'ممتاز'],
            'good' => ['bg' => 'bg-sky-50', 'text' => 'text-sky-700', 'bar' => 'bg-sky-500', 'border' => 'border-sky-200', 'label' => 'جيد'],
            'warning' => ['bg' => 'bg-amber-50', 'text' => 'text-amber-700', 'bar' => 'bg-amber-500', 'border' => 'border-amber-200', 'label' => 'تحت المتابعة'],
            default => ['bg' => 'bg-rose-50', 'text' => 'text-rose-700', 'bar' => 'bg-rose-500', 'border' => 'border-rose-200', 'label' => 'حرج'],
        };
    };
@endphp

<div class="space-y-5">
    @if(session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 text-emerald-800 px-4 py-3 text-sm">{{ session('success') }}</div>
    @endif

    <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-4 sm:p-5">
        <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">
            <div>
                <h1 class="text-lg sm:text-xl font-black text-slate-900 flex items-center gap-2">
                    <i class="fas fa-shield-halved text-teal-600"></i>
                    مركز الرقابة اليومية
                </h1>
                <p class="text-xs sm:text-sm text-slate-500 mt-1">
                    {{ $team->name }} — {{ $date->format('Y-m-d') }}
                    <span class="mx-1">·</span>
                    المحاسبة على نشاط CRM الموثّق فقط (مرتبط بعميل)
                </p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('employee.sales-manager.scorecard.pdf', request()->query()) }}"
                   class="inline-flex items-center gap-2 rounded-xl bg-slate-800 hover:bg-slate-900 text-white font-semibold px-4 py-2 text-sm">
                    <i class="fas fa-file-pdf"></i> PDF
                </a>
                <a href="{{ route('employee.sales-manager.scorecard.excel', request()->query()) }}"
                   class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-semibold px-4 py-2 text-sm">
                    <i class="fas fa-file-excel"></i> Excel
                </a>
                <a href="{{ route('employee.sales-manager.kpi.index') }}"
                   class="inline-flex items-center gap-2 rounded-xl border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                    KPIs
                </a>
            </div>
        </div>

        <form method="GET" class="mt-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 border-t border-slate-100 pt-4">
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1.5">التاريخ</label>
                <input type="date" name="date" value="{{ $date->toDateString() }}" max="{{ now()->toDateString() }}"
                       class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1.5">الموظف</label>
                <select name="user_id" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm">
                    <option value="">كل الفريق</option>
                    @foreach($members as $m)
                        <option value="{{ $m->id }}" @selected($selectedId === (int) $m->id)>{{ $m->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1.5">القناة</label>
                <select name="channel" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm">
                    <option value="">كل القنوات</option>
                    <option value="calls" @selected($channel === 'calls')>مكالمات</option>
                    <option value="whatsapp" @selected($channel === 'whatsapp')>واتساب</option>
                    <option value="social" @selected($channel === 'social')>سوشيال</option>
                    <option value="cold" @selected($channel === 'cold')>كولد داتا</option>
                    <option value="exceptions" @selected($channel === 'exceptions')>استثناءات فقط</option>
                </select>
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-teal-600 hover:bg-teal-700 text-white font-semibold px-5 py-2 text-sm">
                    <i class="fas fa-filter"></i> عرض
                </button>
                <a href="{{ route('employee.sales-manager.scorecard.index') }}" class="inline-flex items-center rounded-xl border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-50">اليوم</a>
            </div>
        </form>
    </div>

    @if($members->isEmpty())
        <div class="rounded-2xl border border-amber-200 bg-amber-50 p-6 text-amber-900 text-sm">
            <p class="font-bold mb-1"><i class="fas fa-triangle-exclamation"></i> الفريق بدون أعضاء نشطين</p>
            <p>أضف موظفي مبيعات إلى الفريق من الإدارة حتى تظهر درجاتهم هنا.</p>
        </div>
    @endif

    <div class="grid grid-cols-2 lg:grid-cols-4 xl:grid-cols-6 gap-3">
        @foreach([
            ['أعضاء', $summary['members'], 'fa-users', 'bg-slate-100 text-slate-700'],
            ['متوسط الدرجة', $summary['avg_score'].'/100', 'fa-gauge-high', 'bg-indigo-100 text-indigo-700'],
            ['محاولات اتصال', $summary['call_attempts'], 'fa-phone', 'bg-sky-100 text-sky-700'],
            ['تم الرد', $summary['calls_answered'], 'fa-phone-volume', 'bg-teal-100 text-teal-700'],
            ['مدفوع مؤكد', $summary['finance_verified_paid'], 'fa-circle-check', 'bg-emerald-100 text-emerald-700'],
            ['استثناءات', $summary['exceptions_total'], 'fa-triangle-exclamation', 'bg-rose-100 text-rose-700'],
        ] as [$label, $value, $icon, $tone])
            <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-4">
                <div class="flex items-center gap-3">
                    <span class="w-9 h-9 rounded-xl flex items-center justify-center {{ $tone }}">
                        <i class="fas {{ $icon }} text-sm"></i>
                    </span>
                    <div class="min-w-0">
                        <p class="text-[11px] font-semibold text-slate-500 truncate">{{ $label }}</p>
                        <p class="text-base font-black text-slate-900 tabular-nums truncate">{{ $value }}</p>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    @if(count($exceptions) > 0)
        <div class="rounded-2xl border border-amber-200 bg-amber-50/60 p-4">
            <p class="text-sm font-black text-amber-900 mb-2"><i class="fas fa-bug"></i> فجوات بيانات لا تدخل الدرجة ({{ count($exceptions) }})</p>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-2 max-h-48 overflow-y-auto">
                @foreach(collect($exceptions)->take(20) as $ex)
                    <div class="rounded-xl bg-white border border-amber-100 px-3 py-2 text-xs text-slate-700">
                        <span class="font-bold">{{ $ex['employee_name'] }}</span>
                        — {{ $ex['label'] }}
                        <span class="tabular-nums font-black text-amber-700">({{ $ex['count'] }})</span>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    @forelse($rows as $row)
        @php $t = $toneClass($row['tone']); @endphp
        <div class="rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-4 sm:px-5 py-4 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3 {{ $t['bg'] }} border-b {{ $t['border'] }}">
                <div class="flex items-center gap-3 min-w-0">
                    <span class="w-11 h-11 rounded-xl bg-white border {{ $t['border'] }} flex items-center justify-center font-black {{ $t['text'] }}">
                        {{ mb_substr($row['name'], 0, 1) }}
                    </span>
                    <div class="min-w-0">
                        <p class="font-black text-slate-900 truncate">{{ $row['name'] }}</p>
                        <p class="text-xs text-slate-500">{{ $row['job_title'] }}
                            @if($row['review'])
                                · مراجعة: {{ $row['review']->statusLabel() }}
                            @else
                                · بانتظار مراجعة المدير
                            @endif
                        </p>
                    </div>
                </div>
                <div class="flex flex-wrap items-center gap-3">
                    <div class="text-left">
                        <p class="text-[11px] font-semibold text-slate-500">Verified Score</p>
                        <p class="text-2xl font-black tabular-nums {{ $t['text'] }}">{{ $row['verified_score'] }}<span class="text-sm text-slate-400">/100</span></p>
                    </div>
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-white border {{ $t['border'] }} {{ $t['text'] }}">{{ $t['label'] }}</span>
                    <a href="{{ route('employee.sales-manager.scorecard.show', ['employee' => $row['employee_id'], 'date' => $date->toDateString()]) }}"
                       class="inline-flex items-center gap-1.5 rounded-xl border border-slate-300 bg-white px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50">
                        تفاصيل ومراجعة
                    </a>
                </div>
            </div>

            <div class="grid grid-cols-2 lg:grid-cols-5 gap-3 px-4 sm:px-5 py-4">
                @foreach($row['pillars'] as $key => $pillar)
                    @php
                        $ps = (float) $pillar['score'];
                        $pt = $toneClass($ps >= 85 ? 'excellent' : ($ps >= 65 ? 'good' : ($ps >= 45 ? 'warning' : 'critical')));
                    @endphp
                    <div class="rounded-xl border border-slate-200 p-3">
                        <div class="flex items-center justify-between gap-2">
                            <p class="text-[11px] font-bold text-slate-600 truncate">
                                {{ ['results'=>'نتائج','activity'=>'نشاط','quality'=>'جودة','crm_discipline'=>'CRM','attendance'=>'حضور'][$key] ?? $key }}
                            </p>
                            <p class="text-sm font-black tabular-nums {{ $pt['text'] }}">{{ $pillar['score'] }}</p>
                        </div>
                        <div class="h-1.5 rounded-full bg-slate-100 mt-2 overflow-hidden">
                            <div class="h-full rounded-full {{ $pt['bar'] }}" style="width: {{ min(100, $ps) }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-6 gap-2 px-4 sm:px-5 pb-4 text-xs">
                <div class="rounded-lg bg-slate-50 px-3 py-2"><span class="text-slate-500">محاولات</span> <span class="font-black tabular-nums">{{ $row['sos']['call_attempts_daily'] ?? 0 }}</span></div>
                <div class="rounded-lg bg-slate-50 px-3 py-2"><span class="text-slate-500">ردود</span> <span class="font-black tabular-nums">{{ $row['sos']['calls_answered_daily'] ?? 0 }}</span></div>
                <div class="rounded-lg bg-slate-50 px-3 py-2"><span class="text-slate-500">سوشيال مرتبط</span> <span class="font-black tabular-nums">{{ $row['channels']['social_linked_total'] ?? 0 }}</span></div>
                <div class="rounded-lg bg-slate-50 px-3 py-2"><span class="text-slate-500">كولد معمول</span> <span class="font-black tabular-nums">{{ $row['cold']['worked_today'] ?? 0 }}</span></div>
                <div class="rounded-lg bg-slate-50 px-3 py-2"><span class="text-slate-500">مدفوع مؤكد</span> <span class="font-black tabular-nums text-emerald-700">{{ $row['financial']['finance_verified_paid'] ?? 0 }}</span></div>
                <div class="rounded-lg bg-slate-50 px-3 py-2"><span class="text-slate-500">تقرير</span> <span class="font-black {{ $row['daily_report_submitted'] ? 'text-emerald-700' : 'text-rose-600' }}">{{ $row['daily_report_submitted'] ? 'مسلّم' : 'ناقص' }}</span></div>
            </div>

            @if(count($row['missed_points']) > 0)
                <div class="px-4 sm:px-5 pb-4">
                    <div class="rounded-xl border border-rose-100 bg-rose-50/50 px-3 py-2">
                        <p class="text-[11px] font-bold text-rose-800 mb-1">نقاط ناقصة</p>
                        <ul class="space-y-0.5">
                            @foreach(array_slice($row['missed_points'], 0, 4) as $m)
                                <li class="text-xs text-rose-700">• {{ $m }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif
        </div>
    @empty
        <div class="rounded-2xl bg-white border border-slate-200 p-10 text-center text-slate-500">
            لا توجد نتائج للفلاتر المحددة.
        </div>
    @endforelse
</div>
@endsection
