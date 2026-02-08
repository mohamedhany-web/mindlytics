@extends('layouts.app')

@section('title', 'كورساتي الأوفلاين')
@section('header', 'كورساتي الأوفلاين')

@push('styles')
<style>
    .offline-card {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        transition: all 0.25s ease;
        box-shadow: 0 1px 3px rgba(0,0,0,0.06);
    }
    .offline-card:hover {
        box-shadow: 0 8px 20px rgba(14, 165, 233, 0.12);
        border-color: #bae6fd;
    }
    .stats-card-offline {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        transition: all 0.2s ease;
        box-shadow: 0 1px 3px rgba(0,0,0,0.06);
    }
    .stats-card-offline:hover {
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    }
</style>
@endpush

@section('content')
<div class="w-full px-4 sm:px-6 lg:px-8 py-6 space-y-6">
    <!-- الهيدر -->
    <div class="bg-white rounded-xl p-5 border border-gray-200 shadow-sm">
        <h1 class="text-xl sm:text-2xl font-bold text-gray-900 mb-1">كورساتي الأوفلاين</h1>
        <p class="text-sm text-gray-500">الكورسات الحضورية المسجلة فيها</p>
    </div>

    <!-- الإحصائيات -->
    <div class="grid grid-cols-2 gap-3 sm:gap-4">
        <div class="stats-card-offline p-4">
            <div class="flex items-center justify-between gap-3">
                <div class="min-w-0">
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">الكورسات</p>
                    <p class="text-2xl font-bold text-sky-600 leading-none">{{ $stats['total_offline'] }}</p>
                </div>
                <div class="w-10 h-10 rounded-lg bg-sky-100 flex items-center justify-center text-sky-600 flex-shrink-0">
                    <i class="fas fa-chalkboard-teacher"></i>
                </div>
            </div>
        </div>
        <div class="stats-card-offline p-4">
            <div class="flex items-center justify-between gap-3">
                <div class="min-w-0">
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">الأنشطة</p>
                    <p class="text-2xl font-bold text-amber-600 leading-none">{{ $stats['total_activities'] }}</p>
                </div>
                <div class="w-10 h-10 rounded-lg bg-amber-100 flex items-center justify-center text-amber-600 flex-shrink-0">
                    <i class="fas fa-tasks"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- قائمة الكورسات -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
        @forelse($enrollments as $enrollment)
            @php
                $course = $enrollment->course;
            @endphp
            <a href="{{ route('student.offline-courses.show', $course->id) }}" class="offline-card block overflow-hidden">
                <div class="h-32 bg-sky-100 flex items-center justify-center text-sky-600 flex-shrink-0">
                    <i class="fas fa-chalkboard-teacher text-3xl"></i>
                </div>
                <div class="p-4">
                    <div class="flex items-start justify-between gap-2 mb-2">
                        <h3 class="text-base font-bold text-gray-900 line-clamp-2 leading-snug flex-1 min-w-0">{{ $course->title }}</h3>
                        <span class="px-2 py-0.5 rounded-md text-xs font-semibold bg-sky-100 text-sky-700 flex-shrink-0">أوفلاين</span>
                    </div>
                    <p class="text-xs text-gray-500 mb-2">
                        {{ $course->instructor->name ?? '—' }}
                        @if($course->locationModel || $course->location)
                            · {{ $course->locationModel->name ?? $course->location ?? '—' }}
                        @endif
                    </p>
                    @if($course->description)
                        <p class="text-xs text-gray-600 line-clamp-2 mb-3">{{ Str::limit($course->description, 80) }}</p>
                    @endif
                    <div class="flex items-center justify-between text-xs text-gray-500 mb-3">
                        <span><i class="fas fa-users ml-1"></i>{{ $course->current_students ?? 0 }} / {{ $course->max_students ?? '—' }}</span>
                        @if($enrollment->group)
                            <span class="truncate max-w-[120px]" title="{{ $enrollment->group->name }}"><i class="fas fa-users-cog ml-1"></i>{{ $enrollment->group->name }}</span>
                        @endif
                    </div>
                    <div class="flex items-center justify-between gap-2 mb-3">
                        <span class="text-xs font-medium text-gray-600">التقدم</span>
                        <span class="text-sm font-bold text-sky-600">{{ number_format($enrollment->progress, 0) }}%</span>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-2 overflow-hidden">
                        <div class="h-full bg-sky-500 rounded-full transition-all duration-500" style="width: {{ min($enrollment->progress, 100) }}%;"></div>
                    </div>
                    <span class="mt-3 inline-flex items-center justify-center gap-2 w-full py-2.5 rounded-lg bg-sky-500 hover:bg-sky-600 text-white text-sm font-semibold transition-colors">
                        <i class="fas fa-eye text-xs"></i>
                        عرض التفاصيل
                    </span>
                </div>
            </a>
        @empty
            <div class="col-span-full rounded-xl p-10 sm:p-12 text-center bg-gray-50 border border-dashed border-gray-200">
                <div class="w-16 h-16 bg-sky-100 rounded-2xl flex items-center justify-center mx-auto mb-4 text-sky-600">
                    <i class="fas fa-chalkboard-teacher text-2xl"></i>
                </div>
                <h3 class="text-lg font-bold text-gray-900 mb-2">لا توجد كورسات أوفلاين</h3>
                <p class="text-sm text-gray-500">لم يتم تسجيلك في أي كورس أوفلاين بعد</p>
            </div>
        @endforelse
    </div>

    @if($enrollments->hasPages())
        <div class="flex justify-center">
            {{ $enrollments->links() }}
        </div>
    @endif
</div>
@endsection
