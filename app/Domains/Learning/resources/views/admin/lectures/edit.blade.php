@extends('layouts.admin')

@section('title', 'تعديل المحاضرة')
@section('header', 'تعديل المحاضرة')

@section('content')
@php
    $scheduledAtValue = old('scheduled_at');
    if ($scheduledAtValue === null && $lecture->scheduled_at) {
        $scheduledAtValue = $lecture->scheduled_at->format('Y-m-d\TH:i');
    }
    $platforms = [
        'youtube' => 'YouTube',
        'vimeo' => 'Vimeo',
        'google_drive' => 'Google Drive',
        'direct' => 'رابط مباشر',
        'bunny' => 'Bunny.net',
    ];
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
                    <span class="text-white">تعديل المحاضرة</span>
                </nav>
                <h1 class="text-xl sm:text-2xl font-bold mt-1">تعديل المحاضرة</h1>
                <p class="text-sm text-white/90 mt-1">{{ Str::limit($lecture->title, 50) }}</p>
            </div>
            <div class="flex flex-wrap gap-2 flex-shrink-0">
                <a href="{{ route('admin.lectures.show', $lecture) }}" class="inline-flex items-center gap-2 bg-white/20 hover:bg-white/30 text-white px-4 py-2.5 rounded-xl font-medium transition-colors border border-white/30">
                    <i class="fas fa-eye"></i>
                    عرض
                </a>
                <a href="{{ route('admin.lectures.by-course', $lecture->course_id) }}" class="inline-flex items-center gap-2 bg-white/20 hover:bg-white/30 text-white px-4 py-2.5 rounded-xl font-medium transition-colors border border-white/30">
                    <i class="fas fa-arrow-right"></i>
                    رجوع لمحاضرات الكورس
                </a>
            </div>
        </div>
    </div>

    <form action="{{ route('admin.lectures.update', $lecture) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- المعلومات الأساسية -->
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                    <i class="fas fa-info-circle text-indigo-600"></i>
                    المعلومات الأساسية
                </h2>
            </div>
            <div class="p-6 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="course_id" class="block text-sm font-semibold text-gray-700 mb-2">الكورس <span class="text-red-500">*</span></label>
                        <select name="course_id" id="course_id" required
                                class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            @foreach($courses as $course)
                                <option value="{{ $course->id }}" {{ old('course_id', $lecture->course_id) == $course->id ? 'selected' : '' }}>{{ Str::limit($course->title, 55) }}</option>
                            @endforeach
                        </select>
                        @error('course_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="instructor_id" class="block text-sm font-semibold text-gray-700 mb-2">المحاضر <span class="text-red-500">*</span></label>
                        <select name="instructor_id" id="instructor_id" required
                                class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            @foreach($instructors as $instructor)
                                <option value="{{ $instructor->id }}" {{ old('instructor_id', $lecture->instructor_id) == $instructor->id ? 'selected' : '' }}>{{ $instructor->name }}</option>
                            @endforeach
                        </select>
                        @error('instructor_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                </div>
                <div>
                    <label for="course_lesson_id" class="block text-sm font-semibold text-gray-700 mb-2">الدرس المرتبط (اختياري)</label>
                    <select name="course_lesson_id" id="course_lesson_id"
                            class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">لا يوجد</option>
                        @foreach($lessons as $lesson)
                            <option value="{{ $lesson->id }}" {{ old('course_lesson_id', $lecture->course_lesson_id) == $lesson->id ? 'selected' : '' }}>{{ $lesson->title }}</option>
                        @endforeach
                    </select>
                    @error('course_lesson_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="title" class="block text-sm font-semibold text-gray-700 mb-2">عنوان المحاضرة <span class="text-red-500">*</span></label>
                    <input type="text" name="title" id="title" value="{{ old('title', $lecture->title) }}" required
                           class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                           placeholder="مثال: المحاضرة الأولى - المقدمة">
                    @error('title')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="description" class="block text-sm font-semibold text-gray-700 mb-2">الوصف</label>
                    <textarea name="description" id="description" rows="3"
                              class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 resize-none"
                              placeholder="وصف مختصر عن المحاضرة">{{ old('description', $lecture->description) }}</textarea>
                    @error('description')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
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
            <div class="p-6 space-y-6">
                <div>
                    <label for="video_platform" class="block text-sm font-semibold text-gray-700 mb-2">منصة الفيديو</label>
                    <select name="video_platform" id="video_platform"
                            class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">—</option>
                        @foreach($platforms as $key => $label)
                            <option value="{{ $key }}" {{ old('video_platform', $lecture->video_platform) == $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="recording_url" class="block text-sm font-semibold text-gray-700 mb-2">رابط التسجيل أو الفيديو</label>
                    <input type="url" name="recording_url" id="recording_url" value="{{ old('recording_url', $lecture->recording_url) }}"
                           class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                           placeholder="https://...">
                    @error('recording_url')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        <!-- التاريخ والوقت والحالة -->
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                    <i class="fas fa-calendar-alt text-indigo-600"></i>
                    التاريخ والوقت والحالة
                </h2>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div>
                        <label for="scheduled_at" class="block text-sm font-semibold text-gray-700 mb-2">تاريخ ووقت المحاضرة <span class="text-red-500">*</span></label>
                        <input type="datetime-local" name="scheduled_at" id="scheduled_at" value="{{ $scheduledAtValue }}" required
                               class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        @error('scheduled_at')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="duration_minutes" class="block text-sm font-semibold text-gray-700 mb-2">المدة (دقيقة) <span class="text-red-500">*</span></label>
                        <input type="number" name="duration_minutes" id="duration_minutes" value="{{ old('duration_minutes', $lecture->duration_minutes) }}" min="1" max="480" required
                               class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        @error('duration_minutes')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="status" class="block text-sm font-semibold text-gray-700 mb-2">الحالة <span class="text-red-500">*</span></label>
                        <select name="status" id="status" required
                                class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="scheduled" {{ old('status', $lecture->status) == 'scheduled' ? 'selected' : '' }}>مجدولة</option>
                            <option value="in_progress" {{ old('status', $lecture->status) == 'in_progress' ? 'selected' : '' }}>قيد التنفيذ</option>
                            <option value="completed" {{ old('status', $lecture->status) == 'completed' ? 'selected' : '' }}>مكتملة</option>
                            <option value="cancelled" {{ old('status', $lecture->status) == 'cancelled' ? 'selected' : '' }}>ملغاة</option>
                        </select>
                        @error('status')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- الملاحظات -->
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                    <i class="fas fa-sticky-note text-indigo-600"></i>
                    ملاحظات
                </h2>
            </div>
            <div class="p-6">
                <textarea name="notes" id="notes" rows="4"
                          class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 resize-none"
                          placeholder="ملاحظات إضافية">{{ old('notes', $lecture->notes) }}</textarea>
                @error('notes')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
        </div>

        <!-- الخيارات -->
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                    <i class="fas fa-cog text-indigo-600"></i>
                    خيارات المحاضرة
                </h2>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <label class="flex items-center gap-3 p-4 rounded-xl border-2 border-gray-200 hover:border-indigo-200 cursor-pointer transition-colors">
                        <input type="hidden" name="has_attendance_tracking" value="0">
                        <input type="checkbox" name="has_attendance_tracking" value="1" {{ old('has_attendance_tracking', $lecture->has_attendance_tracking) ? 'checked' : '' }}
                               class="w-5 h-5 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                        <span class="font-medium text-gray-800">تتبع الحضور</span>
                    </label>
                    <label class="flex items-center gap-3 p-4 rounded-xl border-2 border-gray-200 hover:border-indigo-200 cursor-pointer transition-colors">
                        <input type="hidden" name="has_assignment" value="0">
                        <input type="checkbox" name="has_assignment" value="1" {{ old('has_assignment', $lecture->has_assignment) ? 'checked' : '' }}
                               class="w-5 h-5 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                        <span class="font-medium text-gray-800">يوجد واجب</span>
                    </label>
                    <label class="flex items-center gap-3 p-4 rounded-xl border-2 border-gray-200 hover:border-indigo-200 cursor-pointer transition-colors">
                        <input type="hidden" name="has_evaluation" value="0">
                        <input type="checkbox" name="has_evaluation" value="1" {{ old('has_evaluation', $lecture->has_evaluation) ? 'checked' : '' }}
                               class="w-5 h-5 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                        <span class="font-medium text-gray-800">يوجد تقييم للمحاضر</span>
                    </label>
                </div>
            </div>
        </div>

        <!-- أزرار الحفظ -->
        <div class="flex flex-wrap items-center justify-end gap-3">
            <a href="{{ route('admin.lectures.by-course', $lecture->course_id) }}" class="px-6 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl font-semibold transition-colors">
                إلغاء
            </a>
            <button type="submit" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-semibold transition-colors shadow-lg hover:shadow-xl inline-flex items-center gap-2">
                <i class="fas fa-save"></i>
                حفظ التغييرات
            </button>
        </div>
    </form>
</div>
@endsection
