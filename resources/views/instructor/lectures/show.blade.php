@extends('layouts.app')

@section('title', 'تفاصيل المحاضرة - ' . $lecture->title)
@section('header', 'تفاصيل المحاضرة')

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

    .link-card {
        transition: all 0.3s;
        border: 2px solid rgba(44, 169, 189, 0.1);
    }

    .link-card:hover {
        transform: translateY(-2px);
        border-color: rgba(44, 169, 189, 0.3);
        box-shadow: 0 8px 20px rgba(44, 169, 189, 0.1);
    }
</style>
@endpush

@push('scripts')
<script>
function updateAttendance(studentId, status) {
    const formData = {
        student_id: studentId,
        status: status,
        _token: '{{ csrf_token() }}'
    };
    
    fetch('{{ route("instructor.lectures.update-attendance", $lecture) }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify(formData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.message || 'حدث خطأ أثناء تحديث الحضور');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('حدث خطأ أثناء تحديث الحضور');
    });
}

function updateStatus(status) {
    fetch('{{ route("instructor.lectures.update-status", $lecture) }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify({ status: status })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.message || 'حدث خطأ أثناء تحديث الحالة');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('حدث خطأ أثناء تحديث الحالة');
    });
}
</script>
@endpush

@section('content')
<div class="space-y-6">
    <!-- الهيدر المحسن -->
    <div class="bg-gradient-to-r from-[#2CA9BD]/10 via-[#65DBE4]/10 to-[#2CA9BD]/10 rounded-2xl p-6 border-2 border-[#2CA9BD]/20 shadow-lg">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div class="flex-1">
                <nav class="text-sm text-[#1F3A56] font-medium mb-3">
                    <a href="{{ route('instructor.lectures.index') }}" class="hover:text-[#2CA9BD] transition-colors">المحاضرات</a>
                    <span class="mx-2">/</span>
                    <span class="text-[#1C2C39] font-bold">{{ $lecture->title }}</span>
                </nav>
                <h1 class="text-2xl sm:text-3xl font-black text-[#1C2C39] mb-2">{{ $lecture->title }}</h1>
                <div class="flex flex-wrap items-center gap-2">
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
                    @if($lecture->course)
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold bg-gradient-to-r from-[#2CA9BD]/10 to-[#65DBE4]/10 text-[#2CA9BD] border border-[#2CA9BD]/20">
                            <i class="fas fa-book"></i>
                            {{ $lecture->course->title }}
                        </span>
                    @endif
                    @if($lecture->lesson)
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold bg-gradient-to-r from-purple-500/10 to-indigo-500/10 text-purple-600 border border-purple-500/20">
                            <i class="fas fa-play-circle"></i>
                            {{ $lecture->lesson->title }}
                        </span>
                    @endif
                </div>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('instructor.lectures.edit', $lecture) }}" 
                   class="inline-flex items-center gap-2 bg-gray-500 hover:bg-gray-600 text-white px-5 py-3 rounded-xl font-bold transition-all duration-300 transform hover:scale-105">
                    <i class="fas fa-edit"></i>
                    <span>تعديل</span>
                </a>
                <a href="{{ route('instructor.lectures.index') }}" 
                   class="inline-flex items-center gap-2 bg-gray-400 hover:bg-gray-500 text-white px-5 py-3 rounded-xl font-bold transition-all duration-300 transform hover:scale-105">
                    <i class="fas fa-arrow-right"></i>
                    <span>العودة</span>
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

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <!-- معلومات المحاضرة -->
        <div class="xl:col-span-2 space-y-6">
            <!-- معلومات أساسية -->
            <div class="info-card rounded-xl p-5 sm:p-6">
                <div class="flex items-center justify-between mb-4 pb-4 border-b-2 border-[#2CA9BD]/10">
                    <h3 class="text-lg sm:text-xl font-black text-[#1C2C39] flex items-center gap-2">
                        <i class="fas fa-info-circle text-[#2CA9BD]"></i>
                        معلومات المحاضرة
                    </h3>
                </div>
                <div class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-[#1F3A56] mb-2 uppercase tracking-wide">الحالة</label>
                            <div class="flex items-center gap-2">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold shadow-md
                                    @if($lecture->status == 'scheduled') bg-gradient-to-r from-blue-500 to-indigo-600 text-white
                                    @elseif($lecture->status == 'in_progress') bg-gradient-to-r from-[#FFD34E] to-amber-500 text-white
                                    @elseif($lecture->status == 'completed') bg-gradient-to-r from-green-500 to-emerald-600 text-white
                                    @else bg-gradient-to-r from-red-500 to-rose-600 text-white
                                    @endif">
                                    @if($lecture->status == 'scheduled') مجدولة
                                    @elseif($lecture->status == 'in_progress') قيد التنفيذ
                                    @elseif($lecture->status == 'completed') مكتملة
                                    @else ملغاة
                                    @endif
                                </span>
                                @if($lecture->status != 'completed' && $lecture->status != 'cancelled')
                                <select onchange="updateStatus(this.value)" 
                                        class="text-xs border-2 border-[#2CA9BD]/20 rounded-xl px-3 py-1.5 bg-white text-[#1C2C39] font-bold focus:border-[#2CA9BD] focus:ring-2 focus:ring-[#2CA9BD]/20 transition-all">
                                    <option value="scheduled" {{ $lecture->status == 'scheduled' ? 'selected' : '' }}>مجدولة</option>
                                    <option value="in_progress" {{ $lecture->status == 'in_progress' ? 'selected' : '' }}>قيد التنفيذ</option>
                                    <option value="completed" {{ $lecture->status == 'completed' ? 'selected' : '' }}>مكتملة</option>
                                    <option value="cancelled" {{ $lecture->status == 'cancelled' ? 'selected' : '' }}>ملغاة</option>
                                </select>
                                @endif
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-[#1F3A56] mb-2 uppercase tracking-wide">التاريخ والوقت</label>
                            <div class="flex items-center gap-2 text-[#1C2C39] font-black">
                                <i class="fas fa-calendar-alt text-[#2CA9BD]"></i>
                                {{ $lecture->scheduled_at->format('Y/m/d H:i') }}
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-[#1F3A56] mb-2 uppercase tracking-wide">المدة</label>
                            <div class="flex items-center gap-2 text-[#1C2C39] font-black">
                                <i class="fas fa-clock text-[#FFD34E]"></i>
                                {{ $lecture->duration_minutes }} دقيقة
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-[#1F3A56] mb-2 uppercase tracking-wide">الكورس</label>
                            <div class="flex items-center gap-2 text-[#1C2C39] font-bold">
                                <i class="fas fa-book text-[#2CA9BD]"></i>
                                {{ $lecture->course->title ?? 'غير محدد' }}
                            </div>
                        </div>
                        @if($lecture->lesson)
                        <div>
                            <label class="block text-xs font-bold text-[#1F3A56] mb-2 uppercase tracking-wide">الدرس</label>
                            <div class="flex items-center gap-2 text-[#1C2C39] font-bold">
                                <i class="fas fa-play-circle text-purple-600"></i>
                                {{ $lecture->lesson->title }}
                            </div>
                        </div>
                        @endif
                    </div>

                    @if($lecture->description)
                    <div class="pt-4 border-t-2 border-[#2CA9BD]/10">
                        <label class="block text-xs font-bold text-[#1F3A56] mb-2 uppercase tracking-wide">الوصف</label>
                        <div class="text-[#1C2C39] font-medium bg-gradient-to-r from-[#2CA9BD]/5 to-[#65DBE4]/5 p-4 rounded-xl border border-[#2CA9BD]/10">
                            {{ $lecture->description }}
                        </div>
                    </div>
                    @endif

                    @if($lecture->notes)
                    <div class="pt-4 border-t-2 border-[#2CA9BD]/10">
                        <label class="block text-xs font-bold text-[#1F3A56] mb-2 uppercase tracking-wide">الملاحظات</label>
                        <div class="text-[#1C2C39] font-medium bg-gradient-to-r from-[#2CA9BD]/5 to-[#65DBE4]/5 p-4 rounded-xl border border-[#2CA9BD]/10">
                            {{ $lecture->notes }}
                        </div>
                    </div>
                    @endif

                    <!-- الروابط -->
                    @if($lecture->teams_registration_link || $lecture->teams_meeting_link || $lecture->recording_url)
                    <div class="pt-4 border-t-2 border-[#2CA9BD]/10">
                        <label class="block text-xs font-bold text-[#1F3A56] mb-3 uppercase tracking-wide">الروابط</label>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                            @if($lecture->teams_registration_link)
                            <a href="{{ $lecture->teams_registration_link }}" target="_blank" 
                               class="link-card flex items-center gap-3 p-4 bg-gradient-to-r from-[#2CA9BD]/5 to-[#65DBE4]/5 rounded-xl border-2 border-[#2CA9BD]/10 hover:from-[#2CA9BD]/10 hover:to-[#65DBE4]/10 transition-all">
                                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-[#2CA9BD] to-[#65DBE4] flex items-center justify-center text-white shadow-md flex-shrink-0">
                                    <i class="fas fa-link text-lg"></i>
                                </div>
                                <div class="flex-1">
                                    <div class="font-black text-[#1C2C39] text-sm">تسجيل Teams</div>
                                    <div class="text-xs text-[#1F3A56] font-medium mt-0.5">رابط التسجيل</div>
                                </div>
                                <i class="fas fa-external-link-alt text-[#2CA9BD] text-sm"></i>
                            </a>
                            @endif
                            @if($lecture->teams_meeting_link)
                            <a href="{{ $lecture->teams_meeting_link }}" target="_blank" 
                               class="link-card flex items-center gap-3 p-4 bg-gradient-to-r from-blue-500/5 to-indigo-500/5 rounded-xl border-2 border-blue-500/10 hover:from-blue-500/10 hover:to-indigo-500/10 transition-all">
                                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white shadow-md flex-shrink-0">
                                    <i class="fas fa-video text-lg"></i>
                                </div>
                                <div class="flex-1">
                                    <div class="font-black text-[#1C2C39] text-sm">اجتماع Teams</div>
                                    <div class="text-xs text-[#1F3A56] font-medium mt-0.5">رابط الاجتماع</div>
                                </div>
                                <i class="fas fa-external-link-alt text-blue-600 text-sm"></i>
                            </a>
                            @endif
                            @if($lecture->recording_url)
                            <a href="{{ $lecture->recording_url }}" target="_blank" 
                               class="link-card flex items-center gap-3 p-4 bg-gradient-to-r from-purple-500/5 to-indigo-500/5 rounded-xl border-2 border-purple-500/10 hover:from-purple-500/10 hover:to-indigo-500/10 transition-all">
                                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-purple-500 to-indigo-600 flex items-center justify-center text-white shadow-md flex-shrink-0">
                                    <i class="fas fa-play-circle text-lg"></i>
                                </div>
                                <div class="flex-1">
                                    <div class="font-black text-[#1C2C39] text-sm">تسجيل المحاضرة</div>
                                    <div class="text-xs text-[#1F3A56] font-medium mt-0.5">رابط التسجيل</div>
                                </div>
                                <i class="fas fa-external-link-alt text-purple-600 text-sm"></i>
                            </a>
                            @endif
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- إدارة الحضور -->
            @if($lecture->has_attendance_tracking)
            <div class="info-card rounded-xl p-5 sm:p-6">
                <div class="flex items-center justify-between mb-4 pb-4 border-b-2 border-[#2CA9BD]/10">
                    <h3 class="text-lg sm:text-xl font-black text-[#1C2C39] flex items-center gap-2">
                        <i class="fas fa-clipboard-list text-[#2CA9BD]"></i>
                        إدارة الحضور والغياب
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
                                        <th class="px-4 py-3 text-right text-xs font-bold text-[#1C2C39] uppercase tracking-wider">الإجراءات</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($enrollments as $enrollment)
                                    @php
                                        $record = $attendanceRecords->get($enrollment->user_id);
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
                                                {{ $record && isset($record->attendance_minutes) ? $record->attendance_minutes : 0 }} / {{ $lecture->duration_minutes }}
                                            </div>
                                            @if($record && isset($record->attendance_percentage) && $record->attendance_percentage)
                                                <div class="text-xs text-[#1F3A56] font-medium">
                                                    {{ number_format($record->attendance_percentage, 1) }}%
                                                </div>
                                            @endif
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap">
                                            <select onchange="updateAttendance({{ $enrollment->user_id }}, this.value)" 
                                                    class="text-xs border-2 border-[#2CA9BD]/20 rounded-xl px-3 py-2 bg-white text-[#1C2C39] font-bold focus:border-[#2CA9BD] focus:ring-2 focus:ring-[#2CA9BD]/20 transition-all">
                                                <option value="present" {{ $record && $record->status == 'present' ? 'selected' : '' }}>حاضر</option>
                                                <option value="late" {{ $record && $record->status == 'late' ? 'selected' : '' }}>متأخر</option>
                                                <option value="partial" {{ $record && $record->status == 'partial' ? 'selected' : '' }}>جزئي</option>
                                                <option value="absent" {{ !$record || $record->status == 'absent' ? 'selected' : '' }}>غائب</option>
                                            </select>
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
            @endif
        </div>

        <!-- الشريط الجانبي -->
        <div class="space-y-6">
            <!-- الخيارات -->
            <div class="info-card rounded-xl p-5">
                <div class="flex items-center justify-between mb-4 pb-4 border-b-2 border-[#2CA9BD]/10">
                    <h3 class="text-lg font-black text-[#1C2C39] flex items-center gap-2">
                        <i class="fas fa-cog text-[#2CA9BD]"></i>
                        الخيارات
                    </h3>
                </div>
                <div class="space-y-3">
                    <div class="flex items-center justify-between p-3 bg-gradient-to-r from-[#2CA9BD]/5 to-[#65DBE4]/5 rounded-xl border border-[#2CA9BD]/10">
                        <span class="text-sm text-[#1F3A56] font-bold">تتبع الحضور</span>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold shadow-md {{ $lecture->has_attendance_tracking ? 'bg-gradient-to-r from-green-500 to-emerald-600 text-white' : 'bg-gradient-to-r from-gray-400 to-gray-500 text-white' }}">
                            <i class="fas {{ $lecture->has_attendance_tracking ? 'fa-check-circle' : 'fa-times-circle' }}"></i>
                            {{ $lecture->has_attendance_tracking ? 'مفعل' : 'معطل' }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-gradient-to-r from-[#FFD34E]/5 to-amber-500/5 rounded-xl border border-[#FFD34E]/10">
                        <span class="text-sm text-[#1F3A56] font-bold">يوجد واجب</span>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold shadow-md {{ $lecture->has_assignment ? 'bg-gradient-to-r from-green-500 to-emerald-600 text-white' : 'bg-gradient-to-r from-gray-400 to-gray-500 text-white' }}">
                            <i class="fas {{ $lecture->has_assignment ? 'fa-check-circle' : 'fa-times-circle' }}"></i>
                            {{ $lecture->has_assignment ? 'نعم' : 'لا' }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-gradient-to-r from-purple-500/5 to-indigo-500/5 rounded-xl border border-purple-500/10">
                        <span class="text-sm text-[#1F3A56] font-bold">يوجد تقييم</span>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold shadow-md {{ $lecture->has_evaluation ? 'bg-gradient-to-r from-green-500 to-emerald-600 text-white' : 'bg-gradient-to-r from-gray-400 to-gray-500 text-white' }}">
                            <i class="fas {{ $lecture->has_evaluation ? 'fa-check-circle' : 'fa-times-circle' }}"></i>
                            {{ $lecture->has_evaluation ? 'نعم' : 'لا' }}
                        </span>
                    </div>
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
                    <a href="{{ route('instructor.lectures.edit', $lecture) }}" 
                       class="flex items-center gap-3 p-3 bg-gradient-to-r from-[#2CA9BD]/10 to-[#65DBE4]/10 hover:from-[#2CA9BD]/20 hover:to-[#65DBE4]/20 rounded-xl border border-[#2CA9BD]/20 transition-all">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-[#2CA9BD] to-[#65DBE4] flex items-center justify-center text-white shadow-md">
                            <i class="fas fa-edit text-sm"></i>
                        </div>
                        <span class="font-bold text-[#1C2C39] text-sm">تعديل المحاضرة</span>
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
                    <a href="{{ route('instructor.attendance.lecture', $lecture) }}" 
                       class="flex items-center gap-3 p-3 bg-gradient-to-r from-blue-500/10 to-indigo-500/10 hover:from-blue-500/20 hover:to-indigo-500/20 rounded-xl border border-blue-500/20 transition-all">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white shadow-md">
                            <i class="fas fa-clipboard-list text-sm"></i>
                        </div>
                        <span class="font-bold text-[#1C2C39] text-sm">تفاصيل الحضور</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
