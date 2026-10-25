@extends('layouts.app')

@section('title', 'حضور المحاضرة - ' . $lecture->title)
@section('header', 'حضور المحاضرة')

@section('content')
<div class="w-full max-w-full px-4 sm:px-6 lg:px-8 py-6 space-y-6">
    <!-- الهيدر -->
    <div class="rounded-2xl p-5 sm:p-6 bg-white border border-slate-200 shadow-sm">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div class="min-w-0 flex-1">
                <nav class="text-sm text-slate-500 mb-2">
                    <a href="{{ route('instructor.attendance.index') }}" class="hover:text-sky-600 transition-colors">الحضور والغياب</a>
                    <span class="mx-2">/</span>
                    <span class="text-slate-700 font-semibold">{{ Str::limit($lecture->title, 40) }}</span>
                </nav>
                <h1 class="text-xl sm:text-2xl font-bold text-slate-800 mb-2">{{ $lecture->title }}</h1>
                <div class="flex flex-wrap items-center gap-2">
                    @if($lecture->course)
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold bg-sky-100 text-sky-700">
                            <i class="fas fa-book"></i>
                            {{ Str::limit($lecture->course->title, 30) }}
                        </span>
                    @endif
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold bg-slate-100 text-slate-700">
                        <i class="fas fa-calendar-alt"></i>
                        {{ $lecture->scheduled_at->format('Y/m/d H:i') }}
                    </span>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold
                        @if($lecture->status == 'scheduled') bg-blue-100 text-blue-700
                        @elseif($lecture->status == 'in_progress') bg-amber-100 text-amber-700
                        @elseif($lecture->status == 'completed') bg-emerald-100 text-emerald-700
                        @else bg-red-100 text-red-700
                        @endif">
                        @if($lecture->status == 'scheduled')
                            <i class="fas fa-calendar-alt"></i> مجدولة
                        @elseif($lecture->status == 'in_progress')
                            <i class="fas fa-clock"></i> قيد التنفيذ
                        @elseif($lecture->status == 'completed')
                            <i class="fas fa-check-circle"></i> مكتملة
                        @else
                            <i class="fas fa-times-circle"></i> ملغاة
                        @endif
                    </span>
                </div>
            </div>
            <div class="flex flex-wrap items-center gap-2 shrink-0">
                <a href="{{ route('instructor.lectures.show', $lecture) }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-sky-500 hover:bg-sky-600 text-white rounded-xl font-semibold transition-colors">
                    <i class="fas fa-arrow-right"></i>
                    العودة للمحاضرة
                </a>
                <a href="{{ route('instructor.attendance.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-semibold transition-colors">
                    <i class="fas fa-list"></i>
                    قائمة الحضور
                </a>
            </div>
        </div>
    </div>

    <!-- الإحصائيات السريعة -->
    <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-5 gap-3 sm:gap-4">
        <div class="rounded-xl p-4 bg-white border border-slate-200 shadow-sm text-center">
            <div class="w-10 h-10 rounded-xl bg-emerald-100 flex items-center justify-center text-emerald-600 mx-auto mb-2">
                <i class="fas fa-check-circle text-sm"></i>
            </div>
            <div class="text-xl font-bold text-slate-800">{{ $attendanceStats['present'] ?? 0 }}</div>
            <div class="text-xs text-slate-500 font-medium mt-1">حاضر</div>
        </div>
        <div class="rounded-xl p-4 bg-white border border-slate-200 shadow-sm text-center">
            <div class="w-10 h-10 rounded-xl bg-amber-100 flex items-center justify-center text-amber-600 mx-auto mb-2">
                <i class="fas fa-clock text-sm"></i>
            </div>
            <div class="text-xl font-bold text-slate-800">{{ $attendanceStats['late'] ?? 0 }}</div>
            <div class="text-xs text-slate-500 font-medium mt-1">متأخر</div>
        </div>
        <div class="rounded-xl p-4 bg-white border border-slate-200 shadow-sm text-center">
            <div class="w-10 h-10 rounded-xl bg-sky-100 flex items-center justify-center text-sky-600 mx-auto mb-2">
                <i class="fas fa-user-clock text-sm"></i>
            </div>
            <div class="text-xl font-bold text-slate-800">{{ $attendanceStats['partial'] ?? 0 }}</div>
            <div class="text-xs text-slate-500 font-medium mt-1">جزئي</div>
        </div>
        <div class="rounded-xl p-4 bg-white border border-slate-200 shadow-sm text-center">
            <div class="w-10 h-10 rounded-xl bg-red-100 flex items-center justify-center text-red-600 mx-auto mb-2">
                <i class="fas fa-times-circle text-sm"></i>
            </div>
            <div class="text-xl font-bold text-slate-800">{{ $attendanceStats['absent'] ?? 0 }}</div>
            <div class="text-xs text-slate-500 font-medium mt-1">غائب</div>
        </div>
        <div class="rounded-xl p-4 bg-white border border-slate-200 shadow-sm text-center">
            <div class="w-10 h-10 rounded-xl bg-slate-100 flex items-center justify-center text-slate-600 mx-auto mb-2">
                <i class="fas fa-users text-sm"></i>
            </div>
            <div class="text-xl font-bold text-slate-800">{{ $attendanceStats['total_students'] ?? 0 }}</div>
            <div class="text-xs text-slate-500 font-medium mt-1">إجمالي</div>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-4 gap-6">
        <!-- جدول الحضور -->
        <div class="xl:col-span-3">
            <div class="rounded-xl bg-white border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-200 bg-slate-50 flex items-center justify-between">
                    <h3 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                        <i class="fas fa-clipboard-list text-sky-600"></i>
                        سجلات الحضور
                    </h3>
                    <span class="text-sm font-semibold text-slate-600">إجمالي: <span class="text-sky-600">{{ $attendanceStats['total_students'] ?? 0 }}</span></span>
                </div>
                <div class="overflow-x-auto">
                    @if($enrollments->count() > 0)
                        <table class="min-w-full divide-y divide-slate-200">
                            <thead class="bg-slate-50">
                                <tr>
                                    <th class="px-4 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">الطالب</th>
                                    <th class="px-4 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">الحالة</th>
                                    <th class="px-4 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">دقائق الحضور</th>
                                    <th class="px-4 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">النسبة</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-slate-200">
                                @foreach($enrollments as $enrollment)
                                    @php
                                        $record = $attendanceRecords->get($enrollment->user_id);
                                        $attendanceMinutes = $record && isset($record->attendance_minutes) ? $record->attendance_minutes : 0;
                                        $percentage = $record && $record->attendance_percentage ? $record->attendance_percentage : 0;
                                        $pctWidth = $lecture->duration_minutes > 0 ? min(($attendanceMinutes / $lecture->duration_minutes) * 100, 100) : 0;
                                    @endphp
                                    <tr class="hover:bg-slate-50 transition-colors">
                                        <td class="px-4 py-4 whitespace-nowrap">
                                            <div class="flex items-center gap-3">
                                                <div class="w-10 h-10 rounded-xl bg-sky-100 flex items-center justify-center text-sky-600 font-bold text-sm">
                                                    {{ mb_substr($enrollment->user->name ?? 'ط', 0, 1) }}
                                                </div>
                                                <div>
                                                    <div class="text-sm font-semibold text-slate-800">{{ $enrollment->user->name ?? 'غير محدد' }}</div>
                                                    <div class="text-xs text-slate-500">{{ $enrollment->user->email ?? '' }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap">
                                            @if($record)
                                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold
                                                    @if($record->status == 'present') bg-emerald-100 text-emerald-700
                                                    @elseif($record->status == 'late') bg-amber-100 text-amber-700
                                                    @elseif($record->status == 'partial') bg-sky-100 text-sky-700
                                                    @else bg-red-100 text-red-700
                                                    @endif">
                                                    @if($record->status == 'present') <i class="fas fa-check-circle"></i> حاضر
                                                    @elseif($record->status == 'late') <i class="fas fa-clock"></i> متأخر
                                                    @elseif($record->status == 'partial') <i class="fas fa-user-clock"></i> جزئي
                                                    @else <i class="fas fa-times-circle"></i> غائب
                                                    @endif
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold bg-slate-100 text-slate-600">
                                                    <i class="fas fa-question-circle"></i> غير محدد
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap">
                                            <div class="text-sm font-semibold text-slate-800">{{ $attendanceMinutes }} / {{ $lecture->duration_minutes }}</div>
                                            <div class="mt-1.5 w-24 h-2 bg-slate-200 rounded-full overflow-hidden">
                                                <div class="h-full bg-sky-500 rounded-full transition-all" style="width: {{ $pctWidth }}%"></div>
                                            </div>
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap">
                                            <div class="flex items-center gap-2">
                                                <span class="text-sm font-semibold text-slate-800">{{ number_format($percentage, 1) }}%</span>
                                                @if($percentage >= 80)
                                                    <i class="fas fa-check-circle text-emerald-500 text-sm"></i>
                                                @elseif($percentage >= 50)
                                                    <i class="fas fa-exclamation-circle text-amber-500 text-sm"></i>
                                                @else
                                                    <i class="fas fa-times-circle text-red-500 text-sm"></i>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="text-center py-12 px-4">
                            <div class="w-16 h-16 rounded-2xl bg-slate-100 flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-users text-3xl text-slate-400"></i>
                            </div>
                            <p class="text-lg font-bold text-slate-800 mb-2">لا يوجد طلاب مسجلين</p>
                            <p class="text-sm text-slate-500">لا يوجد طلاب مسجلين في هذا الكورس</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- الشريط الجانبي -->
        <div class="space-y-6">
            <!-- إحصائيات الحضور -->
            <div class="rounded-xl bg-white border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-200 bg-slate-50">
                    <h3 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                        <i class="fas fa-chart-bar text-sky-600"></i>
                        إحصائيات الحضور
                    </h3>
                </div>
                <div class="p-4 space-y-3">
                    @php $total = $attendanceStats['total_students'] ?? 0; $total = $total > 0 ? $total : 1; @endphp
                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                        <div class="flex items-center justify-between mb-1.5">
                            <span class="text-xs font-semibold text-slate-500 uppercase">حاضر</span>
                            <span class="text-sm font-bold text-emerald-600">{{ $attendanceStats['present'] ?? 0 }}</span>
                        </div>
                        <div class="h-2 bg-slate-200 rounded-full overflow-hidden">
                            <div class="h-full bg-emerald-500 rounded-full" style="width: {{ ($attendanceStats['present'] ?? 0) / $total * 100 }}%"></div>
                        </div>
                    </div>
                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                        <div class="flex items-center justify-between mb-1.5">
                            <span class="text-xs font-semibold text-slate-500 uppercase">متأخر</span>
                            <span class="text-sm font-bold text-amber-600">{{ $attendanceStats['late'] ?? 0 }}</span>
                        </div>
                        <div class="h-2 bg-slate-200 rounded-full overflow-hidden">
                            <div class="h-full bg-amber-500 rounded-full" style="width: {{ ($attendanceStats['late'] ?? 0) / $total * 100 }}%"></div>
                        </div>
                    </div>
                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                        <div class="flex items-center justify-between mb-1.5">
                            <span class="text-xs font-semibold text-slate-500 uppercase">جزئي</span>
                            <span class="text-sm font-bold text-sky-600">{{ $attendanceStats['partial'] ?? 0 }}</span>
                        </div>
                        <div class="h-2 bg-slate-200 rounded-full overflow-hidden">
                            <div class="h-full bg-sky-500 rounded-full" style="width: {{ ($attendanceStats['partial'] ?? 0) / $total * 100 }}%"></div>
                        </div>
                    </div>
                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                        <div class="flex items-center justify-between mb-1.5">
                            <span class="text-xs font-semibold text-slate-500 uppercase">غائب</span>
                            <span class="text-sm font-bold text-red-600">{{ $attendanceStats['absent'] ?? 0 }}</span>
                        </div>
                        <div class="h-2 bg-slate-200 rounded-full overflow-hidden">
                            <div class="h-full bg-red-500 rounded-full" style="width: {{ ($attendanceStats['absent'] ?? 0) / $total * 100 }}%"></div>
                        </div>
                    </div>
                    <div class="p-3 rounded-xl bg-sky-50 border border-sky-100">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-semibold text-slate-500 uppercase">إجمالي</span>
                            <span class="text-sm font-bold text-sky-700">{{ $attendanceStats['total_students'] ?? 0 }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- معلومات المحاضرة -->
            <div class="rounded-xl bg-white border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-200 bg-slate-50">
                    <h3 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                        <i class="fas fa-info-circle text-sky-600"></i>
                        معلومات المحاضرة
                    </h3>
                </div>
                <div class="p-4 space-y-3">
                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                        <div class="text-xs font-semibold text-slate-500 mb-1">التاريخ والوقت</div>
                        <div class="flex items-center gap-2 text-slate-800 font-semibold text-sm">
                            <i class="fas fa-calendar-alt text-sky-500"></i>
                            {{ $lecture->scheduled_at->format('Y/m/d H:i') }}
                        </div>
                    </div>
                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                        <div class="text-xs font-semibold text-slate-500 mb-1">المدة</div>
                        <div class="flex items-center gap-2 text-slate-800 font-semibold text-sm">
                            <i class="fas fa-clock text-amber-500"></i>
                            {{ $lecture->duration_minutes }} دقيقة
                        </div>
                    </div>
                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                        <div class="text-xs font-semibold text-slate-500 mb-1">الحالة</div>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold
                            @if($lecture->status == 'scheduled') bg-blue-100 text-blue-700
                            @elseif($lecture->status == 'in_progress') bg-amber-100 text-amber-700
                            @elseif($lecture->status == 'completed') bg-emerald-100 text-emerald-700
                            @else bg-red-100 text-red-700
                            @endif">
                            @if($lecture->status == 'scheduled') <i class="fas fa-calendar-alt"></i> مجدولة
                            @elseif($lecture->status == 'in_progress') <i class="fas fa-clock"></i> قيد التنفيذ
                            @elseif($lecture->status == 'completed') <i class="fas fa-check-circle"></i> مكتملة
                            @else <i class="fas fa-times-circle"></i> ملغاة
                            @endif
                        </span>
                    </div>
                    @if($lecture->course)
                        <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                            <div class="text-xs font-semibold text-slate-500 mb-1">الكورس</div>
                            <div class="flex items-center gap-2 text-slate-800 font-semibold text-sm">
                                <i class="fas fa-book text-emerald-500"></i>
                                {{ Str::limit($lecture->course->title, 28) }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- إجراءات سريعة -->
            <div class="rounded-xl bg-white border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-200 bg-slate-50">
                    <h3 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                        <i class="fas fa-bolt text-sky-600"></i>
                        إجراءات سريعة
                    </h3>
                </div>
                <div class="p-4 space-y-2">
                    <a href="{{ route('instructor.lectures.show', $lecture) }}" class="flex items-center gap-3 p-3 rounded-xl bg-sky-50 hover:bg-sky-100 border border-sky-100 text-slate-800 font-semibold text-sm transition-colors">
                        <div class="w-9 h-9 rounded-lg bg-sky-100 flex items-center justify-center text-sky-600">
                            <i class="fas fa-chalkboard-teacher text-sm"></i>
                        </div>
                        عرض المحاضرة
                    </a>
                    @if($lecture->course)
                        <a href="{{ route('instructor.courses.show', $lecture->course) }}" class="flex items-center gap-3 p-3 rounded-xl bg-emerald-50 hover:bg-emerald-100 border border-emerald-100 text-slate-800 font-semibold text-sm transition-colors">
                            <div class="w-9 h-9 rounded-lg bg-emerald-100 flex items-center justify-center text-emerald-600">
                                <i class="fas fa-book text-sm"></i>
                            </div>
                            عرض الكورس
                        </a>
                    @endif
                    <a href="{{ route('instructor.attendance.index') }}" class="flex items-center gap-3 p-3 rounded-xl bg-slate-50 hover:bg-slate-100 border border-slate-200 text-slate-800 font-semibold text-sm transition-colors">
                        <div class="w-9 h-9 rounded-lg bg-slate-200 flex items-center justify-center text-slate-600">
                            <i class="fas fa-list text-sm"></i>
                        </div>
                        قائمة الحضور
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
