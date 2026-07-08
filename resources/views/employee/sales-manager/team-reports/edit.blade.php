@extends('layouts.employee')

@section('title', 'تقرير الفريق')
@section('header', 'تقرير الفريق — '.$team->name)

@section('content')
<div class="space-y-6 max-w-3xl">
    <div class="bg-slate-50 border border-slate-200 rounded-xl p-4 text-sm">
        <p>تاريخ التقرير: <strong>{{ $date->format('Y-m-d') }}</strong></p>
        <p class="mt-1">تقارير الأعضاء المُسلَّمة: <strong>{{ $memberReports->count() }}/{{ $report->team_members_count }}</strong></p>
        <p class="mt-1">مجموع المكالمات: {{ $report->total_calls }} · Leads: {{ $report->total_leads_qualified }} · حجوزات: {{ $report->total_bookings }}</p>
    </div>

    @if($memberReports->isNotEmpty())
        <div class="bg-white rounded-xl border p-4">
            <p class="font-semibold text-slate-800 mb-2">ملخص تقارير الأعضاء</p>
            <ul class="text-sm space-y-1 text-slate-600">
                @foreach($memberReports as $mr)
                    <li>{{ $mr->user->name }} — مكالمات: {{ $mr->calls_made ?? 0 }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('employee.sales-manager.team-reports.store') }}" class="bg-white rounded-xl border p-6 space-y-4">
        @csrf
        <input type="hidden" name="report_date" value="{{ $date->format('Y-m-d') }}">
        <div>
            <label class="block text-sm font-medium mb-1">ملخص أداء الفريق *</label>
            <textarea name="team_summary" rows="4" required class="w-full px-3 py-2 border rounded-lg text-sm">{{ old('team_summary', $report->team_summary) }}</textarea>
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">ملاحظات الأداء</label>
            <textarea name="performance_notes" rows="3" class="w-full px-3 py-2 border rounded-lg text-sm">{{ old('performance_notes', $report->performance_notes) }}</textarea>
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">التحديات</label>
            <textarea name="challenges" rows="3" class="w-full px-3 py-2 border rounded-lg text-sm">{{ old('challenges', $report->challenges) }}</textarea>
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">توصيات للإدارة</label>
            <textarea name="recommendations" rows="3" class="w-full px-3 py-2 border rounded-lg text-sm">{{ old('recommendations', $report->recommendations) }}</textarea>
        </div>
        <div class="flex gap-3">
            <button type="submit" name="submit" value="0" class="px-5 py-2.5 border border-slate-300 rounded-lg text-sm font-semibold">حفظ مسودة</button>
            <button type="submit" name="submit" value="1" class="px-5 py-2.5 bg-emerald-600 text-white rounded-lg text-sm font-semibold">تسليم للإدارة</button>
        </div>
    </form>
</div>
@endsection
