@extends('layouts.admin')

@section('title', 'إحصائيات الامتحان')
@section('header', 'إحصائيات الامتحان')

@php
    $overview = $stats['overview'] ?? [];
    $totalAttempts = (int) ($overview['total_attempts'] ?? 0);
    $gradeBarClasses = [
        'ممتاز' => 'bg-green-500',
        'جيد جداً' => 'bg-blue-500',
        'جيد' => 'bg-yellow-500',
        'مقبول' => 'bg-orange-500',
        'ضعيف' => 'bg-red-500',
    ];
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
                    <span class="text-white">الإحصائيات</span>
                </nav>
                <h1 class="text-xl sm:text-2xl font-bold mt-1">إحصائيات الامتحان</h1>
                <p class="text-sm text-white/90 mt-1">{{ $exam->title }} — {{ $totalAttempts }} محاولة</p>
            </div>
            <div class="flex flex-wrap gap-2 flex-shrink-0">
                <a href="{{ route('admin.exams.show', $exam) }}" class="inline-flex items-center gap-2 bg-white text-indigo-600 hover:bg-gray-100 px-4 py-2.5 rounded-xl font-semibold transition-colors">
                    <i class="fas fa-arrow-right"></i>
                    العودة للامتحان
                </a>
                <a href="{{ route('admin.exams.preview', $exam) }}" class="inline-flex items-center gap-2 bg-white/20 hover:bg-white/30 text-white px-4 py-2.5 rounded-xl font-medium transition-colors border border-white/30">
                    <i class="fas fa-eye"></i>
                    معاينة الامتحان
                </a>
                <a href="{{ route('admin.exams.by-course', $exam->advanced_course_id) }}" class="inline-flex items-center gap-2 bg-white/20 hover:bg-white/30 text-white px-4 py-2.5 rounded-xl font-medium transition-colors border border-white/30">
                    <i class="fas fa-list"></i>
                    امتحانات الكورس
                </a>
            </div>
        </div>
    </div>

    <!-- بطاقات الإحصائيات العامة -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="p-5 flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-indigo-100 flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-users text-indigo-600 text-xl"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-2xl font-bold text-gray-900">{{ $totalAttempts }}</p>
                    <p class="text-sm text-gray-500">إجمالي المحاولات</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="p-5 flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-emerald-100 flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-chart-line text-emerald-600 text-xl"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($overview['average_score'] ?? 0, 1) }}</p>
                    <p class="text-sm text-gray-500">متوسط الدرجات</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="p-5 flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-amber-100 flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-trophy text-amber-600 text-xl"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($overview['highest_score'] ?? 0, 1) }}</p>
                    <p class="text-sm text-gray-500">أعلى درجة</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="p-5 flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-violet-100 flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-percentage text-violet-600 text-xl"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($overview['pass_rate'] ?? 0, 1) }}%</p>
                    <p class="text-sm text-gray-500">معدل النجاح</p>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- توزيع الدرجات -->
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                    <i class="fas fa-chart-bar text-indigo-600"></i>
                    توزيع الدرجات
                </h2>
            </div>
            <div class="p-6">
                @if($stats['score_distribution']->count() > 0)
                    <div class="space-y-4">
                        @foreach($stats['score_distribution'] as $grade)
                            @php
                                $pct = $totalAttempts > 0 ? round(($grade->count / $totalAttempts) * 100, 1) : 0;
                                $barClass = $gradeBarClasses[$grade->grade] ?? 'bg-gray-500';
                            @endphp
                            <div class="flex items-center justify-between gap-4">
                                <div class="flex items-center gap-3 min-w-0">
                                    <span class="w-3 h-3 rounded-full flex-shrink-0 {{ $barClass }}"></span>
                                    <span class="font-medium text-gray-900">{{ $grade->grade }}</span>
                                </div>
                                <div class="flex items-center gap-3 flex-shrink-0">
                                    <div class="w-28 sm:w-32 h-2 bg-gray-200 rounded-full overflow-hidden">
                                        <div class="h-full rounded-full {{ $barClass }}" style="width: {{ min($pct, 100) }}%"></div>
                                    </div>
                                    <span class="text-sm text-gray-600 min-w-[70px]">{{ $grade->count }} ({{ $pct }}%)</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-10 rounded-xl bg-gray-50 border border-dashed border-gray-200">
                        <i class="fas fa-chart-bar text-4xl text-gray-300 mb-3"></i>
                        <p class="text-gray-500">لا توجد بيانات لعرض توزيع الدرجات</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- المحاولات حسب التاريخ -->
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                    <i class="fas fa-calendar-alt text-indigo-600"></i>
                    المحاولات حسب التاريخ
                </h2>
            </div>
            <div class="p-6">
                @if($stats['attempts_by_date']->count() > 0)
                    <div class="space-y-2">
                        @foreach($stats['attempts_by_date']->take(10) as $attempt)
                            <div class="flex items-center justify-between p-3 rounded-xl bg-gray-50 border border-gray-100 hover:border-indigo-100 transition-colors">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-lg bg-indigo-100 flex items-center justify-center">
                                        <i class="fas fa-calendar text-indigo-600 text-sm"></i>
                                    </div>
                                    <span class="font-medium text-gray-900">{{ \Carbon\Carbon::parse($attempt->date)->format('d/m/Y') }}</span>
                                </div>
                                <span class="inline-flex items-center px-3 py-1 rounded-lg text-sm font-semibold bg-indigo-100 text-indigo-800">
                                    {{ $attempt->count }} محاولة
                                </span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-10 rounded-xl bg-gray-50 border border-dashed border-gray-200">
                        <i class="fas fa-calendar-alt text-4xl text-gray-300 mb-3"></i>
                        <p class="text-gray-500">لا توجد محاولات مسجلة</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- تفاصيل النتائج -->
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                    <i class="fas fa-clipboard-list text-indigo-600"></i>
                    تفاصيل النتائج
                </h2>
            </div>
            <div class="p-6 space-y-3">
                <div class="flex items-center justify-between p-4 rounded-xl bg-emerald-50 border border-emerald-200">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-lg bg-emerald-100 flex items-center justify-center">
                            <i class="fas fa-check text-emerald-600"></i>
                        </div>
                        <span class="font-medium text-emerald-800">الطلاب الناجحون</span>
                    </div>
                    <span class="text-lg font-bold text-emerald-800">{{ $overview['passed_attempts'] ?? 0 }}</span>
                </div>
                <div class="flex items-center justify-between p-4 rounded-xl bg-red-50 border border-red-200">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-lg bg-red-100 flex items-center justify-center">
                            <i class="fas fa-times text-red-600"></i>
                        </div>
                        <span class="font-medium text-red-800">الطلاب الراسبون</span>
                    </div>
                    <span class="text-lg font-bold text-red-800">{{ $overview['failed_attempts'] ?? 0 }}</span>
                </div>
                <div class="flex items-center justify-between p-4 rounded-xl bg-gray-50 border border-gray-200">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-lg bg-gray-200 flex items-center justify-center">
                            <i class="fas fa-arrow-down text-gray-600"></i>
                        </div>
                        <span class="font-medium text-gray-800">أقل درجة</span>
                    </div>
                    <span class="text-lg font-bold text-gray-800">{{ number_format($overview['lowest_score'] ?? 0, 1) }}</span>
                </div>
                <div class="flex items-center justify-between p-4 rounded-xl bg-gray-50 border border-gray-200">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-lg bg-gray-200 flex items-center justify-center">
                            <i class="fas fa-flag-checkered text-gray-600"></i>
                        </div>
                        <span class="font-medium text-gray-800">درجة النجاح المطلوبة</span>
                    </div>
                    <span class="text-lg font-bold text-gray-800">{{ $exam->passing_marks }}%</span>
                </div>
            </div>
        </div>

        <!-- معلومات الامتحان -->
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                    <i class="fas fa-info-circle text-indigo-600"></i>
                    معلومات الامتحان
                </h2>
            </div>
            <div class="p-6 space-y-4">
                <div class="flex items-center justify-between py-2 border-b border-gray-100">
                    <span class="text-gray-600">عدد الأسئلة</span>
                    <span class="font-semibold text-gray-900">{{ $exam->examQuestions->count() }} سؤال</span>
                </div>
                <div class="flex items-center justify-between py-2 border-b border-gray-100">
                    <span class="text-gray-600">إجمالي الدرجات</span>
                    <span class="font-semibold text-gray-900">{{ $exam->total_marks ?? $exam->calculateTotalMarks() }}</span>
                </div>
                <div class="flex items-center justify-between py-2 border-b border-gray-100">
                    <span class="text-gray-600">مدة الامتحان</span>
                    <span class="font-semibold text-gray-900">{{ $exam->duration_minutes }} دقيقة</span>
                </div>
                <div class="flex items-center justify-between py-2 border-b border-gray-100">
                    <span class="text-gray-600">عدد المحاولات المسموحة</span>
                    <span class="font-semibold text-gray-900">{{ $exam->attempts_allowed == 0 ? 'غير محدود' : $exam->attempts_allowed }}</span>
                </div>
                <div class="flex items-center justify-between py-2 border-b border-gray-100">
                    <span class="text-gray-600">حالة الامتحان</span>
                    <span class="inline-flex items-center px-3 py-1 rounded-lg text-sm font-medium {{ $exam->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-red-100 text-red-800' }}">
                        {{ $exam->is_active ? 'نشط' : 'غير نشط' }}
                    </span>
                </div>
                <div class="flex items-center justify-between py-2 border-b border-gray-100">
                    <span class="text-gray-600">حالة النشر</span>
                    <span class="inline-flex items-center px-3 py-1 rounded-lg text-sm font-medium {{ $exam->is_published ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800' }}">
                        {{ $exam->is_published ? 'منشور' : 'مسودة' }}
                    </span>
                </div>
                <div class="flex items-center justify-between py-2">
                    <span class="text-gray-600">تاريخ الإنشاء</span>
                    <span class="font-semibold text-gray-900">{{ $exam->created_at->format('d/m/Y H:i') }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
