@extends('layouts.admin')

@section('title', 'إصدار شهادة جديدة')
@section('header', 'إصدار شهادة جديدة')

@section('content')
@php
    $studentsJson = $studentPayload ?? collect();
    $coursesJson = $coursePayload ?? collect();
@endphp
<div class="space-y-6" x-data="certificateIssueForm(@js($studentsJson), @js($coursesJson))" @click.outside="studentOpen = false">
    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
        <h1 class="text-2xl font-bold text-gray-900 mb-2">إصدار شهادة من النظام</h1>
        <p class="text-sm text-slate-500 mb-6">ابحث بالإيميل أو الاسم، اختَر الكورس — باقي البيانات والسيريال والختم يتملّوا تلقائيًا.</p>

        <form action="{{ route('admin.certificates.store') }}" method="POST" class="space-y-6">
            @csrf
            <input type="hidden" name="user_id" :value="userId" required>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="relative">
                    <label class="block text-sm font-medium text-gray-700 mb-2">الطالب * — بحث بالإيميل</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 start-0 flex items-center ps-3 text-slate-400 pointer-events-none">
                            <i class="fas fa-envelope"></i>
                        </span>
                        <input type="search"
                               x-model="studentQuery"
                               @focus="studentOpen = true"
                               @input="studentOpen = true"
                               @keydown.escape="studentOpen = false"
                               @keydown.enter.prevent="pickFirstFiltered()"
                               placeholder="اكتب الإيميل أو جزء منه… أو الاسم / الهاتف"
                               autocomplete="off"
                               class="w-full ps-10 pe-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm"
                               dir="ltr">
                    </div>

                    <div x-show="studentOpen && filteredStudents.length > 0"
                         x-cloak
                         class="absolute z-30 mt-1 w-full max-h-64 overflow-auto rounded-xl border border-slate-200 bg-white shadow-xl">
                        <template x-for="s in filteredStudents" :key="s.id">
                            <button type="button"
                                    @click="selectStudent(s)"
                                    class="w-full text-start px-4 py-3 hover:bg-sky-50 border-b border-slate-100 last:border-0"
                                    :class="String(userId) === String(s.id) ? 'bg-sky-50' : ''">
                                <p class="font-bold text-slate-900 text-sm" x-text="s.name"></p>
                                <p class="text-xs text-sky-700 font-mono mt-0.5" dir="ltr" x-text="s.email"></p>
                                <p class="text-[11px] text-slate-400 mt-0.5" x-show="s.phone" x-text="s.phone"></p>
                            </button>
                        </template>
                    </div>

                    <div x-show="studentOpen && studentQuery.trim() !== '' && filteredStudents.length === 0"
                         x-cloak
                         class="absolute z-30 mt-1 w-full rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900 shadow">
                        لا يوجد طالب بهذا الإيميل / البحث.
                    </div>

                    <div x-show="selectedStudent" x-cloak class="mt-2 rounded-lg bg-slate-50 border border-slate-200 px-3 py-2 text-xs text-slate-700 flex flex-wrap items-center justify-between gap-2">
                        <div>
                            <span class="font-bold" x-text="selectedStudent?.name"></span>
                            <span class="text-slate-400"> · </span>
                            <span class="font-mono text-sky-700" dir="ltr" x-text="selectedStudent?.email"></span>
                        </div>
                        <button type="button" @click="clearStudent()" class="text-rose-600 font-semibold hover:underline">تغيير</button>
                    </div>
                    <p class="text-xs text-rose-600 mt-1" x-show="submitted && !userId" x-cloak>يجب اختيار طالب.</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">الكورس *</label>
                    <select name="course_id" x-model="courseId" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                        <option value="">اختر الكورس</option>
                        @foreach($courses as $course)
                            <option value="{{ $course->id }}" @selected((string) old('course_id') === (string) $course->id)>
                                {{ $course->title }}
                            </option>
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
                <button type="submit"
                        @click="submitted = true"
                        :disabled="!userId || !courseId"
                        class="bg-gradient-to-r from-emerald-600 to-teal-700 hover:from-emerald-700 hover:to-teal-800 disabled:opacity-50 disabled:cursor-not-allowed text-white px-6 py-3 rounded-lg font-bold transition-colors shadow-lg shadow-emerald-500/30">
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
        studentQuery: '',
        studentOpen: false,
        submitted: false,
        get selectedStudent() {
            return this.students.find(s => String(s.id) === String(this.userId)) || null;
        },
        get selectedCourse() {
            return this.courses.find(c => String(c.id) === String(this.courseId)) || null;
        },
        get filteredStudents() {
            const q = (this.studentQuery || '').trim().toLowerCase();
            if (!q) {
                return this.students.slice(0, 40);
            }
            return this.students.filter(s => {
                const email = (s.email || '').toLowerCase();
                const name = (s.name || '').toLowerCase();
                const phone = (s.phone || '').toLowerCase();
                return email.includes(q) || name.includes(q) || phone.includes(q);
            }).slice(0, 50);
        },
        selectStudent(s) {
            this.userId = s.id;
            this.studentQuery = s.email || s.name || '';
            this.studentOpen = false;
            this.autofill();
        },
        clearStudent() {
            this.userId = '';
            this.studentQuery = '';
            this.studentOpen = true;
            this.autofill();
        },
        pickFirstFiltered() {
            if (this.filteredStudents.length) {
                this.selectStudent(this.filteredStudents[0]);
            }
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
            if (this.selectedStudent) {
                this.studentQuery = this.selectedStudent.email || this.selectedStudent.name || '';
            }
            this.$watch('courseId', () => this.autofill());
            if (!this.title) this.autofill();
        }
    }
}
</script>
@endpush
@endsection
