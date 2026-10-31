@extends('layouts.admin')

@section('title', 'تقرير فريق')
@section('header', 'تقرير فريق — '.$report->team->name)

@section('content')
<div class="max-w-3xl space-y-6">
    <div class="bg-white rounded-xl border p-6">
        <p class="text-sm text-gray-500">التاريخ: {{ $report->report_date?->format('Y-m-d') }} · المدير: {{ $report->manager->name ?? '—' }}</p>
        <div class="grid grid-cols-3 gap-4 mt-4 text-sm">
            <div>أعضاء: <strong>{{ $report->team_members_count }}</strong></div>
            <div>تقارير مستلمة: <strong>{{ $report->reports_received }}</strong></div>
            <div>مكالمات: <strong>{{ $report->total_calls }}</strong></div>
        </div>
        <div class="mt-6 space-y-4">
            <div><p class="font-semibold">ملخص الفريق</p><p class="text-gray-700 mt-1 whitespace-pre-wrap">{{ $report->team_summary }}</p></div>
            @if($report->performance_notes)<div><p class="font-semibold">الأداء</p><p class="text-gray-700 mt-1 whitespace-pre-wrap">{{ $report->performance_notes }}</p></div>@endif
            @if($report->challenges)<div><p class="font-semibold">التحديات</p><p class="text-gray-700 mt-1 whitespace-pre-wrap">{{ $report->challenges }}</p></div>@endif
            @if($report->recommendations)<div><p class="font-semibold">توصيات</p><p class="text-gray-700 mt-1 whitespace-pre-wrap">{{ $report->recommendations }}</p></div>@endif
        </div>
    </div>
    <a href="{{ route('admin.sales.team-daily-reports.index') }}" class="text-emerald-700 font-semibold text-sm">← العودة</a>
</div>
@endsection
