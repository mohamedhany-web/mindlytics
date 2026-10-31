@extends('layouts.admin')

@section('title', 'تحرير الامتحان')
@section('header', 'تحرير الامتحان')

@php
    $startTime = old('start_time');
    if ($startTime === null && $exam->start_time) $startTime = $exam->start_time->format('Y-m-d\TH:i');
    $endTime = old('end_time');
    if ($endTime === null && $exam->end_time) $endTime = $exam->end_time->format('Y-m-d\TH:i');
@endphp

@section('content')
<div class="w-full max-w-full px-4 py-6 space-y-6">
    <!-- الهيدر -->
    <div class="bg-gradient-to-l from-indigo-600 via-blue-600 to-cyan-500 rounded-2xl p-6 text-white shadow-lg">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="min-w-0">
                <nav class="text-sm text-white/80 mb-2">
                    <a href="{{ route('admin.dashboard') }}" class="hover:text-white">لوحة التحكم</a>
                    <span class="mx-2">/</span>
                    <a href="{{ route('admin.exams.index') }}" class="hover:text-white">الامتحانات</a>
                    <span class="mx-2">/</span>
                    <a href="{{ route('admin.exams.by-course', $exam->advanced_course_id) }}" class="hover:text-white">{{ Str::limit($exam->course->title ?? '', 30) }}</a>
                    <span class="mx-2">/</span>
                    <a href="{{ route('admin.exams.show', $exam) }}" class="hover:text-white">{{ Str::limit($exam->title, 25) }}</a>
                    <span class="mx-2">/</span>
                    <span class="text-white">تحرير</span>
                </nav>
                <h1 class="text-xl sm:text-2xl font-bold mt-1">تحرير الامتحان</h1>
                <p class="text-sm text-white/90 mt-1">{{ Str::limit($exam->title, 50) }}</p>
            </div>
            <div class="flex flex-wrap gap-2 flex-shrink-0">
                <a href="{{ route('admin.exams.show', $exam) }}" class="inline-flex items-center gap-2 bg-white/20 hover:bg-white/30 text-white px-4 py-2.5 rounded-xl font-medium transition-colors border border-white/30">
                    <i class="fas fa-eye"></i>
                    عرض
                </a>
                <a href="{{ route('admin.exams.by-course', $exam->advanced_course_id) }}" class="inline-flex items-center gap-2 bg-white/20 hover:bg-white/30 text-white px-4 py-2.5 rounded-xl font-medium transition-colors border border-white/30">
                    <i class="fas fa-arrow-right"></i>
                    رجوع لامتحانات الكورس
                </a>
            </div>
        </div>
    </div>

    <form action="{{ route('admin.exams.update', $exam) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
            <!-- المحتوى الرئيسي -->
            <div class="xl:col-span-2 space-y-6">
                <!-- معلومات أساسية -->
                <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                    <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                        <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                            <i class="fas fa-info-circle text-indigo-600"></i>
                            معلومات الامتحان
                        </h2>
                    </div>
                    <div class="p-6 space-y-6">
                        <div>
                            <label for="title" class="block text-sm font-semibold text-gray-700 mb-2">عنوان الامتحان <span class="text-red-500">*</span></label>
                            <input type="text" name="title" id="title" value="{{ old('title', $exam->title) }}" required
                                   class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                   placeholder="مثال: امتحان الوحدة الأولى">
                            @error('title')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="advanced_course_id" class="block text-sm font-semibold text-gray-700 mb-2">الكورس <span class="text-red-500">*</span></label>
                            <select name="advanced_course_id" id="advanced_course_id" required
                                    class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">اختر الكورس</option>
                                @foreach($courses as $course)
                                    <option value="{{ $course->id }}" {{ old('advanced_course_id', $exam->advanced_course_id) == $course->id ? 'selected' : '' }}>
                                        {{ $course->title }}{{ $course->academicSubject ? ' — ' . $course->academicSubject->name : '' }}
                                    </option>
                                @endforeach
                            </select>
                            @error('advanced_course_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="course_lesson_id" class="block text-sm font-semibold text-gray-700 mb-2">الدرس (اختياري)</label>
                            <select name="course_lesson_id" id="course_lesson_id"
                                    class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">لا يوجد</option>
                                @foreach($lessons as $lesson)
                                    <option value="{{ $lesson->id }}" {{ old('course_lesson_id', $exam->course_lesson_id) == $lesson->id ? 'selected' : '' }}>{{ $lesson->title }}</option>
                                @endforeach
                            </select>
                            @error('course_lesson_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="description" class="block text-sm font-semibold text-gray-700 mb-2">الوصف</label>
                            <textarea name="description" id="description" rows="3" placeholder="وصف مختصر"
                                      class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 resize-none">{{ old('description', $exam->description) }}</textarea>
                            @error('description')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="instructions" class="block text-sm font-semibold text-gray-700 mb-2">تعليمات الامتحان</label>
                            <textarea name="instructions" id="instructions" rows="4" placeholder="تعليمات للطلاب قبل البدء"
                                      class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 resize-none">{{ old('instructions', $exam->instructions) }}</textarea>
                            @error('instructions')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </div>

                <!-- إعدادات الامتحان -->
                <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                    <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                        <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                            <i class="fas fa-cog text-indigo-600"></i>
                            إعدادات الامتحان
                        </h2>
                    </div>
                    <div class="p-6 space-y-6">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <div>
                                <label for="duration_minutes" class="block text-sm font-semibold text-gray-700 mb-2">مدة الامتحان (دقيقة) <span class="text-red-500">*</span></label>
                                <input type="number" name="duration_minutes" id="duration_minutes" value="{{ old('duration_minutes', $exam->duration_minutes) }}" required min="5" max="480"
                                       class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="60">
                                @error('duration_minutes')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label for="attempts_allowed" class="block text-sm font-semibold text-gray-700 mb-2">المحاولات المسموحة <span class="text-red-500">*</span></label>
                                <input type="number" name="attempts_allowed" id="attempts_allowed" value="{{ old('attempts_allowed', $exam->attempts_allowed) }}" required min="0" max="10"
                                       class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="1">
                                <p class="mt-1 text-xs text-gray-500">0 = غير محدود</p>
                                @error('attempts_allowed')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label for="passing_marks" class="block text-sm font-semibold text-gray-700 mb-2">درجة النجاح (%) <span class="text-red-500">*</span></label>
                                <input type="number" name="passing_marks" id="passing_marks" value="{{ old('passing_marks', $exam->passing_marks) }}" required min="0" max="100" step="0.1"
                                       class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="50">
                                @error('passing_marks')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label for="total_marks" class="block text-sm font-semibold text-gray-700 mb-2">إجمالي الدرجات</label>
                                <input type="number" name="total_marks" id="total_marks" value="{{ old('total_marks', $exam->total_marks) }}" min="0" step="0.1"
                                       class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="يُحسب من الأسئلة">
                                @error('total_marks')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- توقيتات الامتحان -->
                <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                    <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                        <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                            <i class="fas fa-calendar-alt text-indigo-600"></i>
                            توقيتات الامتحان
                        </h2>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <div>
                                <label for="start_time" class="block text-sm font-semibold text-gray-700 mb-2">وقت البداية</label>
                                <input type="datetime-local" name="start_time" id="start_time" value="{{ $startTime }}"
                                       class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                <p class="mt-1 text-xs text-gray-500">فارغ = متاح فوراً</p>
                                @error('start_time')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label for="end_time" class="block text-sm font-semibold text-gray-700 mb-2">وقت النهاية</label>
                                <input type="datetime-local" name="end_time" id="end_time" value="{{ $endTime }}"
                                       class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                <p class="mt-1 text-xs text-gray-500">فارغ = متاح باستمرار</p>
                                @error('end_time')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- إعدادات العرض والمراجعة -->
                <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                    <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                        <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                            <i class="fas fa-eye text-indigo-600"></i>
                            إعدادات العرض والمراجعة
                        </h2>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-3">
                                <label class="flex items-center gap-3 p-3 rounded-xl border border-gray-200 hover:bg-gray-50 cursor-pointer">
                                    <input type="hidden" name="randomize_questions" value="0">
                                    <input type="checkbox" name="randomize_questions" value="1" {{ old('randomize_questions', $exam->randomize_questions) ? 'checked' : '' }} class="w-5 h-5 text-indigo-600 rounded focus:ring-indigo-500">
                                    <span class="text-sm font-medium text-gray-800">خلط ترتيب الأسئلة</span>
                                </label>
                                <label class="flex items-center gap-3 p-3 rounded-xl border border-gray-200 hover:bg-gray-50 cursor-pointer">
                                    <input type="hidden" name="randomize_options" value="0">
                                    <input type="checkbox" name="randomize_options" value="1" {{ old('randomize_options', $exam->randomize_options) ? 'checked' : '' }} class="w-5 h-5 text-indigo-600 rounded focus:ring-indigo-500">
                                    <span class="text-sm font-medium text-gray-800">خلط خيارات الإجابة</span>
                                </label>
                                <label class="flex items-center gap-3 p-3 rounded-xl border border-gray-200 hover:bg-gray-50 cursor-pointer">
                                    <input type="hidden" name="show_results_immediately" value="0">
                                    <input type="checkbox" name="show_results_immediately" value="1" {{ old('show_results_immediately', $exam->show_results_immediately) ? 'checked' : '' }} class="w-5 h-5 text-indigo-600 rounded focus:ring-indigo-500">
                                    <span class="text-sm font-medium text-gray-800">عرض النتائج فور الانتهاء</span>
                                </label>
                                <label class="flex items-center gap-3 p-3 rounded-xl border border-gray-200 hover:bg-gray-50 cursor-pointer">
                                    <input type="hidden" name="allow_review" value="0">
                                    <input type="checkbox" name="allow_review" value="1" {{ old('allow_review', $exam->allow_review) ? 'checked' : '' }} class="w-5 h-5 text-indigo-600 rounded focus:ring-indigo-500">
                                    <span class="text-sm font-medium text-gray-800">السماح بمراجعة الأسئلة والإجابات</span>
                                </label>
                            </div>
                            <div class="space-y-3">
                                <label class="flex items-center gap-3 p-3 rounded-xl border border-gray-200 hover:bg-gray-50 cursor-pointer">
                                    <input type="hidden" name="show_correct_answers" value="0">
                                    <input type="checkbox" name="show_correct_answers" value="1" {{ old('show_correct_answers', $exam->show_correct_answers) ? 'checked' : '' }} class="w-5 h-5 text-indigo-600 rounded focus:ring-indigo-500">
                                    <span class="text-sm font-medium text-gray-800">عرض الإجابات الصحيحة</span>
                                </label>
                                <label class="flex items-center gap-3 p-3 rounded-xl border border-gray-200 hover:bg-gray-50 cursor-pointer">
                                    <input type="hidden" name="show_explanations" value="0">
                                    <input type="checkbox" name="show_explanations" value="1" {{ old('show_explanations', $exam->show_explanations) ? 'checked' : '' }} class="w-5 h-5 text-indigo-600 rounded focus:ring-indigo-500">
                                    <span class="text-sm font-medium text-gray-800">عرض تفسيرات الإجابات</span>
                                </label>
                                <label class="flex items-center gap-3 p-3 rounded-xl border border-gray-200 hover:bg-gray-50 cursor-pointer">
                                    <input type="hidden" name="prevent_tab_switch" value="0">
                                    <input type="checkbox" name="prevent_tab_switch" value="1" {{ old('prevent_tab_switch', $exam->prevent_tab_switch) ? 'checked' : '' }} class="w-5 h-5 text-indigo-600 rounded focus:ring-indigo-500">
                                    <span class="text-sm font-medium text-gray-800">منع تبديل التبويبات أثناء الامتحان</span>
                                </label>
                                <label class="flex items-center gap-3 p-3 rounded-xl border border-gray-200 hover:bg-gray-50 cursor-pointer">
                                    <input type="hidden" name="auto_submit" value="0">
                                    <input type="checkbox" name="auto_submit" value="1" {{ old('auto_submit', $exam->auto_submit) ? 'checked' : '' }} class="w-5 h-5 text-indigo-600 rounded focus:ring-indigo-500">
                                    <span class="text-sm font-medium text-gray-800">تسليم تلقائي عند انتهاء الوقت</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- إعدادات الأمان -->
                <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                    <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                        <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                            <i class="fas fa-shield-alt text-indigo-600"></i>
                            إعدادات الأمان
                        </h2>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <label class="flex items-center gap-3 p-3 rounded-xl border border-gray-200 hover:bg-gray-50 cursor-pointer">
                                <input type="hidden" name="require_camera" value="0">
                                <input type="checkbox" name="require_camera" value="1" {{ old('require_camera', $exam->require_camera) ? 'checked' : '' }} class="w-5 h-5 text-indigo-600 rounded focus:ring-indigo-500">
                                <span class="text-sm font-medium text-gray-800">تتطلب تفعيل الكاميرا</span>
                            </label>
                            <label class="flex items-center gap-3 p-3 rounded-xl border border-gray-200 hover:bg-gray-50 cursor-pointer">
                                <input type="hidden" name="require_microphone" value="0">
                                <input type="checkbox" name="require_microphone" value="1" {{ old('require_microphone', $exam->require_microphone) ? 'checked' : '' }} class="w-5 h-5 text-indigo-600 rounded focus:ring-indigo-500">
                                <span class="text-sm font-medium text-gray-800">تتطلب تفعيل الميكروفون</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- الشريط الجانبي -->
            <div class="space-y-6">
                <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                    <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                        <h2 class="text-lg font-bold text-gray-900">حالة الامتحان</h2>
                    </div>
                    <div class="p-6">
                        <label class="flex items-center gap-3 p-4 rounded-xl border-2 border-gray-200 hover:border-indigo-200 cursor-pointer">
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $exam->is_active) ? 'checked' : '' }} class="w-5 h-5 text-indigo-600 rounded focus:ring-indigo-500">
                            <span class="font-semibold text-gray-800">امتحان نشط</span>
                        </label>
                        <p class="text-xs text-gray-500 mt-2">غير النشط لا يظهر للطلاب</p>
                    </div>
                </div>

                <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                    <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                        <h2 class="text-lg font-bold text-gray-900">معلومات</h2>
                    </div>
                    <div class="p-6 space-y-3 text-sm">
                        <div class="flex justify-between"><span class="text-gray-500">تاريخ الإنشاء</span><span class="font-medium text-gray-800">{{ $exam->created_at->format('Y-m-d H:i') }}</span></div>
                        <div class="flex justify-between"><span class="text-gray-500">آخر تحديث</span><span class="font-medium text-gray-800">{{ $exam->updated_at->format('Y-m-d H:i') }}</span></div>
                        <div class="flex justify-between"><span class="text-gray-500">الأسئلة</span><span class="font-medium text-gray-800">{{ $exam->examQuestions->count() }}</span></div>
                        <div class="flex justify-between"><span class="text-gray-500">المحاولات</span><span class="font-medium text-gray-800">{{ $exam->attempts->count() }}</span></div>
                    </div>
                </div>

                <div class="flex flex-col gap-3">
                    <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-xl font-semibold transition-colors shadow-lg hover:shadow-xl inline-flex items-center justify-center gap-2">
                        <i class="fas fa-save"></i>
                        حفظ التغييرات
                    </button>
                    <a href="{{ route('admin.exams.by-course', $exam->advanced_course_id) }}" class="w-full text-center bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-3 rounded-xl font-semibold transition-colors">
                        إلغاء
                    </a>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    var courseSelect = document.getElementById('advanced_course_id');
    var lessonSelect = document.getElementById('course_lesson_id');
    if (!courseSelect || !lessonSelect) return;

    courseSelect.addEventListener('change', function() {
        var courseId = this.value;
        lessonSelect.innerHTML = '<option value="">جاري التحميل...</option>';

        if (!courseId) {
            lessonSelect.innerHTML = '<option value="">لا يوجد</option>';
            return;
        }

        var url = '/admin/courses/' + courseId + '/lessons-list';
        fetch(url, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
            .then(function(r) { return r.json(); })
            .then(function(data) {
                lessonSelect.innerHTML = '<option value="">لا يوجد</option>';
                var list = Array.isArray(data) ? data : (data.lessons || data.data || []);
                list.forEach(function(lesson) {
                    var opt = document.createElement('option');
                    opt.value = lesson.id;
                    opt.textContent = lesson.title;
                    lessonSelect.appendChild(opt);
                });
            })
            .catch(function() {
                lessonSelect.innerHTML = '<option value="">خطأ في التحميل</option>';
            });
    });
});
</script>
@endpush
@endsection
