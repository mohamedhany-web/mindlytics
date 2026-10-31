@extends('layouts.admin')

@section('title', 'امتحانات: ' . $course->title)
@section('header', 'امتحانات الكورس')

@section('content')
<div class="w-full max-w-full px-4 py-6 space-y-6">
    @if(session('success'))
        <div class="rounded-xl bg-green-50 border border-green-200 text-green-800 px-4 py-3 flex items-center gap-2">
            <i class="fas fa-check-circle text-green-600"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif
    @if(session('error'))
        <div class="rounded-xl bg-red-50 border border-red-200 text-red-800 px-4 py-3 flex items-center gap-2">
            <i class="fas fa-exclamation-circle text-red-600"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <!-- الهيدر -->
    <div class="bg-gradient-to-l from-indigo-600 via-blue-600 to-cyan-500 rounded-2xl p-6 text-white shadow-lg">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="min-w-0">
                <nav class="text-sm text-white/80 mb-2">
                    <a href="{{ route('admin.dashboard') }}" class="hover:text-white">لوحة التحكم</a>
                    <span class="mx-2">/</span>
                    <a href="{{ route('admin.exams.index') }}" class="hover:text-white">الامتحانات</a>
                    <span class="mx-2">/</span>
                    <span class="text-white">{{ Str::limit($course->title, 40) }}</span>
                </nav>
                <h1 class="text-xl sm:text-2xl font-bold mt-1">{{ $course->title }}</h1>
                <p class="text-sm text-white/90 mt-1">إدارة امتحانات هذا الكورس — عرض، إضافة، تعديل، حذف، أسئلة، إحصائيات</p>
            </div>
            <div class="flex flex-wrap gap-2 flex-shrink-0">
                <a href="{{ route('admin.exams.index') }}"
                   class="inline-flex items-center gap-2 bg-white/20 hover:bg-white/30 text-white px-4 py-2.5 rounded-xl font-medium transition-colors border border-white/30">
                    <i class="fas fa-arrow-right"></i>
                    كل الكورسات
                </a>
                <a href="{{ route('admin.exams.create', ['course_id' => $course->id]) }}"
                   class="inline-flex items-center gap-2 bg-white text-indigo-600 hover:bg-gray-100 px-4 py-2.5 rounded-xl font-semibold transition-colors">
                    <i class="fas fa-plus"></i>
                    إضافة امتحان
                </a>
            </div>
        </div>
    </div>

    <!-- قائمة الامتحانات -->
    @if($exams->count() > 0)
        <div class="bg-white rounded-2xl border border-gray-200 shadow-lg overflow-hidden">
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200 flex flex-wrap items-center justify-between gap-4">
                <h4 class="text-lg font-bold text-gray-900">الامتحانات ({{ $exams->total() }})</h4>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">العنوان</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">المدة</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">الأسئلة</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">المحاولات</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">الحالة</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($exams as $exam)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="text-sm font-semibold text-gray-900">{{ $exam->title }}</div>
                                    @if($exam->description)
                                        <div class="text-xs text-gray-500 mt-0.5 line-clamp-1">{{ Str::limit($exam->description, 50) }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $exam->duration_minutes }} د</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $exam->questions_count ?? 0 }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $exam->attempts_count ?? 0 }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold {{ $exam->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $exam->is_active ? 'نشط' : 'معطل' }}
                                    </span>
                                    @if($exam->is_published)
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800 mr-1">منشور</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <a href="{{ route('admin.exams.show', $exam) }}" class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-indigo-50 text-indigo-600 hover:bg-indigo-100 transition-colors" title="عرض"><i class="fas fa-eye"></i></a>
                                        <a href="{{ route('admin.exams.questions.manage', $exam) }}" class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-green-50 text-green-600 hover:bg-green-100 transition-colors" title="الأسئلة"><i class="fas fa-question-circle"></i></a>
                                        <a href="{{ route('admin.exams.statistics', $exam) }}" class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-cyan-50 text-cyan-600 hover:bg-cyan-100 transition-colors" title="إحصائيات"><i class="fas fa-chart-bar"></i></a>
                                        <a href="{{ route('admin.exams.preview', $exam) }}" class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-teal-50 text-teal-600 hover:bg-teal-100 transition-colors" title="معاينة"><i class="fas fa-external-link-alt"></i></a>
                                        <a href="{{ route('admin.exams.edit', $exam) }}" class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-amber-50 text-amber-600 hover:bg-amber-100 transition-colors" title="تعديل"><i class="fas fa-edit"></i></a>
                                        <button type="button" onclick="toggleExamStatus({{ $exam->id }})" class="inline-flex items-center justify-center w-9 h-9 rounded-xl {{ $exam->is_active ? 'bg-red-50 text-red-600 hover:bg-red-100' : 'bg-green-50 text-green-600 hover:bg-green-100' }} transition-colors" title="{{ $exam->is_active ? 'إيقاف' : 'تفعيل' }}"><i class="fas {{ $exam->is_active ? 'fa-pause' : 'fa-play' }}"></i></button>
                                        <button type="button" onclick="toggleExamPublish({{ $exam->id }})" class="inline-flex items-center justify-center w-9 h-9 rounded-xl {{ $exam->is_published ? 'bg-yellow-50 text-yellow-600 hover:bg-yellow-100' : 'bg-purple-50 text-purple-600 hover:bg-purple-100' }} transition-colors" title="{{ $exam->is_published ? 'إلغاء النشر' : 'نشر' }}"><i class="fas fa-globe"></i></button>
                                        <form action="{{ route('admin.exams.destroy', $exam) }}" method="POST" class="inline" onsubmit="return confirm('هل أنت متأكد من حذف هذا الامتحان؟');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-red-50 text-red-600 hover:bg-red-100 transition-colors" title="حذف"><i class="fas fa-trash"></i></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 border-t border-gray-200 bg-gray-50/50">
                {{ $exams->links() }}
            </div>
        </div>
    @else
        <div class="bg-white rounded-2xl border border-gray-200 shadow-lg p-12 text-center">
            <div class="w-20 h-20 rounded-2xl bg-indigo-100 text-indigo-600 flex items-center justify-center text-4xl mx-auto mb-4">
                <i class="fas fa-clipboard-list"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-2">لا توجد امتحانات في هذا الكورس</h3>
            <p class="text-gray-500 mb-6">يمكنك إضافة أول امتحان لهذا الكورس</p>
            <a href="{{ route('admin.exams.create', ['course_id' => $course->id]) }}"
               class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-xl font-semibold transition-colors">
                <i class="fas fa-plus"></i>
                إضافة امتحان
            </a>
        </div>
    @endif
</div>

@push('scripts')
<script>
function toggleExamStatus(examId) {
    if (confirm('هل تريد تغيير حالة هذا الامتحان؟')) {
        fetch('/admin/exams/' + examId + '/toggle-status', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
        })
        .then(function(r) { return r.json(); })
        .then(function(data) { if (data.success) location.reload(); else alert('حدث خطأ'); })
        .catch(function() { alert('حدث خطأ'); });
    }
}
function toggleExamPublish(examId) {
    if (confirm('هل تريد تغيير حالة نشر هذا الامتحان؟')) {
        fetch('/admin/exams/' + examId + '/toggle-publish', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
        })
        .then(function(r) { return r.json(); })
        .then(function(data) { if (data.success) location.reload(); else alert('حدث خطأ'); })
        .catch(function() { alert('حدث خطأ'); });
    }
}
</script>
@endpush
@endsection
