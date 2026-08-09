@extends('layouts.admin')

@section('title', 'إصدار شهادة جديدة')
@section('header', 'إصدار شهادة جديدة')

@section('content')
@php
    $studentsJson = $studentPayload ?? collect();
    $coursesJson = $coursePayload ?? collect();
@endphp
<div class="space-y-6" x-data="certificateIssueForm(@js($studentsJson), @js($coursesJson))">
    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
        <h1 class="text-2xl font-bold text-gray-900 mb-2">إصدار شهادة من النظام</h1>
        <p class="text-sm text-slate-500 mb-6">اختَر الطالب والكورس — العنوان والوصف والمدرب والسيريال والختم الإلكتروني يتملّوا تلقائيًا.</p>

        <form action="{{ route('admin.certificates.store') }}" method="POST" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">الطالب *</label>
                    <select name="user_id" x-model="userId" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                        <option value="">اختر الطالب</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }} — {{ $user->phone ?: $user->email }}</option>
                        @endforeach
                    </select>
                    <p class="text-xs text-slate-500 mt-1" x-show="selectedStudent" x-cloak>
                        <span x-text="selectedStudent?.email"></span>
                        <span x-show="selectedStudent?.phone"> · </span>
                        <span x-text="selectedStudent?.phone"></span>
                    </p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">الكورس *</label>
                    <select name="course_id" x-model="courseId" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                        <option value="">اختر الكورس</option>
                        @foreach($courses as $course)
                            <option value="{{ $course->id }}">{{ $course->title }}</option>
                        @endforeach
                    </select>
                    <p class="text-xs text-slate-500 mt-1" x-show="selectedCourse?.instructor" x-cloak>
                        المدرب: <span class="font-semibold" x-text="selectedCourse?.instructor"></span>
                    </p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">عنوان الشهادة (تلقائي)</label>
                    <input type="text" name="title" x-model="title" required
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500 bg-slate-50">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">تاريخ الإصدار</label>
                    <input type="date" name="issued_at" value="{{ old('issued_at', date('Y-m-d')) }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">الحالة *</label>
                    <select name="status" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                        <option value="issued" selected>مُصدرة فورًا</option>
                        <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>معلقة</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">تصميم الشهادة</label>
                    <select name="template" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                        @foreach($templates as $key => $tpl)
                            <option value="{{ $key }}" @selected(old('template', $branding->default_template ?: 'emerald-classic') === $key)>
                                {{ $tpl['name'] }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">الوصف (تلقائي)</label>
                <textarea name="description" x-model="description" rows="3"
                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500 bg-slate-50"></textarea>
            </div>

            <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-900">
                <p class="font-bold mb-1">هيتم تلقائيًا عند الإصدار:</p>
                <ul class="list-disc pe-5 space-y-0.5 text-emerald-800/90">
                    <li>رقم تسلسلي موثّق (Serial)</li>
                    <li>اسم الطالب على الشهادة من ملفه</li>
                    <li>اسم الكورس والمدرب من بيانات الكورس</li>
                    <li>ختم إلكتروني: <b>{{ $branding->academy_name ?: 'Mindlytics Academy' }}</b> — ضريبي <b dir="ltr">{{ $branding->tax_number ?: '774-128-949' }}</b></li>
                </ul>
            </div>

            <div class="flex gap-4">
                <button type="submit" class="bg-gradient-to-r from-emerald-600 to-teal-700 hover:from-emerald-700 hover:to-teal-800 text-white px-6 py-3 rounded-lg font-bold transition-colors shadow-lg shadow-emerald-500/30">
                    إصدار الشهادة الآن
                </button>
                <a href="{{ route('admin.certificates.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-6 py-3 rounded-lg font-medium transition-colors">
                    إلغاء
                </a>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function certificateIssueForm(students, courses) {
    return {
        students: students || [],
        courses: courses || [],
        userId: @js(old('user_id', '')),
        courseId: @js(old('course_id', '')),
        title: @js(old('title', '')),
        description: @js(old('description', '')),
        get selectedStudent() {
            return this.students.find(s => String(s.id) === String(this.userId)) || null;
        },
        get selectedCourse() {
            return this.courses.find(c => String(c.id) === String(this.courseId)) || null;
        },
        autofill() {
            const student = this.selectedStudent;
            const course = this.selectedCourse;
            if (course) {
                this.title = 'شهادة إتمام — ' + course.title;
                this.description = student
                    ? `شهادة إتمام صادرة لـ ${student.name} بعد إكمال كورس ${course.title}.`
                    : `شهادة إتمام كورس ${course.title}.`;
            } else if (student) {
                this.title = 'شهادة إتمام — ' + student.name;
                this.description = `شهادة صادرة لـ ${student.name} من أكاديمية Mindlytics.`;
            }
        },
        init() {
            this.$watch('userId', () => this.autofill());
            this.$watch('courseId', () => this.autofill());
            if (!this.title) this.autofill();
        }
    }
}
</script>
@endpush
@endsection
