@extends('layouts.admin')

@section('title', 'إدارة أسئلة الامتحان')
@section('header', 'إدارة أسئلة الامتحان')

@php
    $currentQuestionIds = $exam->examQuestions->pluck('question_id')->toArray();
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
                    <a href="{{ route('admin.exams.by-course', $exam->advanced_course_id) }}" class="hover:text-white">{{ Str::limit($exam->course?->title ?? '', 25) }}</a>
                    <span class="mx-2">/</span>
                    <a href="{{ route('admin.exams.show', $exam) }}" class="hover:text-white">{{ Str::limit($exam->title, 25) }}</a>
                    <span class="mx-2">/</span>
                    <span class="text-white">الأسئلة</span>
                </nav>
                <h1 class="text-xl sm:text-2xl font-bold mt-1">إدارة أسئلة الامتحان</h1>
                <p class="text-sm text-white/90 mt-1">{{ $exam->title }} — إجمالي الدرجات: {{ $exam->total_marks ?? 0 }}</p>
            </div>
            <div class="flex flex-wrap gap-2 flex-shrink-0">
                <a href="{{ route('admin.exams.show', $exam) }}" class="inline-flex items-center gap-2 bg-white text-indigo-600 hover:bg-gray-100 px-4 py-2.5 rounded-xl font-semibold transition-colors">
                    <i class="fas fa-eye"></i>
                    عرض الامتحان
                </a>
                <a href="{{ route('admin.exams.by-course', $exam->advanced_course_id) }}" class="inline-flex items-center gap-2 bg-white/20 hover:bg-white/30 text-white px-4 py-2.5 rounded-xl font-medium transition-colors border border-white/30">
                    <i class="fas fa-arrow-right"></i>
                    رجوع لامتحانات الكورس
                </a>
                <a href="{{ route('admin.question-bank.index') }}" class="inline-flex items-center gap-2 bg-white/20 hover:bg-white/30 text-white px-4 py-2.5 rounded-xl font-medium transition-colors border border-white/30">
                    <i class="fas fa-database"></i>
                    بنك الأسئلة
                </a>
            </div>
        </div>
    </div>

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
    @if($errors->any())
        <div class="rounded-xl bg-red-50 border border-red-200 text-red-800 px-4 py-3">
            <p class="font-semibold mb-1">يرجى تصحيح الأخطاء:</p>
            <ul class="list-disc list-inside text-sm">
                @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- أسئلة الامتحان الحالية -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200 flex flex-wrap items-center justify-between gap-2">
                    <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                        <i class="fas fa-list-ol text-indigo-600"></i>
                        أسئلة الامتحان ({{ $exam->examQuestions->count() }})
                    </h2>
                    <span class="text-sm font-semibold text-gray-600">إجمالي الدرجات: {{ $exam->total_marks ?? 0 }}</span>
                </div>
                <div class="p-6">
                    @if($exam->examQuestions->count() > 0)
                        <ul class="space-y-3">
                            @foreach($exam->examQuestions as $eq)
                                @php
                                    $q = $eq->question;
                                    $diffClass = $q && $q->difficulty_level == 'easy' ? 'bg-green-100 text-green-800' : ($q && $q->difficulty_level == 'medium' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800');
                                @endphp
                                <li class="flex flex-wrap items-start justify-between gap-3 p-4 bg-gray-50 rounded-xl border border-gray-200 hover:border-indigo-200 transition-colors">
                                    <div class="flex items-start gap-3 min-w-0 flex-1">
                                        <span class="w-9 h-9 rounded-lg bg-indigo-100 text-indigo-600 flex items-center justify-center text-sm font-bold flex-shrink-0">{{ $eq->order }}</span>
                                        <div class="min-w-0">
                                            <p class="font-medium text-gray-900">{{ Str::limit($q->question ?? '—', 120) }}</p>
                                            <div class="flex flex-wrap items-center gap-2 mt-2 text-xs">
                                                @if($q)
                                                    <span class="text-gray-500">{{ $q->getTypeLabel() }}</span>
                                                    <span class="font-semibold text-indigo-600">{{ $eq->marks }} نقطة</span>
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded {{ $diffClass }}">{{ $q->getDifficultyLabel() }}</span>
                                                    @if($eq->time_limit)
                                                        <span class="text-gray-500">{{ $eq->time_limit }} ثانية</span>
                                                    @endif
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <form action="{{ route('admin.exams.questions.remove', [$exam, $eq]) }}" method="POST" class="inline" onsubmit="return confirm('هل تريد إزالة هذا السؤال من الامتحان؟');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 rounded-lg text-gray-400 hover:text-red-600 hover:bg-red-50 transition-colors" title="إزالة من الامتحان">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <div class="text-center py-12 rounded-xl border-2 border-dashed border-gray-200 bg-gray-50">
                            <i class="fas fa-question-circle text-5xl text-gray-300 mb-4"></i>
                            <h3 class="text-lg font-bold text-gray-900 mb-2">لا توجد أسئلة في الامتحان</h3>
                            <p class="text-gray-500 mb-4">أضف أسئلة من بنك الأسئلة باستخدام النموذج على اليمين</p>
                            <a href="{{ route('admin.question-bank.index') }}" class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-xl font-semibold transition-colors">
                                <i class="fas fa-database"></i>
                                بنك الأسئلة
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- إضافة سؤال من البنك -->
        <div>
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden sticky top-4">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                    <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                        <i class="fas fa-plus-circle text-indigo-600"></i>
                        إضافة سؤال من البنك
                    </h2>
                </div>
                <div class="p-6">
                    <form action="{{ route('admin.exams.questions.add', $exam) }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label for="question_id" class="block text-sm font-semibold text-gray-700 mb-2">السؤال <span class="text-red-500">*</span></label>
                            <select name="question_id" id="question_id" required
                                    class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">اختر السؤال</option>
                                @foreach($categories as $category)
                                    @if($category->questions && $category->questions->count() > 0)
                                        <optgroup label="{{ $category->name }}">
                                            @foreach($category->questions as $q)
                                                @if(!in_array($q->id, $currentQuestionIds))
                                                    <option value="{{ $q->id }}" data-type="{{ $q->getTypeLabel() }}" data-diff="{{ $q->getDifficultyLabel() }}">
                                                        {{ Str::limit($q->question, 60) }}
                                                    </option>
                                                @endif
                                            @endforeach
                                        </optgroup>
                                    @endif
                                @endforeach
                            </select>
                            @error('question_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="marks" class="block text-sm font-semibold text-gray-700 mb-2">الدرجة (نقطة) <span class="text-red-500">*</span></label>
                            <input type="number" name="marks" id="marks" value="{{ old('marks', 1) }}" required min="0.5" max="100" step="0.5"
                                   class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="1">
                            @error('marks')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="time_limit" class="block text-sm font-semibold text-gray-700 mb-2">حد زمني (ثانية) — اختياري</label>
                            <input type="number" name="time_limit" id="time_limit" value="{{ old('time_limit') }}" min="10" max="600"
                                   class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="—">
                        </div>
                        <label class="flex items-center gap-3 p-3 rounded-xl border border-gray-200 cursor-pointer">
                            <input type="hidden" name="is_required" value="0">
                            <input type="checkbox" name="is_required" value="1" {{ old('is_required', true) ? 'checked' : '' }} class="w-5 h-5 text-indigo-600 rounded focus:ring-indigo-500">
                            <span class="text-sm font-medium text-gray-800">سؤال مطلوب</span>
                        </label>
                        <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-3 rounded-xl font-semibold transition-colors inline-flex items-center justify-center gap-2">
                            <i class="fas fa-plus"></i>
                            إضافة السؤال للامتحان
                        </button>
                    </form>
                    @if(empty($categories) || $categories->sum(fn($c) => $c->questions ? $c->questions->count() : 0) == 0)
                        <p class="text-sm text-gray-500 mt-4">لا توجد أسئلة في البنك. <a href="{{ route('admin.question-bank.index') }}" class="text-indigo-600 hover:underline">إضافة أسئلة</a></p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
