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
        transition: box-shadow 0.2s ease, border-color 0.2s ease;
    }
    .lectures-hero:hover {
        box-shadow: 0 4px 12px rgba(14, 165, 233, 0.1);
        border-color: rgb(186 230 253);
    }
    .lectures-hero .lectures-hero-accent {
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
        transition: all 0.2s ease;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    }
    .lectures-stat:hover {
        box-shadow: 0 4px 12px rgba(139, 92, 246, 0.12);
        border-color: rgb(196 181 253);
    }
    .lectures-panel {
        background: #fff;
        border: 1px solid rgb(226 232 240);
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 1px 3px rgba(0,0,0,0.06);
    }
    .lectures-panel-head {
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
    $sg = $studentRouteGroup ?? 'student.offline-courses';
    $chLabel = ($channel ?? 'offline') === 'online' ? 'أونلاين' : 'أوفلاين';
    $lectureCount = $lectures->count();
@endphp
<div class="w-full max-w-full space-y-6">
    <nav class="flex flex-wrap items-center gap-x-2 gap-y-1 text-sm text-slate-500" aria-label="مسار التنقل">
        <a href="{{ route('dashboard') }}" class="font-medium hover:text-sky-600">لوحة التحكم</a>
        <span class="text-slate-300" aria-hidden="true">/</span>
        <a href="{{ route($sg . '.index') }}" class="font-medium hover:text-sky-600">{{ ($channel ?? 'offline') === 'online' ? 'كورساتي الأونلاين' : 'كورساتي الأوفلاين' }}</a>
        <span class="text-slate-300" aria-hidden="true">/</span>
        <a href="{{ route($sg . '.show', $offlineCourse) }}" class="max-w-[10rem] truncate font-medium hover:text-sky-600 sm:max-w-xs">{{ \Illuminate\Support\Str::limit($offlineCourse->title, 40) }}</a>
        <span class="text-slate-300" aria-hidden="true">/</span>
        <span class="font-semibold text-slate-800">المحاضرات</span>
    </nav>

    <div class="lectures-hero">
        <div class="lectures-hero-accent" aria-hidden="true"></div>
        <div class="relative pr-2 sm:pr-3">
            <p class="mb-1 text-xs font-bold uppercase tracking-wide text-violet-600">محاضرات الكورس · {{ $chLabel }}</p>
            <h1 class="text-2xl font-black leading-tight text-gray-900 sm:text-3xl">قائمة المحاضرات</h1>
            <p class="mt-2 max-w-3xl text-sm leading-relaxed text-gray-600 sm:text-base">
                {{ $offlineCourse->title }} — جلساتك، نقاط اليوم، التسجيلات والمرفقات حسب ما جهّزه المدرب لمجموعتك.
            </p>
            <div class="mt-4 flex flex-wrap gap-2">
                <a href="{{ route($sg . '.show', $offlineCourse) }}" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-slate-100 px-4 py-2.5 text-sm font-bold text-slate-800 transition-colors hover:bg-slate-200">
                    <i class="fas fa-arrow-right text-slate-500"></i>
                    صفحة الكورس
                </a>
                <a href="{{ route($sg . '.curriculum', $offlineCourse) }}" class="inline-flex items-center gap-2 rounded-xl border border-sky-200 bg-sky-50 px-4 py-2.5 text-sm font-bold text-sky-800 transition-colors hover:bg-sky-100">
                    <i class="fas fa-sitemap"></i>
                    المنهج
                </a>
                <a href="{{ route($sg . '.schedule', $offlineCourse) }}" class="inline-flex items-center gap-2 rounded-xl border border-indigo-200 bg-indigo-50 px-4 py-2.5 text-sm font-bold text-indigo-800 transition-colors hover:bg-indigo-100">
                    <i class="fas fa-calendar-alt"></i>
                    التقويم
                </a>
                <a href="{{ route($sg . '.resources', $offlineCourse) }}" class="inline-flex items-center gap-2 rounded-xl bg-violet-600 px-4 py-2.5 text-sm font-bold text-white shadow-sm transition-colors hover:bg-violet-700">
                    <i class="fas fa-file-alt"></i>
                    الموارد
                </a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-2 gap-3 sm:gap-4 lg:grid-cols-3">
        <div class="lectures-stat text-center sm:text-start">
            <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">عدد المحاضرات</p>
            <p class="mt-1 text-2xl font-black text-violet-600">{{ $lectureCount }}</p>
        </div>
        <div class="lectures-stat text-center sm:text-start">
            <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">نوع التعلم</p>
            <p class="mt-1 text-lg font-black text-slate-800">{{ $chLabel }}</p>
        </div>
        <div class="lectures-stat hidden text-center sm:text-start lg:block">
            <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">المجموعة</p>
            <p class="mt-1 truncate text-lg font-bold text-slate-800" title="{{ $enrollment->group->name ?? '—' }}">{{ $enrollment->group->name ?? '—' }}</p>
        </div>
    </div>

    <div class="lectures-panel">
        <div class="lectures-panel-head">
            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-violet-100 text-violet-600 ring-1 ring-violet-200/60">
                <i class="fas fa-chalkboard-teacher text-sm"></i>
            </span>
            <div class="min-w-0 text-start">
                <p class="text-xs font-bold uppercase tracking-wide text-slate-500">المحتوى</p>
                <p class="text-sm font-black text-slate-900">كل المحاضرات المتاحة لك في هذا الكورس</p>
            </div>
        </div>

        @if($lectures->isEmpty())
            <div class="px-6 py-16 text-center">
                <i class="fas fa-chalkboard-teacher mb-3 block text-5xl text-slate-300" aria-hidden="true"></i>
                <p class="font-bold text-slate-700">لا توجد محاضرات متاحة حالياً.</p>
                <p class="mt-2 text-sm text-slate-500">عند نشر المحاضرات من المدرب ستظهر هنا.</p>
            </div>
        @else
            <ul class="divide-y divide-slate-100" role="list">
                @foreach($lectures as $lecture)
                    <li id="offline-lecture-{{ $lecture->id }}" class="scroll-mt-28 px-4 py-5 transition-colors hover:bg-slate-50/60 sm:px-6 sm:py-6">
                        <div class="flex flex-col gap-4 lg:flex-row lg:items-start">
                            <div class="min-w-0 flex-1 space-y-3">
                                <div class="flex flex-wrap items-start gap-3">
                                    <span class="mt-0.5 hidden h-12 w-1 shrink-0 rounded-full bg-violet-500 sm:block" aria-hidden="true"></span>
                                    <div class="min-w-0 flex-1">
                                        <h2 class="text-lg font-black leading-snug text-slate-900 sm:text-xl">{{ $lecture->title }}</h2>
                                        @if($lecture->relationLoaded('groupSession') && $lecture->groupSession)
                                            <p class="mt-2 flex flex-wrap items-center gap-x-2 gap-y-1 text-xs font-bold text-violet-800">
                                                <i class="far fa-calendar-check text-violet-500"></i>
                                                <span>{{ $lecture->groupSession->session_date->translatedFormat('l j F Y') }}</span>
                                                @php $lgt = $lecture->groupSession->start_time; @endphp
                                                <span class="font-semibold text-slate-500">· {{ is_string($lgt) ? substr($lgt, 0, 5) : $lgt }}</span>
                                                @if($lecture->groupSession->group)
                                                    <span class="text-slate-500">· {{ $lecture->groupSession->group->name }}</span>
                                                @endif
                                            </p>
                                        @endif
                                    </div>
                                </div>

                                @if(filled($lecture->session_agenda))
                                    @php
                                        $lines = array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', $lecture->session_agenda))));
                                    @endphp
                                    @if(count($lines))
                                        <div class="rounded-xl border border-slate-100 bg-slate-50/80 px-4 py-3">
                                            <p class="mb-2 text-[10px] font-bold uppercase tracking-wide text-slate-500">برنامج اليوم</p>
                                            <ul class="space-y-1.5 text-sm text-slate-700">
                                                @foreach($lines as $line)
                                                    <li class="flex gap-2 leading-relaxed">
                                                        <span class="mt-2 h-1.5 w-1.5 shrink-0 rounded-full bg-violet-400" aria-hidden="true"></span>
                                                        <span>{{ $line }}</span>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif
                                @endif

                                @if($lecture->description)
                                    <p class="text-sm leading-relaxed text-slate-600">{{ Str::limit($lecture->description, 280) }}</p>
                                @endif

                                @include('partials.offline-mindmap-visual', ['text' => $lecture->offline_attendee_mindmap])

                                @if($lecture->scheduled_at)
                                    <p class="text-xs text-slate-500">
                                        <i class="fas fa-calendar ml-1 text-slate-400"></i>
                                        {{ $lecture->scheduled_at->translatedFormat('l j F Y — H:i') }}
                                    </p>
                                @endif
                            </div>

                            <div class="flex shrink-0 flex-col gap-2 border-t border-slate-100 pt-4 lg:w-56 lg:border-t-0 lg:border-s lg:border-slate-100 lg:pt-0 lg:ps-5">
                                @if(($channel ?? 'offline') === 'online' && $lecture->meeting_url)
                                    <a href="{{ $lecture->meeting_url }}" target="_blank" rel="noopener" class="inline-flex items-center justify-center gap-2 rounded-xl bg-indigo-600 px-4 py-2.5 text-xs font-bold text-white shadow-sm transition hover:bg-indigo-700">
                                        <i class="fas fa-video"></i>
                                        بث مباشر
                                    </a>
                                @endif
                                @if($lecture->recording_url)
                                    <a href="{{ $lecture->recording_url }}" target="_blank" rel="noopener" class="inline-flex items-center justify-center gap-2 rounded-xl border border-violet-200 bg-violet-50 px-4 py-2.5 text-xs font-bold text-violet-800 transition hover:bg-violet-100">
                                        <i class="fas fa-play"></i>
                                        التسجيل
                                    </a>
                                @endif
                                @if($lecture->download_links && count($lecture->download_links) > 0)
                                    @foreach($lecture->download_links as $link)
                                        <a href="{{ $link['url'] ?? '#' }}" target="_blank" rel="noopener" class="inline-flex items-center justify-center gap-2 rounded-xl border border-sky-200 bg-sky-50 px-4 py-2.5 text-xs font-bold text-sky-800 transition hover:bg-sky-100">
                                            <i class="fas fa-download"></i>
                                            {{ $link['label'] ?? 'تحميل' }}
                                        </a>
                                    @endforeach
                                @endif
                                @if($lecture->attachments && count($lecture->attachments) > 0)
                                    @foreach($lecture->attachments as $att)
                                        <a href="{{ asset('storage/' . ($att['path'] ?? '')) }}" target="_blank" rel="noopener" class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-xs font-bold text-slate-700 shadow-sm transition hover:bg-slate-50">
                                            <i class="fas fa-file"></i>
                                            {{ $att['name'] ?? 'ملف' }}
                                        </a>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
</div>
@endsection
