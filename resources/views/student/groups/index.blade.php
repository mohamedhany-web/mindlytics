@extends('layouts.student-dashboard')

@section('title', __('student.my_groups_title'))
@section('header', __('student.my_groups_title'))

@php
    $bubbleColors = ['#f9e4d7', '#d7e8f9', '#d7eef5', '#f9f0d7', '#dcdef2'];
    $totalMembers = $groups->sum(fn ($g) => $g->members->count());
    $leadingCount = $groups->filter(function ($g) {
        $leaderId = $g->leader_id ?? optional($g->leader)->id;
        return $leaderId && (int) $leaderId === (int) auth()->id();
    })->count();
@endphp

@section('content')
<div class="space-y-5" x-data="window.__studentGroupsPage()">
    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3">
        <div class="min-w-0">
            <p class="text-sm font-bold text-[var(--sp-muted)] m-0 mb-1">{{ __('student.groups_eyebrow') }}</p>
            <p class="text-sm text-[var(--sp-muted)] m-0 max-w-2xl">{{ __('student.my_groups_subtitle') }}</p>
        </div>
        <a href="{{ route('my-courses.index') }}" class="sp-promo-btn !mt-0 self-start shrink-0">
            <x-student.figma-icon name="icon-courses.svg" box="size-4" class="me-2" />
            {{ __('student.groups_go_courses') }}
        </a>
    </div>

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
        <div class="sp-card p-4 sm:p-5">
            <div class="flex items-center justify-between gap-3">
                <div class="min-w-0">
                    <p class="text-xs font-bold text-[var(--sp-muted)] m-0 uppercase tracking-wide">{{ __('student.groups_stat_total') }}</p>
                    <p class="text-2xl font-black text-[var(--sp-accent-text)] m-0 mt-1 leading-none">{{ $groups->count() }}</p>
                </div>
                <span class="sp-icon-bubble" style="background:var(--sp-sky)">
                    <x-student.figma-icon name="icon-community.svg" />
                </span>
            </div>
        </div>
        <div class="sp-card p-4 sm:p-5">
            <div class="flex items-center justify-between gap-3">
                <div class="min-w-0">
                    <p class="text-xs font-bold text-[var(--sp-muted)] m-0 uppercase tracking-wide">{{ __('student.groups_stat_members') }}</p>
                    <p class="text-2xl font-black text-[var(--sp-accent-text)] m-0 mt-1 leading-none">{{ $totalMembers }}</p>
                </div>
                <span class="sp-icon-bubble" style="background:var(--sp-peach)">
                    <x-student.figma-icon name="icon-profile.svg" />
                </span>
            </div>
        </div>
        <div class="sp-card p-4 sm:p-5">
            <div class="flex items-center justify-between gap-3">
                <div class="min-w-0">
                    <p class="text-xs font-bold text-[var(--sp-muted)] m-0 uppercase tracking-wide">{{ __('student.groups_stat_leading') }}</p>
                    <p class="text-2xl font-black text-[var(--sp-accent-text)] m-0 mt-1 leading-none">{{ $leadingCount }}</p>
                </div>
                <span class="sp-icon-bubble" style="background:var(--sp-amber-soft)">
                    <x-student.figma-icon name="icon-star.svg" />
                </span>
            </div>
        </div>
        <div class="sp-card p-4 sm:p-5">
            <div class="flex items-center justify-between gap-3">
                <div class="min-w-0">
                    <p class="text-xs font-bold text-[var(--sp-muted)] m-0 uppercase tracking-wide">{{ __('student.groups_stat_assignments') }}</p>
                    <p class="text-2xl font-black text-[var(--sp-accent-text)] m-0 mt-1 leading-none">{{ $groups->sum('published_assignments_count') }}</p>
                </div>
                <span class="sp-icon-bubble" style="background:var(--sp-lilac)">
                    <x-student.figma-icon name="icon-messages.svg" />
                </span>
            </div>
        </div>
    </div>

    @if($groups->isNotEmpty())
        <div class="sp-card p-4 sm:p-5">
            <div class="sp-search !shadow-none !bg-[#f7f7f5] w-full">
                <x-student.figma-icon name="icon-search.svg" box="size-5" />
                <input
                    type="search"
                    x-model.trim="q"
                    placeholder="{{ __('student.groups_search_placeholder') }}"
                    class="!text-sm"
                    aria-label="{{ __('student.groups_search_placeholder') }}"
                >
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
                <span class="text-[var(--sp-text)]" x-text="visibleCount"></span> / {{ $groups->count() }}
            </p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4" x-ref="grid">
            @foreach($groups as $index => $group)
                @php
                    $courseTitle = $group->course?->localized('title') ?? $group->course?->title ?? '—';
                    $bubble = $bubbleColors[$index % count($bubbleColors)];
                    $isLeader = $group->leader && (int) $group->leader->id === (int) auth()->id();
                    $memberCount = $group->members->count();
                    $maxMembers = (int) ($group->max_members ?: 0);
                @endphp
                <article
                    class="sp-card overflow-hidden flex flex-col group-card"
                    data-title="{{ mb_strtolower((string) $group->name) }}"
                    data-course="{{ mb_strtolower((string) $courseTitle) }}"
                    data-leader="{{ mb_strtolower((string) ($group->leader->name ?? '')) }}"
                    x-show="matches($el)"
                    x-transition.opacity.duration.150ms
                >
                    <div class="h-24 relative overflow-hidden flex items-end p-4" style="background:{{ $bubble }}">
                        <span class="sp-icon-bubble !w-12 !h-12 bg-white/80 text-[var(--sp-accent-text)]">
                            <x-student.figma-icon name="icon-community.svg" box="size-6" />
                        </span>
                        @if($isLeader)
                            <span class="absolute top-3 inset-inline-end-3 sp-pill sp-pill--upcoming">{{ __('student.leader_label') }}</span>
                        @endif
                    </div>
                    <div class="p-5 flex flex-col gap-3 flex-1">
                        <div class="min-w-0">
                            <h3 class="font-extrabold text-[15px] m-0 line-clamp-2 leading-snug">{{ $group->name }}</h3>
                            <p class="text-sm text-[var(--sp-muted)] m-0 mt-1.5 truncate">{{ $courseTitle }}</p>
                        </div>

                        <div class="flex flex-wrap items-center gap-2 text-xs font-bold text-[var(--sp-muted)]">
                            <span class="inline-flex items-center gap-1 rounded-full bg-[#f7f7f5] px-2.5 py-1">
                                <x-student.figma-icon name="icon-profile.svg" box="size-3.5" />
                                {{ $memberCount }}@if($maxMembers) / {{ $maxMembers }}@endif {{ __('student.member_singular') }}
                            </span>
                            @if($group->leader)
                                <span class="inline-flex items-center gap-1 rounded-full bg-[#f7f7f5] px-2.5 py-1 truncate max-w-full">
                                    <x-student.figma-icon name="icon-star.svg" box="size-3.5" />
                                    {{ $group->leader->name }}
                                </span>
                            @endif
                        </div>

                        <div class="flex flex-wrap gap-2 text-xs font-extrabold text-[var(--sp-muted)]">
                            <span class="sp-pill sp-pill--progress">
                                {{ (int) ($group->messages_count ?? 0) }} {{ __('student.groups_messages_count') }}
                            </span>
                            <span class="sp-pill {{ ($group->published_assignments_count ?? 0) > 0 ? 'sp-pill--upcoming' : 'sp-pill--progress' }}">
                                {{ (int) ($group->published_assignments_count ?? 0) }} {{ __('student.groups_assignments_count') }}
                            </span>
                        </div>

                        <div class="mt-auto grid grid-cols-2 gap-2 pt-1">
                            <a href="{{ route('student.groups.show', $group) }}" class="sp-promo-btn !mt-0 !py-2.5 !text-sm text-center">
                                {{ __('student.groups_open_chat') }}
                            </a>
                            <a href="{{ route('student.groups.assignments.index', $group) }}"
                               class="inline-flex items-center justify-center rounded-[30px] px-3 py-2.5 text-sm font-extrabold bg-[#f7f7f5] text-[var(--sp-accent-text)] hover:bg-[var(--sp-accent)] transition-colors">
                                {{ __('student.groups_open_assignments') }}
                            </a>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>

        <p class="text-sm text-[var(--sp-muted)] text-center m-0" x-show="visibleCount === 0" x-cloak>
            {{ __('student.groups_search_empty') }}
        </p>
    @else
        <div class="sp-card p-10 text-center">
            <span class="sp-icon-bubble mx-auto mb-4" style="background:var(--sp-sky);width:56px;height:56px">
                <x-student.figma-icon name="icon-community.svg" box="size-7" />
            </span>
            <h3 class="font-extrabold text-lg m-0 mb-2">{{ __('student.no_groups') }}</h3>
            <p class="text-sm text-[var(--sp-muted)] m-0 mb-5 max-w-md mx-auto">{{ __('student.no_groups_desc') }}</p>
            <a href="{{ route('my-courses.index') }}" class="sp-promo-btn inline-flex">{{ __('student.groups_go_courses') }}</a>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
window.__studentGroupsPage = function () {
    return {
        q: '',
        visibleCount: {{ $groups->count() }},
        normalize(s) {
            return (s || '').toString().toLowerCase().trim();
        },
        matches(el) {
            if (!el) return true;
            const q = this.normalize(this.q);
            if (!q) return true;
            const hay = [el.dataset.title || '', el.dataset.course || '', el.dataset.leader || ''].join(' ');
            return hay.includes(q);
        },
        recount() {
            try {
                const grid = this.$refs.grid;
                if (!grid) { this.visibleCount = 0; return; }
                const cards = Array.from(grid.querySelectorAll('.group-card'));
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
