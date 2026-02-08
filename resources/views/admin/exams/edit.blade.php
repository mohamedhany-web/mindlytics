@extends('layouts.admin')

@section('title', 'تحرير الامتحان')
@section('header', 'تحرير الامتحان: ' . $exam->title)

@section('content')
<div class="space-y-6">
    <!-- الهيدر -->
    <div class="flex items-center justify-between">
        <div>
            <nav class="text-sm text-gray-500 mb-2">
                <a href="{{ route('admin.dashboard') }}" class="hover:text-primary-600">لوحة التحكم</a>
                <span class="mx-2">/</span>
                <a href="{{ route('admin.exams.index') }}" class="hover:text-primary-600">الامتحانات</a>
                <span class="mx-2">/</span>
                <a href="{{ route('admin.exams.show', $exam) }}" class="hover:text-primary-600">{{ $exam->title }}</a>
                <span class="mx-2">/</span>
                <span>تحرير</span>
            </nav>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.exams.show', $exam) }}" 
               class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                <i class="fas fa-arrow-right ml-2"></i>
                العودة
            </a>
        </div>
    </div>

    <!-- نموذج تحرير الامتحان -->
    <form action="{{ route('admin.exams.update', $exam) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
            <!-- المحتوى الرئيسي -->
            <div class="xl:col-span-2 space-y-6">
                <!-- معلومات أساسية -->
                <div class="bg-white shadow-sm rounded-lg border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">معلومات الامتحان</h3>
                    </div>
                    <div class="p-6 space-y-6">
                        <!-- عنوان الامتحان -->
                        <div>
                            <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                                عنوان الامتحان <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="title" id="title" value="{{ old('title', $exam->title) }}" required
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                                   placeholder="مثال: امتحان الوحدة الأولى - الرياضيات">
                            @error('title')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- اختيار الكورس -->
                        <div>
                            <label for="advanced_course_id" class="block text-sm font-medium text-gray-700 mb-2">
                                الكورس <span class="text-red-500">*</span>
                            </label>
                            <select name="advanced_course_id" id="advanced_course_id" required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                <option value="">اختر الكورس</option>
                                @foreach($courses as $course)
                                    <option value="{{ $course->id }}" {{ old('advanced_course_id', $exam->advanced_course_id) == $course->id ? 'selected' : '' }}>
                                        {{ $course->title }}
                                        @if($course->academicSubject)
                                            - {{ $course->academicSubject->name }}
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                            @error('advanced_course_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- اختيار الدرس (اختياري) -->
                        <div>
                            <label for="course_lesson_id" class="block text-sm font-medium text-gray-700 mb-2">
                                الدرس (اختياري)
                            </label>
                            <select name="course_lesson_id" id="course_lesson_id"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                <option value="">اختر الدرس (اختياري)</option>
                                @foreach($lessons as $lesson)
                                    <option value="{{ $lesson->id }}" {{ old('course_lesson_id', $exam->course_lesson_id) == $lesson->id ? 'selected' : '' }}>
                                        {{ $lesson->title }}
                                    </option>
                                @endforeach
                            </select>
                            @error('course_lesson_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- وصف الامتحان -->
                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                                وصف الامتحان
                            </label>
                            <textarea name="description" id="description" rows="3"
                                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                                      placeholder="وصف مختصر للامتحان">{{ old('description', $exam->description) }}</textarea>
                            @error('description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- تعليمات الامتحان -->
                        <div>
                            <label for="instructions" class="block text-sm font-medium text-gray-700 mb-2">
                                تعليمات الامتحان
                            </label>
                            <textarea name="instructions" id="instructions" rows="4"
                                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                                      placeholder="تعليمات مهمة للطلاب قبل بدء الامتحان">{{ old('instructions', $exam->instructions) }}</textarea>
                            @error('instructions')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- إعدادات الامتحان -->
                <div class="bg-white shadow-sm rounded-lg border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">إعدادات الامتحان</h3>
                    </div>
                    <div class="p-6 space-y-6">
                        <!-- الصف الأول -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- مدة الامتحان -->
                            <div>
                                <label for="duration_minutes" class="block text-sm font-medium text-gray-700 mb-2">
                                    مدة الامتحان (دقيقة) <span class="text-red-500">*</span>
                                </label>
                                <input type="number" name="duration_minutes" id="duration_minutes" 
                                       value="{{ old('duration_minutes', $exam->duration_minutes) }}" required min="5" max="480"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                                       placeholder="60">
                                @error('duration_minutes')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- عدد المحاولات -->
                            <div>
                                <label for="attempts_allowed" class="block text-sm font-medium text-gray-700 mb-2">
                                    عدد المحاولات المسموحة <span class="text-red-500">*</span>
                                </label>
                                <input type="number" name="attempts_allowed" id="attempts_allowed" 
                                       value="{{ old('attempts_allowed', $exam->attempts_allowed) }}" required min="0" max="10"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                                       placeholder="1">
                                <p class="mt-1 text-xs text-gray-500">0 = محاولات غير محدودة</p>
                                @error('attempts_allowed')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- الصف الثاني -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- درجة النجاح -->
                            <div>
                                <label for="passing_marks" class="block text-sm font-medium text-gray-700 mb-2">
                                    درجة النجاح (%) <span class="text-red-500">*</span>
                                </label>
                                <input type="number" name="passing_marks" id="passing_marks" 
                                       value="{{ old('passing_marks', $exam->passing_marks) }}" required min="0" max="100" step="0.1"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                                       placeholder="50">
                                @error('passing_marks')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- إجمالي الدرجات -->
                            <div>
                                <label for="total_marks" class="block text-sm font-medium text-gray-700 mb-2">
                                    إجمالي الدرجات
                                </label>
                                <input type="number" name="total_marks" id="total_marks" 
                                       value="{{ old('total_marks', $exam->total_marks) }}" min="0" step="0.1"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                                       placeholder="100">
                                <p class="mt-1 text-xs text-gray-500">سيتم حسابها تلقائياً من الأسئلة إذا تركت فارغة</p>
                                @error('total_marks')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- التوقيتات -->
                <div class="bg-white shadow-sm rounded-lg border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">توقيتات الامتحان</h3>
                    </div>
                    <div class="p-6 space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- وقت البداية -->
                            <div>
                                <label for="start_time" class="block text-sm font-medium text-gray-700 mb-2">
                                    وقت البداية
                                </label>
                                <input type="datetime-local" name="start_time" id="start_time" 
                                       value="{{ old('start_time', $exam->start_time ? $exam->start_time->format('Y-m-d\TH:i') : '') }}"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                <p class="mt-1 text-xs text-gray-500">اتركه فارغاً للإتاحة الفورية</p>
                                @error('start_time')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- وقت النهاية -->
                            <div>
                                <label for="end_time" class="block text-sm font-medium text-gray-700 mb-2">
                                    وقت النهاية
                                </label>
                                <input type="datetime-local" name="end_time" id="end_time" 
                                       value="{{ old('end_time', $exam->end_time ? $exam->end_time->format('Y-m-d\TH:i') : '') }}"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                <p class="mt-1 text-xs text-gray-500">اتركه فارغاً للإتاحة المستمرة</p>
                                @error('end_time')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- إعدادات العرض والمراجعة -->
                <div class="bg-white shadow-sm rounded-lg border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">إعدادات العرض والمراجعة</h3>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-4">
                                <!-- خلط الأسئلة -->
                                <div class="flex items-center">
                                    <input type="checkbox" name="randomize_questions" id="randomize_questions" value="1" 
                                           {{ old('randomize_questions', $exam->randomize_questions) ? 'checked' : '' }}
                                           class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                                    <label for="randomize_questions" class="mr-2 text-sm text-gray-900">
                                        خلط ترتيب الأسئلة
                                    </label>
                                </div>

                                <!-- خلط الخيارات -->
                                <div class="flex items-center">
                                    <input type="checkbox" name="randomize_options" id="randomize_options" value="1"
                                           {{ old('randomize_options', $exam->randomize_options) ? 'checked' : '' }}
                                           class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                                    <label for="randomize_options" class="mr-2 text-sm text-gray-900">
                                        خلط ترتيب خيارات الإجابة
                                    </label>
                                </div>

                                <!-- عرض النتائج فوراً -->
                                <div class="flex items-center">
                                    <input type="checkbox" name="show_results_immediately" id="show_results_immediately" value="1"
                                           {{ old('show_results_immediately', $exam->show_results_immediately) ? 'checked' : '' }}
                                           class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                                    <label for="show_results_immediately" class="mr-2 text-sm text-gray-900">
                                        عرض النتائج فور انتهاء الامتحان
                                    </label>
                                </div>

                                <!-- السماح بالمراجعة -->
                                <div class="flex items-center">
                                    <input type="checkbox" name="allow_review" id="allow_review" value="1"
                                           {{ old('allow_review', $exam->allow_review) ? 'checked' : '' }}
                                           class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                                    <label for="allow_review" class="mr-2 text-sm text-gray-900">
                                        السماح بمراجعة الأسئلة والإجابات
                                    </label>
                                </div>
                            </div>

                            <div class="space-y-4">
                                <!-- عرض الإجابات الصحيحة -->
                                <div class="flex items-center">
                                    <input type="checkbox" name="show_correct_answers" id="show_correct_answers" value="1"
                                           {{ old('show_correct_answers', $exam->show_correct_answers) ? 'checked' : '' }}
                                           class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                                    <label for="show_correct_answers" class="mr-2 text-sm text-gray-900">
                                        عرض الإجابات الصحيحة
                                    </label>
                                </div>

                                <!-- عرض التفسيرات -->
                                <div class="flex items-center">
                                    <input type="checkbox" name="show_explanations" id="show_explanations" value="1"
                                           {{ old('show_explanations', $exam->show_explanations) ? 'checked' : '' }}
                                           class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                                    <label for="show_explanations" class="mr-2 text-sm text-gray-900">
                                        عرض تفسيرات الإجابات
                                    </label>
                                </div>

                                <!-- منع تبديل التبويبات -->
                                <div class="flex items-center">
                                    <input type="checkbox" name="prevent_tab_switch" id="prevent_tab_switch" value="1"
                                           {{ old('prevent_tab_switch', $exam->prevent_tab_switch) ? 'checked' : '' }}
                                           class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                                    <label for="prevent_tab_switch" class="mr-2 text-sm text-gray-900">
                                        منع تبديل التبويبات أثناء الامتحان
                                    </label>
                                </div>

                                <!-- التسليم التلقائي -->
                                <div class="flex items-center">
                                    <input type="checkbox" name="auto_submit" id="auto_submit" value="1"
                                           {{ old('auto_submit', $exam->auto_submit) ? 'checked' : '' }}
                                           class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                                    <label for="auto_submit" class="mr-2 text-sm text-gray-900">
                                        تسليم تلقائي عند انتهاء الوقت
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- إعدادات الأمان -->
                <div class="bg-white shadow-sm rounded-lg border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">إعدادات الأمان</h3>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-4">
                                <!-- متطلبات الكاميرا -->
                                <div class="flex items-center">
                                    <input type="checkbox" name="require_camera" id="require_camera" value="1"
                                           {{ old('require_camera', $exam->require_camera) ? 'checked' : '' }}
                                           class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                                    <label for="require_camera" class="mr-2 text-sm text-gray-900">
                                        تتطلب تفعيل الكاميرا
                                    </label>
                                </div>
                            </div>

                            <div class="space-y-4">
                                <!-- متطلبات الميكروفون -->
                                <div class="flex items-center">
                                    <input type="checkbox" name="require_microphone" id="require_microphone" value="1"
                                           {{ old('require_microphone', $exam->require_microphone) ? 'checked' : '' }}
                                           class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                                    <label for="require_microphone" class="mr-2 text-sm text-gray-900">
                                        تتطلب تفعيل الميكروفون
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- الشريط الجانبي -->
            <div class="space-y-6">
                <!-- حالة النشر -->
                <div class="bg-white shadow-sm rounded-lg border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">حالة الامتحان</h3>
                    </div>
                    <div class="p-6 space-y-4">
                        <!-- تفعيل الامتحان -->
                        <div class="flex items-center">
                            <input type="checkbox" name="is_active" id="is_active" value="1" 
                                   {{ old('is_active', $exam->is_active) ? 'checked' : '' }}
                                   class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                            <label for="is_active" class="mr-2 text-sm font-medium text-gray-900">
                                امتحان نشط
                            </label>
                        </div>
                        <p class="text-xs text-gray-500">الامتحانات غير النشطة لن تظهر للطلاب</p>
                    </div>
                </div>

                <!-- معلومات إضافية -->
                <div class="bg-white shadow-sm rounded-lg border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">معلومات الامتحان</h3>
                    </div>
                    <div class="p-6 space-y-4 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">تاريخ الإنشاء:</span>
                            <span class="text-gray-900">{{ $exam->created_at->format('d/m/Y H:i') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">آخر تحديث:</span>
                            <span class="text-gray-900">{{ $exam->updated_at->format('d/m/Y H:i') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">عدد الأسئلة:</span>
                            <span class="text-gray-900">{{ $exam->examQuestions->count() }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">المحاولات:</span>
                            <span class="text-gray-900">{{ $exam->attempts->count() }}</span>
                        </div>
                    </div>
                </div>

                <!-- أزرار الحفظ -->
                <div class="bg-white shadow-sm rounded-lg border border-gray-200">
                    <div class="p-6 space-y-4">
                        <button type="submit" 
                                class="w-full bg-primary-600 hover:bg-primary-700 text-white px-6 py-3 rounded-lg font-medium transition-colors">
                            <i class="fas fa-save ml-2"></i>
                            حفظ التغييرات
                        </button>
                        
                        <a href="{{ route('admin.exams.show', $exam) }}" 
                           class="w-full bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded-lg font-medium transition-colors block text-center">
                            <i class="fas fa-times ml-2"></i>
                            إلغاء
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const courseSelect = document.getElementById('advanced_course_id');
    const lessonSelect = document.getElementById('course_lesson_id');
    
    // تحديث الدروس عند تغيير الكورس
    courseSelect.addEventListener('change', function() {
        const courseId = this.value;
        lessonSelect.innerHTML = '<option value="">جاري التحميل...</option>';
        
        if (courseId) {
            fetch(`/api/courses/${courseId}/lessons`)
                .then(response => response.json())
                .then(lessons => {
                    lessonSelect.innerHTML = '<option value="">اختر الدرس (اختياري)</option>';
                    lessons.forEach(lesson => {
                        const option = document.createElement('option');
                        option.value = lesson.id;
                        option.textContent = lesson.title;
                        lessonSelect.appendChild(option);
                    });
                })
                .catch(error => {
                    console.error('Error loading lessons:', error);
                    lessonSelect.innerHTML = '<option value="">خطأ في التحميل</option>';
                });
        } else {
            lessonSelect.innerHTML = '<option value="">اختر الدرس (اختياري)</option>';
        }
    });
});
</script>
@endpush
@endsection
