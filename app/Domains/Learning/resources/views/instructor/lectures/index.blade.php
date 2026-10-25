@extends('layouts.app')

@section('title', __('instructor.lectures') . ' - Mindlytics')
@section('header', __('instructor.lectures'))

@section('content')
<div class="space-y-6">
    <!-- الهيدر -->
    <div class="rounded-2xl p-5 sm:p-6 bg-white border border-slate-200 shadow-sm">
        <h1 class="text-2xl sm:text-3xl font-bold text-slate-800 mb-1">{{ __('instructor.lectures') }}</h1>
        <p class="text-sm text-slate-500">{{ __('instructor.manage_lectures_curriculum') }}</p>
    </div>

    <!-- الإحصائيات -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
        <div class="rounded-2xl p-5 bg-white border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1">{{ __('instructor.total_courses') }}</p>
                <p class="text-2xl sm:text-3xl font-bold text-slate-800">{{ $stats['total_courses'] ?? 0 }}</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-sky-50 flex items-center justify-center">
                <i class="fas fa-book text-sky-600 text-lg"></i>
            </div>
        </div>
        <div class="rounded-2xl p-5 bg-white border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1">{{ __('instructor.active_courses') }}</p>
                <p class="text-2xl sm:text-3xl font-bold text-slate-800">{{ $stats['active_courses'] ?? 0 }}</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-50 flex items-center justify-center">
                <i class="fas fa-check-circle text-emerald-600 text-lg"></i>
            </div>
        </div>
        <div class="rounded-2xl p-5 bg-white border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1">{{ __('instructor.total_lectures') }}</p>
                <p class="text-2xl sm:text-3xl font-bold text-slate-800">{{ $stats['total_lectures'] ?? 0 }}</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-violet-50 flex items-center justify-center">
                <i class="fas fa-chalkboard-teacher text-violet-600 text-lg"></i>
            </div>
        </div>
        <div class="rounded-2xl p-5 bg-white border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1">{{ __('instructor.scheduled_lectures') }}</p>
                <p class="text-2xl sm:text-3xl font-bold text-slate-800">{{ $stats['scheduled_lectures'] ?? 0 }}</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-amber-50 flex items-center justify-center">
                <i class="fas fa-calendar-alt text-amber-600 text-lg"></i>
            </div>
        </div>
        <div class="rounded-2xl p-5 bg-white border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1">{{ __('instructor.total_students') }}</p>
                <p class="text-2xl sm:text-3xl font-bold text-slate-800">{{ $stats['total_students'] ?? 0 }}</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-violet-50 flex items-center justify-center">
                <i class="fas fa-users text-violet-600 text-lg"></i>
            </div>
        </div>
    </div>

    <!-- الفلاتر -->
    <div class="bg-white rounded-2xl p-5 sm:p-6 border border-slate-200 shadow-sm">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label for="search" class="block text-sm font-semibold text-slate-700 mb-2">{{ __('common.search') }}</label>
                <input type="text" name="search" id="search" value="{{ request('search') }}" 
                       placeholder="{{ __('instructor.search_in_courses') }}"
                       class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-slate-800 focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 transition-colors">
            </div>
            <div>
                <label for="status" class="block text-sm font-semibold text-slate-700 mb-2">{{ __('common.status') }}</label>
                <select name="status" id="status" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-slate-800 focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 transition-colors">
                    <option value="">{{ __('instructor.all_statuses') }}</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>{{ __('instructor.active_status') }}</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>{{ __('instructor.not_active') }}</option>
                </select>
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 inline-flex items-center justify-center gap-2 bg-sky-500 hover:bg-sky-600 text-white px-5 py-2.5 rounded-xl font-semibold transition-colors">
                    <i class="fas fa-search"></i>
                    <span class="mr-2">{{ __('common.search') }}</span>
                </button>
                @if(request()->anyFilled(['search', 'status']))
                    <a href="{{ route('instructor.lectures.index') }}" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-semibold transition-colors inline-flex items-center justify-center">
                        <i class="fas fa-times"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- قائمة الكورسات -->
    @if($courses->count() > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4 max-w-6xl">
            @foreach($courses as $course)
            <div class="rounded-xl overflow-hidden bg-white border border-slate-200 shadow-sm hover:shadow-md transition-shadow flex flex-col">
                <div class="p-4 border-b border-slate-200">
                    <div class="flex items-center justify-between gap-2 flex-wrap">
                        <h3 class="text-base font-bold text-slate-800 truncate flex-1 min-w-0">{{ $course->title }}</h3>
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-xs font-semibold shrink-0 {{ $course->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">
                            @if($course->is_active)<i class="fas fa-check-circle"></i> {{ __('instructor.active_status') }}
                            @else<i class="fas fa-times-circle"></i> {{ __('instructor.not_active') }}
                            @endif
                        </span>
                    </div>
                    @if($course->academicYear)
                        <p class="text-xs text-slate-500 mt-1">{{ $course->academicYear->name }}</p>
                    @endif
                </div>

                <div class="p-4 flex-1">
                    <div class="grid grid-cols-2 gap-x-3 gap-y-1.5 text-xs">
                        <div class="flex items-center gap-1.5">
                            <i class="fas fa-book-open text-violet-500 w-4 text-center"></i>
                            <span class="text-slate-500">{{ __('instructor.sections_label') }}:</span>
                            <span class="text-slate-800 font-medium">{{ $course->sections_count ?? 0 }}</span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <i class="fas fa-chalkboard-teacher text-amber-500 w-4 text-center"></i>
                            <span class="text-slate-500">{{ __('instructor.lectures_label') }}:</span>
                            <span class="text-slate-800 font-medium">{{ $course->lectures_count ?? 0 }}</span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <i class="fas fa-users text-emerald-500 w-4 text-center"></i>
                            <span class="text-slate-500">{{ __('instructor.students_label') }}:</span>
                            <span class="text-slate-800 font-medium">{{ $course->enrollments_count ?? 0 }}</span>
                        </div>
                    </div>
                </div>

                <div class="p-4 pt-0 flex flex-col gap-2">
                    <a href="{{ route('instructor.courses.curriculum', $course) }}" 
                       class="w-full inline-flex items-center justify-center gap-2 bg-sky-500 hover:bg-sky-600 text-white px-3 py-2 rounded-lg text-sm font-semibold transition-colors">
                        <i class="fas fa-sitemap"></i>
<span>{{ __('instructor.build_curriculum') }}</span>
                    </a>
                    <a href="{{ route('instructor.courses.show', $course) }}"
                       class="w-full inline-flex items-center justify-center gap-2 bg-slate-100 hover:bg-slate-200 text-slate-700 px-3 py-2 rounded-lg text-sm font-semibold transition-colors">
                        <i class="fas fa-eye"></i>
                        <span>{{ __('instructor.view_details') }}</span>
                    </a>
                </div>
            </div>
            @endforeach
        </div>

        <div class="mt-6 flex justify-center">
            <div class="bg-white rounded-xl p-3 border border-slate-200 shadow-sm">
                {{ $courses->links() }}
            </div>
        </div>
    @else
        <div class="rounded-2xl p-12 sm:p-16 text-center bg-white border border-slate-200 shadow-sm">
            <div class="w-24 h-24 rounded-2xl bg-sky-50 flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-chalkboard-teacher text-4xl text-sky-500"></i>
            </div>
            <h3 class="text-xl sm:text-2xl font-bold text-slate-800 mb-2">{{ __('instructor.no_courses') }}</h3>
            <p class="text-slate-500 max-w-md mx-auto">{{ __('instructor.no_courses_lectures_desc') }}</p>
        </div>
    @endif
</div>
@endsection
