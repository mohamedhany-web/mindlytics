@extends('layouts.app')

@section('title', 'الأنماط التعليمية')
@section('header', 'الأنماط التعليمية')

@section('content')
<div class="space-y-6">
    <div class="rounded-2xl p-5 sm:p-6 bg-white border border-slate-200 shadow-sm">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">الأنماط التعليمية التفاعلية</h1>
                <p class="text-sm text-slate-500 mt-0.5">{{ $course->title }}</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('instructor.courses.curriculum', $course) }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-semibold transition-colors">
                    <i class="fas fa-arrow-right ml-2"></i> العودة للمنهج
                </a>
                <a href="{{ route('instructor.learning-patterns.create', $course) }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-sky-500 hover:bg-sky-600 text-white rounded-xl font-semibold transition-colors">
                    <i class="fas fa-plus ml-2"></i> إضافة نمط جديد
                </a>
            </div>
        </div>
    </div>

    @if($patterns->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
            @foreach($patterns as $pattern)
                @php $typeInfo = $pattern->getTypeInfo(); @endphp
                <div class="rounded-xl bg-white border border-slate-200 shadow-sm hover:border-sky-300 hover:shadow-md transition-all overflow-hidden flex flex-col">
                    <div class="p-5 flex-1 flex flex-col">
                        <div class="flex items-start justify-between gap-2 mb-3">
                            <div class="flex items-center gap-3 flex-1 min-w-0">
                                <div class="w-10 h-10 rounded-xl bg-amber-100 flex items-center justify-center text-amber-600 shrink-0">
                                    <i class="{{ $typeInfo['icon'] ?? 'fas fa-puzzle-piece' }} text-sm"></i>
                                </div>
                                <div class="min-w-0">
                                    <h3 class="font-bold text-slate-800 truncate">{{ $pattern->title }}</h3>
                                    <p class="text-xs text-slate-500">{{ $typeInfo['name'] ?? 'نمط تعليمي' }}</p>
                                </div>
                            </div>
                            <div class="flex gap-1.5 shrink-0">
                                <a href="{{ route('instructor.learning-patterns.show', [$course, $pattern]) }}" class="p-2 rounded-lg bg-sky-100 hover:bg-sky-200 text-sky-600 transition-colors" title="عرض"><i class="fas fa-eye text-xs"></i></a>
                                <a href="{{ route('instructor.learning-patterns.edit', [$course, $pattern]) }}" class="p-2 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 transition-colors" title="تعديل"><i class="fas fa-edit text-xs"></i></a>
                                <form action="{{ route('instructor.learning-patterns.destroy', [$course, $pattern]) }}" method="POST" class="inline" onsubmit="return confirm('هل أنت متأكد من حذف هذا النمط؟');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 rounded-lg bg-red-50 hover:bg-red-100 text-red-600 transition-colors" title="حذف"><i class="fas fa-trash-alt text-xs"></i></button>
                                </form>
                            </div>
                        </div>
                        @if($pattern->description)
                            <p class="text-sm text-slate-600 mb-3 line-clamp-2">{{ $pattern->description }}</p>
                        @endif
                        <div class="flex flex-wrap gap-2 mb-3">
                            <span class="px-2 py-1 bg-sky-50 text-sky-700 rounded-lg text-xs font-semibold"><i class="fas fa-star ml-1"></i> {{ $pattern->points }}</span>
                            <span class="px-2 py-1 bg-slate-100 text-slate-600 rounded-lg text-xs font-semibold">مستوى {{ $pattern->difficulty_level }}/5</span>
                            @if($pattern->is_required)
                                <span class="px-2 py-1 bg-amber-100 text-amber-700 rounded-lg text-xs font-semibold">إلزامي</span>
                            @endif
                        </div>
                        <div class="flex items-center gap-3 text-xs text-slate-500 mt-auto pt-3 border-t border-slate-100">
                            <span><i class="fas fa-redo ml-1"></i> {{ $pattern->total_attempts }} محاولة</span>
                            <span><i class="fas fa-check-circle ml-1"></i> {{ $pattern->total_completions }} إكمال</span>
                        </div>
                    </div>
                    <div class="px-5 py-3 bg-slate-50/80 border-t border-slate-100">
                        <a href="{{ route('instructor.learning-patterns.show', [$course, $pattern]) }}" class="block w-full text-center px-3 py-2 bg-sky-500 hover:bg-sky-600 text-white rounded-lg font-semibold text-sm transition-colors">
                            <i class="fas fa-eye ml-1"></i> عرض التفاصيل
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="rounded-xl border border-dashed border-slate-200 bg-slate-50/50 py-12 text-center">
            <div class="w-16 h-16 rounded-2xl bg-amber-100 flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-puzzle-piece text-2xl text-amber-500"></i>
            </div>
            <h3 class="text-lg font-bold text-slate-800 mb-2">لا توجد أنماط تعليمية</h3>
            <p class="text-sm text-slate-500 mb-4">أنشئ نمطاً تفاعلياً (اختبار، بطاقات، تحدي برمجي، إلخ) لطلابك</p>
            <a href="{{ route('instructor.learning-patterns.create', $course) }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-sky-500 hover:bg-sky-600 text-white rounded-xl font-semibold transition-colors">
                <i class="fas fa-plus"></i> إضافة نمط جديد
            </a>
        </div>
    @endif
</div>
@endsection
