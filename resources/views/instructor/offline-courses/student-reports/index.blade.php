@extends('layouts.app')

@section('title', 'تقارير الطلاب - ' . $offlineCourse->title)
@section('header', 'تقارير الطلاب')

@section('content')
<div class="space-y-6">
    <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-5 sm:p-6">
        <nav class="text-sm text-slate-500 mb-2">
            <a href="{{ route('instructor.offline-courses.show', ['offline_course' => $offlineCourse, 'channel' => ($channel ?? 'offline')]) }}" class="hover:text-amber-600 transition-colors">
                {{ $offlineCourse->title }}
            </a>
            <span class="mx-2">/</span>
            <span class="text-slate-700 font-semibold">تقارير الطلاب</span>
        </nav>

        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div class="min-w-0">
                <h1 class="text-xl sm:text-2xl font-bold text-slate-800 flex items-center gap-2">
                    <i class="fas fa-chart-line text-slate-700"></i>
                    تقارير الطلاب
                </h1>
                <p class="text-sm text-slate-600 mt-1">نظرة سريعة على الحضور والدرجات والتسليمات لكل طالب.</p>
            </div>

            <form method="GET" class="flex flex-col sm:flex-row gap-2 sm:items-center">
                <input type="hidden" name="channel" value="{{ $channel ?? 'offline' }}">
                <div class="relative">
                    <i class="fas fa-search absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                    <input name="q" value="{{ $search ?? '' }}" placeholder="بحث بالاسم أو البريد..." class="w-full sm:w-72 pr-9 pl-3 py-2 rounded-xl border border-slate-200 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm">
                </div>
                <button class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-xl bg-slate-900 text-white text-sm font-semibold hover:bg-slate-800">
                    <i class="fas fa-filter"></i>
                    تطبيق
                </button>
            </form>
        </div>
    </div>

    <div class="rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden">
        @if($enrollments->isEmpty())
            <div class="p-12 text-center text-slate-500">
                <i class="fas fa-user-slash text-4xl mb-3 opacity-50"></i>
                <p>لا يوجد طلاب.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-slate-50">
                        <tr class="text-slate-600">
                            <th class="text-right py-3 px-3 font-bold">الطالب</th>
                            <th class="text-right py-3 px-3 font-bold">المجموعة</th>
                            <th class="text-right py-3 px-3 font-bold">الحضور</th>
                            <th class="text-right py-3 px-3 font-bold">تسليمات الأنشطة</th>
                            <th class="text-right py-3 px-3 font-bold">امتحانات الأكاديمية</th>
                            <th class="text-right py-3 px-3 font-bold"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($enrollments as $en)
                            @php
                                $st = $en->student;
                                $sid = (int) ($en->user_id ?? 0);
                                $att = $attendanceAgg[$sid] ?? null;
                                $act = $activityAgg[$sid] ?? null;
                                $ex = $examAgg[$sid] ?? null;
                            @endphp
                            <tr class="hover:bg-slate-50/50">
                                <td class="py-3 px-3">
                                    <div class="font-semibold text-slate-800">{{ $st?->name ?? $st?->email ?? ('طالب #' . $sid) }}</div>
                                    <div class="text-xs text-slate-500">{{ $st?->email ?? '' }}</div>
                                </td>
                                <td class="py-3 px-3 text-slate-700">
                                    {{ $en->group?->name ?? '—' }}
                                </td>
                                <td class="py-3 px-3 text-slate-700">
                                    @if($att)
                                        <div class="text-xs text-slate-600">
                                            حاضر: {{ (int) ($att->present_cnt ?? 0) }} · غائب: {{ (int) ($att->absent_cnt ?? 0) }} · متأخر: {{ (int) ($att->late_cnt ?? 0) }}
                                        </div>
                                    @else
                                        —
                                    @endif
                                </td>
                                <td class="py-3 px-3 text-slate-700">
                                    @if($act)
                                        <div class="text-xs text-slate-600">
                                            {{ (int) ($act->submissions_cnt ?? 0) }} تسليم
                                            @if($act->avg_score !== null)
                                                · متوسط: {{ number_format((float) $act->avg_score, 1) }}
                                            @endif
                                        </div>
                                    @else
                                        —
                                    @endif
                                </td>
                                <td class="py-3 px-3 text-slate-700">
                                    @if($ex)
                                        <div class="text-xs text-slate-600">
                                            {{ (int) ($ex->attempts_cnt ?? 0) }} محاولة
                                            @if($ex->avg_score !== null)
                                                · متوسط: {{ number_format((float) $ex->avg_score, 1) }}
                                            @endif
                                        </div>
                                    @else
                                        —
                                    @endif
                                </td>
                                <td class="py-3 px-3 text-left">
                                    @if($st)
                                        <a href="{{ route('instructor.offline-courses.student-reports.show', ['offlineCourse' => $offlineCourse, 'student' => $st, 'channel' => ($channel ?? 'offline')]) }}"
                                           class="inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-sky-600 hover:bg-sky-700 text-white font-semibold">
                                            <i class="fas fa-file-lines"></i>
                                            تقرير
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection

