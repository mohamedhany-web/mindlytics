@extends('layouts.student-dashboard')

@section('title', __('student.my_courses_active_title'))
@section('header', __('student.my_courses_active_title'))

@php
    $isStudent = auth()->user()->role === 'student' || strtolower((string) auth()->user()->role) === 'student';
    $scholarshipOnlyPortal = $isStudent && auth()->user()->usesScholarshipOnlyPortal();
    $totalShown = $activeCourses->count();
    $bubbleColors = ['#f9e4d7', '#d7e8f9', '#d7eef5', '#f9f0d7', '#dcdef2'];
@endphp

@section('content')
<div class="space-y-5" x-data="window.__myCoursesPage({{ (int) $totalShown }})">
    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3">
        <div class="min-w-0">
            <p class="text-sm font-bold text-[var(--sp-muted)] m-0 mb-1">{{ __('student.my_courses_eyebrow') }}</p>
            <p class="text-sm text-[var(--sp-muted)] m-0 max-w-2xl">{{ __('student.my_courses_subtitle') }}</p>
        </div>
        @unless($scholarshipOnlyPortal)
            <a href="{{ route('academic-years') }}" class="sp-promo-btn !mt-0 self-start shrink-0">
                <x-student.figma-icon name="icon-search.svg" box="size-4" class="me-2" />
                {{ __('student.browse_new_courses') }}
            </a>
        @endunless
    </div>

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
        <div class="sp-card p-4 sm:p-5">
            <div class="flex items-center justify-between gap-3">
                <div class="min-w-0">
                    <p class="text-xs font-bold text-[var(--sp-muted)] m-0 uppercase tracking-wide">{{ __('student.active_label') }}</p>
                    <p class="text-2xl font-black text-[var(--sp-accent-text)] m-0 mt-1 leading-none">{{ $stats['total_active'] }}</p>
                </div>
                <span class="sp-icon-bubble" style="background:var(--sp-sky)">
                    <x-student.figma-icon name="icon-courses.svg" />
                </span>
            </div>
        </div>
        <div class="sp-card p-4 sm:p-5">
            <div class="flex items-center justify-between gap-3">
                <div class="min-w-0">
                    <p class="text-xs font-bold text-[var(--sp-muted)] m-0 uppercase tracking-wide">{{ __('student.completed') }}</p>
                    <p class="text-2xl font-black text-[var(--sp-accent-text)] m-0 mt-1 leading-none">{{ $stats['total_completed'] }}</p>
                </div>
                <span class="sp-icon-bubble" style="background:var(--sp-badge-done)">
                    <x-student.figma-icon name="icon-certificates.svg" />
                </span>
            </div>
        </div>
        <div class="sp-card p-4 sm:p-5">
            <div class="flex items-center justify-between gap-3">
                <div class="min-w-0">
                    <p class="text-xs font-bold text-[var(--sp-muted)] m-0 uppercase tracking-wide">{{ __('student.hours_label') }}</p>
                    <p class="text-2xl font-black text-[var(--sp-accent-text)] m-0 mt-1 leading-none">{{ $stats['total_hours'] }}</p>
                </div>
                <span class="sp-icon-bubble" style="background:var(--sp-peach)">
                    <x-student.figma-icon name="icon-calendar.svg" />
                </span>
            </div>
        </div>
        <div class="sp-card p-4 sm:p-5">
            <div class="flex items-center justify-between gap-3">
                <div class="min-w-0">
                    <p class="text-xs font-bold text-[var(--sp-muted)] m-0 uppercase tracking-wide">{{ __('student.avg_progress_label') }}</p>
                    <p class="text-2xl font-black text-[var(--sp-accent-text)] m-0 mt-1 leading-none">{{ $stats['avg_progress'] }}%</p>
                </div>
                <span class="sp-icon-bubble" style="background:var(--sp-lilac)">
                    <x-student.figma-icon name="icon-trend.svg" />
                </span>
            </div>
        </div>
    </div>

    <div class="sp-card p-4 sm:p-5 sticky top-2 z-20">
        <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
            <div class="flex-1 min-w-0">
                <div class="sp-search !shadow-none !bg-[#f7f7f5] w-full">
                    <x-student.figma-icon name="icon-search.svg" box="size-5" />
                    <input
                        x-model.trim="q"
                        type="search"
                        placeholder="{{ __('student.search_my_courses_placeholder') }}"
                        class="!text-sm"
                    >
                    <button type="button" class="text-xs font-extrabold text-[var(--sp-muted)] hover:text-[var(--sp-accent-text)] shrink-0" x-show="q.length" x-cloak @click="q=''">
                        {{ __('student.clear_search') }}
                    </button>
                </div>
                <p class="text-xs font-bold text-[var(--sp-muted)] m-0 mt-2">
                    {{ __('student.showing_count') }}:
                    <span class="text-[var(--sp-text)]" x-text="visibleCount"></span> / {{ $totalShown }}
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <label class="inline-flex items-center gap-2 rounded-full bg-[#f7f7f5] px-3 py-2 text-xs font-extrabold text-[var(--sp-text)] cursor-pointer">
                    <input type="checkbox" class="rounded border-slate-300 text-[var(--sp-accent-text)] focus:ring-[var(--sp-accent)]" x-model="onlyInProgress">
                    {{ __('student.filter_in_progress') }}
                </label>
                <label class="inline-flex items-center gap-2 rounded-full bg-[#f7f7f5] px-3 py-2 text-xs font-extrabold text-[var(--sp-text)] cursor-pointer">
                    <input type="checkbox" class="rounded border-slate-300 text-[var(--sp-accent-text)] focus:ring-[var(--sp-accent)]" x-model="onlyCompleted">
                    {{ __('student.filter_completed') }}
                </label>
            </div>
        </div>
    </div>

    @if($activeCourses->count() > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4" x-ref="grid">
            @foreach($activeCourses as $index => $course)
                @php
                    $progress = (float) ($course->pivot->progress ?? 0);
                    $isCompleted = $progress >= 100;
                    $subjectName = $course->academicSubject->name ?? __('student.course_fallback');
                    $teacherName = $course->teacher->name ?? '—';
                    $title = $course->localized('title');
                    $bubble = $bubbleColors[$index % count($bubbleColors)];
                @endphp
                <article
                    class="sp-course-card sp-card overflow-hidden flex flex-col"
                    data-title="{{ mb_strtolower((string) $title) }}"
                    data-teacher="{{ mb_strtolower((string) $teacherName) }}"
                    data-subject="{{ mb_strtolower((string) $subjectName) }}"
                    data-progress="{{ (int) $progress }}"
                    x-show="matches($el)"
                    x-transition.opacity.duration.150ms
                >
                    <a href="{{ route('my-courses.show', $course) }}" class="block no-underline text-inherit flex-1 min-w-0">
                        <div class="h-36 relative overflow-hidden" style="background:{{ $bubble }}">
                            @if($course->thumbnail)
                                <img src="{{ asset('storage/' . $course->thumbnail) }}" alt="" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex flex-col items-center justify-center gap-2 text-[var(--sp-accent-text)]">
                                    <x-student.figma-icon name="icon-courses.svg" box="size-8" />
                                    <span class="text-xs font-bold">{{ $subjectName }}</span>
                                </div>
                            @endif
                            <span class="absolute top-3 inset-inline-start-3 sp-pill {{ $isCompleted ? 'sp-pill--done' : 'sp-pill--progress' }}">
                                {{ $isCompleted ? __('student.completed_badge') : __('student.active_badge') }}
                            </span>
                        </div>
                        <div class="p-5 space-y-3">
                            <div>
                                <h3 class="font-extrabold text-[15px] m-0 line-clamp-2 leading-snug">{{ $title }}</h3>
                                <p class="text-sm text-[var(--sp-muted)] m-0 mt-1.5 truncate">
                                    {{ $subjectName }} · {{ $teacherName }} · {{ $course->lessons->count() }} {{ __('student.lesson_singular') }}
                                </p>
                            </div>
                            <div>
                                <div class="flex items-center justify-between gap-2 mb-1.5">
                                    <span class="text-xs font-bold text-[var(--sp-muted)]">{{ __('student.progress') }}</span>
                                    <span class="text-sm font-extrabold text-[var(--sp-accent-text)]">{{ (int) $progress }}%</span>
                                </div>
                                <div class="w-full bg-[#f0f0ec] rounded-full h-2 overflow-hidden">
                                    <div class="h-full rounded-full bg-[var(--sp-accent)] transition-all" style="width: {{ min(100, $progress) }}%"></div>
                                </div>
                            </div>
                            <div class="flex items-center justify-between gap-2 text-sm">
                                <span class="font-bold text-[var(--sp-muted)]">{{ __('student.points_label') }}</span>
                                <span class="inline-flex items-center gap-1 font-extrabold">
                                    <img src="{{ \App\Support\StudentFigmaAssets::url('icon-star.svg') }}" alt="" class="size-4">
                                    {{ number_format((float) ($course->student_points ?? 0), 0) }}
                                </span>
                            </div>
                            @unless($isCompleted)
                                <span class="sp-promo-btn !mt-1 w-full !py-2.5 !text-sm">{{ __('student.continue_learning') }}</span>
                            @endunless
                        </div>
                    </a>
                    @if($isCompleted)
                        <div class="px-5 pb-5 -mt-1">
                            <a href="{{ route('student.certificates.claim', $course) }}" class="sp-promo-btn !mt-0 w-full !py-2.5 !text-sm !bg-[var(--sp-sidebar)] !text-white">
                                {{ __('student.claim_certificate') }}
                            </a>
                        </div>
                    @endif
                </article>
            @endforeach
        </div>

        @if($activeCourses->hasPages())
            <div class="flex justify-center pt-2">
                {{ $activeCourses->links() }}
            </div>
        @endif
    @else
        <div class="sp-card p-10 text-center">
            <span class="sp-icon-bubble mx-auto mb-4" style="background:var(--sp-sky);width:56px;height:56px">
                <x-student.figma-icon name="icon-courses.svg" box="size-7" />
            </span>
            <h3 class="font-extrabold text-lg m-0 mb-2">{{ __('student.no_active_courses_my') }}</h3>
            <p class="text-sm text-[var(--sp-muted)] m-0 mb-5 max-w-md mx-auto">{{ __('student.no_active_courses_desc') }}</p>
            @unless($scholarshipOnlyPortal)
                <a href="{{ route('academic-years') }}" class="sp-promo-btn inline-flex">{{ __('student.browse_courses_btn') }}</a>
            @endunless
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
window.__myCoursesPage = function (initialCount) {
    return {
        q: '',
        onlyInProgress: false,
        onlyCompleted: false,
        visibleCount: initialCount || 0,
        normalize(s) {
            return (s || '').toString().toLowerCase().trim();
        },
        matches(el) {
            if (!el) return true;
            const progress = Number(el.dataset.progress || 0);
            if (this.onlyInProgress && progress >= 100) return false;
            if (this.onlyCompleted && progress < 100) return false;
            const q = this.normalize(this.q);
            if (!q) return true;
            const hay = [el.dataset.title || '', el.dataset.teacher || '', el.dataset.subject || ''].join(' ');
            return hay.includes(q);
        },
        recount() {
            try {
                const grid = this.$refs.grid;
                if (!grid) { this.visibleCount = 0; return; }
                const cards = Array.from(grid.querySelectorAll('.sp-course-card'));
                this.visibleCount = cards.filter(a => this.matches(a)).length;
            } catch (e) {}
        },
        init() {
            this.$watch('q', () => this.recount());
            this.$watch('onlyInProgress', () => this.recount());
            this.$watch('onlyCompleted', () => this.recount());
            this.recount();
        }
    }
}
</script>
@endpush
