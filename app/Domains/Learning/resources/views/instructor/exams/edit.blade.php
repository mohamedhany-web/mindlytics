@extends('layouts.app')

@section('title', 'تعديل الاختبار')
@section('header', 'تعديل الاختبار: ' . $exam->title)

@section('content')
<div class="space-y-6">
    <div class="rounded-2xl p-5 sm:p-6 bg-white border border-slate-200 shadow-sm">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">تعديل الاختبار</h1>
                <p class="text-sm text-slate-500 mt-0.5">تعديل معلومات الاختبار والإعدادات</p>
            </div>
            <a href="{{ route('instructor.exams.show', $exam) }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-semibold transition-colors">
                <i class="fas fa-arrow-right"></i> العودة
            </a>
        </div>
    </div>

    <form action="{{ route('instructor.exams.update', $exam) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
            <div class="xl:col-span-2 space-y-6">
                <div class="rounded-xl bg-white border border-slate-200 shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-slate-200 bg-slate-50">
                        <h3 class="text-lg font-bold text-slate-800">معلومات الاختبار</h3>
                    </div>
                    <div class="p-6 space-y-6">
                        <div>
                            <label for="title" class="block text-sm font-semibold text-slate-700 mb-1">عنوان الاختبار <span class="text-red-500">*</span></label>
                            <input type="text" name="title" id="title" value="{{ old('title', $exam->title) }}" required
                                   class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 text-slate-800"
                                   placeholder="مثال: اختبار الوحدة الأولى">
                            @error('title')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-1">نوع الكورس</label>
                                <div class="flex gap-4">
                                    <label class="inline-flex items-center gap-2 cursor-pointer">
                                        <input type="radio" name="course_type" value="online" {{ old('course_type', $exam->offline_course_id ? 'offline' : 'online') !== 'offline' ? 'checked' : '' }} class="course-type-radio rounded border-slate-300 text-sky-600 focus:ring-sky-500">
                                        <span>أونلاين</span>
                                    </label>
                                    <label class="inline-flex items-center gap-2 cursor-pointer">
                                        <input type="radio" name="course_type" value="offline" {{ old('course_type', $exam->offline_course_id ? 'offline' : 'online') === 'offline' ? 'checked' : '' }} class="course-type-radio rounded border-slate-300 text-sky-600 focus:ring-sky-500">
                                        <span>أوفلاين</span>
                                    </label>
                                </div>
                            </div>
                            <div id="edit-online-course-wrap" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="advanced_course_id" class="block text-sm font-semibold text-slate-700 mb-1">الكورس (أونلاين)</label>
                                    <select name="advanced_course_id" id="advanced_course_id" onchange="loadLessons()"
                                            class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 text-slate-800">
                                        <option value="">اختر الكورس</option>
                                        @foreach($courses as $course)
                                            <option value="{{ $course->id }}" {{ old('advanced_course_id', $exam->advanced_course_id) == $course->id ? 'selected' : '' }}>{{ $course->title }}</option>
                                        @endforeach
                                    </select>
                                    @error('advanced_course_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                                </div>
                                <div>
                                    <label for="course_lesson_id" class="block text-sm font-semibold text-slate-700 mb-1">الدرس (اختياري)</label>
                                    <select name="course_lesson_id" id="course_lesson_id"
                                            class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 text-slate-800">
                                        <option value="">اختبار عام للكورس</option>
                                        @foreach($lessons as $lesson)
                                            <option value="{{ $lesson->id }}" {{ old('course_lesson_id', $exam->course_lesson_id) == $lesson->id ? 'selected' : '' }}>{{ $lesson->title }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div id="edit-offline-course-wrap" style="display: none;">
                                <label for="offline_course_id_edit" class="block text-sm font-semibold text-slate-700 mb-1">الكورس (أوفلاين)</label>
                                <select name="offline_course_id" id="offline_course_id_edit"
                                        class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 text-slate-800">
                                    <option value="">اختر الكورس الأوفلاين</option>
                                    @foreach($offlineCourses ?? [] as $oc)
                                        <option value="{{ $oc->id }}" {{ old('offline_course_id', $exam->offline_course_id) == $oc->id ? 'selected' : '' }}>{{ $oc->title }}</option>
                                    @endforeach
                                </select>
                                @error('offline_course_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                            </div>
                        </div>
                        <div>
                            <label for="description" class="block text-sm font-semibold text-slate-700 mb-1">وصف الاختبار</label>
                            <textarea name="description" id="description" rows="3" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 text-slate-800" placeholder="وصف مختصر...">{{ old('description', $exam->description) }}</textarea>
                        </div>
                        <div>
                            <label for="instructions" class="block text-sm font-semibold text-slate-700 mb-1">تعليمات الاختبار</label>
                            <textarea name="instructions" id="instructions" rows="4" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 text-slate-800" placeholder="تعليمات تظهر للطالب...">{{ old('instructions', $exam->instructions) }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="rounded-xl bg-white border border-slate-200 shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-slate-200 bg-slate-50">
                        <h3 class="text-lg font-bold text-slate-800">إعدادات التوقيت والدرجات</h3>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label for="duration_minutes" class="block text-sm font-semibold text-slate-700 mb-1">مدة الاختبار (دقيقة) <span class="text-red-500">*</span></label>
                                <input type="number" name="duration_minutes" id="duration_minutes" value="{{ old('duration_minutes', $exam->duration_minutes) }}" min="5" max="480" required
                                       class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 text-slate-800">
                            </div>
                            <div>
                                <label for="total_marks" class="block text-sm font-semibold text-slate-700 mb-1">الدرجة الكلية <span class="text-red-500">*</span></label>
                                <input type="number" name="total_marks" id="total_marks" value="{{ old('total_marks', $exam->total_marks) }}" min="1" required
                                       class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 text-slate-800">
                            </div>
                            <div>
                                <label for="passing_marks" class="block text-sm font-semibold text-slate-700 mb-1">درجة النجاح <span class="text-red-500">*</span></label>
                                <input type="number" name="passing_marks" id="passing_marks" value="{{ old('passing_marks', $exam->passing_marks) }}" min="0" step="0.5" required
                                       class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 text-slate-800">
                            </div>
                            <div>
                                <label for="attempts_allowed" class="block text-sm font-semibold text-slate-700 mb-1">عدد المحاولات <span class="text-red-500">*</span></label>
                                <input type="number" name="attempts_allowed" id="attempts_allowed" value="{{ old('attempts_allowed', $exam->attempts_allowed) }}" min="1" max="10" required
                                       class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 text-slate-800">
                                <p class="mt-1 text-xs text-slate-500">1–10 محاولات</p>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                            <div>
                                <label for="start_time" class="block text-sm font-semibold text-slate-700 mb-1">بداية الاختبار</label>
                                <input type="datetime-local" name="start_time" id="start_time" value="{{ old('start_time', $exam->start_time ? $exam->start_time->format('Y-m-d\TH:i') : '') }}"
                                       class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 text-slate-800">
                                <p class="mt-1 text-xs text-slate-500">فارغ = متاح دائماً</p>
                            </div>
                            <div>
                                <label for="end_time" class="block text-sm font-semibold text-slate-700 mb-1">نهاية الاختبار</label>
                                <input type="datetime-local" name="end_time" id="end_time" value="{{ old('end_time', $exam->end_time ? $exam->end_time->format('Y-m-d\TH:i') : '') }}"
                                       class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 text-slate-800">
                                <p class="mt-1 text-xs text-slate-500">فارغ = متاح دائماً</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="rounded-xl bg-white border border-slate-200 shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-slate-200 bg-slate-50">
                        <h3 class="text-lg font-bold text-slate-800">إعدادات العرض</h3>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-3">
                                <h4 class="font-semibold text-slate-800">العرض</h4>
                                @foreach(['randomize_questions' => 'خلط ترتيب الأسئلة', 'randomize_options' => 'خلط ترتيب الخيارات', 'show_results_immediately' => 'عرض النتيجة فوراً', 'show_correct_answers' => 'عرض الإجابات الصحيحة', 'show_explanations' => 'عرض شرح الإجابات', 'allow_review' => 'السماح بمراجعة الإجابات'] as $name => $label)
                                    <label class="flex items-center gap-3 cursor-pointer">
                                        <input type="checkbox" name="{{ $name }}" value="1" {{ old($name, $exam->$name) ? 'checked' : '' }}
                                               class="w-5 h-5 rounded border-slate-300 text-sky-500 focus:ring-sky-500/20">
                                        <span class="text-sm font-medium text-slate-700">{{ $label }}</span>
                                    </label>
                                @endforeach
                            </div>
                            <div class="space-y-3">
                                <h4 class="font-semibold text-slate-800">الحالة</h4>
                                <label class="flex items-center gap-3 cursor-pointer">
                                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $exam->is_active) ? 'checked' : '' }}
                                           class="w-5 h-5 rounded border-slate-300 text-sky-500 focus:ring-sky-500/20">
                                    <span class="text-sm font-medium text-slate-700">اختبار نشط</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="rounded-xl bg-white border border-slate-200 shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-slate-200 bg-slate-50">
                        <h3 class="text-lg font-bold text-slate-800">إعدادات السايدبار</h3>
                    </div>
                    <div class="p-6 space-y-4">
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="checkbox" name="show_in_sidebar" value="1" {{ old('show_in_sidebar', $exam->show_in_sidebar ?? true) ? 'checked' : '' }}
                                   class="w-5 h-5 rounded border-slate-300 text-sky-500 focus:ring-sky-500/20">
                            <span class="text-sm font-medium text-slate-700">إظهار الاختبار في السايدبار</span>
                        </label>
                        <div>
                            <label for="sidebar_position" class="block text-sm font-semibold text-slate-700 mb-1">موضع في السايدبار (1–10)</label>
                            <input type="number" name="sidebar_position" id="sidebar_position" value="{{ old('sidebar_position', $exam->sidebar_position ?? 1) }}" min="1" max="10"
                                   class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 text-slate-800">
                            <p class="mt-1 text-xs text-slate-500">الرقم الأصغر يظهر أولاً</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <div class="rounded-xl bg-white border border-slate-200 shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-slate-200 bg-slate-50">
                        <h3 class="text-lg font-bold text-slate-800">معلومات سريعة</h3>
                    </div>
                    <div class="p-6">
                        <div class="p-4 rounded-xl border border-sky-200 bg-sky-50">
                            <div class="flex items-center gap-2 mb-2">
                                <i class="fas fa-info-circle text-sky-600"></i>
                                <span class="text-sm font-semibold text-sky-800">نصائح</span>
                            </div>
                            <ul class="text-sm text-sky-700 space-y-1.5">
                                <li>• يمكنك تعديل جميع معلومات الاختبار</li>
                                <li>• احفظ التغييرات قبل الخروج</li>
                                <li>• إدارة الأسئلة من صفحة عرض الاختبار</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="rounded-xl bg-white border border-slate-200 shadow-sm overflow-hidden">
                    <div class="p-6 space-y-3">
                        <button type="submit" class="w-full px-4 py-3 bg-sky-500 hover:bg-sky-600 text-white rounded-xl font-semibold transition-colors">
                            <i class="fas fa-save ml-2"></i> حفظ التغييرات
                        </button>
                        <a href="{{ route('instructor.exams.show', $exam) }}" class="w-full px-4 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-semibold transition-colors block text-center">
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
function loadLessons() {
    var courseId = document.getElementById('advanced_course_id').value;
    var lessonSelect = document.getElementById('course_lesson_id');
    lessonSelect.innerHTML = '<option value="">اختبار عام للكورس</option>';
    if (courseId) {
        fetch('/instructor/api/courses/' + courseId + '/lessons-list')
            .then(function(r) { return r.json(); })
            .then(function(lessons) {
                lessons.forEach(function(lesson) {
                    var option = document.createElement('option');
                    option.value = lesson.id;
                    option.textContent = lesson.title;
                    @if($exam->course_lesson_id)
                    if (lesson.id == {{ $exam->course_lesson_id }}) option.selected = true;
                    @endif
                    lessonSelect.appendChild(option);
                });
            })
            .catch(function(e) { console.error('Error loading lessons:', e); });
    }
}
function toggleEditCourseType() {
    var isOffline = document.querySelector('input[name="course_type"]:checked').value === 'offline';
    var onlineWrap = document.getElementById('edit-online-course-wrap');
    var offlineWrap = document.getElementById('edit-offline-course-wrap');
    var advSelect = document.getElementById('advanced_course_id');
    var lessonSelect = document.getElementById('course_lesson_id');
    var offSelect = document.getElementById('offline_course_id_edit');
    onlineWrap.style.display = isOffline ? 'none' : 'block';
    offlineWrap.style.display = isOffline ? 'block' : 'none';
    if (isOffline) {
        advSelect.removeAttribute('required');
        advSelect.disabled = true;
        lessonSelect.disabled = true;
        offSelect.disabled = false;
    } else {
        advSelect.disabled = false;
        lessonSelect.disabled = false;
        offSelect.disabled = true;
        if (advSelect.value) advSelect.setAttribute('required', 'required');
    }
}
document.addEventListener('DOMContentLoaded', function() {
    var courseId = document.getElementById('advanced_course_id').value;
    if (courseId) loadLessons();
    document.querySelectorAll('.course-type-radio').forEach(function(r) {
        r.addEventListener('change', toggleEditCourseType);
    });
    toggleEditCourseType();
    document.querySelector('form').addEventListener('submit', function() {
        var isOffline = document.querySelector('input[name="course_type"]:checked').value === 'offline';
        document.getElementById('advanced_course_id').disabled = isOffline;
        document.getElementById('course_lesson_id').disabled = isOffline;
        document.getElementById('offline_course_id_edit').disabled = !isOffline;
    });
});
</script>
@endpush
@endsection
