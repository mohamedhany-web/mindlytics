@extends('layouts.employee')

@section('title', 'شيفت — ' . $employee->name)
@section('header', 'شيفت الموظف')

@section('content')
<div class="space-y-6">
    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg p-4">
        <a href="{{ route('employee.sales-manager.shifts.index', request()->only('week')) }}" class="text-xs font-semibold text-violet-700 hover:text-violet-900 inline-flex items-center gap-1 mb-3">
            <i class="fas fa-arrow-right"></i> العودة لجدول الفريق
        </a>
        <h2 class="text-xl font-black text-slate-900">{{ $employee->name }}</h2>
        <p class="text-xs text-slate-500">{{ $team->name }}</p>

        @if($today)
            <div class="mt-4 rounded-xl border border-violet-100 bg-violet-50/50 p-3">
                <p class="text-xs font-bold text-violet-800">اليوم — {{ $today['day_name'] ?? '' }}</p>
                @if($today['is_working_today'] ?? false)
                    @if($today['current'] ?? null)
                        <p class="text-lg font-black text-slate-900 mt-1">الآن: {{ $today['current']['channels_label'] }} حتى {{ $today['current']['end_label'] }}</p>
                    @elseif($today['next'] ?? null)
                        <p class="text-sm font-semibold text-slate-700 mt-1">التالي {{ $today['next']['start_label'] }}: {{ $today['next']['channels_label'] }}</p>
                    @endif
                    @if(! empty($today['segments_today']))
                        <div class="flex flex-wrap gap-2 mt-2">
                            @foreach($today['segments_today'] as $seg)
                                <span class="text-[11px] rounded-lg px-2 py-1 border {{ !empty($seg['is_current']) ? 'bg-violet-600 text-white border-violet-600' : 'bg-white border-slate-200 text-slate-700' }}">
                                    {{ $seg['start_label'] }}–{{ $seg['end_label'] }}: {{ $seg['channels_label'] }}
                                </span>
                            @endforeach
                        </div>
                    @endif
                @else
                    <p class="text-sm text-slate-600 mt-1">{{ $today['message'] ?? 'لا شيفت' }}</p>
                @endif
            </div>
        @endif

        <div class="mt-4 flex flex-wrap gap-2">
            <a href="{{ route('employee.sales-manager.team.show', $employee) }}" class="text-sm font-semibold text-sky-700">ملف الموظف</a>
            <a href="{{ route('employee.sales-manager.scorecard.show', $employee) }}" class="text-sm font-semibold text-teal-700">مركز الرقابة</a>
            <a href="{{ route('employee.sales-manager.attendance.employee', $employee) }}" class="text-sm font-semibold text-slate-600">الحضور</a>
        </div>
    </section>

    @if($board)
        @include('sales._shift_week_board', [
            'board' => $board,
            'navRoute' => 'employee.sales-manager.shifts.show',
            'navRouteParams' => ['employee' => $employee->id],
            'highlightUserId' => $employee->id,
            'title' => 'أسبوع '.$employee->name,
        ])
    @endif
</div>
@endsection
