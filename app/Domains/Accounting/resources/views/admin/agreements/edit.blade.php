@extends('layouts.admin')

@section('title', 'تعديل الاتفاقية - Mindlytics')
@section('header', 'تعديل الاتفاقية')

@section('content')
<div class="p-3 sm:p-4 md:p-6 space-y-4 sm:space-y-6" style="background: #f8fafc; min-height: 100vh;">
    <section class="rounded-3xl bg-white/95 backdrop-blur border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-5 py-6 sm:px-8 lg:px-12 border-b border-slate-200">
            <h2 class="text-2xl font-bold text-slate-900 flex items-center gap-3">
                <i class="fas fa-edit text-sky-600"></i>
                تعديل الاتفاقية: {{ $agreement->agreement_number }}
            </h2>
        </div>
        <form method="POST" action="{{ route('admin.agreements.update', $agreement) }}" class="px-5 py-6 sm:px-8 lg:px-12">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">المدرب <span class="text-red-500">*</span></label>
                    <select name="instructor_id" required class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 focus:ring-2 focus:ring-sky-500 focus:border-sky-400 transition-all">
                        @foreach($instructors as $instructor)
                            <option value="{{ $instructor->id }}" {{ $agreement->instructor_id == $instructor->id ? 'selected' : '' }}>{{ $instructor->name }} - {{ $instructor->phone }}</option>
                        @endforeach
                    </select>
                    @error('instructor_id')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">نوع الاتفاقية <span class="text-red-500">*</span></label>
                    <select name="type" id="type" required class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 focus:ring-2 focus:ring-sky-500 focus:border-sky-400 transition-all">
                        @php $effectiveType = ($agreement->billing_type ?? '') === 'course_percentage' ? 'course_percentage' : $agreement->type; @endphp
                        <option value="course_price" {{ $effectiveType == 'course_price' ? 'selected' : '' }}>سعر للكورس كاملاً</option>
                        <option value="hourly_rate" {{ $effectiveType == 'hourly_rate' ? 'selected' : '' }}>سعر للساعة المسجلة</option>
                        <option value="monthly_salary" {{ $effectiveType == 'monthly_salary' ? 'selected' : '' }}>راتب شهري</option>
                        <option value="course_percentage" {{ $effectiveType == 'course_percentage' ? 'selected' : '' }}>نسبة من الكورس</option>
                    </select>
                    @error('type')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>

                <div id="rate-field">
                    <label class="block text-sm font-semibold text-slate-700 mb-2">السعر/المعدل (ج.م) <span class="text-red-500">*</span></label>
                    <input type="number" name="rate" id="rate" step="0.01" min="0" value="{{ old('rate', $agreement->rate) }}" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 focus:ring-2 focus:ring-sky-500 focus:border-sky-400 transition-all" />
                    @error('rate')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>

                <div id="course-percentage-fields" class="md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-6" style="display: none;">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">الكورس الأونلاين <span class="text-red-500">*</span></label>
                        <select name="advanced_course_id" id="advanced_course_id" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 focus:ring-2 focus:ring-sky-500 focus:border-sky-400 transition-all">
                            <option value="">اختر المدرب أولاً ثم الكورس</option>
                            @foreach($advancedCourses ?? [] as $ac)
                                <option value="{{ $ac->id }}" data-instructor-id="{{ $ac->instructor_id ?? '' }}" {{ old('advanced_course_id', $agreement->advanced_course_id) == $ac->id ? 'selected' : '' }}>{{ $ac->title }}</option>
                            @endforeach
                        </select>
                        <p class="mt-1 text-xs text-slate-500">تظهر فقط الكورسات المُعيَّنة للمدرب المختار.</p>
                        @error('advanced_course_id')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">نسبة المدرب (%) <span class="text-red-500">*</span></label>
                        <input type="number" name="course_percentage" id="course_percentage" step="0.01" min="0" max="100" value="{{ old('course_percentage', $agreement->course_percentage) }}" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 focus:ring-2 focus:ring-sky-500 focus:border-sky-400 transition-all" />
                        @error('course_percentage')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">عنوان الاتفاقية <span class="text-red-500">*</span></label>
                    <input type="text" name="title" value="{{ old('title', $agreement->title) }}" required class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 focus:ring-2 focus:ring-sky-500 focus:border-sky-400 transition-all" />
                    @error('title')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">تاريخ البدء <span class="text-red-500">*</span></label>
                    <input type="date" name="start_date" value="{{ old('start_date', $agreement->start_date->format('Y-m-d')) }}" required class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 focus:ring-2 focus:ring-sky-500 focus:border-sky-400 transition-all" />
                    @error('start_date')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">تاريخ الانتهاء</label>
                    <input type="date" name="end_date" value="{{ old('end_date', $agreement->end_date ? $agreement->end_date->format('Y-m-d') : '') }}" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 focus:ring-2 focus:ring-sky-500 focus:border-sky-400 transition-all" />
                    @error('end_date')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">الحالة <span class="text-red-500">*</span></label>
                    <select name="status" required class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 focus:ring-2 focus:ring-sky-500 focus:border-sky-400 transition-all">
                        <option value="draft" {{ $agreement->status == 'draft' ? 'selected' : '' }}>مسودة</option>
                        <option value="active" {{ $agreement->status == 'active' ? 'selected' : '' }}>نشط</option>
                        <option value="suspended" {{ $agreement->status == 'suspended' ? 'selected' : '' }}>معلق</option>
                        <option value="terminated" {{ $agreement->status == 'terminated' ? 'selected' : '' }}>منتهي</option>
                        <option value="completed" {{ $agreement->status == 'completed' ? 'selected' : '' }}>مكتمل</option>
                    </select>
                    @error('status')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-slate-700 mb-2">الوصف</label>
                    <textarea name="description" rows="3" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 focus:ring-2 focus:ring-sky-500 focus:border-sky-400 transition-all">{{ old('description', $agreement->description) }}</textarea>
                    @error('description')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-slate-700 mb-2">شروط العقد</label>
                    <textarea name="terms" rows="5" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 focus:ring-2 focus:ring-sky-500 focus:border-sky-400 transition-all">{{ old('terms', $agreement->terms) }}</textarea>
                    @error('terms')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-slate-700 mb-2">ملاحظات</label>
                    <textarea name="notes" rows="3" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 focus:ring-2 focus:ring-sky-500 focus:border-sky-400 transition-all">{{ old('notes', $agreement->notes) }}</textarea>
                    @error('notes')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 mt-6 pt-6 border-t border-slate-200">
                <a href="{{ route('admin.agreements.show', $agreement) }}" class="inline-flex items-center gap-2 rounded-2xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-slate-50">
                    إلغاء
                </a>
                <button type="submit" class="inline-flex items-center gap-2 rounded-2xl bg-sky-600 px-4 py-2.5 text-sm font-semibold text-white shadow hover:bg-sky-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-500 transition-all">
                    <i class="fas fa-save"></i>
                    حفظ التغييرات
                </button>
            </div>
        </form>
    </section>
</div>
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const typeSelect = document.getElementById('type');
    const rateField = document.getElementById('rate-field');
    const rateInput = document.getElementById('rate');
    const coursePercentageBlock = document.getElementById('course-percentage-fields');
    const advancedCourseId = document.getElementById('advanced_course_id');
    const coursePercentageInput = document.getElementById('course_percentage');

    function filterCoursesByInstructor() {
        const instructorSelect = document.querySelector('select[name="instructor_id"]');
        if (!advancedCourseId || !instructorSelect) return;
        const selectedInstructor = instructorSelect.value;
        const options = advancedCourseId.querySelectorAll('option[data-instructor-id]');
        options.forEach(function(opt) {
            const show = !selectedInstructor || (opt.getAttribute('data-instructor-id') === selectedInstructor);
            opt.style.display = show ? '' : 'none';
            opt.disabled = !show;
            if (opt.value && !show) advancedCourseId.value = '';
        });
        if (advancedCourseId.value) {
            const chosen = advancedCourseId.querySelector('option:checked');
            if (chosen && chosen.disabled) advancedCourseId.value = '';
        }
    }

    function toggleTypeFields() {
        const type = typeSelect.value;
        const isPercentage = type === 'course_percentage';
        if (rateField) rateField.style.display = isPercentage ? 'none' : 'block';
        if (coursePercentageBlock) coursePercentageBlock.style.display = isPercentage ? 'grid' : 'none';
        if (rateInput) { rateInput.required = !isPercentage; }
        if (advancedCourseId) advancedCourseId.required = isPercentage;
        if (coursePercentageInput) coursePercentageInput.required = isPercentage;
        if (isPercentage) filterCoursesByInstructor();
    }
    typeSelect.addEventListener('change', toggleTypeFields);
    document.querySelector('select[name="instructor_id"]').addEventListener('change', function() {
        if (document.getElementById('type').value === 'course_percentage') filterCoursesByInstructor();
    });
    toggleTypeFields();
});
</script>
@endpush
@endsection
