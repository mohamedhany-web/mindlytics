@extends('layouts.employee')

@section('title', 'تقرير '.$report->user->name)
@section('header', 'تقرير يومي — '.$report->user->name)

@section('content')
<div class="space-y-6 max-w-3xl">
    <div class="bg-white rounded-xl border p-6">
        <p class="text-sm text-slate-500">التاريخ: {{ $report->report_date?->format('Y-m-d') }}</p>
        <div class="grid grid-cols-2 md:grid-cols-3 gap-4 mt-4 text-sm">
            <div><span class="text-slate-500">مكالمات:</span> <strong>{{ $report->calls_made ?? '—' }}</strong></div>
            <div><span class="text-slate-500">Leads مؤهلة:</span> <strong>{{ $report->leads_qualified ?? '—' }}</strong></div>
            <div><span class="text-slate-500">حجوزات:</span> <strong>{{ $report->bookings_from_leads ?? '—' }}</strong></div>
            <div><span class="text-slate-500">متابعات:</span> <strong>{{ $report->followups_done ?? '—' }}</strong></div>
            <div><span class="text-slate-500">رسائل:</span> <strong>{{ $report->messages_replied ?? '—' }}</strong></div>
        </div>
        @if($report->activity_notes)
            <div class="mt-4"><p class="text-sm font-semibold text-slate-700">ملاحظات النشاط</p><p class="text-sm text-slate-600 mt-1">{{ $report->activity_notes }}</p></div>
        @endif
        @if($report->productivity_notes)
            <div class="mt-4"><p class="text-sm font-semibold text-slate-700">ملاحظات الإنتاجية</p><p class="text-sm text-slate-600 mt-1">{{ $report->productivity_notes }}</p></div>
        @endif
    </div>
    <a href="{{ route('employee.sales-manager.daily-reports.index') }}" class="text-sm text-emerald-700 font-semibold">← العودة</a>
</div>
@endsection
