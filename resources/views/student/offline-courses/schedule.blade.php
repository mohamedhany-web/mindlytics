@extends('layouts.student-dashboard')

@section('title', 'التقويم — ' . $offlineCourse->title)

@push('styles')
<style>
    .schedule-hero {
        background: #fff;
        border-radius: 16px;
        padding: 24px 28px;
        position: relative;
        overflow: hidden;
        box-shadow: 0 1px 3px rgba(0,0,0,0.06);
        border: 1px solid rgb(226 232 240);
        transition: box-shadow 0.2s ease, border-color 0.2s ease;
    }
    .schedule-hero:hover {
        box-shadow: 0 4px 12px rgba(14, 165, 233, 0.1);
        border-color: rgb(186 230 253);
    }
    .schedule-hero .schedule-hero-accent {
        position: absolute;
        top: 0;
        right: 0;
        width: 4px;
        height: 100%;
        background: linear-gradient(180deg, rgb(14 165 233), rgb(2 132 199));
        border-radius: 0 16px 16px 0;
    }
    .schedule-stat {
        background: #fff;
        border: 1px solid rgb(226 232 240);
        border-radius: 14px;
        padding: 16px 18px;
        transition: all 0.2s ease;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    }
    .schedule-stat:hover {
        box-shadow: 0 4px 12px rgba(14, 165, 233, 0.1);
        border-color: rgb(186 230 253);
    }
    .schedule-panel {
        background: #fff;
        border: 1px solid rgb(226 232 240);
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 1px 3px rgba(0,0,0,0.06);
    }
    .schedule-panel-head {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 14px 18px;
        border-bottom: 1px solid rgb(241 245 249);
        background: linear-gradient(to left, rgb(248 250 252), rgb(255 255 255));
    }
</style>
@endpush

@section('content')
@php
    $today = now()->startOfDay();
    $sessionCount = $sessions->count();
    $monthCount = $timelineByMonth->count();
    $sg = $studentRouteGroup ?? 'student.offline-courses';
    $timelineTotal = $timelineByMonth->flatten(1)->count();
@endphp
<div class="w-full max-w-full space-y-6">
    <nav class="flex flex-wrap items-center gap-x-2 gap-y-1 text-sm text-slate-500" aria-label="مسار التنقل">
        <a href="{{ route('dashboard') }}" class="hover:text-sky-600 font-medium">لوحة التحكم</a>
        <span class="text-slate-300" aria-hidden="true">/</span>
        <a href="{{ route($sg . '.index') }}" class="hover:text-sky-600 font-medium">{{ ($channel ?? 'offline') === 'online' ? 'كورساتي الأونلاين' : 'كورساتي الأوفلاين' }}</a>
        <span class="text-slate-300" aria-hidden="true">/</span>
        <a href="{{ route($sg . '.show', $offlineCourse) }}" class="hover:text-sky-600 font-medium truncate max-w-[10rem] sm:max-w-xs">{{ \Illuminate\Support\Str::limit($offlineCourse->title, 40) }}</a>
        <span class="text-slate-300" aria-hidden="true">/</span>
        <span class="text-slate-800 font-semibold">التقويم</span>
    </nav>

    <div class="schedule-hero">
        <div class="schedule-hero-accent" aria-hidden="true"></div>
        <div class="relative pr-2 sm:pr-3">
            <div class="flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">
                <div class="min-w-0 flex-1 space-y-3">
                    <p class="text-xs font-bold uppercase tracking-wide text-indigo-600">تقويم الكورس</p>
                    <h1 class="text-2xl font-black leading-tight text-gray-900 sm:text-3xl">الجلسات والمواعيد</h1>
                    <p class="max-w-3xl text-sm leading-relaxed text-gray-600 sm:text-base">
                        {{ $offlineCourse->title }} — جدول حضورك، مواعيد تسليم الأنشطة، والاختبارات المرتبطة بالتاريخ في عرض واحد يملأ مساحة المحتوى.
                    </p>
                    <div class="flex flex-wrap items-center gap-2 pt-1">
                        <a href="{{ route($sg . '.show', $offlineCourse) }}" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-slate-100 px-4 py-2.5 text-sm font-bold text-slate-800 transition-colors hover:bg-slate-200">
                            <i class="fas fa-arrow-right text-slate-500"></i>
                            صفحة الكورس
                        </a>
                        <a href="{{ route($sg . '.curriculum', $offlineCourse) }}" class="inline-flex items-center gap-2 rounded-xl border border-sky-200 bg-sky-50 px-4 py-2.5 text-sm font-bold text-sky-800 transition-colors hover:bg-sky-100">
                            <i class="fas fa-sitemap"></i>
                            المنهج والتوصيف
                        </a>
                        <a href="{{ route($sg . '.lectures', $offlineCourse) }}" class="inline-flex items-center gap-2 rounded-xl bg-sky-500 px-4 py-2.5 text-sm font-bold text-white shadow-sm transition-colors hover:bg-sky-600">
                            <i class="fas fa-chalkboard-teacher"></i>
                            المحاضرات
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-2 gap-3 sm:grid-cols-4 sm:gap-4">
        <div class="schedule-stat text-center sm:text-start">
            <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">جلسات مسجّلة</p>
            <p class="mt-1 text-2xl font-black text-indigo-600">{{ $sessionCount }}</p>
        </div>
        <div class="schedule-stat text-center sm:text-start">
            <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">بدون تاريخ تسليم</p>
            <p class="mt-1 text-2xl font-black text-amber-600">{{ $activitiesNoDue->count() }}</p>
        </div>
        <div class="schedule-stat text-center sm:text-start">
            <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">مواعيد في الخط الزمني</p>
            <p class="mt-1 text-2xl font-black text-slate-800">{{ $timelineTotal }}</p>
        </div>
        <div class="schedule-stat text-center sm:text-start">
            <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">أشهر بها فعاليات</p>
            <p class="mt-1 text-2xl font-black text-emerald-600">{{ $monthCount }}</p>
        </div>
    </div>

    @if($enrollment->group)
        <div class="schedule-panel">
            <div class="schedule-panel-head">
                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-indigo-100 text-indigo-600 ring-1 ring-indigo-200/60">
                    <i class="fas fa-users text-sm"></i>
                </span>
                <div class="min-w-0 text-start">
                    <p class="text-xs font-bold uppercase tracking-wide text-slate-500">مجموعتك</p>
                    <p class="text-sm font-bold text-slate-900">{{ $enrollment->group->name }}</p>
                    @if($enrollment->group->start_date)
                        <p class="mt-0.5 text-xs text-slate-600">بداية المسار: {{ $enrollment->group->start_date->translatedFormat('l j F Y') }}</p>
                    @endif
                </div>
            </div>
        </div>
    @endif

    @if($timelineByMonth->isEmpty() && $activitiesNoDue->isEmpty() && ! $enrollment->group)
        <div class="schedule-panel">
            <div class="px-6 py-16 text-center">
                <i class="fas fa-calendar-times mb-3 block text-5xl text-slate-300" aria-hidden="true"></i>
                <p class="font-bold text-slate-700">لا توجد جلسات أو مواعيد مسجّلة بعد.</p>
                <p class="mt-2 text-sm text-slate-500">عند جدولة الجلسات من الإدارة ستظهر هنا تلقائياً.</p>
            </div>
        </div>
    @elseif($timelineByMonth->isEmpty() && $sessions->isEmpty())
        <div class="schedule-panel">
            <div class="border-b border-slate-100 px-5 py-4 text-sm text-slate-600">
                لا توجد جلسات في التقويم حالياً. راجع الأنشطة أدناه إن وُجدت.
            </div>
        </div>
    @endif

    <div class="space-y-5">
        @foreach($timelineByMonth as $monthLabel => $rows)
            <section class="schedule-panel overflow-hidden" aria-labelledby="month-{{ \Illuminate\Support\Str::slug($monthLabel) }}">
                <div class="schedule-panel-head">
                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-sky-100 text-sky-600 ring-1 ring-sky-200/60">
                        <i class="fas fa-calendar-week text-sm"></i>
                    </span>
                    <h2 id="month-{{ \Illuminate\Support\Str::slug($monthLabel) }}" class="text-base font-black text-gray-900 sm:text-lg">
                        {{ $monthLabel }}
                    </h2>
                </div>
                <ul class="divide-y divide-slate-100" role="list">
                    @foreach($rows as $row)
                        @php
                            $d = $row['date'];
                            $isPast = $d->lt($today);
                            $isToday = $d->isToday();
                        @endphp
                        <li class="flex flex-col gap-3 px-4 py-4 sm:flex-row sm:items-stretch sm:gap-4 sm:px-5 sm:py-4 {{ $isToday ? 'bg-sky-50/40' : '' }} {{ $isPast && ! $isToday ? 'opacity-[0.88]' : '' }}">
                            <div class="flex shrink-0 flex-row items-center gap-3 border-b border-slate-100 pb-3 sm:w-40 sm:flex-col sm:items-start sm:border-b-0 sm:border-s sm:border-slate-100 sm:pb-0 sm:ps-4">
                                <div class="min-w-0 flex-1 text-start sm:w-full">
                                    <p class="text-sm font-black text-slate-900">{{ $d->translatedFormat('l') }}</p>
                                    <p class="text-xs text-slate-600">{{ $d->translatedFormat('j F Y') }}</p>
                                </div>
                                <div class="flex shrink-0 flex-wrap gap-1 sm:w-full">
                                    @if($isToday)
                                        <span class="inline-flex rounded-full bg-sky-200 px-2 py-0.5 text-[10px] font-bold text-sky-900">اليوم</span>
                                    @elseif($isPast)
                                        <span class="inline-flex rounded-full bg-slate-200 px-2 py-0.5 text-[10px] font-semibold text-slate-700">انتهى</span>
                                    @endif
                                </div>
                            </div>
                            <div class="min-w-0 flex-1 rounded-xl border border-slate-100 bg-slate-50/60 px-4 py-3 sm:me-4">
                                @if($row['type'] === 'session')
                                    @php $s = $row['session']; @endphp
                                    <div class="mb-2 flex flex-wrap items-center gap-2">
                                        <span class="inline-flex items-center gap-1 rounded-lg bg-indigo-100 px-2.5 py-1 text-[11px] font-bold text-indigo-900 ring-1 ring-indigo-200/60">
                                            <i class="fas fa-door-open"></i>
                                            جلسة حضور
                                        </span>
                                        @if(filled($s->title))
                                            <span class="text-sm font-bold text-slate-900">{{ $s->title }}</span>
                                        @endif
                                    </div>
                                    @php
                                        $stLabel = $s->start_time ? \Carbon\Carbon::parse($s->start_time)->format('h:i A') : '—';
                                        $etLabel = $s->end_time ? \Carbon\Carbon::parse($s->end_time)->format('h:i A') : '—';
                                    @endphp
                                    <p class="text-sm text-slate-700">
                                        <i class="far fa-clock ml-1 text-slate-400"></i>
                                        {{ $stLabel }} — {{ $etLabel }}
                                        <span class="text-slate-500">({{ (int) $s->duration_minutes }} دقيقة)</span>
                                    </p>
                                    @if(filled($s->location) || filled(optional($enrollment->group)->location))
                                        <p class="mt-1.5 text-xs text-slate-600">
                                            <i class="fas fa-map-marker-alt ml-1 text-slate-400"></i>
                                            {{ $s->location ?? optional($enrollment->group)->location ?? '—' }}
                                        </p>
                                    @endif
                                @elseif($row['type'] === 'activity')
                                    @php $a = $row['activity']; @endphp
                                    <div class="mb-2 flex flex-wrap items-center gap-2">
                                        <span class="inline-flex items-center gap-1 rounded-lg bg-amber-100 px-2.5 py-1 text-[11px] font-bold text-amber-950 ring-1 ring-amber-200/60">
                                            <i class="fas fa-tasks"></i>
                                            تسليم نشاط
                                        </span>
                                        <span class="text-sm font-bold text-slate-900">{{ $a->title }}</span>
                                    </div>
                                    <p class="text-xs text-slate-600">آخر موعد: نهاية يوم {{ $d->translatedFormat('l j F') }} — {{ $a->max_score }} نقطة</p>
                                    <a href="{{ route($sg . '.activities.show', [$offlineCourse, $a]) }}" class="mt-3 inline-flex items-center gap-1.5 rounded-xl bg-amber-500 px-3 py-2 text-xs font-bold text-white shadow-sm transition hover:bg-amber-600">
                                        فتح النشاط
                                        <i class="fas fa-chevron-left text-[10px]"></i>
                                    </a>
                                @else
                                    @php $ex = $row['exam']; @endphp
                                    <div class="mb-2 flex flex-wrap items-center gap-2">
                                        <span class="inline-flex items-center gap-1 rounded-lg bg-emerald-100 px-2.5 py-1 text-[11px] font-bold text-emerald-950 ring-1 ring-emerald-200/60">
                                            <i class="fas fa-clipboard-check"></i>
                                            اختبار
                                        </span>
                                        <span class="text-sm font-bold text-slate-900">{{ $ex->title }}</span>
                                    </div>
                                    <p class="text-xs text-slate-600">تاريخ البدء: {{ $d->translatedFormat('l j F Y') }}</p>
                                    <a href="{{ route('student.exams.show', $ex) }}" class="mt-3 inline-flex items-center gap-1.5 rounded-xl border border-emerald-200 bg-white px-3 py-2 text-xs font-bold text-emerald-800 shadow-sm transition hover:bg-emerald-50">
                                        صفحة الاختبار
                                        <i class="fas fa-chevron-left text-[10px]"></i>
                                    </a>
                                @endif
                            </div>
                        </li>
                    @endforeach
                </ul>
            </section>
        @endforeach
    </div>

    @if($activitiesNoDue->isNotEmpty())
        <section class="overflow-hidden rounded-2xl border border-amber-200 bg-amber-50/40 shadow-sm" aria-labelledby="undated-activities-heading">
            <div class="border-b border-amber-100 bg-amber-50/90 px-5 py-4">
                <div class="flex items-start gap-3">
                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-amber-100 text-amber-700 ring-1 ring-amber-200/60">
                        <i class="fas fa-inbox text-sm"></i>
                    </span>
                    <div class="min-w-0 text-start">
                        <h2 id="undated-activities-heading" class="text-base font-black text-amber-950">أنشطة بدون تاريخ تسليم محدد</h2>
                        <p class="mt-1 text-xs leading-relaxed text-amber-900/85">افتح كل نشاط للاطلاع على التعليمات والتسليم.</p>
                    </div>
                </div>
            </div>
            <ul class="divide-y divide-amber-100/90 bg-white/60" role="list">
                @foreach($activitiesNoDue as $activity)
                    <li class="flex flex-col gap-2 px-5 py-3.5 sm:flex-row sm:items-center sm:justify-between">
                        <div class="min-w-0 text-start">
                            <p class="text-sm font-bold text-slate-900">{{ $activity->title }}</p>
                            <p class="mt-0.5 text-[11px] text-slate-600">{{ $activity->type }} · {{ $activity->max_score }} نقطة</p>
                        </div>
                        <a href="{{ route($sg . '.activities.show', [$offlineCourse, $activity]) }}" class="inline-flex shrink-0 items-center justify-center rounded-xl border border-amber-200 bg-white px-4 py-2 text-xs font-bold text-amber-900 shadow-sm transition hover:bg-amber-50">
                            عرض / تسليم
                        </a>
                    </li>
                @endforeach
            </ul>
        </section>
    @endif
</div>
@endsection
