@extends('layouts.app')

@section('title', $academicYear->name . ' - المجموعات المهارية')
@section('header', 'مجموعات المهارات المتخصصة')

@section('content')
<div class="space-y-8">
    <div class="bg-white border border-gray-100 rounded-3xl shadow-xl overflow-hidden">
        <div class="px-6 py-8 sm:px-10 sm:py-10">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                <div class="space-y-4 max-w-3xl">
                    <span class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-sky-100 text-sky-700 text-sm font-semibold">
                        <i class="fas fa-route"></i>
                        مسار {{ $academicYear->name }}
                    </span>
                    <h1 class="text-3xl font-black text-gray-900">
                        اختر المجموعة المهارية التي تناسب هدفك داخل هذا المسار
                    </h1>
                    <p class="text-gray-600 text-lg">
                        قمنا بإعادة تنظيم المواد إلى مجموعات مهارية تركز على التقنيات والمهام العملية المطلوبة في سوق العمل. كل مجموعة تحتوي على كورسات مترابطة تساعدك على بناء مشروع متكامل أو احتراف تقنية محددة.
                    </p>
                </div>
                <a href="{{ route('academic-years') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-slate-900 text-white hover:bg-slate-700 transition-colors">
                    <i class="fas fa-arrow-right"></i>
                    العودة للمسارات
                </a>
            </div>
        </div>
    </div>

    @if($subjects->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
            @foreach($subjects as $index => $subject)
                @php
                    $stats = $subject->catalog_stats ?? [];
                    $coursesCount = $stats['courses_count'] ?? 0;
                    $languages = collect($stats['languages'] ?? []);
                    $frameworks = collect($stats['frameworks'] ?? []);
                    $levels = collect($stats['levels'] ?? []);
                    $previewCourses = $subject->preview_courses ?? collect();
                    $colorPalette = [
                        ['from' => 'from-sky-500', 'to' => 'to-indigo-600', 'icon' => 'fa-terminal'],
                        ['from' => 'from-emerald-500', 'to' => 'to-teal-600', 'icon' => 'fa-database'],
                        ['from' => 'from-purple-500', 'to' => 'to-pink-600', 'icon' => 'fa-brain'],
                        ['from' => 'from-amber-500', 'to' => 'to-orange-600', 'icon' => 'fa-robot'],
                        ['from' => 'from-rose-500', 'to' => 'to-fuchsia-600', 'icon' => 'fa-layer-group'],
                    ];
                    $palette = $colorPalette[$index % count($colorPalette)];
                @endphp
                <a href="{{ route('subjects.courses', $subject) }}" class="group relative bg-white border border-gray-100 rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 overflow-hidden">
                    <div class="absolute inset-0 opacity-0 group-hover:opacity-100 transition-opacity duration-500 bg-gradient-to-br {{ $palette['from'] }} {{ $palette['to'] }}/10 pointer-events-none"></div>
                    <div class="relative p-6 space-y-5">
                        <div class="flex items-start justify-between gap-4">
                            <div class="flex items-start gap-3">
                                <span class="inline-flex items-center justify-center w-12 h-12 rounded-xl bg-gradient-to-br {{ $palette['from'] }} {{ $palette['to'] }} text-white shadow-lg">
                                    <i class="fas {{ $palette['icon'] }} text-lg"></i>
                                </span>
                                <div class="space-y-2">
                                    <h2 class="text-lg font-bold text-gray-900">{{ $subject->name }}</h2>
                                    <p class="text-sm text-gray-500">
                                        {{ $subject->description ? Str::limit($subject->description, 110) : 'مجموعة تركز على إتقان مهارات محددة مع مشاريع تطبيقية عملية واختبارات تقييمية.' }}
                                    </p>
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="inline-flex items-center gap-2 text-sm font-semibold text-sky-600">
                                    تصفح المجموعة
                                    <i class="fas fa-arrow-left text-xs transition-transform group-hover:-translate-x-1"></i>
                                </span>
                            </div>
                        </div>

                        <div class="flex flex-wrap items-center gap-2">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-slate-100 text-slate-700 text-xs font-semibold">
                                <i class="fas fa-graduation-cap text-[10px]"></i>
                                {{ $coursesCount }} كورس متخصص
                            </span>
                            @if($languages->isNotEmpty())
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-sky-100 text-sky-700 text-xs font-semibold">
                                    <i class="fas fa-code text-[10px]"></i>
                                    {{ $languages->take(2)->implode(' • ') }}@if($languages->count() > 2) +{{ $languages->count() - 2 }}@endif
                                </span>
                            @endif
                            @if($frameworks->isNotEmpty())
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-indigo-100 text-indigo-700 text-xs font-semibold">
                                    <i class="fas fa-cubes text-[10px]"></i>
                                    {{ $frameworks->take(2)->implode(' • ') }}@if($frameworks->count() > 2) +{{ $frameworks->count() - 2 }}@endif
                                </span>
                            @endif
                        </div>

                        @if($languages->isNotEmpty() || $frameworks->isNotEmpty() || $levels->isNotEmpty())
                            <div class="bg-slate-50 rounded-xl border border-slate-100 p-4 space-y-3">
                                @if($languages->isNotEmpty())
                                    <div class="flex items-start gap-3">
                                        <span class="text-xs font-semibold text-gray-400 uppercase tracking-wide mt-1">اللغات</span>
                                        <div class="flex flex-wrap gap-2">
                                            @foreach($languages as $language)
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-white text-slate-600 border border-slate-200">
                                                    {{ $language }}
                                                </span>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                                @if($frameworks->isNotEmpty())
                                    <div class="flex items-start gap-3">
                                        <span class="text-xs font-semibold text-gray-400 uppercase tracking-wide mt-1">الأطر</span>
                                        <div class="flex flex-wrap gap-2">
                                            @foreach($frameworks as $framework)
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-white text-slate-600 border border-slate-200">
                                                    {{ $framework }}
                                                </span>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                                @if($levels->isNotEmpty())
                                    <div class="flex items-start gap-3">
                                        <span class="text-xs font-semibold text-gray-400 uppercase tracking-wide mt-1">المستويات</span>
                                        <div class="flex flex-wrap gap-2">
                                            @foreach($levels as $level)
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-slate-200 text-slate-700 capitalize">
                                                    {{ __($level) }}
                                                </span>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endif

                        @if($previewCourses->isNotEmpty())
                            <div class="space-y-2">
                                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide">كورسات مميزة في هذه المجموعة</p>
                                <div class="space-y-2">
                                    @foreach($previewCourses as $course)
                                        <div class="flex items-center justify-between gap-3 text-sm text-gray-600">
                                            <div class="flex items-center gap-2 truncate">
                                                <span class="w-2 h-2 rounded-full bg-gradient-to-br {{ $palette['from'] }} {{ $palette['to'] }}"></span>
                                                <span class="truncate">{{ $course->title }}</span>
                                            </div>
                                            <div class="flex items-center gap-2 text-xs text-gray-400">
                                                @if($course->programming_language)
                                                    <span><i class="fas fa-code ml-1"></i>{{ $course->programming_language }}</span>
                                                @endif
                                                @if($course->level)
                                                    <span><i class="fas fa-signal ml-1"></i>{{ $course->level }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </a>
            @endforeach
        </div>
    @else
        <div class="bg-white border border-gray-100 rounded-3xl shadow-xl p-12 text-center space-y-4">
            <div class="flex items-center justify-center">
                <span class="w-16 h-16 rounded-2xl bg-sky-100 text-sky-600 flex items-center justify-center text-2xl">
                    <i class="fas fa-layer-group"></i>
                </span>
            </div>
            <h3 class="text-2xl font-bold text-gray-900">لم يتم إعداد مجموعات مهارية بعد</h3>
            <p class="text-gray-500 max-w-xl mx-auto">
                لم يتم ربط هذا المسار بالكورسات البرمجية حتى الآن. تواصل مع فريق المنصة لإضافة مجموعات مهارات وتخصيص الكورسات المناسبة.
            </p>
            <a href="{{ route('academic-years') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-sky-600 text-white hover:bg-sky-700 transition-colors">
                <i class="fas fa-arrow-right"></i>
                العودة للمسارات
            </a>
        </div>
    @endif
</div>
@endsection









