@extends('layouts.student-dashboard')

@section('title', $course->localized('title') . ' - ' . __('student.my_courses'))
@section('header', $course->localized('title'))

@php
    use App\Models\AdvancedExam;
    use App\Models\Assignment;
    use App\Models\CourseLesson;
    use App\Models\Exam;
    use App\Models\Lecture;
    use App\Models\LearningPattern;
    use App\Support\StudentFigmaAssets;
    use Illuminate\Support\Str;

    $sp = StudentFigmaAssets::urls();
    $progressService = app(\App\Services\CourseProgressService::class);
    $user = auth()->user();
    $progressPct = max(0, min(100, (int) round((float) $progress)));
    $isDone = $progressPct >= 100;
    $remaining = max(0, (int) $totalLessons - (int) $completedLessons);
    $bubbleColors = ['#f9e4d7', '#d7e8f9', '#d7eef5', '#f9f0d7', '#dcdef2'];
    $sectionMeta = [];
    $firstOpenId = null;
    $firstIncompleteHref = route('my-courses.learn', $course);

    if (isset($sections) && $sections->isNotEmpty()) {
        foreach ($sections as $section) {
            $items = $section->relationLoaded('activeItems') ? $section->activeItems : collect();
            $rows = [];
            $doneCount = 0;
            foreach ($items as $ci) {
                $entity = $ci->item ?? null;
                if (! $entity) {
                    continue;
                }
                $completed = $progressService->isItemCompletedForUser($entity, $user);
                if ($completed) {
                    $doneCount++;
                }

                $typeKey = 'other';
                $icon = 'icon-courses.svg';
                $bubble = $bubbleColors[count($rows) % count($bubbleColors)];
                $title = method_exists($entity, 'localized')
                    ? ($entity->localized('title') ?: ($entity->title ?? __('student.section_fallback')))
                    : ($entity->title ?? $entity->name ?? __('student.section_fallback'));
                $href = route('my-courses.learn', $course);
                $learnType = null;
                $learnId = $entity->id;

                if ($entity instanceof CourseLesson) {
                    $typeKey = 'lesson';
                    $icon = 'icon-courses.svg';
                    $learnType = 'lesson';
                    $href = route('my-courses.learn', $course) . '?type=lesson&id=' . $entity->id;
                } elseif ($entity instanceof Lecture) {
                    $typeKey = 'lecture';
                    $icon = 'icon-classes.svg';
                    $learnType = 'lecture';
                    $href = route('my-courses.learn', $course) . '?type=lecture&id=' . $entity->id;
                } elseif ($entity instanceof LearningPattern) {
                    $typeKey = 'pattern';
                    $icon = 'icon-path.svg';
                    $learnType = 'pattern';
                    $href = route('my-courses.learn', $course) . '?type=pattern&id=' . $entity->id;
                } elseif ($entity instanceof AdvancedExam || $entity instanceof Exam) {
                    $typeKey = 'exam';
                    $icon = 'icon-exams.svg';
                    $learnType = 'exam';
                    $href = route('my-courses.learn', $course) . '?type=exam&id=' . $entity->id;
                } elseif ($entity instanceof Assignment) {
                    $typeKey = 'assignment';
                    $icon = 'icon-orders.svg';
                    $learnType = 'assignment';
                    $href = route('my-courses.learn', $course) . '?type=assignment&id=' . $entity->id;
                }

                if (! $completed && $firstOpenId === null) {
                    $firstOpenId = 's' . $section->id;
                    $firstIncompleteHref = $href;
                }

                $rows[] = [
                    'title' => $title,
                    'type' => $typeKey,
                    'icon' => $icon,
                    'bubble' => $bubble,
                    'completed' => $completed,
                    'href' => $href,
                ];
            }
            $total = count($rows);
            $sectionMeta[] = [
                'id' => $section->id,
                'title' => $section->title ?? $section->name ?? __('student.section_fallback'),
                'description' => $section->description ?? null,
                'rows' => $rows,
                'done' => $doneCount,
                'total' => $total,
                'pct' => $total > 0 ? (int) round(($doneCount / $total) * 100) : 0,
            ];
        }
        if ($firstOpenId === null && count($sectionMeta)) {
            $firstOpenId = 's' . $sectionMeta[0]['id'];
        }
    }

    $ringDeg = (int) round($progressPct * 3.6);
@endphp

@push('styles')
<style>
    .sp-course-hub-hero {
        background: #2f2e43;
        border-radius: 30px;
        color: #fff;
        padding: 28px 24px;
        position: relative;
        overflow: hidden;
        box-shadow: var(--sp-shadow);
    }
    .sp-course-hub-hero::before {
        content: '';
        position: absolute;
        inset-inline-end: -40px;
        top: -60px;
        width: 220px;
        height: 220px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(174,217,234,0.28), transparent 70%);
        pointer-events: none;
    }
    .sp-course-hub-hero::after {
        content: '';
        position: absolute;
        inset-inline-start: -30px;
        bottom: -80px;
        width: 180px;
        height: 180px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(249,228,215,0.18), transparent 70%);
        pointer-events: none;
    }
    .sp-progress-ring {
        width: 132px;
        height: 132px;
        border-radius: 50%;
        background: conic-gradient(var(--sp-accent) {{ $ringDeg }}deg, rgba(255,255,255,0.12) 0deg);
        display: grid;
        place-items: center;
        flex-shrink: 0;
        position: relative;
        z-index: 1;
    }
    .sp-progress-ring-inner {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        background: #2f2e43;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
    }
    .sp-curriculum-section {
        background: #fff;
        border-radius: var(--sp-radius-card);
        box-shadow: var(--sp-shadow);
        overflow: hidden;
    }
    .sp-curriculum-section-btn {
        width: 100%;
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 16px 18px;
        background: transparent;
        border: 0;
        cursor: pointer;
        text-align: start;
        font-family: inherit;
        color: inherit;
    }
    .sp-curriculum-section-btn:hover { background: #fafaf8; }
    .sp-curriculum-chevron {
        width: 28px;
        height: 28px;
        border-radius: 10px;
        background: #f5f5f5;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: transform 0.2s ease;
        flex-shrink: 0;
    }
    .sp-curriculum-chevron.is-open { transform: rotate(180deg); }
    .sp-curriculum-body {
        padding: 0 14px 14px;
        display: flex;
        flex-direction: column;
        gap: 8px;
    }
    .sp-hub-sticky {
        position: sticky;
        top: 12px;
    }
    @media (max-width: 1023px) {
        .sp-hub-sticky { position: static; }
        .sp-progress-ring { width: 112px; height: 112px; }
        .sp-progress-ring-inner { width: 84px; height: 84px; }
    }
</style>
@endpush

@section('content')
<div class="space-y-5" x-data="{ openSection: @js($firstOpenId) }">
    <div class="flex items-center justify-between gap-3 flex-wrap">
        <a href="{{ route('my-courses.index') }}" class="sp-link inline-flex items-center gap-2">
            <img src="{{ $sp['chevron'] ?? StudentFigmaAssets::url('icon-chevron.svg') }}" alt="" class="size-4 rotate-180 rtl:rotate-0">
            {{ __('student.back_to_my_courses') }}
        </a>
        <span class="sp-pill {{ $isDone ? 'sp-pill--done' : 'sp-pill--progress' }}">
            {{ $isDone ? __('student.completed_badge') : __('student.active_badge') }}
        </span>
    </div>

    {{-- Hero hub --}}
    <section class="sp-course-hub-hero">
        <div class="relative z-[1] flex flex-col lg:flex-row lg:items-center gap-6 lg:gap-8">
            <div class="flex-1 min-w-0">
                <p class="text-sm font-bold text-white/60 m-0 mb-2">{{ __('student.course_hub_eyebrow') }}</p>
                <h2 class="text-2xl sm:text-[28px] font-extrabold m-0 leading-tight">{{ $course->localized('title') }}</h2>
                <p class="text-sm text-white/70 m-0 mt-3 flex flex-wrap gap-x-2 gap-y-1">
                    <span>{{ $course->academicSubject->name ?? __('student.course_fallback') }}</span>
                    <span class="opacity-40">·</span>
                    <span>{{ $course->teacher->name ?? '—' }}</span>
                    @if($course->academicYear)
                        <span class="opacity-40">·</span>
                        <span>{{ $course->academicYear->name }}</span>
                    @endif
                </p>
                @if($course->description)
                    <p class="text-sm text-white/55 m-0 mt-3 max-w-2xl leading-relaxed line-clamp-2">{{ Str::limit(strip_tags($course->description), 160) }}</p>
                @endif
                <div class="flex flex-wrap gap-3 mt-6">
                    <a href="{{ $firstIncompleteHref }}" class="sp-promo-btn !mt-0 !text-[var(--sp-accent-text)]">
                        {{ $isDone ? __('student.review_course') : ($progressPct > 0 ? __('student.continue_where_left') : __('student.start_learning')) }}
                    </a>
                    @if($isDone)
                        <a href="{{ route('student.certificates.claim', $course) }}" class="inline-flex items-center justify-center rounded-[20px] bg-white/10 hover:bg-white/15 px-5 py-3.5 text-sm font-extrabold text-white transition">
                            {{ __('student.claim_certificate') }}
                        </a>
                    @endif
                </div>
            </div>

            <div class="flex items-center gap-5 shrink-0">
                <div class="sp-progress-ring" role="img" aria-label="{{ __('student.percent_complete', ['pct' => $progressPct]) }}">
                    <div class="sp-progress-ring-inner">
                        <span class="text-2xl font-black text-[var(--sp-accent)] leading-none">{{ $progressPct }}%</span>
                        <span class="text-[10px] font-bold text-white/50 mt-1 uppercase tracking-wide">{{ __('student.progress') }}</span>
                    </div>
                </div>
                <div class="hidden sm:grid gap-2 min-w-[120px]">
                    <div class="rounded-2xl bg-white/8 px-3 py-2.5 border border-white/10">
                        <p class="text-[11px] font-bold text-white/50 m-0">{{ __('student.completed') }}</p>
                        <p class="text-lg font-black m-0 text-white">{{ (int) $completedLessons }}/{{ (int) $totalLessons }}</p>
                    </div>
                    <div class="rounded-2xl bg-white/8 px-3 py-2.5 border border-white/10">
                        <p class="text-[11px] font-bold text-white/50 m-0">{{ __('student.remaining_items') }}</p>
                        <p class="text-lg font-black m-0 text-white">{{ $remaining }}</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="grid gap-5 xl:grid-cols-[minmax(0,1.7fr)_minmax(280px,1fr)]">
        {{-- MAIN: interactive curriculum --}}
        <div class="space-y-4 min-w-0">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <h3 class="sp-section-title m-0">{{ __('student.curriculum') }}</h3>
                    <p class="text-sm text-[var(--sp-muted)] m-0 mt-1">{{ __('student.curriculum_hub_hint') }}</p>
                </div>
                <a href="{{ route('my-courses.learn', $course) }}" class="sp-link shrink-0">{{ __('student.open_in_learn') }}</a>
            </div>

            @if(count($sectionMeta))
                <div class="space-y-3">
                    @foreach($sectionMeta as $index => $sec)
                        @php $sid = 's' . $sec['id']; @endphp
                        <div class="sp-curriculum-section">
                            <button
                                type="button"
                                class="sp-curriculum-section-btn"
                                @click="openSection = openSection === '{{ $sid }}' ? null : '{{ $sid }}'"
                                :aria-expanded="(openSection === '{{ $sid }}').toString()"
                            >
                                <span class="sp-icon-bubble" style="background:{{ $bubbleColors[$index % count($bubbleColors)] }}">
                                    <x-student.figma-icon name="icon-path.svg" />
                                </span>
                                <span class="flex-1 min-w-0 text-start">
                                    <span class="block font-extrabold text-[15px] truncate">{{ $sec['title'] }}</span>
                                    <span class="block text-xs font-bold text-[var(--sp-muted)] mt-1">
                                        {{ __('student.section_progress_count', ['done' => $sec['done'], 'total' => $sec['total']]) }}
                                    </span>
                                </span>
                                <span class="sp-pill {{ $sec['pct'] >= 100 ? 'sp-pill--done' : 'sp-pill--progress' }} !py-1.5 !px-2.5 !text-xs">
                                    {{ $sec['pct'] }}%
                                </span>
                                <span class="sp-curriculum-chevron" :class="openSection === '{{ $sid }}' && 'is-open'">
                                    <img src="{{ StudentFigmaAssets::url('icon-dropdown.svg') }}" alt="" class="size-2.5">
                                </span>
                            </button>

                            <div class="sp-curriculum-body" x-show="openSection === '{{ $sid }}'" x-cloak x-transition>
                                @if(!empty($sec['description']))
                                    <p class="text-xs text-[var(--sp-muted)] m-0 mb-1 px-1">{{ Str::limit(strip_tags($sec['description']), 140) }}</p>
                                @endif
                                @forelse($sec['rows'] as $row)
                                    <a href="{{ $row['href'] }}" class="sp-process-row !shadow-none border border-[#f0f0ec]">
                                        <span class="sp-icon-bubble !w-10 !h-10" style="background:{{ $row['bubble'] }}">
                                            <x-student.figma-icon :name="$row['icon']" box="size-5" />
                                        </span>
                                        <span class="flex-1 min-w-0">
                                            <span class="block font-extrabold text-sm truncate">{{ $row['title'] }}</span>
                                            <span class="block text-xs font-bold text-[var(--sp-muted)] mt-0.5">
                                                {{ __('student.curriculum_type_' . $row['type']) }}
                                            </span>
                                        </span>
                                        <span class="sp-pill {{ $row['completed'] ? 'sp-pill--done' : 'sp-pill--upcoming' }} !py-1.5 !px-2.5 !text-xs">
                                            {{ $row['completed'] ? __('student.completed_badge') : __('student.ready_to_start') }}
                                        </span>
                                    </a>
                                @empty
                                    <p class="text-sm text-[var(--sp-muted)] text-center py-4 m-0">{{ __('student.section_empty') }}</p>
                                @endforelse
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="sp-card p-10 text-center">
                    <span class="sp-icon-bubble mx-auto mb-4" style="background:var(--sp-sky);width:56px;height:56px">
                        <x-student.figma-icon name="icon-path.svg" box="size-7" />
                    </span>
                    <h3 class="font-extrabold text-lg m-0 mb-2">{{ __('student.curriculum_empty_title') }}</h3>
                    <p class="text-sm text-[var(--sp-muted)] m-0 mb-5">{{ __('student.curriculum_empty_desc') }}</p>
                    <a href="{{ route('my-courses.learn', $course) }}" class="sp-promo-btn inline-flex">{{ __('student.open_in_learn') }}</a>
                </div>
            @endif

            @if($course->description)
                <section class="sp-card p-5 sm:p-6">
                    <h3 class="sp-section-title mb-3">{{ __('student.course_overview') }}</h3>
                    <p class="text-sm text-[var(--sp-muted)] leading-relaxed m-0 whitespace-pre-line">{{ $course->description }}</p>
                </section>
            @endif
        </div>

        {{-- RAIL --}}
        <aside class="space-y-4 min-w-0 sp-hub-sticky">
            <section class="sp-card p-5">
                <p class="text-xs font-bold text-[var(--sp-muted)] uppercase tracking-wide m-0 mb-2">{{ __('student.next_step') }}</p>
                <h3 class="font-extrabold text-base m-0 mb-3 leading-snug">
                    {{ $isDone ? __('student.course_finished_cta') : __('student.keep_momentum') }}
                </h3>
                <a href="{{ $firstIncompleteHref }}" class="sp-promo-btn !mt-0 w-full !text-[var(--sp-accent-text)]">
                    {{ $isDone ? __('student.review_course') : __('student.continue_learning') }}
                </a>
                @if($isDone)
                    <a href="{{ route('student.certificates.claim', $course) }}" class="mt-3 block text-center text-sm font-extrabold text-[var(--sp-accent-text)] sp-link">
                        {{ __('student.claim_certificate_design') }}
                    </a>
                @endif
            </section>

            <section class="sp-card p-5">
                <div class="flex items-center gap-3 mb-4">
                    <span class="sp-icon-bubble" style="background:var(--sp-peach)">
                        <x-student.figma-icon name="icon-profile.svg" />
                    </span>
                    <div class="min-w-0">
                        <p class="text-xs font-bold text-[var(--sp-muted)] m-0">{{ __('student.teacher_label') }}</p>
                        <p class="font-extrabold text-[15px] m-0 truncate">{{ $course->teacher->name ?? '—' }}</p>
                    </div>
                </div>
                <div class="space-y-2">
                    <div class="flex items-center justify-between gap-3 rounded-[14px] bg-[#f7f7f5] px-3 py-2.5">
                        <span class="text-sm font-bold text-[var(--sp-muted)]">{{ __('student.level_label') }}</span>
                        <span class="text-sm font-extrabold">{{ $course->level ?? '—' }}</span>
                    </div>
                    <div class="flex items-center justify-between gap-3 rounded-[14px] bg-[#f7f7f5] px-3 py-2.5">
                        <span class="text-sm font-bold text-[var(--sp-muted)]">{{ __('student.duration_hours_label') }}</span>
                        <span class="text-sm font-extrabold">{{ $course->duration_hours ?? '—' }} {{ __('student.hours_unit') }}</span>
                    </div>
                    <div class="flex items-center justify-between gap-3 rounded-[14px] bg-[#f7f7f5] px-3 py-2.5">
                        <span class="text-sm font-bold text-[var(--sp-muted)]">{{ __('student.points_label') }}</span>
                        <span class="inline-flex items-center gap-1 text-sm font-extrabold">
                            <img src="{{ $sp['star'] ?? StudentFigmaAssets::url('icon-star.svg') }}" alt="" class="size-4">
                            {{ number_format((float) ($coursePoints ?? 0), 0) }}
                        </span>
                    </div>
                </div>
            </section>

            <section class="sp-card p-5">
                <h3 class="sp-section-title mb-4">{{ __('student.your_stats') }}</h3>
                <div class="grid grid-cols-2 gap-3">
                    <div class="rounded-[16px] bg-[var(--sp-mint)] p-3 text-center">
                        <p class="text-xl font-black text-[var(--sp-accent-text)] m-0">{{ (int) $completedLessons }}</p>
                        <p class="text-[11px] font-bold text-[var(--sp-muted)] m-0 mt-1">{{ __('student.completed') }}</p>
                    </div>
                    <div class="rounded-[16px] bg-[var(--sp-lilac)] p-3 text-center">
                        <p class="text-xl font-black text-[var(--sp-accent-text)] m-0">{{ (int) $totalLessons }}</p>
                        <p class="text-[11px] font-bold text-[var(--sp-muted)] m-0 mt-1">{{ __('student.lessons_count') }}</p>
                    </div>
                    <div class="rounded-[16px] bg-[var(--sp-amber-soft)] p-3 text-center">
                        <p class="text-xl font-black text-[var(--sp-accent-text)] m-0">{{ $progressPct }}%</p>
                        <p class="text-[11px] font-bold text-[var(--sp-muted)] m-0 mt-1">{{ __('student.progress') }}</p>
                    </div>
                    <div class="rounded-[16px] bg-[var(--sp-peach)] p-3 text-center">
                        <p class="text-xl font-black text-[var(--sp-accent-text)] m-0">{{ $remaining }}</p>
                        <p class="text-[11px] font-bold text-[var(--sp-muted)] m-0 mt-1">{{ __('student.remaining_items') }}</p>
                    </div>
                </div>
            </section>
        </aside>
    </div>
</div>
@endsection
