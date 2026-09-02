@extends('layouts.student-dashboard')

@php
    $isOnlineChannel = ($channel ?? 'offline') === 'online';
    $sg = $studentRouteGroup ?? 'student.offline-courses';
    $maxScore = (int) $activity->max_score;
@endphp

@section('title', $activity->title)
@section('header', $activity->title)

@section('content')
<div class="space-y-5 max-w-4xl">
    <nav class="flex flex-wrap items-center gap-2 text-sm font-bold text-[var(--sp-muted)]">
        <a href="{{ route($sg . '.index') }}" class="sp-link">{{ $isOnlineChannel ? __('student.online_courses_title') : __('student.offline_courses_title') }}</a>
        <x-student.figma-icon name="icon-chevron.svg" box="size-3" class="opacity-40 rtl:rotate-180" />
        <a href="{{ route($sg . '.show', $offlineCourse) }}" class="sp-link truncate max-w-[40vw]">{{ $offlineCourse->title }}</a>
        <x-student.figma-icon name="icon-chevron.svg" box="size-3" class="opacity-40 rtl:rotate-180" />
        <span class="text-[var(--sp-text)] truncate max-w-[40vw]">{{ $activity->title }}</span>
    </nav>

    @if(session('success'))
        <div class="sp-card !rounded-[16px] px-4 py-3 text-sm font-bold" style="background:var(--sp-mint);color:var(--sp-accent-text)">{{ session('success') }}</div>
    @endif

    <section class="sp-card p-5 sm:p-6 space-y-5">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div class="flex items-start gap-4 min-w-0">
                <span class="sp-icon-bubble shrink-0 !w-14 !h-14" style="background:var(--sp-peach)">
                    <x-student.figma-icon name="icon-star.svg" box="size-7" />
                </span>
                <div class="min-w-0">
                    <h2 class="sp-section-title m-0">{{ $activity->title }}</h2>
                    <p class="text-sm text-[var(--sp-muted)] m-0 mt-2">
                        {{ $activity->type }} · {{ __('student.oc_max_score') }}: {{ $activity->max_score }}
                        @if($activity->due_date) · {{ __('student.oc_due_date') }}: {{ $activity->due_date->format('Y/m/d') }}@endif
                    </p>
                </div>
            </div>
            <a href="{{ route($sg . '.show', $offlineCourse) }}" class="inline-flex items-center justify-center rounded-[30px] px-4 py-2.5 text-sm font-extrabold bg-[#f7f7f5] text-[var(--sp-accent-text)] hover:bg-[var(--sp-accent)] transition-colors shrink-0">{{ __('student.oc_back_course') }}</a>
        </div>

        @if($activity->description)
            <div>
                <h3 class="text-xs font-bold text-[var(--sp-muted)] m-0 mb-2 uppercase tracking-wide">{{ __('student.oc_activity_description') }}</h3>
                <p class="text-sm whitespace-pre-line m-0 leading-relaxed">{{ $activity->description }}</p>
            </div>
        @endif

        @if($activity->instructions)
            <div class="rounded-[16px] bg-[#f7f7f5] p-4">
                <h3 class="text-xs font-bold text-[var(--sp-muted)] m-0 mb-2 uppercase tracking-wide">{{ __('student.oc_submit_instructions') }}</h3>
                <p class="text-sm whitespace-pre-line m-0 leading-relaxed">{{ $activity->instructions }}</p>
            </div>
        @endif

        @if($activity->attachments && count($activity->attachments) > 0)
            <div>
                <h3 class="text-xs font-bold text-[var(--sp-muted)] m-0 mb-2 uppercase tracking-wide">{{ __('student.oc_instructor_attachments') }}</h3>
                <ul class="space-y-2 m-0 p-0 list-none">
                    @foreach($activity->attachments as $att)
                        <li>
                            <a href="{{ stored_upload_file_url($att) }}" target="_blank" rel="noopener" class="sp-link text-sm font-extrabold inline-flex items-center gap-2">
                                {{ $att['name'] ?? __('student.oc_file') }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if($submission)
            <div class="rounded-[16px] bg-[#f7f7f5] p-4 sm:p-5 space-y-3">
                <div class="flex flex-wrap items-center justify-between gap-2">
                    <h3 class="font-extrabold text-sm m-0">{{ __('student.oc_your_submission') }}</h3>
                    @if($submission->submitted_at)
                        <span class="text-xs text-[var(--sp-muted)]">{{ __('student.oc_last_update') }}: {{ $submission->submitted_at->format('Y/m/d H:i') }}</span>
                    @endif
                </div>
                @if($submission->status === 'submitted')
                    <p class="text-xs font-bold m-0 rounded-[12px] px-3 py-2" style="background:var(--sp-amber-soft);color:var(--sp-accent-text)">{{ __('student.oc_submission_pending_review') }}</p>
                @endif
                @if($submission->submission_content)
                    <div>
                        <h4 class="text-xs font-bold text-[var(--sp-muted)] m-0 mb-1">{{ __('student.oc_submission_text') }}</h4>
                        <p class="text-sm whitespace-pre-line m-0">{{ $submission->submission_content }}</p>
                    </div>
                @endif
                @if($submission->attachments && count($submission->attachments))
                    <div>
                        <h4 class="text-xs font-bold text-[var(--sp-muted)] m-0 mb-2">{{ __('student.oc_your_files') }}</h4>
                        <ul class="space-y-2 m-0 p-0 list-none">
                            @foreach($submission->attachments as $f)
                                <li>
                                    <a href="{{ offline_activity_submission_file_url($f) }}" target="_blank" rel="noopener" download="{{ $f['name'] ?? 'download' }}" class="sp-link text-sm font-extrabold">
                                        {{ $f['name'] ?? __('student.oc_file') }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
        @endif

        @if($submission && $submission->status === 'graded')
            <div class="rounded-[16px] p-4 sm:p-5 space-y-3" style="background:var(--sp-mint)">
                <h3 class="font-extrabold text-sm m-0">{{ __('student.oc_grade_result') }}</h3>
                <div class="flex flex-wrap items-baseline gap-3">
                    <p class="text-3xl font-black m-0 text-[var(--sp-accent-text)]">{{ $submission->score }}</p>
                    <span class="font-extrabold">/ {{ $activity->max_score }}</span>
                    @if($maxScore > 0)
                        @php $pct = round((float) $submission->score / $maxScore * 100, 1); @endphp
                        <span class="sp-pill sp-pill--done">{{ $pct }}%</span>
                    @endif
                </div>
                @if($submission->graded_at)
                    <p class="text-xs text-[var(--sp-muted)] m-0">{{ __('student.oc_graded_at') }}: {{ $submission->graded_at->format('Y/m/d H:i') }}</p>
                @endif
                @if($submission->relationLoaded('grader') && $submission->grader)
                    <p class="text-xs text-[var(--sp-muted)] m-0">{{ __('student.oc_instructor') }}: {{ $submission->grader->name }}</p>
                @endif
                @if($submission->feedback)
                    <div>
                        <h4 class="text-xs font-bold text-[var(--sp-muted)] m-0 mb-1">{{ __('student.oc_instructor_feedback') }}</h4>
                        <p class="text-sm whitespace-pre-line m-0 leading-relaxed">{{ $submission->feedback }}</p>
                    </div>
                @else
                    <p class="text-sm text-[var(--sp-muted)] m-0">{{ __('student.oc_no_feedback') }}</p>
                @endif
            </div>
        @endif

        @if($activity->status !== 'published')
            <p class="text-sm font-bold m-0 rounded-[16px] px-4 py-3" style="background:var(--sp-amber-soft);color:var(--sp-accent-text)">{{ __('student.oc_activity_unavailable') }}</p>
        @elseif(!$submission || $submission->status !== 'graded')
            <div class="border-t border-black/5 pt-5 space-y-4">
                <h3 class="font-extrabold text-sm m-0">
                    {{ ($submission && $submission->status === 'submitted') ? __('student.oc_update_submission') : __('student.oc_submit_now') }}
                </h3>
                <form action="{{ route($sg . '.activities.submit', [$offlineCourse, $activity]) }}" method="post" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-[var(--sp-muted)] mb-2">{{ __('student.oc_submission_text_optional') }}</label>
                        <textarea name="submission_content" rows="5" class="w-full rounded-[16px] border-0 bg-[#f7f7f5] px-4 py-3 text-sm font-medium focus:ring-2 focus:ring-[var(--sp-accent)]" placeholder="{{ __('student.oc_submission_placeholder') }}">{{ old('submission_content', $submission->submission_content ?? '') }}</textarea>
                        @error('submission_content')<p class="text-sm font-bold text-[#7a3b2e] mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-[var(--sp-muted)] mb-2">{{ __('student.oc_attachments_optional') }}</label>
                        <input type="file" name="attachments[]" multiple class="w-full rounded-[16px] border-0 bg-[#f7f7f5] px-4 py-3 text-sm">
                        <p class="text-xs text-[var(--sp-muted)] m-0 mt-2">{{ __('student.oc_attachments_hint') }}</p>
                        @error('attachments.*')<p class="text-sm font-bold text-[#7a3b2e] mt-1">{{ $message }}</p>@enderror
                    </div>
                    <button type="submit" class="sp-promo-btn !mt-0 border-0 cursor-pointer">
                        {{ ($submission && $submission->status === 'submitted') ? __('student.oc_update_submission') : __('student.oc_submit_now') }}
                    </button>
                </form>
            </div>
        @elseif($submission && $submission->status === 'graded')
            <p class="text-sm font-bold m-0 rounded-[16px] bg-[#f7f7f5] px-4 py-3">{{ __('student.oc_grade_locked') }}</p>
        @endif
    </section>
</div>
@endsection
