@extends('layouts.student-dashboard')

@section('title', __('student.oc_lectures_title') . ' — ' . $offlineCourse->title)
@section('header', __('student.oc_lectures_title'))

@push('styles')
<style>
    .lecture-row { border-bottom: 1px solid rgba(0,0,0,.05); }
    .lecture-row:last-child { border-bottom: 0; }
    .lecture-row.is-active { background: rgba(174, 217, 234, .12); }
    [x-cloak] { display: none !important; }
</style>
@endpush

@section('content')
@php
    $sg = $studentRouteGroup ?? 'student.offline-courses';
    $isOnlineChannel = ($channel ?? 'offline') === 'online';
    $chLabel = $isOnlineChannel ? __('student.exam_source_online') : __('student.exam_source_offline');
    $lectureCount = $lectures->count();

    $withRecording = 0;
    $upcomingCount = 0;
    $pastCount = 0;
    foreach ($lectures as $L) {
        if ($L->hasPlayableRecording()) {
            $withRecording++;
        }
        $d = null;
        if ($L->relationLoaded('groupSession') && $L->groupSession && $L->groupSession->session_date) {
            $d = $L->groupSession->session_date;
        } elseif ($L->scheduled_at) {
            $d = $L->scheduled_at;
        }
        if ($d) {
            if ($d->isFuture() || $d->isToday()) {
                $upcomingCount++;
            } else {
                $pastCount++;
            }
        }
    }
@endphp

<div class="space-y-5" x-data="window.__offlineLecturesPage()">
    <nav class="flex flex-wrap items-center gap-2 text-sm font-bold text-[var(--sp-muted)]">
        <a href="{{ route($sg . '.index') }}" class="sp-link">{{ $isOnlineChannel ? __('student.online_courses_title') : __('student.offline_courses_title') }}</a>
        <x-student.figma-icon name="icon-chevron.svg" box="size-3" class="opacity-40 rtl:rotate-180" />
        <a href="{{ route($sg . '.show', $offlineCourse) }}" class="sp-link truncate max-w-[40vw]">{{ $offlineCourse->title }}</a>
        <x-student.figma-icon name="icon-chevron.svg" box="size-3" class="opacity-40 rtl:rotate-180" />
        <span class="text-[var(--sp-text)]">{{ __('student.oc_lectures_title') }}</span>
    </nav>

    <section class="sp-card p-5 sm:p-6 space-y-4">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div class="flex items-start gap-4 min-w-0">
                <span class="sp-icon-bubble shrink-0 !w-14 !h-14" style="background:var(--sp-mint)">
                    <x-student.figma-icon name="icon-classes.svg" box="size-7" />
                </span>
                <div class="min-w-0">
                    <p class="text-xs font-bold text-[var(--sp-muted)] m-0 mb-1 uppercase tracking-wide">{{ __('student.oc_lectures_eyebrow') }} · {{ $chLabel }}</p>
                    <h2 class="sp-section-title m-0">{{ $offlineCourse->title }}</h2>
                    <p class="text-sm text-[var(--sp-muted)] m-0 mt-2 max-w-2xl">{{ __('student.oc_lectures_subtitle') }}</p>
                </div>
            </div>
            <div class="flex flex-wrap gap-2 shrink-0">
                <a href="{{ route($sg . '.show', $offlineCourse) }}" class="inline-flex items-center justify-center rounded-[30px] px-4 py-2.5 text-sm font-extrabold bg-[#f7f7f5] text-[var(--sp-accent-text)] hover:bg-[var(--sp-accent)] transition-colors">{{ __('student.oc_back_course') }}</a>
                <a href="{{ route($sg . '.resources', $offlineCourse) }}" class="sp-promo-btn !mt-0">{{ __('student.oc_tile_resources') }}</a>
            </div>
        </div>
    </section>

    <div class="grid grid-cols-2 gap-3 sm:gap-4 lg:grid-cols-4">
        <div class="sp-card p-4 sm:p-5">
            <p class="text-xs font-bold text-[var(--sp-muted)] m-0 uppercase tracking-wide">{{ __('student.oc_stat_lectures') }}</p>
            <p class="text-2xl font-black text-[var(--sp-accent-text)] m-0 mt-1">{{ $lectureCount }}</p>
        </div>
        <div class="sp-card p-4 sm:p-5">
            <p class="text-xs font-bold text-[var(--sp-muted)] m-0 uppercase tracking-wide">{{ __('student.oc_stat_recordings') }}</p>
            <p class="text-2xl font-black text-[var(--sp-accent-text)] m-0 mt-1">{{ $withRecording }}</p>
        </div>
        <div class="sp-card p-4 sm:p-5">
            <p class="text-xs font-bold text-[var(--sp-muted)] m-0 uppercase tracking-wide">{{ __('student.oc_stat_upcoming') }}</p>
            <p class="text-2xl font-black text-[var(--sp-accent-text)] m-0 mt-1">{{ $upcomingCount }}</p>
        </div>
        <div class="sp-card p-4 sm:p-5">
            <p class="text-xs font-bold text-[var(--sp-muted)] m-0 uppercase tracking-wide">{{ __('student.oc_group') }}</p>
            <p class="text-lg font-extrabold text-[var(--sp-accent-text)] m-0 mt-1 truncate">{{ $enrollment->group->name ?? '—' }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 items-start gap-5 xl:grid-cols-12">
        <div class="min-w-0 space-y-4 xl:col-span-8">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <h3 class="flex items-center gap-2 font-extrabold text-lg m-0">
                    <span class="sp-icon-bubble" style="background:var(--sp-mint)"><x-student.figma-icon name="icon-classes.svg" /></span>
                    {{ __('student.oc_lectures_list') }}
                </h3>
                <p class="text-sm font-bold text-[var(--sp-muted)] m-0">
                    {{ __('student.oc_showing') }} <span class="text-[var(--sp-accent-text)]" x-text="visibleCount"></span> / {{ $lectureCount }}
                </p>
            </div>

            <div class="sp-card overflow-hidden">
                @if($lectures->isEmpty())
                    <div class="px-6 py-14 text-center">
                        <span class="sp-icon-bubble mx-auto mb-3" style="background:var(--sp-mint)"><x-student.figma-icon name="icon-classes.svg" /></span>
                        <p class="font-extrabold m-0">{{ __('student.oc_no_lectures') }}</p>
                        <p class="text-sm text-[var(--sp-muted)] m-0 mt-1">{{ __('student.oc_no_lectures_hint') }}</p>
                    </div>
                @else
                    <div x-ref="list">
                        @foreach($lectures as $lecture)
                            @php
                                $agendaLines = filled($lecture->session_agenda)
                                    ? array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', $lecture->session_agenda))))
                                    : [];
                                $linksCount = is_array($lecture->download_links) ? count($lecture->download_links) : 0;
                                $filesCount = is_array($lecture->attachments) ? count($lecture->attachments) : 0;
                                $hasRecording = $lecture->hasPlayableRecording();
                                $hasMeeting = ($channel ?? 'offline') === 'online' && filled($lecture->meeting_url);
                                $hasMaterials = ($hasRecording ? 1 : 0) + ($hasMeeting ? 1 : 0) + $linksCount + $filesCount;
                                $hasDetails = count($agendaLines) > 0 || filled($lecture->offline_attendee_mindmap) || $linksCount > 0 || $filesCount > 0;

                                $whenText = null;
                                $whenISO = null;
                                $timeText = null;
                                $groupName = null;
                                if ($lecture->relationLoaded('groupSession') && $lecture->groupSession) {
                                    $whenText = $lecture->groupSession->session_date->translatedFormat('l j F Y');
                                    $whenISO = optional($lecture->groupSession->session_date)->toDateString();
                                    $lgt = $lecture->groupSession->start_time;
                                    $timeText = is_string($lgt) ? substr($lgt, 0, 5) : $lgt;
                                    $groupName = optional($lecture->groupSession->group)->name;
                                } elseif ($lecture->scheduled_at) {
                                    $whenText = $lecture->scheduled_at->translatedFormat('l j F Y — H:i');
                                    $whenISO = optional($lecture->scheduled_at)->toIso8601String();
                                }

                                $statusLabel = null;
                                $statusPill = 'sp-pill';
                                $dateForStatus = null;
                                if ($lecture->relationLoaded('groupSession') && $lecture->groupSession && $lecture->groupSession->session_date) {
                                    $dateForStatus = $lecture->groupSession->session_date;
                                } elseif ($lecture->scheduled_at) {
                                    $dateForStatus = $lecture->scheduled_at;
                                }
                                if ($dateForStatus) {
                                    if ($dateForStatus->isToday()) {
                                        $statusLabel = __('student.oc_today');
                                        $statusPill = 'sp-pill sp-pill--upcoming';
                                    } elseif ($dateForStatus->isFuture()) {
                                        $statusLabel = __('student.oc_upcoming');
                                        $statusPill = 'sp-pill sp-pill--done';
                                    } else {
                                        $statusLabel = __('student.oc_past');
                                        $statusPill = 'sp-pill';
                                    }
                                }
                            @endphp

                            <article
                                id="offline-lecture-{{ $lecture->id }}"
                                class="lecture-row scroll-mt-24"
                                :class="openId === {{ $lecture->id }} ? 'is-active' : ''"
                                data-title="{{ mb_strtolower((string) $lecture->title) }}"
                                data-description="{{ mb_strtolower((string) ($lecture->description ?? '')) }}"
                                data-agenda="{{ mb_strtolower((string) ($lecture->session_agenda ?? '')) }}"
                                data-has-materials="{{ $hasMaterials > 0 ? '1' : '0' }}"
                                data-when="{{ $whenISO ?? '' }}"
                                x-show="matches($el)"
                                x-transition.opacity.duration.120ms
                            >
                                <div class="grid grid-cols-1 gap-4 p-4 sm:p-5 lg:grid-cols-12 lg:gap-6">
                                    {{-- معلومات المحاضرة --}}
                                    <div class="min-w-0 lg:col-span-7">
                                        <div class="flex gap-3">
                                            <span class="mt-0.5 flex h-10 w-10 shrink-0 items-center justify-center rounded-[14px] text-sm font-black tabular-nums" style="background:var(--sp-mint);color:var(--sp-accent-text)">
                                                {{ $loop->iteration }}
                                            </span>
                                            <div class="min-w-0 flex-1 space-y-2">
                                                <div class="flex flex-wrap items-center gap-2">
                                                    <h3 class="text-base font-extrabold leading-snug m-0 sm:text-lg">{{ $lecture->title }}</h3>
                                                    @if($statusLabel)
                                                        <span class="{{ $statusPill }}">{{ $statusLabel }}</span>
                                                    @endif
                                                    @if($hasRecording)
                                                        <span class="sp-pill sp-pill--progress">{{ __('student.oc_recording_badge') }}</span>
                                                    @endif
                                                </div>

                                                @if($whenText)
                                                    <p class="text-xs font-semibold text-slate-500 sm:text-sm">
                                                        <i class="far fa-calendar-alt ml-1"></i>{{ $whenText }}
                                                        @if($timeText)
                                                            <span class="mx-1 text-slate-300">·</span>
                                                            <i class="far fa-clock ml-1"></i>{{ $timeText }}
                                                        @endif
                                                        @if($groupName)
                                                            <span class="mx-1 text-slate-300">·</span>
                                                            <i class="fas fa-users ml-1 text-[10px]"></i>{{ $groupName }}
                                                        @endif
                                                    </p>
                                                @endif

                                                @if($lecture->description)
                                                    <p class="text-sm leading-relaxed text-slate-600 line-clamp-2">{{ $lecture->description }}</p>
                                                @endif

                                                <div class="flex flex-wrap gap-3 text-[11px] font-bold text-slate-500">
                                                    @if(count($agendaLines))
                                                        <span><i class="fas fa-list-ul ml-1 text-violet-400"></i>{{ count($agendaLines) }} نقاط اليوم</span>
                                                    @endif
                                                    @if($filesCount)
                                                        <span><i class="fas fa-paperclip ml-1 text-sky-400"></i>{{ $filesCount }} مرفق</span>
                                                    @endif
                                                    @if($linksCount)
                                                        <span><i class="fas fa-link ml-1 text-amber-500"></i>{{ $linksCount }} رابط</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- إجراءات --}}
                                    <div class="flex flex-col justify-center gap-2 lg:col-span-5">
                                        <div class="grid grid-cols-1 gap-2 sm:grid-cols-2 lg:grid-cols-1 xl:grid-cols-2">
                                            @if($hasRecording)
                                                <button type="button"
                                                        class="js-open-student-recording sp-promo-btn !mt-0 !py-2.5 border-0 cursor-pointer inline-flex items-center justify-center gap-2"
                                                        data-watch-url="{{ route($sg . '.lectures.watch', [$offlineCourse, $lecture]) }}"
                                                        data-title="{{ $lecture->title }}">
                                                    {{ __('student.oc_watch_recording') }}
                                                </button>
                                            @endif
                                            @if($hasMeeting)
                                                <a href="{{ $lecture->meeting_url }}" target="_blank" rel="noopener"
                                                   class="inline-flex items-center justify-center gap-2 rounded-[30px] px-4 py-2.5 text-sm font-extrabold bg-[var(--sp-accent)] text-[var(--sp-accent-text)]">
                                                    {{ __('student.oc_join_meeting') }}
                                                </a>
                                            @endif
                                            @if($hasDetails)
                                                <button type="button"
                                                        class="inline-flex items-center justify-center gap-2 rounded-[30px] bg-[#f7f7f5] px-4 py-2.5 text-sm font-extrabold text-[var(--sp-accent-text)] border-0 cursor-pointer sm:col-span-2 xl:col-span-1"
                                                        @click="toggleDetails({{ $lecture->id }})">
                                                    <span x-text="openId === {{ $lecture->id }} ? @js(__('student.oc_hide_details')) : @js(__('student.oc_show_details'))"></span>
                                                </button>
                                            @elseif(! $hasRecording && ! $hasMeeting)
                                                <p class="text-center text-xs font-semibold text-[var(--sp-muted)] sm:col-span-2">{{ __('student.oc_no_materials_yet') }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                {{-- تفاصيل موسّعة بعرض كامل الصف --}}
                                @if($hasDetails)
                                    <div x-show="openId === {{ $lecture->id }}" x-cloak class="border-t border-slate-100 bg-slate-50/60 px-4 py-5 sm:px-5">
                                        <div class="grid grid-cols-1 gap-5 lg:grid-cols-2">
                                            <div class="space-y-4">
                                                @if(count($agendaLines))
                                                    <section class="rounded-xl border border-slate-200 bg-white p-4">
                                                        <h4 class="mb-3 text-xs font-bold uppercase tracking-wide text-slate-500">برنامج اليوم</h4>
                                                        <ul class="max-h-64 space-y-2 overflow-auto text-sm text-slate-700">
                                                            @foreach($agendaLines as $line)
                                                                <li class="flex gap-2 leading-relaxed">
                                                                    <span class="mt-2 h-1.5 w-1.5 shrink-0 rounded-full bg-violet-500"></span>
                                                                    <span>{{ $line }}</span>
                                                                </li>
                                                            @endforeach
                                                        </ul>
                                                    </section>
                                                @endif
                                                @include('partials.offline-mindmap-visual', ['text' => $lecture->offline_attendee_mindmap])
                                            </div>

                                            <div class="space-y-4">
                                                @if($linksCount > 0)
                                                    <section class="rounded-xl border border-slate-200 bg-white p-4">
                                                        <h4 class="mb-3 text-xs font-bold uppercase tracking-wide text-slate-500">روابط التحميل</h4>
                                                        <ul class="space-y-2">
                                                            @foreach($lecture->download_links as $link)
                                                                <li>
                                                                    <a href="{{ $link['url'] ?? '#' }}" target="_blank" rel="noopener"
                                                                       class="inline-flex w-full items-center gap-2 rounded-lg border border-sky-100 bg-sky-50 px-3 py-2.5 text-sm font-bold text-sky-800 hover:bg-sky-100">
                                                                        <i class="fas fa-download text-xs"></i>
                                                                        <span class="truncate">{{ $link['label'] ?? 'رابط' }}</span>
                                                                    </a>
                                                                </li>
                                                            @endforeach
                                                        </ul>
                                                    </section>
                                                @endif

                                                @if($filesCount > 0)
                                                    <section class="rounded-xl border border-slate-200 bg-white p-4">
                                                        <h4 class="mb-3 text-xs font-bold uppercase tracking-wide text-slate-500">مرفقات</h4>
                                                        <ul class="space-y-2">
                                                            @foreach($lecture->attachments as $att)
                                                                @php $name = (string) ($att['name'] ?? 'ملف'); @endphp
                                                                <li>
                                                                    <a href="{{ asset('storage/' . ($att['path'] ?? '')) }}" target="_blank" rel="noopener"
                                                                       class="inline-flex w-full items-center gap-2 rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-sm font-bold text-slate-700 hover:bg-slate-50">
                                                                        <i class="fas fa-paperclip text-slate-400 text-xs"></i>
                                                                        <span class="truncate">{{ $name }}</span>
                                                                    </a>
                                                                </li>
                                                            @endforeach
                                                        </ul>
                                                    </section>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </article>
                        @endforeach
                    </div>

                    <p class="border-t border-slate-100 px-6 py-8 text-center text-sm text-slate-400" x-show="visibleCount === 0" x-cloak>
                        لا توجد محاضرات تطابق البحث أو الفلاتر.
                    </p>
                @endif
            </div>
        </div>

        <aside class="min-w-0 space-y-5 xl:col-span-4">
            <div class="sp-card p-5 space-y-5 xl:sticky xl:top-4">
                <div>
                    <p class="mb-2 text-xs font-bold uppercase tracking-wide text-[var(--sp-muted)]">{{ __('student.oc_search_filter') }}</p>
                    <div class="flex items-center gap-2 rounded-[30px] bg-[#f7f7f5] px-4 py-2.5">
                        <x-student.figma-icon name="icon-search.svg" box="size-4" class="opacity-50" />
                        <input
                            x-model.trim="q"
                            type="search"
                            placeholder="{{ __('student.oc_search_lectures') }}"
                            class="w-full min-w-0 border-0 bg-transparent p-0 text-sm font-bold focus:outline-none focus:ring-0"
                        >
                    </div>
                    <div class="mt-3 flex flex-col gap-2">
                        <label class="flex cursor-pointer items-center justify-between rounded-[16px] bg-[#f7f7f5] px-3 py-2.5 text-sm font-extrabold">
                            <span>{{ __('student.oc_filter_materials') }}</span>
                            <input type="checkbox" class="rounded border-slate-300 text-[var(--sp-accent-text)] focus:ring-[var(--sp-accent)]" x-model="onlyWithMaterials">
                        </label>
                        <label class="flex cursor-pointer items-center justify-between rounded-[16px] bg-[#f7f7f5] px-3 py-2.5 text-sm font-extrabold">
                            <span>{{ __('student.oc_filter_upcoming') }}</span>
                            <input type="checkbox" class="rounded border-slate-300 text-[var(--sp-accent-text)] focus:ring-[var(--sp-accent)]" x-model="onlyUpcoming">
                        </label>
                        <label class="flex cursor-pointer items-center justify-between rounded-[16px] bg-[#f7f7f5] px-3 py-2.5 text-sm font-extrabold">
                            <span>{{ __('student.oc_filter_past') }}</span>
                            <input type="checkbox" class="rounded border-slate-300 text-[var(--sp-accent-text)] focus:ring-[var(--sp-accent)]" x-model="onlyPast">
                        </label>
                    </div>
                    <button type="button"
                            class="mt-3 w-full rounded-[30px] bg-[#f7f7f5] px-3 py-2.5 text-sm font-extrabold text-[var(--sp-accent-text)] border-0 cursor-pointer hover:bg-[var(--sp-accent)]"
                            @click="q=''; onlyWithMaterials=false; onlyUpcoming=false; onlyPast=false">
                        {{ __('student.oc_reset_filters') }}
                    </button>
                </div>

                <div class="border-t border-black/5 pt-4 space-y-2">
                    <p class="mb-2 text-xs font-bold uppercase tracking-wide text-[var(--sp-muted)]">{{ __('student.oc_quick_links') }}</p>
                    <a href="{{ route($sg . '.curriculum', $offlineCourse) }}" class="flex items-center justify-between gap-2 rounded-[16px] bg-[#f7f7f5] px-4 py-3 text-sm font-extrabold hover:bg-[var(--sp-accent)] transition-colors">
                        <span>{{ __('student.oc_tile_curriculum') }}</span>
                        <x-student.figma-icon name="icon-chevron.svg" box="size-3" class="opacity-40 rtl:rotate-180" />
                    </a>
                    <a href="{{ route($sg . '.schedule', $offlineCourse) }}" class="flex items-center justify-between gap-2 rounded-[16px] bg-[#f7f7f5] px-4 py-3 text-sm font-extrabold hover:bg-[var(--sp-accent)] transition-colors">
                        <span>{{ __('student.oc_tile_schedule') }}</span>
                        <x-student.figma-icon name="icon-chevron.svg" box="size-3" class="opacity-40 rtl:rotate-180" />
                    </a>
                    <a href="{{ route($sg . '.resources', $offlineCourse) }}" class="flex items-center justify-between gap-2 rounded-[16px] bg-[#f7f7f5] px-4 py-3 text-sm font-extrabold hover:bg-[var(--sp-accent)] transition-colors">
                        <span>{{ __('student.oc_tile_resources') }}</span>
                        <x-student.figma-icon name="icon-chevron.svg" box="size-3" class="opacity-40 rtl:rotate-180" />
                    </a>
                </div>

                <div class="rounded-[16px] px-4 py-3 text-xs leading-relaxed" style="background:var(--sp-mint);color:var(--sp-accent-text)">
                    <p class="font-extrabold mb-1 m-0">{{ __('student.oc_tip_title') }}</p>
                    <p class="m-0">{{ __('student.oc_tip_recording') }}</p>
                </div>
            </div>
        </aside>
    </div>
</div>

{{-- بوب أب التسجيل --}}
<div id="studentRecordingModal" class="fixed inset-0 z-[100] hidden" aria-hidden="true">
    <div class="absolute inset-0 bg-slate-900/70 backdrop-blur-sm" data-close-student-recording></div>
    <div class="relative z-10 flex min-h-full items-center justify-center p-3 sm:p-6">
        <div class="w-full max-w-5xl overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-2xl">
            <div class="flex items-center justify-between gap-3 border-b border-slate-100 bg-slate-50 px-4 py-3">
                <h3 id="studentRecordingModalTitle" class="truncate text-sm font-bold text-slate-800 sm:text-base">تسجيل المحاضرة</h3>
                <button type="button" data-close-student-recording class="inline-flex h-9 w-9 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-600 hover:bg-slate-100" aria-label="إغلاق">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="aspect-video w-full bg-black">
                <iframe id="studentRecordingFrame" src="about:blank" class="h-full w-full border-0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; fullscreen" allowfullscreen></iframe>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
window.__offlineLecturesPage = function () {
    return {
        q: '',
        onlyWithMaterials: false,
        onlyUpcoming: false,
        onlyPast: false,
        openId: null,
        visibleCount: {{ (int) $lectureCount }},
        normalize(s) {
            return (s || '').toString().toLowerCase().trim();
        },
        parseWhen(el) {
            const v = (el && el.dataset && el.dataset.when) ? el.dataset.when : '';
            if (!v) return null;
            const d = new Date(v);
            return isNaN(d.getTime()) ? null : d;
        },
        isUpcoming(el) {
            const d = this.parseWhen(el);
            if (!d) return false;
            return d.getTime() >= Date.now() - (1000 * 60 * 60 * 12);
        },
        isPast(el) {
            const d = this.parseWhen(el);
            if (!d) return false;
            return d.getTime() < Date.now() - (1000 * 60 * 60 * 12);
        },
        matches(el) {
            if (!el) return true;
            if (this.onlyWithMaterials && el.dataset.hasMaterials !== '1') return false;
            if (this.onlyUpcoming && !this.isUpcoming(el)) return false;
            if (this.onlyPast && !this.isPast(el)) return false;
            const q = this.normalize(this.q);
            if (!q) return true;
            const hay = [el.dataset.title || '', el.dataset.description || '', el.dataset.agenda || ''].join(' ');
            return hay.includes(q);
        },
        recount() {
            try {
                const list = this.$refs.list;
                if (!list) return;
                this.visibleCount = Array.from(list.querySelectorAll('article')).filter(el => this.matches(el)).length;
            } catch (e) {}
        },
        toggleDetails(id) {
            this.openId = this.openId === id ? null : id;
        },
        init() {
            this.$watch('q', () => this.recount());
            this.$watch('onlyWithMaterials', () => this.recount());
            this.$watch('onlyUpcoming', () => this.recount());
            this.$watch('onlyPast', () => this.recount());
            this.recount();

            const hash = (window.location.hash || '').replace('#', '');
            if (hash && hash.indexOf('offline-lecture-') === 0) {
                const id = parseInt(hash.replace('offline-lecture-', ''), 10);
                if (id) {
                    this.openId = id;
                    this.$nextTick(() => {
                        const el = document.getElementById(hash);
                        if (el) el.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    });
                }
            }
        }
    }
};

(function () {
    const modal = document.getElementById('studentRecordingModal');
    const frame = document.getElementById('studentRecordingFrame');
    const titleEl = document.getElementById('studentRecordingModalTitle');
    if (!modal || !frame) return;

    function openModal(url, title) {
        titleEl.textContent = title || 'تسجيل المحاضرة';
        frame.src = url;
        modal.classList.remove('hidden');
        modal.setAttribute('aria-hidden', 'false');
        document.body.classList.add('overflow-hidden');
    }
    function closeModal() {
        frame.src = 'about:blank';
        modal.classList.add('hidden');
        modal.setAttribute('aria-hidden', 'true');
        document.body.classList.remove('overflow-hidden');
    }

    document.addEventListener('click', function (e) {
        const btn = e.target.closest('.js-open-student-recording');
        if (btn) {
            e.preventDefault();
            openModal(btn.getAttribute('data-watch-url'), btn.getAttribute('data-title'));
            return;
        }
        if (e.target.closest('[data-close-student-recording]')) closeModal();
    });
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && !modal.classList.contains('hidden')) closeModal();
    });
})();
</script>
@endpush
