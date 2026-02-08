@extends('layouts.admin')

@section('title', 'إدارة الامتحانات')
@section('header', 'إدارة الامتحانات')

@section('content')
<div class="w-full max-w-full px-4 py-6 space-y-6">
    @if(session('success'))
        <div class="rounded-xl bg-green-100 text-green-800 px-4 py-3 font-medium">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="rounded-xl bg-red-100 text-red-800 px-4 py-3 font-medium">{{ session('error') }}</div>
    @endif

    <!-- الهيدر -->
    <div class="bg-gradient-to-l from-indigo-600 via-blue-600 to-cyan-500 rounded-2xl p-6 text-white shadow-lg">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="min-w-0">
                <nav class="text-sm text-white/80 mb-2">
                    <a href="{{ route('admin.dashboard') }}" class="hover:text-white">لوحة التحكم</a>
                    <span class="mx-2">/</span>
                    <span class="text-white">الامتحانات</span>
                </nav>
                <h1 class="text-xl sm:text-2xl font-bold mt-1">إدارة الامتحانات</h1>
                <p class="text-sm text-white/90 mt-1">إنشاء وإدارة الامتحانات للكورسات</p>
            </div>
            <div class="flex flex-wrap gap-2 flex-shrink-0">
                <a href="{{ route('admin.question-bank.index') }}" class="inline-flex items-center gap-2 bg-white text-indigo-600 hover:bg-gray-100 px-4 py-2.5 rounded-xl font-semibold transition-colors">
                    <i class="fas fa-database"></i>
                    بنك الأسئلة
                </a>
                <a href="{{ route('admin.exams.create') }}" class="inline-flex items-center gap-2 bg-white text-indigo-600 hover:bg-gray-100 px-4 py-2.5 rounded-xl font-semibold transition-colors">
                    <i class="fas fa-plus"></i>
                    إنشاء امتحان جديد
                </a>
            </div>
        </div>
    </div>

    <!-- إحصائيات -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl border border-gray-200 shadow-lg p-5">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-xl bg-indigo-100 flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-clipboard-list text-xl text-indigo-600"></i>
                </div>
                <div><p class="text-2xl font-bold text-gray-900">{{ $stats['total'] }}</p><p class="text-sm text-gray-500">إجمالي الامتحانات</p></div>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-gray-200 shadow-lg p-5">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-xl bg-green-100 flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-check text-xl text-green-600"></i>
                </div>
                <div><p class="text-2xl font-bold text-gray-900">{{ $stats['active'] }}</p><p class="text-sm text-gray-500">نشط</p></div>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-gray-200 shadow-lg p-5">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-xl bg-blue-100 flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-globe text-xl text-blue-600"></i>
                </div>
                <div><p class="text-2xl font-bold text-gray-900">{{ $stats['published'] }}</p><p class="text-sm text-gray-500">منشور</p></div>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-gray-200 shadow-lg p-5">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-xl bg-cyan-100 flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-users text-xl text-cyan-600"></i>
                </div>
                <div><p class="text-2xl font-bold text-gray-900">{{ $stats['total_attempts'] }}</p><p class="text-sm text-gray-500">محاولات</p></div>
            </div>
        </div>
    </div>

    <!-- الفلاتر -->
    <div class="bg-white rounded-2xl border border-gray-200 shadow-lg p-6">
        <form method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div>
                <label for="search" class="block text-sm font-medium text-gray-700 mb-1">البحث</label>
                <input type="text" name="search" id="search" value="{{ request('search') }}"
                       placeholder="العنوان..."
                       class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
            </div>
            <div>
                <label for="course_id" class="block text-sm font-medium text-gray-700 mb-1">الكورس</label>
                <select name="course_id" id="course_id" class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                    <option value="">جميع الكورسات</option>
                    @foreach($courses as $course)
                        <option value="{{ $course->id }}" {{ request('course_id') == $course->id ? 'selected' : '' }}>
                            {{ Str::limit($course->title, 45) }}{{ $course->academicSubject ? ' - ' . $course->academicSubject->name : '' }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">الحالة</label>
                <select name="status" id="status" class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                    <option value="">الكل</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>نشط</option>
                    <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>منشور</option>
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full inline-flex items-center justify-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2.5 rounded-xl font-semibold transition-colors">
                    <i class="fas fa-search"></i>
                    بحث
                </button>
            </div>
        </form>
    </div>

    <!-- قائمة الامتحانات -->
    @if($exams->count() > 0)
        <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6">
            @foreach($exams as $exam)
                <div class="bg-white rounded-2xl border border-gray-200 shadow-lg overflow-hidden hover:shadow-xl transition-shadow">
                    <div class="px-6 py-4 border-b border-gray-200 flex flex-wrap items-center justify-between gap-2">
                        <h3 class="text-lg font-bold text-gray-900 truncate flex-1 min-w-0">{{ $exam->title }}</h3>
                        <div class="flex items-center gap-2 flex-shrink-0">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold {{ $exam->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $exam->is_active ? 'نشط' : 'معطل' }}
                            </span>
                            @if($exam->is_published)
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">منشور</span>
                            @endif
                        </div>
                    </div>

                    <div class="p-6">
                        @if($exam->description)
                            <p class="text-sm text-gray-600 mb-4">{{ Str::limit($exam->description, 100) }}</p>
                        @endif

                        <div class="space-y-2 text-sm">
                            <div class="flex items-center gap-2">
                                <i class="fas fa-graduation-cap text-gray-400 w-4"></i>
                                <span class="text-gray-600">الكورس:</span>
                                <span class="text-gray-900 truncate flex-1">{{ $exam->course->title ?? '—' }}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <i class="fas fa-book text-gray-400 w-4"></i>
                                <span class="text-gray-600">المادة:</span>
                                <span class="text-gray-900">{{ $exam->course->academicSubject->name ?? '—' }}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <i class="fas fa-clock text-gray-400 w-4"></i>
                                <span class="text-gray-600">المدة:</span>
                                <span class="text-gray-900">{{ $exam->duration_minutes }} دقيقة</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <i class="fas fa-question-circle text-gray-400 w-4"></i>
                                <span class="text-gray-600">الأسئلة:</span>
                                <span class="text-gray-900">{{ $exam->questions_count }} سؤال</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <i class="fas fa-users text-gray-400 w-4"></i>
                                <span class="text-gray-600">المحاولات:</span>
                                <span class="text-gray-900">{{ $exam->attempts_count }} محاولة</span>
                            </div>
                        </div>
                    </div>

                    <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 space-y-3">
                        <div class="flex flex-wrap items-center gap-2">
                            <a href="{{ route('admin.exams.show', $exam) }}" class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-indigo-50 text-indigo-600 hover:bg-indigo-100 transition-colors" title="عرض"><i class="fas fa-eye"></i></a>
                            <a href="{{ route('admin.exams.questions.manage', $exam) }}" class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-green-50 text-green-600 hover:bg-green-100 transition-colors" title="الأسئلة"><i class="fas fa-question-circle"></i></a>
                            <a href="{{ route('admin.exams.statistics', $exam) }}" class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-cyan-50 text-cyan-600 hover:bg-cyan-100 transition-colors" title="إحصائيات"><i class="fas fa-chart-bar"></i></a>
                            <a href="{{ route('admin.exams.preview', $exam) }}" class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-teal-50 text-teal-600 hover:bg-teal-100 transition-colors" title="معاينة"><i class="fas fa-external-link-alt"></i></a>
                            <a href="{{ route('admin.exams.edit', $exam) }}" class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-amber-50 text-amber-600 hover:bg-amber-100 transition-colors" title="تعديل"><i class="fas fa-edit"></i></a>
                        </div>
                        <div class="flex flex-wrap items-center gap-2">
                            <button type="button" onclick="toggleExamStatus({{ $exam->id }})" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium {{ $exam->is_active ? 'bg-red-100 text-red-700 hover:bg-red-200' : 'bg-green-100 text-green-700 hover:bg-green-200' }}">
                                <i class="fas {{ $exam->is_active ? 'fa-pause' : 'fa-play' }}"></i>
                                {{ $exam->is_active ? 'إيقاف' : 'تفعيل' }}
                            </button>
                            <button type="button" onclick="toggleExamPublish({{ $exam->id }})" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium {{ $exam->is_published ? 'bg-yellow-100 text-yellow-700 hover:bg-yellow-200' : 'bg-purple-100 text-purple-700 hover:bg-purple-200' }}">
                                <i class="fas fa-globe"></i>
                                {{ $exam->is_published ? 'إلغاء النشر' : 'نشر' }}
                            </button>
                            <form action="{{ route('admin.exams.destroy', $exam) }}" method="POST" class="inline" onsubmit="return confirm('هل أنت متأكد من حذف هذا الامتحان؟');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium bg-red-100 text-red-700 hover:bg-red-200" title="حذف"><i class="fas fa-trash"></i> حذف</button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="bg-white rounded-2xl border border-gray-200 shadow-lg px-6 py-4">
            {{ $exams->links() }}
        </div>
    @else
        <div class="bg-white rounded-2xl border border-gray-200 shadow-lg p-12 text-center">
            <div class="w-20 h-20 rounded-2xl bg-indigo-100 text-indigo-600 flex items-center justify-center text-4xl mx-auto mb-4">
                <i class="fas fa-clipboard-list"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-2">لا توجد امتحانات</h3>
            <p class="text-gray-500 mb-6">ابدأ بإنشاء الامتحانات للكورسات أو راجع بنك الأسئلة</p>
            <div class="flex flex-wrap items-center justify-center gap-4">
                <a href="{{ route('admin.question-bank.index') }}" class="inline-flex items-center gap-2 bg-purple-600 hover:bg-purple-700 text-white px-5 py-2.5 rounded-xl font-semibold transition-colors">
                    <i class="fas fa-database"></i>
                    بنك الأسئلة
                </a>
                <a href="{{ route('admin.exams.create') }}" class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-xl font-semibold transition-colors">
                    <i class="fas fa-plus"></i>
                    إنشاء أول امتحان
                </a>
            </div>
        </div>
    @endif
</div>

@push('scripts')
<script>
function toggleExamStatus(examId) {
    if (confirm('هل تريد تغيير حالة هذا الامتحان؟')) {
        fetch('/admin/exams/' + examId + '/toggle-status', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        })
        .then(function(r) { return r.json(); })
        .then(function(data) {
            if (data.success) location.reload();
            else alert('حدث خطأ في تغيير حالة الامتحان');
        })
        .catch(function() { alert('حدث خطأ في تغيير حالة الامتحان'); });
    }
}

function toggleExamPublish(examId) {
    if (confirm('هل تريد تغيير حالة نشر هذا الامتحان؟')) {
        fetch('/admin/exams/' + examId + '/toggle-publish', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        })
        .then(function(r) { return r.json(); })
        .then(function(data) {
            if (data.success) location.reload();
            else alert('حدث خطأ في تغيير حالة النشر');
        })
        .catch(function() { alert('حدث خطأ في تغيير حالة النشر'); });
    }
}
</script>
@endpush
@endsection
