@extends('layouts.admin')

@section('title', 'تسليمات الواجب: ' . $assignment->title)
@section('header', 'تسليمات الواجب')

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
                    <a href="{{ route('admin.assignments.show', $assignment) }}" class="hover:text-white truncate">{{ Str::limit($assignment->title, 25) }}</a>
                    <span class="mx-2">/</span>
                    <span class="text-white">التسليمات</span>
                </nav>
                <h1 class="text-xl sm:text-2xl font-bold mt-1">تسليمات الواجب</h1>
                <p class="text-sm text-white/90 mt-1 truncate">{{ $assignment->title }}</p>
            </div>
            <div class="flex flex-wrap gap-2 flex-shrink-0">
                <a href="{{ route('admin.assignments.show', $assignment) }}" class="inline-flex items-center gap-2 bg-white text-indigo-600 hover:bg-gray-100 px-4 py-2.5 rounded-xl font-semibold transition-colors">
                    <i class="fas fa-eye"></i>
                    تفاصيل الواجب
                </a>
                <a href="{{ route('admin.assignments.index') }}" class="inline-flex items-center gap-2 bg-white/20 hover:bg-white/30 text-white px-4 py-2.5 rounded-xl font-medium transition-colors border border-white/30">
                    <i class="fas fa-arrow-right"></i>
                    العودة للواجبات
                </a>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-gray-200 shadow-lg p-6">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div class="flex items-center gap-4 min-w-0">
                <div class="w-14 h-14 bg-indigo-100 rounded-xl flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-tasks text-2xl text-indigo-600"></i>
                </div>
                <div class="min-w-0">
                    <h3 class="text-lg font-bold text-gray-900 truncate">{{ $assignment->title }}</h3>
                    <p class="text-sm text-gray-500">{{ $assignment->course->title ?? '—' }} · الدرجة الكلية: {{ $assignment->max_score }}</p>
                </div>
            </div>
            <div class="text-center px-4 py-2 bg-indigo-50 rounded-xl border border-indigo-100">
                <div class="text-2xl font-bold text-indigo-600">{{ $submissions->total() }}</div>
                <div class="text-sm text-gray-600 font-medium">إجمالي التسليمات</div>
            </div>
        </div>
    </div>

    @if(isset($gradeSubmission) && $gradeSubmission)
        <div class="bg-white rounded-2xl border border-gray-200 shadow-lg p-6" id="grade-form-box">
            <h4 class="text-lg font-bold text-gray-900 mb-4">تقييم تسليم: {{ $gradeSubmission->student->name ?? 'طالب' }}</h4>
            <form action="{{ route('admin.assignments.grade', [$assignment, $gradeSubmission]) }}" method="POST" class="space-y-4 max-w-xl">
                @csrf
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">الدرجة (0 - {{ $assignment->max_score }}) <span class="text-red-500">*</span></label>
                    <input type="number" name="score" value="{{ old('score', $gradeSubmission->score) }}" min="0" max="{{ $assignment->max_score }}" required
                           class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                    @error('score')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">التعليق / التغذية الراجعة</label>
                    <textarea name="feedback" rows="3"
                              class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                              placeholder="تعليق للطالب">{{ old('feedback', $gradeSubmission->feedback) }}</textarea>
                    @error('feedback')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">الحالة <span class="text-red-500">*</span></label>
                    <select name="status" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                        <option value="submitted" {{ old('status', $gradeSubmission->status) == 'submitted' ? 'selected' : '' }}>مُسلّم</option>
                        <option value="graded" {{ old('status', $gradeSubmission->status) == 'graded' ? 'selected' : '' }}>مقيّم</option>
                        <option value="returned" {{ old('status', $gradeSubmission->status) == 'returned' ? 'selected' : '' }}>مُرجع للطالب</option>
                    </select>
                    @error('status')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div class="flex gap-3">
                    <button type="submit" class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-xl font-semibold transition-colors">
                        <i class="fas fa-check"></i>
                        حفظ التقييم
                    </button>
                    <a href="{{ route('admin.assignments.submissions', $assignment) }}" class="inline-flex items-center gap-2 border-2 border-gray-300 text-gray-700 hover:bg-gray-50 px-5 py-2.5 rounded-xl font-semibold transition-colors">إلغاء</a>
                </div>
            </form>
        </div>
    @endif

    <div class="bg-white rounded-2xl border border-gray-200 shadow-lg overflow-hidden">
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
            <h4 class="text-lg font-bold text-gray-900">قائمة التسليمات</h4>
        </div>
        @if($submissions->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase">الطالب</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase">تاريخ التسليم</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase">المحتوى</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase">الدرجة</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase">الحالة</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($submissions as $sub)
                            @php
                                $subStatusClass = $sub->status == 'graded' || $sub->status == 'returned' ? 'bg-green-100 text-green-800' : 'bg-amber-100 text-amber-800';
                                $subStatusText = $sub->status == 'returned' ? 'مُرجع' : ($sub->status == 'graded' ? 'مقيّم' : 'مُسلّم');
                            @endphp
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $sub->student->name ?? '—' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $sub->submitted_at ? $sub->submitted_at->format('Y-m-d H:i') : '—' }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600 max-w-[200px] truncate" title="{{ strip_tags($sub->content ?? '') }}">{{ Str::limit(strip_tags($sub->content ?? ''), 50) ?: '—' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $sub->score !== null ? $sub->score . ' / ' . $assignment->max_score : '—' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium {{ $subStatusClass }}">{{ $subStatusText }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <a href="{{ route('admin.assignments.submissions', ['assignment' => $assignment, 'grade' => $sub->id]) }}#grade-form-box"
                                       class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-indigo-50 text-indigo-600 hover:bg-indigo-100 transition-colors"
                                       title="تقييم">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 border-t border-gray-200 bg-gray-50/50">
                {{ $submissions->withQueryString()->links() }}
            </div>
        @else
            <div class="p-12 text-center text-gray-500">
                <div class="w-20 h-20 rounded-2xl bg-gray-100 text-gray-400 flex items-center justify-center text-4xl mx-auto mb-4">
                    <i class="fas fa-inbox"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">لا توجد تسليمات</h3>
                <p class="mb-6">لم يسلّم أي طالب هذا الواجب بعد</p>
                <a href="{{ route('admin.assignments.show', $assignment) }}" class="inline-flex items-center gap-2 text-indigo-600 font-semibold hover:underline">
                    <i class="fas fa-arrow-right"></i>
                    العودة لتفاصيل الواجب
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
