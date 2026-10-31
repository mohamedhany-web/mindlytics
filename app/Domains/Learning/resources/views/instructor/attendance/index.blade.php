@extends('layouts.app')

@section('title', __('instructor.attendance_absence'))
@section('header', __('instructor.attendance_absence'))

@section('content')
<div class="space-y-6">
    <div class="rounded-2xl p-5 sm:p-6 bg-white border border-slate-200 shadow-sm">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">{{ __('instructor.attendance_absence') }}</h1>
                <p class="text-sm text-slate-500 mt-0.5">{{ __('instructor.attendance_manage_desc') }}</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="rounded-xl p-4 bg-white border border-slate-200 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-slate-500 uppercase">{{ __('instructor.lectures') }}</p>
                    <p class="text-2xl font-bold text-slate-800">{{ $stats['total_lectures'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-sky-100 flex items-center justify-center text-sky-600"><i class="fas fa-chalkboard-teacher"></i></div>
            </div>
        </div>
        <div class="rounded-xl p-4 bg-white border border-slate-200 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-slate-500 uppercase">{{ __('instructor.attendance_records') }}</p>
                    <p class="text-2xl font-bold text-slate-800">{{ $stats['total_attendance_records'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-violet-100 flex items-center justify-center text-violet-600"><i class="fas fa-clipboard-list"></i></div>
            </div>
        </div>
        <div class="rounded-xl p-4 bg-white border border-slate-200 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-slate-500 uppercase">{{ __('instructor.present') }}</p>
                    <p class="text-2xl font-bold text-slate-800">{{ $stats['present_count'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-emerald-100 flex items-center justify-center text-emerald-600"><i class="fas fa-check-circle"></i></div>
            </div>
        </div>
        <div class="rounded-xl p-4 bg-white border border-slate-200 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-slate-500 uppercase">{{ __('instructor.absent') }}</p>
                    <p class="text-2xl font-bold text-slate-800">{{ $stats['absent_count'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-amber-100 flex items-center justify-center text-amber-600"><i class="fas fa-times-circle"></i></div>
            </div>
        </div>
    </div>

    <div class="rounded-xl bg-white border border-slate-200 shadow-sm p-5">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div>
                <label for="course_id" class="block text-sm font-semibold text-slate-700 mb-1">{{ __('instructor.course') }}</label>
                <select name="course_id" id="course_id" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 text-slate-800">
                    <option value="">{{ __('instructor.all_courses') }}</option>
                    @foreach($courses as $course)
                        <option value="{{ $course->id }}" {{ request('course_id') == $course->id ? 'selected' : '' }}>{{ $course->title }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="status" class="block text-sm font-semibold text-slate-700 mb-1">{{ __('instructor.lecture_status') }}</label>
                <select name="status" id="status" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 text-slate-800">
                    <option value="">{{ __('instructor.all') }}</option>
                    <option value="scheduled" {{ request('status') == 'scheduled' ? 'selected' : '' }}>{{ __('instructor.scheduled_lecture') }}</option>
                    <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>{{ __('instructor.in_progress') }}</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>{{ __('instructor.completed') }}</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>{{ __('instructor.cancelled_lecture') }}</option>
                </select>
            </div>
            <div>
                <label for="date_from" class="block text-sm font-semibold text-slate-700 mb-1">{{ __('instructor.date_from') }}</label>
                <input type="date" name="date_from" id="date_from" value="{{ request('date_from') }}" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 text-slate-800">
            </div>
            <div>
                <label for="date_to" class="block text-sm font-semibold text-slate-700 mb-1">{{ __('instructor.date_to') }}</label>
                <input type="date" name="date_to" id="date_to" value="{{ request('date_to') }}" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 text-slate-800">
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="px-4 py-2.5 bg-sky-500 hover:bg-sky-600 text-white rounded-xl font-semibold transition-colors">
                    <i class="fas fa-search ml-1"></i> {{ __('common.search') }}
                </button>
                @if(request()->anyFilled(['course_id', 'status', 'date_from', 'date_to']))
                    <a href="{{ route('instructor.attendance.index') }}" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-semibold transition-colors">
                        <i class="fas fa-times"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    @if($lectures->count() > 0)
        <div class="space-y-4">
            @foreach($lectures as $lecture)
                <div class="rounded-xl bg-white border border-slate-200 shadow-sm hover:border-sky-300 hover:shadow-md transition-all p-5">
                    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                        <div class="flex-1 min-w-0">
                            <div class="flex flex-wrap items-center gap-2 mb-2">
                                <h3 class="text-lg font-bold text-slate-800">{{ $lecture->title }}</h3>
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-semibold
                                    @if($lecture->status == 'scheduled') bg-sky-100 text-sky-700
                                    @elseif($lecture->status == 'in_progress') bg-amber-100 text-amber-700
                                    @elseif($lecture->status == 'completed') bg-emerald-100 text-emerald-700
                                    @else bg-slate-100 text-slate-600
                                    @endif">
                                    @if($lecture->status == 'scheduled') {{ __('instructor.scheduled_lecture') }}
                                    @elseif($lecture->status == 'in_progress') {{ __('instructor.in_progress') }}
                                    @elseif($lecture->status == 'completed') {{ __('instructor.completed') }}
                                    @else {{ __('instructor.cancelled_lecture') }}
                                    @endif
                                </span>
                            </div>
                            <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-sm text-slate-500">
                                <span><i class="fas fa-book text-sky-500 ml-1"></i> {{ $lecture->course->title ?? '—' }}</span>
                                <span><i class="fas fa-calendar text-slate-400 ml-1"></i> {{ $lecture->scheduled_at ? $lecture->scheduled_at->format('Y/m/d H:i') : '—' }}</span>
                                @if($lecture->attendance_records_count > 0)
                                    <span>{{ $lecture->attendance_records_count }} {{ __('instructor.attendance_record_single') }}</span>
                                @endif
                            </div>
                        </div>
                        <div class="flex items-center gap-2 shrink-0">
                            <a href="{{ route('instructor.attendance.lecture', $lecture) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-sky-500 hover:bg-sky-600 text-white rounded-xl font-semibold text-sm transition-colors">
                                <i class="fas fa-eye"></i> {{ __('instructor.view_attendance') }}
                            </a>
                            <a href="{{ route('instructor.lectures.show', $lecture) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-semibold text-sm transition-colors">
                                <i class="fas fa-info-circle"></i>
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="flex justify-center">
            <div class="rounded-xl p-3 bg-white border border-slate-200 shadow-sm">{{ $lectures->links() }}</div>
        </div>
    @else
        <div class="rounded-xl border border-dashed border-slate-200 bg-slate-50/50 py-12 text-center">
            <div class="w-16 h-16 rounded-2xl bg-sky-100 flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-clipboard-check text-2xl text-sky-500"></i>
            </div>
            <h3 class="text-lg font-bold text-slate-800 mb-2">{{ __('instructor.no_lectures') }}</h3>
            <p class="text-sm text-slate-500">{{ __('instructor.no_lectures_yet') }}</p>
        </div>
    @endif
</div>
@endsection
