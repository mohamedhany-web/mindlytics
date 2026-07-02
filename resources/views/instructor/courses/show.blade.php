@extends('layouts.app')

@section('title', __('instructor.course_details') . ' - ' . $course->title)
@section('header', __('instructor.course_details'))

@push('styles')
<style>
    [x-cloak] { display: none !important; }
    .tab-button { transition: all 0.2s; position: relative; }
    .tab-button.active { color: rgb(14 165 233); font-weight: 700; }
    .tab-button.active::after {
        content: ''; position: absolute; bottom: -1px; right: 0; left: 0; height: 2px;
        background: rgb(14 165 233); border-radius: 2px 2px 0 0;
    }
    .content-card {
        background: #fff; border: 1px solid rgb(226 232 240); border-radius: 1rem;
        transition: box-shadow 0.2s, border-color 0.2s;
    }
    .content-card:hover { box-shadow: 0 4px 12px rgba(0,0,0,0.06); border-color: rgb(148 163 184); }
    .stats-mini-card {
        background: #fff; border: 1px solid rgb(226 232 240); border-radius: 1rem;
        transition: box-shadow 0.2s, border-color 0.2s;
    }
    .stats-mini-card:hover { box-shadow: 0 4px 12px rgba(0,0,0,0.06); border-color: rgb(148 163 184); }
    .item-row { transition: background 0.2s; }
    .item-row:hover { background: rgb(241 245 249); }
</style>
@endpush

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6" x-data="{ activeTab: 'overview' }">
    <div class="space-y-6">
    <!-- الهيدر -->
    <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-5 sm:p-6">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div class="flex-1 min-w-0">
                <nav class="text-sm text-slate-500 mb-2">
                    <a href="{{ route('instructor.courses.index') }}" class="hover:text-sky-600 transition-colors">{{ __('instructor.my_courses') }}</a>
                    <span class="mx-2">/</span>
                    <span class="text-slate-700 font-semibold truncate block sm:inline">{{ $course->title }}</span>
                </nav>
                <h1 class="text-xl sm:text-2xl font-bold text-slate-800 mb-3">{{ $course->title }}</h1>
                <div class="flex flex-wrap items-center gap-2">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold {{ $course->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' }}">
                        <i class="fas {{ $course->is_active ? 'fa-check-circle' : 'fa-ban' }}"></i>
                        {{ $course->is_active ? __('instructor.active_status') : __('instructor.inactive_status') }}
                    </span>
                    @if($course->is_scholarship_only)
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold bg-violet-100 text-violet-700">
                            <i class="fas fa-graduation-cap"></i> كورس منحة
                        </span>
                    @endif
                    @if($course->is_featured)
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold bg-amber-100 text-amber-800">
                            <i class="fas fa-star"></i> {{ __('instructor.featured') }}
                        </span>
                    @endif
                    @if($course->academicYear)
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold bg-sky-100 text-sky-700">
                            <i class="fas fa-graduation-cap"></i> {{ $course->academicYear->name }}
                        </span>
                    @endif
                    @if($course->academicSubject)
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold bg-violet-100 text-violet-700">
                            <i class="fas fa-book"></i> {{ $course->academicSubject->name }}
                        </span>
                    @endif
                </div>
            </div>
            <div class="flex flex-wrap items-center gap-2 shrink-0">
                @if($course->is_scholarship_only && $course->scholarshipProgram)
                    <a href="{{ route('instructor.scholarships.show', $course->scholarshipProgram) }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-violet-600 hover:bg-violet-700 text-white rounded-xl font-semibold transition-colors">
                        <i class="fas fa-user-graduate"></i>
                        <span>طلاب المنحة</span>
                    </a>
                @endif
                <a href="{{ route('instructor.courses.mind-map.edit', $course) }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-emerald-50 to-sky-50 border border-emerald-200 text-emerald-800 hover:border-emerald-300 rounded-xl font-semibold transition-colors">
                    <i class="fas fa-route"></i>
                    <span>{{ __('instructor.mind_map_short') }}</span>
                </a>
                <a href="{{ route('instructor.courses.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-semibold transition-colors">
                    <i class="fas fa-arrow-right"></i>
                    <span>{{ __('instructor.back') }}</span>
                </a>
            </div>
        </div>
    </div>

    <!-- الإحصائيات -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3">
        <div class="stats-mini-card rounded-xl p-4 text-center">
            <div class="w-10 h-10 rounded-xl bg-sky-100 text-sky-600 flex items-center justify-center mx-auto mb-2">
                <i class="fas fa-chalkboard-teacher text-sm"></i>
            </div>
            <div class="text-xl font-bold text-slate-800">{{ $stats['total_lectures'] }}</div>
            <div class="text-xs text-slate-600 font-medium mt-1">{{ __('instructor.lecture_single') }}</div>
        </div>
        <div class="stats-mini-card rounded-xl p-4 text-center">
            <div class="w-10 h-10 rounded-xl bg-violet-100 text-violet-600 flex items-center justify-center mx-auto mb-2">
                <i class="fas fa-clipboard-check text-sm"></i>
            </div>
            <div class="text-xl font-bold text-slate-800">{{ $stats['total_exams'] }}</div>
            <div class="text-xs text-slate-600 font-medium mt-1">{{ __('instructor.exam_single') }}</div>
        </div>
        <div class="stats-mini-card rounded-xl p-4 text-center">
            <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center mx-auto mb-2">
                <i class="fas fa-tasks text-sm"></i>
            </div>
            <div class="text-xl font-bold text-slate-800">{{ $stats['total_assignments'] }}</div>
            <div class="text-xs text-slate-600 font-medium mt-1">{{ __('instructor.assignment_single') }}</div>
        </div>
        <div class="stats-mini-card rounded-xl p-4 text-center">
            <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center mx-auto mb-2">
                <i class="fas fa-user-graduate text-sm"></i>
            </div>
            <div class="text-xl font-bold text-slate-800">{{ $stats['total_students'] }}</div>
            <div class="text-xs text-slate-600 font-medium mt-1">{{ __('instructor.student_single') }}</div>
        </div>
        <div class="stats-mini-card rounded-xl p-4 text-center">
            <div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center mx-auto mb-2">
                <i class="fas fa-users text-sm"></i>
            </div>
            <div class="text-xl font-bold text-slate-800">{{ $stats['total_groups'] }}</div>
            <div class="text-xs text-slate-600 font-medium mt-1">{{ __('instructor.group_single') }}</div>
        </div>
    </div>

    <!-- التبويبات -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="border-b border-slate-200 bg-slate-50/50">
            <div class="flex flex-wrap items-center gap-1 sm:gap-2 px-3 sm:px-4 py-2 overflow-x-auto">
                <button @click="activeTab = 'overview'" 
                        :class="activeTab === 'overview' ? 'tab-button active' : 'tab-button'"
                        class="px-3 py-2.5 text-sm font-medium text-slate-600 hover:text-sky-600 transition-colors rounded-lg hover:bg-white">
                    <i class="fas fa-chart-line ml-2"></i> {{ __('instructor.overview') }}
                </button>
                <button @click="activeTab = 'lectures'" 
                        :class="activeTab === 'lectures' ? 'tab-button active' : 'tab-button'"
                        class="px-3 py-2.5 text-sm font-medium text-slate-600 hover:text-sky-600 transition-colors rounded-lg hover:bg-white relative">
                    <i class="fas fa-chalkboard-teacher ml-2"></i> {{ __('instructor.lectures') }}
                    @if($stats['upcoming_lectures'] > 0)
                        <span class="absolute top-1 right-1 w-4 h-4 bg-red-500 text-white rounded-full text-[10px] flex items-center justify-center font-bold">{{ $stats['upcoming_lectures'] }}</span>
                    @endif
                </button>
                <button @click="activeTab = 'exams'" 
                        :class="activeTab === 'exams' ? 'tab-button active' : 'tab-button'"
                        class="px-3 py-2.5 text-sm font-medium text-slate-600 hover:text-sky-600 transition-colors rounded-lg hover:bg-white">
                    <i class="fas fa-clipboard-check ml-2"></i> {{ __('instructor.exams') }}
                </button>
                <button @click="activeTab = 'assignments'" 
                        :class="activeTab === 'assignments' ? 'tab-button active' : 'tab-button'"
                        class="px-3 py-2.5 text-sm font-medium text-slate-600 hover:text-sky-600 transition-colors rounded-lg hover:bg-white relative">
                    <i class="fas fa-tasks ml-2"></i> {{ __('instructor.assignments') }}
                    @if($stats['pending_submissions'] > 0)
                        <span class="absolute top-1 right-1 w-4 h-4 bg-red-500 text-white rounded-full text-[10px] flex items-center justify-center font-bold">{{ $stats['pending_submissions'] }}</span>
                    @endif
                </button>
                <button @click="activeTab = 'students'" 
                        :class="activeTab === 'students' ? 'tab-button active' : 'tab-button'"
                        class="px-3 py-2.5 text-sm font-medium text-slate-600 hover:text-sky-600 transition-colors rounded-lg hover:bg-white">
                    <i class="fas fa-user-graduate ml-2"></i> {{ __('instructor.students') }}
                </button>
                <button @click="activeTab = 'groups'" 
                        :class="activeTab === 'groups' ? 'tab-button active' : 'tab-button'"
                        class="px-3 py-2.5 text-sm font-medium text-slate-600 hover:text-sky-600 transition-colors rounded-lg hover:bg-white">
                    <i class="fas fa-users ml-2"></i> {{ __('instructor.groups') }}
                </button>
                <button @click="activeTab = 'attendance'" 
                        :class="activeTab === 'attendance' ? 'tab-button active' : 'tab-button'"
                        class="px-3 py-2.5 text-sm font-medium text-slate-600 hover:text-sky-600 transition-colors rounded-lg hover:bg-white">
                    <i class="fas fa-clipboard-list ml-2"></i> {{ __('instructor.attendance') }}
                </button>
            </div>
        </div>

        <!-- محتوى التبويبات -->
        <div class="p-4 sm:p-6">
            <!-- تبويب نظرة عامة -->
            <div x-show="activeTab === 'overview'" x-transition>
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- معلومات الكورس -->
                    <div class="lg:col-span-2 space-y-6">
                        @if($course->thumbnail)
                            <div class="content-card rounded-xl overflow-hidden">
                                <img src="{{ asset('storage/' . $course->thumbnail) }}" alt="{{ $course->title }}" 
                                     class="w-full h-64 object-cover">
                            </div>
                            @endif

                        <div class="content-card rounded-xl p-5 sm:p-6">
                            <h3 class="text-lg font-bold text-slate-800 mb-4 flex items-center gap-2">
                                <i class="fas fa-info-circle text-sky-500"></i>
                                {{ __('instructor.course_info') }}
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <div class="mb-3">
                                        <label class="block text-xs font-bold text-slate-600 mb-1 uppercase tracking-wide">{{ __('instructor.title') }}</label>
                                        <div class="font-black text-slate-800 text-base">{{ $course->title }}</div>
                            </div>
                            @if($course->instructor)
                                    <div class="mb-3">
                                        <label class="block text-xs font-bold text-slate-600 mb-1 uppercase tracking-wide">{{ __('instructor.instructor_label') }}</label>
                                        <div class="text-slate-800 font-bold">{{ $course->instructor->name }}</div>
                            </div>
                            @endif
                                    @if($course->level)
                                    <div class="mb-3">
                                        <label class="block text-xs font-bold text-slate-600 mb-1 uppercase tracking-wide">{{ __('instructor.level') }}</label>
                                        <div class="text-slate-800 font-bold">
                                    @if($course->level == 'beginner') {{ __('instructor.beginner') }}
                                    @elseif($course->level == 'intermediate') {{ __('instructor.intermediate') }}
                                    @elseif($course->level == 'advanced') {{ __('instructor.advanced') }}
                                    @else {{ __('instructor.level_unspecified') }}
                                    @endif
                                </div>
                            </div>
                                    @endif
                                </div>
                                <div>
                                    <div class="mb-3">
                                        <label class="block text-xs font-bold text-slate-600 mb-1 uppercase tracking-wide">{{ __('instructor.price') }}</label>
                                        <div class="text-slate-800 font-black text-lg">
                                            @if($course->price && $course->price > 0)
                                                {{ number_format($course->price, 2) }} ج.م
                                            @else
                                                <span class="text-green-600">{{ __('instructor.free') }}</span>
                                    @endif
                                </div>
                            </div>
                                    <div class="mb-3">
                                        <label class="block text-xs font-bold text-slate-600 mb-1 uppercase tracking-wide">{{ __('instructor.course_duration') }}</label>
                                        <div class="text-slate-800 font-bold">
                                    {{ $course->duration_hours ?? 0 }} {{ __('instructor.hour') }}
                                    @if($course->duration_minutes && $course->duration_minutes > 0)
                                        {{ __('instructor.and') }} {{ $course->duration_minutes }} {{ __('instructor.minutes') }}
                                    @endif
                                </div>
                            </div>
                            @if($course->programming_language)
                                    <div class="mb-3">
                                        <label class="block text-xs font-bold text-slate-600 mb-1 uppercase tracking-wide">{{ __('instructor.programming_language') }}</label>
                                        <div class="text-slate-800 font-bold">{{ $course->programming_language }}</div>
                            </div>
                            @endif
                        </div>
                    </div>

                    @if($course->description)
                                <div class="mt-4 pt-4 border-t-2 border-slate-200">
                                    <label class="block text-xs font-bold text-slate-600 mb-2 uppercase tracking-wide">{{ __('instructor.description') }}</label>
                                    <div class="text-slate-800 font-medium bg-slate-50 p-4 rounded-xl border border-slate-200">
                                {{ $course->description }}
                            </div>
                        </div>
                    @endif

                    @if($course->objectives)
                                <div class="mt-4 pt-4 border-t-2 border-slate-200">
                                    <label class="block text-xs font-bold text-slate-600 mb-2 uppercase tracking-wide">{{ __('instructor.objectives') }}</label>
                                    <div class="text-slate-800 font-medium bg-slate-50 p-4 rounded-xl border border-slate-200">
                                {{ $course->objectives }}
                            </div>
                        </div>
                    @endif
                        </div>
                    </div>

                    <!-- الإجراءات السريعة -->
                    <div class="space-y-4">
                        <div class="content-card rounded-xl p-5">
                            <h3 class="text-lg font-black text-slate-800 mb-4 flex items-center gap-2">
                                <i class="fas fa-bolt text-sky-600"></i>
                                {{ __('instructor.quick_actions') }}
                            </h3>
                            <div class="space-y-2">
                                <a href="{{ route('instructor.lectures.create', ['course_id' => $course->id]) }}" 
                                   class="flex items-center gap-3 p-3 bg-sky-50 hover:bg-sky-100 rounded-xl border border-sky-200 transition-all">
                                    <div class="w-10 h-10 rounded-xl bg-sky-500 flex items-center justify-center text-white shadow-md">
                                        <i class="fas fa-video text-sm"></i>
                                    </div>
                                    <span class="font-bold text-slate-800 text-sm">{{ __('instructor.add_lecture') }}</span>
                                </a>
                                <a href="{{ route('instructor.exams.create', ['course_id' => $course->id]) }}" 
                                   class="flex items-center gap-3 p-3 bg-gradient-to-r from-indigo-500/10 to-purple-500/10 hover:from-indigo-500/20 hover:to-purple-500/20 rounded-xl border border-indigo-500/20 transition-all">
                                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white shadow-md">
                                        <i class="fas fa-clipboard-check text-sm"></i>
                                    </div>
                                    <span class="font-bold text-slate-800 text-sm">{{ __('instructor.create_exam') }}</span>
                                </a>
                                <a href="{{ route('instructor.assignments.create', ['course_id' => $course->id]) }}" 
                                   class="flex items-center gap-3 p-3 bg-amber-50 hover:bg-amber-100 rounded-xl border border-amber-200 transition-all">
                                    <div class="w-10 h-10 rounded-xl bg-amber-500 flex items-center justify-center text-white shadow-md">
                                        <i class="fas fa-tasks text-sm"></i>
                                    </div>
                                    <span class="font-bold text-slate-800 text-sm">{{ __('instructor.create_assignment') }}</span>
                                </a>
                                <a href="{{ route('instructor.groups.create', ['course_id' => $course->id]) }}" 
                                   class="flex items-center gap-3 p-3 bg-gradient-to-r from-green-500/10 to-emerald-500/10 hover:from-green-500/20 hover:to-emerald-500/20 rounded-xl border border-green-500/20 transition-all">
                                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-green-500 to-emerald-600 flex items-center justify-center text-white shadow-md">
                                        <i class="fas fa-users text-sm"></i>
                                    </div>
                                    <span class="font-bold text-slate-800 text-sm">{{ __('instructor.create_group') }}</span>
                                </a>
                            </div>
                        </div>

                        <!-- إحصائيات إضافية -->
                        <div class="content-card rounded-xl p-5">
                            <h3 class="text-lg font-black text-slate-800 mb-4 flex items-center gap-2">
                                <i class="fas fa-chart-bar text-sky-600"></i>
                                {{ __('instructor.statistics') }}
                            </h3>
                            <div class="space-y-3">
                                <div class="flex items-center justify-between p-2 bg-slate-50 rounded-lg">
                                    <span class="text-xs text-slate-600 font-semibold">{{ __('instructor.upcoming_lectures') }}</span>
                                    <span class="font-black text-slate-800">{{ $stats['upcoming_lectures'] }}</span>
                                </div>
                                <div class="flex items-center justify-between p-2 bg-gradient-to-r from-indigo-500/5 to-purple-500/5 rounded-lg">
                                    <span class="text-xs text-slate-600 font-semibold">{{ __('instructor.active_exams') }}</span>
                                    <span class="font-black text-slate-800">{{ $stats['active_exams'] }}</span>
                                </div>
                                <div class="flex items-center justify-between p-2 bg-amber-50 rounded-lg">
                                    <span class="text-xs text-slate-600 font-semibold">{{ __('instructor.pending_submissions') }}</span>
                                    <span class="font-black text-slate-800">{{ $stats['pending_submissions'] }}</span>
                                </div>
                                <div class="flex items-center justify-between p-2 bg-gradient-to-r from-blue-500/5 to-cyan-500/5 rounded-lg">
                                    <span class="text-xs text-slate-600 font-semibold">{{ __('instructor.attendance_records') }}</span>
                                    <span class="font-black text-slate-800">{{ $stats['total_attendance_records'] }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- تبويب المحاضرات -->
            <div x-show="activeTab === 'lectures'" x-transition style="display: none;">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg sm:text-xl font-black text-slate-800 flex items-center gap-2">
                        <i class="fas fa-chalkboard-teacher text-sky-600"></i>
                        {{ __('instructor.lectures') }} ({{ $lectures->total() }})
                    </h3>
                    <a href="{{ route('instructor.lectures.create', ['course_id' => $course->id]) }}" 
                       class="inline-flex items-center gap-2 bg-sky-500 hover:bg-sky-600 text-white px-5 py-2.5 rounded-xl font-bold shadow-lg shadow-sky-500/25 hover:shadow-xl transition-all duration-300 transform hover:scale-105">
                        <i class="fas fa-plus"></i>
                        <span>{{ __('instructor.add_lecture') }}</span>
                    </a>
                </div>
                @if($lectures->count() > 0)
                    <div class="space-y-3">
                        @foreach($lectures as $lecture)
                        <div class="item-row flex items-center justify-between p-4 bg-slate-50 rounded-xl border border-slate-200">
                            <div class="flex items-center gap-4 flex-1">
                                <div class="w-12 h-12 rounded-xl bg-sky-500 flex items-center justify-center text-white shadow-md">
                                    <i class="fas fa-video"></i>
                                </div>
                                <div class="flex-1">
                                    <div class="font-black text-slate-800 mb-1">{{ $lecture->title }}</div>
                                    <div class="text-sm text-slate-600 font-medium">
                                        <i class="fas fa-calendar-alt text-sky-600 ml-1"></i>
                                        {{ $lecture->scheduled_at->format('Y/m/d H:i') }}
                                        @if($lecture->lesson)
                                            <span class="mr-2">-</span>
                                            <i class="fas fa-book text-purple-600 ml-1"></i>
                                            {{ $lecture->lesson->title }}
                                    @endif
                                    </div>
                                </div>
                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold shadow-md
                                    @if($lecture->status == 'scheduled') bg-gradient-to-r from-blue-500 to-indigo-600 text-white
                                    @elseif($lecture->status == 'in_progress') bg-amber-500 text-white
                                    @elseif($lecture->status == 'completed') bg-gradient-to-r from-green-500 to-emerald-600 text-white
                                    @else bg-gradient-to-r from-red-500 to-rose-600 text-white
                                    @endif">
                                    @if($lecture->status == 'scheduled') {{ __('instructor.scheduled_lecture') }}
                                    @elseif($lecture->status == 'in_progress') {{ __('instructor.in_progress') }}
                                    @elseif($lecture->status == 'completed') {{ __('instructor.completed') }}
                                    @else {{ __('instructor.cancelled_lecture') }}
                                    @endif
                                </span>
                            </div>
                            <div class="flex items-center gap-2">
                                <a href="{{ route('instructor.lectures.show', $lecture) }}" 
                                   class="px-3 py-2 bg-sky-500 hover:bg-sky-600 text-white rounded-xl font-bold transition-all duration-300 transform hover:scale-105">
                                    <i class="fas fa-eye text-xs"></i>
                                </a>
                                <a href="{{ route('instructor.lectures.edit', $lecture) }}" 
                                   class="px-3 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-xl font-bold transition-all duration-300 transform hover:scale-105">
                                    <i class="fas fa-edit text-xs"></i>
                                </a>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <div class="mt-4">
                        {{ $lectures->links() }}
                    </div>
                @else
                    <div class="text-center py-12">
                        <div class="w-24 h-24 bg-gradient-to-br bg-sky-50 rounded-2xl flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-chalkboard-teacher text-4xl text-sky-600"></i>
                </div>
                        <p class="text-lg font-black text-slate-800 mb-2">{{ __('instructor.no_lectures') }}</p>
                        <a href="{{ route('instructor.lectures.create', ['course_id' => $course->id]) }}" 
                           class="inline-flex items-center gap-2 mt-4 px-6 py-3 bg-sky-500 hover:bg-sky-600 text-white font-bold rounded-xl shadow-lg shadow-sky-500/25 hover:shadow-xl transition-all duration-300 transform hover:scale-105">
                            <i class="fas fa-plus"></i>
                            {{ __('instructor.add_new_lecture') }}
                        </a>
            </div>
            @endif
        </div>

            <!-- تبويب الاختبارات -->
            <div x-show="activeTab === 'exams'" x-transition style="display: none;">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg sm:text-xl font-black text-slate-800 flex items-center gap-2">
                        <i class="fas fa-clipboard-check text-indigo-600"></i>
                        {{ __('instructor.exams') }} ({{ $exams->total() }})
                    </h3>
                    <a href="{{ route('instructor.exams.create') }}" 
                       class="inline-flex items-center gap-2 bg-gradient-to-r from-indigo-500 to-purple-600 hover:from-indigo-600 hover:to-purple-700 text-white px-5 py-2.5 rounded-xl font-bold shadow-lg shadow-indigo-500/30 hover:shadow-xl transition-all duration-300 transform hover:scale-105">
                        <i class="fas fa-plus"></i>
                        <span>{{ __('instructor.create_exam') }}</span>
                    </a>
                </div>
                @if($exams->count() > 0)
                    <div class="space-y-3">
                        @foreach($exams as $exam)
                        <div class="item-row flex items-center justify-between p-4 bg-gradient-to-r from-indigo-500/5 to-purple-500/5 rounded-xl border border-indigo-500/10">
                            <div class="flex items-center gap-4 flex-1">
                                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white shadow-md">
                                    <i class="fas fa-clipboard-check"></i>
                                </div>
                                <div class="flex-1">
                                    <div class="font-black text-slate-800 mb-1">{{ $exam->title }}</div>
                                    <div class="text-sm text-slate-600 font-medium">
                                        <i class="fas fa-clock text-indigo-600 ml-1"></i>
                                        {{ $exam->duration_minutes }} {{ __('instructor.minutes') }}
                                        <span class="mr-2">-</span>
                                        <i class="fas fa-question-circle text-purple-600 ml-1"></i>
                                        {{ $exam->questions_count }} {{ __('instructor.question_single') }}
                                        @if($exam->lesson)
                                            <span class="mr-2">-</span>
                                            <i class="fas fa-book text-blue-600 ml-1"></i>
                                            {{ $exam->lesson->title }}
                                        @endif
                                    </div>
                                </div>
                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold shadow-md {{ $exam->is_active ? 'bg-gradient-to-r from-green-500 to-emerald-600 text-white' : 'bg-amber-500 text-white' }}">
                                    <i class="fas {{ $exam->is_active ? 'fa-check-circle' : 'fa-ban' }}"></i>
                                    {{ $exam->is_active ? __('instructor.active_status') : __('instructor.inactive_status') }}
                                </span>
                            </div>
                            <a href="{{ route('instructor.exams.show', $exam) }}" 
                               class="px-3 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold transition-all duration-300 transform hover:scale-105">
                                <i class="fas fa-eye text-xs"></i>
                            </a>
                        </div>
                        @endforeach
                    </div>
                    <div class="mt-4">
                        {{ $exams->links() }}
                    </div>
                @else
                    <div class="text-center py-12">
                        <div class="w-24 h-24 bg-gradient-to-br from-indigo-500/10 to-purple-500/10 rounded-2xl flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-clipboard-check text-4xl text-indigo-600"></i>
                        </div>
                        <p class="text-lg font-black text-slate-800 mb-2">{{ __('instructor.no_exams') }}</p>
                        <a href="{{ route('instructor.exams.create') }}" 
                           class="inline-flex items-center gap-2 mt-4 px-6 py-3 bg-gradient-to-r from-indigo-500 to-purple-600 hover:from-indigo-600 hover:to-purple-700 text-white font-bold rounded-xl shadow-lg shadow-indigo-500/30 hover:shadow-xl transition-all duration-300 transform hover:scale-105">
                            <i class="fas fa-plus"></i>
                            {{ __('instructor.create_new_exam') }}
                        </a>
                    </div>
                    @endif
            </div>

            <!-- تبويب الواجبات -->
            <div x-show="activeTab === 'assignments'" x-transition style="display: none;">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg sm:text-xl font-black text-slate-800 flex items-center gap-2">
                        <i class="fas fa-tasks text-amber-500"></i>
                        {{ __('instructor.assignments') }} ({{ $assignments->total() }})
                    </h3>
                    <a href="{{ route('instructor.assignments.create') }}" 
                       class="inline-flex items-center gap-2 bg-amber-500 hover:bg-amber-600 text-white px-5 py-2.5 rounded-xl font-bold shadow-lg shadow-amber-500/25 hover:shadow-xl transition-all duration-300 transform hover:scale-105">
                        <i class="fas fa-plus"></i>
                        <span>{{ __('instructor.create_assignment') }}</span>
                    </a>
                </div>
                @if($assignments->count() > 0)
                    <div class="space-y-3">
                        @foreach($assignments as $assignment)
                        <div class="item-row flex items-center justify-between p-4 bg-amber-50 rounded-xl border border-amber-100">
                            <div class="flex items-center gap-4 flex-1">
                                <div class="w-12 h-12 rounded-xl bg-amber-500 flex items-center justify-center text-white shadow-md">
                                    <i class="fas fa-tasks"></i>
                                </div>
                                <div class="flex-1">
                                    <div class="font-black text-slate-800 mb-1">{{ $assignment->title }}</div>
                                    <div class="text-sm text-slate-600 font-medium">
                                        <i class="fas fa-file-upload text-amber-500 ml-1"></i>
                                        {{ $assignment->submissions_count }} {{ __('instructor.submission_single') }}
                                        @if($assignment->due_date)
                                            <span class="mr-2">-</span>
                                            <i class="fas fa-calendar-alt text-red-600 ml-1"></i>
                                            {{ $assignment->due_date->format('Y/m/d') }}
                                        @endif
                                    </div>
                                </div>
                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold shadow-md
                                    @if($assignment->status == 'published') bg-gradient-to-r from-green-500 to-emerald-600 text-white
                                    @elseif($assignment->status == 'draft') bg-amber-500 text-white
                                    @else bg-gradient-to-r from-gray-500 to-gray-600 text-white
                                    @endif">
                                    @if($assignment->status == 'published') {{ __('instructor.published') }}
                                    @elseif($assignment->status == 'draft') {{ __('instructor.draft') }}
                                    @else {{ __('instructor.archived') }}
                                    @endif
                                </span>
                            </div>
                            <div class="flex items-center gap-2">
                                <a href="{{ route('instructor.assignments.show', $assignment) }}" 
                                   class="px-3 py-2 bg-amber-500 hover:bg-amber-600 text-white rounded-xl font-bold transition-all duration-300 transform hover:scale-105">
                                    <i class="fas fa-eye text-xs"></i>
                                </a>
                                <a href="{{ route('instructor.assignments.submissions', $assignment) }}" 
                                   class="px-3 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-xl font-bold transition-all duration-300 transform hover:scale-105">
                                    <i class="fas fa-list text-xs"></i>
                                </a>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <div class="mt-4">
                        {{ $assignments->links() }}
                    </div>
                @else
                    <div class="text-center py-12">
                        <div class="w-24 h-24 bg-gradient-to-br bg-amber-50 rounded-2xl flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-tasks text-4xl text-amber-500"></i>
                        </div>
                        <p class="text-lg font-black text-slate-800 mb-2">{{ __('instructor.no_assignments') }}</p>
                        <a href="{{ route('instructor.assignments.create') }}" 
                           class="inline-flex items-center gap-2 mt-4 px-6 py-3 bg-amber-500 hover:bg-amber-600 text-white font-bold rounded-xl shadow-lg shadow-amber-500/25 hover:shadow-xl transition-all duration-300 transform hover:scale-105">
                            <i class="fas fa-plus"></i>
                            {{ __('instructor.create_assignment_modal_title') }}
                        </a>
                    </div>
                    @endif
    </div>

            <!-- تبويب الطلاب -->
            <div x-show="activeTab === 'students'" x-transition style="display: none;">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg sm:text-xl font-black text-slate-800 flex items-center gap-2">
                        <i class="fas fa-user-graduate text-green-600"></i>
                        {{ __('instructor.enrolled_students') }} ({{ $enrollments->total() }})
                    </h3>
        </div>
            @if($enrollments->count() > 0)
                <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-sky-50">
                                <tr>
                                    <th class="px-4 py-3 text-right text-xs font-bold text-slate-800 uppercase tracking-wider">{{ __('instructor.name') }}</th>
                                    <th class="px-4 py-3 text-right text-xs font-bold text-slate-800 uppercase tracking-wider">{{ __('instructor.email') }}</th>
                                    <th class="px-4 py-3 text-right text-xs font-bold text-slate-800 uppercase tracking-wider">{{ __('instructor.phone') }}</th>
                                    <th class="px-4 py-3 text-right text-xs font-bold text-slate-800 uppercase tracking-wider">{{ __('instructor.registration_date') }}</th>
                                    <th class="px-4 py-3 text-right text-xs font-bold text-slate-800 uppercase tracking-wider">{{ __('common.status') }}</th>
                                    <th class="px-4 py-3 text-right text-xs font-bold text-slate-800 uppercase tracking-wider">{{ __('instructor.actions') }}</th>
                            </tr>
                        </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($enrollments as $enrollment)
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-xl bg-sky-500 flex items-center justify-center text-white font-black shadow-md">
                                                {{ mb_substr($enrollment->user->name ?? __('instructor.student_single'), 0, 1) }}
                                            </div>
                                            <div class="text-sm font-black text-slate-800">
                                        {{ $enrollment->user->name ?? __('instructor.not_specified') }}
                                            </div>
                                    </div>
                                </td>
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        <div class="text-sm text-slate-600 font-medium">
                                        {{ $enrollment->user->email ?? __('instructor.not_specified') }}
                                    </div>
                                </td>
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        <div class="text-sm text-slate-600 font-medium">
                                        {{ $enrollment->user->phone ?? __('instructor.not_specified') }}
                                    </div>
                                </td>
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        <div class="text-sm text-slate-600 font-medium">
                                        {{ $enrollment->created_at->format('Y/m/d') }}
                                    </div>
                                </td>
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold bg-gradient-to-r from-green-500 to-emerald-600 text-white shadow-md">
                                            <i class="fas fa-check-circle"></i>
                                        {{ $enrollment->status ?? __('instructor.active_status') }}
                                    </span>
                                </td>
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        <a href="{{ route('profile') }}" 
                                           class="px-3 py-1.5 bg-sky-500 hover:bg-sky-600 text-white rounded-lg font-bold text-xs transition-all duration-300 transform hover:scale-105">
                                            <i class="fas fa-user"></i>
                                        </a>
                                    </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                    <div class="mt-4">
                    {{ $enrollments->links() }}
                </div>
            @else
                <div class="text-center py-12">
                        <div class="w-24 h-24 bg-gradient-to-br from-green-500/10 to-emerald-500/10 rounded-2xl flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-user-graduate text-4xl text-green-600"></i>
                        </div>
                        <p class="text-lg font-black text-slate-800 mb-2">{{ __('instructor.no_enrolled_students') }}</p>
                        <p class="text-sm text-slate-600 font-medium">{{ __('instructor.no_enrolled_description') }}</p>
                    </div>
                @endif
            </div>

            <!-- تبويب المجموعات -->
            <div x-show="activeTab === 'groups'" x-transition style="display: none;">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg sm:text-xl font-black text-slate-800 flex items-center gap-2">
                        <i class="fas fa-users text-green-600"></i>
                        {{ __('instructor.groups') }} ({{ $groups->count() }})
                    </h3>
                    <a href="{{ route('instructor.groups.create') }}" 
                       class="inline-flex items-center gap-2 bg-gradient-to-r from-green-500 to-emerald-600 hover:from-emerald-600 hover:to-green-700 text-white px-5 py-2.5 rounded-xl font-bold shadow-lg shadow-green-500/30 hover:shadow-xl transition-all duration-300 transform hover:scale-105">
                        <i class="fas fa-plus"></i>
                        <span>{{ __('instructor.create_group') }}</span>
                    </a>
                </div>
                @if($groups->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($groups as $group)
                        <div class="content-card rounded-xl p-5">
                            <div class="flex items-center justify-between mb-3">
                                <h4 class="font-black text-slate-800">{{ $group->name }}</h4>
                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold shadow-md {{ $group->status == 'active' ? 'bg-gradient-to-r from-green-500 to-emerald-600 text-white' : 'bg-amber-500 text-white' }}">
                                    {{ $group->status == 'active' ? __('instructor.active') : __('instructor.inactive') }}
                                </span>
                            </div>
                            <div class="space-y-2 text-sm mb-4">
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-users text-green-600"></i>
                                    <span class="text-slate-600 font-medium">{{ __('instructor.members_label') }}:</span>
                                    <span class="text-slate-800 font-bold">{{ $group->members_count ?? 0 }} / {{ $group->max_members ?? __('instructor.unlimited') }}</span>
                                </div>
                            </div>
                            <a href="{{ route('instructor.groups.show', $group) }}" 
                               class="w-full inline-flex items-center justify-center gap-2 bg-gradient-to-r from-green-500 to-emerald-600 hover:from-emerald-600 hover:to-green-700 text-white px-4 py-2.5 rounded-xl font-bold shadow-lg shadow-green-500/30 hover:shadow-xl transition-all duration-300 transform hover:scale-105">
                                <i class="fas fa-eye"></i>
                                <span>{{ __('instructor.view_details') }}</span>
                            </a>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-12">
                        <div class="w-24 h-24 bg-gradient-to-br from-green-500/10 to-emerald-500/10 rounded-2xl flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-users text-4xl text-green-600"></i>
                        </div>
                        <p class="text-lg font-black text-slate-800 mb-2">{{ __('instructor.no_groups') }}</p>
                        <a href="{{ route('instructor.groups.create') }}" 
                           class="inline-flex items-center gap-2 mt-4 px-6 py-3 bg-gradient-to-r from-green-500 to-emerald-600 hover:from-emerald-600 hover:to-green-700 text-white font-bold rounded-xl shadow-lg shadow-green-500/30 hover:shadow-xl transition-all duration-300 transform hover:scale-105">
                            <i class="fas fa-plus"></i>
                            {{ __('instructor.create_new_group') }}
                        </a>
                    </div>
                @endif
            </div>

            <!-- تبويب الحضور -->
            <div x-show="activeTab === 'attendance'" x-transition style="display: none;">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg sm:text-xl font-black text-slate-800 flex items-center gap-2">
                        <i class="fas fa-clipboard-list text-blue-600"></i>
                        {{ __('instructor.attendance_absence') }}
                    </h3>
                    <a href="{{ route('instructor.attendance.index', ['course_id' => $course->id]) }}" 
                       class="inline-flex items-center gap-2 bg-gradient-to-r from-blue-500 to-cyan-600 hover:from-cyan-600 hover:to-blue-700 text-white px-5 py-2.5 rounded-xl font-bold shadow-lg shadow-blue-500/30 hover:shadow-xl transition-all duration-300 transform hover:scale-105">
                        <i class="fas fa-eye"></i>
                        <span>{{ __('instructor.view_all_records') }}</span>
                    </a>
                </div>
                @php
                    $courseLectures = \App\Models\Lecture::where('course_id', $course->id)
                        ->where('status', 'completed')
                        ->withCount('attendanceRecords')
                        ->orderBy('scheduled_at', 'desc')
                        ->take(10)
                        ->get();
                @endphp
                @if($courseLectures->count() > 0)
                    <div class="space-y-3">
                        @foreach($courseLectures as $lecture)
                        <div class="item-row flex items-center justify-between p-4 bg-gradient-to-r from-blue-500/5 to-cyan-500/5 rounded-xl border border-blue-500/10">
                            <div class="flex items-center gap-4 flex-1">
                                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-blue-500 to-cyan-600 flex items-center justify-center text-white shadow-md">
                                    <i class="fas fa-clipboard-list"></i>
                                </div>
                                <div class="flex-1">
                                    <div class="font-black text-slate-800 mb-1">{{ $lecture->title }}</div>
                                    <div class="text-sm text-slate-600 font-medium">
                                        <i class="fas fa-calendar-alt text-blue-600 ml-1"></i>
                                        {{ $lecture->scheduled_at->format('Y/m/d H:i') }}
                                        <span class="mr-2">-</span>
                                        <i class="fas fa-users text-green-600 ml-1"></i>
                                        {{ $lecture->attendance_records_count }} {{ __('instructor.attendance_record_single') }}
                                    </div>
                                </div>
                            </div>
                            <a href="{{ route('instructor.attendance.lecture', $lecture) }}" 
                               class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold transition-all duration-300 transform hover:scale-105">
                                <i class="fas fa-eye text-xs"></i>
                                <span class="mr-2">{{ __('common.view') }}</span>
                            </a>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-12">
                        <div class="w-24 h-24 bg-gradient-to-br from-blue-500/10 to-cyan-500/10 rounded-2xl flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-clipboard-list text-4xl text-blue-600"></i>
                        </div>
                        <p class="text-lg font-black text-slate-800 mb-2">{{ __('instructor.no_attendance_records') }}</p>
                        <p class="text-sm text-slate-600 font-medium">{{ __('instructor.no_attendance_description') }}</p>
                </div>
            @endif
            </div>
        </div>
    </div>
    </div>
</div>
@endsection
