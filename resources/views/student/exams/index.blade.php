@extends('layouts.app')

@section('title', 'امتحاناتي')
@section('header', 'امتحاناتي')

@section('content')
@php
    $completedExams = $availableExams->filter(function($exam) {
        return $exam->last_attempt && $exam->last_attempt->status === 'completed';
    });
    $canAttemptCount = $availableExams->where('can_attempt', true)->count();
    $avgScore = $completedExams->where('best_score', '!=', null)->avg('best_score');
@endphp

<div class="w-full px-4 sm:px-6 lg:px-8 py-6 space-y-6">
    <!-- الهيدر -->
    <div class="bg-white rounded-xl p-5 border border-gray-200 shadow-sm">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-xl sm:text-2xl font-bold text-gray-900 mb-1">امتحاناتي</h1>
                <p class="text-sm text-gray-500">الامتحانات المتاحة من الكورسات المفعلة لك</p>
            </div>
            <a href="{{ route('my-courses.index') }}" class="inline-flex items-center gap-2 bg-sky-500 hover:bg-sky-600 text-white px-4 py-2.5 rounded-lg text-sm font-semibold transition-colors">
                <i class="fas fa-book-open"></i>
                كورساتي
            </a>
        </div>
    </div>

    @if($availableExams->count() > 0)
        <!-- إحصائيات -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
            <div class="bg-white rounded-xl p-4 border border-gray-200 shadow-sm">
                <div class="flex items-center justify-between gap-3">
                    <div class="min-w-0">
                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">متاحة</p>
                        <p class="text-2xl font-bold text-sky-600 leading-none">{{ $availableExams->count() }}</p>
                    </div>
                    <div class="w-10 h-10 rounded-lg bg-sky-100 flex items-center justify-center text-sky-600 flex-shrink-0">
                        <i class="fas fa-clipboard-check"></i>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl p-4 border border-gray-200 shadow-sm">
                <div class="flex items-center justify-between gap-3">
                    <div class="min-w-0">
                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">مكتملة</p>
                        <p class="text-2xl font-bold text-emerald-600 leading-none">{{ $completedExams->count() }}</p>
                    </div>
                    <div class="w-10 h-10 rounded-lg bg-emerald-100 flex items-center justify-center text-emerald-600 flex-shrink-0">
                        <i class="fas fa-check"></i>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl p-4 border border-gray-200 shadow-sm">
                <div class="flex items-center justify-between gap-3">
                    <div class="min-w-0">
                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">يمكن أداؤها</p>
                        <p class="text-2xl font-bold text-amber-600 leading-none">{{ $canAttemptCount }}</p>
                    </div>
                    <div class="w-10 h-10 rounded-lg bg-amber-100 flex items-center justify-center text-amber-600 flex-shrink-0">
                        <i class="fas fa-play"></i>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl p-4 border border-gray-200 shadow-sm">
                <div class="flex items-center justify-between gap-3">
                    <div class="min-w-0">
                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">متوسط النتائج</p>
                        <p class="text-2xl font-bold text-gray-700 leading-none">{{ $avgScore ? number_format($avgScore, 1) : 0 }}%</p>
                    </div>
                    <div class="w-10 h-10 rounded-lg bg-gray-100 flex items-center justify-center text-gray-600 flex-shrink-0">
                        <i class="fas fa-percentage"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- الامتحانات -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            @foreach($availableExams as $exam)
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden hover:shadow-md transition-shadow">
                <div class="px-4 sm:px-5 py-3 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between flex-wrap gap-2">
                    <h3 class="text-base sm:text-lg font-bold text-gray-900">{{ $exam->title }}</h3>
                    @if($exam->can_attempt)
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold bg-emerald-100 text-emerald-800">
                            <i class="fas fa-check-circle"></i> متاح
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold bg-gray-100 text-gray-600">
                            <i class="fas fa-times-circle"></i> غير متاح
                        </span>
                    @endif
                </div>

                <div class="p-4 sm:p-5">
                    <p class="text-sm text-gray-600 mb-3">
                        <span class="font-semibold text-gray-900">{{ $exam->offlineCourse->title ?? $exam->course->title ?? '—' }}</span>
                        @if($exam->offline_course_id)
                            <span class="text-amber-600">(أوفلاين)</span>
@elseif(optional($exam->course)->academicSubject)
                                            · {{ $exam->course->academicSubject->name }}
                        @endif
                    </p>

                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 mb-4">
                        <div class="py-2 px-2 rounded-lg bg-gray-50 border border-gray-100">
                            <p class="text-xs text-gray-500">المدة</p>
                            <p class="text-sm font-semibold text-gray-900">{{ $exam->duration_minutes }} دقيقة</p>
                        </div>
                        <div class="py-2 px-2 rounded-lg bg-gray-50 border border-gray-100">
                            <p class="text-xs text-gray-500">الأسئلة</p>
                            <p class="text-sm font-semibold text-gray-900">{{ $exam->questions_count }} سؤال</p>
                        </div>
                        <div class="py-2 px-2 rounded-lg bg-gray-50 border border-gray-100">
                            <p class="text-xs text-gray-500">درجة النجاح</p>
                            <p class="text-sm font-semibold text-gray-900">{{ $exam->passing_marks }}%</p>
                        </div>
                        <div class="py-2 px-2 rounded-lg bg-gray-50 border border-gray-100">
                            <p class="text-xs text-gray-500">المحاولات</p>
                            <p class="text-sm font-semibold text-gray-900">{{ $exam->attempts_allowed == 0 ? 'غير محدود' : $exam->attempts_allowed }}</p>
                        </div>
                    </div>

                    @if($exam->user_attempts > 0)
                        <div class="flex items-center justify-between p-3 bg-sky-50 rounded-lg border border-sky-100 mb-4">
                            <div class="text-sm text-gray-700">
                                محاولاتك: <span class="font-semibold">{{ $exam->user_attempts }}</span> من {{ $exam->attempts_allowed == 0 ? 'غير محدود' : $exam->attempts_allowed }}
                            </div>
                            @if($exam->best_score !== null)
                                <span class="text-sm font-bold text-sky-600">أفضل نتيجة: {{ number_format($exam->best_score, 1) }}%</span>
                            @endif
                        </div>
                    @endif

                    @if($exam->description)
                        <p class="text-sm text-gray-600 mb-4 line-clamp-2">{{ $exam->description }}</p>
                    @endif

                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pt-3 border-t border-gray-100">
                        <div class="text-xs text-gray-500">
                            @if($exam->start_time)
                                يبدأ: <span class="font-medium text-gray-700">{{ $exam->start_time->format('Y-m-d H:i') }}</span>
                            @endif
                            @if($exam->end_time)
                                <span class="sm:mr-3">ينتهي: <span class="font-medium text-gray-700">{{ $exam->end_time->format('Y-m-d H:i') }}</span></span>
                            @endif
                        </div>
                        <div>
                            @if($exam->can_attempt)
                                <a href="{{ route('student.exams.show', $exam) }}" class="inline-flex items-center justify-center gap-2 bg-sky-500 hover:bg-sky-600 text-white px-5 py-2.5 rounded-lg text-sm font-semibold transition-colors">
                                    <i class="fas fa-play"></i>
                                    ابدأ الامتحان
                                </a>
                            @elseif($exam->user_attempts >= $exam->attempts_allowed && $exam->attempts_allowed > 0)
                                <span class="inline-flex items-center gap-2 bg-red-100 text-red-800 px-4 py-2.5 rounded-lg text-sm font-semibold">
                                    <i class="fas fa-ban"></i>
                                    استنفدت المحاولات
                                </span>
                            @else
                                <span class="inline-flex items-center gap-2 bg-gray-100 text-gray-600 px-4 py-2.5 rounded-lg text-sm font-semibold">
                                    <i class="fas fa-lock"></i>
                                    غير متاح حالياً
                                </span>
                            @endif
                        </div>
                    </div>

                    @if($exam->prevent_tab_switch || $exam->require_camera || $exam->require_microphone)
                        <div class="mt-3 pt-3 border-t border-gray-100 flex flex-wrap items-center gap-2 text-xs">
                            <span class="text-amber-600 font-semibold"><i class="fas fa-shield-alt ml-1"></i>امتحان محمي:</span>
                            @if($exam->prevent_tab_switch)<span class="px-2 py-0.5 rounded bg-amber-50 text-amber-700 border border-amber-100">منع تبديل التبويبات</span>@endif
                            @if($exam->require_camera)<span class="px-2 py-0.5 rounded bg-amber-50 text-amber-700 border border-amber-100">كاميرا</span>@endif
                            @if($exam->require_microphone)<span class="px-2 py-0.5 rounded bg-amber-50 text-amber-700 border border-amber-100">مايكروفون</span>@endif
                        </div>
                    @endif
                </div>
            </div>
            @endforeach
        </div>

        <!-- الامتحانات المكتملة -->
        @if($completedExams->count() > 0)
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="px-4 sm:px-5 py-3 border-b border-gray-200">
                    <h3 class="text-base font-bold text-gray-900">الامتحانات المكتملة</h3>
                </div>
                <div class="p-4 sm:p-5 space-y-3">
                    @foreach($completedExams as $exam)
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 p-4 rounded-lg border border-gray-100 hover:bg-gray-50/50 transition-colors">
                            <div class="flex-1 min-w-0">
                                <p class="font-bold text-gray-900">{{ $exam->title }}</p>
                                <p class="text-sm text-gray-500">{{ $exam->offlineCourse->title ?? $exam->course->title ?? '—' }} · {{ $exam->last_attempt->created_at->diffForHumans() }}</p>
                            </div>
                            <div class="flex items-center gap-3 flex-shrink-0">
                                <div class="text-center">
                                    <p class="text-lg font-bold {{ $exam->last_attempt->result_color == 'green' ? 'text-emerald-600' : 'text-red-600' }}">
                                        {{ number_format($exam->last_attempt->percentage, 1) }}%
                                    </p>
                                    <p class="text-xs text-gray-500">{{ $exam->last_attempt->result_status }}</p>
                                </div>
                                @if($exam->show_results_immediately)
                                    <a href="{{ route('student.exams.result', [$exam, $exam->last_attempt]) }}" class="inline-flex items-center gap-2 text-sky-600 hover:text-sky-700 text-sm font-semibold transition-colors">
                                        <i class="fas fa-chart-line"></i>
                                        عرض النتيجة
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    @else
        <div class="rounded-xl p-10 sm:p-12 text-center bg-gray-50 border border-dashed border-gray-200">
            <div class="w-16 h-16 bg-sky-100 rounded-2xl flex items-center justify-center mx-auto mb-4 text-sky-600">
                <i class="fas fa-clipboard-check text-2xl"></i>
            </div>
            <h3 class="text-lg font-bold text-gray-900 mb-2">لا توجد امتحانات متاحة</h3>
            <p class="text-sm text-gray-500 mb-6 max-w-sm mx-auto">لا توجد امتحانات متاحة في الكورسات المفعلة لك حالياً</p>
            <a href="{{ route('my-courses.index') }}" class="inline-flex items-center gap-2 bg-sky-500 hover:bg-sky-600 text-white px-5 py-2.5 rounded-lg text-sm font-semibold transition-colors">
                <i class="fas fa-book-open"></i>
                عرض كورساتي
            </a>
        </div>
    @endif
</div>
@endsection
