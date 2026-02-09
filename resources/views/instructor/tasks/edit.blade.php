@extends('layouts.app')

@section('title', 'تعديل المهمة - ' . $task->title)
@section('header', 'تعديل المهمة')

@section('content')
<div class="space-y-6">
    <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-5 sm:p-6">
        <nav class="text-sm text-slate-500 mb-4">
            <a href="{{ route('instructor.tasks.index') }}" class="hover:text-sky-600">المهام من الإدارة</a>
            <span class="mx-2">/</span>
            <a href="{{ route('instructor.tasks.show', $task) }}" class="hover:text-sky-600">{{ $task->title }}</a>
            <span class="mx-2">/</span>
            <span class="text-slate-700 font-semibold">تعديل</span>
        </nav>
        <h1 class="text-xl font-bold text-slate-800">تعديل المهمة</h1>
        <p class="text-sm text-slate-500 mt-0.5">تحديث الحالة أو التفاصيل</p>
    </div>

    <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-5 sm:p-6">
        <form action="{{ route('instructor.tasks.update', $task) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">عنوان المهمة *</label>
                <input type="text" name="title" value="{{ old('title', $task->title) }}" required
                       class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-slate-800 focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 transition-colors">
                @error('title')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">الوصف</label>
                <textarea name="description" rows="4"
                          class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-slate-800 focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 transition-colors">{{ old('description', $task->description) }}</textarea>
                @error('description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">الأولوية</label>
                    <select name="priority" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-slate-800 focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 transition-colors">
                        <option value="low" {{ old('priority', $task->priority) == 'low' ? 'selected' : '' }}>منخفضة</option>
                        <option value="medium" {{ old('priority', $task->priority) == 'medium' ? 'selected' : '' }}>متوسطة</option>
                        <option value="high" {{ old('priority', $task->priority) == 'high' ? 'selected' : '' }}>عالية</option>
                        <option value="urgent" {{ old('priority', $task->priority) == 'urgent' ? 'selected' : '' }}>عاجلة</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">الحالة</label>
                    <select name="status" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-slate-800 focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 transition-colors">
                        <option value="pending" {{ old('status', $task->status) == 'pending' ? 'selected' : '' }}>معلقة</option>
                        <option value="in_progress" {{ old('status', $task->status) == 'in_progress' ? 'selected' : '' }}>قيد التنفيذ</option>
                        <option value="completed" {{ old('status', $task->status) == 'completed' ? 'selected' : '' }}>مكتملة</option>
                        <option value="cancelled" {{ old('status', $task->status) == 'cancelled' ? 'selected' : '' }}>ملغاة</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">تاريخ الاستحقاق</label>
                <input type="datetime-local" name="due_date" value="{{ old('due_date', $task->due_date ? $task->due_date->format('Y-m-d\TH:i') : '') }}"
                       class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-slate-800 focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 transition-colors">
                @error('due_date')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">الكورس (اختياري)</label>
                    <select name="related_course_id" id="related_course_id" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-slate-800 focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 transition-colors">
                        <option value="">لا يوجد</option>
                        @foreach($courses as $course)
                            <option value="{{ $course->id }}" {{ old('related_course_id', $task->related_course_id) == $course->id ? 'selected' : '' }}>{{ $course->title }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">المحاضرة (اختياري)</label>
                    <select name="related_lecture_id" id="related_lecture_id" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-slate-800 focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 transition-colors">
                        <option value="">لا يوجد</option>
                        @foreach($lectures as $lecture)
                            <option value="{{ $lecture->id }}" {{ old('related_lecture_id', $task->related_lecture_id) == $lecture->id ? 'selected' : '' }}>
                                {{ $lecture->title }} @if($lecture->scheduled_at) - {{ $lecture->scheduled_at->format('Y/m/d') }} @endif
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="flex flex-wrap gap-3 pt-4 border-t border-slate-200">
                <a href="{{ route('instructor.tasks.show', $task) }}" class="inline-flex items-center gap-2 px-5 py-2.5 border border-slate-200 bg-white text-slate-700 rounded-xl font-semibold hover:bg-slate-50 transition-colors">
                    <i class="fas fa-times"></i>
                    إلغاء
                </a>
                <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 bg-sky-500 hover:bg-sky-600 text-white rounded-xl font-semibold transition-colors">
                    <i class="fas fa-save"></i>
                    حفظ التعديلات
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('related_course_id').addEventListener('change', function() {
    const courseId = this.value;
    const lectureSelect = document.getElementById('related_lecture_id');
    lectureSelect.innerHTML = '<option value="">لا يوجد</option>';
    if (courseId) {
        fetch(`{{ route('instructor.tasks.lectures') }}?course_id=${courseId}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        })
        .then(r => r.json())
        .then(data => {
            data.forEach(lecture => {
                const opt = document.createElement('option');
                opt.value = lecture.id;
                opt.textContent = lecture.title + (lecture.scheduled_at ? ' - ' + lecture.scheduled_at : '');
                lectureSelect.appendChild(opt);
            });
        })
        .catch(() => {});
    }
});
</script>
@endpush
@endsection
