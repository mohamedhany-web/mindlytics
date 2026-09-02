@extends('layouts.student-dashboard')

@section('title', __('student.assignments'))
@section('header', __('student.assignments'))

@section('content')
<div class="space-y-5" x-data="window.__studentAssignmentsPage()">
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

    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3">
        <div class="min-w-0">
            <p class="text-sm font-bold text-[var(--sp-muted)] m-0 mb-1">{{ __('student.assign_index_eyebrow') }}</p>
            <p class="text-sm text-[var(--sp-muted)] m-0 max-w-2xl">{{ __('student.assignments_view_submit') }}</p>
        </div>
        <a href="{{ route('my-courses.index') }}" class="sp-promo-btn !mt-0 self-start shrink-0">
            <x-student.figma-icon name="icon-courses.svg" box="size-4" class="me-2" />
            {{ __('student.my_courses_active_title') }}
        </a>
    </div>

    <div class="grid grid-cols-2 lg:grid-cols-5 gap-3 sm:gap-4">
        <div class="sp-card p-4 sm:p-5">
            <div class="flex items-center justify-between gap-3">
                <div class="min-w-0">
                    <p class="text-xs font-bold text-[var(--sp-muted)] m-0 uppercase tracking-wide">{{ __('student.assign_stat_total') }}</p>
                    <p class="text-2xl font-black text-[var(--sp-accent-text)] m-0 mt-1 leading-none">{{ $stats['total'] }}</p>
                </div>
                <span class="sp-icon-bubble" style="background:var(--sp-sky)">
                    <x-student.figma-icon name="icon-messages.svg" />
                </span>
            </div>
        </div>
        <div class="sp-card p-4 sm:p-5">
            <div class="flex items-center justify-between gap-3">
                <div class="min-w-0">
                    <p class="text-xs font-bold text-[var(--sp-muted)] m-0 uppercase tracking-wide">{{ __('student.assign_stat_pending') }}</p>
                    <p class="text-2xl font-black text-[var(--sp-accent-text)] m-0 mt-1 leading-none">{{ $stats['pending'] }}</p>
                </div>
                <span class="sp-icon-bubble" style="background:var(--sp-lilac)">
                    <x-student.figma-icon name="icon-calendar.svg" />
                </span>
            </div>
        </div>
        <div class="sp-card p-4 sm:p-5">
            <div class="flex items-center justify-between gap-3">
                <div class="min-w-0">
                    <p class="text-xs font-bold text-[var(--sp-muted)] m-0 uppercase tracking-wide">{{ __('student.assign_stat_submitted') }}</p>
                    <p class="text-2xl font-black text-[var(--sp-accent-text)] m-0 mt-1 leading-none">{{ $stats['submitted'] }}</p>
                </div>
                <span class="sp-icon-bubble" style="background:var(--sp-mint)">
                    <x-student.figma-icon name="icon-certificates.svg" />
                </span>
            </div>
        </div>
        <div class="sp-card p-4 sm:p-5">
            <div class="flex items-center justify-between gap-3">
                <div class="min-w-0">
                    <p class="text-xs font-bold text-[var(--sp-muted)] m-0 uppercase tracking-wide">{{ __('student.assign_stat_graded') }}</p>
                    <p class="text-2xl font-black text-[var(--sp-accent-text)] m-0 mt-1 leading-none">{{ $stats['graded'] }}</p>
                </div>
                <span class="sp-icon-bubble" style="background:var(--sp-badge-done)">
                    <x-student.figma-icon name="icon-star.svg" />
                </span>
            </div>
        </div>
        <div class="sp-card p-4 sm:p-5 col-span-2 lg:col-span-1">
            <div class="flex items-center justify-between gap-3">
                <div class="min-w-0">
                    <p class="text-xs font-bold text-[var(--sp-muted)] m-0 uppercase tracking-wide">{{ __('student.assign_stat_overdue') }}</p>
                    <p class="text-2xl font-black text-[var(--sp-accent-text)] m-0 mt-1 leading-none">{{ $stats['overdue'] }}</p>
                </div>
                <span class="sp-icon-bubble" style="background:var(--sp-peach)">
                    <x-student.figma-icon name="icon-exams.svg" />
                </span>
            </div>
        </div>
    </div>

    @if($assignments->isNotEmpty())
        <div class="sp-card p-4 sm:p-5 space-y-4">
            <div class="sp-search !shadow-none !bg-[#f7f7f5] w-full">
                <x-student.figma-icon name="icon-search.svg" box="size-5" />
                <input type="search"
                       x-model.trim="q"
                       placeholder="{{ __('student.assign_search_placeholder') }}"
                       class="!text-sm"
                       aria-label="{{ __('student.assign_search_placeholder') }}">
                <button type="button"
                        class="text-xs font-extrabold text-[var(--sp-muted)] hover:text-[var(--sp-accent-text)] shrink-0"
                        x-show="q.length"
                        x-cloak
                        @click="q=''">
                    {{ __('student.clear_search') }}
                </button>
            </div>

            <div class="flex flex-wrap gap-2">
                <button type="button"
                        @click="filter = 'all'"
                        :class="filter === 'all' ? 'bg-[var(--sp-accent)] text-[var(--sp-accent-text)]' : 'bg-[#f7f7f5] text-[var(--sp-muted)]'"
                        class="rounded-[30px] px-3.5 py-2 text-xs font-extrabold transition-colors">
                    {{ __('student.assign_filter_all') }}
                </button>
                <button type="button"
                        @click="filter = 'pending'"
                        :class="filter === 'pending' ? 'bg-[var(--sp-accent)] text-[var(--sp-accent-text)]' : 'bg-[#f7f7f5] text-[var(--sp-muted)]'"
                        class="rounded-[30px] px-3.5 py-2 text-xs font-extrabold transition-colors">
                    {{ __('student.assign_stat_pending') }}
                </button>
                <button type="button"
                        @click="filter = 'submitted'"
                        :class="filter === 'submitted' ? 'bg-[var(--sp-accent)] text-[var(--sp-accent-text)]' : 'bg-[#f7f7f5] text-[var(--sp-muted)]'"
                        class="rounded-[30px] px-3.5 py-2 text-xs font-extrabold transition-colors">
                    {{ __('student.assign_stat_submitted') }}
                </button>
                <button type="button"
                        @click="filter = 'graded'"
                        :class="filter === 'graded' ? 'bg-[var(--sp-accent)] text-[var(--sp-accent-text)]' : 'bg-[#f7f7f5] text-[var(--sp-muted)]'"
                        class="rounded-[30px] px-3.5 py-2 text-xs font-extrabold transition-colors">
                    {{ __('student.assign_stat_graded') }}
                </button>
                <button type="button"
                        @click="filter = 'overdue'"
                        :class="filter === 'overdue' ? 'bg-[var(--sp-accent)] text-[var(--sp-accent-text)]' : 'bg-[#f7f7f5] text-[var(--sp-muted)]'"
                        class="rounded-[30px] px-3.5 py-2 text-xs font-extrabold transition-colors">
                    {{ __('student.assign_stat_overdue') }}
                </button>
            </div>

            <p class="text-xs font-bold text-[var(--sp-muted)] m-0">
                {{ __('student.showing_count') }}:
                <span class="text-[var(--sp-text)]" x-text="visibleCount"></span> / {{ $assignments->count() }}
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4" x-ref="grid">
            @foreach($assignments as $assignment)
                @php
                    $status = $assignment->student_status ?? 'pending';
                    $courseTitle = $assignment->course?->localized('title') ?? $assignment->course?->title ?? '—';
                    $sub = $assignment->student_submission;
                @endphp
                <article
                    class="sp-card p-5 flex flex-col assign-card"
                    data-title="{{ mb_strtolower((string) $assignment->title) }}"
                    data-course="{{ mb_strtolower((string) $courseTitle) }}"
                    data-status="{{ $status }}"
                    x-show="matches($el)"
                    x-transition.opacity.duration.150ms
                >
                    <div class="flex items-start justify-between gap-2 mb-3">
                        <span class="sp-icon-bubble !w-10 !h-10 shrink-0" style="background:var(--sp-sky)">
                            <x-student.figma-icon name="icon-messages.svg" box="size-4" />
                        </span>
                        @if($status === 'graded')
                            <span class="sp-pill sp-pill--done">{{ __('student.assign_status_graded') }}</span>
                        @elseif($status === 'submitted')
                            <span class="sp-pill sp-pill--progress">{{ __('student.assign_status_submitted') }}</span>
                        @elseif($status === 'overdue')
                            <span class="sp-pill sp-pill--upcoming">{{ __('student.assign_status_overdue') }}</span>
                        @else
                            <span class="sp-pill sp-pill--upcoming">{{ __('student.assign_status_pending') }}</span>
                        @endif
                    </div>

                    <h3 class="font-extrabold text-[15px] m-0 line-clamp-2 leading-snug">{{ $assignment->title }}</h3>

                    @if($assignment->description)
                        <p class="text-sm text-[var(--sp-muted)] m-0 mt-2 line-clamp-2">{{ \Illuminate\Support\Str::limit(strip_tags($assignment->description), 120) }}</p>
                    @endif

                    <div class="mt-3 space-y-1.5 text-xs font-bold text-[var(--sp-muted)]">
                        <p class="m-0 flex items-center gap-1.5">
                            <x-student.figma-icon name="icon-courses.svg" box="size-3.5" />
                            {{ $courseTitle }}
                        </p>
                        @if($assignment->lesson)
                            <p class="m-0 flex items-center gap-1.5">
                                <x-student.figma-icon name="icon-classes.svg" box="size-3.5" />
                                {{ $assignment->lesson->title }}
                            </p>
                        @endif
                        @if($assignment->due_date)
                            <p class="m-0 flex items-center gap-1.5">
                                <x-student.figma-icon name="icon-calendar.svg" box="size-3.5" />
                                {{ __('student.assign_due_date') }}: {{ $assignment->due_date->format('Y/m/d H:i') }}
                            </p>
                        @endif
                        <p class="m-0 flex items-center gap-1.5">
                            <x-student.figma-icon name="icon-star.svg" box="size-3.5" />
                            {{ __('student.assign_max_score') }}: {{ $assignment->max_score }}
                        </p>
                        @if($sub && $sub->score !== null)
                            <p class="m-0 text-[var(--sp-accent-text)]">
                                {{ __('student.assign_score') }}: {{ $sub->score }} / {{ $assignment->max_score }}
                            </p>
                        @endif
                    </div>

                    <div class="mt-auto pt-4">
                        <a href="{{ route('student.assignments.show', $assignment) }}"
                           class="sp-promo-btn !mt-0 w-full !py-2.5 !text-sm text-center">
                            {{ $status === 'pending' || $status === 'overdue' ? __('student.assign_open_submit') : __('student.assign_open_view') }}
                        </a>
                    </div>
                </article>
            @endforeach
        </div>

        <p class="text-sm text-[var(--sp-muted)] text-center m-0" x-show="visibleCount === 0" x-cloak>
            {{ __('student.assign_search_empty') }}
        </p>
    @else
        <div class="sp-card p-10 text-center">
            <span class="sp-icon-bubble mx-auto mb-4" style="background:var(--sp-sky);width:56px;height:56px">
                <x-student.figma-icon name="icon-messages.svg" box="size-7" />
            </span>
            <h3 class="font-extrabold text-lg m-0 mb-2">{{ __('student.assign_empty') }}</h3>
            <p class="text-sm text-[var(--sp-muted)] m-0 mb-5 max-w-md mx-auto">{{ __('student.assign_empty_hint') }}</p>
            <a href="{{ route('my-courses.index') }}" class="sp-promo-btn inline-flex">{{ __('student.my_courses_active_title') }}</a>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
window.__studentAssignmentsPage = function () {
    return {
        q: '',
        filter: 'all',
        visibleCount: {{ $assignments->count() }},
        normalize(s) {
            return (s || '').toString().toLowerCase().trim();
        },
        matches(el) {
            if (!el) return true;
            const status = el.dataset.status || '';
            if (this.filter !== 'all' && status !== this.filter) return false;
            const q = this.normalize(this.q);
            if (!q) return true;
            const hay = [el.dataset.title || '', el.dataset.course || ''].join(' ');
            return hay.includes(q);
        },
        recount() {
            try {
                const grid = this.$refs.grid;
                if (!grid) { this.visibleCount = 0; return; }
                const cards = Array.from(grid.querySelectorAll('.assign-card'));
                this.visibleCount = cards.filter(a => this.matches(a)).length;
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
