@extends('layouts.student-dashboard')

@section('title', __('student.lp_page_title', ['name' => $learningPath->name]))
@section('header', $learningPath->name)

@php
    $bubbleColors = ['#f9e4d7', '#d7e8f9', '#d7eef5', '#f9f0d7', '#dcdef2'];
    $continue = $learningPath->continue_course;
    $enrolledAt = $enrollment->enrolled_at ?? $enrollment->created_at ?? null;
@endphp

@push('styles')
<style>
    .lp-intro-video .intro-video-container {
        position: relative;
        width: 100%;
        padding-bottom: 56.25%;
        height: 0;
        background: #1f1e31;
        border-radius: 20px;
        overflow: hidden;
    }
    .lp-intro-video .intro-video-container iframe,
    .lp-intro-video .intro-video-container video {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        border: 0;
    }
</style>
@endpush

@section('content')
<div class="space-y-5" x-data="window.__learningPathPage()">
    <nav class="flex flex-wrap items-center gap-2 text-sm font-bold text-[var(--sp-muted)]">
        <a href="{{ route('student.learning-path.index') }}" class="sp-link">{{ __('student.lp_index_title') }}</a>
        <x-student.figma-icon name="icon-chevron.svg" box="size-3" class="opacity-40 rtl:rotate-180" />
        <span class="text-[var(--sp-text)]">{{ $learningPath->name }}</span>
    </nav>

    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3">
        <div class="min-w-0">
            <p class="text-sm font-bold text-[var(--sp-muted)] m-0 mb-1">{{ __('student.lp_eyebrow') }}</p>
            <p class="text-sm text-[var(--sp-muted)] m-0 max-w-2xl">
                {{ __('student.lp_subtitle') }}
            </p>
        </div>
        @if($continue)
            @if($continue->is_enrolled)
                <a href="{{ route('my-courses.show', $continue) }}" class="sp-promo-btn !mt-0 self-start shrink-0">
                    <x-student.figma-icon name="icon-courses.svg" box="size-4" class="me-2" />
                    {{ $continue->is_completed ? __('student.lp_review_course') : __('student.continue_learning') }}
                </a>
            @else
                <a href="{{ route('courses.show', $continue) }}" class="sp-promo-btn !mt-0 self-start shrink-0">
                    <x-student.figma-icon name="icon-path.svg" box="size-4" class="me-2" />
                    {{ __('student.lp_start_next') }}
                </a>
            @endif
        @endif
    </div>

    {{-- Hero --}}
    <section class="sp-card p-5 sm:p-6 overflow-hidden relative">
        <div class="absolute inset-0 pointer-events-none opacity-70"
             style="background: radial-gradient(ellipse 70% 80% at 100% 0%, rgba(174,217,234,0.45), transparent 55%), radial-gradient(ellipse 50% 60% at 0% 100%, rgba(249,228,215,0.35), transparent 50%);"></div>
        <div class="relative flex flex-col lg:flex-row lg:items-start gap-5">
            <div class="flex items-start gap-4 min-w-0 flex-1">
                <span class="sp-icon-bubble shrink-0 text-[var(--sp-accent-text)]" style="background:var(--sp-accent);width:56px;height:56px">
                    <x-student.figma-icon name="icon-path.svg" box="size-7" />
                </span>
                <div class="min-w-0">
                    <div class="flex flex-wrap items-center gap-2">
                        <h2 class="sp-section-title m-0">{{ $learningPath->name }}</h2>
                        @if($learningPath->code)
                            <span class="sp-pill sp-pill--progress">{{ $learningPath->code }}</span>
                        @endif
                    </div>
                    @if($learningPath->description)
                        <p class="text-sm text-[var(--sp-text)] m-0 mt-3 leading-relaxed max-w-3xl">{{ $learningPath->description }}</p>
                    @endif
                    @if($enrolledAt)
                        <p class="text-xs font-bold text-[var(--sp-muted)] m-0 mt-3 flex items-center gap-1.5">
                            <x-student.figma-icon name="icon-calendar.svg" box="size-3.5" />
                            {{ __('student.lp_enrolled_on') }}: {{ $enrolledAt->format('Y/m/d') }}
                        </p>
                    @endif
                </div>
            </div>

            <div class="shrink-0 w-full sm:w-auto sm:min-w-[200px] rounded-[20px] bg-white/90 border border-black/5 p-5 text-center">
                <p class="text-xs font-bold text-[var(--sp-muted)] m-0 uppercase tracking-wide">{{ __('student.lp_progress') }}</p>
                <p class="text-4xl font-black text-[var(--sp-accent-text)] m-0 mt-2 leading-none tabular-nums">{{ rtrim(rtrim(number_format($learningPath->progress, 1), '0'), '.') }}%</p>
                <div class="mt-3 w-full bg-[#f0f0ec] rounded-full h-2.5 overflow-hidden">
                    <div class="h-full rounded-full bg-[var(--sp-accent)] transition-all" style="width: {{ min(100, max(0, $learningPath->progress)) }}%"></div>
                </div>
                <p class="text-xs font-bold text-[var(--sp-muted)] m-0 mt-2">
                    {{ __('student.lp_completed_of', [
                        'done' => $learningPath->completed_courses_count,
                        'total' => $learningPath->courses_count,
                    ]) }}
                </p>
            </div>
        </div>
    </section>

    {{-- Stats --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
        <div class="sp-card p-4 sm:p-5">
            <div class="flex items-center justify-between gap-3">
                <div class="min-w-0">
                    <p class="text-xs font-bold text-[var(--sp-muted)] m-0 uppercase tracking-wide">{{ __('student.lp_stat_courses') }}</p>
                    <p class="text-2xl font-black text-[var(--sp-accent-text)] m-0 mt-1 leading-none">{{ $learningPath->courses_count }}</p>
                </div>
                <span class="sp-icon-bubble" style="background:var(--sp-sky)">
                    <x-student.figma-icon name="icon-courses.svg" />
                </span>
            </div>
        </div>
        <div class="sp-card p-4 sm:p-5">
            <div class="flex items-center justify-between gap-3">
                <div class="min-w-0">
                    <p class="text-xs font-bold text-[var(--sp-muted)] m-0 uppercase tracking-wide">{{ __('student.lp_stat_enrolled') }}</p>
                    <p class="text-2xl font-black text-[var(--sp-accent-text)] m-0 mt-1 leading-none">{{ $learningPath->enrolled_courses_count }}</p>
                </div>
                <span class="sp-icon-bubble" style="background:var(--sp-mint)">
                    <x-student.figma-icon name="icon-certificates.svg" />
                </span>
            </div>
        </div>
        <div class="sp-card p-4 sm:p-5">
            <div class="flex items-center justify-between gap-3">
                <div class="min-w-0">
                    <p class="text-xs font-bold text-[var(--sp-muted)] m-0 uppercase tracking-wide">{{ __('student.lp_stat_done') }}</p>
                    <p class="text-2xl font-black text-[var(--sp-accent-text)] m-0 mt-1 leading-none">{{ $learningPath->completed_courses_count }}</p>
                </div>
                <span class="sp-icon-bubble" style="background:var(--sp-badge-done)">
                    <x-student.figma-icon name="icon-star.svg" />
                </span>
            </div>
        </div>
        <div class="sp-card p-4 sm:p-5">
            <div class="flex items-center justify-between gap-3">
                <div class="min-w-0">
                    <p class="text-xs font-bold text-[var(--sp-muted)] m-0 uppercase tracking-wide">{{ __('student.lp_stat_remaining') }}</p>
                    <p class="text-2xl font-black text-[var(--sp-accent-text)] m-0 mt-1 leading-none">{{ $learningPath->remaining_courses_count }}</p>
                </div>
                <span class="sp-icon-bubble" style="background:var(--sp-peach)">
                    <x-student.figma-icon name="icon-calendar.svg" />
                </span>
            </div>
        </div>
    </div>

    {{-- Intro video --}}
    @if($learningPath->video_url)
        <section class="sp-card p-5 sm:p-6 lp-intro-video">
            <div class="flex items-center gap-2 mb-4">
                <span class="sp-icon-bubble !w-10 !h-10" style="background:var(--sp-lilac)">
                    <x-student.figma-icon name="icon-classes.svg" box="size-4" />
                </span>
                <div>
                    <h3 class="font-extrabold text-sm m-0">{{ __('student.lp_intro_video') }}</h3>
                    <p class="text-xs text-[var(--sp-muted)] m-0 mt-0.5">{{ __('student.lp_intro_video_hint') }}</p>
                </div>
            </div>
            @include('partials.intro-video-embed', [
                'url' => trim((string) $learningPath->video_url),
                'title' => __('student.lp_intro_video'),
            ])
        </section>
    @endif

    {{-- Courses --}}
    <section class="space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <h3 class="sp-section-title m-0">{{ __('student.lp_courses_title') }}</h3>
            <div class="flex flex-wrap gap-2">
                <button type="button"
                        @click="filter = 'all'"
                        :class="filter === 'all' ? 'bg-[var(--sp-accent)] text-[var(--sp-accent-text)]' : 'bg-[#f7f7f5] text-[var(--sp-muted)]'"
                        class="rounded-[30px] px-3.5 py-2 text-xs font-extrabold transition-colors">
                    {{ __('student.lp_filter_all') }} ({{ $learningPath->courses_count }})
                </button>
                <button type="button"
                        @click="filter = 'enrolled'"
                        :class="filter === 'enrolled' ? 'bg-[var(--sp-accent)] text-[var(--sp-accent-text)]' : 'bg-[#f7f7f5] text-[var(--sp-muted)]'"
                        class="rounded-[30px] px-3.5 py-2 text-xs font-extrabold transition-colors">
                    {{ __('student.lp_filter_enrolled') }}
                </button>
                <button type="button"
                        @click="filter = 'todo'"
                        :class="filter === 'todo' ? 'bg-[var(--sp-accent)] text-[var(--sp-accent-text)]' : 'bg-[#f7f7f5] text-[var(--sp-muted)]'"
                        class="rounded-[30px] px-3.5 py-2 text-xs font-extrabold transition-colors">
                    {{ __('student.lp_filter_todo') }}
                </button>
                <button type="button"
                        @click="filter = 'done'"
                        :class="filter === 'done' ? 'bg-[var(--sp-accent)] text-[var(--sp-accent-text)]' : 'bg-[#f7f7f5] text-[var(--sp-muted)]'"
                        class="rounded-[30px] px-3.5 py-2 text-xs font-extrabold transition-colors">
                    {{ __('student.lp_filter_done') }}
                </button>
            </div>
        </div>

        <div class="sp-card p-4">
            <div class="sp-search !shadow-none !bg-[#f7f7f5] w-full">
                <x-student.figma-icon name="icon-search.svg" box="size-5" />
                <input type="search"
                       x-model.trim="q"
                       placeholder="{{ __('student.lp_search_placeholder') }}"
                       class="!text-sm"
                       aria-label="{{ __('student.lp_search_placeholder') }}">
                <button type="button"
                        class="text-xs font-extrabold text-[var(--sp-muted)] hover:text-[var(--sp-accent-text)] shrink-0"
                        x-show="q.length"
                        x-cloak
                        @click="q=''">
                    {{ __('student.clear_search') }}
                </button>
            </div>
        </div>

        <div class="space-y-3" x-ref="list">
            @forelse($learningPath->courses as $index => $course)
                @php
                    $title = method_exists($course, 'localized') ? $course->localized('title') : ($course->title ?? '—');
                    $subjectName = $course->academicSubject->name ?? null;
                    $instructorName = $course->instructor->name ?? ($course->teacher->name ?? null);
                    $progressPct = (float) ($course->enrollment_progress ?? 0);
                    $status = $course->is_completed ? 'done' : ($course->is_enrolled ? 'enrolled' : 'todo');
                    $bubble = $bubbleColors[$index % count($bubbleColors)];
                    $price = (float) ($course->price ?? 0);
                @endphp
                <article
                    class="sp-card p-4 sm:p-5 lp-course-row"
                    data-title="{{ mb_strtolower((string) $title) }}"
                    data-subject="{{ mb_strtolower((string) $subjectName) }}"
                    data-instructor="{{ mb_strtolower((string) $instructorName) }}"
                    data-status="{{ $status }}"
                    x-show="matches($el)"
                    x-transition.opacity.duration.150ms
                >
                    <div class="flex flex-col sm:flex-row sm:items-center gap-4">
                        <div class="flex items-start gap-3 min-w-0 flex-1">
                            <div class="w-12 h-12 rounded-[16px] flex items-center justify-center font-black text-[var(--sp-accent-text)] shrink-0"
                                 style="background:{{ $bubble }}">
                                {{ $index + 1 }}
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="flex flex-wrap items-center gap-2 mb-1">
                                    <h4 class="font-extrabold text-[15px] m-0 leading-snug">{{ $title }}</h4>
                                    @if($course->is_completed)
                                        <span class="sp-pill sp-pill--done">{{ __('student.lp_badge_done') }}</span>
                                    @elseif($course->is_enrolled)
                                        <span class="sp-pill sp-pill--progress">{{ __('student.lp_badge_enrolled') }}</span>
                                    @else
                                        <span class="sp-pill sp-pill--upcoming">{{ __('student.lp_badge_available') }}</span>
                                    @endif
                                </div>
                                <div class="flex flex-wrap gap-x-3 gap-y-1 text-xs font-bold text-[var(--sp-muted)]">
                                    @if($subjectName)
                                        <span>{{ $subjectName }}</span>
                                    @endif
                                    @if(($course->lessons_count ?? 0) > 0)
                                        <span>{{ $course->lessons_count }} {{ __('student.lesson_singular') }}</span>
                                    @endif
                                    @if($instructorName)
                                        <span>{{ $instructorName }}</span>
                                    @endif
                                    @if($price > 0)
                                        <span>{{ number_format($price, 0) }} {{ __('student.lp_currency') }}</span>
                                    @else
                                        <span>{{ __('student.lp_free') }}</span>
                                    @endif
                                </div>

                                @if($course->is_enrolled)
                                    <div class="mt-3 max-w-md">
                                        <div class="flex items-center justify-between gap-2 mb-1">
                                            <span class="text-[11px] font-bold text-[var(--sp-muted)]">{{ __('student.progress') }}</span>
                                            <span class="text-[11px] font-extrabold text-[var(--sp-accent-text)]">{{ (int) $progressPct }}%</span>
                                        </div>
                                        <div class="w-full bg-[#f0f0ec] rounded-full h-2 overflow-hidden">
                                            <div class="h-full rounded-full bg-[var(--sp-accent)]" style="width: {{ min(100, max(0, $progressPct)) }}%"></div>
                                        </div>
                                    </div>
                                @elseif($course->description)
                                    <p class="text-sm text-[var(--sp-muted)] m-0 mt-2 line-clamp-2">{{ \Illuminate\Support\Str::limit(strip_tags($course->description), 140) }}</p>
                                @endif
                            </div>
                        </div>

                        <div class="shrink-0 flex sm:flex-col gap-2 w-full sm:w-auto">
                            @if($course->is_enrolled)
                                <a href="{{ route('my-courses.show', $course) }}"
                                   class="sp-promo-btn !mt-0 !py-2.5 !text-sm text-center flex-1 sm:flex-none sm:min-w-[148px]">
                                    {{ $course->is_completed ? __('student.lp_review_course') : __('student.continue_learning') }}
                                </a>
                            @else
                                <a href="{{ route('courses.show', $course) }}"
                                   class="inline-flex items-center justify-center rounded-[30px] px-4 py-2.5 text-sm font-extrabold bg-[#f7f7f5] text-[var(--sp-accent-text)] hover:bg-[var(--sp-accent)] transition-colors flex-1 sm:flex-none sm:min-w-[148px]">
                                    {{ __('student.lp_view_course') }}
                                </a>
                            @endif
                        </div>
                    </div>
                </article>
            @empty
                <div class="sp-card p-10 text-center">
                    <span class="sp-icon-bubble mx-auto mb-4" style="background:var(--sp-sky);width:56px;height:56px">
                        <x-student.figma-icon name="icon-courses.svg" box="size-7" />
                    </span>
                    <h3 class="font-extrabold text-lg m-0 mb-2">{{ __('student.lp_no_courses') }}</h3>
                    <p class="text-sm text-[var(--sp-muted)] m-0">{{ __('student.lp_no_courses_hint') }}</p>
                </div>
            @endforelse
        </div>

        @if($learningPath->courses_count > 0)
            <p class="text-sm text-[var(--sp-muted)] text-center m-0" x-show="visibleCount === 0" x-cloak>
                {{ __('student.lp_search_empty') }}
            </p>
        @endif
    </section>
</div>
@endsection

@push('scripts')
<script>
window.__learningPathPage = function () {
    return {
        q: '',
        filter: 'all',
        visibleCount: {{ $learningPath->courses_count }},
        normalize(s) {
            return (s || '').toString().toLowerCase().trim();
        },
        matches(el) {
            if (!el) return true;
            const status = el.dataset.status || '';
            if (this.filter === 'enrolled' && status !== 'enrolled' && status !== 'done') return false;
            if (this.filter === 'todo' && status !== 'todo') return false;
            if (this.filter === 'done' && status !== 'done') return false;
            const q = this.normalize(this.q);
            if (!q) return true;
            const hay = [el.dataset.title || '', el.dataset.subject || '', el.dataset.instructor || ''].join(' ');
            return hay.includes(q);
        },
        recount() {
            try {
                const list = this.$refs.list;
                if (!list) { this.visibleCount = 0; return; }
                const rows = Array.from(list.querySelectorAll('.lp-course-row'));
                this.visibleCount = rows.filter(a => this.matches(a)).length;
            } catch (e) {}
        },
        init() {
            this.$watch('q', () => this.recount());
            this.$watch('filter', () => this.recount());
            this.recount();
        }
    };
};
</script>
@endpush
