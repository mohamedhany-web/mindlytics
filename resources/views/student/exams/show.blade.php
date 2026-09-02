@extends('layouts.student-dashboard')

@section('title', $exam->title)
@section('header', $exam->title)

@section('content')
@php
    $courseLabel = $exam->offlineCourse->title ?? $exam->course->title ?? __('student.course_undefined');
    $questionCount = $exam->examQuestions()->whereHas('question')->count();
    $bestPercentage = $previousAttempts->where('status', 'completed')->max('percentage');
    $lastCompleted = $previousAttempts->where('status', 'completed')->first();
    $activeAttempt = $previousAttempts->firstWhere('status', 'in_progress');
@endphp

<div class="space-y-5 max-w-6xl">
    @if(session('error'))
        <div class="sp-card !rounded-[16px] px-4 py-3 text-sm font-bold bg-[#f9e4d7] text-[#7a3b2e]">{{ session('error') }}</div>
    @endif

    <nav class="flex flex-wrap items-center gap-2 text-sm font-bold text-[var(--sp-muted)]">
        <a href="{{ $exam->module_route }}" class="sp-link">{{ $exam->source_label }}</a>
        <x-student.figma-icon name="icon-chevron.svg" box="size-3" class="opacity-40 rtl:rotate-180" />
        <span class="text-[var(--sp-text)] truncate max-w-[60vw]">{{ $exam->title }}</span>
    </nav>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-5">
        <div class="xl:col-span-2 space-y-5">
            <section class="sp-card p-5 sm:p-6 space-y-4">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div class="flex items-start gap-4 min-w-0">
                        <span class="sp-icon-bubble shrink-0 !w-14 !h-14" style="background:{{ $exam->source_bubble }}">
                            <x-student.figma-icon :name="$exam->source_icon" box="size-7" />
                        </span>
                        <div class="min-w-0">
                            <h2 class="sp-section-title m-0">{{ $exam->title }}</h2>
                            <p class="text-sm text-[var(--sp-muted)] m-0 mt-1">{{ $exam->source_label }}</p>
                            <div class="flex flex-wrap items-center gap-2 mt-2">
                                <span class="sp-pill {{ $exam->isAvailable() ? 'sp-pill--done' : 'sp-pill--upcoming' }}">
                                    {{ $exam->isAvailable() ? __('student.exam_status_available') : __('student.exam_status_locked') }}
                                </span>
                            </div>
                            <a href="{{ $exam->course_route }}" class="sp-link text-sm font-bold inline-block mt-2">{{ $exam->course_label }}</a>
                        </div>
                    </div>
                    <a href="{{ $exam->module_route }}" class="inline-flex items-center justify-center gap-2 rounded-[30px] px-4 py-2.5 text-sm font-extrabold bg-[#f7f7f5] text-[var(--sp-accent-text)] hover:bg-[var(--sp-accent)] transition-colors shrink-0">
                        <x-student.figma-icon name="icon-chevron.svg" box="size-3.5" class="rtl:rotate-180" />
                        {{ __('student.exam_show_back') }}
                    </a>
                </div>

                @if($exam->description)
                    <div>
                        <p class="text-xs font-bold text-[var(--sp-muted)] m-0 mb-1.5">{{ __('student.exam_show_description') }}</p>
                        <p class="text-sm text-[var(--sp-text)] m-0 leading-relaxed">{{ $exam->description }}</p>
                    </div>
                @endif

                @if($exam->instructions)
                    <div class="rounded-[20px] p-4 sm:p-5" style="background:var(--sp-mint)">
                        <p class="font-extrabold text-sm m-0 mb-2 text-[var(--sp-accent-text)]">{{ __('student.exam_show_instructions') }}</p>
                        <div class="text-sm text-[var(--sp-accent-text)] leading-relaxed whitespace-pre-wrap opacity-90">{{ $exam->instructions }}</div>
                    </div>
                @endif
            </section>

            @if($previousAttempts->isNotEmpty())
                <section class="sp-card overflow-hidden">
                    <div class="px-5 py-4 border-b border-black/5">
                        <h3 class="font-extrabold text-base m-0">{{ __('student.exam_show_previous_attempts') }}</h3>
                    </div>
                    <div class="divide-y divide-black/5">
                        @foreach($previousAttempts as $index => $attempt)
                            <div class="px-5 py-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                                <div class="min-w-0">
                                    <p class="font-extrabold text-sm m-0">{{ __('student.exam_attempt_n', ['n' => $index + 1]) }}</p>
                                    <p class="text-xs text-[var(--sp-muted)] m-0 mt-1">{{ $attempt->created_at->format('Y/m/d H:i') }} · {{ $attempt->formatted_time }}</p>
                                </div>
                                <div class="flex items-center gap-3 shrink-0">
                                    @if($attempt->status === 'completed')
                                        <span class="sp-pill {{ $attempt->result_color === 'green' ? 'sp-pill--done' : 'sp-pill--upcoming' }}">
                                            {{ number_format($attempt->percentage, 1) }}% · {{ $attempt->result_status }}
                                        </span>
                                        @if($exam->show_results_immediately)
                                            <a href="{{ route('student.exams.result', [$exam, $attempt]) }}" class="sp-link text-sm font-extrabold">{{ __('student.view_result') }}</a>
                                        @endif
                                    @elseif($attempt->status === 'in_progress')
                                        <span class="sp-pill sp-pill--progress">{{ __('student.exam_status_in_progress') }}</span>
                                        <a href="{{ route('student.exams.take', [$exam, $attempt]) }}" class="sp-promo-btn !mt-0 !py-2 !px-3 text-xs">{{ __('student.exam_resume') }}</a>
                                    @else
                                        <span class="sp-pill sp-pill--upcoming">{{ __('student.exam_result_incomplete') }}</span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </section>
            @endif
        </div>

        <aside class="space-y-4">
            <section class="sp-card p-5 space-y-3">
                <h3 class="font-extrabold text-sm m-0">{{ __('student.exam_show_meta') }}</h3>
                <dl class="space-y-3 m-0 text-sm">
                    @foreach([
                        __('student.duration_label') => $exam->duration_minutes.' '.__('student.minutes'),
                        __('student.questions_label') => $questionCount,
                        __('student.passing_marks_label') => $exam->passing_marks.'%',
                        __('student.attempts_label') => $exam->attempts_allowed == 0 ? __('student.unlimited_attempts') : $exam->attempts_allowed,
                        __('student.your_attempts') => $previousAttempts->count(),
                    ] as $label => $value)
                        <div class="flex items-center justify-between gap-3">
                            <dt class="font-bold text-[var(--sp-muted)]">{{ $label }}</dt>
                            <dd class="m-0 font-extrabold">{{ $value }}</dd>
                        </div>
                    @endforeach
                    @if($exam->start_time)
                        <div class="flex items-center justify-between gap-3">
                            <dt class="font-bold text-[var(--sp-muted)]">{{ __('student.starts_at') }}</dt>
                            <dd class="m-0 font-extrabold">{{ $exam->start_time->format('Y/m/d H:i') }}</dd>
                        </div>
                    @endif
                    @if($exam->end_time)
                        <div class="flex items-center justify-between gap-3">
                            <dt class="font-bold text-[var(--sp-muted)]">{{ __('student.ends_at') }}</dt>
                            <dd class="m-0 font-extrabold">{{ $exam->end_time->format('Y/m/d H:i') }}</dd>
                        </div>
                    @endif
                </dl>
            </section>

            @if($exam->prevent_tab_switch || $exam->require_camera || $exam->require_microphone || $exam->auto_submit)
                <section class="sp-card p-5 space-y-2" style="background:#f9f0d7">
                    <h3 class="font-extrabold text-sm m-0 text-[var(--sp-accent-text)]">{{ __('student.exam_show_security') }}</h3>
                    <ul class="space-y-1.5 m-0 ps-4 text-sm text-[var(--sp-accent-text)]">
                        @if($exam->prevent_tab_switch)<li>{{ __('student.no_tab_switch') }}</li>@endif
                        @if($exam->require_camera)<li>{{ __('student.camera_label') }}</li>@endif
                        @if($exam->require_microphone)<li>{{ __('student.microphone_label') }}</li>@endif
                        @if($exam->auto_submit)<li>{{ __('student.exam_auto_submit') }}</li>@endif
                    </ul>
                </section>
            @endif

            @if($bestPercentage !== null)
                <section class="sp-card p-5 text-center space-y-2">
                    <p class="text-xs font-bold text-[var(--sp-muted)] m-0">{{ __('student.exam_show_best_score') }}</p>
                    <p class="text-3xl font-black text-[var(--sp-accent-text)] m-0">{{ number_format($bestPercentage, 1) }}%</p>
                    @if($exam->show_results_immediately && $lastCompleted)
                        <a href="{{ route('student.exams.result', [$exam, $lastCompleted]) }}" class="sp-link text-sm font-extrabold">{{ __('student.view_result') }}</a>
                    @endif
                </section>
            @endif

            <section class="sp-card p-5 space-y-3">
                @if($activeAttempt)
                    <a href="{{ route('student.exams.take', [$exam, $activeAttempt]) }}" class="sp-promo-btn !mt-0 w-full text-center">{{ __('student.exam_resume') }}</a>
                @elseif($exam->canAttempt(auth()->id()))
                    <form action="{{ route('student.exams.start', $exam) }}" method="POST" id="start-exam-form">
                        @csrf
                        <button type="button" onclick="document.getElementById('exam-confirm-modal').classList.remove('hidden')" class="sp-promo-btn !mt-0 w-full border-0 cursor-pointer">
                            {{ __('student.start_exam') }}
                        </button>
                    </form>
                @else
                    <div class="rounded-[16px] bg-[#f7f7f5] px-4 py-5 text-center">
                        <p class="font-extrabold m-0 mb-1">{{ __('student.exam_show_cannot_start') }}</p>
                        <p class="text-sm text-[var(--sp-muted)] m-0">
                            @if($previousAttempts->count() >= $exam->attempts_allowed && $exam->attempts_allowed > 0)
                                {{ __('student.exam_attempts_exhausted') }}
                            @elseif(!$exam->isAvailable())
                                {{ __('student.exam_not_available') }}
                            @else
                                {{ __('student.exam_start_forbidden') }}
                            @endif
                        </p>
                    </div>
                @endif
            </section>
        </aside>
    </div>
</div>

<div id="exam-confirm-modal" class="hidden fixed inset-0 z-[80] flex items-end sm:items-center justify-center p-0 sm:p-6" style="background:rgba(15,15,20,.45);backdrop-filter:blur(4px)">
    <div class="sp-card !rounded-t-[24px] sm:!rounded-[24px] w-full sm:max-w-md p-6 space-y-4" onclick="event.stopPropagation()">
        <span class="sp-icon-bubble mx-auto" style="background:var(--sp-peach)">
            <x-student.figma-icon name="icon-exams.svg" />
        </span>
        <div class="text-center">
            <h3 class="font-black text-lg m-0 mb-2">{{ __('student.exam_show_confirm_title') }}</h3>
            <p class="text-sm text-[var(--sp-muted)] m-0">{{ __('student.exam_show_confirm_body') }}</p>
        </div>
        @if($exam->prevent_tab_switch)
            <div class="rounded-[14px] px-3 py-2 text-sm font-bold bg-[#f9e4d7] text-[#7a3b2e]">{{ __('student.no_tab_switch') }}</div>
        @endif
        <div class="flex flex-wrap gap-2">
            <button type="button" onclick="document.getElementById('start-exam-form').submit()" class="sp-promo-btn !mt-0 flex-1 border-0 cursor-pointer">{{ __('student.exam_show_confirm_start') }}</button>
            <button type="button" onclick="document.getElementById('exam-confirm-modal').classList.add('hidden')" class="flex-1 inline-flex items-center justify-center rounded-[30px] px-4 py-2.5 text-sm font-extrabold bg-[#f7f7f5] text-[var(--sp-accent-text)] border-0 cursor-pointer">{{ __('common.cancel') }}</button>
        </div>
    </div>
</div>
@endsection
