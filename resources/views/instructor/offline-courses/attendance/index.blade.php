@extends('layouts.app')

@section('title', 'الحضور والغياب - ' . $offlineCourse->title)
@section('header', 'الحضور والغياب')

@section('content')
<div class="space-y-6">
    <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-5 sm:p-6">
        <nav class="text-sm text-slate-500 mb-2">
            <a href="{{ route('instructor.offline-courses.show', ['offline_course' => $offlineCourse, 'channel' => ($channel ?? 'offline')]) }}" class="hover:text-amber-600 transition-colors">
                {{ $offlineCourse->title }}
            </a>
            <span class="mx-2">/</span>
            <span class="text-slate-700 font-semibold">الحضور والغياب</span>
        </nav>
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div class="min-w-0">
                <h1 class="text-xl sm:text-2xl font-bold text-slate-800 flex items-center gap-2">
                    <i class="fas fa-user-check text-emerald-600"></i>
                    الحضور والغياب
                </h1>
                <p class="text-sm text-slate-600 mt-1">
                    اختر محاضرة/جلسة لعرض الطلاب وتسجيل حضورهم.
                </p>
            </div>
            <a href="{{ route('instructor.offline-courses.show', ['offline_course' => $offlineCourse, 'channel' => ($channel ?? 'offline')]) }}"
               class="inline-flex items-center gap-2 px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-semibold transition-colors">
                <i class="fas fa-arrow-right"></i>
                العودة
            </a>
        </div>
    </div>

    <div class="rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden">
        @if($sessions->isEmpty())
            <div class="p-12 text-center text-slate-500">
                <i class="fas fa-calendar-xmark text-4xl mb-3 opacity-50"></i>
                <p>لا توجد جلسات/محاضرات بعد.</p>
            </div>
        @else
            <ul class="divide-y divide-slate-100">
                @foreach($sessions as $s)
                    <li class="p-4 sm:p-5 hover:bg-slate-50/50">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div class="min-w-0">
                                <div class="font-bold text-slate-800">
                                    {{ $s->title ?: 'جلسة' }}
                                    — {{ $s->group?->name ?? 'مجموعة' }}
                                </div>
                                <div class="text-sm text-slate-600 mt-1">
                                    {{ optional($s->session_date)->format('Y-m-d') }}
                                    @if($s->start_time)
                                        · {{ $s->start_time }}{{ $s->end_time ? ' - '.$s->end_time : '' }}
                                    @endif
                                </div>
                            </div>
                            <a href="{{ route('instructor.offline-courses.attendance.sessions.show', ['offlineCourse' => $offlineCourse, 'session' => $s, 'channel' => ($channel ?? 'offline')]) }}"
                               class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-semibold transition-colors">
                                <i class="fas fa-clipboard-list"></i>
                                فتح الحضور
                            </a>
                        </div>
                    </li>
                @endforeach
            </ul>

            <div class="p-4 sm:p-5 border-t border-slate-200">
                {{ $sessions->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

