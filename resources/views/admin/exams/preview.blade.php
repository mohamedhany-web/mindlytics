@extends('layouts.admin')

@section('content')
<div class="p-6">
    <div class="mb-6">
        <div class="flex items-center justify-between mb-2">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ __('معاينة الامتحان') }}</h1>
                <p class="text-gray-600">{{ $exam->title }}</p>
            </div>
            <div class="flex space-x-2 space-x-reverse">
                <a href="{{ route('admin.exams.show', $exam) }}" 
                   class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                    <i class="fas fa-arrow-right ml-2"></i>
                    {{ __('العودة') }}
                </a>
            </div>
        </div>
    </div>

    <div class="max-w-4xl mx-auto">
        <!-- معلومات الامتحان -->
        <div class="bg-white rounded-xl shadow-sm mb-6">
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-xl font-bold text-gray-900 mb-4">{{ $exam->title }}</h2>
                
                @if($exam->description)
                    <div class="mb-4">
                        <h3 class="text-sm font-medium text-gray-700 mb-2">{{ __('وصف الامتحان') }}</h3>
                        <p class="text-gray-600">{!! nl2br(e($exam->description)) !!}</p>
                    </div>
                @endif

                @if($exam->instructions)
                    <div class="mb-4">
                        <h3 class="text-sm font-medium text-gray-700 mb-2">{{ __('تعليمات الامتحان') }}</h3>
                        <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                            <p class="text-blue-800">{!! nl2br(e($exam->instructions)) !!}</p>
                        </div>
                    </div>
                @endif

                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                    <div class="bg-gray-50 p-3 rounded-lg">
                        <div class="text-gray-500">{{ __('المدة') }}</div>
                        <div class="font-semibold text-gray-900">
                            {{ $exam->duration_minutes }} {{ __('دقيقة') }}
                        </div>
                    </div>
                    
                    <div class="bg-gray-50 p-3 rounded-lg">
                        <div class="text-gray-500">{{ __('عدد الأسئلة') }}</div>
                        <div class="font-semibold text-gray-900">
                            {{ $exam->examQuestions->count() }} {{ __('سؤال') }}
                        </div>
                    </div>
                    
                    <div class="bg-gray-50 p-3 rounded-lg">
                        <div class="text-gray-500">{{ __('إجمالي الدرجات') }}</div>
                        <div class="font-semibold text-gray-900">
                            {{ $exam->total_marks ?? $exam->calculateTotalMarks() }}
                        </div>
                    </div>
                    
                    <div class="bg-gray-50 p-3 rounded-lg">
                        <div class="text-gray-500">{{ __('درجة النجاح') }}</div>
                        <div class="font-semibold text-gray-900">
                            {{ $exam->passing_marks }}%
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- الأسئلة -->
        <div class="space-y-6">
            @if($exam->examQuestions->count() > 0)
                @foreach($exam->examQuestions as $index => $examQuestion)
                    <div class="bg-white rounded-xl shadow-sm">
                        <div class="p-6">
                            <!-- رأس السؤال -->
                            <div class="flex items-start justify-between mb-4">
                                <div class="flex items-center">
                                    <span class="bg-primary-100 text-primary-800 text-sm font-medium px-3 py-1 rounded-full ml-4">
                                        {{ __('السؤال') }} {{ $index + 1 }}
                                    </span>
                                    @if($examQuestion->is_required)
                                        <span class="bg-red-100 text-red-800 text-xs font-medium px-2 py-1 rounded-full ml-2">
                                            {{ __('إجباري') }}
                                        </span>
                                    @endif
                                    <span class="text-sm text-gray-500">
                                        ({{ $examQuestion->marks }} {{ __('نقطة') }})
                                    </span>
                                </div>
                                
                                <div class="text-xs text-gray-500">
                                    {{ $examQuestion->question->getTypeLabel() }}
                                    @if($examQuestion->question->category)
                                        | {{ $examQuestion->question->category->name }}
                                    @endif
                                </div>
                            </div>

                            <!-- نص السؤال -->
                            <div class="mb-4">
                                <div class="text-gray-900 text-lg leading-relaxed">
                                    {!! nl2br(e($examQuestion->question->question)) !!}
                                </div>
                            </div>

                            <!-- صورة السؤال -->
                            @if($examQuestion->question->image_url)
                                <div class="mb-4">
                                    <img src="{{ $examQuestion->question->getImageUrl() }}" 
                                         alt="صورة السؤال" 
                                         class="max-w-full h-auto rounded-lg border border-gray-200">
                                </div>
                            @endif

                            <!-- الخيارات -->
                            @if($examQuestion->question->type === 'multiple_choice' && $examQuestion->question->options)
                                <div class="space-y-2">
                                    @foreach($examQuestion->question->options as $optionIndex => $option)
                                        <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                                            <div class="w-6 h-6 bg-white border-2 border-gray-300 rounded-full flex items-center justify-center ml-3">
                                                <span class="text-sm font-medium text-gray-600">
                                                    {{ chr(65 + $optionIndex) }}
                                                </span>
                                            </div>
                                            <span class="text-gray-900">{{ $option }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            @elseif($examQuestion->question->type === 'true_false')
                                <div class="space-y-2">
                                    <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                                        <div class="w-6 h-6 bg-white border-2 border-gray-300 rounded-full flex items-center justify-center ml-3">
                                            <span class="text-sm font-medium text-gray-600">أ</span>
                                        </div>
                                        <span class="text-gray-900">{{ __('صحيح') }}</span>
                                    </div>
                                    <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                                        <div class="w-6 h-6 bg-white border-2 border-gray-300 rounded-full flex items-center justify-center ml-3">
                                            <span class="text-sm font-medium text-gray-600">ب</span>
                                        </div>
                                        <span class="text-gray-900">{{ __('خطأ') }}</span>
                                    </div>
                                </div>
                            @elseif($examQuestion->question->type === 'fill_blank')
                                <div class="p-3 bg-gray-50 rounded-lg">
                                    <div class="border-b-2 border-gray-400 pb-1 mb-2">
                                        <span class="text-gray-500 text-sm">{{ __('منطقة الإجابة') }}</span>
                                    </div>
                                </div>
                            @elseif(in_array($examQuestion->question->type, ['short_answer', 'essay']))
                                <div class="p-3 bg-gray-50 rounded-lg">
                                    <div class="border border-gray-300 rounded-lg p-4 min-h-[100px] bg-white">
                                        <span class="text-gray-500 text-sm">
                                            {{ $examQuestion->question->type === 'essay' ? __('منطقة الإجابة المقالية') : __('منطقة الإجابة القصيرة') }}
                                        </span>
                                    </div>
                                </div>
                            @endif

                            <!-- وقت الإجابة -->
                            @if($examQuestion->time_limit)
                                <div class="mt-3 text-xs text-gray-500 flex items-center">
                                    <i class="fas fa-clock ml-1"></i>
                                    {{ __('وقت الإجابة المخصص') }}: {{ $examQuestion->time_limit }} {{ __('ثانية') }}
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            @else
                <div class="bg-white rounded-xl shadow-sm p-8 text-center">
                    <div class="text-gray-400 text-6xl mb-4">
                        <i class="fas fa-question-circle"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">
                        {{ __('لا توجد أسئلة في الامتحان') }}
                    </h3>
                    <p class="text-gray-600 mb-4">
                        {{ __('يجب إضافة أسئلة للامتحان قبل المعاينة') }}
                    </p>
                    <a href="{{ route('admin.exams.questions.manage', $exam) }}" 
                       class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-lg font-medium transition-colors inline-flex items-center">
                        <i class="fas fa-plus ml-2"></i>
                        {{ __('إضافة أسئلة') }}
                    </a>
                </div>
            @endif
        </div>

        <!-- ملخص الامتحان -->
        @if($exam->examQuestions->count() > 0)
            <div class="bg-primary-50 rounded-xl p-6 mt-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-primary-900 mb-2">
                            {{ __('ملخص الامتحان') }}
                        </h3>
                        <div class="text-primary-700 text-sm space-y-1">
                            <div>{{ __('إجمالي الأسئلة') }}: {{ $exam->examQuestions->count() }}</div>
                            <div>{{ __('إجمالي الدرجات') }}: {{ $exam->total_marks ?? $exam->calculateTotalMarks() }}</div>
                            <div>{{ __('الأسئلة الإجبارية') }}: {{ $exam->examQuestions->where('is_required', true)->count() }}</div>
                            <div>{{ __('الأسئلة الاختيارية') }}: {{ $exam->examQuestions->where('is_required', false)->count() }}</div>
                        </div>
                    </div>
                    <div class="text-primary-600">
                        <i class="fas fa-file-alt text-3xl"></i>
                    </div>
                </div>
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
