@extends('layouts.student-dashboard')

@section('title', __('student.lp_index_title'))
@section('header', __('student.lp_index_title'))

@php
    $bubbleColors = ['#f9e4d7', '#d7e8f9', '#d7eef5', '#f9f0d7', '#dcdef2'];
@endphp

@section('content')
<div class="space-y-5" x-data="window.__learningPathsIndex()">
    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3">
        <div class="min-w-0">
            <p class="text-sm font-bold text-[var(--sp-muted)] m-0 mb-1">{{ __('student.lp_index_eyebrow') }}</p>
            <p class="text-sm text-[var(--sp-muted)] m-0 max-w-2xl">{{ __('student.lp_index_subtitle') }}</p>
        </div>
        @unless(auth()->user()->usesScholarshipOnlyPortal())
            <a href="{{ route('academic-years') }}" class="sp-promo-btn !mt-0 self-start shrink-0">
                <x-student.figma-icon name="icon-search.svg" box="size-4" class="me-2" />
                {{ __('student.lp_browse_more') }}
            </a>
        @endunless
    </div>

    <div class="grid grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-4">
        <div class="sp-card p-4 sm:p-5">
            <div class="flex items-center justify-between gap-3">
                <div class="min-w-0">
                    <p class="text-xs font-bold text-[var(--sp-muted)] m-0 uppercase tracking-wide">{{ __('student.lp_stat_paths') }}</p>
                    <p class="text-2xl font-black text-[var(--sp-accent-text)] m-0 mt-1 leading-none">{{ $paths->count() }}</p>
                </div>
                <span class="sp-icon-bubble" style="background:var(--sp-sky)">
                    <x-student.figma-icon name="icon-path.svg" />
                </span>
            </div>
        </div>
        <div class="sp-card p-4 sm:p-5">
            <div class="flex items-center justify-between gap-3">
                <div class="min-w-0">
                    <p class="text-xs font-bold text-[var(--sp-muted)] m-0 uppercase tracking-wide">{{ __('student.lp_stat_courses') }}</p>
                    <p class="text-2xl font-black text-[var(--sp-accent-text)] m-0 mt-1 leading-none">{{ $paths->sum('courses_count') }}</p>
                </div>
                <span class="sp-icon-bubble" style="background:var(--sp-mint)">
                    <x-student.figma-icon name="icon-courses.svg" />
                </span>
            </div>
        </div>
        <div class="sp-card p-4 sm:p-5 col-span-2 lg:col-span-1">
            <div class="flex items-center justify-between gap-3">
                <div class="min-w-0">
                    <p class="text-xs font-bold text-[var(--sp-muted)] m-0 uppercase tracking-wide">{{ __('student.lp_stat_avg_progress') }}</p>
                    <p class="text-2xl font-black text-[var(--sp-accent-text)] m-0 mt-1 leading-none">
                        {{ $paths->count() ? rtrim(rtrim(number_format($paths->avg('progress'), 1), '0'), '.') : 0 }}%
                    </p>
                </div>
                <span class="sp-icon-bubble" style="background:var(--sp-peach)">
                    <x-student.figma-icon name="icon-trend.svg" />
                </span>
            </div>
        </div>
    </div>

    @if($paths->isNotEmpty())
        <div class="sp-card p-4 sm:p-5">
            <div class="sp-search !shadow-none !bg-[#f7f7f5] w-full">
                <x-student.figma-icon name="icon-search.svg" box="size-5" />
                <input type="search"
                       x-model.trim="q"
                       placeholder="{{ __('student.lp_index_search') }}"
                       class="!text-sm"
                       aria-label="{{ __('student.lp_index_search') }}">
                <button type="button"
                        class="text-xs font-extrabold text-[var(--sp-muted)] hover:text-[var(--sp-accent-text)] shrink-0"
                        x-show="q.length"
                        x-cloak
                        @click="q=''">
                    {{ __('student.clear_search') }}
                </button>
            </div>
            <p class="text-xs font-bold text-[var(--sp-muted)] m-0 mt-2">
                {{ __('student.showing_count') }}:
                <span class="text-[var(--sp-text)]" x-text="visibleCount"></span> / {{ $paths->count() }}
            </p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4" x-ref="grid">
            @foreach($paths as $index => $path)
                @php $bubble = $bubbleColors[$index % count($bubbleColors)]; @endphp
                <article
                    class="sp-card overflow-hidden flex flex-col lp-path-card"
                    data-title="{{ mb_strtolower((string) $path->name) }}"
                    data-code="{{ mb_strtolower((string) ($path->code ?? '')) }}"
                    x-show="matches($el)"
                    x-transition.opacity.duration.150ms
                >
                    <div class="h-28 relative overflow-hidden flex items-end p-4" style="background:{{ $bubble }}">
                        <span class="sp-icon-bubble !w-12 !h-12 bg-white/85 text-[var(--sp-accent-text)]">
                            <x-student.figma-icon name="icon-path.svg" box="size-6" />
                        </span>
                        @if($path->code)
                            <span class="absolute top-3 inset-inline-end-3 sp-pill sp-pill--progress">{{ $path->code }}</span>
                        @endif
                    </div>

                    <div class="p-5 flex flex-col gap-3 flex-1">
                        <div class="min-w-0">
                            <h3 class="font-extrabold text-[15px] m-0 line-clamp-2 leading-snug">{{ $path->name }}</h3>
                            @if($path->description)
                                <p class="text-sm text-[var(--sp-muted)] m-0 mt-2 line-clamp-2">{{ \Illuminate\Support\Str::limit(strip_tags($path->description), 110) }}</p>
                            @endif
                        </div>

                        <div>
                            <div class="flex items-center justify-between gap-2 mb-1.5">
                                <span class="text-xs font-bold text-[var(--sp-muted)]">{{ __('student.lp_progress') }}</span>
                                <span class="text-sm font-extrabold text-[var(--sp-accent-text)]">{{ (int) $path->progress }}%</span>
                            </div>
                            <div class="w-full bg-[#f0f0ec] rounded-full h-2 overflow-hidden">
                                <div class="h-full rounded-full bg-[var(--sp-accent)]" style="width: {{ min(100, max(0, $path->progress)) }}%"></div>
                            </div>
                        </div>

                        <div class="flex flex-wrap gap-2 text-xs font-extrabold text-[var(--sp-muted)]">
                            <span class="inline-flex items-center gap-1 rounded-full bg-[#f7f7f5] px-2.5 py-1">
                                {{ $path->courses_count }} {{ __('student.lp_stat_courses') }}
                            </span>
                            <span class="sp-pill sp-pill--done">
                                {{ $path->completed_courses_count }} {{ __('student.lp_stat_done') }}
                            </span>
                            @if($path->enrolled_at)
                                <span class="inline-flex items-center gap-1 rounded-full bg-[#f7f7f5] px-2.5 py-1">
                                    {{ $path->enrolled_at->format('Y/m/d') }}
                                </span>
                            @endif
                        </div>

                        <div class="mt-auto pt-1">
                            <a href="{{ route('student.learning-path.show', $path->slug) }}"
                               class="sp-promo-btn !mt-0 w-full !py-2.5 !text-sm text-center">
                                {{ __('student.lp_open_path') }}
                            </a>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>

        <p class="text-sm text-[var(--sp-muted)] text-center m-0" x-show="visibleCount === 0" x-cloak>
            {{ __('student.lp_index_search_empty') }}
        </p>
    @else
        <div class="sp-card p-10 text-center">
            <span class="sp-icon-bubble mx-auto mb-4" style="background:var(--sp-sky);width:56px;height:56px">
                <x-student.figma-icon name="icon-path.svg" box="size-7" />
            </span>
            <h3 class="font-extrabold text-lg m-0 mb-2">{{ __('student.lp_index_empty') }}</h3>
            <p class="text-sm text-[var(--sp-muted)] m-0 mb-5 max-w-md mx-auto">{{ __('student.lp_index_empty_hint') }}</p>
            @unless(auth()->user()->usesScholarshipOnlyPortal())
                <a href="{{ route('academic-years') }}" class="sp-promo-btn inline-flex">{{ __('student.lp_browse_more') }}</a>
            @endunless
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
window.__learningPathsIndex = function () {
    return {
        q: '',
        visibleCount: {{ $paths->count() }},
        normalize(s) {
            return (s || '').toString().toLowerCase().trim();
        },
        matches(el) {
            if (!el) return true;
            const q = this.normalize(this.q);
            if (!q) return true;
            const hay = [el.dataset.title || '', el.dataset.code || ''].join(' ');
            return hay.includes(q);
        },
        recount() {
            try {
                const grid = this.$refs.grid;
                if (!grid) { this.visibleCount = 0; return; }
                const cards = Array.from(grid.querySelectorAll('.lp-path-card'));
                this.visibleCount = cards.filter(a => this.matches(a)).length;
            } catch (e) {}
        },
        init() {
            this.$watch('q', () => this.recount());
            this.recount();
        }
    };
};
</script>
@endpush
