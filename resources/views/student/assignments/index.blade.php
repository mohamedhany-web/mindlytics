@extends('layouts.student-dashboard')

@section('title', __('student.assignments_page_title'))

@php
    $submittedCount = $assignments->filter(fn ($a) => $submissions->has($a->id))->count();
    $pendingCount = $assignments->count() - $submittedCount;
@endphp

@push('styles')
@include('student.offline-courses.partials.los-styles')
<style>
    .as-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(min(100%, 320px), 1fr));
        gap: 12px;
    }
    .as-card {
        display: flex; flex-direction: column;
        background: var(--ml-surface); border: 1px solid var(--ml-line);
        border-radius: var(--ml-r); overflow: hidden;
        transition: border-color var(--ml-fast) ease, box-shadow var(--ml-fast) ease;
    }
    .as-card:hover {
        border-color: rgba(73, 164, 162, 0.35);
        box-shadow: 0 10px 28px rgba(26, 34, 56, 0.06);
    }
    .as-card-head {
        display: flex; align-items: flex-start; justify-content: space-between; gap: 10px;
        padding: 14px 16px; background: var(--ml-well); border-bottom: 1px solid var(--ml-line);
    }
    .as-card-head h3 { margin: 0; font-size: 15px; font-weight: 700; line-height: 1.35; }
    .as-card-body { padding: 14px 16px; flex: 1; display: flex; flex-direction: column; gap: 12px; }
    .as-desc {
        margin: 0; font-size: 12px; color: var(--ml-muted); line-height: 1.55;
        display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden;
    }
    .as-meta { list-style: none; margin: 0; padding: 0; display: flex; flex-direction: column; gap: 6px; }
    .as-meta li {
        display: flex; align-items: flex-start; gap: 8px;
        font-size: 12px; color: var(--ml-muted); line-height: 1.45;
    }
    .as-meta li i { width: 14px; text-align: center; color: var(--ml-teal-deep); margin-top: 2px; flex-shrink: 0; }
    .as-meta strong { color: var(--ml-ink); font-weight: 700; }
    .as-card-foot { margin-top: auto; padding-top: 4px; }
    .as-card-foot .oc-btn { width: 100%; }
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
            <nav class="oc-crumb" aria-label="{{ __('student.learning_center') }}">
                <a href="{{ route('dashboard') }}">{{ __('student.learning_center') }}</a>
                <span aria-hidden="true">/</span>
                <span style="color:var(--ml-ink);font-weight:700">{{ __('student.assignments_page_title') }}</span>
            </nav>
            <h1>{{ __('student.assignments_page_title') }}</h1>
            <p class="sub">{{ __('student.assignments_subtitle') }}</p>
        </div>
        <div class="oc-signals">
            <span class="oc-signal oc-signal-live">{{ trans_choice('student.assignment_count', $assignments->count(), ['count' => $assignments->count()]) }}</span>
            @if($pendingCount > 0)
                <span class="oc-signal oc-signal-hot">{{ $pendingCount }} {{ __('student.assignment_pending_count') }}</span>
            @endif
        </div>
    </header>

    <section class="oc-stage" aria-label="{{ __('student.assignments_page_title') }}">
        <div class="oc-eyebrow">{{ __('student.assignments_view_submit') }}</div>
        <h2>{{ __('student.assignments_available_title') }}</h2>
        <p class="oc-copy">{{ __('student.assignments_subtitle') }}</p>
        <div class="oc-nav">
            <a class="oc-btn oc-btn-quiet" href="{{ route('my-courses.index') }}">
                <i class="fas fa-book-open text-xs"></i> {{ __('student.my_courses_link') }}
            </a>
        </div>
    </section>

    @if($assignments->count() > 0)
        <div class="oc-pulse" aria-label="{{ __('student.assignments_page_title') }}">
            <div>
                <span class="lbl">{{ __('student.assignments_available_title') }}</span>
                <span class="val teal">{{ $assignments->count() }}</span>
            </div>
            <div>
                <span class="lbl">{{ __('student.assignment_pending_count') }}</span>
                <span class="val hot">{{ $pendingCount }}</span>
            </div>
            <div>
                <span class="lbl">{{ __('student.assignment_submitted_count') }}</span>
                <span class="val">{{ $submittedCount }}</span>
            </div>
        </div>

        <p class="oc-section-title">{{ __('student.assignments_available_title') }}</p>
        <div class="as-grid">
            @foreach($assignments as $assignment)
                @php $sub = $submissions->get($assignment->id); @endphp
                <article class="as-card">
                    <div class="as-card-head">
                        <h3>{{ $assignment->title }}</h3>
                        @if($sub)
                            <span class="oc-badge oc-badge-ok">{{ __('student.assignment_submitted') }}</span>
                        @else
                            <span class="oc-badge oc-badge-warn">{{ __('student.assignment_pending') }}</span>
                        @endif
                    </div>
                    <div class="as-card-body">
                        @if($assignment->description)
                            <p class="as-desc">{{ $assignment->description }}</p>
                        @endif
                        <ul class="as-meta">
                            <li>
                                <i class="fas fa-book" aria-hidden="true"></i>
                                <span><strong>{{ __('student.assignment_course_label') }}:</strong> {{ $assignment->course->title ?? '—' }}</span>
                            </li>
                            @if($assignment->lesson)
                                <li>
                                    <i class="fas fa-list-alt" aria-hidden="true"></i>
                                    <span><strong>{{ __('student.assignment_lesson_label') }}:</strong> {{ $assignment->lesson->title }}</span>
                                </li>
                            @endif
                            @if($assignment->due_date)
                                <li>
                                    <i class="fas fa-calendar" aria-hidden="true"></i>
                                    <span><strong>{{ __('student.assignment_due_label') }}:</strong> {{ $assignment->due_date->format('Y-m-d H:i') }}</span>
                                </li>
                            @endif
                            <li>
                                <i class="fas fa-star" aria-hidden="true"></i>
                                <span><strong>{{ __('student.assignment_score_label') }}:</strong> {{ $assignment->max_score }}</span>
                            </li>
                        </ul>
                        <div class="as-card-foot">
                            <a href="{{ route('student.assignments.show', $assignment) }}" class="oc-btn">
                                <i class="fas fa-eye text-xs"></i>
                                {{ __('student.assignment_view') }}
                            </a>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
    @else
        <div class="oc-empty">
            <div class="icon"><i class="fas fa-tasks"></i></div>
            <h3>{{ __('student.assignment_no_available') }}</h3>
            <p>{{ __('student.assignment_no_available_desc') }}</p>
            <div style="margin-top:16px">
                <a href="{{ route('my-courses.index') }}" class="oc-btn">
                    <i class="fas fa-book-open text-xs"></i>
                    {{ __('student.view_my_courses') }}
                </a>
            </div>
        </div>
    @endif
</div>
@endsection
