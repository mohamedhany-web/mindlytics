@extends('layouts.student-dashboard')

@section('title', $assignment->title)

@php
    $statusKey = match ($submission?->status) {
        'graded' => 'student.assignment_status_graded',
        'submitted' => 'student.assignment_status_submitted',
        'draft' => 'student.assignment_status_draft',
        default => null,
    };
@endphp

@push('styles')
@include('student.offline-courses.partials.los-styles')
<style>
    .as-layout {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 300px;
        gap: 20px;
        align-items: start;
    }
    @media (max-width: 999px) {
        .as-layout { grid-template-columns: 1fr; }
    }
    .as-prose {
        margin: 0; font-size: 14px; line-height: 1.75; color: var(--ml-ink);
        white-space: pre-wrap; word-break: break-word;
    }
    .as-instruct {
        margin-top: 12px; padding: 14px 16px; border-radius: var(--ml-r);
        border: 1px solid rgba(73, 164, 162, 0.3);
        background: rgba(73, 164, 162, 0.08);
        font-size: 13px; line-height: 1.7; color: var(--ml-ink);
        white-space: pre-wrap;
    }
    .as-form label {
        display: block; margin-bottom: 6px;
        font-size: 12px; font-weight: 700; color: var(--ml-ink);
    }
    .as-form textarea,
    .as-form input[type="file"] {
        width: 100%; border: 1px solid var(--ml-line); border-radius: 12px;
        background: var(--ml-surface); color: var(--ml-ink);
        font-family: inherit; font-size: 13px;
    }
    .as-form textarea {
        padding: 12px 14px; min-height: 120px; resize: vertical; line-height: 1.6;
    }
    .as-form textarea:focus {
        outline: none; border-color: rgba(73, 164, 162, 0.55);
        box-shadow: 0 0 0 3px rgba(73, 164, 162, 0.15);
    }
    .as-form .hint { margin: 6px 0 0; font-size: 11px; color: var(--ml-muted); }
    .as-form .field { margin-bottom: 14px; }
    .as-sub-meta { font-size: 13px; color: var(--ml-muted); line-height: 1.7; }
    .as-sub-meta strong { color: var(--ml-ink); }
    .as-attach { list-style: none; margin: 10px 0 0; padding: 0; display: flex; flex-direction: column; gap: 6px; }
    .as-attach a {
        display: inline-flex; align-items: center; gap: 8px;
        font-size: 13px; font-weight: 600; color: var(--ml-teal-deep); text-decoration: none;
    }
    .as-attach a:hover { text-decoration: underline; }
    .as-feedback {
        margin-top: 10px; padding: 12px 14px; border-radius: 10px;
        background: var(--ml-well); border: 1px solid var(--ml-line);
        font-size: 13px; line-height: 1.65; white-space: pre-wrap; color: var(--ml-ink);
    }
    .as-aside-sticky { display: flex; flex-direction: column; gap: 12px; }
    @media (min-width: 1000px) {
        .as-aside-sticky { position: sticky; top: 12px; }
    }
</style>
@endpush

@section('content')
<div class="oc">
    @if(session('success'))
        <div class="oc-panel" style="border-color:rgba(16,185,129,0.35);background:rgba(16,185,129,0.08);margin-bottom:16px;color:#047857;font-size:13px;font-weight:600">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="oc-panel" style="border-color:rgba(239,68,68,0.35);background:rgba(239,68,68,0.08);margin-bottom:16px;color:#b91c1c;font-size:13px;font-weight:600">
            {{ session('error') }}
        </div>
    @endif

    <header class="oc-chrome">
        <div>
            <nav class="oc-crumb" aria-label="{{ __('student.assignments_page_title') }}">
                <a href="{{ route('dashboard') }}">{{ __('student.learning_center') }}</a>
                <span aria-hidden="true">/</span>
                <a href="{{ route('student.assignments.index') }}">{{ __('student.assignments_page_title') }}</a>
                <span aria-hidden="true">/</span>
                <span style="color:var(--ml-ink);font-weight:700">{{ \Illuminate\Support\Str::limit($assignment->title, 36) }}</span>
            </nav>
            <h1>{{ $assignment->title }}</h1>
            <p class="sub">{{ $assignment->course->title ?? '—' }}</p>
        </div>
        <div class="oc-signals">
            @if($submission)
                <span class="oc-signal oc-signal-live">{{ __('student.assignment_submitted') }}</span>
            @else
                <span class="oc-signal oc-signal-hot">{{ __('student.assignment_pending') }}</span>
            @endif
        </div>
    </header>

    <div class="as-layout">
        <div>
            <section class="oc-panel" aria-labelledby="as-details">
                <p class="oc-label" id="as-details">{{ __('student.assignment_details') }}</p>
                @if($assignment->description)
                    <p class="oc-label" style="margin-top:4px">{{ __('student.assignment_description') }}</p>
                    <p class="as-prose">{{ $assignment->description }}</p>
                @endif
                @if($assignment->instructions)
                    <p class="oc-label" style="margin-top:14px">{{ __('student.assignment_instructions') }}</p>
                    <div class="as-instruct">{{ $assignment->instructions }}</div>
                @endif
                <ul class="oc-facts" style="margin-top:16px">
                    <li>
                        <span class="k">{{ __('student.assignment_score_label') }}</span>
                        <span class="v">{{ $assignment->max_score }}</span>
                    </li>
                    <li>
                        <span class="k">{{ __('student.assignment_due_label') }}</span>
                        <span class="v">{{ $assignment->due_date ? $assignment->due_date->format('Y-m-d H:i') : __('student.not_specified') }}</span>
                    </li>
                    <li>
                        <span class="k">{{ __('student.assignment_late_allowed') }}</span>
                        <span class="v">{{ $assignment->allow_late_submission ? __('student.assignment_late_yes') : __('student.assignment_late_no') }}</span>
                    </li>
                    @if($assignment->lesson)
                        <li>
                            <span class="k">{{ __('student.assignment_lesson_label') }}</span>
                            <span class="v">{{ $assignment->lesson->title }}</span>
                        </li>
                    @endif
                </ul>
            </section>

            <section class="oc-panel" aria-labelledby="as-submit">
                <p class="oc-label" id="as-submit">{{ __('student.assignment_submit_section') }}</p>
                <form method="POST" action="{{ route('student.assignments.submit', $assignment) }}" enctype="multipart/form-data" class="as-form">
                    @csrf
                    <div class="field">
                        <label for="as-content">{{ __('student.assignment_content_label') }}</label>
                        <textarea id="as-content" name="content" rows="5" placeholder="{{ __('student.assignment_content_placeholder') }}">{{ old('content', $submission->content ?? '') }}</textarea>
                        @error('content')
                            <p class="hint" style="color:#b91c1c">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="field">
                        <label for="as-files">{{ __('student.assignment_attachments_label') }}</label>
                        <input id="as-files" type="file" name="attachments[]" multiple />
                        <p class="hint">{{ __('student.assignment_attachments_hint') }}</p>
                        @error('attachments.*')
                            <p class="hint" style="color:#b91c1c">{{ $message }}</p>
                        @enderror
                    </div>
                    <button type="submit" class="oc-btn">
                        <i class="fas fa-upload text-xs"></i>
                        {{ __('student.assignment_submit_btn') }}
                    </button>
                </form>

                @if($submission)
                    <div style="margin-top:20px;padding-top:16px;border-top:1px solid var(--ml-line)">
                        <p class="oc-label">{{ __('student.assignment_last_submission') }}</p>
                        <div class="as-sub-meta">
                            <p>
                                {{ __('student.assignment_status_label') }}:
                                <strong>{{ $statusKey ? __($statusKey) : $submission->status }}</strong>
                            </p>
                            @if($submission->submitted_at)
                                <p>{{ __('student.assignment_submitted_at') }}: <strong>{{ $submission->submitted_at->format('Y-m-d H:i') }}</strong></p>
                            @endif
                            @if($submission->score !== null)
                                <p>
                                    {{ __('student.assignment_score_label') }}:
                                    <strong style="color:var(--ml-teal-deep)">{{ $submission->score }}</strong>
                                    / {{ $assignment->max_score }}
                                </p>
                            @endif
                        </div>
                        @if($submission->feedback)
                            <p class="oc-label" style="margin-top:12px">{{ __('student.assignment_feedback') }}</p>
                            <div class="as-feedback">{{ $submission->feedback }}</div>
                        @endif
                        @if($submission->attachments && count($submission->attachments))
                            <p class="oc-label" style="margin-top:12px">{{ __('student.assignment_attachments_list') }}</p>
                            <ul class="as-attach">
                                @foreach($submission->attachments as $att)
                                    @php
                                        $path = is_string($att) ? $att : ($att['path'] ?? $att['url'] ?? null);
                                        $url = is_array($att) && !empty($att['url']) ? $att['url'] : ($path ? (str_starts_with($path, 'http') ? $path : url('storage/'.$path)) : '#');
                                        $name = is_array($att) ? ($att['name'] ?? basename($path ?? 'attachment')) : basename($att);
                                    @endphp
                                    <li>
                                        <a href="{{ $url }}" target="_blank" rel="noopener">
                                            <i class="fas fa-paperclip text-xs"></i> {{ $name }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                @endif
            </section>
        </div>

        <aside class="as-aside-sticky">
            <div class="oc-panel">
                <p class="oc-label">{{ __('student.assignment_course_label') }}</p>
                <p style="margin:0;font-size:14px;font-weight:700;line-height:1.4">{{ $assignment->course->title ?? '—' }}</p>
                @if($assignment->teacher)
                    <p style="margin:8px 0 0;font-size:12px;color:var(--ml-muted)">{{ $assignment->teacher->name }}</p>
                @endif
            </div>
            <div class="oc-panel">
                <a href="{{ route('student.assignments.index') }}" class="oc-btn oc-btn-quiet" style="width:100%">
                    <i class="fas fa-arrow-right text-xs"></i>
                    {{ __('student.assignment_back') }}
                </a>
            </div>
        </aside>
    </div>
</div>
@endsection
