@extends('layouts.admin')

@section('content')
<div class="p-6">
    <div class="mb-6">
        <div class="flex items-center justify-between mb-2">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ __('إحصائيات الامتحان') }}</h1>
                <p class="text-gray-600">{{ $exam->title }}</p>
            </div>
            <div class="flex space-x-2 space-x-reverse">
                <a href="{{ route('admin.exams.show', $exam) }}" 
                   class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                    <i class="fas fa-arrow-right ml-2"></i>
                    {{ __('العودة') }}
                </a>
                <a href="{{ route('admin.exams.preview', $exam) }}" 
                   class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                    <i class="fas fa-eye ml-2"></i>
                    {{ __('معاينة الامتحان') }}
                </a>
            </div>
        </div>
    </div>

    <!-- الإحصائيات العامة -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center">
                <div class="p-3 bg-blue-100 rounded-full">
                    <i class="fas fa-users text-blue-600 text-xl"></i>
                </div>
                <div class="mr-4">
                    <div class="text-2xl font-bold text-gray-900">
                        {{ $stats['overview']['total_attempts'] }}
                    </div>
                    <div class="text-gray-600 text-sm">
                        {{ __('إجمالي المحاولات') }}
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center">
                <div class="p-3 bg-green-100 rounded-full">
                    <i class="fas fa-chart-line text-green-600 text-xl"></i>
                </div>
                <div class="mr-4">
                    <div class="text-2xl font-bold text-gray-900">
                        {{ number_format($stats['overview']['average_score'], 1) }}
                    </div>
                    <div class="text-gray-600 text-sm">
                        {{ __('متوسط الدرجات') }}
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center">
                <div class="p-3 bg-yellow-100 rounded-full">
                    <i class="fas fa-trophy text-yellow-600 text-xl"></i>
                </div>
                <div class="mr-4">
                    <div class="text-2xl font-bold text-gray-900">
                        {{ number_format($stats['overview']['highest_score'], 1) }}
                    </div>
                    <div class="text-gray-600 text-sm">
                        {{ __('أعلى درجة') }}
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center">
                <div class="p-3 bg-purple-100 rounded-full">
                    <i class="fas fa-percentage text-purple-600 text-xl"></i>
                </div>
                <div class="mr-4">
                    <div class="text-2xl font-bold text-gray-900">
                        {{ number_format($stats['overview']['pass_rate'], 1) }}%
                    </div>
                    <div class="text-gray-600 text-sm">
                        {{ __('معدل النجاح') }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- توزيع الدرجات -->
        <div class="bg-white rounded-xl shadow-sm">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-chart-bar ml-2"></i>
                    {{ __('توزيع الدرجات') }}
                </h3>
            </div>
            
            <div class="p-6">
                @if($stats['score_distribution']->count() > 0)
                    <div class="space-y-4">
                        @foreach($stats['score_distribution'] as $grade)
                            @php
                                $percentage = $stats['overview']['total_attempts'] > 0 
                                    ? ($grade->count / $stats['overview']['total_attempts']) * 100 
                                    : 0;
                                
                                $colors = [
                                    'ممتاز' => 'green',
                                    'جيد جداً' => 'blue', 
                                    'جيد' => 'yellow',
                                    'مقبول' => 'orange',
                                    'ضعيف' => 'red'
                                ];
                                $color = $colors[$grade->grade] ?? 'gray';
                            @endphp
                            
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="w-4 h-4 bg-{{ $color }}-500 rounded-full ml-3"></div>
                                    <span class="text-gray-900 font-medium">
                                        {{ $grade->grade }}
                                    </span>
                                </div>
                                
                                <div class="flex items-center">
                                    <div class="w-32 bg-gray-200 rounded-full h-2 ml-3">
                                        <div class="bg-{{ $color }}-500 h-2 rounded-full" 
                                             style="width: {{ $percentage }}%"></div>
                                    </div>
                                    <span class="text-gray-600 text-sm min-w-[60px]">
                                        {{ $grade->count }} ({{ number_format($percentage, 1) }}%)
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <div class="text-gray-400 text-4xl mb-4">
                            <i class="fas fa-chart-bar"></i>
                        </div>
                        <p class="text-gray-600">
                            {{ __('لا توجد بيانات لعرض توزيع الدرجات') }}
                        </p>
                    </div>
                @endif
            </div>
        </div>

        <!-- المحاولات حسب التاريخ -->
        <div class="bg-white rounded-xl shadow-sm">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-calendar-alt ml-2"></i>
                    {{ __('المحاولات حسب التاريخ') }}
                </h3>
            </div>
            
            <div class="p-6">
                @if($stats['attempts_by_date']->count() > 0)
                    <div class="space-y-3">
                        @foreach($stats['attempts_by_date']->take(10) as $attempt)
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <div class="flex items-center">
                                    <div class="p-2 bg-blue-100 rounded-full ml-3">
                                        <i class="fas fa-calendar text-blue-600 text-sm"></i>
                                    </div>
                                    <span class="text-gray-900 font-medium">
                                        {{ \Carbon\Carbon::parse($attempt->date)->format('d/m/Y') }}
                                    </span>
                                </div>
                                
                                <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm font-medium">
                                    {{ $attempt->count }} {{ __('محاولة') }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <div class="text-gray-400 text-4xl mb-4">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                        <p class="text-gray-600">
                            {{ __('لا توجد محاولات مسجلة') }}
                        </p>
                    </div>
                @endif
            </div>
        </div>

        <!-- تفاصيل النتائج -->
        <div class="bg-white rounded-xl shadow-sm">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-clipboard-list ml-2"></i>
                    {{ __('تفاصيل النتائج') }}
                </h3>
            </div>
            
            <div class="p-6">
                <div class="space-y-4">
                    <div class="flex items-center justify-between p-4 bg-green-50 border border-green-200 rounded-lg">
                        <div class="flex items-center">
                            <div class="p-2 bg-green-100 rounded-full ml-3">
                                <i class="fas fa-check text-green-600"></i>
                            </div>
                            <span class="text-green-800 font-medium">
                                {{ __('الطلاب الناجحون') }}
                            </span>
                        </div>
                        <span class="text-green-800 font-bold text-lg">
                            {{ $stats['overview']['passed_attempts'] ?? 0 }}
                        </span>
                    </div>

                    <div class="flex items-center justify-between p-4 bg-red-50 border border-red-200 rounded-lg">
                        <div class="flex items-center">
                            <div class="p-2 bg-red-100 rounded-full ml-3">
                                <i class="fas fa-times text-red-600"></i>
                            </div>
                            <span class="text-red-800 font-medium">
                                {{ __('الطلاب الراسبون') }}
                            </span>
                        </div>
                        <span class="text-red-800 font-bold text-lg">
                            {{ $stats['overview']['failed_attempts'] ?? 0 }}
                        </span>
                    </div>

                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <div class="flex items-center">
                            <div class="p-2 bg-gray-100 rounded-full ml-3">
                                <i class="fas fa-arrow-down text-gray-600"></i>
                            </div>
                            <span class="text-gray-800 font-medium">
                                {{ __('أقل درجة') }}
                            </span>
                        </div>
                        <span class="text-gray-800 font-bold text-lg">
                            {{ number_format($stats['overview']['lowest_score'], 1) }}
                        </span>
                    </div>

                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <div class="flex items-center">
                            <div class="p-2 bg-gray-100 rounded-full ml-3">
                                <i class="fas fa-calculator text-gray-600"></i>
                            </div>
                            <span class="text-gray-800 font-medium">
                                {{ __('درجة النجاح المطلوبة') }}
                            </span>
                        </div>
                        <span class="text-gray-800 font-bold text-lg">
                            {{ $exam->passing_marks }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- معلومات الامتحان -->
        <div class="bg-white rounded-xl shadow-sm">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-info-circle ml-2"></i>
                    {{ __('معلومات الامتحان') }}
                </h3>
            </div>
            
            <div class="p-6">
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-gray-600">{{ __('عدد الأسئلة') }}</span>
                        <span class="text-gray-900 font-medium">
                            {{ $exam->examQuestions->count() }} {{ __('سؤال') }}
                        </span>
                    </div>

                    <div class="flex items-center justify-between">
                        <span class="text-gray-600">{{ __('إجمالي الدرجات') }}</span>
                        <span class="text-gray-900 font-medium">
                            {{ $exam->total_marks ?? $exam->calculateTotalMarks() }}
                        </span>
                    </div>

                    <div class="flex items-center justify-between">
                        <span class="text-gray-600">{{ __('مدة الامتحان') }}</span>
                        <span class="text-gray-900 font-medium">
                            {{ $exam->duration_minutes }} {{ __('دقيقة') }}
                        </span>
                    </div>

                    <div class="flex items-center justify-between">
                        <span class="text-gray-600">{{ __('عدد المحاولات المسموحة') }}</span>
                        <span class="text-gray-900 font-medium">
                            {{ $exam->attempts_allowed == 0 ? __('غير محدود') : $exam->attempts_allowed }}
                        </span>
                    </div>

                    <div class="flex items-center justify-between">
                        <span class="text-gray-600">{{ __('حالة الامتحان') }}</span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            {{ $exam->is_active ? 'bg-green-100 text-green-800 ': ''bg-red-100 text-red-800 }}">']
                            {{ $exam->is_active ? __('نشط') : __('غير نشط') }}
                        </span>
                    </div>

                    <div class="flex items-center justify-between">
                        <span class="text-gray-600">{{ __('حالة النشر') }}</span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            {{ $exam->is_published ? 'bg-blue-100 text-blue-800 ': ''bg-gray-100 text-gray-800 }}">']
                            {{ $exam->is_published ? __('منشور') : __('مسودة') }}
                        </span>
                    </div>

                    <div class="flex items-center justify-between">
                        <span class="text-gray-600">{{ __('تاريخ الإنشاء') }}</span>
                        <span class="text-gray-900 font-medium">
                            {{ $exam->created_at->format('d/m/Y H:i') }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
.statistics-content {
    font-family: 'IBM Plex Sans Arabic', sans-serif;
    line-height: 1.6;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // يمكن إضافة تفاعلات JavaScript هنا إذا لزم الأمر
    console.log('Statistics page loaded');
});
</script>
@endpush
