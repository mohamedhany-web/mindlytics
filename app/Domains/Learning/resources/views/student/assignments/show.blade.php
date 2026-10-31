@extends('layouts.student-dashboard')

@section('title', $assignment->title)
@section('header', $assignment->title)

@php
    $courseTitle = $assignment->course?->localized('title') ?? $assignment->course?->title ?? '—';
@endphp

@section('content')
<div class="space-y-5 max-w-4xl">
    @if(session('success'))
        <div class="sp-card !rounded-[16px] px-4 py-3 text-sm font-bold" style="background:var(--sp-mint);color:var(--sp-accent-text)">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="sp-card !rounded-[16px] px-4 py-3 text-sm font-bold bg-[#f9e4d7] text-[#7a3b2e]">
            {{ session('error') }}
        </div>
    @endif

    <nav class="flex flex-wrap items-center gap-2 text-sm font-bold text-[var(--sp-muted)]">
        <a href="{{ route('student.assignments.index') }}" class="sp-link">{{ __('student.assignments') }}</a>
        <x-student.figma-icon name="icon-chevron.svg" box="size-3" class="opacity-40 rtl:rotate-180" />
        <span class="text-[var(--sp-text)] truncate max-w-[60vw]">{{ $assignment->title }}</span>
    </nav>

    {{-- Assignment header --}}
    <section class="sp-card p-5 sm:p-6 space-y-4">
        <div class="flex flex-wrap items-start justify-between gap-3">
            <div class="min-w-0 flex items-start gap-4">
                <span class="sp-icon-bubble shrink-0" style="background:var(--sp-sky);width:56px;height:56px">
                    <x-student.figma-icon name="icon-messages.svg" box="size-7" />
                </span>
                <div class="min-w-0">
                    <div class="flex flex-wrap items-center gap-2 mb-1">
                        <h2 class="sp-section-title m-0">{{ $assignment->title }}</h2>
                        @if($studentStatus === 'graded')
                            <span class="sp-pill sp-pill--done">{{ __('student.assign_status_graded') }}</span>
                        @elseif($studentStatus === 'submitted')
                            <span class="sp-pill sp-pill--progress">{{ __('student.assign_status_submitted') }}</span>
                        @elseif($studentStatus === 'overdue')
                            <span class="sp-pill sp-pill--upcoming">{{ __('student.assign_status_overdue') }}</span>
                        @else
                            <span class="sp-pill sp-pill--upcoming">{{ __('student.assign_status_pending') }}</span>
                        @endif
                    </div>
                    <p class="text-sm text-[var(--sp-muted)] m-0">{{ $courseTitle }}</p>
                    @if($assignment->lesson)
                        <p class="text-xs font-bold text-[var(--sp-muted)] m-0 mt-1 flex items-center gap-1.5">
                            <x-student.figma-icon name="icon-classes.svg" box="size-3.5" />
                            {{ $assignment->lesson->title }}
                        </p>
                    @endif
                </div>
            </div>
            <a href="{{ route('student.assignments.index') }}"
               class="inline-flex items-center justify-center gap-2 rounded-[30px] px-4 py-2.5 text-sm font-extrabold bg-[#f7f7f5] text-[var(--sp-accent-text)] hover:bg-[var(--sp-accent)] transition-colors shrink-0">
                <x-student.figma-icon name="icon-chevron.svg" box="size-3.5" class="rtl:rotate-180" />
                {{ __('student.assign_back') }}
            </a>
        </div>

        @if($assignment->description)
            <div>
                <p class="text-xs font-bold text-[var(--sp-muted)] m-0 mb-2">{{ __('student.assign_description') }}</p>
                <p class="text-sm text-[var(--sp-text)] m-0 leading-relaxed whitespace-pre-line">{{ $assignment->description }}</p>
            </div>
        @endif

        @if($assignment->instructions)
            <div class="rounded-[20px] p-4 sm:p-5" style="background:var(--sp-mint)">
                <p class="text-xs font-bold text-[var(--sp-accent-text)] m-0 mb-2 flex items-center gap-1.5">
                    <x-student.figma-icon name="icon-exams.svg" box="size-3.5" />
                    {{ __('student.assign_instructions') }}
                </p>
                <p class="text-sm text-[var(--sp-accent-text)] m-0 leading-relaxed whitespace-pre-line">{{ $assignment->instructions }}</p>
            </div>
        @endif

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div class="rounded-[16px] bg-[#f7f7f5] p-4">
                <p class="text-xs font-bold text-[var(--sp-muted)] m-0">{{ __('student.assign_max_score') }}</p>
                <p class="text-lg font-black text-[var(--sp-accent-text)] m-0 mt-1">{{ $assignment->max_score }}</p>
            </div>
            <div class="rounded-[16px] bg-[#f7f7f5] p-4">
                <p class="text-xs font-bold text-[var(--sp-muted)] m-0">{{ __('student.assign_due_date') }}</p>
                <p class="text-sm font-extrabold text-[var(--sp-accent-text)] m-0 mt-1">
                    {{ $assignment->due_date ? $assignment->due_date->format('Y/m/d H:i') : __('student.assign_due_unset') }}
                </p>
            </div>
            <div class="rounded-[16px] bg-[#f7f7f5] p-4">
                <p class="text-xs font-bold text-[var(--sp-muted)] m-0">{{ __('student.assign_late_policy') }}</p>
                <p class="text-sm font-extrabold text-[var(--sp-accent-text)] m-0 mt-1">
                    {{ $assignment->allow_late_submission ? __('student.assign_late_allowed') : __('student.assign_late_not_allowed') }}
                </p>
            </div>
        </div>
    </section>

    {{-- Grade result (if graded) --}}
    @if($submission && ($submission->score !== null || $submission->feedback || in_array($submission->status, ['graded', 'returned'], true)))
        <section class="sp-card p-5 sm:p-6 space-y-4">
            <h3 class="font-extrabold text-sm m-0 flex items-center gap-2">
                <x-student.figma-icon name="icon-certificates.svg" box="size-4" />
                {{ __('student.assign_grade_section') }}
            </h3>
            @if($submission->score !== null)
                <p class="text-sm m-0">
                    <span class="font-bold text-[var(--sp-muted)]">{{ __('student.assign_score') }}:</span>
                    <span class="font-black text-[var(--sp-accent-text)] text-2xl ms-1">{{ $submission->score }}</span>
                    <span class="text-[var(--sp-muted)]">/ {{ $assignment->max_score }}</span>
                </p>
            @endif
            @if(in_array($submission->status, ['graded', 'returned'], true))
                <p class="text-sm m-0">
                    <span class="font-bold text-[var(--sp-muted)]">{{ __('student.assign_grade_status') }}:</span>
                    @if($submission->status === 'returned')
                        <span class="sp-pill sp-pill--progress ms-1">{{ __('student.assign_status_returned') }}</span>
                    @else
                        <span class="sp-pill sp-pill--done ms-1">{{ __('student.assign_status_graded') }}</span>
                    @endif
                </p>
            @endif
            @if($submission->feedback)
                <div>
                    <p class="text-xs font-bold text-[var(--sp-muted)] m-0 mb-1">{{ __('student.assign_feedback') }}</p>
                    <p class="text-sm m-0 whitespace-pre-wrap leading-relaxed bg-[#f7f7f5] rounded-[12px] p-4">{{ $submission->feedback }}</p>
                </div>
            @endif
        </section>
    @endif

    {{-- Previous submission summary --}}
    @if($submission)
        <section class="sp-card p-5 sm:p-6 space-y-4">
            <h3 class="font-extrabold text-sm m-0">{{ __('student.assign_last_submission') }}</h3>
            @if($submission->submitted_at)
                <p class="text-sm text-[var(--sp-muted)] m-0">
                    {{ __('student.assign_submitted_at') }}: {{ $submission->submitted_at->format('Y/m/d H:i') }}
                    <span class="opacity-70">({{ $submission->submitted_at->diffForHumans() }})</span>
                </p>
            @endif
            @if($submission->content)
                <div>
                    <p class="text-xs font-bold text-[var(--sp-muted)] m-0 mb-1">{{ __('student.assign_submit_content') }}</p>
                    <p class="text-sm m-0 whitespace-pre-wrap leading-relaxed">{{ $submission->content }}</p>
                </div>
            @endif
            @if($submission->attachments && count($submission->attachments))
                <div>
                    <p class="text-xs font-bold text-[var(--sp-muted)] m-0 mb-2">{{ __('student.assign_uploaded_attachments') }}</p>
                    <ul class="space-y-2 m-0 p-0 list-none">
                        @foreach($submission->attachments as $att)
                            @php
                                $path = is_string($att) ? $att : ($att['path'] ?? $att['url'] ?? null);
                                $url = is_array($att) && !empty($att['url'])
                                    ? $att['url']
                                    : ($path ? (str_starts_with($path, 'http') ? $path : url('storage/'.$path)) : '#');
                                $name = is_array($att) ? ($att['name'] ?? basename($path ?? 'attachment')) : basename($att);
                            @endphp
                            <li>
                                <a href="{{ $url }}" target="_blank" rel="noopener" class="sp-link inline-flex items-center gap-2 text-sm font-bold">
                                    <x-student.figma-icon name="icon-orders.svg" box="size-3.5" />
                                    {{ $name }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </section>
    @endif

    {{-- Submit form --}}
    <section class="sp-card p-5 sm:p-6 space-y-4">
        <div>
            <h3 class="font-extrabold text-sm m-0">{{ __('student.assign_submit_section') }}</h3>
            @if($submission)
                <p class="text-xs text-[var(--sp-muted)] m-0 mt-1">{{ __('student.assign_resubmit_hint') }}</p>
            @endif
        </div>

        @if(!$canSubmit)
            <div class="rounded-[16px] bg-[#f9e4d7] px-4 py-3 text-sm font-bold text-[#7a3b2e]">
                {{ __('student.assign_due_passed') }}
            </div>
        @else
            <form method="POST"
                  action="{{ route('student.assignments.submit', $assignment) }}"
                  enctype="multipart/form-data"
                  class="space-y-4">
                @csrf
                <div>
                    <label for="assign-content" class="block text-sm font-extrabold text-[var(--sp-text)] mb-2">
                        {{ __('student.assign_submit_content') }}
                    </label>
                    <textarea
                        id="assign-content"
                        name="content"
                        rows="5"
                        class="w-full rounded-[16px] border border-black/5 bg-[#f7f7f5] px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-[var(--sp-accent)]"
                        placeholder="{{ __('student.assign_submit_content_placeholder') }}"
                    >{{ old('content', $submission->content ?? '') }}</textarea>
                </div>
                <div>
                    <label for="assign-files" class="block text-sm font-extrabold text-[var(--sp-text)] mb-2">
                        {{ __('student.assign_submit_attachments') }}
                    </label>
                    <input
                        id="assign-files"
                        type="file"
                        name="attachments[]"
                        multiple
                        accept=".pdf,.doc,.docx,.zip,.rar,.jpg,.jpeg,.png,.ppt,.pptx"
                        class="block w-full text-sm text-[var(--sp-muted)] file:me-3 file:rounded-full file:border-0 file:bg-[var(--sp-accent)] file:px-4 file:py-2 file:text-sm file:font-extrabold file:text-[var(--sp-accent-text)]"
                    >
                    <p class="text-xs text-[var(--sp-muted)] m-0 mt-2">{{ __('student.assign_submit_attachments_hint') }}</p>
                </div>
                <button type="submit" class="sp-promo-btn !mt-0 border-0 cursor-pointer">
                    {{ $submission ? __('student.assign_resubmit') : __('student.assign_confirm_submit') }}
                </button>
            </form>
        @endif
    </section>
</div>
@endsection
