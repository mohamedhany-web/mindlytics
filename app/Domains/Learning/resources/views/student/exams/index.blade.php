@extends('layouts.student-dashboard')

@section('title', __('student.exams_page_title'))
@section('header', __('student.exams_page_title'))

@section('content')
@php $initialModule = request('module', 'all'); @endphp
<div class="space-y-5" x-data="window.__studentExamsPage(@js($initialModule))">
    @if(session('success'))
        <div class="sp-card !rounded-[16px] px-4 py-3 text-sm font-bold" style="background:var(--sp-mint);color:var(--sp-accent-text)">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="sp-card !rounded-[16px] px-4 py-3 text-sm font-bold bg-[#f9e4d7] text-[#7a3b2e]">{{ session('error') }}</div>
    @endif
    @if(session('info'))
        <div class="sp-card !rounded-[16px] px-4 py-3 text-sm font-bold" style="background:var(--sp-sky);color:var(--sp-accent-text)">{{ session('info') }}</div>
    @endif

    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3">
        <div class="min-w-0">
            <p class="text-sm font-bold text-[var(--sp-muted)] m-0 mb-1">{{ __('student.exam_index_eyebrow') }}</p>
            <p class="text-sm text-[var(--sp-muted)] m-0 max-w-2xl">{{ __('student.exams_subtitle') }}</p>
        </div>
        <div class="flex flex-wrap gap-2 shrink-0">
            <a href="{{ route('my-courses.index') }}" class="sp-promo-btn !mt-0">
                <x-student.figma-icon name="icon-courses.svg" box="size-4" class="me-2" />
                {{ __('student.exam_module_recorded') }}
            </a>
            <a href="{{ route('student.offline-courses.index') }}" class="inline-flex items-center justify-center rounded-[30px] px-4 py-2.5 text-sm font-extrabold bg-[#f7f7f5] text-[var(--sp-accent-text)] hover:bg-[var(--sp-accent)] transition-colors">
                {{ __('student.exam_module_offline') }}
            </a>
            <a href="{{ route('student.online-courses.index') }}" class="inline-flex items-center justify-center rounded-[30px] px-4 py-2.5 text-sm font-extrabold bg-[#f7f7f5] text-[var(--sp-accent-text)] hover:bg-[var(--sp-accent)] transition-colors">
                {{ __('student.exam_module_online') }}
            </a>
        </div>
    </div>

    <div class="sp-card p-3 sm:p-4">
        <div class="flex flex-wrap gap-2">
            @foreach([
                'all' => ['label' => __('student.exam_module_all'), 'count' => $stats['total']],
                'recorded' => ['label' => __('student.exam_module_recorded'), 'count' => $moduleStats['recorded']['total']],
                'offline' => ['label' => __('student.exam_module_offline'), 'count' => $moduleStats['offline']['total']],
                'online' => ['label' => __('student.exam_module_online'), 'count' => $moduleStats['online']['total']],
            ] as $modKey => $mod)
                <button type="button"
                        @click="module = @js($modKey)"
                        class="inline-flex items-center gap-2 rounded-[30px] px-3.5 py-2 text-xs sm:text-sm font-extrabold border-0 cursor-pointer transition-colors"
                        :class="module === @js($modKey) ? 'bg-[var(--sp-accent)] text-[var(--sp-accent-text)]' : 'bg-[#f7f7f5] text-[var(--sp-muted)] hover:text-[var(--sp-accent-text)]'">
                    {{ $mod['label'] }}
                    <span class="rounded-full px-2 py-0.5 text-[10px]" style="background:rgba(9,36,75,.08)">{{ $mod['count'] }}</span>
                </button>
            @endforeach
        </div>
    </div>

    <div class="grid grid-cols-2 lg:grid-cols-5 gap-3 sm:gap-4">
        <div class="sp-card p-4 sm:p-5">
            <div class="flex items-center justify-between gap-3">
                <div class="min-w-0">
                    <p class="text-xs font-bold text-[var(--sp-muted)] m-0 uppercase tracking-wide">{{ __('student.exam_stat_total') }}</p>
                    <p class="text-2xl font-black text-[var(--sp-accent-text)] m-0 mt-1 leading-none">{{ $stats['total'] }}</p>
                </div>
                <span class="sp-icon-bubble" style="background:var(--sp-sky)"><x-student.figma-icon name="icon-exams.svg" /></span>
            </div>
        </div>
        <div class="sp-card p-4 sm:p-5">
            <div class="flex items-center justify-between gap-3">
                <div class="min-w-0">
                    <p class="text-xs font-bold text-[var(--sp-muted)] m-0 uppercase tracking-wide">{{ __('student.exam_stat_available') }}</p>
                    <p class="text-2xl font-black text-[var(--sp-accent-text)] m-0 mt-1 leading-none">{{ $stats['available'] }}</p>
                </div>
                <span class="sp-icon-bubble" style="background:var(--sp-mint)"><x-student.figma-icon name="icon-star.svg" /></span>
            </div>
        </div>
        <div class="sp-card p-4 sm:p-5">
            <div class="flex items-center justify-between gap-3">
                <div class="min-w-0">
                    <p class="text-xs font-bold text-[var(--sp-muted)] m-0 uppercase tracking-wide">{{ __('student.exam_stat_in_progress') }}</p>
                    <p class="text-2xl font-black text-[var(--sp-accent-text)] m-0 mt-1 leading-none">{{ $stats['in_progress'] }}</p>
                </div>
                <span class="sp-icon-bubble" style="background:var(--sp-lilac)"><x-student.figma-icon name="icon-trend.svg" /></span>
            </div>
        </div>
        <div class="sp-card p-4 sm:p-5">
            <div class="flex items-center justify-between gap-3">
                <div class="min-w-0">
                    <p class="text-xs font-bold text-[var(--sp-muted)] m-0 uppercase tracking-wide">{{ __('student.completed') }}</p>
                    <p class="text-2xl font-black text-[var(--sp-accent-text)] m-0 mt-1 leading-none">{{ $stats['completed'] }}</p>
                </div>
                <span class="sp-icon-bubble" style="background:var(--sp-badge-done)"><x-student.figma-icon name="icon-certificates.svg" /></span>
            </div>
        </div>
        <div class="sp-card p-4 sm:p-5 col-span-2 lg:col-span-1">
            <div class="flex items-center justify-between gap-3">
                <div class="min-w-0">
                    <p class="text-xs font-bold text-[var(--sp-muted)] m-0 uppercase tracking-wide">{{ __('student.avg_results_label') }}</p>
                    <p class="text-2xl font-black text-[var(--sp-accent-text)] m-0 mt-1 leading-none">{{ $stats['avg_score'] }}%</p>
                </div>
                <span class="sp-icon-bubble" style="background:var(--sp-peach)"><x-student.figma-icon name="icon-exams.svg" /></span>
            </div>
        </div>
    </div>

    @if($availableExams->isNotEmpty())
        <div class="sp-card p-4 sm:p-5 space-y-4">
            <div class="sp-search !shadow-none !bg-[#f7f7f5] w-full">
                <x-student.figma-icon name="icon-search.svg" box="size-5" />
                <input type="search" x-model.trim="q" placeholder="{{ __('student.exam_search_placeholder') }}" class="!text-sm" aria-label="{{ __('student.exam_search_placeholder') }}">
            </div>
            <div class="flex flex-wrap gap-2">
                <template x-for="chip in filters" :key="chip.key">
                    <button type="button"
                            class="inline-flex items-center rounded-[30px] px-3.5 py-2 text-xs sm:text-sm font-extrabold bg-[#f7f7f5] text-[var(--sp-muted)] border-0 cursor-pointer transition-colors"
                            :class="status === chip.key ? 'bg-[var(--sp-accent)] text-[var(--sp-accent-text)]' : 'hover:text-[var(--sp-accent-text)]'"
                            @click="status = chip.key"
                            x-text="chip.label"></button>
                </template>
            </div>
        </div>

        <div class="space-y-3">
            @foreach($availableExams as $exam)
                @php
                    $statusPill = match($exam->portal_status) {
                        'available' => ['sp-pill--done', __('student.exam_status_available')],
                        'in_progress' => ['sp-pill--progress', __('student.exam_status_in_progress')],
                        'completed' => ['sp-pill--done', __('student.exam_status_completed')],
                        'exhausted' => ['sp-pill--upcoming', __('student.exam_status_exhausted')],
                        default => ['sp-pill--upcoming', __('student.exam_status_locked')],
                    };
                    $resumeUrl = ($exam->last_attempt && $exam->last_attempt->status === 'in_progress')
                        ? route('student.exams.take', [$exam, $exam->last_attempt])
                        : null;
                @endphp
                <article class="sp-card p-4 sm:p-5"
                         data-title="{{ Str::lower($exam->title) }}"
                         data-course="{{ Str::lower($exam->course_label) }}"
                         data-status="{{ $exam->portal_status }}"
                         data-source="{{ $exam->source }}"
                         x-show="matches(@js(Str::lower($exam->title)), @js(Str::lower($exam->course_label)), @js($exam->portal_status), @js($exam->source))"
                         x-cloak>
                    <div class="flex flex-col lg:flex-row lg:items-start gap-4">
                        <div class="flex items-start gap-3 min-w-0 flex-1">
                            <span class="sp-icon-bubble shrink-0" style="background:{{ $exam->source_bubble }}">
                                <x-student.figma-icon :name="$exam->source_icon" box="size-5" />
                            </span>
                            <div class="min-w-0 flex-1">
                                <div class="flex flex-wrap items-center gap-2 mb-1">
                                    <h3 class="font-extrabold text-[15px] m-0 leading-snug">{{ $exam->title }}</h3>
                                    <span class="sp-pill {{ $statusPill[0] }}">{{ $statusPill[1] }}</span>
                                    <span class="sp-pill sp-pill--progress">{{ $exam->source_label }}</span>
                                </div>
                                <a href="{{ $exam->course_route }}" class="sp-link text-sm font-bold">{{ $exam->course_label }}</a>

                                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                                    <div class="rounded-[14px] bg-[#f7f7f5] px-3 py-2">
                                        <p class="text-[11px] font-bold text-[var(--sp-muted)] m-0">{{ __('student.duration_label') }}</p>
                                        <p class="text-sm font-extrabold m-0 mt-0.5">{{ $exam->duration_minutes }} {{ __('student.minutes') }}</p>
                                    </div>
                                    <div class="rounded-[14px] bg-[#f7f7f5] px-3 py-2">
                                        <p class="text-[11px] font-bold text-[var(--sp-muted)] m-0">{{ __('student.questions_label') }}</p>
                                        <p class="text-sm font-extrabold m-0 mt-0.5">{{ $exam->questions_count }}</p>
                                    </div>
                                    <div class="rounded-[14px] bg-[#f7f7f5] px-3 py-2">
                                        <p class="text-[11px] font-bold text-[var(--sp-muted)] m-0">{{ __('student.passing_marks_label') }}</p>
                                        <p class="text-sm font-extrabold m-0 mt-0.5">{{ $exam->passing_marks }}%</p>
                                    </div>
                                    <div class="rounded-[14px] bg-[#f7f7f5] px-3 py-2">
                                        <p class="text-[11px] font-bold text-[var(--sp-muted)] m-0">{{ __('student.attempts_label') }}</p>
                                        <p class="text-sm font-extrabold m-0 mt-0.5">{{ $exam->attempts_allowed == 0 ? __('student.unlimited_attempts') : $exam->user_attempts.' / '.$exam->attempts_allowed }}</p>
                                    </div>
                                </div>

                                @if($exam->user_attempts > 0 && $exam->best_percentage !== null)
                                    <p class="text-xs font-bold text-[var(--sp-muted)] m-0 mt-3">
                                        {{ __('student.best_score_label') }}:
                                        <span class="text-[var(--sp-accent-text)]">{{ number_format($exam->best_percentage, 1) }}%</span>
                                    </p>
                                @endif

                                @if($exam->prevent_tab_switch || $exam->require_camera || $exam->require_microphone)
                                    <div class="flex flex-wrap gap-1.5 mt-3">
                                        <span class="text-[11px] font-bold text-[#7a3b2e]">{{ __('student.protected_exam') }}:</span>
                                        @if($exam->prevent_tab_switch)<span class="sp-pill sp-pill--upcoming !text-[11px]">{{ __('student.no_tab_switch') }}</span>@endif
                                        @if($exam->require_camera)<span class="sp-pill sp-pill--upcoming !text-[11px]">{{ __('student.camera_label') }}</span>@endif
                                        @if($exam->require_microphone)<span class="sp-pill sp-pill--upcoming !text-[11px]">{{ __('student.microphone_label') }}</span>@endif
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="flex flex-wrap lg:flex-col gap-2 shrink-0">
                            @if($resumeUrl)
                                <a href="{{ $resumeUrl }}" class="sp-promo-btn !mt-0">{{ __('student.exam_resume') }}</a>
                            @elseif($exam->can_attempt)
                                <a href="{{ route('student.exams.show', $exam) }}" class="sp-promo-btn !mt-0">{{ __('student.start_exam') }}</a>
                            @endif
                            <a href="{{ route('student.exams.show', $exam) }}" class="inline-flex items-center justify-center rounded-[30px] px-4 py-2.5 text-sm font-extrabold bg-[#f7f7f5] text-[var(--sp-accent-text)] hover:bg-[var(--sp-accent)] transition-colors">
                                {{ __('student.exam_view_details') }}
                            </a>
                            @if($exam->show_results_immediately && $exam->last_attempt && $exam->last_attempt->status === 'completed')
                                <a href="{{ route('student.exams.result', [$exam, $exam->last_attempt]) }}" class="inline-flex items-center justify-center rounded-[30px] px-4 py-2.5 text-sm font-extrabold bg-[var(--sp-mint)] text-[var(--sp-accent-text)] hover:opacity-90">
                                    {{ __('student.view_result') }}
                                </a>
                            @endif
                        </div>
                    </div>
                </article>
            @endforeach
        </div>

        <p class="text-sm font-bold text-[var(--sp-muted)] text-center py-6" x-show="!hasVisible()" x-cloak>{{ __('student.exam_filter_empty') }}</p>
    @else
        <div class="sp-card p-10 text-center">
            <span class="sp-icon-bubble mx-auto mb-4" style="background:var(--sp-peach);width:56px;height:56px">
                <x-student.figma-icon name="icon-exams.svg" box="size-7" />
            </span>
            <h3 class="font-extrabold text-lg m-0 mb-2">{{ __('student.no_exams_available') }}</h3>
            <p class="text-sm text-[var(--sp-muted)] m-0 max-w-md mx-auto mb-5">{{ __('student.no_exams_desc') }}</p>
            <a href="{{ route('my-courses.index') }}" class="sp-promo-btn !mt-0 inline-flex">{{ __('student.view_my_courses') }}</a>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
window.__studentExamsPage = function (initialModule) {
    const i18n = {
        all: @json(__('student.exam_filter_all')),
        available: @json(__('student.exam_filter_available')),
        in_progress: @json(__('student.exam_filter_in_progress')),
        completed: @json(__('student.exam_filter_completed')),
        locked: @json(__('student.exam_filter_locked')),
    };
    return {
        q: '',
        status: 'all',
        module: initialModule || 'all',
        filters: [
            { key: 'all', label: i18n.all },
            { key: 'available', label: i18n.available },
            { key: 'in_progress', label: i18n.in_progress },
            { key: 'completed', label: i18n.completed },
            { key: 'locked', label: i18n.locked },
        ],
        matches(title, course, portalStatus, source) {
            const term = (this.q || '').toLowerCase();
            const hay = `${title} ${course}`;
            if (term && !hay.includes(term)) return false;
            if (this.module !== 'all' && source !== this.module) return false;
            if (this.status === 'all') return true;
            if (this.status === 'locked') return ['locked', 'exhausted'].includes(portalStatus);
            return portalStatus === this.status;
        },
        hasVisible() {
            return [...document.querySelectorAll('[data-source]')].some(el => el.offsetParent !== null);
        },
    };
};
</script>
@endpush
