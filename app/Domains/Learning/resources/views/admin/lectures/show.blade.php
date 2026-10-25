@extends('layouts.admin')

@section('title', 'تفاصيل المحاضرة')
@section('header', 'تفاصيل المحاضرة')

@section('content')
@php
    $statusMap = [
        'scheduled' => ['label' => 'مجدولة', 'class' => 'bg-amber-100 text-amber-800'],
        'in_progress' => ['label' => 'قيد التنفيذ', 'class' => 'bg-blue-100 text-blue-800'],
        'completed' => ['label' => 'مكتملة', 'class' => 'bg-green-100 text-green-800'],
        'cancelled' => ['label' => 'ملغاة', 'class' => 'bg-red-100 text-red-800'],
    ];
    $status = $statusMap[$lecture->status ?? 'scheduled'] ?? $statusMap['scheduled'];
    $platformLabels = [
        'youtube' => 'YouTube',
        'vimeo' => 'Vimeo',
        'google_drive' => 'Google Drive',
        'direct' => 'رابط مباشر',
        'bunny' => 'Bunny.net',
    ];
    $platformLabel = $platformLabels[strtolower($lecture->video_platform ?? '')] ?? $lecture->video_platform ?? '—';
@endphp
<div class="w-full max-w-full px-4 py-6 space-y-6">
    <!-- الهيدر -->
    <div class="bg-gradient-to-l from-indigo-600 via-blue-600 to-cyan-500 rounded-2xl p-6 text-white shadow-lg">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="min-w-0">
                <nav class="text-sm text-white/80 mb-2">
                    <a href="{{ route('admin.dashboard') }}" class="hover:text-white">لوحة التحكم</a>
                    <span class="mx-2">/</span>
                    <a href="{{ route('admin.lectures.index') }}" class="hover:text-white">المحاضرات</a>
                    <span class="mx-2">/</span>
                    <a href="{{ route('admin.lectures.by-course', $lecture->course_id) }}" class="hover:text-white">{{ Str::limit($lecture->course->title ?? '', 30) }}</a>
                    <span class="mx-2">/</span>
                    <span class="text-white">تفاصيل المحاضرة</span>
                </nav>
                <h1 class="text-xl sm:text-2xl font-bold mt-1">{{ $lecture->title }}</h1>
                <p class="text-sm text-white/90 mt-1">{{ $lecture->course->title ?? '' }}</p>
            </div>
            <div class="flex flex-wrap gap-2 flex-shrink-0">
                <a href="{{ route('admin.lectures.edit', $lecture) }}" class="inline-flex items-center gap-2 bg-white text-indigo-600 hover:bg-gray-100 px-4 py-2.5 rounded-xl font-semibold transition-colors">
                    <i class="fas fa-edit"></i>
                    تعديل
                </a>
                <a href="{{ route('admin.lectures.by-course', $lecture->course_id) }}" class="inline-flex items-center gap-2 bg-white/20 hover:bg-white/30 text-white px-4 py-2.5 rounded-xl font-medium transition-colors border border-white/30">
                    <i class="fas fa-arrow-right"></i>
                    رجوع لمحاضرات الكورس
                </a>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="rounded-xl bg-green-50 border border-green-200 text-green-800 px-4 py-3 flex items-center gap-2">
            <i class="fas fa-check-circle text-green-600"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- العمود الرئيسي -->
        <div class="lg:col-span-2 space-y-6">
            <!-- المعلومات الأساسية -->
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                    <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                        <i class="fas fa-info-circle text-indigo-600"></i>
                        المعلومات الأساسية
                    </h2>
                </div>
                <div class="p-6 space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">الكورس</p>
                            <p class="text-gray-900 font-medium">{{ $lecture->course->title ?? '—' }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">المحاضر</p>
                            <p class="text-gray-900 font-medium">{{ $lecture->instructor->name ?? '—' }}</p>
                        </div>
                        @if($lecture->lesson)
                        <div class="sm:col-span-2">
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">الدرس المرتبط (اختياري)</p>
                            <p class="text-gray-900 font-medium">{{ $lecture->lesson->title }}</p>
                        </div>
                        @endif
                    </div>
                    @if($lecture->description)
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">الوصف</p>
                        <p class="text-gray-700 whitespace-pre-wrap">{{ $lecture->description }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- رابط التسجيل / الفيديو -->
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                    <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                        <i class="fas fa-video text-indigo-600"></i>
                        رابط تسجيل المحاضرة
                    </h2>
                </div>
                <div class="p-6 space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">منصة الفيديو</p>
                            <p class="text-gray-900 font-medium">{{ $platformLabel }}</p>
                        </div>
                        @if($lecture->recording_url)
                        <div class="sm:col-span-2">
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">الرابط</p>
                            <a href="{{ $lecture->recording_url }}" target="_blank" rel="noopener" class="text-indigo-600 hover:text-indigo-800 font-medium break-all inline-flex items-center gap-1">
                                {{ Str::limit($lecture->recording_url, 60) }}
                                <i class="fas fa-external-link-alt text-xs"></i>
                            </a>
                        </div>
                        @else
                        <div class="sm:col-span-2">
                            <p class="text-gray-500">لم يُضف رابط تسجيل</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- التاريخ والوقت والمدة -->
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                    <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                        <i class="fas fa-calendar-alt text-indigo-600"></i>
                        التاريخ والوقت
                    </h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">تاريخ ووقت المحاضرة</p>
                            <p class="text-gray-900 font-medium">{{ $lecture->scheduled_at ? $lecture->scheduled_at->format('Y-m-d H:i') : '—' }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">المدة (دقيقة)</p>
                            <p class="text-gray-900 font-medium">{{ $lecture->duration_minutes ?? '—' }} دقيقة</p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">الحالة</p>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold {{ $status['class'] }}">
                                {{ $status['label'] }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- مواد المحاضرة -->
            @if($lecture->materials->isNotEmpty())
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                    <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                        <i class="fas fa-paperclip text-indigo-600"></i>
                        مواد المحاضرة
                    </h2>
                </div>
                <div class="p-6">
                    <ul class="space-y-3">
                        @foreach($lecture->materials as $material)
                        <li class="flex flex-wrap items-center justify-between gap-3 p-4 bg-slate-50 rounded-xl border border-slate-200">
                            <div class="min-w-0 flex-1">
                                <p class="font-medium text-gray-900">{{ $material->title ?: $material->file_name }}</p>
                                <p class="text-sm text-gray-500 mt-0.5">{{ $material->file_name }}</p>
                            </div>
                            <div class="flex items-center gap-3 flex-shrink-0">
                                @if($material->is_visible_to_student)
                                    <span class="text-xs font-semibold text-green-700 bg-green-100 px-2 py-1 rounded-lg">ظاهر للطالب</span>
                                @else
                                    <span class="text-xs font-semibold text-gray-600 bg-gray-200 px-2 py-1 rounded-lg">مخفي</span>
                                @endif
                                <a href="{{ asset('storage/' . $material->file_path) }}" target="_blank" class="inline-flex items-center gap-1 text-indigo-600 hover:text-indigo-800 font-medium text-sm">
                                    <i class="fas fa-download"></i>
                                    تحميل
                                </a>
                            </div>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
            @endif

            <!-- الملاحظات -->
            @if($lecture->notes)
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                    <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                        <i class="fas fa-sticky-note text-indigo-600"></i>
                        ملاحظات
                    </h2>
                </div>
                <div class="p-6">
                    <p class="text-gray-700 whitespace-pre-wrap">{{ $lecture->notes }}</p>
                </div>
            </div>
            @endif
        </div>

        <!-- الشريط الجانبي -->
        <div class="space-y-6">
            <!-- خيارات المحاضرة -->
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                    <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                        <i class="fas fa-cog text-indigo-600"></i>
                        الخيارات
                    </h2>
                </div>
                <div class="p-6 space-y-3">
                    <div class="flex items-center gap-3 p-3 rounded-xl {{ $lecture->has_attendance_tracking ? 'bg-green-50 border border-green-200' : 'bg-gray-50 border border-gray-200' }}">
                        <i class="fas {{ $lecture->has_attendance_tracking ? 'fa-check-circle text-green-600' : 'fa-times-circle text-gray-400' }}"></i>
                        <span class="font-medium {{ $lecture->has_attendance_tracking ? 'text-green-800' : 'text-gray-600' }}">تتبع الحضور</span>
                    </div>
                    <div class="flex items-center gap-3 p-3 rounded-xl {{ $lecture->has_assignment ? 'bg-blue-50 border border-blue-200' : 'bg-gray-50 border border-gray-200' }}">
                        <i class="fas {{ $lecture->has_assignment ? 'fa-check-circle text-blue-600' : 'fa-times-circle text-gray-400' }}"></i>
                        <span class="font-medium {{ $lecture->has_assignment ? 'text-blue-800' : 'text-gray-600' }}">يوجد واجب</span>
                    </div>
                    <div class="flex items-center gap-3 p-3 rounded-xl {{ $lecture->has_evaluation ? 'bg-amber-50 border border-amber-200' : 'bg-gray-50 border border-gray-200' }}">
                        <i class="fas {{ $lecture->has_evaluation ? 'fa-check-circle text-amber-600' : 'fa-times-circle text-gray-400' }}"></i>
                        <span class="font-medium {{ $lecture->has_evaluation ? 'text-amber-800' : 'text-gray-600' }}">يوجد تقييم</span>
                    </div>
                </div>
            </div>

            @if($lecture->has_attendance_tracking)
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                    <h2 class="text-lg font-bold text-gray-900">الحضور</h2>
                </div>
                <div class="p-6">
                    <a href="{{ route('admin.attendance.lecture', $lecture) }}" class="block w-full text-center py-2.5 px-4 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-semibold transition-colors">
                        عرض تفاصيل الحضور
                        <i class="fas fa-arrow-left mr-1"></i>
                    </a>
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- واجبات المحاضرة -->
    @if($lecture->has_assignment && $lecture->assignments->isNotEmpty())
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
            <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                <i class="fas fa-tasks text-indigo-600"></i>
                واجبات المحاضرة
            </h2>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($lecture->assignments as $assignment)
                <div class="p-4 bg-slate-50 rounded-xl border border-slate-200">
                    <h4 class="font-semibold text-gray-900">{{ $assignment->title }}</h4>
                    <p class="text-sm text-gray-600 mt-1">
                        تاريخ التسليم: {{ $assignment->due_date ? $assignment->due_date->format('Y-m-d H:i') : '—' }}
                    </p>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
