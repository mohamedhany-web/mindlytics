@extends('layouts.app')

@section('title', 'حضور المحاضرة - ' . $lecture->title)
@section('header', 'حضور المحاضرة')

@push('styles')
<style>
    .info-card {
        background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
        border: 2px solid rgba(44, 169, 189, 0.1);
        transition: all 0.3s;
    }

    .info-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(44, 169, 189, 0.1);
        border-color: rgba(44, 169, 189, 0.2);
    }

    .stats-mini-card {
        background: linear-gradient(135deg, rgba(44, 169, 189, 0.05) 0%, rgba(101, 219, 228, 0.03) 100%);
        border: 1.5px solid rgba(44, 169, 189, 0.15);
        transition: all 0.3s;
    }

    .stats-mini-card:hover {
        transform: translateY(-2px);
        border-color: rgba(44, 169, 189, 0.3);
    }

    .student-row {
        transition: all 0.2s;
    }

    .student-row:hover {
        transform: translateX(-4px);
        background: linear-gradient(to right, rgba(44, 169, 189, 0.05), transparent);
    }

    .progress-bar {
        height: 8px;
        border-radius: 4px;
        overflow: hidden;
        background: rgba(44, 169, 189, 0.1);
    }

    .progress-fill {
        height: 100%;
        transition: width 0.3s ease;
        border-radius: 4px;
    }
</style>
@endpush

@section('content')
<div class="space-y-6">
    <!-- الهيدر المحسن -->
    <div class="bg-gradient-to-r from-[#2CA9BD]/10 via-[#65DBE4]/10 to-[#2CA9BD]/10 rounded-2xl p-6 border-2 border-[#2CA9BD]/20 shadow-lg">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div class="flex-1">
                <nav class="text-sm text-[#1F3A56] font-medium mb-3">
                    <a href="{{ route('instructor.attendance.index') }}" class="hover:text-[#2CA9BD] transition-colors">الحضور والغياب</a>
                    <span class="mx-2">/</span>
                    <span class="text-[#1C2C39] font-bold">{{ $lecture->title }}</span>
                </nav>
                <h1 class="text-2xl sm:text-3xl font-black text-[#1C2C39] mb-2">{{ $lecture->title }}</h1>
                <div class="flex flex-wrap items-center gap-2">
                    @if($lecture->course)
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold bg-gradient-to-r from-[#2CA9BD]/10 to-[#65DBE4]/10 text-[#2CA9BD] border border-[#2CA9BD]/20">
                            <i class="fas fa-book"></i>
                            {{ $lecture->course->title }}
                        </span>
                    @endif
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold bg-gradient-to-r from-purple-500/10 to-indigo-500/10 text-purple-600 border border-purple-500/20">
                        <i class="fas fa-calendar-alt"></i>
                        {{ $lecture->scheduled_at->format('Y/m/d H:i') }}
                    </span>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold shadow-md
                        @if($lecture->status == 'scheduled') bg-gradient-to-r from-blue-500 to-indigo-600 text-white
                        @elseif($lecture->status == 'in_progress') bg-gradient-to-r from-[#FFD34E] to-amber-500 text-white
                        @elseif($lecture->status == 'completed') bg-gradient-to-r from-green-500 to-emerald-600 text-white
                        @else bg-gradient-to-r from-red-500 to-rose-600 text-white
                        @endif">
                        @if($lecture->status == 'scheduled')
                            <i class="fas fa-calendar-alt"></i>
                            مجدولة
                        @elseif($lecture->status == 'in_progress')
                            <i class="fas fa-clock"></i>
                            قيد التنفيذ
                        @elseif($lecture->status == 'completed')
                            <i class="fas fa-check-circle"></i>
                            مكتملة
                        @else
                            <i class="fas fa-times-circle"></i>
                            ملغاة
                        @endif
                    </span>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('instructor.lectures.show', $lecture) }}" 
                   class="inline-flex items-center gap-2 bg-gray-500 hover:bg-gray-600 text-white px-5 py-3 rounded-xl font-bold transition-all duration-300 transform hover:scale-105">
                    <i class="fas fa-arrow-right"></i>
                    <span>العودة للمحاضرة</span>
                </a>
                <a href="{{ route('instructor.attendance.index') }}" 
                   class="inline-flex items-center gap-2 bg-gray-400 hover:bg-gray-500 text-white px-5 py-3 rounded-xl font-bold transition-all duration-300 transform hover:scale-105">
                    <i class="fas fa-list"></i>
                    <span>قائمة الحضور</span>
                </a>
            </div>
        </div>
    </div>

    <!-- الإحصائيات السريعة -->
    <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-5 gap-3 sm:gap-4">
        <div class="stats-mini-card rounded-xl p-4 text-center">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-green-500 to-emerald-600 flex items-center justify-center text-white mx-auto mb-2 shadow-md">
                <i class="fas fa-check-circle text-sm"></i>
            </div>
            <div class="text-xl font-black text-[#1C2C39]">{{ $attendanceStats['present'] ?? 0 }}</div>
            <div class="text-xs text-[#1F3A56] font-semibold mt-1">حاضر</div>
        </div>
        <div class="stats-mini-card rounded-xl p-4 text-center">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-[#FFD34E] to-amber-500 flex items-center justify-center text-white mx-auto mb-2 shadow-md">
                <i class="fas fa-clock text-sm"></i>
            </div>
            <div class="text-xl font-black text-[#1C2C39]">{{ $attendanceStats['late'] ?? 0 }}</div>
            <div class="text-xs text-[#1F3A56] font-semibold mt-1">متأخر</div>
        </div>
        <div class="stats-mini-card rounded-xl p-4 text-center">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white mx-auto mb-2 shadow-md">
                <i class="fas fa-user-clock text-sm"></i>
            </div>
            <div class="text-xl font-black text-[#1C2C39]">{{ $attendanceStats['partial'] ?? 0 }}</div>
            <div class="text-xs text-[#1F3A56] font-semibold mt-1">جزئي</div>
        </div>
        <div class="stats-mini-card rounded-xl p-4 text-center">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-red-500 to-rose-600 flex items-center justify-center text-white mx-auto mb-2 shadow-md">
                <i class="fas fa-times-circle text-sm"></i>
            </div>
            <div class="text-xl font-black text-[#1C2C39]">{{ $attendanceStats['absent'] ?? 0 }}</div>
            <div class="text-xs text-[#1F3A56] font-semibold mt-1">غائب</div>
        </div>
        <div class="stats-mini-card rounded-xl p-4 text-center">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-[#2CA9BD] to-[#65DBE4] flex items-center justify-center text-white mx-auto mb-2 shadow-md">
                <i class="fas fa-users text-sm"></i>
            </div>
            <div class="text-xl font-black text-[#1C2C39]">{{ $attendanceStats['total_students'] ?? 0 }}</div>
            <div class="text-xs text-[#1F3A56] font-semibold mt-1">إجمالي</div>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-4 gap-6">
        <!-- جدول الحضور -->
        <div class="xl:col-span-3">
            <div class="info-card rounded-xl p-5 sm:p-6">
                <div class="flex items-center justify-between mb-4 pb-4 border-b-2 border-[#2CA9BD]/10">
                    <h3 class="text-lg sm:text-xl font-black text-[#1C2C39] flex items-center gap-2">
                        <i class="fas fa-clipboard-list text-[#2CA9BD]"></i>
                        سجلات الحضور
                    </h3>
                    <div class="text-sm text-[#1F3A56] font-bold">
                        إجمالي: <span class="text-[#2CA9BD]">{{ $attendanceStats['total_students'] }}</span>
                    </div>
                </div>
                <div>
                    @if($enrollments->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gradient-to-r from-[#2CA9BD]/10 to-[#65DBE4]/10">
                                    <tr>
                                        <th class="px-4 py-3 text-right text-xs font-bold text-[#1C2C39] uppercase tracking-wider">الطالب</th>
                                        <th class="px-4 py-3 text-right text-xs font-bold text-[#1C2C39] uppercase tracking-wider">الحالة</th>
                                        <th class="px-4 py-3 text-right text-xs font-bold text-[#1C2C39] uppercase tracking-wider">دقائق الحضور</th>
                                        <th class="px-4 py-3 text-right text-xs font-bold text-[#1C2C39] uppercase tracking-wider">النسبة</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($enrollments as $enrollment)
                                    @php
                                        $record = $attendanceRecords->get($enrollment->user_id);
                                        $attendanceMinutes = $record && isset($record->attendance_minutes) ? $record->attendance_minutes : 0;
                                        $percentage = $record && $record->attendance_percentage ? $record->attendance_percentage : 0;
                                    @endphp
                                    <tr class="student-row">
                                        <td class="px-4 py-4 whitespace-nowrap">
                                            <div class="flex items-center gap-3">
                                                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-[#2CA9BD] to-[#65DBE4] flex items-center justify-center text-white font-black shadow-md">
                                                    {{ mb_substr($enrollment->user->name ?? 'ط', 0, 1) }}
                                                </div>
                                                <div>
                                                    <div class="text-sm font-black text-[#1C2C39]">
                                                        {{ $enrollment->user->name ?? 'غير محدد' }}
                                                    </div>
                                                    <div class="text-xs text-[#1F3A56] font-medium">
                                                        {{ $enrollment->user->email ?? '' }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap">
                                            @if($record)
                                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold shadow-md
                                                    @if($record->status == 'present') bg-gradient-to-r from-green-500 to-emerald-600 text-white
                                                    @elseif($record->status == 'late') bg-gradient-to-r from-[#FFD34E] to-amber-500 text-white
                                                    @elseif($record->status == 'partial') bg-gradient-to-r from-blue-500 to-indigo-600 text-white
                                                    @else bg-gradient-to-r from-red-500 to-rose-600 text-white
                                                    @endif">
                                                    @if($record->status == 'present')
                                                        <i class="fas fa-check-circle"></i>
                                                        حاضر
                                                    @elseif($record->status == 'late')
                                                        <i class="fas fa-clock"></i>
                                                        متأخر
                                                    @elseif($record->status == 'partial')
                                                        <i class="fas fa-user-clock"></i>
                                                        جزئي
                                                    @else
                                                        <i class="fas fa-times-circle"></i>
                                                        غائب
                                                    @endif
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold bg-gradient-to-r from-gray-400 to-gray-500 text-white shadow-md">
                                                    <i class="fas fa-question-circle"></i>
                                                    غير محدد
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap">
                                            <div class="text-sm font-black text-[#1C2C39]">
                                                {{ $attendanceMinutes }} / {{ $lecture->duration_minutes }}
                                            </div>
                                            <div class="progress-bar mt-2 w-24">
                                                <div class="progress-fill bg-gradient-to-r from-[#2CA9BD] to-[#65DBE4]" 
                                                     style="width: {{ min(($attendanceMinutes / $lecture->duration_minutes) * 100, 100) }}%"></div>
                                            </div>
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap">
                                            <div class="flex items-center gap-2">
                                                <div class="text-sm font-black text-[#1C2C39]">
                                                    {{ number_format($percentage, 1) }}%
                                                </div>
                                                @if($percentage >= 80)
                                                    <i class="fas fa-check-circle text-green-600 text-sm"></i>
                                                @elseif($percentage >= 50)
                                                    <i class="fas fa-exclamation-circle text-[#FFD34E] text-sm"></i>
                                                @else
                                                    <i class="fas fa-times-circle text-red-600 text-sm"></i>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-12">
                            <div class="w-24 h-24 bg-gradient-to-br from-[#2CA9BD]/10 to-[#65DBE4]/10 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-users text-4xl text-[#2CA9BD]"></i>
                            </div>
                            <p class="text-lg font-black text-[#1C2C39] mb-2">لا يوجد طلاب مسجلين</p>
                            <p class="text-sm text-[#1F3A56] font-medium">لا يوجد طلاب مسجلين في هذا الكورس</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- الشريط الجانبي -->
        <div class="space-y-6">
            <!-- إحصائيات الحضور -->
            <div class="info-card rounded-xl p-5">
                <div class="flex items-center justify-between mb-4 pb-4 border-b-2 border-[#2CA9BD]/10">
                    <h3 class="text-lg font-black text-[#1C2C39] flex items-center gap-2">
                        <i class="fas fa-chart-bar text-[#2CA9BD]"></i>
                        إحصائيات الحضور
                    </h3>
                </div>
                <div class="space-y-3">
                    <div class="p-3 bg-gradient-to-r from-green-500/5 to-emerald-500/5 rounded-xl border border-green-500/10">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs text-[#1F3A56] font-bold uppercase tracking-wide">حاضر</span>
                            <span class="text-lg font-black text-green-600">{{ $attendanceStats['present'] ?? 0 }}</span>
                        </div>
                        @if($attendanceStats['total_students'] > 0)
                        <div class="progress-bar">
                            <div class="progress-fill bg-gradient-to-r from-green-500 to-emerald-600" 
                                 style="width: {{ ($attendanceStats['present'] / $attendanceStats['total_students']) * 100 }}%"></div>
                        </div>
                        @endif
                    </div>
                    <div class="p-3 bg-gradient-to-r from-[#FFD34E]/5 to-amber-500/5 rounded-xl border border-[#FFD34E]/10">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs text-[#1F3A56] font-bold uppercase tracking-wide">متأخر</span>
                            <span class="text-lg font-black text-[#FFD34E]">{{ $attendanceStats['late'] ?? 0 }}</span>
                        </div>
                        @if($attendanceStats['total_students'] > 0)
                        <div class="progress-bar">
                            <div class="progress-fill bg-gradient-to-r from-[#FFD34E] to-amber-500" 
                                 style="width: {{ ($attendanceStats['late'] / $attendanceStats['total_students']) * 100 }}%"></div>
                        </div>
                        @endif
                    </div>
                    <div class="p-3 bg-gradient-to-r from-blue-500/5 to-indigo-500/5 rounded-xl border border-blue-500/10">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs text-[#1F3A56] font-bold uppercase tracking-wide">جزئي</span>
                            <span class="text-lg font-black text-blue-600">{{ $attendanceStats['partial'] ?? 0 }}</span>
                        </div>
                        @if($attendanceStats['total_students'] > 0)
                        <div class="progress-bar">
                            <div class="progress-fill bg-gradient-to-r from-blue-500 to-indigo-600" 
                                 style="width: {{ ($attendanceStats['partial'] / $attendanceStats['total_students']) * 100 }}%"></div>
                        </div>
                        @endif
                    </div>
                    <div class="p-3 bg-gradient-to-r from-red-500/5 to-rose-500/5 rounded-xl border border-red-500/10">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs text-[#1F3A56] font-bold uppercase tracking-wide">غائب</span>
                            <span class="text-lg font-black text-red-600">{{ $attendanceStats['absent'] ?? 0 }}</span>
                        </div>
                        @if($attendanceStats['total_students'] > 0)
                        <div class="progress-bar">
                            <div class="progress-fill bg-gradient-to-r from-red-500 to-rose-600" 
                                 style="width: {{ ($attendanceStats['absent'] / $attendanceStats['total_students']) * 100 }}%"></div>
                        </div>
                        @endif
                    </div>
                    <div class="p-3 bg-gradient-to-r from-[#2CA9BD]/5 to-[#65DBE4]/5 rounded-xl border border-[#2CA9BD]/10">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs text-[#1F3A56] font-bold uppercase tracking-wide">إجمالي</span>
                            <span class="text-lg font-black text-[#2CA9BD]">{{ $attendanceStats['total_students'] ?? 0 }}</span>
                        </div>
                        <div class="progress-bar">
                            <div class="progress-fill bg-gradient-to-r from-[#2CA9BD] to-[#65DBE4]" style="width: 100%"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- معلومات المحاضرة -->
            <div class="info-card rounded-xl p-5">
                <div class="flex items-center justify-between mb-4 pb-4 border-b-2 border-[#2CA9BD]/10">
                    <h3 class="text-lg font-black text-[#1C2C39] flex items-center gap-2">
                        <i class="fas fa-info-circle text-[#2CA9BD]"></i>
                        معلومات المحاضرة
                    </h3>
                </div>
                <div class="space-y-3">
                    <div class="p-3 bg-gradient-to-r from-[#2CA9BD]/5 to-[#65DBE4]/5 rounded-xl border border-[#2CA9BD]/10">
                        <div class="text-xs text-[#1F3A56] font-bold mb-1 uppercase tracking-wide">التاريخ والوقت</div>
                        <div class="flex items-center gap-2 text-[#1C2C39] font-black">
                            <i class="fas fa-calendar-alt text-[#2CA9BD] text-sm"></i>
                            {{ $lecture->scheduled_at->format('Y/m/d H:i') }}
                        </div>
                    </div>
                    <div class="p-3 bg-gradient-to-r from-[#FFD34E]/5 to-amber-500/5 rounded-xl border border-[#FFD34E]/10">
                        <div class="text-xs text-[#1F3A56] font-bold mb-1 uppercase tracking-wide">المدة</div>
                        <div class="flex items-center gap-2 text-[#1C2C39] font-black">
                            <i class="fas fa-clock text-[#FFD34E] text-sm"></i>
                            {{ $lecture->duration_minutes }} دقيقة
                        </div>
                    </div>
                    <div class="p-3 bg-gradient-to-r from-purple-500/5 to-indigo-500/5 rounded-xl border border-purple-500/10">
                        <div class="text-xs text-[#1F3A56] font-bold mb-1 uppercase tracking-wide">الحالة</div>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold shadow-md
                            @if($lecture->status == 'scheduled') bg-gradient-to-r from-blue-500 to-indigo-600 text-white
                            @elseif($lecture->status == 'in_progress') bg-gradient-to-r from-[#FFD34E] to-amber-500 text-white
                            @elseif($lecture->status == 'completed') bg-gradient-to-r from-green-500 to-emerald-600 text-white
                            @else bg-gradient-to-r from-red-500 to-rose-600 text-white
                            @endif">
                            @if($lecture->status == 'scheduled')
                                <i class="fas fa-calendar-alt"></i>
                                مجدولة
                            @elseif($lecture->status == 'in_progress')
                                <i class="fas fa-clock"></i>
                                قيد التنفيذ
                            @elseif($lecture->status == 'completed')
                                <i class="fas fa-check-circle"></i>
                                مكتملة
                            @else
                                <i class="fas fa-times-circle"></i>
                                ملغاة
                            @endif
                        </span>
                    </div>
                    @if($lecture->course)
                    <div class="p-3 bg-gradient-to-r from-green-500/5 to-emerald-500/5 rounded-xl border border-green-500/10">
                        <div class="text-xs text-[#1F3A56] font-bold mb-1 uppercase tracking-wide">الكورس</div>
                        <div class="flex items-center gap-2 text-[#1C2C39] font-bold">
                            <i class="fas fa-book text-green-600 text-sm"></i>
                            {{ $lecture->course->title }}
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- إجراءات سريعة -->
            <div class="info-card rounded-xl p-5">
                <div class="flex items-center justify-between mb-4 pb-4 border-b-2 border-[#2CA9BD]/10">
                    <h3 class="text-lg font-black text-[#1C2C39] flex items-center gap-2">
                        <i class="fas fa-bolt text-[#2CA9BD]"></i>
                        إجراءات سريعة
                    </h3>
                </div>
                <div class="space-y-2">
                    <a href="{{ route('instructor.lectures.show', $lecture) }}" 
                       class="flex items-center gap-3 p-3 bg-gradient-to-r from-[#2CA9BD]/10 to-[#65DBE4]/10 hover:from-[#2CA9BD]/20 hover:to-[#65DBE4]/20 rounded-xl border border-[#2CA9BD]/20 transition-all">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-[#2CA9BD] to-[#65DBE4] flex items-center justify-center text-white shadow-md">
                            <i class="fas fa-chalkboard-teacher text-sm"></i>
                        </div>
                        <span class="font-bold text-[#1C2C39] text-sm">عرض المحاضرة</span>
                    </a>
                    @if($lecture->course)
                    <a href="{{ route('instructor.courses.show', $lecture->course) }}" 
                       class="flex items-center gap-3 p-3 bg-gradient-to-r from-green-500/10 to-emerald-500/10 hover:from-green-500/20 hover:to-emerald-500/20 rounded-xl border border-green-500/20 transition-all">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-green-500 to-emerald-600 flex items-center justify-center text-white shadow-md">
                            <i class="fas fa-book text-sm"></i>
                        </div>
                        <span class="font-bold text-[#1C2C39] text-sm">عرض الكورس</span>
                    </a>
                    @endif
                    <a href="{{ route('instructor.attendance.index') }}" 
                       class="flex items-center gap-3 p-3 bg-gradient-to-r from-blue-500/10 to-indigo-500/10 hover:from-blue-500/20 hover:to-indigo-500/20 rounded-xl border border-blue-500/20 transition-all">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white shadow-md">
                            <i class="fas fa-list text-sm"></i>
                        </div>
                        <span class="font-bold text-[#1C2C39] text-sm">قائمة الحضور</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
