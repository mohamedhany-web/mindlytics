@extends('layouts.admin')

@section('title', 'إعدادات التقرير اليومي')
@section('header', 'إعدادات التقرير اليومي')

@section('content')
@php
    $s = $settings;
    $reportEnabled = (bool) ($s['enabled'] ?? true);
    $penaltyEnabled = (bool) ($s['penalty_enabled'] ?? true);
    $workDaysOnly = (bool) ($s['work_days_only'] ?? true);

    $summaryCards = [
        [
            'label' => 'التقرير اليومي',
            'value' => $reportEnabled ? 'مفعّل' : 'معطّل',
            'icon' => $reportEnabled ? 'fas fa-toggle-on' : 'fas fa-toggle-off',
            'bg' => $reportEnabled ? 'bg-emerald-100' : 'bg-slate-100',
            'text' => $reportEnabled ? 'text-emerald-600' : 'text-slate-500',
            'description' => $workDaysOnly ? 'أيام العمل فقط' : 'كل أيام الأسبوع',
        ],
        [
            'label' => 'موعد التسليم',
            'value' => $s['deadline_time'] ?? '23:59',
            'icon' => 'fas fa-clock',
            'bg' => 'bg-sky-100',
            'text' => 'text-sky-600',
            'description' => 'آخر موعد يومي',
        ],
        [
            'label' => 'هدف KPI',
            'value' => ($s['kpi_submission_target_pct'] ?? 95) . '%',
            'icon' => 'fas fa-bullseye',
            'bg' => 'bg-violet-100',
            'text' => 'text-violet-600',
            'description' => 'نسبة التسليم الشهرية',
        ],
        [
            'label' => 'الخصم التلقائي',
            'value' => $penaltyEnabled ? number_format((float) ($s['penalty_amount'] ?? 50), 2) . ' ج.م' : 'معطّل',
            'icon' => 'fas fa-gavel',
            'bg' => $penaltyEnabled ? 'bg-rose-100' : 'bg-slate-100',
            'text' => $penaltyEnabled ? 'text-rose-600' : 'text-slate-500',
            'description' => $penaltyEnabled ? ($s['penalty_title'] ?? 'غرامة التأخير') : 'لا يُطبَّق خصم',
        ],
    ];
@endphp

<div class="space-y-6">
    @if(session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 text-emerald-800 px-4 py-3 text-sm font-semibold">
            <i class="fas fa-check-circle ml-1"></i>{{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="rounded-xl border border-rose-200 bg-rose-50 text-rose-800 px-4 py-3 text-sm">
            <p class="font-semibold mb-1"><i class="fas fa-exclamation-circle ml-1"></i> يوجد أخطاء في النموذج:</p>
            <ul class="list-disc list-inside space-y-0.5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- الهيدر + ملخص الإعدادات الحالية --}}
    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-4 py-4 bg-slate-50 border-b border-slate-200 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-sky-500 to-sky-600 flex items-center justify-center text-white shadow-md">
                    <i class="fas fa-cog"></i>
                </div>
                <div>
                    <h2 class="text-xl font-black text-slate-900">إعدادات التقرير اليومي</h2>
                    <p class="text-xs text-slate-600">تحكم في إلزامية التقرير، مواعيد التسليم، أهداف KPI، والخصم التلقائي.</p>
                </div>
            </div>
            <a href="{{ route('admin.sales.daily-reports.index') }}" class="inline-flex items-center gap-2 px-3 py-2 text-sm font-semibold text-slate-700 rounded-xl border border-slate-300 hover:bg-white">
                <i class="fas fa-arrow-right"></i>
                التقارير اليومية
            </a>
        </div>

        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 p-4">
            @foreach($summaryCards as $card)
                <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                    <div class="flex items-center justify-between gap-2">
                        <div class="min-w-0">
                            <p class="text-xs font-semibold text-slate-600 truncate">{{ $card['label'] }}</p>
                            <p class="text-lg font-black text-slate-900 truncate tabular-nums">{{ $card['value'] }}</p>
                        </div>
                        <div class="w-9 h-9 rounded-lg {{ $card['bg'] }} flex items-center justify-center {{ $card['text'] }} flex-shrink-0">
                            <i class="{{ $card['icon'] }} text-sm"></i>
                        </div>
                    </div>
                    <p class="text-[11px] text-slate-500 mt-1 line-clamp-2">{{ $card['description'] }}</p>
                </div>
            @endforeach
        </div>

        <div class="px-4 pb-4">
            <p class="text-xs text-slate-600 bg-sky-50 border border-sky-200 rounded-xl px-3 py-2">
                <i class="fas fa-info-circle text-sky-600 ml-1"></i>
                التغييرات تُطبَّق على موظفي المبيعات من اليوم التالي. الخصم التلقائي يُسجَّل في
                <a href="{{ route('admin.employee-deductions.index') }}" class="font-semibold text-sky-700 hover:underline">خصومات الموظفين</a>.
            </p>
        </div>
    </section>

    @include('admin.sales.daily-reports._settings_form', [
        'formAction' => route('admin.sales.daily-reports.settings.update'),
        'method' => 'PUT',
        'settings' => $settings,
        'cancelUrl' => route('admin.sales.daily-reports.index'),
        'layout' => 'sections',
    ])
</div>
@endsection
