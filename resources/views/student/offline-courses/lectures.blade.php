@extends('layouts.student-dashboard')

@section('title', 'محاضرات الكورس — ' . $offlineCourse->title)

@push('styles')
<style>
    .lectures-hero {
        background: #fff;
        border-radius: 16px;
        padding: 24px 28px;
        position: relative;
        overflow: hidden;
        box-shadow: 0 1px 3px rgba(0,0,0,0.06);
        border: 1px solid rgb(226 232 240);
    }
    .lectures-hero-accent {
        position: absolute;
        top: 0;
        right: 0;
        width: 4px;
        height: 100%;
        background: linear-gradient(180deg, rgb(139 92 246), rgb(99 102 241));
        border-radius: 0 16px 16px 0;
    }
    .lectures-stat {
        background: #fff;
        border: 1px solid rgb(226 232 240);
        border-radius: 14px;
        padding: 16px 18px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    }
    .lectures-panel {
        background: #fff;
        border: 1px solid rgb(226 232 240);
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 1px 3px rgba(0,0,0,0.06);
    }
    .lectures-aside {
        background: #fff;
        border: 1px solid rgb(226 232 240);
        border-radius: 16px;
        padding: 20px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.06);
    }
    @media (min-width: 1280px) {
        .lectures-aside-sticky { position: sticky; top: 1rem; }
    }
    .lecture-row {
        border-bottom: 1px solid rgb(241 245 249);
    }
    .lecture-row:last-child { border-bottom: 0; }
    .lecture-row.is-active {
        background: linear-gradient(to left, rgb(245 243 255), #fff);
    }
    [x-cloak] { display: none !important; }
</style>
@endpush

@section('content')
@php
    $sg = $studentRouteGroup ?? 'student.offline-courses';
    $chLabel = ($channel ?? 'offline') === 'online' ? 'أونلاين' : 'أوفلاين';
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

<div class="w-full max-w-full space-y-6" x-data="window.__offlineLecturesPage()">
    {{-- مسار --}}
    <nav class="flex flex-wrap items-center gap-x-2 gap-y-1 text-sm text-slate-500" aria-label="مسار التنقل">
        <a href="{{ route('dashboard') }}" class="font-medium hover:text-sky-600">لوحة التحكم</a>
        <span class="text-slate-300" aria-hidden="true">/</span>
        <a href="{{ route($sg . '.index') }}" class="font-medium hover:text-sky-600">{{ ($channel ?? 'offline') === 'online' ? 'كورساتي الأونلاين' : 'كورساتي الأوفلاين' }}</a>
        <span class="text-slate-300" aria-hidden="true">/</span>
        <a href="{{ route($sg . '.show', $offlineCourse) }}" class="max-w-[10rem] truncate font-medium hover:text-sky-600 sm:max-w-xs">{{ \Illuminate\Support\Str::limit($offlineCourse->title, 40) }}</a>
        <span class="text-slate-300" aria-hidden="true">/</span>
        <span class="font-semibold text-slate-800">المحاضرات</span>
    </nav>

    {{-- رأس كامل العرض --}}
    <div class="lectures-hero">
        <div class="lectures-hero-accent" aria-hidden="true"></div>
        <div class="relative pr-2 sm:pr-3">
            <div class="flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">
                <div class="min-w-0 flex-1 space-y-3">
                    <p class="text-xs font-bold uppercase tracking-wide text-violet-600">محاضرات الكورس · {{ $chLabel }}</p>
                    <h1 class="text-2xl font-black leading-tight text-gray-900 sm:text-3xl">{{ $offlineCourse->title }}</h1>
                    <p class="max-w-3xl text-sm leading-relaxed text-gray-600 sm:text-base">
                        تابع جلساتك، برنامج اليوم، التسجيلات والمرفقات — الصفحة تستخدم عرض لوحة الطالب بالكامل.
                    </p>
                    <div class="flex flex-wrap items-center gap-2 pt-1">
                        <a href="{{ route($sg . '.show', $offlineCourse) }}" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-slate-100 px-4 py-2.5 text-sm font-bold text-slate-800 hover:bg-slate-200">
                            <i class="fas fa-arrow-right text-slate-500"></i>
                            صفحة الكورس
                        </a>
                        <a href="{{ route($sg . '.curriculum', $offlineCourse) }}" class="inline-flex items-center gap-2 rounded-xl border border-sky-200 bg-sky-50 px-4 py-2.5 text-sm font-bold text-sky-800 hover:bg-sky-100">
                            <i class="fas fa-sitemap"></i>
                            المنهج
                        </a>
                        <a href="{{ route($sg . '.schedule', $offlineCourse) }}" class="inline-flex items-center gap-2 rounded-xl border border-indigo-200 bg-indigo-50 px-4 py-2.5 text-sm font-bold text-indigo-800 hover:bg-indigo-100">
                            <i class="fas fa-calendar-alt"></i>
                            التقويم
                        </a>
                        <a href="{{ route($sg . '.resources', $offlineCourse) }}" class="inline-flex items-center gap-2 rounded-xl bg-violet-600 px-4 py-2.5 text-sm font-bold text-white shadow-sm hover:bg-violet-700">
                            <i class="fas fa-file-alt"></i>
                            الموارد
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- إحصائيات --}}
    <div class="grid grid-cols-2 gap-3 sm:gap-4 lg:grid-cols-4">
        <div class="lectures-stat">
            <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">كل المحاضرات</p>
            <p class="mt-1 text-2xl font-black text-violet-600">{{ $lectureCount }}</p>
        </div>
        <div class="lectures-stat">
            <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">بتسجيل</p>
            <p class="mt-1 text-2xl font-black text-rose-600">{{ $withRecording }}</p>
        </div>
        <div class="lectures-stat">
            <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">قادمة / اليوم</p>
            <p class="mt-1 text-2xl font-black text-emerald-600">{{ $upcomingCount }}</p>
        </div>
        <div class="lectures-stat">
            <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">المجموعة</p>
            <p class="mt-1 truncate text-lg font-bold text-gray-900" title="{{ $enrollment->group->name ?? '—' }}">{{ $enrollment->group->name ?? '—' }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 items-start gap-6 xl:grid-cols-12">
        {{-- القائمة الرئيسية --}}
        <div class="min-w-0 space-y-4 xl:col-span-8">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <h2 class="flex items-center gap-2 text-lg font-black text-gray-900 sm:text-xl">
                    <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-violet-100 text-violet-600">
                        <i class="fas fa-chalkboard-teacher"></i>
                    </span>
                    قائمة المحاضرات
                </h2>
                <p class="text-sm font-semibold text-slate-500">
                    يظهر <span class="text-slate-800" x-text="visibleCount"></span> من {{ $lectureCount }}
                </p>
            </div>

            <div class="lectures-panel">
                @if($lectures->isEmpty())
                    <div class="px-6 py-16 text-center text-slate-500">
                        <i class="fas fa-chalkboard-teacher mb-3 block text-4xl text-slate-300"></i>
                        <p class="font-bold text-slate-700">لا توجد محاضرات متاحة حالياً</p>
                        <p class="mt-1 text-sm">ستظهر هنا عند نشرها من المدرب.</p>
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
                                $statusClass = 'bg-slate-100 text-slate-600';
                                $dateForStatus = null;
                                if ($lecture->relationLoaded('groupSession') && $lecture->groupSession && $lecture->groupSession->session_date) {
                                    $dateForStatus = $lecture->groupSession->session_date;
                                } elseif ($lecture->scheduled_at) {
                                    $dateForStatus = $lecture->scheduled_at;
                                }
                                if ($dateForStatus) {
                                    if ($dateForStatus->isToday()) {
                                        $statusLabel = 'اليوم';
                                        $statusClass = 'bg-amber-50 text-amber-800';
                                    } elseif ($dateForStatus->isFuture()) {
                                        $statusLabel = 'قادمة';
                                        $statusClass = 'bg-emerald-50 text-emerald-800';
                                    } else {
                                        $statusLabel = 'سابقة';
                                        $statusClass = 'bg-slate-100 text-slate-600';
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
                                            <span class="mt-0.5 flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-violet-100 text-sm font-black text-violet-700 tabular-nums">
                                                {{ $loop->iteration }}
                                            </span>
                                            <div class="min-w-0 flex-1 space-y-2">
                                                <div class="flex flex-wrap items-center gap-2">
                                                    <h3 class="text-base font-black leading-snug text-slate-900 sm:text-lg">{{ $lecture->title }}</h3>
                                                    @if($statusLabel)
                                                        <span class="inline-flex rounded-md px-2 py-0.5 text-[11px] font-bold {{ $statusClass }}">{{ $statusLabel }}</span>
                                                    @endif
                                                    @if($hasRecording)
                                                        <span class="inline-flex items-center gap-1 rounded-md bg-rose-50 px-2 py-0.5 text-[11px] font-bold text-rose-700">
                                                            <i class="fas fa-circle-play text-[10px]"></i> تسجيل
                                                        </span>
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
                                                        class="js-open-student-recording inline-flex items-center justify-center gap-2 rounded-xl bg-violet-600 px-4 py-2.5 text-sm font-bold text-white hover:bg-violet-700"
                                                        data-watch-url="{{ route($sg . '.lectures.watch', [$offlineCourse, $lecture]) }}"
                                                        data-title="{{ $lecture->title }}">
                                                    <i class="fas fa-play text-xs"></i>
                                                    مشاهدة التسجيل
                                                </button>
                                            @endif
                                            @if($hasMeeting)
                                                <a href="{{ $lecture->meeting_url }}" target="_blank" rel="noopener"
                                                   class="inline-flex items-center justify-center gap-2 rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-bold text-white hover:bg-indigo-700">
                                                    <i class="fas fa-video text-xs"></i>
                                                    دخول البث
                                                </a>
                                            @endif
                                            @if($hasDetails)
                                                <button type="button"
                                                        class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-bold text-slate-700 hover:bg-slate-50 sm:col-span-2 xl:col-span-1"
                                                        @click="toggleDetails({{ $lecture->id }})">
                                                    <i class="fas fa-chevron-down text-xs transition-transform" :class="openId === {{ $lecture->id }} ? 'rotate-180' : ''"></i>
                                                    <span x-text="openId === {{ $lecture->id }} ? 'إخفاء التفاصيل' : 'التفاصيل والمواد'"></span>
                                                </button>
                                            @elseif(! $hasRecording && ! $hasMeeting)
                                                <p class="text-center text-xs font-semibold text-slate-400 sm:col-span-2">لا مواد مرفقة بعد</p>
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

        {{-- الشريط الجانبي --}}
        <aside class="min-w-0 space-y-5 xl:col-span-4">
            <div class="lectures-aside lectures-aside-sticky space-y-5">
                <div>
                    <p class="mb-2 text-xs font-bold uppercase tracking-wide text-slate-500">بحث وفلترة</p>
                    <div class="flex items-center gap-2 rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5">
                        <i class="fas fa-search text-slate-400 text-sm"></i>
                        <input
                            x-model.trim="q"
                            type="search"
                            placeholder="ابحث بالعنوان أو الوصف…"
                            class="w-full min-w-0 border-0 bg-transparent p-0 text-sm font-semibold text-slate-800 placeholder:text-slate-400 focus:outline-none focus:ring-0"
                        >
                    </div>
                    <div class="mt-3 flex flex-col gap-2">
                        <label class="flex cursor-pointer items-center justify-between rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm font-bold text-slate-700 hover:bg-slate-50">
                            <span>محاضرات بمواد فقط</span>
                            <input type="checkbox" class="rounded border-slate-300 text-violet-600 focus:ring-violet-500" x-model="onlyWithMaterials">
                        </label>
                        <label class="flex cursor-pointer items-center justify-between rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm font-bold text-slate-700 hover:bg-slate-50">
                            <span>القادمة / اليوم</span>
                            <input type="checkbox" class="rounded border-slate-300 text-violet-600 focus:ring-violet-500" x-model="onlyUpcoming">
                        </label>
                        <label class="flex cursor-pointer items-center justify-between rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm font-bold text-slate-700 hover:bg-slate-50">
                            <span>السابقة فقط</span>
                            <input type="checkbox" class="rounded border-slate-300 text-violet-600 focus:ring-violet-500" x-model="onlyPast">
                        </label>
                    </div>
                    <button type="button"
                            class="mt-3 w-full rounded-xl border border-slate-200 bg-slate-100 px-3 py-2.5 text-sm font-bold text-slate-700 hover:bg-slate-200"
                            @click="q=''; onlyWithMaterials=false; onlyUpcoming=false; onlyPast=false">
                        إعادة ضبط الفلاتر
                    </button>
                </div>

                <div class="border-t border-slate-100 pt-4">
                    <p class="mb-2 text-xs font-bold uppercase tracking-wide text-slate-500">انتقال سريع</p>
                    <div class="flex flex-col gap-2">
                        <a href="{{ route($sg . '.curriculum', $offlineCourse) }}" class="flex items-center justify-between gap-2 rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-bold text-gray-800 hover:border-sky-200 hover:bg-white">
                            <span class="flex items-center gap-2"><i class="fas fa-sitemap text-sky-500"></i> المنهج</span>
                            <i class="fas fa-chevron-left text-xs text-gray-400"></i>
                        </a>
                        <a href="{{ route($sg . '.schedule', $offlineCourse) }}" class="flex items-center justify-between gap-2 rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-bold text-gray-800 hover:border-indigo-200 hover:bg-white">
                            <span class="flex items-center gap-2"><i class="fas fa-calendar-alt text-indigo-500"></i> التقويم</span>
                            <i class="fas fa-chevron-left text-xs text-gray-400"></i>
                        </a>
                        <a href="{{ route($sg . '.resources', $offlineCourse) }}" class="flex items-center justify-between gap-2 rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-bold text-gray-800 hover:border-violet-200 hover:bg-white">
                            <span class="flex items-center gap-2"><i class="fas fa-file-alt text-violet-500"></i> الموارد</span>
                            <i class="fas fa-chevron-left text-xs text-gray-400"></i>
                        </a>
                    </div>
                </div>

                <div class="rounded-xl border border-violet-100 bg-violet-50/60 px-4 py-3 text-xs leading-relaxed text-violet-900">
                    <p class="font-bold mb-1">نصيحة</p>
                    <p>اضغط «مشاهدة التسجيل» لفتح الفيديو داخل المنصة دون مغادرة الصفحة.</p>
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
