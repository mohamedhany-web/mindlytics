@extends('layouts.admin')

@section('title', 'إنشاء امتحان جديد')
@section('header', 'إنشاء امتحان جديد')

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
                <span>إنشاء امتحان جديد</span>
            </nav>
        </div>
        <a href="{{ route('admin.exams.index') }}" 
           class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg font-medium transition-colors">
            <i class="fas fa-arrow-right ml-2"></i>
            العودة
        </a>
    </div>

    <!-- نموذج إنشاء الامتحان -->
    <form action="{{ route('admin.exams.store') }}" method="POST">
        @csrf
        
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
                            <input type="text" name="title" id="title" value="{{ old('title') }}" required
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                                   placeholder="مثال: امتحان الوحدة الأولى - الرياضيات">
                            @error('title')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- الكورس والدرس -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="advanced_course_id" class="block text-sm font-medium text-gray-700 mb-2">
                                    الكورس <span class="text-red-500">*</span>
                                </label>
                                <select name="advanced_course_id" id="advanced_course_id" required onchange="loadLessons()"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                    <option value="">اختر الكورس</option>
                                    @foreach($courses as $course)
                                        <option value="{{ $course->id }}" {{ (old('advanced_course_id', $selectedCourse) == $course->id) ? 'selected' : '' }}>
                                            {{ $course->title }} - {{ $course->academicSubject->name ?? 'غير محدد' }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('advanced_course_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="course_lesson_id" class="block text-sm font-medium text-gray-700 mb-2">
                                    الدرس (اختياري)
                                </label>
                                <select name="course_lesson_id" id="course_lesson_id"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                    <option value="">امتحان عام للكورس</option>
                                    @foreach($lessons as $lesson)
                                        <option value="{{ $lesson->id }}" {{ old('course_lesson_id') == $lesson->id ? 'selected' : '' }}>
                                            {{ $lesson->title }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- الوصف -->
                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                                وصف الامتحان
                            </label>
                            <textarea name="description" id="description" rows="3"
                                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                                      placeholder="وصف مختصر عن الامتحان ومحتواه...">{{ old('description') }}</textarea>
                        </div>

                        <!-- التعليمات -->
                        <div>
                            <label for="instructions" class="block text-sm font-medium text-gray-700 mb-2">
                                تعليمات الامتحان
                            </label>
                            <textarea name="instructions" id="instructions" rows="4"
                                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                                      placeholder="اكتب التعليمات التي ستظهر للطالب قبل بدء الامتحان...">{{ old('instructions') }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- إعدادات التوقيت والدرجات -->
                <div class="bg-white shadow-sm rounded-lg border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">إعدادات التوقيت والدرجات</h3>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <!-- مدة الامتحان -->
                            <div>
                                <label for="duration_minutes" class="block text-sm font-medium text-gray-700 mb-2">
                                    مدة الامتحان (دقيقة) <span class="text-red-500">*</span>
                                </label>
                                <input type="number" name="duration_minutes" id="duration_minutes" 
                                       value="{{ old('duration_minutes', 60) }}" min="5" max="480" required
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                            </div>

                            <!-- عدد المحاولات -->
                            <div>
                                <label for="attempts_allowed" class="block text-sm font-medium text-gray-700 mb-2">
                                    عدد المحاولات المسموحة <span class="text-red-500">*</span>
                                </label>
                                <input type="number" name="attempts_allowed" id="attempts_allowed" 
                                       value="{{ old('attempts_allowed', 1) }}" min="0" max="10" required
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                <p class="mt-1 text-sm text-gray-500">0 = محاولات غير محدودة</p>
                            </div>

                            <!-- درجة النجاح -->
                            <div>
                                <label for="passing_marks" class="block text-sm font-medium text-gray-700 mb-2">
                                    درجة النجاح (%) <span class="text-red-500">*</span>
                                </label>
                                <input type="number" name="passing_marks" id="passing_marks" 
                                       value="{{ old('passing_marks', 60) }}" min="0" max="100" step="0.5" required
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                            </div>
                        </div>

                        <!-- تواريخ الامتحان -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                            <div>
                                <label for="start_time" class="block text-sm font-medium text-gray-700 mb-2">
                                    تاريخ بداية الامتحان
                                </label>
                                <input type="datetime-local" name="start_time" id="start_time" 
                                       value="{{ old('start_time') }}"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                <p class="mt-1 text-sm text-gray-500">اتركه فارغاً إذا كان متاحاً دائماً</p>
                            </div>

                            <div>
                                <label for="end_time" class="block text-sm font-medium text-gray-700 mb-2">
                                    تاريخ انتهاء الامتحان
                                </label>
                                <input type="datetime-local" name="end_time" id="end_time" 
                                       value="{{ old('end_time') }}"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                <p class="mt-1 text-sm text-gray-500">اتركه فارغاً إذا كان متاحاً دائماً</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- إعدادات العرض والأمان -->
                <div class="bg-white shadow-sm rounded-lg border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">إعدادات العرض والأمان</h3>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- إعدادات العرض -->
                            <div class="space-y-4">
                                <h4 class="font-medium text-gray-900">إعدادات العرض</h4>
                                
                                <label class="flex items-center">
                                    <input type="checkbox" name="randomize_questions" value="1" 
                                           {{ old('randomize_questions') ? 'checked' : '' }}
                                           class="w-4 h-4 text-primary-600 bg-gray-100 border-gray-300 rounded focus:ring-primary-500">
                                    <span class="mr-2 text-sm text-gray-700">خلط ترتيب الأسئلة</span>
                                </label>
                                
                                <label class="flex items-center">
                                    <input type="checkbox" name="randomize_options" value="1" 
                                           {{ old('randomize_options') ? 'checked' : '' }}
                                           class="w-4 h-4 text-primary-600 bg-gray-100 border-gray-300 rounded focus:ring-primary-500">
                                    <span class="mr-2 text-sm text-gray-700">خلط ترتيب الخيارات</span>
                                </label>
                                
                                <label class="flex items-center">
                                    <input type="checkbox" name="show_results_immediately" value="1" 
                                           {{ old('show_results_immediately', true) ? 'checked' : '' }}
                                           class="w-4 h-4 text-primary-600 bg-gray-100 border-gray-300 rounded focus:ring-primary-500">
                                    <span class="mr-2 text-sm text-gray-700">عرض النتيجة فوراً</span>
                                </label>
                                
                                <label class="flex items-center">
                                    <input type="checkbox" name="show_correct_answers" value="1" 
                                           {{ old('show_correct_answers') ? 'checked' : '' }}
                                           class="w-4 h-4 text-primary-600 bg-gray-100 border-gray-300 rounded focus:ring-primary-500">
                                    <span class="mr-2 text-sm text-gray-700">عرض الإجابات الصحيحة</span>
                                </label>
                                
                                <label class="flex items-center">
                                    <input type="checkbox" name="show_explanations" value="1" 
                                           {{ old('show_explanations') ? 'checked' : '' }}
                                           class="w-4 h-4 text-primary-600 bg-gray-100 border-gray-300 rounded focus:ring-primary-500">
                                    <span class="mr-2 text-sm text-gray-700">عرض شرح الإجابات</span>
                                </label>
                                
                                <label class="flex items-center">
                                    <input type="checkbox" name="allow_review" value="1" 
                                           {{ old('allow_review', true) ? 'checked' : '' }}
                                           class="w-4 h-4 text-primary-600 bg-gray-100 border-gray-300 rounded focus:ring-primary-500">
                                    <span class="mr-2 text-sm text-gray-700">السماح بمراجعة الإجابات</span>
                                </label>
                            </div>

                            <!-- إعدادات الأمان -->
                            <div class="space-y-4">
                                <h4 class="font-medium text-gray-900">إعدادات الأمان</h4>
                                
                                <label class="flex items-center">
                                    <input type="checkbox" name="prevent_tab_switch" value="1" 
                                           {{ old('prevent_tab_switch', true) ? 'checked' : '' }}
                                           class="w-4 h-4 text-primary-600 bg-gray-100 border-gray-300 rounded focus:ring-primary-500">
                                    <span class="mr-2 text-sm text-gray-700">منع تبديل التبويبات</span>
                                </label>
                                
                                <label class="flex items-center">
                                    <input type="checkbox" name="auto_submit" value="1" 
                                           {{ old('auto_submit', true) ? 'checked' : '' }}
                                           class="w-4 h-4 text-primary-600 bg-gray-100 border-gray-300 rounded focus:ring-primary-500">
                                    <span class="mr-2 text-sm text-gray-700">تسليم تلقائي عند انتهاء الوقت</span>
                                </label>
                                
                                <label class="flex items-center">
                                    <input type="checkbox" name="require_camera" value="1" 
                                           {{ old('require_camera') ? 'checked' : '' }}
                                           class="w-4 h-4 text-primary-600 bg-gray-100 border-gray-300 rounded focus:ring-primary-500">
                                    <span class="mr-2 text-sm text-gray-700">تتطلب تفعيل الكاميرا</span>
                                </label>
                                
                                <label class="flex items-center">
                                    <input type="checkbox" name="require_microphone" value="1" 
                                           {{ old('require_microphone') ? 'checked' : '' }}
                                           class="w-4 h-4 text-primary-600 bg-gray-100 border-gray-300 rounded focus:ring-primary-500">
                                    <span class="mr-2 text-sm text-gray-700">تتطلب تفعيل المايكروفون</span>
                                </label>
                                
                                <label class="flex items-center">
                                    <input type="checkbox" name="is_active" value="1" 
                                           {{ old('is_active', true) ? 'checked' : '' }}
                                           class="w-4 h-4 text-primary-600 bg-gray-100 border-gray-300 rounded focus:ring-primary-500">
                                    <span class="mr-2 text-sm text-gray-700">امتحان نشط</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- الشريط الجانبي -->
            <div class="space-y-6">
                <!-- معلومات سريعة -->
                <div class="bg-white shadow-sm rounded-lg border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">معلومات سريعة</h3>
                    </div>
                    <div class="p-6 space-y-4">
                        <div class="bg-blue-50 p-4 rounded-lg">
                            <div class="flex items-center">
                                <i class="fas fa-info-circle text-blue-600 ml-2"></i>
                                <span class="text-sm font-medium text-blue-800">نصائح</span>
                            </div>
                            <ul class="mt-2 text-sm text-blue-700 space-y-1">
                                <li>• بعد الإنشاء ستتمكن من إضافة الأسئلة</li>
                                <li>• يمكن اختيار أسئلة من البنك أو إنشاء جديدة</li>
                                <li>• تأكد من اختبار الامتحان قبل النشر</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- أزرار الحفظ -->
                <div class="bg-white shadow-sm rounded-lg border border-gray-200">
                    <div class="p-6">
                        <div class="space-y-3">
                            <button type="submit" 
                                    class="w-full bg-primary-600 hover:bg-primary-700 text-white py-3 px-4 rounded-lg font-medium transition-colors">
                                <i class="fas fa-save ml-2"></i>
                                إنشاء الامتحان
                            </button>
                            
                            <a href="{{ route('admin.exams.index') }}" 
                               class="w-full bg-gray-300 hover:bg-gray-400 text-gray-700 py-3 px-4 rounded-lg font-medium transition-colors block text-center">
                                إلغاء
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
function loadLessons() {
    const courseId = document.getElementById('advanced_course_id').value;
    const lessonSelect = document.getElementById('course_lesson_id');
    
    // مسح الخيارات الحالية
    lessonSelect.innerHTML = '<option value="">امتحان عام للكورس</option>';
    
    if (courseId) {
        fetch(`/admin/courses/${courseId}/lessons-list`)
            .then(response => response.json())
            .then(lessons => {
                lessons.forEach(lesson => {
                    const option = document.createElement('option');
                    option.value = lesson.id;
                    option.textContent = lesson.title;
                    lessonSelect.appendChild(option);
                });
            })
            .catch(error => {
                console.error('Error loading lessons:', error);
            });
    }
}

// تحميل الدروس عند تحميل الصفحة إذا كان هناك كورس محدد
document.addEventListener('DOMContentLoaded', function() {
    const courseId = document.getElementById('advanced_course_id').value;
    if (courseId) {
        loadLessons();
    }
});
</script>
@endpush
@endsection
