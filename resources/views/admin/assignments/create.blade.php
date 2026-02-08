@extends('layouts.admin')

@section('title', 'إضافة واجب جديد')
@section('header', 'إضافة واجب جديد')

@section('content')
<div class="w-full max-w-full px-4 py-6 space-y-6">
    @if(session('success'))
        <div class="rounded-xl bg-green-100 text-green-800 px-4 py-3 font-medium">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="rounded-xl bg-red-100 text-red-800 px-4 py-3 font-medium">{{ session('error') }}</div>
    @endif

    <div class="bg-gradient-to-l from-indigo-600 via-blue-600 to-cyan-500 rounded-2xl p-6 text-white shadow-lg">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="min-w-0">
                <nav class="text-sm text-white/80 mb-2">
                    <a href="{{ route('admin.dashboard') }}" class="hover:text-white">لوحة التحكم</a>
                    <span class="mx-2">/</span>
                    <a href="{{ route('admin.assignments.index') }}" class="hover:text-white">الواجبات</a>
                    <span class="mx-2">/</span>
                    <span class="text-white">إضافة واجب</span>
                </nav>
                <h1 class="text-xl sm:text-2xl font-bold mt-1">إضافة واجب جديد</h1>
                <p class="text-sm text-white/90 mt-1">إنشاء واجب وربطه بكورس ودرس (اختياري)</p>
            </div>
            <a href="{{ route('admin.assignments.index') }}" class="inline-flex items-center gap-2 bg-white/20 hover:bg-white/30 text-white px-4 py-2.5 rounded-xl font-medium transition-colors border border-white/30">
                <i class="fas fa-arrow-right"></i>
                العودة للواجبات
            </a>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-gray-200 shadow-lg overflow-hidden">
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
            <h4 class="text-lg font-bold text-gray-900">بيانات الواجب</h4>
        </div>

        <form action="{{ route('admin.assignments.store') }}" method="POST" class="p-6 sm:p-8">
            @csrf

            <div class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">الكورس <span class="text-red-500">*</span></label>
                        <select name="advanced_course_id" id="advanced_course_id" required
                                class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                            <option value="">اختر الكورس</option>
                            @foreach($courses as $course)
                                <option value="{{ $course->id }}" {{ old('advanced_course_id') == $course->id ? 'selected' : '' }}>{{ Str::limit($course->title, 50) }}</option>
                            @endforeach
                        </select>
                        @error('advanced_course_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">الدرس (اختياري)</label>
                        <select name="lesson_id" id="lesson_id"
                                class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                            <option value="">بدون درس محدد</option>
                        </select>
                        <p class="mt-1 text-xs text-gray-500">يتم تعبئة الدروس حسب الكورس المختار</p>
                        @error('lesson_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">عنوان الواجب <span class="text-red-500">*</span></label>
                    <input type="text" name="title" value="{{ old('title') }}" required
                           class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                           placeholder="مثال: واجب البرمجة الكائنية">
                    @error('title')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">الوصف</label>
                    <textarea name="description" rows="3"
                              class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                              placeholder="وصف مختصر عن الواجب">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">التعليمات</label>
                    <textarea name="instructions" rows="4"
                              class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                              placeholder="تعليمات للطلاب">{{ old('instructions') }}</textarea>
                    @error('instructions')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">تاريخ الاستحقاق</label>
                        <input type="datetime-local" name="due_date" value="{{ old('due_date') }}"
                               class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                        @error('due_date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">الدرجة الكلية <span class="text-red-500">*</span></label>
                        <input type="number" name="max_score" value="{{ old('max_score', 100) }}" min="1" max="1000" required
                               class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                        @error('max_score')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <input type="checkbox" name="allow_late_submission" id="allow_late_submission" value="1"
                           {{ old('allow_late_submission') ? 'checked' : '' }}
                           class="w-5 h-5 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                    <label for="allow_late_submission" class="text-sm font-medium text-gray-700">السماح بالتسليم المتأخر</label>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">الحالة <span class="text-red-500">*</span></label>
                    <select name="status" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                        <option value="draft" {{ old('status', 'draft') == 'draft' ? 'selected' : '' }}>مسودة</option>
                        <option value="published" {{ old('status') == 'published' ? 'selected' : '' }}>منشور</option>
                        <option value="archived" {{ old('status') == 'archived' ? 'selected' : '' }}>مؤرشف</option>
                    </select>
                    @error('status')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex flex-wrap gap-3 mt-8 pt-6 border-t border-gray-200">
                <button type="submit" class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2.5 rounded-xl font-semibold transition-colors">
                    <i class="fas fa-save"></i>
                    إنشاء الواجب
                </button>
                <a href="{{ route('admin.assignments.index') }}" class="inline-flex items-center gap-2 border-2 border-gray-300 text-gray-700 hover:bg-gray-50 px-6 py-2.5 rounded-xl font-semibold transition-colors">
                    <i class="fas fa-times"></i>
                    إلغاء
                </a>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const courseSelect = document.getElementById('advanced_course_id');
    const lessonSelect = document.getElementById('lesson_id');
    if (!courseSelect || !lessonSelect) return;

    function clearLessonsExceptFirst() {
        while (lessonSelect.options.length > 1) {
            lessonSelect.remove(1);
        }
    }

    courseSelect.addEventListener('change', function() {
        const courseId = this.value;
        clearLessonsExceptFirst();
        if (!courseId) return;

        const opt = document.createElement('option');
        opt.value = '';
        opt.textContent = 'جاري التحميل...';
        opt.disabled = true;
        lessonSelect.appendChild(opt);
        lessonSelect.disabled = true;

        fetch('{{ url("/admin/courses") }}/' + courseId + '/lessons-list', {
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        })
            .then(function(r) { return r.ok ? r.json() : Promise.reject(); })
            .then(function(data) {
                opt.remove();
                const lessons = Array.isArray(data) ? data : (data.lessons || data.data || []);
                lessons.forEach(function(lesson) {
                    const o = document.createElement('option');
                    o.value = lesson.id;
                    o.textContent = lesson.title || ('درس ' + (lesson.order || ''));
                    lessonSelect.appendChild(o);
                });
                lessonSelect.disabled = false;
            })
            .catch(function() {
                opt.remove();
                const err = document.createElement('option');
                err.value = '';
                err.textContent = 'حدث خطأ';
                err.disabled = true;
                lessonSelect.appendChild(err);
                lessonSelect.disabled = false;
            });
    });

    @if(old('advanced_course_id'))
        courseSelect.dispatchEvent(new Event('change'));
        setTimeout(function() {
            lessonSelect.value = '{{ old("lesson_id") }}';
        }, 500);
    @endif
});
</script>
@endsection
