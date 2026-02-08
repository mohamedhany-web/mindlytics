@extends('layouts.app')

@section('content')
<div class="w-full px-4 sm:px-6 lg:px-8 py-6">
    <div class="max-w-6xl mx-auto">
        <!-- رأس الصفحة -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 sm:p-6 mb-6">
            <nav class="text-sm text-slate-500 mb-2">
                <a href="{{ route('student.dashboard') }}" class="hover:text-sky-600">لوحة التحكم</a>
                <span class="mx-1">/</span>
                <a href="{{ route('student.exams.index') }}" class="hover:text-sky-600">امتحاناتي</a>
                <span class="mx-1">/</span>
                <span class="text-slate-700">{{ $exam->title }}</span>
                <span class="mx-1">/</span>
                <span class="text-slate-900 font-medium">النتيجة</span>
            </nav>
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-xl bg-sky-100 flex items-center justify-center">
                        <i class="fas fa-chart-pie text-sky-600"></i>
                    </div>
                    <div>
                        <h1 class="text-xl font-bold text-slate-900">{{ __('نتيجة الامتحان') }}</h1>
                        <p class="text-slate-600">{{ $exam->title }}</p>
                    </div>
                </div>
                <a href="{{ route('student.exams.show', $exam) }}" 
                   class="inline-flex items-center text-slate-600 hover:text-sky-600 font-medium">
                    <i class="fas fa-arrow-right ml-2"></i>
                    {{ __('عودة للامتحان') }}
                </a>
                <a href="{{ route('student.exams.index') }}" 
                   class="bg-sky-600 hover:bg-sky-700 text-white px-4 py-2.5 rounded-xl font-medium transition-colors">
                    <i class="fas fa-list ml-2"></i>
                    {{ __('امتحاناتي') }}
                </a>
            </div>
        </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- النتيجة الإجمالية -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 mb-6">
                <div class="text-center">
                    <div class="w-32 h-32 mx-auto mb-4 relative">
                        <svg class="w-full h-full transform -rotate-90" viewBox="0 0 36 36">
                            <path class="text-slate-200" stroke="currentColor" stroke-width="3" fill="none"
                                  d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"/>
                            <path class="text-{{ $attempt->result_color }}-500" stroke="currentColor" stroke-width="3" fill="none" 
                                  stroke-linecap="round" stroke-dasharray="{{ $attempt->percentage }}, 100"
                                  d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"/>
                        </svg>
                        <div class="absolute inset-0 flex items-center justify-center">
                            <div class="text-center">
                                <div class="text-2xl font-bold text-slate-900">
                                    {{ number_format($attempt->percentage, 1) }}%
                                </div>
                                <div class="text-sm text-slate-500">
                                    {{ $attempt->result_status }}
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="space-y-3">
                        <div class="flex justify-between items-center p-3 bg-slate-50 rounded-xl">
                            <span class="text-slate-600">{{ __('النقاط المحصل عليها') }}</span>
                            <span class="font-semibold text-slate-900">
                                {{ $attempt->score ?? 0 }} / {{ $exam->total_marks ?? $exam->calculateTotalMarks() }}
                            </span>
                        </div>
                        
                        <div class="flex justify-between items-center p-3 bg-slate-50 rounded-xl">
                            <span class="text-slate-600">{{ __('الوقت المستغرق') }}</span>
                            <span class="font-semibold text-slate-900">
                                {{ $attempt->formatted_time ?? 'غير محدد' }}
                            </span>
                        </div>
                        
                        <div class="flex justify-between items-center p-3 bg-slate-50 rounded-xl">
                            <span class="text-slate-600">{{ __('تاريخ التسليم') }}</span>
                            <span class="font-semibold text-slate-900">
                                {{ $attempt->submitted_at ? $attempt->submitted_at->format('d/m/Y H:i') : '-' }}
                            </span>
                        </div>

                        @if($attempt->auto_submitted)
                            <div class="p-3 bg-amber-50 border border-amber-200 rounded-lg">
                                <div class="flex items-center text-amber-700">
                                    <i class="fas fa-clock ml-2"></i>
                                    <span class="text-sm">{{ __('تم التسليم تلقائياً بانتهاء الوقت') }}</span>
                                </div>
                            </div>
                        @endif

                        @if($attempt->tab_switches > 0)
                            <div class="p-3 bg-red-50 border border-red-200 rounded-lg">
                                <div class="flex items-center text-red-700">
                                    <i class="fas fa-exclamation-triangle ml-2"></i>
                                    <span class="text-sm">
                                        {{ __('تبديل التبويبات') }}: {{ $attempt->tab_switches }} {{ __('مرة') }}
                                    </span>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            @if($attempt->feedback)
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-slate-900 mb-3">
                        <i class="fas fa-comment ml-2"></i>
                        {{ __('تعليقات المصحح') }}
                    </h3>
                    <div class="text-slate-700 leading-relaxed">
                        {!! nl2br(e($attempt->feedback)) !!}
                    </div>
                    @if($attempt->reviewed_by)
                        <div class="mt-3 pt-3 border-t border-slate-200 text-sm text-slate-500">
                            {{ __('تم التصحيح بواسطة') }}: {{ $attempt->reviewer->name ?? 'المصحح' }}
                            @if($attempt->reviewed_at)
                                {{ __('في') }} {{ $attempt->reviewed_at->format('d/m/Y H:i') }}
                            @endif
                        </div>
                    @endif
                </div>
            @endif
        </div>

        <!-- تفاصيل الأسئلة والإجابات -->
        <div class="lg:col-span-2">
            @if($exam->show_correct_answers || $exam->allow_review)
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm">
                    <div class="p-6 border-b border-slate-200">
                        <h3 class="text-lg font-semibold text-slate-900">
                            {{ __('مراجعة الأسئلة والإجابات') }}
                        </h3>
                        <p class="text-slate-600 text-sm mt-1">
                            {{ __('يمكنك مراجعة إجاباتك والإجابات الصحيحة') }}
                        </p>
                    </div>

                    <div class="divide-y divide-gray-200">
                        @foreach($exam->examQuestions as $index => $examQuestion)
                            @php
                                $question = $examQuestion->question;
                                $userAnswer = $attempt->answers[$question->id] ?? null;
                                $isCorrect = $question->isCorrectAnswer($userAnswer);
                            @endphp
                            
                            <div class="p-6">
                                <!-- رأس السؤال -->
                                <div class="flex items-start justify-between mb-4">
                                    <div class="flex items-center">
                                        <span class="bg-gray-100 text-gray-800 text-sm font-medium px-3 py-1 rounded-full ml-3">
                                            {{ __('السؤال') }} {{ $index + 1 }}
                                        </span>
                                        <span class="text-sm text-gray-600">
                                            ({{ $examQuestion->marks }} {{ __('نقطة') }})
                                        </span>
                                    </div>
                                    
                                    @if($isCorrect !== null)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            {{ $isCorrect ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            @if($isCorrect)
                                                <i class="fas fa-check ml-1"></i>
                                                {{ __('صحيح') }}
                                            @else
                                                <i class="fas fa-times ml-1"></i>
                                                {{ __('خطأ') }}
                                            @endif
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                            <i class="fas fa-question ml-1"></i>
                                            {{ __('يحتاج مراجعة') }}
                                        </span>
                                    @endif
                                </div>

                                <!-- نص السؤال -->
                                <div class="mb-4">
                                    <div class="text-gray-900 leading-relaxed">
                                        {!! nl2br(e($question->question)) !!}
                                    </div>
                                </div>

                                <!-- صورة السؤال -->
                                @if($question->image_url)
                                    <div class="mb-4">
                                        <img src="{{ $question->getImageUrl() }}" 
                                             alt="صورة السؤال" 
                                             class="max-w-full h-auto rounded-lg border border-gray-200">
                                    </div>
                                @endif

                                <!-- الإجابات -->
                                @if($question->type === 'multiple_choice' && $question->options)
                                    <div class="space-y-2">
                                        @foreach($question->options as $optionIndex => $option)
                                            @php
                                                $optionLetter = chr(65 + $optionIndex);
                                                $isUserAnswer = $userAnswer == $optionIndex;
                                                $isCorrectAnswer = in_array($optionIndex, (array)$question->correct_answer);
                                            @endphp
                                            
                                            @php
                                                $borderClass = 'border-gray-200 bg-gray-50';
                                                $circleClass = 'border-gray-400';
                                                if ($exam->show_correct_answers && $isCorrectAnswer) {
                                                    $borderClass = 'border-green-300 bg-green-50';
                                                    $circleClass = 'border-green-500 bg-green-500 text-white';
                                                } elseif ($isUserAnswer && !$isCorrectAnswer) {
                                                    $borderClass = 'border-red-300 bg-red-50';
                                                    $circleClass = 'border-red-500 bg-red-500 text-white';
                                                } elseif ($isUserAnswer) {
                                                    $borderClass = 'border-blue-300 bg-blue-50';
                                                    $circleClass = 'border-blue-500 bg-blue-500 text-white';
                                                }
                                            @endphp
                                            <div class="flex items-center p-3 rounded-lg border {{ $borderClass }}">
                                                
                                                <div class="flex items-center">
                                                    <div class="w-6 h-6 border-2 rounded-full flex items-center justify-center ml-3 {{ $circleClass }}">
                                                        <span class="text-sm font-medium">{{ $optionLetter }}</span>
                                                    </div>
                                                    <span class="text-gray-900">{{ $option }}</span>
                                                </div>
                                                
                                                <div class="mr-auto flex space-x-1 space-x-reverse">
                                                    @if($isUserAnswer)
                                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                            {{ __('إجابتك') }}
                                                        </span>
                                                    @endif
                                                    @if($exam->show_correct_answers && $isCorrectAnswer)
                                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                            {{ __('الإجابة الصحيحة') }}
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @elseif($question->type === 'true_false')
                                    <div class="space-y-2">
                                        @foreach(['true' => 'صحيح', 'false' => 'خطأ'] as $value => $label)
                                            @php
                                                $isUserAnswer = $userAnswer === $value;
                                                $isCorrectAnswer = $question->correct_answer === $value;
                                            @endphp
                                            
                                            @php
                                                $borderClass = 'border-gray-200 bg-gray-50';
                                                if ($exam->show_correct_answers && $isCorrectAnswer) {
                                                    $borderClass = 'border-green-300 bg-green-50';
                                                } elseif ($isUserAnswer && !$isCorrectAnswer) {
                                                    $borderClass = 'border-red-300 bg-red-50';
                                                } elseif ($isUserAnswer) {
                                                    $borderClass = 'border-blue-300 bg-blue-50';
                                                }
                                            @endphp
                                            <div class="flex items-center p-3 rounded-lg border {{ $borderClass }}">
                                                
                                                <span class="text-gray-900">{{ $label }}</span>
                                                
                                                <div class="mr-auto flex space-x-1 space-x-reverse">
                                                    @if($isUserAnswer)
                                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                            {{ __('إجابتك') }}
                                                        </span>
                                                    @endif
                                                    @if($exam->show_correct_answers && $isCorrectAnswer)
                                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                            {{ __('الإجابة الصحيحة') }}
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @elseif(in_array($question->type, ['fill_blank', 'short_answer']))
                                    <div class="space-y-3">
                                        <div class="p-3 bg-gray-50 rounded-lg">
                                            <div class="text-sm text-gray-600 mb-1">{{ __('إجابتك') }}:</div>
                                            <div class="text-gray-900">
                                                {{ $userAnswer ?: __('لم تتم الإجابة') }}
                                            </div>
                                        </div>
                                        
                                        @if($exam->show_correct_answers && $question->correct_answer)
                                            <div class="p-3 bg-green-50 border border-green-200 rounded-lg">
                                                <div class="text-sm text-green-700 mb-1">{{ __('الإجابة الصحيحة') }}:</div>
                                                <div class="text-green-900">
                                                    @if(is_array($question->correct_answer))
                                                        {{ implode(' أو ', $question->correct_answer) }}
                                                    @else
                                                        {{ $question->correct_answer }}
                                                    @endif
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                @elseif($question->type === 'essay')
                                    <div class="space-y-3">
                                        <div class="p-3 bg-gray-50 rounded-lg">
                                            <div class="text-sm text-gray-600 mb-1">{{ __('إجابتك') }}:</div>
                                            <div class="text-gray-900 leading-relaxed">
                                                @if($userAnswer)
                                                    {!! nl2br(e($userAnswer)) !!}
                                                @else
                                                    <em class="text-gray-500">{{ __('لم تتم الإجابة') }}</em>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <!-- شرح الإجابة -->
                                @if($exam->show_explanations && $question->explanation)
                                    <div class="mt-4 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                                        <div class="flex items-start">
                                            <i class="fas fa-info-circle text-blue-500 ml-2 mt-0.5"></i>
                                            <div>
                                                <div class="text-sm font-medium text-blue-700 mb-1">
                                                    {{ __('شرح الإجابة') }}:
                                                </div>
                                                <div class="text-blue-800 text-sm leading-relaxed">
                                                    {!! nl2br(e($question->explanation)) !!}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @else
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-8 text-center">
                    <div class="text-slate-400 text-6xl mb-4">
                        <i class="fas fa-eye-slash"></i>
                    </div>
                    <h3 class="text-lg font-medium text-slate-900 mb-2">
                        {{ __('مراجعة الأسئلة غير متاحة') }}
                    </h3>
                    <p class="text-slate-600">
                        {{ __('لا يسمح بمراجعة الأسئلة والإجابات لهذا الامتحان') }}
                    </p>
                </div>
            @endif
        </div>
    </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.result-content {
    font-family: 'IBM Plex Sans Arabic', sans-serif;
    line-height: 1.8;
}
</style>
@endpush
