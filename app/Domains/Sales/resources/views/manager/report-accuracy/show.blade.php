@extends('sales::crm.layout')

@section('title', 'تفاصيل مطابقة التقرير')
@section('header', 'تفاصيل مطابقة التقرير')

@section('content')
<div class="space-y-6">
    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg p-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <a href="{{ route('employee.sales-manager.report-accuracy.index') }}" class="text-xs font-semibold text-teal-700 hover:underline">← لوحة الدقة</a>
            <h2 class="text-xl font-black text-slate-900 mt-1">{{ $report->user->name ?? '—' }} · {{ $report->report_date?->format('Y-m-d') }}</h2>
        </div>
        <a href="{{ route('employee.sales-manager.daily-reports.show', $report) }}"
           class="inline-flex items-center gap-2 px-3 py-2 text-sm font-semibold text-slate-700 rounded-xl border border-slate-300 hover:bg-slate-50">
            فتح التقرير الكامل
        </a>
    </section>

    @include('sales::partials.report-actuals-diff', [
        'diff' => $diff,
        'leadShowRoute' => fn ($id) => route('employee.sales-manager.leads.show', $id),
    ])
</div>
@endsection
