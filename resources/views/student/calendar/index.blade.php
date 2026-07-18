@extends('layouts.student-dashboard')

@section('title', __('student.calendar_title'))

@php
    $locale = app()->getLocale();
    $isRtl = $locale === 'ar';
    $upcoming = $events->where('start_date', '>=', now())->take(10);
@endphp

@push('styles')
@include('student.offline-courses.partials.los-styles')
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.5/main.min.css" rel="stylesheet" />
<style>
    .cal-layout {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 280px;
        gap: 20px;
        align-items: start;
    }
    @media (max-width: 1099px) {
        .cal-layout { grid-template-columns: 1fr; }
    }
    .cal-aside { display: flex; flex-direction: column; gap: 12px; }
    @media (min-width: 1100px) {
        .cal-aside-sticky { position: sticky; top: 12px; }
    }
    .cal-board {
        background: var(--ml-surface);
        border: 1px solid var(--ml-line);
        border-radius: var(--ml-r);
        padding: 14px 16px 18px;
    }
    .cal-legend {
        display: flex; flex-wrap: wrap; gap: 12px 16px;
        margin-top: 14px; padding-top: 14px;
        border-top: 1px solid var(--ml-line);
        font-size: 12px; color: var(--ml-muted); font-weight: 600;
    }
    .cal-legend span {
        display: inline-flex; align-items: center; gap: 6px;
    }
    .cal-legend i {
        width: 12px; height: 12px; border-radius: 4px; display: inline-block;
    }
    .cal-event {
        display: flex; gap: 10px; padding: 10px 12px;
        border: 1px solid var(--ml-line); border-radius: 10px;
        text-decoration: none !important; color: inherit !important;
        transition: border-color var(--ml-fast) ease, background var(--ml-fast) ease;
        cursor: pointer; background: var(--ml-surface);
    }
    .cal-event:hover {
        border-color: rgba(73, 164, 162, 0.4);
        background: rgba(73, 164, 162, 0.06);
    }
    .cal-event .dot {
        width: 10px; height: 10px; border-radius: 999px; margin-top: 5px; flex-shrink: 0;
    }
    .cal-event strong {
        display: block; font-size: 13px; font-weight: 700; line-height: 1.35;
        margin-bottom: 2px;
    }
    .cal-event .when { font-size: 11px; color: var(--ml-muted); }
    .cal-event .kind {
        margin-top: 4px; font-size: 11px; font-weight: 700; color: var(--ml-teal-deep);
    }
    .cal-stat-row {
        display: flex; align-items: center; justify-content: space-between;
        gap: 8px; padding: 10px 12px; border-radius: 10px;
        background: var(--ml-well); margin-bottom: 8px;
        font-size: 13px; font-weight: 600; color: var(--ml-ink);
    }
    .cal-stat-row:last-child { margin-bottom: 0; }
    .cal-stat-row .n { font-weight: 700; color: var(--ml-teal-deep); font-size: 1rem; }

    /* FullCalendar LOS theme */
    .oc .fc { font-family: inherit; color: var(--ml-ink); }
    .oc .fc-theme-standard td, .oc .fc-theme-standard th,
    .oc .fc-theme-standard .fc-scrollgrid {
        border-color: var(--ml-line);
    }
    .oc .fc-col-header-cell-cushion,
    .oc .fc-daygrid-day-number {
        color: var(--ml-ink); text-decoration: none !important; font-weight: 600;
    }
    .oc .fc-toolbar-title { font-size: 1.05rem; font-weight: 700; }
    .oc .fc-button-primary {
        background: var(--ml-teal) !important;
        border-color: var(--ml-teal) !important;
        box-shadow: none !important;
        font-weight: 700;
        text-transform: none;
    }
    .oc .fc-button-primary:not(:disabled):hover,
    .oc .fc-button-primary:not(:disabled).fc-button-active {
        background: var(--ml-teal-deep) !important;
        border-color: var(--ml-teal-deep) !important;
    }
    .oc .fc-day-today { background: rgba(73, 164, 162, 0.08) !important; }
    .oc .fc-event {
        border-radius: 6px; border: 0; padding: 2px 4px; cursor: pointer;
    }
    .oc .fc-daygrid-event { white-space: normal; font-size: 0.8rem; }
</style>
@endpush

@section('content')
<div class="oc">
    <header class="oc-chrome">
        <div>
            <nav class="oc-crumb" aria-label="{{ __('student.calendar_title') }}">
                <a href="{{ route('dashboard') }}">{{ __('student.learning_center') }}</a>
                <span aria-hidden="true">/</span>
                <span style="color:var(--ml-ink);font-weight:700">{{ __('student.calendar_title') }}</span>
            </nav>
            <h1>{{ __('student.calendar_title') }}</h1>
            <p class="sub">{{ __('student.calendar_subtitle') }}</p>
        </div>
        <div class="oc-signals">
            <span class="oc-signal oc-signal-live">{{ __('student.total_events') }}: {{ $stats['total'] ?? 0 }}</span>
            <span class="oc-signal oc-signal-hot">{{ __('student.calendar_upcoming_count') }}: {{ $stats['upcoming'] ?? 0 }}</span>
        </div>
    </header>

    <div class="oc-pulse" aria-label="{{ __('student.calendar_stats') }}">
        <div>
            <span class="lbl">{{ __('student.legend_exams') }}</span>
            <span class="val teal">{{ $stats['exams'] ?? 0 }}</span>
        </div>
        <div>
            <span class="lbl">{{ __('student.legend_lectures') }}</span>
            <span class="val">{{ $stats['lectures'] ?? 0 }}</span>
        </div>
        <div>
            <span class="lbl">{{ __('student.legend_assignments') }}</span>
            <span class="val hot">{{ $stats['assignments'] ?? 0 }}</span>
        </div>
        <div>
            <span class="lbl">{{ __('student.calendar_upcoming_count') }}</span>
            <span class="val">{{ $stats['upcoming'] ?? 0 }}</span>
        </div>
    </div>

    <div class="cal-layout">
        <div class="cal-board">
            <div id="calendar"></div>
            <div class="cal-legend">
                <span><i style="background:#ef4444"></i> {{ __('student.legend_exams') }}</span>
                <span><i style="background:#49A4A2"></i> {{ __('student.legend_lectures') }}</span>
                <span><i style="background:#f59e0b"></i> {{ __('student.legend_assignments') }}</span>
                <span><i style="background:#10b981"></i> {{ __('student.other_events') }}</span>
            </div>
        </div>

        <aside class="cal-aside">
            <div class="oc-panel cal-aside-sticky">
                <p class="oc-label">{{ __('student.upcoming_events') }}</p>
                <div style="display:flex;flex-direction:column;gap:8px;max-height:28rem;overflow:auto">
                    @forelse($upcoming as $event)
                        @php
                            $typeLabel = match ($event->type ?? '') {
                                'exam' => __('student.event_type_exam'),
                                'lecture' => __('student.event_type_lecture'),
                                'assignment' => __('student.event_type_assignment'),
                                default => __('student.event_type_event'),
                            };
                            $typeIcon = match ($event->type ?? '') {
                                'exam' => 'fa-clipboard-check',
                                'lecture' => 'fa-chalkboard-teacher',
                                'assignment' => 'fa-tasks',
                                default => 'fa-calendar-alt',
                            };
                        @endphp
                        <a class="cal-event" href="{{ $event->url ?? '#' }}">
                            <span class="dot" style="background:{{ $event->color ?? '#49A4A2' }}"></span>
                            <div class="min-w-0">
                                <strong>{{ $event->title }}</strong>
                                <div class="when">
                                    {{ $event->start_date->format('d/m/Y') }}
                                    @if(!($event->is_all_day ?? false))
                                        · {{ $event->start_date->format('H:i') }}
                                    @endif
                                </div>
                                <div class="kind"><i class="fas {{ $typeIcon }}"></i> {{ $typeLabel }}</div>
                            </div>
                        </a>
                    @empty
                        <div class="oc-empty" style="padding:24px 12px">
                            <div class="icon" style="width:44px;height:44px;font-size:18px"><i class="fas fa-calendar-times"></i></div>
                            <p style="margin:0">{{ __('student.no_upcoming_events') }}</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="oc-panel">
                <p class="oc-label">{{ __('student.calendar_stats') }}</p>
                <div class="cal-stat-row">
                    <span><i class="fas fa-clipboard-check text-xs" style="color:var(--ml-teal-deep);margin-inline-end:6px"></i>{{ __('student.legend_exams') }}</span>
                    <span class="n">{{ $stats['exams'] ?? 0 }}</span>
                </div>
                <div class="cal-stat-row">
                    <span><i class="fas fa-chalkboard-teacher text-xs" style="color:var(--ml-teal-deep);margin-inline-end:6px"></i>{{ __('student.legend_lectures') }}</span>
                    <span class="n">{{ $stats['lectures'] ?? 0 }}</span>
                </div>
                <div class="cal-stat-row">
                    <span><i class="fas fa-tasks text-xs" style="color:var(--ml-teal-deep);margin-inline-end:6px"></i>{{ __('student.legend_assignments') }}</span>
                    <span class="n">{{ $stats['assignments'] ?? 0 }}</span>
                </div>
                <div class="cal-stat-row">
                    <span><i class="fas fa-arrow-up text-xs" style="color:var(--ml-teal-deep);margin-inline-end:6px"></i>{{ __('student.calendar_upcoming_count') }}</span>
                    <span class="n">{{ $stats['upcoming'] ?? 0 }}</span>
                </div>
            </div>
        </aside>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.5/main.min.js"></script>
@if($isRtl)
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.5/locales/ar.js"></script>
@endif
<script>
document.addEventListener('DOMContentLoaded', function () {
    var calendarEl = document.getElementById('calendar');
    if (!calendarEl || typeof FullCalendar === 'undefined') return;

    var calendar = new FullCalendar.Calendar(calendarEl, {
        locale: @json($isRtl ? 'ar' : 'en'),
        direction: @json($isRtl ? 'rtl' : 'ltr'),
        initialView: 'dayGridMonth',
        headerToolbar: {
            start: 'prev,next today',
            center: 'title',
            end: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        buttonText: {
            today: @json(__('student.calendar_today')),
            month: @json(__('student.calendar_month')),
            week: @json(__('student.calendar_week')),
            day: @json(__('student.calendar_day'))
        },
        events: {
            url: @json(route('calendar.events')),
            failure: function () {
                alert(@json(__('student.calendar_load_error')));
            }
        },
        eventClick: function (info) {
            if (info.event.url) {
                window.open(info.event.url, '_self');
                info.jsEvent.preventDefault();
            }
        },
        eventContent: function (arg) {
            return { html: '<div class="fc-event-title">' + arg.event.title + '</div>' };
        },
        height: 'auto',
        contentHeight: 600,
        firstDay: 6,
        weekends: true,
        navLinks: true,
        dayMaxEvents: 3,
        moreLinkClick: 'popover'
    });

    calendar.render();
});
</script>
@endpush
