@extends('layouts.app')

@section('title', 'مسارات التعلم')
@section('header', 'مسارات التعلم')

@section('content')
<div class="space-y-8">
    <!-- Hero Section -->
    <div class="bg-gradient-to-br from-sky-500 via-indigo-600 to-slate-700 rounded-3xl shadow-2xl overflow-hidden relative">
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_top,_rgba(255,255,255,0.12),_transparent_55%)]"></div>
        <div class="absolute -top-16 -left-16 w-56 h-56 bg-white/10 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-20 -right-20 w-72 h-72 bg-indigo-400/20 rounded-full blur-3xl"></div>
        <div class="relative z-10 px-6 py-10 sm:px-10 sm:py-12 lg:px-14 lg:py-14 text-white space-y-6">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                <div class="space-y-4 max-w-3xl">
                    <span class="inline-flex items-center gap-2 bg-white/15 text-white px-4 py-2 rounded-full text-sm font-medium backdrop-blur-md">
                        <i class="fas fa-route"></i>
                        اكتشف مسارات Mindlytics التعليمية
                    </span>
                    <h1 class="text-3xl sm:text-4xl lg:text-5xl font-black leading-tight">
                        اختر مسار التعلم الأنسب لك واستعد لبناء مشروعك التقني القادم
                    </h1>
                    <p class="text-white/80 text-lg sm:text-xl max-w-2xl">
                        قمنا بتحويل التقسيمات التقليدية إلى مسارات احترافية تجمع المهارات والأدوات المطلوبة في سوق العمل البرمجي. كل مسار يحتوي على مجموعات مهارية متكاملة تساعدك على الانتقال من مستوى المبتدئ إلى المحترف.
                    </p>
                </div>
                <div class="bg-white/10 rounded-2xl backdrop-blur-md p-6 border border-white/20 shadow-lg w-full max-w-xs">
                    <div class="space-y-4">
                        <div>
                            <p class="text-sm text-white/70">إجمالي المسارات</p>
                            <p class="text-3xl font-bold">{{ $tracks->count() }}</p>
                        </div>
                        <div class="border-t border-white/10 pt-4">
                            <p class="text-sm text-white/70">مجموعات المهارات</p>
                            <p class="text-xl font-semibold">{{ $tracks->sum('academic_subjects_count') }}</p>
                        </div>
                        <div class="border-t border-white/10 pt-4">
                            <p class="text-sm text-white/70">أحدث الكورسات</p>
                            <p class="text-xl font-semibold">
                                {{ $tracks->sum(fn($track) => optional($track->track_metrics)['courses_count'] ?? 0) }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="flex flex-wrap gap-3">
                <span class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white/15 text-white text-sm font-medium backdrop-blur-sm">
                    <i class="fas fa-laptop-code text-emerald-300"></i>
                    مشاريع تطبيقية واقعية
                </span>
                <span class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white/15 text-white text-sm font-medium backdrop-blur-sm">
                    <i class="fas fa-layer-group text-amber-300"></i>
                    مستويات متدرجة من المبتدئ للاحترافي
                </span>
                <span class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white/15 text-white text-sm font-medium backdrop-blur-sm">
                    <i class="fas fa-code-branch text-sky-300"></i>
                    تكامل مع لغات وأطر عمل حديثة
                </span>
            </div>
        </div>
    </div>

    <!-- Tracks -->
    @if($tracks->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
            @foreach($tracks as $index => $track)
                @php
                    $metrics = $track->track_metrics ?? [];
                    $languages = collect($metrics['languages'] ?? []);
                    $frameworks = collect($metrics['frameworks'] ?? []);
                    $levels = collect($metrics['levels'] ?? []);
                    $coursesCount = $metrics['courses_count'] ?? 0;
                    $previewCourses = $track->preview_courses ?? collect();
                    $colorPalette = [
                        ['from' => 'from-sky-500', 'to' => 'to-slate-600', 'icon' => 'fa-globe'],
                        ['from' => 'from-emerald-500', 'to' => 'to-teal-600', 'icon' => 'fa-database'],
                        ['from' => 'from-purple-500', 'to' => 'to-pink-600', 'icon' => 'fa-layer-group'],
                        ['from' => 'from-amber-500', 'to' => 'to-orange-600', 'icon' => 'fa-robot'],
                        ['from' => 'from-indigo-500', 'to' => 'to-sky-600', 'icon' => 'fa-cloud'],
                        ['from' => 'from-rose-500', 'to' => 'to-fuchsia-600', 'icon' => 'fa-bolt'],
                    ];
                    $palette = $colorPalette[$index % count($colorPalette)];
                @endphp
                <a href="{{ route('academic-years.subjects', $track) }}"
                   class="group relative bg-white border border-gray-100 rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 overflow-hidden">
                    <div class="absolute inset-0 opacity-0 group-hover:opacity-100 transition-opacity duration-500 bg-gradient-to-br {{ $palette['from'] }} {{ $palette['to'] }}/10 pointer-events-none"></div>
                    <div class="relative p-6 space-y-6">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <div class="flex items-center gap-3 mb-3">
                                    <span class="inline-flex items-center justify-center w-12 h-12 rounded-xl bg-gradient-to-br {{ $palette['from'] }} {{ $palette['to'] }} text-white shadow-lg">
                                        <i class="fas {{ $palette['icon'] }} text-lg"></i>
                                    </span>
                                    <div>
                                        <h2 class="text-xl font-bold text-gray-900">{{ $track->name }}</h2>
                                        <p class="text-sm text-gray-500">
                                            {{ $track->description ? Str::limit($track->description, 90) : 'مسار متكامل يجمع بين المهارات المطلوبة لتطوير حلول برمجية حديثة.' }}
                                        </p>
                                    </div>
                                </div>
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-slate-100 text-slate-700 text-xs font-semibold">
                                        <i class="fas fa-layer-group text-[10px]"></i>
                                        {{ $track->academic_subjects_count }} مجموعة مهارات
                                    </span>
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-sky-100 text-sky-700 text-xs font-semibold">
                                        <i class="fas fa-graduation-cap text-[10px]"></i>
                                        {{ $coursesCount }} كورس متخصص
                                    </span>
                                    @if(!empty($metrics['avg_duration']))
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-100 text-emerald-700 text-xs font-semibold">
                                            <i class="fas fa-clock text-[10px]"></i>
                                            مدة متوسطة {{ $metrics['avg_duration'] }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="inline-flex items-center gap-2 text-sm font-semibold text-sky-600">
                                    استعرض المسار
                                    <i class="fas fa-arrow-left text-xs transition-transform group-hover:-translate-x-1"></i>
                                </span>
                            </div>
                        </div>

                        @if($languages->isNotEmpty() || $frameworks->isNotEmpty() || $levels->isNotEmpty())
                            <div class="space-y-3">
                                @if($languages->isNotEmpty())
                                    <div class="flex items-start gap-3">
                                        <span class="text-xs font-semibold text-gray-400 uppercase tracking-wide mt-1">اللغات</span>
                                        <div class="flex flex-wrap gap-2">
                                            @foreach($languages as $language)
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-700">
                                                    <i class="fas fa-code ml-1 text-[10px] text-sky-500"></i>
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
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-700">
                                                    <i class="fas fa-cubes ml-1 text-[10px] text-indigo-500"></i>
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
                            <div class="border-t border-gray-100 pt-4">
                                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-3">أبرز الكورسات داخل المسار</p>
                                <div class="space-y-3">
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
                    <i class="fas fa-compass"></i>
                </span>
            </div>
            <h3 class="text-2xl font-bold text-gray-900">لم يتم إعداد المسارات بعد</h3>
            <p class="text-gray-500 max-w-xl mx-auto">
                لم يتم ربط المسارات التعليمية بالكورسات البرمجية حتى الآن. تواصل مع فريق المنصة لإضافة المسارات وتوزيع الكورسات عليها.
            </p>
            <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-sky-600 text-white hover:bg-sky-700 transition-colors">
                <i class="fas fa-arrow-right"></i>
                العودة للوحة التحكم
            </a>
        </div>
    @endif
</div>
@endsection