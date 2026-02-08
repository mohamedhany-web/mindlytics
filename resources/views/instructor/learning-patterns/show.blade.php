@extends('layouts.app')

@section('title', 'تفاصيل النمط التعليمي - ' . $pattern->title)
@section('header', 'تفاصيل النمط التعليمي')

@section('content')
<div class="space-y-6">
    <div class="rounded-2xl p-5 sm:p-6 bg-white border border-slate-200 shadow-sm">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-slate-800 mb-1">{{ $pattern->title }}</h1>
                <p class="text-sm text-slate-500">{{ $course->title }}</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('instructor.learning-patterns.index', $course) }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-semibold transition-colors">
                    <i class="fas fa-arrow-right ml-2"></i> العودة
                </a>
                <a href="{{ route('instructor.learning-patterns.edit', [$course, $pattern]) }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-sky-500 hover:bg-sky-600 text-white rounded-xl font-semibold transition-colors">
                    <i class="fas fa-edit ml-2"></i> تعديل
                </a>
                <a href="{{ route('instructor.courses.curriculum', $course) }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-emerald-500 hover:bg-emerald-600 text-white rounded-xl font-semibold transition-colors">
                    <i class="fas fa-book ml-2"></i> إضافة للمنهج
                </a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="rounded-2xl bg-white border border-slate-200 p-6 shadow-sm">
                <h2 class="text-lg font-bold text-slate-800 mb-4">تفاصيل النمط</h2>
                
                @php
                    $typeInfo = $pattern->getTypeInfo();
                @endphp
                
                <div class="space-y-4">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-xl bg-amber-100 flex items-center justify-center text-amber-600">
                            <i class="{{ $typeInfo['icon'] ?? 'fas fa-puzzle-piece' }} text-xl"></i>
                        </div>
                        <div>
                            <p class="text-xs text-slate-500">نوع النمط</p>
                            <p class="font-bold text-slate-800">{{ $typeInfo['name'] ?? 'نمط تعليمي' }}</p>
                        </div>
                    </div>
                    
                    @if($pattern->description)
                        <div>
                            <p class="text-xs text-slate-500 mb-1">الوصف</p>
                            <p class="text-slate-700">{{ $pattern->description }}</p>
                        </div>
                    @endif
                    
                    @if($pattern->instructions)
                        <div>
                            <p class="text-xs text-slate-500 mb-1">التعليمات</p>
                            <div class="text-slate-700 whitespace-pre-wrap">{{ $pattern->instructions }}</div>
                        </div>
                    @endif
                </div>
            </div>

            @if($pattern->pattern_data && count($pattern->pattern_data) > 0)
                <div class="rounded-2xl bg-white border border-slate-200 p-6 shadow-sm">
                    <h2 class="text-lg font-bold text-slate-800 mb-4">البيانات التفاعلية</h2>
                    <div class="space-y-4">
                        @if($pattern->type === 'code_challenge')
                            @if(isset($pattern->pattern_data['problem_description']))
                                <div>
                                    <h4 class="font-bold text-[#1C2C39] mb-2">وصف التحدي</h4>
                                    <div class="bg-slate-50 rounded-xl p-4 whitespace-pre-wrap text-slate-700">{{ $pattern->pattern_data['problem_description'] }}</div>
                                </div>
                            @endif
                            @if(isset($pattern->pattern_data['language']))
                                <div>
                                    <h4 class="font-bold text-slate-800 mb-2">لغة البرمجة</h4>
                                    <div class="bg-sky-50 rounded-xl p-4 text-sky-800">{{ $pattern->pattern_data['language'] }}</div>
                                </div>
                            @endif
                        @elseif($pattern->type === 'interactive_quiz')
                            @if(isset($pattern->pattern_data['questions']))
                                <div>
                                    <h4 class="font-bold text-slate-800 mb-2">الأسئلة ({{ count($pattern->pattern_data['questions']) }})</h4>
                                    <div class="space-y-3">
                                        @foreach($pattern->pattern_data['questions'] as $index => $question)
                                            <div class="bg-slate-50 rounded-xl p-4 border border-slate-200">
                                                <div class="font-bold mb-2 text-slate-800">سؤال {{ $index + 1 }}: {{ $question['question'] ?? '' }}</div>
                                                <div class="text-sm text-slate-600">
                                                    النوع: {{ $question['type'] ?? 'multiple_choice' }}
                                                    @if(isset($question['correct_answer']))
                                                        | الإجابة الصحيحة: {{ $question['correct_answer'] }}
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        @elseif($pattern->type === 'live_coding')
                            @if(isset($pattern->pattern_data['video_url']))
                                <div>
                                    <h4 class="font-bold text-slate-800 mb-2">رابط الفيديو</h4>
                                    <div class="bg-slate-50 rounded-xl p-4">
                                        <a href="{{ $pattern->pattern_data['video_url'] }}" target="_blank" class="text-sky-600 hover:underline">
                                            {{ $pattern->pattern_data['video_url'] }}
                                        </a>
                                    </div>
                                </div>
                            @endif
                        @elseif($pattern->type === 'flashcards')
                            @if(isset($pattern->pattern_data['flashcards']))
                                <div>
                                    <h4 class="font-bold text-slate-800 mb-2">البطاقات ({{ count($pattern->pattern_data['flashcards']) }})</h4>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                        @foreach($pattern->pattern_data['flashcards'] as $index => $card)
                                            <div class="bg-slate-50 rounded-xl p-4 border border-slate-200">
                                                <div class="text-xs text-slate-500 mb-1">بطاقة {{ $index + 1 }}</div>
                                                <div class="font-bold mb-1 text-slate-800">{{ $card['front'] ?? '' }}</div>
                                                <div class="text-sm text-slate-600">{{ $card['back'] ?? '' }}</div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        @else
                            <div class="bg-slate-50 rounded-xl p-4">
                                <pre class="text-sm text-slate-700 whitespace-pre-wrap">{{ json_encode($pattern->pattern_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>

        <div class="space-y-6">
            <div class="rounded-2xl bg-white border border-slate-200 p-6 shadow-sm">
                <h2 class="text-lg font-bold text-slate-800 mb-4">الإحصائيات</h2>
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-slate-600">النقاط</span>
                        <span class="font-bold text-sky-600">{{ $pattern->points }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-600">مستوى الصعوبة</span>
                        <span class="font-bold text-slate-800">{{ $pattern->difficulty_level }}/5</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-600">إجمالي المحاولات</span>
                        <span class="font-bold text-sky-600">{{ $pattern->total_attempts }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-600">إجمالي الإكمالات</span>
                        <span class="font-bold text-emerald-600">{{ $pattern->total_completions }}</span>
                    </div>
                    @if($pattern->time_limit_minutes)
                        <div class="flex items-center justify-between">
                            <span class="text-slate-600">الحد الزمني</span>
                            <span class="font-bold text-amber-600">{{ $pattern->time_limit_minutes }} دقيقة</span>
                        </div>
                    @endif
                </div>
            </div>

            <div class="rounded-2xl bg-white border border-slate-200 p-6 shadow-sm">
                <h2 class="text-lg font-bold text-slate-800 mb-4">الإعدادات</h2>
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-slate-600">إلزامي</span>
                        @if($pattern->is_required)
                            <span class="px-2 py-1 bg-amber-100 text-amber-700 rounded-lg text-xs font-semibold">نعم</span>
                        @else
                            <span class="px-2 py-1 bg-slate-100 text-slate-600 rounded-lg text-xs font-semibold">لا</span>
                        @endif
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-600">محاولات متعددة</span>
                        @if($pattern->allow_multiple_attempts)
                            <span class="px-2 py-1 bg-emerald-100 text-emerald-700 rounded-lg text-xs font-semibold">مسموح</span>
                        @else
                            <span class="px-2 py-1 bg-slate-100 text-slate-600 rounded-lg text-xs font-semibold">غير مسموح</span>
                        @endif
                    </div>
                    @if($pattern->max_attempts)
                        <div class="flex items-center justify-between">
                            <span class="text-slate-600">الحد الأقصى</span>
                            <span class="font-bold text-slate-800">{{ $pattern->max_attempts }}</span>
                        </div>
                    @endif
                    <div class="flex items-center justify-between">
                        <span class="text-slate-600">الحالة</span>
                        @if($pattern->is_active)
                            <span class="px-2 py-1 bg-emerald-100 text-emerald-700 rounded-lg text-xs font-semibold">نشط</span>
                        @else
                            <span class="px-2 py-1 bg-slate-100 text-slate-600 rounded-lg text-xs font-semibold">غير نشط</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($pattern->attempts->count() > 0)
        <div class="rounded-2xl bg-white border border-slate-200 p-6 shadow-sm">
            <h2 class="text-lg font-bold text-slate-800 mb-4">المحاولات الأخيرة</h2>
            <div class="overflow-x-auto rounded-xl border border-slate-200">
                <table class="w-full">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200">
                            <th class="text-right py-3 px-4 text-sm font-bold text-slate-800">الطالب</th>
                            <th class="text-right py-3 px-4 text-sm font-bold text-slate-800">الحالة</th>
                            <th class="text-right py-3 px-4 text-sm font-bold text-slate-800">النتيجة</th>
                            <th class="text-right py-3 px-4 text-sm font-bold text-slate-800">التاريخ</th>
                            <th class="text-right py-3 px-4 text-sm font-bold text-slate-800">إجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pattern->attempts as $attempt)
                            <tr class="border-b border-slate-100 hover:bg-slate-50/50">
                                <td class="py-3 px-4">
                                    <div class="flex items-center gap-2">
                                        <div class="w-8 h-8 rounded-full bg-sky-100 text-sky-600 flex items-center justify-center text-xs font-bold">
                                            {{ substr($attempt->user->name ?? 'غير معروف', 0, 1) }}
                                        </div>
                                        <span class="text-sm text-slate-800">{{ $attempt->user->name ?? 'غير معروف' }}</span>
                                    </div>
                                </td>
                                <td class="py-3 px-4">
                                    @if($attempt->status === 'completed')
                                        <span class="px-2 py-1 bg-emerald-100 text-emerald-700 rounded-lg text-xs font-semibold">مكتمل</span>
                                    @elseif($attempt->status === 'in_progress')
                                        <span class="px-2 py-1 bg-amber-100 text-amber-700 rounded-lg text-xs font-semibold">قيد التنفيذ</span>
                                    @else
                                        <span class="px-2 py-1 bg-slate-100 text-slate-600 rounded-lg text-xs font-semibold">{{ $attempt->status }}</span>
                                    @endif
                                </td>
                                <td class="py-3 px-4">
                                    @if($attempt->score !== null)
                                        <span class="font-bold text-sky-600">{{ $attempt->score }}%</span>
                                    @else
                                        <span class="text-slate-400">-</span>
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-sm text-slate-600">
                                    {{ $attempt->created_at->format('Y/m/d H:i') }}
                                </td>
                                <td class="py-3 px-4">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <form action="{{ route('instructor.learning-patterns.attempts.destroy', [$course, $pattern, $attempt]) }}" method="POST" class="inline" onsubmit="return confirm('إزالة هذه المحاولة للسماح للطالب بالمحاولة مرة أخرى؟');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-sky-100 hover:bg-sky-200 text-sky-700 text-xs font-semibold transition-colors" title="إعادة المحاولة">
                                                <i class="fas fa-redo text-xs"></i>
                                                إعادة المحاولة
                                            </button>
                                        </form>
                                        <form action="{{ route('instructor.learning-patterns.attempts.destroy', [$course, $pattern, $attempt]) }}" method="POST" class="inline" onsubmit="return confirm('هل أنت متأكد من حذف هذه المحاولة؟');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-red-100 hover:bg-red-200 text-red-700 text-xs font-semibold transition-colors" title="حذف المحاولة">
                                                <i class="fas fa-trash-alt text-xs"></i>
                                                حذف
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>
@endsection
