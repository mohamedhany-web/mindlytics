@extends('layouts.student-dashboard')

@section('title', __('student.calendar_title'))
@section('header', __('student.calendar_title'))

@php
    $isRtl = app()->getLocale() === 'ar';
    $typeMeta = [
        'exam' => ['icon' => 'icon-exams.svg', 'bubble' => 'var(--sp-peach)', 'label' => __('student.legend_exams')],
        'lecture' => ['icon' => 'icon-classes.svg', 'bubble' => 'var(--sp-sky)', 'label' => __('student.legend_lectures')],
        'assignment' => ['icon' => 'icon-messages.svg', 'bubble' => 'var(--sp-amber-soft)', 'label' => __('student.legend_assignments')],
        'other' => ['icon' => 'icon-calendar.svg', 'bubble' => 'var(--sp-mint)', 'label' => __('student.other_events')],
    ];
@endphp

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.5/main.min.css" rel="stylesheet" />
<style>
    .sp-cal-shell .fc {
        font-family: var(--sp-font);
        direction: {{ $isRtl ? 'rtl' : 'ltr' }};
    }
    .sp-cal-shell .fc-toolbar {
        flex-wrap: wrap;
        gap: 0.75rem;
        margin-bottom: 1.25rem !important;
    }
    .sp-cal-shell .fc-toolbar-title {
        font-size: 1.15rem !important;
        font-weight: 900 !important;
        color: var(--sp-accent-text);
    }
    .sp-cal-shell .fc-button {
        background: #f7f7f5 !important;
        border: 0 !important;
        color: var(--sp-accent-text) !important;
        font-family: var(--sp-font) !important;
        font-weight: 800 !important;
        border-radius: 999px !important;
        padding: 0.45rem 0.9rem !important;
        box-shadow: none !important;
        text-transform: none !important;
    }
    .sp-cal-shell .fc-button:hover {
        background: var(--sp-accent) !important;
    }
    .sp-cal-shell .fc-button-primary:not(:disabled).fc-button-active,
    .sp-cal-shell .fc-button-primary:not(:disabled):active {
        background: var(--sp-accent) !important;
        color: var(--sp-accent-text) !important;
    }
    .sp-cal-shell .fc-button-group {
        gap: 0.35rem;
    }
    .sp-cal-shell .fc-button-group > .fc-button {
        margin: 0 !important;
    }
    .sp-cal-shell .fc-theme-standard td,
    .sp-cal-shell .fc-theme-standard th,
    .sp-cal-shell .fc-theme-standard .fc-scrollgrid {
        border-color: rgba(15, 15, 20, 0.06) !important;
    }
    .sp-cal-shell .fc-col-header-cell-cushion {
        color: var(--sp-muted);
        font-weight: 800;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        padding: 0.75rem 0.25rem !important;
    }
    .sp-cal-shell .fc-daygrid-day-number,
    .sp-cal-shell .fc-timegrid-slot-label-cushion {
        color: var(--sp-accent-text);
        font-weight: 800;
        font-size: 0.85rem;
        padding: 0.5rem !important;
    }
    .sp-cal-shell .fc-day-today {
        background: rgba(174, 217, 234, 0.28) !important;
    }
    .sp-cal-shell .fc-daygrid-day.fc-day-today .fc-daygrid-day-number {
        background: var(--sp-accent);
        border-radius: 999px;
        width: 1.85rem;
        height: 1.85rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0 !important;
    }
    .sp-cal-shell .fc-event {
        border: 0 !important;
        border-radius: 10px !important;
        padding: 3px 8px !important;
        font-weight: 800 !important;
        font-size: 0.72rem !important;
        box-shadow: none !important;
        cursor: pointer;
        transition: transform .15s ease, box-shadow .15s ease;
    }
    .sp-cal-shell .fc-event:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 16px rgba(9, 36, 75, 0.12);
    }
    .sp-cal-shell .fc-daygrid-event {
        white-space: normal;
        line-height: 1.25;
    }
    .sp-cal-shell .fc-more-link {
        font-weight: 800;
        color: var(--sp-accent-text) !important;
    }
    .sp-cal-shell .fc-popover {
        border-radius: 16px !important;
        border: 0 !important;
        box-shadow: var(--sp-shadow) !important;
        font-family: var(--sp-font);
    }
    .sp-cal-filter.is-active {
        background: var(--sp-accent) !important;
        color: var(--sp-accent-text) !important;
    }
    .sp-cal-modal-backdrop {
        background: rgba(15, 15, 20, 0.45);
        backdrop-filter: blur(4px);
    }
    @media (max-width: 640px) {
        .sp-cal-shell .fc-toolbar.fc-header-toolbar {
            flex-direction: column;
            align-items: stretch;
        }
        .sp-cal-shell .fc-toolbar-chunk {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
        }
        .sp-cal-shell .fc-toolbar-title {
            text-align: center;
            width: 100%;
        }
    }
</style>
@endpush

@section('content')
<div class="space-y-5"
     x-data="studentCalendarPage()"
     x-cloak>

    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3">
        <div class="min-w-0">
            <p class="text-sm font-bold text-[var(--sp-muted)] m-0 mb-1">{{ __('student.cal_eyebrow') }}</p>
            <p class="text-sm text-[var(--sp-muted)] m-0 max-w-2xl">{{ __('student.calendar_subtitle') }}</p>
        </div>
        <div class="flex flex-wrap gap-2 shrink-0">
            <button type="button"
                    @click="goToday()"
                    class="sp-promo-btn !mt-0 border-0 cursor-pointer">
                {{ __('student.cal_btn_today') }}
            </button>
            <a href="{{ route('student.assignments.index') }}"
               class="inline-flex items-center justify-center rounded-[30px] px-4 py-2.5 text-sm font-extrabold bg-[#f7f7f5] text-[var(--sp-accent-text)] hover:bg-[var(--sp-accent)] transition-colors">
                {{ __('student.assignments') }}
            </a>
        </div>
    </div>

    <div class="grid grid-cols-2 lg:grid-cols-5 gap-3 sm:gap-4">
        <div class="sp-card p-4 sm:p-5">
            <div class="flex items-center justify-between gap-3">
                <div class="min-w-0">
                    <p class="text-xs font-bold text-[var(--sp-muted)] m-0 uppercase tracking-wide">{{ __('student.total_events') }}</p>
                    <p class="text-2xl font-black text-[var(--sp-accent-text)] m-0 mt-1 leading-none">{{ $stats['total'] }}</p>
                </div>
                <span class="sp-icon-bubble" style="background:var(--sp-sky)">
                    <x-student.figma-icon name="icon-calendar.svg" />
                </span>
            </div>
        </div>
        <div class="sp-card p-4 sm:p-5">
            <div class="flex items-center justify-between gap-3">
                <div class="min-w-0">
                    <p class="text-xs font-bold text-[var(--sp-muted)] m-0 uppercase tracking-wide">{{ __('student.today_label') }}</p>
                    <p class="text-2xl font-black text-[var(--sp-accent-text)] m-0 mt-1 leading-none">{{ $stats['today'] }}</p>
                </div>
                <span class="sp-icon-bubble" style="background:var(--sp-mint)">
                    <x-student.figma-icon name="icon-star.svg" />
                </span>
            </div>
        </div>
        <div class="sp-card p-4 sm:p-5">
            <div class="flex items-center justify-between gap-3">
                <div class="min-w-0">
                    <p class="text-xs font-bold text-[var(--sp-muted)] m-0 uppercase tracking-wide">{{ __('student.legend_exams') }}</p>
                    <p class="text-2xl font-black text-[var(--sp-accent-text)] m-0 mt-1 leading-none">{{ $stats['exams'] }}</p>
                </div>
                <span class="sp-icon-bubble" style="background:var(--sp-peach)">
                    <x-student.figma-icon name="icon-exams.svg" />
                </span>
            </div>
        </div>
        <div class="sp-card p-4 sm:p-5">
            <div class="flex items-center justify-between gap-3">
                <div class="min-w-0">
                    <p class="text-xs font-bold text-[var(--sp-muted)] m-0 uppercase tracking-wide">{{ __('student.legend_lectures') }}</p>
                    <p class="text-2xl font-black text-[var(--sp-accent-text)] m-0 mt-1 leading-none">{{ $stats['lectures'] }}</p>
                </div>
                <span class="sp-icon-bubble" style="background:var(--sp-lilac)">
                    <x-student.figma-icon name="icon-classes.svg" />
                </span>
            </div>
        </div>
        <div class="sp-card p-4 sm:p-5 col-span-2 lg:col-span-1">
            <div class="flex items-center justify-between gap-3">
                <div class="min-w-0">
                    <p class="text-xs font-bold text-[var(--sp-muted)] m-0 uppercase tracking-wide">{{ __('student.cal_stat_upcoming') }}</p>
                    <p class="text-2xl font-black text-[var(--sp-accent-text)] m-0 mt-1 leading-none">{{ $stats['upcoming'] }}</p>
                </div>
                <span class="sp-icon-bubble" style="background:var(--sp-amber-soft)">
                    <x-student.figma-icon name="icon-trend.svg" />
                </span>
            </div>
        </div>
    </div>

    <div class="sp-card p-3 sm:p-4">
        <div class="flex flex-wrap gap-2">
            <template x-for="chip in filters" :key="chip.key">
                <button type="button"
                        class="sp-cal-filter inline-flex items-center gap-2 rounded-[30px] px-3.5 py-2 text-xs sm:text-sm font-extrabold bg-[#f7f7f5] text-[var(--sp-muted)] hover:text-[var(--sp-accent-text)] border-0 cursor-pointer transition-colors"
                        :class="{ 'is-active': filter === chip.key }"
                        @click="setFilter(chip.key)"
                        x-text="chip.label"></button>
            </template>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-5">
        <div class="xl:col-span-2 space-y-5">
            <section class="sp-card p-4 sm:p-6 sp-cal-shell">
                <div id="student-calendar"></div>
                <div class="flex flex-wrap gap-3 mt-5 pt-4 border-t border-black/5">
                    @foreach([
                        ['color' => 'var(--sp-peach)', 'label' => __('student.legend_exams')],
                        ['color' => 'var(--sp-sky)', 'label' => __('student.legend_lectures')],
                        ['color' => 'var(--sp-amber-soft)', 'label' => __('student.legend_assignments')],
                        ['color' => 'var(--sp-mint)', 'label' => __('student.other_events')],
                    ] as $legend)
                        <span class="inline-flex items-center gap-2 text-xs font-bold text-[var(--sp-muted)]">
                            <span class="size-3 rounded-md" style="background:{{ $legend['color'] }}"></span>
                            {{ $legend['label'] }}
                        </span>
                    @endforeach
                </div>
            </section>

            <section class="sp-card p-5 sm:p-6 space-y-4">
                <div class="flex items-center justify-between gap-3">
                    <h2 class="font-extrabold text-base m-0">{{ __('student.cal_today_agenda') }}</h2>
                    <span class="sp-pill sp-pill--progress">{{ $stats['today'] }}</span>
                </div>
                @if($todayEvents->isNotEmpty())
                    <div class="space-y-2">
                        @foreach($todayEvents as $event)
                            @php
                                $meta = $typeMeta[$event->type] ?? $typeMeta['other'];
                            @endphp
                            <button type="button"
                                    class="sp-process-row !p-3 w-full text-start border-0 cursor-pointer bg-transparent"
                                    @click="openEventFromServer(@js([
                                        'title' => $event->title,
                                        'rawTitle' => $event->raw_title ?? $event->title,
                                        'type' => $event->type,
                                        'typeLabel' => $meta['label'],
                                        'description' => $event->description,
                                        'location' => $event->location ?? null,
                                        'url' => $event->url ?? null,
                                        'courseTitle' => $event->course_title ?? null,
                                        'start' => optional($event->start_date)->toIso8601String(),
                                        'end' => optional($event->end_date)->toIso8601String(),
                                        'allDay' => (bool) ($event->is_all_day ?? false),
                                        'color' => $event->color,
                                    ]))">
                                <span class="sp-icon-bubble !w-10 !h-10" style="background:{{ $meta['bubble'] }}">
                                    <x-student.figma-icon :name="$meta['icon']" box="size-4" />
                                </span>
                                <span class="flex-1 min-w-0">
                                    <span class="block font-extrabold text-sm truncate">{{ $event->raw_title ?? $event->title }}</span>
                                    <span class="block text-xs mt-0.5 text-[var(--sp-muted)]">
                                        {{ $meta['label'] }}
                                        ·
                                        @if($event->is_all_day)
                                            {{ __('student.cal_all_day') }}
                                        @else
                                            {{ $event->start_date->format('h:i A') }}
                                        @endif
                                    </span>
                                </span>
                                <x-student.figma-icon name="icon-chevron.svg" box="size-3.5" class="opacity-40 rtl:rotate-180" />
                            </button>
                        @endforeach
                    </div>
                @else
                    <div class="rounded-[16px] bg-[#f7f7f5] px-4 py-8 text-center">
                        <span class="sp-icon-bubble mx-auto mb-3" style="background:var(--sp-mint)">
                            <x-student.figma-icon name="icon-calendar.svg" />
                        </span>
                        <p class="font-extrabold m-0 mb-1">{{ __('student.cal_today_empty') }}</p>
                        <p class="text-sm text-[var(--sp-muted)] m-0">{{ __('student.cal_today_empty_hint') }}</p>
                    </div>
                @endif
            </section>
        </div>

        <aside class="space-y-5">
            <section class="sp-card p-5 space-y-4">
                <div class="flex items-center justify-between gap-3">
                    <h2 class="font-extrabold text-base m-0">{{ __('student.cal_upcoming_title') }}</h2>
                    <span class="text-xs font-bold text-[var(--sp-muted)]">{{ __('student.cal_next_count', ['count' => $upcomingEvents->count()]) }}</span>
                </div>

                @if($upcomingEvents->isNotEmpty())
                    <div class="space-y-2 max-h-[34rem] overflow-y-auto pe-1">
                        @foreach($upcomingEvents as $event)
                            @php
                                $meta = $typeMeta[$event->type] ?? $typeMeta['other'];
                            @endphp
                            <button type="button"
                                    class="sp-process-row !p-3 w-full text-start border-0 cursor-pointer bg-transparent"
                                    @click="openEventFromServer(@js([
                                        'title' => $event->title,
                                        'rawTitle' => $event->raw_title ?? $event->title,
                                        'type' => $event->type,
                                        'typeLabel' => $meta['label'],
                                        'description' => $event->description,
                                        'location' => $event->location ?? null,
                                        'url' => $event->url ?? null,
                                        'courseTitle' => $event->course_title ?? null,
                                        'start' => optional($event->start_date)->toIso8601String(),
                                        'end' => optional($event->end_date)->toIso8601String(),
                                        'allDay' => (bool) ($event->is_all_day ?? false),
                                        'color' => $event->color,
                                    ]))">
                                <span class="sp-icon-bubble !w-10 !h-10" style="background:{{ $meta['bubble'] }}">
                                    <x-student.figma-icon :name="$meta['icon']" box="size-4" />
                                </span>
                                <span class="flex-1 min-w-0">
                                    <span class="block font-extrabold text-sm truncate">{{ $event->raw_title ?? $event->title }}</span>
                                    <span class="block text-xs mt-0.5 text-[var(--sp-muted)]">
                                        {{ $event->start_date->translatedFormat($isRtl ? 'D d M' : 'D, M j') }}
                                        @unless($event->is_all_day)
                                            · {{ $event->start_date->format('h:i A') }}
                                        @endunless
                                    </span>
                                    <span class="inline-flex mt-1.5">
                                        <span class="sp-pill {{ $event->type === 'exam' ? 'sp-pill--upcoming' : ($event->type === 'assignment' ? 'sp-pill--done' : 'sp-pill--progress') }}">
                                            {{ $meta['label'] }}
                                        </span>
                                    </span>
                                </span>
                            </button>
                        @endforeach
                    </div>
                @else
                    <div class="rounded-[16px] bg-[#f7f7f5] px-4 py-8 text-center">
                        <p class="font-extrabold m-0 mb-1">{{ __('student.cal_upcoming_empty') }}</p>
                        <p class="text-sm text-[var(--sp-muted)] m-0 mb-4">{{ __('student.cal_upcoming_empty_hint') }}</p>
                        <a href="{{ route('my-courses.index') }}" class="sp-promo-btn !mt-0 inline-flex">{{ __('student.my_courses_active_title') }}</a>
                    </div>
                @endif
            </section>

            <section class="sp-card overflow-hidden" style="background:linear-gradient(145deg, #aed9ea 0%, #d7eef5 55%, #f7f7f5 100%)">
                <div class="p-5 space-y-3">
                    <span class="sp-icon-bubble" style="background:#fff">
                        <x-student.figma-icon name="icon-exams.svg" />
                    </span>
                    <h3 class="font-black text-[var(--sp-accent-text)] text-lg m-0 leading-snug">{{ __('student.cal_tip_title') }}</h3>
                    <p class="text-sm text-[var(--sp-accent-text)]/80 m-0 leading-relaxed">{{ __('student.cal_tip_body') }}</p>
                    <a href="{{ route('student.exams.index') }}" class="sp-promo-btn !mt-2 !bg-[var(--sp-accent-text)] !text-white inline-flex">
                        {{ __('student.cal_tip_cta') }}
                    </a>
                </div>
            </section>
        </aside>
    </div>

    {{-- Event detail modal --}}
    <div x-show="modalOpen"
         x-transition.opacity
         class="fixed inset-0 z-[80] flex items-end sm:items-center justify-center p-0 sm:p-6"
         @keydown.escape.window="closeModal()">
        <div class="absolute inset-0 sp-cal-modal-backdrop" @click="closeModal()"></div>
        <div x-show="modalOpen"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 translate-y-6 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             class="relative w-full sm:max-w-lg sp-card !rounded-t-[24px] sm:!rounded-[24px] p-5 sm:p-6 space-y-4 shadow-2xl max-h-[88vh] overflow-y-auto"
             @click.stop>
            <div class="flex items-start justify-between gap-3">
                <div class="flex items-start gap-3 min-w-0">
                    <span class="sp-icon-bubble shrink-0" :style="{ background: active.color || 'var(--sp-sky)' }"></span>
                    <div class="min-w-0">
                        <p class="text-xs font-bold text-[var(--sp-muted)] m-0 mb-1" x-text="active.typeLabel"></p>
                        <h3 class="font-black text-lg m-0 leading-snug text-[var(--sp-accent-text)]" x-text="active.rawTitle || active.title"></h3>
                    </div>
                </div>
                <button type="button"
                        class="rounded-full size-9 inline-flex items-center justify-center bg-[#f7f7f5] border-0 cursor-pointer shrink-0"
                        @click="closeModal()"
                        aria-label="{{ __('student.cal_close') }}">
                    <span class="text-lg leading-none font-black text-[var(--sp-muted)]">&times;</span>
                </button>
            </div>

            <dl class="space-y-3 m-0 text-sm">
                <div class="flex items-center justify-between gap-3 rounded-[14px] bg-[#f7f7f5] px-3 py-2.5">
                    <dt class="font-bold text-[var(--sp-muted)] m-0">{{ __('student.cal_when') }}</dt>
                    <dd class="m-0 font-extrabold text-end" x-text="active.whenLabel"></dd>
                </div>
                <template x-if="active.courseTitle">
                    <div class="flex items-center justify-between gap-3 rounded-[14px] bg-[#f7f7f5] px-3 py-2.5">
                        <dt class="font-bold text-[var(--sp-muted)] m-0">{{ __('student.cal_course') }}</dt>
                        <dd class="m-0 font-extrabold text-end" x-text="active.courseTitle"></dd>
                    </div>
                </template>
                <template x-if="active.location">
                    <div class="rounded-[14px] bg-[#f7f7f5] px-3 py-2.5">
                        <dt class="font-bold text-[var(--sp-muted)] m-0 mb-1">{{ __('student.cal_location') }}</dt>
                        <dd class="m-0 font-extrabold break-all">
                            <a :href="active.location" class="sp-link" target="_blank" rel="noopener" x-text="active.location"></a>
                        </dd>
                    </div>
                </template>
            </dl>

            <template x-if="active.description">
                <div class="rounded-[16px] border border-black/5 p-4">
                    <p class="text-xs font-bold text-[var(--sp-muted)] m-0 mb-2">{{ __('student.cal_details') }}</p>
                    <p class="text-sm leading-relaxed m-0 whitespace-pre-wrap" x-text="active.description"></p>
                </div>
            </template>

            <div class="flex flex-wrap gap-2 pt-1">
                <template x-if="active.url">
                    <a :href="active.url" class="sp-promo-btn !mt-0">{{ __('student.cal_open_event') }}</a>
                </template>
                <button type="button"
                        class="inline-flex items-center justify-center rounded-[30px] px-4 py-2.5 text-sm font-extrabold bg-[#f7f7f5] text-[var(--sp-accent-text)] border-0 cursor-pointer"
                        @click="closeModal()">
                    {{ __('student.cal_close') }}
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.5/main.min.js"></script>
@if($isRtl)
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.5/locales/ar.js"></script>
@endif
<script>
window.studentCalendarPage = function studentCalendarPage() {
    const locale = @json(app()->getLocale());
    const isRtl = @json($isRtl);
    const eventsUrl = @json(route('calendar.events'));
    const i18n = {
        today: @json(__('student.cal_btn_today')),
        month: @json(__('student.cal_view_month')),
        week: @json(__('student.cal_view_week')),
        day: @json(__('student.cal_view_day')),
        list: @json(__('student.cal_view_list')),
        loadError: @json(__('student.cal_load_error')),
        allDay: @json(__('student.cal_all_day')),
        filters: {
            all: @json(__('student.cal_filter_all')),
            exam: @json(__('student.legend_exams')),
            lecture: @json(__('student.legend_lectures')),
            assignment: @json(__('student.legend_assignments')),
            other: @json(__('student.other_events')),
        },
    };

    return {
        calendar: null,
        filter: 'all',
        modalOpen: false,
        active: {},
        filters: [
            { key: 'all', label: i18n.filters.all },
            { key: 'exam', label: i18n.filters.exam },
            { key: 'lecture', label: i18n.filters.lecture },
            { key: 'assignment', label: i18n.filters.assignment },
            { key: 'other', label: i18n.filters.other },
        ],

        init() {
            const el = document.getElementById('student-calendar');
            if (!el || typeof FullCalendar === 'undefined') return;

            const self = this;
            this.calendar = new FullCalendar.Calendar(el, {
                locale: isRtl ? 'ar' : locale,
                direction: isRtl ? 'rtl' : 'ltr',
                initialView: window.matchMedia('(max-width: 640px)').matches ? 'listWeek' : 'dayGridMonth',
                headerToolbar: {
                    start: isRtl ? 'dayGridMonth,timeGridWeek,listWeek' : 'prev,next today',
                    center: 'title',
                    end: isRtl ? 'prev,next today' : 'dayGridMonth,timeGridWeek,listWeek',
                },
                buttonText: {
                    today: i18n.today,
                    month: i18n.month,
                    week: i18n.week,
                    day: i18n.day,
                    list: i18n.list,
                },
                events: function(info, success, failure) {
                    const url = new URL(eventsUrl, window.location.origin);
                    url.searchParams.set('start', info.startStr);
                    url.searchParams.set('end', info.endStr);
                    if (self.filter && self.filter !== 'all') {
                        url.searchParams.set('type', self.filter);
                    }
                    fetch(url.toString(), { headers: { 'Accept': 'application/json' } })
                        .then(r => r.json())
                        .then(success)
                        .catch(() => {
                            failure();
                            alert(i18n.loadError);
                        });
                },
                eventClick: function(info) {
                    info.jsEvent.preventDefault();
                    self.openFromFc(info.event);
                },
                height: 'auto',
                contentHeight: 620,
                firstDay: isRtl ? 6 : 0,
                weekends: true,
                navLinks: true,
                dayMaxEvents: 3,
                moreLinkClick: 'popover',
                nowIndicator: true,
                eventDisplay: 'block',
            });

            this.calendar.render();
        },

        setFilter(key) {
            this.filter = key;
            if (this.calendar) this.calendar.refetchEvents();
        },

        goToday() {
            if (this.calendar) this.calendar.today();
        },

        formatWhen(startIso, endIso, allDay) {
            if (!startIso) return '—';
            const start = new Date(startIso);
            const optsDate = { weekday: 'short', year: 'numeric', month: 'short', day: 'numeric' };
            const optsTime = { hour: 'numeric', minute: '2-digit' };
            const datePart = start.toLocaleDateString(locale === 'ar' ? 'ar-EG' : 'en-US', optsDate);
            if (allDay) return datePart + ' · ' + i18n.allDay;
            const timePart = start.toLocaleTimeString(locale === 'ar' ? 'ar-EG' : 'en-US', optsTime);
            let label = datePart + ' · ' + timePart;
            if (endIso) {
                const end = new Date(endIso);
                label += ' – ' + end.toLocaleTimeString(locale === 'ar' ? 'ar-EG' : 'en-US', optsTime);
            }
            return label;
        },

        openFromFc(event) {
            const xp = event.extendedProps || {};
            this.active = {
                title: event.title,
                rawTitle: xp.rawTitle || event.title,
                type: xp.type || 'other',
                typeLabel: xp.typeLabel || '',
                description: xp.description || null,
                location: xp.location || null,
                url: event.url || null,
                courseTitle: xp.courseTitle || null,
                color: event.backgroundColor || 'var(--sp-sky)',
                whenLabel: this.formatWhen(event.startStr || (event.start && event.start.toISOString()), event.endStr || (event.end && event.end.toISOString()), event.allDay),
            };
            this.modalOpen = true;
        },

        openEventFromServer(payload) {
            this.active = {
                title: payload.title,
                rawTitle: payload.rawTitle || payload.title,
                type: payload.type || 'other',
                typeLabel: payload.typeLabel || '',
                description: payload.description || null,
                location: payload.location || null,
                url: payload.url || null,
                courseTitle: payload.courseTitle || null,
                color: payload.color || 'var(--sp-sky)',
                whenLabel: this.formatWhen(payload.start, payload.end, payload.allDay),
            };
            this.modalOpen = true;
        },

        closeModal() {
            this.modalOpen = false;
        },
    };
}
</script>
@endpush
