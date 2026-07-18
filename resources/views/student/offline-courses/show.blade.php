@extends('layouts.student-dashboard')

@section('title', $offlineCourse->title)

@php
    $sg = $studentRouteGroup ?? 'student.offline-courses';
    $isOnline = ($channel ?? 'offline') === 'online';
    $channelLabel = $isOnline ? __('student.online_badge') : __('student.offline_badge');
    $listTitle = $isOnline ? __('student.my_online_courses') : __('student.offline_courses_title');
    $isRtl = app()->getLocale() === 'ar';

    $resourcesCount = $offlineCourse->resources()
        ->active()
        ->when($enrollment->group_id, fn ($q) => $q->where(function ($x) use ($enrollment) {
            $x->whereNull('group_id')->orWhere('group_id', $enrollment->group_id);
        }))
        ->count();

    $lecturesCount = $offlineCourse->offlineLectures()
        ->active()
        ->when($enrollment->group_id, fn ($q) => $q->where(function ($x) use ($enrollment) {
            $x->whereNull('group_id')->orWhere('group_id', $enrollment->group_id);
        }))
        ->count();

    $activitiesCount = $offlineCourse->activities()
        ->where('status', 'published')
        ->when($enrollment->group_id, fn ($q) => $q->where(function ($x) use ($enrollment) {
            $x->whereNull('group_id')->orWhere('group_id', $enrollment->group_id);
        }))
        ->count();

    $examsCount = \App\Models\AdvancedExam::query()
        ->where('offline_course_id', $offlineCourse->id)
        ->where('is_active', true)
        ->where('is_published', true)
        ->count();
@endphp

@push('styles')
@include('student.offline-courses.partials.los-styles')
@endpush

@section('content')
<div class="oc">
    <header class="oc-chrome">
        <div>
            <nav class="oc-crumb" aria-label="{{ __('student.oc_breadcrumb') }}">
                <a href="{{ route('dashboard') }}">{{ __('los.page_title') }}</a>
                <span aria-hidden="true">/</span>
                <a href="{{ route($sg . '.index') }}">{{ $listTitle }}</a>
                <span aria-hidden="true">/</span>
                <span style="color:var(--ml-ink);font-weight:700">{{ \Illuminate\Support\Str::limit($offlineCourse->title, 36) }}</span>
            </nav>
            <h1>{{ $offlineCourse->title }}</h1>
            <p class="sub">
                {{ $offlineCourse->instructor->name ?? '—' }}
                @if($enrollment->group)
                    · {{ $enrollment->group->name }}
                @endif
            </p>
        </div>
        <div class="oc-signals">
            <span class="oc-signal oc-signal-live">{{ $channelLabel }}</span>
            <span class="oc-signal">{{ __('student.oc_progress', ['pct' => number_format($enrollment->progress, 0)]) }}</span>
        </div>
    </header>

    <section class="oc-stage" aria-label="{{ __('student.oc_overview') }}">
        <div class="oc-eyebrow">{{ __('student.oc_course_space') }} <em>{{ $channelLabel }}</em></div>
        <h2>{{ $offlineCourse->title }}</h2>
        @if(filled($offlineCourse->description))
            <p class="oc-copy">{{ \Illuminate\Support\Str::limit($offlineCourse->description, 220) }}</p>
        @endif
        <div class="oc-meter" role="progressbar" aria-valuenow="{{ (int) $enrollment->progress }}" aria-valuemin="0" aria-valuemax="100">
            <i style="width:{{ min(100, (float) $enrollment->progress) }}%"></i>
        </div>
        <div class="oc-nav">
            <a class="oc-chip" href="{{ route($sg . '.curriculum', $offlineCourse) }}"><i class="fas fa-sitemap"></i> {{ __('student.oc_curriculum') }}</a>
            <a class="oc-chip" href="{{ route($sg . '.schedule', $offlineCourse) }}"><i class="fas fa-calendar-alt"></i> {{ __('student.oc_schedule') }}</a>
            <a class="oc-chip" href="{{ route($sg . '.resources', $offlineCourse) }}"><i class="fas fa-file-alt"></i> {{ __('student.oc_resources') }}</a>
            <a class="oc-chip" href="{{ route($sg . '.lectures', $offlineCourse) }}"><i class="fas fa-chalkboard-teacher"></i> {{ __('student.oc_lectures') }}</a>
            <a class="oc-chip" href="{{ route('student.exams.index') }}"><i class="fas fa-clipboard-check"></i> {{ __('student.oc_exams') }}</a>
        </div>
    </section>

    <ul class="oc-facts" style="margin-bottom:20px">
        <li>
            <span class="k">{{ __('student.oc_instructor') }}</span>
            <span class="v">{{ $offlineCourse->instructor->name ?? '—' }}</span>
        </li>
        @if($offlineCourse->locationModel || $offlineCourse->location)
            <li>
                <span class="k">{{ $isOnline ? __('student.oc_platform_location') : __('student.oc_location') }}</span>
                <span class="v">{{ $offlineCourse->locationModel->name ?? $offlineCourse->location ?? '—' }}</span>
            </li>
        @endif
        @if($offlineCourse->start_date)
            <li>
                <span class="k">{{ __('student.oc_start_date') }}</span>
                <span class="v">{{ $offlineCourse->start_date->format('Y-m-d') }}</span>
            </li>
        @endif
        @if($enrollment->group)
            <li>
                <span class="k">{{ __('student.oc_group') }}</span>
                <span class="v">{{ $enrollment->group->name }}</span>
            </li>
        @endif
    </ul>

    <div class="oc-hub" aria-label="{{ __('student.oc_sections') }}">
        <a href="{{ route($sg . '.curriculum', $offlineCourse) }}">
            <span class="ico"><i class="fas fa-sitemap"></i></span>
            <strong>{{ __('student.oc_hub_curriculum') }}</strong>
            <span>{{ __('student.oc_hub_curriculum_desc') }}</span>
        </a>
        <a href="{{ route($sg . '.schedule', $offlineCourse) }}">
            <span class="ico"><i class="fas fa-calendar-alt"></i></span>
            <strong>{{ __('student.oc_hub_schedule') }}</strong>
            <span>{{ __('student.oc_hub_schedule_desc') }}</span>
        </a>
        <a href="{{ route($sg . '.resources', $offlineCourse) }}">
            <span class="count">{{ $resourcesCount }}</span>
            <span class="ico"><i class="fas fa-file-alt"></i></span>
            <strong>{{ __('student.oc_resources') }}</strong>
            <span>{{ __('student.oc_hub_resources_desc') }}</span>
        </a>
        <a href="{{ route($sg . '.lectures', $offlineCourse) }}">
            <span class="count">{{ $lecturesCount }}</span>
            <span class="ico"><i class="fas fa-chalkboard-teacher"></i></span>
            <strong>{{ __('student.oc_lectures') }}</strong>
            <span>{{ __('student.oc_hub_lectures_desc') }}</span>
        </a>
        <a href="#activities-required">
            <span class="count">{{ $activitiesCount }}</span>
            <span class="ico"><i class="fas fa-tasks"></i></span>
            <strong>{{ __('student.oc_hub_activities') }}</strong>
            <span>{{ __('student.oc_hub_activities_desc') }}</span>
        </a>
        <a href="{{ route('student.exams.index') }}">
            <span class="count">{{ $examsCount }}</span>
            <span class="ico"><i class="fas fa-clipboard-check"></i></span>
            <strong>{{ __('student.oc_exams') }}</strong>
            <span>{{ __('student.oc_hub_exams_desc') }}</span>
        </a>
    </div>

    @if($enrollment->group)
        <div class="oc-panel" style="display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:12px">
            <div>
                <p class="oc-label" style="margin-bottom:4px">{{ __('student.oc_sessions_schedule') }}</p>
                <p style="margin:0;font-size:14px;font-weight:700">
                    {{ __('student.oc_group_name', ['name' => $enrollment->group->name]) }}
                    @if(($enrollment->group->sessions_count ?? 0) > 0)
                        — {{ __('student.oc_sessions_count', ['count' => $enrollment->group->sessions_count]) }}
                    @endif
                </p>
            </div>
            <a class="oc-btn" href="{{ route($sg . '.schedule', $offlineCourse) }}">
                <i class="fas fa-calendar-alt text-xs"></i> {{ __('student.oc_open_calendar') }}
            </a>
        </div>
    @endif

    @if((float) $enrollment->total_amount > 0)
        <div class="oc-panel">
            <p class="oc-label">{{ __('student.oc_payment_status') }}</p>
            @php
                $pTexts = [
                    'paid' => __('student.oc_payment_paid'),
                    'partial' => __('student.oc_payment_partial'),
                    'unpaid' => __('student.oc_payment_unpaid'),
                ];
                $pBadge = ['paid' => 'oc-badge-ok', 'partial' => 'oc-badge-warn', 'unpaid' => 'oc-badge-bad'];
            @endphp
            <div class="oc-facts">
                <li>
                    <span class="k">{{ __('student.oc_status') }}</span>
                    <span class="v"><span class="oc-badge {{ $pBadge[$enrollment->payment_status] ?? '' }}">{{ $pTexts[$enrollment->payment_status] ?? '—' }}</span></span>
                </li>
                <li>
                    <span class="k">{{ __('student.oc_paid_amount') }}</span>
                    <span class="v">{{ number_format($enrollment->paid_amount, 2) }} {{ __('student.oc_currency') }}</span>
                </li>
                <li>
                    <span class="k">{{ __('student.oc_remaining') }}</span>
                    <span class="v" style="color:{{ (float) $enrollment->remaining_amount > 0 ? '#b91c1c' : '#047857' }}">
                        {{ number_format($enrollment->remaining_amount, 2) }} {{ __('student.oc_currency') }}
                    </span>
                </li>
            </div>
        </div>
    @endif

    <div id="activities-required">
        <p class="oc-section-title">{{ __('student.oc_required_activities') }}</p>
        @if($pendingActivities->count() > 0)
            <div class="oc-list">
                @foreach($pendingActivities as $activity)
                    <a href="{{ route($sg . '.activities.show', [$offlineCourse, $activity]) }}" class="oc-row">
                        <div class="oc-ico warn"><i class="fas fa-tasks"></i></div>
                        <div class="oc-body">
                            <h3>{{ $activity->title }}</h3>
                            <p class="meta">
                                {{ $activity->type }}
                                @if($activity->due_date) · {{ $activity->due_date->format('Y-m-d') }} @endif
                                · {{ __('student.oc_points', ['count' => $activity->max_score]) }}
                            </p>
                        </div>
                        <span class="oc-side">{{ __('student.oc_submit') }} <i class="fas fa-arrow-{{ $isRtl ? 'left' : 'right' }} text-[10px]"></i></span>
                    </a>
                @endforeach
            </div>
        @else
            <div class="oc-panel" style="font-size:13px;color:var(--ml-muted)">{{ __('student.oc_no_required_activities') }}</div>
        @endif
    </div>

    @if($completedActivities->count() > 0)
        <p class="oc-section-title" style="margin-top:20px">{{ __('student.oc_completed_activities') }}</p>
        <div class="oc-list">
            @foreach($completedActivities as $activity)
                @php $submission = $activity->submissions->firstWhere('student_id', auth()->id()); @endphp
                <a href="{{ route($sg . '.activities.show', [$offlineCourse, $activity]) }}" class="oc-row">
                    <div class="oc-ico" style="background:rgba(16,185,129,0.12);color:#047857"><i class="fas fa-check"></i></div>
                    <div class="oc-body">
                        <h3>{{ $activity->title }}</h3>
                        @if($submission && $submission->score !== null)
                            <p class="meta">{{ __('student.oc_graded', ['score' => $submission->score, 'max' => $activity->max_score]) }}</p>
                        @endif
                    </div>
                    <span class="oc-side">{{ __('student.oc_view') }}</span>
                </a>
            @endforeach
        </div>
    @endif
</div>
@endsection
