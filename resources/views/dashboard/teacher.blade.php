@extends('layouts.app')

@section('title', 'لوحة تحكم المدرس')
@section('header', 'لوحة تحكم المدرس')

@section('content')
<div class="space-y-6">
    <!-- ترحيب شخصي -->
    <div class="bg-gradient-to-r from-blue-500 to-purple-600 rounded-xl shadow-lg p-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold">مرحباً، {{ auth()->user()->name }}</h2>
                <p class="text-blue-100 mt-1">إليك نظرة عامة على نشاطك التعليمي اليوم</p>
            </div>
            <div class="w-16 h-16 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                <i class="fas fa-chalkboard-teacher text-3xl"></i>
            </div>
        </div>
    </div>

    <!-- إحصائيات سريعة -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- كورساتي -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border border-gray-200 dark:border-gray-700 hover:shadow-xl transition-shadow duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">كورساتي</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ number_format($stats['my_courses']) }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center">
                    <i class="fas fa-book text-blue-600 dark:text-blue-400 text-xl"></i>
                </div>
            </div>
            <div class="mt-4">
                <a href="#" class="text-sm text-blue-600 dark:text-blue-400 hover:underline">إدارة الكورسات</a>
            </div>
        </div>

        <!-- طلابي -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border border-gray-200 dark:border-gray-700 hover:shadow-xl transition-shadow duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">إجمالي الطلاب</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ number_format($stats['total_students']) }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 dark:bg-green-900 rounded-lg flex items-center justify-center">
                    <i class="fas fa-user-graduate text-green-600 dark:text-green-400 text-xl"></i>
                </div>
            </div>
            <div class="mt-4">
                <a href="#" class="text-sm text-green-600 dark:text-green-400 hover:underline">عرض الطلاب</a>
            </div>
        </div>

        <!-- صفوفي -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border border-gray-200 dark:border-gray-700 hover:shadow-xl transition-shadow duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">صفوفي</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ number_format($stats['my_classrooms']) }}</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900 rounded-lg flex items-center justify-center">
                    <i class="fas fa-users text-purple-600 dark:text-purple-400 text-xl"></i>
                </div>
            </div>
            <div class="mt-4">
                <a href="#" class="text-sm text-purple-600 dark:text-purple-400 hover:underline">إدارة الصفوف</a>
            </div>
        </div>
    </div>

    <!-- كورساتي الحديثة -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- آخر الكورسات -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">كورساتي الحديثة</h3>
                    <a href="#" class="text-sm text-blue-600 dark:text-blue-400 hover:underline">عرض الكل</a>
                </div>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    @forelse($my_courses as $course)
                    <div class="flex items-start gap-4 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors duration-200">
                        <div class="w-12 h-12 bg-gradient-to-br from-blue-400 to-purple-600 rounded-lg flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-play text-white"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h4 class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $course->title }}</h4>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $course->subject->name ?? 'غير محدد' }}</p>
                            <div class="flex items-center gap-4 mt-2">
                                <span class="text-xs text-gray-500 dark:text-gray-400">
                                    <i class="fas fa-users ml-1"></i>
                                    {{ $course->students->count() }} طالب
                                </span>
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                    @if($course->status === 'published') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300
                                    @elseif($course->status === 'draft') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300
                                    @else bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300 @endif">
                                    @if($course->status === 'published') منشور
                                    @elseif($course->status === 'draft') مسودة
                                    @else مؤرشف @endif
                                </span>
                            </div>
                        </div>
                        <div class="flex flex-col gap-2">
                            <button class="p-2 text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="p-2 text-gray-400 hover:text-green-600 dark:hover:text-green-400 transition-colors">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                        <i class="fas fa-book text-3xl mb-2"></i>
                        <p>لم تقم بإنشاء أي كورسات بعد</p>
                        <a href="#" class="inline-flex items-center mt-3 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200">
                            <i class="fas fa-plus ml-2"></i>
                            إنشاء كورس جديد
                        </a>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- صفوفي -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">صفوفي الدراسية</h3>
                    <a href="#" class="text-sm text-blue-600 dark:text-blue-400 hover:underline">عرض الكل</a>
                </div>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    @forelse($my_classrooms as $classroom)
                    <div class="flex items-center gap-4 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors duration-200">
                        <div class="w-12 h-12 bg-gradient-to-br from-green-400 to-blue-600 rounded-lg flex items-center justify-center">
                            <i class="fas fa-school text-white"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h4 class="text-sm font-medium text-gray-900 dark:text-white">{{ $classroom->name }}</h4>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $classroom->description ?? 'لا يوجد وصف' }}</p>
                            <div class="flex items-center gap-4 mt-2">
                                <span class="text-xs text-gray-500 dark:text-gray-400">
                                    <i class="fas fa-users ml-1"></i>
                                    {{ $classroom->students->count() }} طالب
                                </span>
                                <span class="text-xs text-gray-500 dark:text-gray-400">
                                    <i class="fas fa-calendar ml-1"></i>
                                    {{ $classroom->created_at->format('Y/m/d') }}
                                </span>
                            </div>
                        </div>
                        <div class="flex flex-col gap-2">
                            <button class="p-2 text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                                <i class="fas fa-cog"></i>
                            </button>
                            <button class="p-2 text-gray-400 hover:text-green-600 dark:hover:text-green-400 transition-colors">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                        <i class="fas fa-school text-3xl mb-2"></i>
                        <p>لم يتم تعيينك لأي صف دراسي بعد</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- إجراءات سريعة -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border border-gray-200 dark:border-gray-700">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-6">إجراءات سريعة</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <a href="#" class="flex flex-col items-center p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg hover:bg-blue-100 dark:hover:bg-blue-900/30 transition-colors duration-200">
                <i class="fas fa-plus text-blue-600 dark:text-blue-400 text-2xl mb-2"></i>
                <span class="text-sm font-medium text-blue-700 dark:text-blue-300">إنشاء كورس</span>
            </a>
            
            <a href="#" class="flex flex-col items-center p-4 bg-green-50 dark:bg-green-900/20 rounded-lg hover:bg-green-100 dark:hover:bg-green-900/30 transition-colors duration-200">
                <i class="fas fa-tasks text-green-600 dark:text-green-400 text-2xl mb-2"></i>
                <span class="text-sm font-medium text-green-700 dark:text-green-300">إضافة واجب</span>
            </a>
            
            <a href="#" class="flex flex-col items-center p-4 bg-purple-50 dark:bg-purple-900/20 rounded-lg hover:bg-purple-100 dark:hover:bg-purple-900/30 transition-colors duration-200">
                <i class="fas fa-clipboard-check text-purple-600 dark:text-purple-400 text-2xl mb-2"></i>
                <span class="text-sm font-medium text-purple-700 dark:text-purple-300">إنشاء اختبار</span>
            </a>
            
            <a href="#" class="flex flex-col items-center p-4 bg-orange-50 dark:bg-orange-900/20 rounded-lg hover:bg-orange-100 dark:hover:bg-orange-900/30 transition-colors duration-200">
                <i class="fas fa-chart-line text-orange-600 dark:text-orange-400 text-2xl mb-2"></i>
                <span class="text-sm font-medium text-orange-700 dark:text-orange-300">تقارير الأداء</span>
            </a>
        </div>
    </div>

    <!-- جدول الأعمال اليومي -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700">
        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">جدول الأعمال اليوم</h3>
        </div>
        <div class="p-6">
            <div class="space-y-4">
                <!-- مهمة -->
                <div class="flex items-center gap-4 p-4 border border-gray-200 dark:border-gray-600 rounded-lg">
                    <div class="w-3 h-3 bg-blue-500 rounded-full"></div>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-900 dark:text-white">مراجعة واجبات الرياضيات</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">10:00 ص - 11:00 ص</p>
                    </div>
                    <button class="p-2 text-gray-400 hover:text-green-600 dark:hover:text-green-400 transition-colors">
                        <i class="fas fa-check"></i>
                    </button>
                </div>

                <!-- مهمة -->
                <div class="flex items-center gap-4 p-4 border border-gray-200 dark:border-gray-600 rounded-lg">
                    <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-900 dark:text-white">درس العلوم - الصف الثامن</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">2:00 م - 3:00 م</p>
                    </div>
                    <button class="p-2 text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                        <i class="fas fa-video"></i>
                    </button>
                </div>

                <!-- مهمة -->
                <div class="flex items-center gap-4 p-4 border border-gray-200 dark:border-gray-600 rounded-lg opacity-50">
                    <div class="w-3 h-3 bg-gray-400 rounded-full"></div>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-900 dark:text-white line-through">إعداد اختبار الكيمياء</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">مكتمل</p>
                    </div>
                    <div class="p-2 text-green-600 dark:text-green-400">
                        <i class="fas fa-check-circle"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
