@extends('layouts.admin')

@section('title', 'معاينة الامتحان')
@section('header', 'معاينة الامتحان')

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
                    <span class="text-white">المعاينة</span>
                </nav>
                <h1 class="text-xl sm:text-2xl font-bold mt-1">معاينة الامتحان</h1>
                <p class="text-sm text-white/90 mt-1">{{ $exam->title }} — {{ $exam->examQuestions->count() }} سؤال</p>
            </div>
            <div class="flex flex-wrap gap-2 flex-shrink-0">
                <a href="{{ route('admin.exams.show', $exam) }}" class="inline-flex items-center gap-2 bg-white text-indigo-600 hover:bg-gray-100 px-4 py-2.5 rounded-xl font-semibold transition-colors">
                    <i class="fas fa-arrow-right"></i>
                    العودة للامتحان
                </a>
                <a href="{{ route('admin.exams.by-course', $exam->advanced_course_id) }}" class="inline-flex items-center gap-2 bg-white/20 hover:bg-white/30 text-white px-4 py-2.5 rounded-xl font-medium transition-colors border border-white/30">
                    <i class="fas fa-list"></i>
                    امتحانات الكورس
                </a>
                <a href="{{ route('admin.exams.questions.manage', $exam) }}" class="inline-flex items-center gap-2 bg-white/20 hover:bg-white/30 text-white px-4 py-2.5 rounded-xl font-medium transition-colors border border-white/30">
                    <i class="fas fa-cog"></i>
                    إدارة الأسئلة
                </a>
            </div>
        </div>
    </div>

    <div class="max-w-4xl mx-auto space-y-6">
        <!-- معلومات الامتحان -->
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                    <i class="fas fa-info-circle text-indigo-600"></i>
                    {{ $exam->title }}
                </h2>
            </div>
            <div class="p-6 space-y-4">
                @if($exam->description)
                    <div>
                        <h3 class="text-sm font-semibold text-gray-700 mb-2">وصف الامتحان</h3>
                        <p class="text-gray-600 leading-relaxed">{!! nl2br(e($exam->description)) !!}</p>
                    </div>
                @endif

                @if($exam->instructions)
                    <div>
                        <h3 class="text-sm font-semibold text-gray-700 mb-2">تعليمات الامتحان</h3>
                        <div class="bg-indigo-50 p-4 rounded-xl border border-indigo-100">
                            <p class="text-indigo-900 leading-relaxed">{!! nl2br(e($exam->instructions)) !!}</p>
                        </div>
                    </div>
                @endif

                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="bg-gray-50 p-4 rounded-xl border border-gray-100">
                        <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider">المدة</div>
                        <div class="font-bold text-gray-900 mt-1">{{ $exam->duration_minutes }} دقيقة</div>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-xl border border-gray-100">
                        <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider">عدد الأسئلة</div>
                        <div class="font-bold text-gray-900 mt-1">{{ $exam->examQuestions->count() }} سؤال</div>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-xl border border-gray-100">
                        <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider">إجمالي الدرجات</div>
                        <div class="font-bold text-gray-900 mt-1">{{ $exam->total_marks ?? $exam->calculateTotalMarks() }}</div>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-xl border border-gray-100">
                        <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider">درجة النجاح</div>
                        <div class="font-bold text-gray-900 mt-1">{{ $exam->passing_marks }}%</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- الأسئلة -->
        @if($exam->examQuestions->count() > 0)
            <div class="space-y-6">
                @foreach($exam->examQuestions as $index => $examQuestion)
                    @php $q = $examQuestion->question; @endphp
                    @if(!$q)
                        @continue
                    @endif
                    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                        <div class="p-6">
                            <!-- رأس السؤال -->
                            <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-indigo-100 text-indigo-700 text-sm font-bold">
                                        {{ $index + 1 }}
                                    </span>
                                    @if($examQuestion->is_required)
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold bg-red-100 text-red-800">
                                            إجباري
                                        </span>
                                    @endif
                                    <span class="text-sm font-semibold text-gray-600">({{ $examQuestion->marks }} نقطة)</span>
                                </div>
                                <div class="text-xs text-gray-500 flex items-center gap-2">
                                    <span>{{ $q->getTypeLabel() }}</span>
                                    @if($q->category)
                                        <span>|</span>
                                        <span>{{ $q->category->name }}</span>
                                    @endif
                                </div>
                            </div>

                            <!-- نص السؤال -->
                            <div class="mb-4 text-gray-900 text-lg leading-relaxed question-content">
                                {!! nl2br(e($q->question)) !!}
                            </div>

                            <!-- صورة السؤال -->
                            @if($q->image_url && $q->getImageUrl())
                                <div class="mb-4">
                                    <img src="{{ $q->getImageUrl() }}" alt="صورة السؤال" class="max-w-full h-auto rounded-xl border border-gray-200 shadow-sm">
                                </div>
                            @endif

                            <!-- الخيارات -->
                            @if($q->type === 'multiple_choice' && $q->options && count($q->options) > 0)
                                <div class="space-y-2">
                                    @foreach($q->options as $optIndex => $option)
                                        <div class="flex items-center p-3 bg-gray-50 rounded-xl border border-gray-100">
                                            <span class="w-8 h-8 rounded-lg bg-white border-2 border-gray-200 flex items-center justify-center ml-3 text-sm font-bold text-gray-600">{{ chr(65 + $optIndex) }}</span>
                                            <span class="text-gray-900">{{ $option }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            @elseif($q->type === 'true_false')
                                <div class="space-y-2">
                                    <div class="flex items-center p-3 bg-gray-50 rounded-xl border border-gray-100">
                                        <span class="w-8 h-8 rounded-lg bg-white border-2 border-gray-200 flex items-center justify-center ml-3 text-sm font-bold text-gray-600">أ</span>
                                        <span class="text-gray-900">صحيح</span>
                                    </div>
                                    <div class="flex items-center p-3 bg-gray-50 rounded-xl border border-gray-100">
                                        <span class="w-8 h-8 rounded-lg bg-white border-2 border-gray-200 flex items-center justify-center ml-3 text-sm font-bold text-gray-600">ب</span>
                                        <span class="text-gray-900">خطأ</span>
                                    </div>
                                </div>
                            @elseif($q->type === 'fill_blank')
                                <div class="p-4 bg-gray-50 rounded-xl border border-dashed border-gray-200">
                                    <span class="text-sm text-gray-500">منطقة الإجابة (املأ الفراغ)</span>
                                </div>
                            @elseif(in_array($q->type, ['short_answer', 'essay']))
                                <div class="p-4 bg-gray-50 rounded-xl border border-gray-200 min-h-[120px]">
                                    <span class="text-sm text-gray-500">{{ $q->type === 'essay' ? 'منطقة الإجابة المقالية' : 'منطقة الإجابة القصيرة' }}</span>
                                </div>
                            @elseif($q->type === 'matching')
                                <div class="p-4 bg-gray-50 rounded-xl border border-gray-200">
                                    <span class="text-sm text-gray-500">سؤال مطابقة — قائمة العناصر</span>
                                </div>
                            @elseif($q->type === 'ordering')
                                <div class="p-4 bg-gray-50 rounded-xl border border-gray-200">
                                    <span class="text-sm text-gray-500">سؤال ترتيب — قائمة العناصر</span>
                                </div>
                            @endif

                            @if($examQuestion->time_limit)
                                <div class="mt-3 text-xs text-gray-500 flex items-center gap-1">
                                    <i class="fas fa-clock"></i>
                                    وقت الإجابة المخصص: {{ $examQuestion->time_limit }} ثانية
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- ملخص الامتحان -->
            <div class="bg-indigo-50 rounded-2xl p-6 border border-indigo-100">
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <div>
                        <h3 class="text-lg font-bold text-indigo-900 mb-2 flex items-center gap-2">
                            <i class="fas fa-file-alt text-indigo-600"></i>
                            ملخص الامتحان
                        </h3>
                        <ul class="text-indigo-800 text-sm space-y-1">
                            <li>إجمالي الأسئلة: {{ $exam->examQuestions->count() }}</li>
                            <li>إجمالي الدرجات: {{ $exam->total_marks ?? $exam->calculateTotalMarks() }}</li>
                            <li>الأسئلة الإجبارية: {{ $exam->examQuestions->where('is_required', true)->count() }}</li>
                            <li>الأسئلة الاختيارية: {{ $exam->examQuestions->where('is_required', false)->count() }}</li>
                        </ul>
                    </div>
                    <div class="text-indigo-400">
                        <i class="fas fa-clipboard-check text-4xl"></i>
                    </div>
                </div>
            </div>
        @else
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-8 text-center">
                <div class="w-20 h-20 rounded-2xl bg-gray-100 flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-question-circle text-4xl text-gray-400"></i>
                </div>
                <h3 class="text-lg font-bold text-gray-900 mb-2">لا توجد أسئلة في الامتحان</h3>
                <p class="text-gray-600 mb-6">أضف أسئلة من صفحة إدارة الأسئلة ثم عاين الامتحان مرة أخرى.</p>
                <a href="{{ route('admin.exams.questions.manage', $exam) }}" class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-xl font-semibold transition-colors">
                    <i class="fas fa-plus"></i>
                    إدارة الأسئلة
                </a>
            </div>
        @endif
    </div>
</div>
@endsection

@push('styles')
<style>
.question-content {
    font-family: 'IBM Plex Sans Arabic', sans-serif;
    line-height: 1.8;
}
</style>
@endpush
