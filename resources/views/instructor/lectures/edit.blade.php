@extends('layouts.app')

@section('title', 'تعديل المحاضرة - ' . $lecture->title)
@section('header', 'تعديل المحاضرة')

@section('content')
<div class="space-y-6">
    <!-- الهيدر -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border border-gray-200 dark:border-gray-700">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">تعديل المحاضرة</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-1">{{ $lecture->title }}</p>
            </div>
            <a href="{{ route('instructor.lectures.show', $lecture) }}" 
               class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                <i class="fas fa-arrow-right ml-2"></i>
                العودة
            </a>
        </div>
    </div>

    <!-- النموذج -->
    <form action="{{ route('instructor.lectures.update', $lecture) }}" method="POST" class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
        @csrf
        @method('PUT')
        
        <div class="p-6 space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- الكورس -->
                <div>
                    <label for="course_id" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                        الكورس <span class="text-rose-500">*</span>
                    </label>
                    <select name="course_id" id="course_id" required
                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500 dark:bg-gray-700 dark:text-white">
                        <option value="">اختر الكورس</option>
                        @foreach($courses as $course)
                            <option value="{{ $course->id }}" {{ old('course_id', $lecture->course_id) == $course->id ? 'selected' : '' }}>
                                {{ $course->title }}
                            </option>
                        @endforeach
                    </select>
                    @error('course_id')
                        <p class="mt-1 text-sm text-rose-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- الدرس (اختياري) -->
                <div>
                    <label for="course_lesson_id" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                        الدرس (اختياري)
                    </label>
                    <select name="course_lesson_id" id="course_lesson_id"
                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500 dark:bg-gray-700 dark:text-white">
                        <option value="">بدون درس محدد</option>
                        @foreach($lessons as $lesson)
                            <option value="{{ $lesson->id }}" {{ old('course_lesson_id', $lecture->course_lesson_id) == $lesson->id ? 'selected' : '' }}>
                                {{ $lesson->title }}
                            </option>
                        @endforeach
                    </select>
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">يمكنك ربط المحاضرة بدرس محدد من الكورس</p>
                    @error('course_lesson_id')
                        <p class="mt-1 text-sm text-rose-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- العنوان -->
                <div>
                    <label for="title" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                        عنوان المحاضرة <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="title" id="title" value="{{ old('title', $lecture->title) }}" required
                           class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500 dark:bg-gray-700 dark:text-white">
                    @error('title')
                        <p class="mt-1 text-sm text-rose-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- الوصف -->
            <div>
                <label for="description" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                    الوصف
                </label>
                <textarea name="description" id="description" rows="3"
                          class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500 dark:bg-gray-700 dark:text-white">{{ old('description', $lecture->description) }}</textarea>
                @error('description')
                    <p class="mt-1 text-sm text-rose-500">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- التاريخ والوقت -->
                <div>
                    <label for="scheduled_at" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                        التاريخ والوقت <span class="text-rose-500">*</span>
                    </label>
                    <input type="datetime-local" name="scheduled_at" id="scheduled_at" 
                           value="{{ old('scheduled_at', $lecture->scheduled_at->format('Y-m-d\TH:i')) }}" required
                           class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500 dark:bg-gray-700 dark:text-white">
                    @error('scheduled_at')
                        <p class="mt-1 text-sm text-rose-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- المدة -->
                <div>
                    <label for="duration_minutes" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                        المدة (بالدقائق) <span class="text-rose-500">*</span>
                    </label>
                    <input type="number" name="duration_minutes" id="duration_minutes" 
                           value="{{ old('duration_minutes', $lecture->duration_minutes) }}" min="15" max="480" required
                           class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500 dark:bg-gray-700 dark:text-white">
                    @error('duration_minutes')
                        <p class="mt-1 text-sm text-rose-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- الحالة -->
            <div>
                <label for="status" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                    الحالة <span class="text-rose-500">*</span>
                </label>
                <select name="status" id="status" required
                        class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500 dark:bg-gray-700 dark:text-white">
                    <option value="scheduled" {{ old('status', $lecture->status) == 'scheduled' ? 'selected' : '' }}>مجدولة</option>
                    <option value="in_progress" {{ old('status', $lecture->status) == 'in_progress' ? 'selected' : '' }}>قيد التنفيذ</option>
                    <option value="completed" {{ old('status', $lecture->status) == 'completed' ? 'selected' : '' }}>مكتملة</option>
                    <option value="cancelled" {{ old('status', $lecture->status) == 'cancelled' ? 'selected' : '' }}>ملغاة</option>
                </select>
                @error('status')
                    <p class="mt-1 text-sm text-rose-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- روابط Teams -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="teams_registration_link" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                        رابط تسجيل Teams
                    </label>
                    <input type="url" name="teams_registration_link" id="teams_registration_link" 
                           value="{{ old('teams_registration_link', $lecture->teams_registration_link) }}"
                           class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500 dark:bg-gray-700 dark:text-white">
                    @error('teams_registration_link')
                        <p class="mt-1 text-sm text-rose-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="teams_meeting_link" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                        رابط اجتماع Teams
                    </label>
                    <input type="url" name="teams_meeting_link" id="teams_meeting_link" 
                           value="{{ old('teams_meeting_link', $lecture->teams_meeting_link) }}"
                           class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500 dark:bg-gray-700 dark:text-white">
                    @error('teams_meeting_link')
                        <p class="mt-1 text-sm text-rose-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- رابط التسجيل -->
            <div>
                <label for="recording_url" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                    رابط تسجيل المحاضرة
                </label>
                <input type="url" name="recording_url" id="recording_url" 
                       value="{{ old('recording_url', $lecture->recording_url) }}"
                       class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500 dark:bg-gray-700 dark:text-white">
                @error('recording_url')
                    <p class="mt-1 text-sm text-rose-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- الملاحظات -->
            <div>
                <label for="notes" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                    الملاحظات
                </label>
                <textarea name="notes" id="notes" rows="3"
                          class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500 dark:bg-gray-700 dark:text-white">{{ old('notes', $lecture->notes) }}</textarea>
                @error('notes')
                    <p class="mt-1 text-sm text-rose-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- الخيارات -->
            <div class="space-y-3">
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                    الخيارات
                </label>
                
                <label class="flex items-center gap-3 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                    <input type="checkbox" name="has_attendance_tracking" value="1" 
                           {{ old('has_attendance_tracking', $lecture->has_attendance_tracking) ? 'checked' : '' }}
                           class="w-5 h-5 text-sky-600 border-gray-300 rounded focus:ring-sky-500">
                    <div>
                        <div class="font-medium text-gray-900 dark:text-white">تتبع الحضور</div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">تسجيل حضور الطلاب تلقائياً أو يدوياً</div>
                    </div>
                </label>

                <label class="flex items-center gap-3 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                    <input type="checkbox" name="has_assignment" value="1" 
                           {{ old('has_assignment', $lecture->has_assignment) ? 'checked' : '' }}
                           class="w-5 h-5 text-sky-600 border-gray-300 rounded focus:ring-sky-500">
                    <div>
                        <div class="font-medium text-gray-900 dark:text-white">يوجد واجب</div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">إضافة واجب مرتبط بهذه المحاضرة</div>
                    </div>
                </label>

                <label class="flex items-center gap-3 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                    <input type="checkbox" name="has_evaluation" value="1" 
                           {{ old('has_evaluation', $lecture->has_evaluation) ? 'checked' : '' }}
                           class="w-5 h-5 text-sky-600 border-gray-300 rounded focus:ring-sky-500">
                    <div>
                        <div class="font-medium text-gray-900 dark:text-white">يوجد تقييم</div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">السماح للطلاب بتقييم المحاضرة</div>
                    </div>
                </label>
            </div>
        </div>

        <!-- الأزرار -->
        <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700 border-t border-gray-200 dark:border-gray-600 flex items-center justify-end gap-3">
            <a href="{{ route('instructor.lectures.show', $lecture) }}" 
               class="px-6 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg font-medium hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                إلغاء
            </a>
            <button type="submit" 
                    class="bg-gradient-to-r from-sky-600 to-sky-700 hover:from-sky-700 hover:to-sky-800 text-white px-6 py-2 rounded-lg font-medium transition-colors shadow-lg shadow-sky-500/30">
                <i class="fas fa-save ml-2"></i>
                حفظ التعديلات
            </button>
        </div>
    </form>
</div>

<script>
    // تحديث قائمة الدروس عند اختيار الكورس
    document.getElementById('course_id').addEventListener('change', function() {
        const courseId = this.value;
        const lessonSelect = document.getElementById('course_lesson_id');
        
        // مسح الخيارات الحالية (ما عدا الخيار الأول)
        while (lessonSelect.children.length > 1) {
            lessonSelect.removeChild(lessonSelect.lastChild);
        }
        
        if (courseId) {
            // جلب دروس الكورس
            fetch(`/api/courses/${courseId}/lessons`)
                .then(response => response.json())
                .then(data => {
                    if (data.lessons) {
                        data.lessons.forEach(lesson => {
                            const option = document.createElement('option');
                            option.value = lesson.id;
                            option.textContent = lesson.title;
                            lessonSelect.appendChild(option);
                        });
                    }
                })
                .catch(error => {
                    console.error('Error fetching lessons:', error);
                });
        }
    });
</script>
@endsection

