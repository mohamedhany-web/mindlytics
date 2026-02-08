@extends('layouts.admin')

@section('title', 'لوحة المؤشرات الرئيسية')
@section('header', 'لوحة المؤشرات الرئيسية')

@section('content')
@php
    $primaryCards = [
        [
            'label' => 'إجمالي المستخدمين',
            'value' => number_format($totalUsers),
            'icon' => 'fas fa-users',
            'color' => 'blue',
            'footer' => "+$newUsersThisMonth مستخدم جديد هذا الشهر",
        ],
        [
            'label' => 'الطلاب النشطون',
            'value' => number_format($totalStudents),
            'icon' => 'fas fa-user-graduate',
            'color' => 'emerald',
            'footer' => round(($totalStudents / max($totalUsers, 1)) * 100, 1) . '% من إجمالي المستخدمين',
        ],
        [
            'label' => 'المدربون',
            'value' => number_format($totalTeachers),
            'icon' => 'fas fa-chalkboard-teacher',
            'color' => 'amber',
            'footer' => round(($totalTeachers / max($totalUsers, 1)) * 100, 1) . '% من إجمالي المستخدمين',
        ],
        [
            'label' => 'الكورسات النشطة',
            'value' => number_format($totalCourses),
            'icon' => 'fas fa-code',
            'color' => 'purple',
            'footer' => number_format($totalEnrollments) . ' تسجيل إجمالي',
        ],
    ];

    $secondaryCards = [
        [
            'label' => 'مسارات التعلم',
            'value' => number_format($totalAcademicYears),
            'icon' => 'fas fa-route',
            'color' => 'indigo',
        ],
        [
            'label' => 'مجموعات المهارات',
            'value' => number_format($totalSubjects),
            'icon' => 'fas fa-layer-group',
            'color' => 'rose',
        ],
        [
            'label' => 'تسجيلات هذا الشهر',
            'value' => number_format($newEnrollmentsThisMonth),
            'icon' => 'fas fa-user-plus',
            'color' => 'teal',
        ],
    ];

    $activityLogRoute = \Illuminate\Support\Facades\Route::has('admin.activity-log')
        ? route('admin.activity-log')
        : null;
@endphp

<div class="space-y-6">
    <!-- الهيدر -->
    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-6 py-5 bg-slate-50 border-b border-slate-200 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white shadow-md">
                    <i class="fas fa-chart-bar text-lg"></i>
                </div>
                <div>
                    <h2 class="text-2xl font-black text-slate-900">لوحة المؤشرات الرئيسية</h2>
                    <p class="text-sm text-slate-600 mt-1">عرض سريع لحالة المنصة ونموها عبر المستخدمين والمحتوى والتسجيلات.</p>
                </div>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('admin.statistics.users') }}" class="inline-flex items-center gap-2 rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition-colors">
                    <i class="fas fa-users"></i>
                    إحصائيات المستخدمين
                </a>
                <a href="{{ route('admin.statistics.courses') }}" class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-blue-600 to-blue-500 hover:from-blue-700 hover:to-blue-600 px-4 py-2.5 text-sm font-semibold text-white shadow-md hover:shadow-lg transition-all duration-200">
                    <i class="fas fa-code"></i>
                    إحصائيات الكورسات
                </a>
            </div>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 p-6">
            @foreach ($primaryCards as $card)
                <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex-1">
                            <p class="text-xs font-semibold text-slate-600 mb-1">{{ $card['label'] }}</p>
                            <p class="text-2xl font-black text-slate-900">{{ $card['value'] }}</p>
                        </div>
                        <div class="w-12 h-12 rounded-lg bg-{{ $card['color'] }}-100 flex items-center justify-center text-{{ $card['color'] }}-600 shadow-sm">
                            <i class="{{ $card['icon'] }} text-lg"></i>
                        </div>
                    </div>
                    <p class="text-xs text-slate-600 mt-2 pt-2 border-t border-slate-200">{{ htmlspecialchars($card['footer'], ENT_QUOTES, 'UTF-8') }}</p>
                </div>
            @endforeach
        </div>
    </section>

    <!-- تفاصيل إضافية -->
    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-200 bg-slate-50">
            <h3 class="text-lg font-black text-slate-900 mb-2 flex items-center gap-2">
                <i class="fas fa-info-circle text-blue-600"></i>
                تفاصيل إضافية
            </h3>
            <p class="text-xs text-slate-600 mt-1">توزيع المسارات التعليمية وشدة النشاط خلال الشهر الحالي.</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 p-6">
            @foreach ($secondaryCards as $card)
                <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <p class="text-xs font-semibold text-slate-600 mb-1">{{ $card['label'] }}</p>
                            <p class="text-xl font-black text-slate-900">{{ $card['value'] }}</p>
                        </div>
                        <div class="w-10 h-10 rounded-lg bg-{{ $card['color'] }}-100 flex items-center justify-center text-{{ $card['color'] }}-600 shadow-sm">
                            <i class="{{ $card['icon'] }} text-base"></i>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    <!-- أكثر الكورسات تسجيلاً وآخر النشاطات -->
    <section class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- أكثر الكورسات تسجيلاً -->
        <div class="rounded-xl bg-white border border-slate-200 shadow-lg overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-200 bg-slate-50 flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-black text-slate-900 mb-2 flex items-center gap-2">
                        <i class="fas fa-trophy text-blue-600"></i>
                        أكثر الكورسات تسجيلاً
                    </h3>
                    <p class="text-xs text-slate-600 mt-1">أكثر المسارات جذباً للطلاب خلال الفترة الحالية.</p>
                </div>
            </div>
            <div class="p-6">
                @if ($popularCourses->count() > 0)
                    <div class="space-y-3">
                        @foreach ($popularCourses as $course)
                            <div class="rounded-lg border border-slate-200 bg-white p-4 hover:border-blue-300 hover:shadow-md transition-all duration-200">
                                <div class="flex items-center justify-between mb-2">
                                    <h4 class="text-sm font-bold text-slate-900">{{ htmlspecialchars($course->title, ENT_QUOTES, 'UTF-8') }}</h4>
                                    <span class="inline-flex items-center gap-1.5 rounded-lg bg-blue-100 px-3 py-1 text-xs font-semibold text-blue-700 border border-blue-200">
                                        <i class="fas fa-user-friends"></i>
                                        {{ number_format($course->enrollments_count) }} طالب
                                    </span>
                                </div>
                                <p class="text-xs text-slate-600">
                                    {{ htmlspecialchars($course->academicYear->name ?? 'غير محدد', ENT_QUOTES, 'UTF-8') }} • 
                                    {{ htmlspecialchars($course->academicSubject->name ?? 'غير محدد', ENT_QUOTES, 'UTF-8') }}
                                </p>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="rounded-lg border border-slate-200 bg-slate-50 p-8 text-center">
                        <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center mx-auto mb-3">
                            <i class="fas fa-chart-line text-xl"></i>
                        </div>
                        <p class="text-sm font-semibold text-slate-700 mb-1">لا توجد بيانات</p>
                        <p class="text-xs text-slate-600">لا توجد بيانات متاحة حالياً.</p>
                    </div>
                @endif
            </div>
        </div>
        <!-- آخر النشاطات -->
        <div class="rounded-xl bg-white border border-slate-200 shadow-lg overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-200 bg-slate-50 flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-black text-slate-900 mb-2 flex items-center gap-2">
                        <i class="fas fa-clock text-blue-600"></i>
                        آخر النشاطات
                    </h3>
                    <p class="text-xs text-slate-600 mt-1">تحركات الفريق خلال الساعات القليلة الماضية.</p>
                </div>
            </div>
            <div class="p-6">
                @if ($recentActivities->count() > 0)
                    <div class="space-y-3">
                        @foreach ($recentActivities as $activity)
                            <div class="flex items-start gap-3 rounded-lg border border-slate-200 bg-white p-4 hover:shadow-md transition-shadow">
                                <div class="w-9 h-9 flex-shrink-0 items-center justify-center rounded-lg bg-blue-100 text-blue-600 flex">
                                    <i class="fas fa-user text-sm"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm text-slate-900">
                                        <span class="font-bold">{{ htmlspecialchars($activity->user->name ?? 'مستخدم مجهول', ENT_QUOTES, 'UTF-8') }}</span> 
                                        {{ htmlspecialchars($activity->description ?? 'نشاط', ENT_QUOTES, 'UTF-8') }}
                                    </p>
                                    <p class="text-xs text-slate-600 mt-1">{{ $activity->created_at->diffForHumans() }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    @if ($activityLogRoute)
                        <div class="mt-5 text-center pt-5 border-t border-slate-200">
                            <a href="{{ $activityLogRoute }}" class="inline-flex items-center gap-2 text-sm font-semibold text-blue-600 hover:text-blue-700 transition-colors">
                                <i class="fas fa-list"></i>
                                استعرض كامل السجل
                                <i class="fas fa-arrow-left text-xs"></i>
                            </a>
                        </div>
                    @endif
                @else
                    <div class="rounded-lg border border-slate-200 bg-slate-50 p-8 text-center">
                        <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center mx-auto mb-3">
                            <i class="fas fa-bell-slash text-xl"></i>
                        </div>
                        <p class="text-sm font-semibold text-slate-700 mb-1">لا توجد نشاطات</p>
                        <p class="text-xs text-slate-600">لا توجد نشاطات حديثة.</p>
                    </div>
                @endif
            </div>
        </div>
    </section>
</div>
@endsection

