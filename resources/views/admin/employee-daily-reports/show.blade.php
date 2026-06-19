@extends('layouts.admin')

@section('title', 'تقرير يومي')
@section('header', 'تقرير يومي — ' . ($report->user->name ?? ''))

@section('content')
<div class="max-w-3xl space-y-4">
    <a href="{{ route('admin.employee-daily-reports.index') }}" class="text-sm text-slate-600 hover:text-slate-900"><i class="fas fa-arrow-right ml-1"></i> العودة</a>
    <div class="rounded-2xl bg-white border p-6 space-y-4">
        <p><strong>الموظف:</strong> {{ $report->user->name ?? '—' }}</p>
        <p><strong>التاريخ:</strong> {{ $report->report_date->format('Y-m-d') }}</p>
        <p><strong>الحالة:</strong> {{ $report->isSubmitted() ? 'مُرسل' : 'مسودة' }}</p>
        @if($report->hours_worked)<p><strong>ساعات:</strong> {{ $report->hours_worked }}</p>@endif
        <div><h3 class="font-bold mb-1">ملخص</h3><p class="whitespace-pre-wrap text-slate-800">{{ $report->summary ?: '—' }}</p></div>
        <div><h3 class="font-bold mb-1">المهام المنجزة</h3><p class="whitespace-pre-wrap text-slate-800">{{ $report->tasks_done ?: '—' }}</p></div>
        @if($report->tomorrow_plan)<div><h3 class="font-bold mb-1">خطة الغد</h3><p class="whitespace-pre-wrap">{{ $report->tomorrow_plan }}</p></div>@endif
        @if($report->blockers)<div><h3 class="font-bold mb-1">معوقات</h3><p class="whitespace-pre-wrap text-rose-800">{{ $report->blockers }}</p></div>@endif
        @if($report->autoDeduction)
            <div class="rounded-xl border border-rose-200 bg-rose-50 p-3 text-sm text-rose-900">
                <i class="fas fa-minus-circle ml-1"></i> غرامة مسجلة: {{ number_format((float) $report->autoDeduction->amount, 2) }} ج.م
            </div>
        @endif
    </div>
</div>
@endsection
