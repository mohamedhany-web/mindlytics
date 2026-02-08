@extends('layouts.app')

@section('title', 'لوحة تحكم ولي الأمر')
@section('header', 'لوحة تحكم ولي الأمر')

@section('content')
<div class="space-y-6">
    <!-- ترحيب شخصي -->
    <div class="bg-gradient-to-r from-purple-500 to-pink-600 rounded-xl shadow-lg p-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold">مرحباً، {{ auth()->user()->name }}</h2>
                <p class="text-purple-100 mt-1">تابع تقدم أطفالك التعليمي وكن جزءاً من رحلتهم</p>
            </div>
            <div class="w-16 h-16 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                <i class="fas fa-users text-3xl"></i>
            </div>
        </div>
    </div>

    <!-- إحصائيات سريعة -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- عدد الأطفال -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border border-gray-200 dark:border-gray-700 hover:shadow-xl transition-shadow duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">أطفالي</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ number_format($stats['total_children']) }}</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900 rounded-lg flex items-center justify-center">
                    <i class="fas fa-child text-purple-600 dark:text-purple-400 text-xl"></i>
                </div>
            </div>
            <div class="mt-4">
                <a href="#" class="text-sm text-purple-600 dark:text-purple-400 hover:underline">إدارة الأطفال</a>
            </div>
        </div>

        <!-- إجمالي الكورسات -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border border-gray-200 dark:border-gray-700 hover:shadow-xl transition-shadow duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">إجمالي الكورسات</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ number_format($stats['total_courses']) }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center">
                    <i class="fas fa-book-open text-blue-600 dark:text-blue-400 text-xl"></i>
                </div>
            </div>
            <div class="mt-4">
                <a href="#" class="text-sm text-blue-600 dark:text-blue-400 hover:underline">عرض التقدم</a>
            </div>
        </div>

        <!-- إجمالي الصفوف -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border border-gray-200 dark:border-gray-700 hover:shadow-xl transition-shadow duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">إجمالي الصفوف</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ number_format($stats['total_classrooms']) }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 dark:bg-green-900 rounded-lg flex items-center justify-center">
                    <i class="fas fa-school text-green-600 dark:text-green-400 text-xl"></i>
                </div>
            </div>
            <div class="mt-4">
                <a href="#" class="text-sm text-green-600 dark:text-green-400 hover:underline">تفاصيل الصفوف</a>
            </div>
        </div>
    </div>

    <!-- أطفالي -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700">
        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">أطفالي</h3>
                <button class="text-sm text-purple-600 dark:text-purple-400 hover:underline">إضافة طفل</button>
            </div>
        </div>
        <div class="p-6">
            @forelse($children as $child)
            <div class="mb-6 last:mb-0">
                <!-- معلومات الطفل -->
                <div class="flex items-center gap-4 mb-4 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                    <div class="w-16 h-16 bg-gradient-to-br from-blue-400 to-purple-600 rounded-full flex items-center justify-center text-white text-xl font-bold">
                        {{ substr($child->name, 0, 1) }}
                    </div>
                    <div class="flex-1">
                        <h4 class="text-lg font-medium text-gray-900 dark:text-white">{{ $child->name }}</h4>
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $child->phone }}</p>
                        <div class="flex items-center gap-4 mt-2">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300">
                                <i class="fas fa-circle text-green-500 text-xs ml-1"></i>
                                نشط
                            </span>
                            <span class="text-xs text-gray-500 dark:text-gray-400">
                                انضم في {{ $child->created_at->format('Y/m/d') }}
                            </span>
                        </div>
                    </div>
                    <div class="text-right">
                        <button class="p-2 text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>

                <!-- إحصائيات الطفل -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                    <!-- الكورسات -->
                    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-lg p-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-600 dark:text-gray-400">الكورسات</p>
                                <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $child->enrolledCourses->count() }}</p>
                            </div>
                            <i class="fas fa-book text-blue-600 dark:text-blue-400"></i>
                        </div>
                    </div>

                    <!-- الصفوف -->
                    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-lg p-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-600 dark:text-gray-400">الصفوف</p>
                                <p class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $child->classrooms->count() }}</p>
                            </div>
                            <i class="fas fa-school text-green-600 dark:text-green-400"></i>
                        </div>
                    </div>

                    <!-- التقدم العام -->
                    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-lg p-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-600 dark:text-gray-400">التقدم العام</p>
                                @php
                                    $averageProgress = $child->enrolledCourses->avg('pivot.progress_percentage') ?? 0;
                                @endphp
                                <p class="text-2xl font-bold text-purple-600 dark:text-purple-400">{{ round($averageProgress) }}%</p>
                            </div>
                            <i class="fas fa-chart-line text-purple-600 dark:text-purple-400"></i>
                        </div>
                    </div>
                </div>

                <!-- كورسات الطفل -->
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                    <h5 class="text-sm font-medium text-gray-900 dark:text-white mb-3">الكورسات الحالية</h5>
                    @if($child->enrolledCourses->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($child->enrolledCourses->take(4) as $course)
                            <div class="bg-white dark:bg-gray-800 rounded-lg p-3 border border-gray-200 dark:border-gray-600">
                                <div class="flex items-start gap-3">
                                    <div class="w-10 h-10 bg-gradient-to-br from-blue-400 to-purple-600 rounded-lg flex items-center justify-center flex-shrink-0">
                                        <i class="fas fa-play text-white text-sm"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h6 class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $course->title }}</h6>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $course->subject->name ?? 'غير محدد' }}</p>
                                        
                                        <!-- شريط التقدم -->
                                        @php
                                            $progress = $course->pivot->progress_percentage ?? 0;
                                        @endphp
                                        <div class="mt-2">
                                            <div class="flex items-center justify-between text-xs mb-1">
                                                <span class="text-gray-600 dark:text-gray-400">التقدم</span>
                                                <span class="font-medium">{{ $progress }}%</span>
                                            </div>
                                            <div class="w-full bg-gray-200 dark:bg-gray-600 rounded-full h-1.5">
                                                <div class="bg-blue-600 h-1.5 rounded-full transition-all duration-300" style="width: {{ $progress }}%"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @if($child->enrolledCourses->count() > 4)
                            <div class="mt-4 text-center">
                                <a href="#" class="text-sm text-blue-600 dark:text-blue-400 hover:underline">
                                    عرض جميع الكورسات ({{ $child->enrolledCourses->count() }})
                                </a>
                            </div>
                        @endif
                    @else
                        <div class="text-center py-6 text-gray-500 dark:text-gray-400">
                            <i class="fas fa-book-open text-2xl mb-2"></i>
                            <p class="text-sm">لم يسجل في أي كورسات بعد</p>
                        </div>
                    @endif
                </div>
            </div>
            @empty
            <div class="text-center py-12 text-gray-500 dark:text-gray-400">
                <i class="fas fa-child text-4xl mb-4"></i>
                <h4 class="text-lg font-medium mb-2">لم تقم بإضافة أي أطفال بعد</h4>
                <p class="text-sm mb-4">قم بإضافة أطفالك لمتابعة تقدمهم التعليمي</p>
                <button class="inline-flex items-center px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors duration-200">
                    <i class="fas fa-plus ml-2"></i>
                    إضافة طفل
                </button>
            </div>
            @endforelse
        </div>
    </div>

    <!-- تقارير الأداء -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- الأداء الأسبوعي -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">الأداء الأسبوعي</h3>
            </div>
            <div class="p-6">
                <!-- مساحة للرسم البياني -->
                <div class="h-48 bg-gray-50 dark:bg-gray-700 rounded-lg flex items-center justify-center">
                    <div class="text-center text-gray-500 dark:text-gray-400">
                        <i class="fas fa-chart-bar text-3xl mb-2"></i>
                        <p>سيتم إضافة الرسم البياني قريباً</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- التواصل مع المدرسين -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">الرسائل الحديثة</h3>
                    <a href="#" class="text-sm text-blue-600 dark:text-blue-400 hover:underline">عرض الكل</a>
                </div>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    <!-- رسالة -->
                    <div class="flex items-start gap-4 p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                        <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center text-white text-sm font-medium">
                            أ
                        </div>
                        <div class="flex-1 min-w-0">
                            <h4 class="text-sm font-medium text-gray-900 dark:text-white">أ. أحمد محمد</h4>
                            <p class="text-xs text-gray-500 dark:text-gray-400">مدرس الرياضيات</p>
                            <p class="text-sm text-gray-700 dark:text-gray-300 mt-1">أحمد يُظهر تحسناً ملحوظاً في حل المسائل الرياضية</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">منذ ساعتين</p>
                        </div>
                        <button class="p-1 text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                            <i class="fas fa-reply"></i>
                        </button>
                    </div>

                    <!-- رسالة -->
                    <div class="flex items-start gap-4 p-3 bg-green-50 dark:bg-green-900/20 rounded-lg border border-green-200 dark:border-green-800">
                        <div class="w-10 h-10 bg-green-500 rounded-full flex items-center justify-center text-white text-sm font-medium">
                            س
                        </div>
                        <div class="flex-1 min-w-0">
                            <h4 class="text-sm font-medium text-gray-900 dark:text-white">أ. سارة أحمد</h4>
                            <p class="text-xs text-gray-500 dark:text-gray-400">مدرسة العلوم</p>
                            <p class="text-sm text-gray-700 dark:text-gray-300 mt-1">فاطمة متفوقة في التجارب العملية</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">أمس</p>
                        </div>
                        <button class="p-1 text-gray-400 hover:text-green-600 dark:hover:text-green-400 transition-colors">
                            <i class="fas fa-reply"></i>
                        </button>
                    </div>

                    <!-- رسالة -->
                    <div class="flex items-start gap-4 p-3 bg-orange-50 dark:bg-orange-900/20 rounded-lg border border-orange-200 dark:border-orange-800">
                        <div class="w-10 h-10 bg-orange-500 rounded-full flex items-center justify-center text-white text-sm font-medium">
                            م
                        </div>
                        <div class="flex-1 min-w-0">
                            <h4 class="text-sm font-medium text-gray-900 dark:text-white">أ. محمد علي</h4>
                            <p class="text-xs text-gray-500 dark:text-gray-400">مدرس اللغة العربية</p>
                            <p class="text-sm text-gray-700 dark:text-gray-300 mt-1">يرجى متابعة حل الواجبات المنزلية</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">منذ 3 أيام</p>
                        </div>
                        <button class="p-1 text-gray-400 hover:text-orange-600 dark:hover:text-orange-400 transition-colors">
                            <i class="fas fa-reply"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- إجراءات سريعة -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border border-gray-200 dark:border-gray-700">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-6">إجراءات سريعة</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <a href="#" class="flex flex-col items-center p-4 bg-purple-50 dark:bg-purple-900/20 rounded-lg hover:bg-purple-100 dark:hover:bg-purple-900/30 transition-colors duration-200">
                <i class="fas fa-user-plus text-purple-600 dark:text-purple-400 text-2xl mb-2"></i>
                <span class="text-sm font-medium text-purple-700 dark:text-purple-300">إضافة طفل</span>
            </a>
            
            <a href="#" class="flex flex-col items-center p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg hover:bg-blue-100 dark:hover:bg-blue-900/30 transition-colors duration-200">
                <i class="fas fa-chart-line text-blue-600 dark:text-blue-400 text-2xl mb-2"></i>
                <span class="text-sm font-medium text-blue-700 dark:text-blue-300">تقارير مفصلة</span>
            </a>
            
            <a href="#" class="flex flex-col items-center p-4 bg-green-50 dark:bg-green-900/20 rounded-lg hover:bg-green-100 dark:hover:bg-green-900/30 transition-colors duration-200">
                <i class="fas fa-comments text-green-600 dark:text-green-400 text-2xl mb-2"></i>
                <span class="text-sm font-medium text-green-700 dark:text-green-300">التواصل</span>
            </a>
            
            <a href="#" class="flex flex-col items-center p-4 bg-orange-50 dark:bg-orange-900/20 rounded-lg hover:bg-orange-100 dark:hover:bg-orange-900/30 transition-colors duration-200">
                <i class="fas fa-calendar text-orange-600 dark:text-orange-400 text-2xl mb-2"></i>
                <span class="text-sm font-medium text-orange-700 dark:text-orange-300">المواعيد</span>
            </a>
        </div>
    </div>
</div>
@endsection
