@extends('layouts.app')

@section('title', 'لوحة تحكم المدرب')
@section('header', 'لوحة تحكم المدرب')

@push('styles')
<style>
    .item-card:hover { background-color: rgb(248 250 252); }
</style>
@endpush

@section('content')
<div class="space-y-6">
    <!-- بطاقة ترحيب محسّنة -->
    <div class="relative rounded-2xl border border-slate-200 bg-gradient-to-br from-white via-slate-50/30 to-white shadow-sm overflow-hidden">
        <div class="absolute top-0 right-0 w-32 h-32 rounded-full bg-sky-100/40 -translate-y-1/2 translate-x-1/2" aria-hidden="true"></div>
        <div class="relative flex flex-col lg:flex-row lg:items-center gap-6 lg:gap-8 p-6 sm:p-8">
            <div class="flex items-start sm:items-center gap-4 sm:gap-5 min-w-0 flex-1">
                <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl bg-white shadow-sm flex items-center justify-center flex-shrink-0 border border-slate-100">
                    <i class="fas fa-chalkboard-teacher text-sky-500 text-3xl sm:text-4xl"></i>
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-xs font-semibold text-sky-600 uppercase tracking-widest mb-1.5">لوحة المدرب</p>
                    <h2 class="text-2xl sm:text-3xl font-bold text-slate-800 mb-1 truncate">مرحباً، {{ auth()->user()->name }}</h2>
                    <p class="text-slate-500 text-sm">نظرة عامة على نشاطك التعليمي اليوم</p>
                </div>
            </div>
            <div class="flex flex-wrap items-center gap-x-6 gap-y-3 lg:gap-8 lg:border-r lg:border-slate-200 lg:pr-8">
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-sky-50 text-sky-600">
                        <i class="fas fa-book text-sm"></i>
                    </span>
                    <span class="text-slate-600"><span class="font-bold text-slate-800">{{ number_format($stats['my_courses']) }}</span> كورس</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600">
                        <i class="fas fa-user-graduate text-sm"></i>
                    </span>
                    <span class="text-slate-600"><span class="font-bold text-slate-800">{{ number_format($stats['total_students']) }}</span> طالب</span>
                </div>
                <div class="flex items-center gap-2 text-slate-400 text-sm">
                    <i class="fas fa-calendar-alt"></i>
                    <time datetime="{{ now()->toIso8601String() }}">{{ now()->translatedFormat('l، d F Y') }}</time>
                </div>
            </div>
        </div>
    </div>

    <!-- إحصائيات سريعة (نفس أسلوب الكارد) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
        <!-- كورساتي -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 sm:p-6 transition-shadow hover:shadow-md">
            <div class="flex items-center justify-between">
                <div class="flex-1 min-w-0">
                    <p class="text-xs sm:text-sm font-semibold text-slate-500 uppercase tracking-wide mb-2">كورساتي</p>
                    <p class="text-3xl sm:text-4xl font-bold text-slate-800 leading-none">{{ number_format($stats['my_courses']) }}</p>
                </div>
                <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-2xl bg-sky-50 flex items-center justify-center flex-shrink-0 border border-slate-100">
                    <i class="fas fa-book text-sky-600 text-xl sm:text-2xl"></i>
                </div>
            </div>
            <div class="mt-4">
                <a href="{{ route('instructor.courses.index') }}" class="text-sm font-semibold text-sky-600 hover:text-sky-700 transition-colors inline-flex items-center gap-2">
                    إدارة الكورسات
                    <i class="fas fa-arrow-left text-xs"></i>
                </a>
            </div>
        </div>

        <!-- طلابي -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 sm:p-6 transition-shadow hover:shadow-md">
            <div class="flex items-center justify-between">
                <div class="flex-1 min-w-0">
                    <p class="text-xs sm:text-sm font-semibold text-slate-500 uppercase tracking-wide mb-2">إجمالي الطلاب</p>
                    <p class="text-3xl sm:text-4xl font-bold text-slate-800 leading-none">{{ number_format($stats['total_students']) }}</p>
                </div>
                <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-2xl bg-emerald-50 flex items-center justify-center flex-shrink-0 border border-slate-100">
                    <i class="fas fa-user-graduate text-emerald-600 text-xl sm:text-2xl"></i>
                </div>
            </div>
            <div class="mt-4">
                <a href="{{ route('instructor.courses.index') }}" class="text-sm font-semibold text-emerald-600 hover:text-emerald-700 transition-colors inline-flex items-center gap-2">
                    عرض الطلاب
                    <i class="fas fa-arrow-left text-xs"></i>
                </a>
            </div>
        </div>

        <!-- المحاضرات -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 sm:p-6 transition-shadow hover:shadow-md">
            <div class="flex items-center justify-between">
                <div class="flex-1 min-w-0">
                    <p class="text-xs sm:text-sm font-semibold text-slate-500 uppercase tracking-wide mb-2">المحاضرات</p>
                    <p class="text-3xl sm:text-4xl font-bold text-slate-800 leading-none">{{ number_format($stats['total_lectures']) }}</p>
                    <p class="text-xs text-slate-500 font-medium mt-1">{{ $stats['upcoming_lectures'] }} قادمة</p>
                </div>
                <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-2xl bg-violet-50 flex items-center justify-center flex-shrink-0 border border-slate-100">
                    <i class="fas fa-chalkboard-teacher text-violet-600 text-xl sm:text-2xl"></i>
                </div>
            </div>
            <div class="mt-4">
                <a href="{{ route('instructor.lectures.index') }}" class="text-sm font-semibold text-violet-600 hover:text-violet-700 transition-colors inline-flex items-center gap-2">
                    إدارة المحاضرات
                    <i class="fas fa-arrow-left text-xs"></i>
                </a>
            </div>
        </div>

        <!-- الواجبات -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 sm:p-6 transition-shadow hover:shadow-md">
            <div class="flex items-center justify-between">
                <div class="flex-1 min-w-0">
                    <p class="text-xs sm:text-sm font-semibold text-slate-500 uppercase tracking-wide mb-2">الواجبات</p>
                    <p class="text-3xl sm:text-4xl font-bold text-slate-800 leading-none">{{ number_format($stats['total_assignments']) }}</p>
                    <p class="text-xs text-slate-500 font-medium mt-1">{{ $stats['pending_submissions'] }} تحتاج تقييم</p>
                </div>
                <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-2xl bg-amber-50 flex items-center justify-center flex-shrink-0 border border-slate-100">
                    <i class="fas fa-tasks text-amber-600 text-xl sm:text-2xl"></i>
                </div>
            </div>
            <div class="mt-4">
                <a href="{{ route('instructor.assignments.index') }}" class="text-sm font-semibold text-amber-600 hover:text-amber-700 transition-colors inline-flex items-center gap-2">
                    إدارة الواجبات
                    <i class="fas fa-arrow-left text-xs"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- المحتوى الرئيسي -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 sm:gap-6">
        <!-- آخر الكورسات -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-5 sm:px-6 py-4 border-b border-slate-200 bg-slate-50">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                        <i class="fas fa-book text-sky-500"></i>
                        كورساتي الحديثة
                    </h3>
                    <a href="{{ route('instructor.courses.index') }}" class="text-sm font-semibold text-sky-600 hover:text-sky-700 transition-colors inline-flex items-center gap-2">
                        عرض الكل
                        <i class="fas fa-arrow-left text-xs"></i>
                    </a>
                </div>
            </div>
            <div class="p-5 sm:p-6">
                <div class="space-y-3">
                    @forelse($my_courses as $course)
                    <div class="item-card flex items-start gap-4 p-4 rounded-xl border border-slate-200 hover:border-slate-300 transition-colors">
                        <div class="w-12 h-12 rounded-xl bg-sky-50 flex items-center justify-center flex-shrink-0 border border-slate-100">
                            <i class="fas fa-play text-sky-600 text-sm"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h4 class="text-sm font-bold text-slate-800 truncate mb-1">{{ $course->title }}</h4>
                            <p class="text-xs text-slate-500 mb-2">
                                <i class="fas fa-book text-sky-500 ml-1"></i>
                                {{ $course->academicSubject->name ?? 'غير محدد' }}
                                @if($course->academicYear)
                                    - {{ $course->academicYear->name }}
                                @endif
                            </p>
                            <div class="flex items-center gap-4 mt-2">
                                <span class="text-xs text-slate-600 font-medium">
                                    <i class="fas fa-users text-sky-500 ml-1"></i>
                                    {{ $course->active_students_count ?? 0 }} طالب
                                </span>
                                <span class="text-xs text-slate-400">
                                    <i class="fas fa-calendar ml-1"></i>
                                    {{ $course->created_at->format('Y/m/d') }}
                                </span>
                            </div>
                        </div>
                        <a href="{{ route('instructor.courses.show', $course) }}" class="p-2.5 rounded-xl bg-sky-100 text-sky-600 hover:bg-sky-200 transition-colors" title="عرض التفاصيل">
                            <i class="fas fa-eye text-xs"></i>
                        </a>
                    </div>
                    @empty
                    <div class="text-center py-8 text-slate-500">
                        <i class="fas fa-book text-4xl mb-3 text-slate-300"></i>
                        <p class="font-medium">لم تُعيّن لك كورسات بعد</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- المحاضرات القادمة -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-5 sm:px-6 py-4 border-b border-slate-200 bg-slate-50">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                        <i class="fas fa-calendar-alt text-violet-500"></i>
                        المحاضرات القادمة
                    </h3>
                    <a href="{{ route('instructor.lectures.index') }}" class="text-sm font-semibold text-violet-600 hover:text-violet-700 transition-colors inline-flex items-center gap-2">
                        عرض الكل
                        <i class="fas fa-arrow-left text-xs"></i>
                    </a>
                </div>
            </div>
            <div class="p-5 sm:p-6">
                <div class="space-y-3">
                    @forelse($upcoming_lectures as $lecture)
                    <div class="item-card flex items-start gap-4 p-4 rounded-xl border border-slate-200 hover:border-slate-300 transition-colors">
                        <div class="w-12 h-12 rounded-xl bg-violet-50 flex items-center justify-center flex-shrink-0 border border-slate-100">
                            <i class="fas fa-video text-violet-600 text-sm"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h4 class="text-sm font-bold text-slate-800 truncate mb-1">{{ $lecture->title }}</h4>
                            <p class="text-xs text-slate-500 mb-2">
                                <i class="fas fa-book text-violet-500 ml-1"></i>
                                {{ $lecture->course->title ?? 'غير محدد' }}
                                @if($lecture->lesson)
                                    - {{ $lecture->lesson->title }}
                                @endif
                            </p>
                            <div class="flex items-center gap-4 mt-2">
                                <span class="text-xs text-slate-600 font-medium">
                                    <i class="fas fa-calendar text-violet-500 ml-1"></i>
                                    {{ $lecture->scheduled_at->format('Y/m/d') }}
                                </span>
                                <span class="text-xs text-slate-400">
                                    <i class="fas fa-clock ml-1"></i>
                                    {{ $lecture->scheduled_at->format('H:i') }}
                                </span>
                            </div>
                        </div>
                        <a href="{{ route('instructor.lectures.show', $lecture) }}" class="p-2.5 rounded-xl bg-violet-100 text-violet-600 hover:bg-violet-200 transition-colors" title="عرض التفاصيل">
                            <i class="fas fa-eye text-xs"></i>
                        </a>
                    </div>
                    @empty
                    <div class="text-center py-8 text-slate-500">
                        <i class="fas fa-calendar-alt text-4xl mb-3 text-slate-300"></i>
                        <p class="font-medium mb-3">لا توجد محاضرات قادمة</p>
                        <a href="{{ route('instructor.lectures.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-violet-100 text-violet-700 rounded-xl hover:bg-violet-200 font-semibold text-sm transition-colors">
                            <i class="fas fa-plus"></i>
                            إضافة محاضرة
                        </a>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- الواجبات المعلقة والمجموعات -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 sm:gap-6">
        <!-- الواجبات المعلقة -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-5 sm:px-6 py-4 border-b border-slate-200 bg-slate-50">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                        <i class="fas fa-tasks text-amber-500"></i>
                        واجبات تحتاج تقييم ({{ $stats['pending_submissions'] }})
                    </h3>
                    <a href="{{ route('instructor.assignments.index') }}" class="text-sm font-semibold text-amber-600 hover:text-amber-700 transition-colors inline-flex items-center gap-2">
                        عرض الكل
                        <i class="fas fa-arrow-left text-xs"></i>
                    </a>
                </div>
            </div>
            <div class="p-5 sm:p-6">
                <div class="space-y-3">
                    @forelse($pending_assignments as $submission)
                    <div class="item-card flex items-start gap-4 p-4 rounded-xl border border-slate-200 hover:border-slate-300 transition-colors">
                        <div class="w-12 h-12 rounded-xl bg-amber-50 flex items-center justify-center flex-shrink-0 border border-slate-100">
                            <i class="fas fa-file-alt text-amber-600 text-sm"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h4 class="text-sm font-bold text-slate-800 truncate mb-1">{{ $submission->assignment->title ?? 'واجب' }}</h4>
                            <p class="text-xs text-slate-500 mb-2">
                                <i class="fas fa-user text-amber-500 ml-1"></i>
                                {{ $submission->student->name ?? 'طالب' }}
                            </p>
                            <div class="flex items-center gap-4 mt-2">
                                <span class="text-xs text-slate-600 font-medium">
                                    <i class="fas fa-calendar text-amber-500 ml-1"></i>
                                    {{ $submission->created_at->format('Y/m/d') }}
                                </span>
                                <span class="text-xs text-slate-400">{{ $submission->created_at->diffForHumans() }}</span>
                            </div>
                        </div>
                        <a href="{{ route('instructor.assignments.submissions', $submission->assignment) }}" class="p-2.5 rounded-xl bg-amber-100 text-amber-600 hover:bg-amber-200 transition-colors" title="تقييم الواجب">
                            <i class="fas fa-check text-xs"></i>
                        </a>
                    </div>
                    @empty
                    <div class="text-center py-8 text-slate-500">
                        <i class="fas fa-check-circle text-4xl mb-2 text-slate-300"></i>
                        <p class="font-medium">جميع الواجبات تم تقييمها</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- المجموعات -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-5 sm:px-6 py-4 border-b border-slate-200 bg-slate-50">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                        <i class="fas fa-users text-emerald-500"></i>
                        مجموعاتي ({{ $stats['total_groups'] }})
                    </h3>
                    <a href="{{ route('instructor.groups.index') }}" class="text-sm font-semibold text-emerald-600 hover:text-emerald-700 transition-colors inline-flex items-center gap-2">
                        عرض الكل
                        <i class="fas fa-arrow-left text-xs"></i>
                    </a>
                </div>
            </div>
            <div class="p-5 sm:p-6">
                <div class="space-y-3">
                    @forelse($my_groups as $group)
                    <div class="item-card flex items-start gap-4 p-4 rounded-xl border border-slate-200 hover:border-slate-300 transition-colors">
                        <div class="w-12 h-12 rounded-xl bg-emerald-50 flex items-center justify-center flex-shrink-0 border border-slate-100">
                            <i class="fas fa-users text-emerald-600 text-sm"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h4 class="text-sm font-bold text-slate-800 truncate mb-1">{{ $group->name }}</h4>
                            <p class="text-xs text-slate-500 mb-2">
                                <i class="fas fa-book text-emerald-500 ml-1"></i>
                                {{ $group->course->title ?? 'غير محدد' }}
                            </p>
                            <div class="flex items-center gap-4 mt-2">
                                <span class="text-xs text-slate-600 font-medium">
                                    <i class="fas fa-users text-emerald-500 ml-1"></i>
                                    {{ $group->members->count() ?? 0 }} عضو
                                </span>
                                @if($group->max_members)
                                    <span class="text-xs text-slate-400">/ {{ $group->max_members }} حد أقصى</span>
                                @endif
                            </div>
                        </div>
                        <a href="{{ route('instructor.groups.show', $group) }}" class="p-2.5 rounded-xl bg-emerald-100 text-emerald-600 hover:bg-emerald-200 transition-colors" title="عرض التفاصيل">
                            <i class="fas fa-eye text-xs"></i>
                        </a>
                    </div>
                    @empty
                    <div class="text-center py-8 text-slate-500">
                        <i class="fas fa-users text-4xl mb-3 text-slate-300"></i>
                        <p class="font-medium mb-3">لا توجد مجموعات</p>
                        <a href="{{ route('instructor.groups.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-emerald-100 text-emerald-700 rounded-xl hover:bg-emerald-200 font-semibold text-sm transition-colors">
                            <i class="fas fa-plus"></i>
                            إنشاء مجموعة
                        </a>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- إجراءات سريعة -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-5 sm:px-6 py-4 border-b border-slate-200 bg-slate-50">
            <h3 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                <i class="fas fa-bolt text-sky-500"></i>
                إجراءات سريعة
            </h3>
        </div>
        <div class="p-5 sm:p-6 grid grid-cols-2 md:grid-cols-4 gap-3 sm:gap-4">
            <a href="{{ route('instructor.lectures.create') }}" class="flex flex-col items-center p-4 rounded-xl border border-slate-200 bg-slate-50/50 hover:bg-sky-50 hover:border-sky-200 transition-colors group">
                <div class="w-12 h-12 rounded-xl bg-sky-100 flex items-center justify-center mb-3 group-hover:bg-sky-200 transition-colors">
                    <i class="fas fa-video text-sky-600 text-xl"></i>
                </div>
                <span class="text-xs sm:text-sm font-semibold text-slate-700">إضافة محاضرة</span>
            </a>
            <a href="{{ route('instructor.assignments.create') }}" class="flex flex-col items-center p-4 rounded-xl border border-slate-200 bg-slate-50/50 hover:bg-emerald-50 hover:border-emerald-200 transition-colors group">
                <div class="w-12 h-12 rounded-xl bg-emerald-100 flex items-center justify-center mb-3 group-hover:bg-emerald-200 transition-colors">
                    <i class="fas fa-tasks text-emerald-600 text-xl"></i>
                </div>
                <span class="text-xs sm:text-sm font-semibold text-slate-700">إضافة واجب</span>
            </a>
            <a href="{{ route('instructor.exams.index') }}" class="flex flex-col items-center p-4 rounded-xl border border-slate-200 bg-slate-50/50 hover:bg-violet-50 hover:border-violet-200 transition-colors group">
                <div class="w-12 h-12 rounded-xl bg-violet-100 flex items-center justify-center mb-3 group-hover:bg-violet-200 transition-colors">
                    <i class="fas fa-clipboard-check text-violet-600 text-xl"></i>
                </div>
                <span class="text-xs sm:text-sm font-semibold text-slate-700">إدارة الاختبارات</span>
            </a>
            <a href="{{ route('instructor.attendance.index') }}" class="flex flex-col items-center p-4 rounded-xl border border-slate-200 bg-slate-50/50 hover:bg-amber-50 hover:border-amber-200 transition-colors group">
                <div class="w-12 h-12 rounded-xl bg-amber-100 flex items-center justify-center mb-3 group-hover:bg-amber-200 transition-colors">
                    <i class="fas fa-clipboard-list text-amber-600 text-xl"></i>
                </div>
                <span class="text-xs sm:text-sm font-semibold text-slate-700">الحضور والغياب</span>
            </a>
        </div>
    </div>
</div>
@endsection
