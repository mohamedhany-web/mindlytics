@extends('layouts.admin')

@section('title', 'لوحة الإدارة - Mindlytics')

@section('content')
<div class="p-3 sm:p-4 md:p-6 space-y-4 sm:space-y-6" style="background: #f8fafc; min-height: 100vh;">
    <!-- إحصائيات سريعة -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
        <!-- إجمالي المستخدمين -->
        @php $usersMetric = $metrics['users'] ?? null; $usersTrend = $usersMetric['trend'] ?? null; @endphp
        <div class="dashboard-card rounded-2xl p-5 sm:p-6 card-hover-effect relative overflow-hidden group border-2 border-blue-200/50 hover:border-blue-300/70 shadow-xl hover:shadow-2xl transition-all duration-300 w-full" style="background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(240, 249, 255, 0.95) 50%, rgba(224, 242, 254, 0.9) 100%);">
            <div class="absolute inset-0 bg-gradient-to-br from-blue-100/60 via-sky-100/40 to-blue-50/30 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
            <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-blue-400/20 to-transparent rounded-full opacity-80" aria-hidden="true"></div>
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex-1 min-w-0">
                        <p class="text-base sm:text-sm font-bold text-blue-800/80 mb-2 sm:mb-1">إجمالي المستخدمين</p>
                        <p class="text-5xl sm:text-4xl font-black bg-gradient-to-r from-blue-700 via-blue-600 to-sky-600 bg-clip-text text-transparent drop-shadow-sm">{{ number_format($usersMetric['total'] ?? 0) }}</p>
                    </div>
                    <div class="card-icon w-20 h-20 sm:w-16 sm:h-16 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300 flex-shrink-0 mr-3 sm:mr-0" style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 50%, #0284c7 100%); box-shadow: 0 8px 20px 0 rgba(59, 130, 246, 0.4);">
                        <i class="fas fa-users text-white text-2xl sm:text-xl"></i>
                    </div>
                </div>
                <p class="text-xs font-medium text-blue-700/70">مستخدمون جدد هذا الشهر: <span class="font-bold text-blue-800">{{ number_format($usersMetric['new_this_month'] ?? 0) }}</span></p>
                @if($usersTrend)
                    @php
                        $diff = (int) round($usersTrend['difference']);
                        $percent = $usersTrend['percent'];
                        $positive = $diff >= 0;
                    @endphp
                    <div class="mt-3 flex items-center gap-2 text-sm flex-wrap">
                        <span class="font-bold {{ $positive ? 'text-emerald-600' : 'text-rose-500' }}">
                            {{ $positive ? '+' : '' }}{{ number_format($diff) }}
                        </span>
                        <span class="text-blue-700/70">عن الشهر الماضي</span>
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold {{ $positive ? 'bg-emerald-100 text-emerald-700 border border-emerald-200' : 'bg-rose-100 text-rose-700 border border-rose-200' }}">
                            {{ $percent >= 0 ? '+' : '' }}{{ number_format($percent, 1) }}%
                        </span>
                </div>
                @else
                    <p class="mt-3 text-sm text-blue-600/60">لا توجد بيانات مقارنة للشهر السابق.</p>
                @endif
            </div>
        </div>

        <!-- الطلاب -->
        @php $studentsMetric = $metrics['students'] ?? null; $studentsTrend = $studentsMetric['trend'] ?? null; @endphp
        <div class="dashboard-card rounded-2xl p-6 card-hover-effect relative overflow-hidden group border-2 border-emerald-200/50 hover:border-emerald-300/70 shadow-xl hover:shadow-2xl transition-all duration-300" style="background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(236, 253, 245, 0.95) 50%, rgba(209, 250, 229, 0.9) 100%);">
            <div class="absolute inset-0 bg-gradient-to-br from-emerald-100/60 via-green-100/40 to-teal-50/30 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
            <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-emerald-400/15 to-transparent rounded-full opacity-80" aria-hidden="true"></div>
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-sm font-bold text-emerald-800/80 mb-1">الطلاب</p>
                        <p class="text-4xl font-black bg-gradient-to-r from-emerald-700 via-green-600 to-teal-600 bg-clip-text text-transparent drop-shadow-sm">{{ number_format($studentsMetric['total'] ?? 0) }}</p>
                    </div>
                    <div class="w-16 h-16 bg-gradient-to-br from-emerald-500 via-green-500 to-teal-600 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300" style="box-shadow: 0 8px 20px 0 rgba(16, 185, 129, 0.4);">
                        <i class="fas fa-user-graduate text-white text-xl"></i>
                    </div>
                </div>
                <p class="text-xs font-medium text-emerald-700/70">طلاب جدد هذا الشهر: <span class="font-bold text-emerald-800">{{ number_format($studentsMetric['new_this_month'] ?? 0) }}</span></p>
                @if($studentsTrend)
                    @php
                        $diff = (int) round($studentsTrend['difference']);
                        $percent = $studentsTrend['percent'];
                        $positive = $diff >= 0;
                    @endphp
                    <div class="mt-3 flex items-center gap-2 text-sm flex-wrap">
                        <span class="font-bold {{ $positive ? 'text-emerald-600' : 'text-rose-500' }}">
                            {{ $positive ? '+' : '' }}{{ number_format($diff) }}
                        </span>
                        <span class="text-emerald-700/70">عن الشهر الماضي</span>
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold {{ $positive ? 'bg-emerald-100 text-emerald-700 border border-emerald-200' : 'bg-rose-100 text-rose-700 border border-rose-200' }}">
                            {{ $percent >= 0 ? '+' : '' }}{{ number_format($percent, 1) }}%
                        </span>
                </div>
                @endif
            </div>
        </div>

        <!-- المدربين -->
        @php $instructorsMetric = $metrics['instructors'] ?? null; $instructorsTrend = $instructorsMetric['trend'] ?? null; @endphp
        <div class="dashboard-card rounded-2xl p-6 card-hover-effect relative overflow-hidden group border-2 border-indigo-200/50 hover:border-indigo-300/70 shadow-xl hover:shadow-2xl transition-all duration-300" style="background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(238, 242, 255, 0.95) 50%, rgba(224, 231, 255, 0.9) 100%);">
            <div class="absolute inset-0 bg-gradient-to-br from-indigo-100/60 via-purple-100/40 to-violet-50/30 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
            <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-indigo-400/15 to-transparent rounded-full opacity-80" aria-hidden="true"></div>
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-sm font-bold text-indigo-800/80 mb-1">المدربين</p>
                        <p class="text-4xl font-black bg-gradient-to-r from-indigo-700 via-purple-600 to-violet-600 bg-clip-text text-transparent drop-shadow-sm">{{ number_format($instructorsMetric['total'] ?? 0) }}</p>
                    </div>
                    <div class="w-16 h-16 bg-gradient-to-br from-indigo-500 via-purple-500 to-violet-600 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300" style="box-shadow: 0 8px 20px 0 rgba(99, 102, 241, 0.4);">
                        <i class="fas fa-user-tie text-white text-xl"></i>
                    </div>
                </div>
                <p class="text-xs font-medium text-indigo-700/70">مدربون جدد هذا الشهر: <span class="font-bold text-indigo-800">{{ number_format($instructorsMetric['new_this_month'] ?? 0) }}</span></p>
                @if($instructorsTrend)
                    @php
                        $diff = (int) round($instructorsTrend['difference']);
                        $percent = $instructorsTrend['percent'];
                        $positive = $diff >= 0;
                    @endphp
                    <div class="mt-3 flex items-center gap-2 text-sm flex-wrap">
                        <span class="font-bold {{ $positive ? 'text-emerald-600' : 'text-rose-500' }}">
                            {{ $positive ? '+' : '' }}{{ number_format($diff) }}
                        </span>
                        <span class="text-indigo-700/70">عن الشهر الماضي</span>
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold {{ $positive ? 'bg-emerald-100 text-emerald-700 border border-emerald-200' : 'bg-rose-100 text-rose-700 border border-rose-200' }}">
                            {{ $percent >= 0 ? '+' : '' }}{{ number_format($percent, 1) }}%
                        </span>
                    </div>
                @endif
            </div>
        </div>

        <!-- الكورسات -->
        @php $coursesMetric = $metrics['courses'] ?? null; $coursesTrend = $coursesMetric['trend'] ?? null; @endphp
        <div class="dashboard-card rounded-2xl p-6 card-hover-effect relative overflow-hidden group border-2 border-amber-200/50 hover:border-amber-300/70 shadow-xl hover:shadow-2xl transition-all duration-300" style="background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(255, 251, 235, 0.95) 50%, rgba(254, 243, 199, 0.9) 100%);">
            <div class="absolute inset-0 bg-gradient-to-br from-amber-100/60 via-orange-100/40 to-yellow-50/30 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
            <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-amber-400/15 to-transparent rounded-full opacity-80" aria-hidden="true"></div>
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-sm font-bold text-amber-800/80 mb-1">الكورسات</p>
                        <p class="text-4xl font-black bg-gradient-to-r from-amber-700 via-orange-600 to-yellow-600 bg-clip-text text-transparent drop-shadow-sm">{{ number_format($coursesMetric['total'] ?? 0) }}</p>
                    </div>
                    <div class="w-16 h-16 bg-gradient-to-br from-amber-500 via-orange-500 to-yellow-600 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300" style="box-shadow: 0 8px 20px 0 rgba(245, 158, 11, 0.4);">
                        <i class="fas fa-book text-white text-xl"></i>
                    </div>
                </div>
                <p class="text-xs font-medium text-amber-700/70">كورسات جديدة هذا الشهر: <span class="font-bold text-amber-800">{{ number_format($coursesMetric['new_this_month'] ?? 0) }}</span></p>
                @if($coursesTrend)
                    @php
                        $diff = (int) round($coursesTrend['difference']);
                        $percent = $coursesTrend['percent'];
                        $positive = $diff >= 0;
                    @endphp
                    <div class="mt-3 flex items-center gap-2 text-sm flex-wrap">
                        <span class="font-bold {{ $positive ? 'text-emerald-600' : 'text-rose-500' }}">
                            {{ $positive ? '+' : '' }}{{ number_format($diff) }}
                        </span>
                        <span class="text-amber-700/70">عن الشهر الماضي</span>
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold {{ $positive ? 'bg-emerald-100 text-emerald-700 border border-emerald-200' : 'bg-rose-100 text-rose-700 border border-rose-200' }}">
                            {{ $percent >= 0 ? '+' : '' }}{{ number_format($percent, 1) }}%
                        </span>
                    </div>
                @endif
                </div>
            </div>
        </div>

    <!-- إحصائيات مالية -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- إجمالي الإيرادات -->
        @php $revenueMetric = $metrics['monthly_revenue'] ?? null; $revenueTrend = $revenueMetric['trend'] ?? null; @endphp
        <div class="dashboard-card rounded-2xl p-6 card-hover-effect relative overflow-hidden group border-2 border-emerald-200/50 hover:border-emerald-300/70 shadow-xl hover:shadow-2xl transition-all duration-300" style="background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(236, 253, 245, 0.95) 50%, rgba(209, 250, 229, 0.9) 100%);">
            <div class="absolute inset-0 bg-gradient-to-br from-emerald-100/60 via-green-100/40 to-teal-50/30 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
            <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-emerald-400/15 to-transparent rounded-full opacity-80" aria-hidden="true"></div>
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-sm font-bold text-emerald-800/80 mb-1">إجمالي الإيرادات</p>
                        <p class="text-3xl font-black bg-gradient-to-r from-emerald-700 via-green-600 to-teal-600 bg-clip-text text-transparent drop-shadow-sm">{{ number_format($stats['total_revenue'] ?? 0, 2) }} <span class="text-lg">ج.م</span></p>
                    </div>
                    <div class="w-16 h-16 bg-gradient-to-br from-emerald-500 via-green-500 to-teal-600 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300" style="box-shadow: 0 8px 20px 0 rgba(16, 185, 129, 0.4);">
                        <i class="fas fa-money-bill-wave text-white text-xl"></i>
                </div>
            </div>
        </div>
    </div>

        <!-- إيرادات الشهر -->
        <div class="dashboard-card rounded-2xl p-6 card-hover-effect relative overflow-hidden group border-2 border-blue-200/50 hover:border-blue-300/70 shadow-xl hover:shadow-2xl transition-all duration-300" style="background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(240, 249, 255, 0.95) 50%, rgba(224, 242, 254, 0.9) 100%);">
            <div class="absolute inset-0 bg-gradient-to-br from-blue-100/60 via-sky-100/40 to-cyan-50/30 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
            <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-blue-400/20 to-transparent rounded-full opacity-80" aria-hidden="true"></div>
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-sm font-bold text-blue-800/80 mb-1">إيرادات الشهر</p>
                        <p class="text-3xl font-black bg-gradient-to-r from-blue-700 via-sky-600 to-cyan-600 bg-clip-text text-transparent drop-shadow-sm">{{ number_format($revenueMetric['current'] ?? 0, 2) }} <span class="text-lg">ج.م</span></p>
                    </div>
                    <div class="w-16 h-16 bg-gradient-to-br from-blue-500 via-sky-500 to-cyan-600 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300" style="box-shadow: 0 8px 20px 0 rgba(59, 130, 246, 0.4);">
                        <i class="fas fa-chart-line text-white text-xl"></i>
                    </div>
                </div>
                @if($revenueTrend)
                    @php
                        $diff = $revenueTrend['difference'];
                        $percent = $revenueTrend['percent'];
                        $positive = $diff >= 0;
                    @endphp
                    <div class="mt-3 flex items-center gap-2 text-sm flex-wrap">
                        <span class="font-bold {{ $positive ? 'text-emerald-600' : 'text-rose-500' }}">
                            {{ $positive ? '+' : '' }}{{ number_format($diff, 2) }} ج.م
                        </span>
                        <span class="text-blue-700/70">عن الشهر الماضي</span>
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold {{ $positive ? 'bg-emerald-100 text-emerald-700 border border-emerald-200' : 'bg-rose-100 text-rose-700 border border-rose-200' }}">
                            {{ $percent >= 0 ? '+' : '' }}{{ number_format($percent, 1) }}%
                        </span>
                    </div>
                @endif
            </div>
        </div>
        
        <!-- الفواتير المعلقة -->
        @php $pendingMetric = $metrics['pending_invoices'] ?? null; $pendingTrend = $pendingMetric['trend'] ?? null; @endphp
        <div class="dashboard-card rounded-2xl p-6 card-hover-effect relative overflow-hidden group border-2 border-yellow-200/50 hover:border-yellow-300/70 shadow-xl hover:shadow-2xl transition-all duration-300" style="background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(255, 251, 235, 0.95) 50%, rgba(254, 243, 199, 0.9) 100%);">
            <div class="absolute inset-0 bg-gradient-to-br from-yellow-100/60 via-amber-100/40 to-orange-50/30 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
            <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-yellow-400/15 to-transparent rounded-full opacity-80" aria-hidden="true"></div>
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-sm font-bold text-yellow-800/80 mb-1">فواتير معلقة</p>
                        <p class="text-3xl font-black bg-gradient-to-r from-yellow-700 via-amber-600 to-orange-600 bg-clip-text text-transparent drop-shadow-sm">{{ number_format($pendingMetric['total'] ?? 0) }}</p>
                    </div>
                    <div class="w-16 h-16 bg-gradient-to-br from-yellow-500 via-amber-500 to-orange-600 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300" style="box-shadow: 0 8px 20px 0 rgba(245, 158, 11, 0.4);">
                        <i class="fas fa-file-invoice text-white text-xl"></i>
                    </div>
                </div>
                <p class="text-xs font-medium text-yellow-700/70">فواتير جديدة هذا الشهر: <span class="font-bold text-yellow-800">{{ number_format($pendingMetric['new_this_month'] ?? 0) }}</span></p>
                @if($pendingTrend)
                    @php
                        $diff = (int) round($pendingTrend['difference']);
                        $percent = $pendingTrend['percent'];
                        $positive = $diff >= 0;
                    @endphp
                    <div class="mt-3 flex items-center gap-2 text-sm flex-wrap">
                        <span class="font-bold {{ $positive ? 'text-yellow-600' : 'text-rose-500' }}">
                            {{ $positive ? '+' : '' }}{{ number_format($diff) }}
                        </span>
                        <span class="text-yellow-700/70">عن الشهر الماضي</span>
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold {{ $positive ? 'bg-yellow-100 text-yellow-700 border border-yellow-200' : 'bg-rose-100 text-rose-700 border border-rose-200' }}">
                            {{ $percent >= 0 ? '+' : '' }}{{ number_format($percent, 1) }}%
                        </span>
                    </div>
                @endif
            </div>
        </div>
        
        <!-- التسجيلات النشطة -->
        @php $enrollmentsMetric = $metrics['enrollments'] ?? null; $enrollmentsTrend = $enrollmentsMetric['trend'] ?? null; @endphp
        <div class="dashboard-card rounded-2xl p-6 card-hover-effect relative overflow-hidden group border-2 border-purple-200/50 hover:border-purple-300/70 shadow-xl hover:shadow-2xl transition-all duration-300" style="background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(250, 245, 255, 0.95) 50%, rgba(243, 232, 255, 0.9) 100%);">
            <div class="absolute inset-0 bg-gradient-to-br from-purple-100/60 via-pink-100/40 to-fuchsia-50/30 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
            <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-purple-400/15 to-transparent rounded-full opacity-80" aria-hidden="true"></div>
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-sm font-bold text-purple-800/80 mb-1">التسجيلات النشطة</p>
                        <p class="text-3xl font-black bg-gradient-to-r from-purple-700 via-pink-600 to-fuchsia-600 bg-clip-text text-transparent drop-shadow-sm">{{ number_format($enrollmentsMetric['total'] ?? 0) }}</p>
                    </div>
                    <div class="w-16 h-16 bg-gradient-to-br from-purple-500 via-pink-500 to-fuchsia-600 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300" style="box-shadow: 0 8px 20px 0 rgba(168, 85, 247, 0.4);">
                        <i class="fas fa-user-check text-white text-xl"></i>
                    </div>
                </div>
                <p class="text-xs font-medium text-purple-700/70">تسجيلات جديدة هذا الشهر: <span class="font-bold text-purple-800">{{ number_format($enrollmentsMetric['new_this_month'] ?? 0) }}</span></p>
                @if($enrollmentsTrend)
                    @php
                        $diff = (int) round($enrollmentsTrend['difference']);
                        $percent = $enrollmentsTrend['percent'];
                        $positive = $diff >= 0;
                    @endphp
                    <div class="mt-3 flex items-center gap-2 text-sm flex-wrap">
                        <span class="font-bold {{ $positive ? 'text-emerald-600' : 'text-rose-500' }}">
                            {{ $positive ? '+' : '' }}{{ number_format($diff) }}
                        </span>
                        <span class="text-purple-700/70">عن الشهر الماضي</span>
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold {{ $positive ? 'bg-emerald-100 text-emerald-700 border border-emerald-200' : 'bg-rose-100 text-rose-700 border border-rose-200' }}">
                            {{ $percent >= 0 ? '+' : '' }}{{ number_format($percent, 1) }}%
                        </span>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- الأنشطة الأخيرة -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- سجل النشاطات -->
        <div class="dashboard-card rounded-2xl card-hover-effect border-2 border-blue-200/50 hover:border-blue-300/70 shadow-xl hover:shadow-2xl transition-all duration-300" style="background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(240, 249, 255, 0.95) 50%, rgba(224, 242, 254, 0.9) 100%);">
            <div class="px-6 py-4 section-header rounded-t-2xl" style="background: linear-gradient(135deg, rgba(59, 130, 246, 0.15) 0%, rgba(37, 99, 235, 0.1) 50%, rgba(2, 132, 199, 0.08) 100%); border-bottom: 2px solid rgba(59, 130, 246, 0.3);">
                <h3 class="text-xl font-black bg-gradient-to-r from-blue-700 via-blue-600 to-sky-600 bg-clip-text text-transparent">
                    <i class="fas fa-history text-blue-600 ml-2"></i>
                    آخر النشاطات
                </h3>
            </div>
            <div class="p-6">
                @if(isset($stats['recent_activities']) && $stats['recent_activities']->count() > 0)
                    <div class="space-y-4">
                        @foreach($stats['recent_activities']->take(5) as $activity)
                            <div class="flex items-center space-x-3 space-x-reverse p-3 rounded-xl hover:bg-blue-50/80 transition-all duration-300 border border-blue-100/50">
                                <div class="flex-shrink-0">
                                    <div class="w-10 h-10 bg-gradient-to-br from-blue-500 via-blue-600 to-sky-600 rounded-full flex items-center justify-center shadow-md">
                                        <i class="fas fa-history text-white text-xs"></i>
                                    </div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-semibold text-blue-900">
                                        {{ $activity->user->name ?? 'مستخدم محذوف' }}
                                    </p>
                                    <p class="text-xs text-blue-700/70">
                                        {{ $activity->action }} - {{ $activity->created_at->diffForHumans() }}
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-6 pt-4 border-t border-blue-200/50">
                        <a href="{{ route('admin.activity-log') }}" class="text-blue-600 hover:text-blue-800 text-sm font-bold inline-flex items-center gap-2 transition-colors">
                            عرض جميع النشاطات
                            <i class="fas fa-arrow-left text-xs"></i>
                        </a>
                    </div>
                @else
                    <p class="text-blue-600/60 text-center py-8">لا توجد أنشطة بعد</p>
                @endif
            </div>
        </div>

        <!-- آخر محاولات الامتحانات -->
        <div class="dashboard-card rounded-2xl card-hover-effect border-2 border-indigo-200/50 hover:border-indigo-300/70 shadow-xl hover:shadow-2xl transition-all duration-300" style="background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(238, 242, 255, 0.95) 50%, rgba(224, 231, 255, 0.9) 100%);">
            <div class="px-6 py-4 section-header rounded-t-2xl" style="background: linear-gradient(135deg, rgba(99, 102, 241, 0.15) 0%, rgba(79, 70, 229, 0.1) 50%, rgba(67, 56, 202, 0.08) 100%); border-bottom: 2px solid rgba(99, 102, 241, 0.3);">
                <h3 class="text-xl font-black bg-gradient-to-r from-indigo-700 via-indigo-600 to-purple-600 bg-clip-text text-transparent">
                    <i class="fas fa-clipboard-check text-indigo-600 ml-2"></i>
                    آخر محاولات الامتحانات
                </h3>
            </div>
            <div class="p-6">
                @if(isset($stats['recent_exam_attempts']) && $stats['recent_exam_attempts']->count() > 0)
                    <div class="space-y-4">
                        @foreach($stats['recent_exam_attempts']->take(5) as $attempt)
                            <div class="flex items-center justify-between p-3 rounded-xl hover:bg-indigo-50/80 transition-all duration-300 border border-indigo-100/50">
                                <div class="flex-1">
                                    <p class="text-sm font-semibold text-indigo-900">{{ $attempt->student->name ?? 'طالب محذوف' }}</p>
                                    <p class="text-xs text-indigo-700/70">{{ $attempt->exam->title ?? 'امتحان محذوف' }}</p>
                                </div>
                                <div class="text-left">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold border
                                        {{ $attempt->score >= 80 ? 'bg-emerald-100 text-emerald-700 border-emerald-200' : ($attempt->score >= 60 ? 'bg-yellow-100 text-yellow-700 border-yellow-200' : 'bg-rose-100 text-rose-700 border-rose-200') }}">
                                        {{ $attempt->score }}%
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-indigo-600/60 text-center py-8">لا توجد محاولات امتحانات بعد</p>
                @endif
            </div>
        </div>
    </div>

    <!-- آخر المستخدمين والكورسات -->
    @if(isset($recent_users) || isset($recent_courses))
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- آخر المستخدمين -->
        @if(isset($recent_users))
        <div class="dashboard-card rounded-2xl card-hover-effect border-2 border-blue-200/50 hover:border-blue-300/70 shadow-xl hover:shadow-2xl transition-all duration-300" style="background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(240, 249, 255, 0.95) 50%, rgba(224, 242, 254, 0.9) 100%);">
            <div class="p-6 section-header rounded-t-2xl" style="background: linear-gradient(135deg, rgba(59, 130, 246, 0.15) 0%, rgba(37, 99, 235, 0.1) 50%, rgba(2, 132, 199, 0.08) 100%); border-bottom: 2px solid rgba(59, 130, 246, 0.3);">
                <div class="flex items-center justify-between">
                    <h3 class="text-xl font-black bg-gradient-to-r from-blue-700 via-blue-600 to-sky-600 bg-clip-text text-transparent">
                        <i class="fas fa-users text-blue-600 ml-2"></i>
                        آخر المستخدمين
                    </h3>
                    <a href="{{ route('admin.users.index') }}" class="text-sm font-bold text-blue-600 hover:text-blue-700 transition-colors inline-flex items-center gap-2">
                        عرض الكل
                        <i class="fas fa-arrow-left text-xs"></i>
                    </a>
                </div>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    @foreach($recent_users as $user)
                    <div class="list-item-card flex items-center gap-4 p-3 rounded-xl group border border-blue-100/50 hover:border-blue-200/70" style="background: linear-gradient(135deg, rgba(255, 255, 255, 0.95) 0%, rgba(240, 249, 255, 0.85) 100%);">
                        <div class="w-12 h-12 bg-gradient-to-br from-blue-500 via-blue-600 to-sky-600 rounded-full flex items-center justify-center text-white font-bold shadow-lg group-hover:scale-110 transition-transform duration-300">
                            {{ substr($user->name, 0, 1) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-blue-900 truncate">{{ $user->name }}</p>
                            <p class="text-xs text-blue-700/70">{{ $user->phone ?? $user->email }}</p>
                        </div>
                        <div class="text-right">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold border
                                @if($user->role === 'student') bg-emerald-100 text-emerald-700 border-emerald-200
                                @elseif($user->role === 'instructor') bg-indigo-100 text-indigo-700 border-indigo-200
                                @elseif($user->role === 'super_admin') bg-rose-100 text-rose-700 border-rose-200
                                @else bg-gray-100 text-gray-700 border-gray-200 @endif">
                                @if($user->role === 'student') طالب
                                @elseif($user->role === 'instructor') مدرب
                                @elseif($user->role === 'super_admin') مدير عام
                                @else غير محدد @endif
                            </span>
                            <p class="text-xs text-blue-600/60 mt-1">{{ $user->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        <!-- آخر الكورسات -->
        @if(isset($recent_courses))
        <div class="dashboard-card rounded-2xl card-hover-effect border-2 border-amber-200/50 hover:border-amber-300/70 shadow-xl hover:shadow-2xl transition-all duration-300" style="background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(255, 251, 235, 0.95) 50%, rgba(254, 243, 199, 0.9) 100%);">
            <div class="p-6 section-header rounded-t-2xl" style="background: linear-gradient(135deg, rgba(245, 158, 11, 0.15) 0%, rgba(217, 119, 6, 0.1) 50%, rgba(180, 83, 9, 0.08) 100%); border-bottom: 2px solid rgba(245, 158, 11, 0.3);">
                <div class="flex items-center justify-between">
                    <h3 class="text-xl font-black bg-gradient-to-r from-amber-700 via-orange-600 to-yellow-600 bg-clip-text text-transparent">
                        <i class="fas fa-book text-amber-600 ml-2"></i>
                        آخر الكورسات
                    </h3>
                    <a href="{{ route('admin.advanced-courses.index') }}" class="text-sm font-bold text-amber-600 hover:text-amber-700 transition-colors inline-flex items-center gap-2">
                        عرض الكل
                        <i class="fas fa-arrow-left text-xs"></i>
                    </a>
                </div>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    @forelse($recent_courses as $course)
                    <div class="list-item-card flex items-start gap-4 p-3 rounded-xl group border border-amber-100/50 hover:border-amber-200/70" style="background: linear-gradient(135deg, rgba(255, 255, 255, 0.95) 0%, rgba(255, 251, 235, 0.85) 100%);">
                        <div class="w-14 h-14 bg-gradient-to-br from-amber-500 via-orange-500 to-yellow-600 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300">
                            <i class="fas fa-book text-white text-lg"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-amber-900 truncate">{{ $course->title }}</p>
                            <p class="text-xs text-amber-700/70">{{ $course->academicSubject->name ?? 'غير محدد' }}</p>
                        </div>
                        <div class="text-right">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold border
                                @if($course->is_active) bg-emerald-100 text-emerald-700 border-emerald-200
                                @else bg-gray-100 text-gray-700 border-gray-200 @endif">
                                @if($course->is_active) نشط
                                @else غير نشط @endif
                            </span>
                            <p class="text-xs text-amber-600/60 mt-1">{{ $course->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-8 text-amber-600/60">
                        <i class="fas fa-book text-3xl mb-2"></i>
                        <p>لا توجد كورسات بعد</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
        @endif
    </div>
    @endif

    <!-- الفواتير والمدفوعات -->
    @if((isset($pending_invoices) && $pending_invoices->count() > 0) || (isset($recent_payments) && $recent_payments->count() > 0))
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- الفواتير المعلقة -->
        @if(isset($pending_invoices) && $pending_invoices->count() > 0)
        <div class="dashboard-card rounded-2xl card-hover-effect border-2 border-yellow-200/50 hover:border-yellow-300/70 shadow-xl hover:shadow-2xl transition-all duration-300" style="background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(255, 251, 235, 0.95) 50%, rgba(254, 243, 199, 0.9) 100%);">
            <div class="p-6 section-header rounded-t-2xl" style="background: linear-gradient(135deg, rgba(245, 158, 11, 0.15) 0%, rgba(217, 119, 6, 0.1) 50%, rgba(180, 83, 9, 0.08) 100%); border-bottom: 2px solid rgba(245, 158, 11, 0.3);">
                <div class="flex items-center justify-between">
                    <h3 class="text-xl font-black bg-gradient-to-r from-yellow-700 via-amber-600 to-orange-600 bg-clip-text text-transparent">
                        <i class="fas fa-file-invoice text-yellow-600 ml-2"></i>
                        الفواتير المعلقة
                    </h3>
                    <a href="#" class="text-sm font-bold text-yellow-600 hover:text-yellow-700 transition-colors inline-flex items-center gap-2">
                        عرض الكل
                        <i class="fas fa-arrow-left text-xs"></i>
                    </a>
                </div>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    @foreach($pending_invoices as $invoice)
                    <div class="list-item-card flex items-start gap-4 p-3 rounded-xl group border border-yellow-100/50 hover:border-yellow-200/70" style="background: linear-gradient(135deg, rgba(255, 255, 255, 0.95) 0%, rgba(255, 251, 235, 0.85) 100%);">
                        <div class="w-14 h-14 bg-gradient-to-br from-yellow-500 via-amber-500 to-orange-600 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300">
                            <i class="fas fa-file-invoice text-white text-lg"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-yellow-900 truncate">{{ $invoice->invoice_number ?? 'غير محدد' }}</p>
                            <p class="text-xs text-yellow-700/70">{{ $invoice->user->name ?? 'غير محدد' }}</p>
                            <p class="text-xs font-bold text-yellow-700">{{ number_format($invoice->total_amount ?? 0, 2) }} ج.م</p>
                        </div>
                        <div class="text-right">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-yellow-100 text-yellow-700 border border-yellow-200">
                                معلق
                            </span>
                            <p class="text-xs text-yellow-600/60 mt-1">{{ $invoice->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        <!-- المدفوعات الأخيرة -->
        @if(isset($recent_payments) && $recent_payments->count() > 0)
        <div class="dashboard-card rounded-2xl card-hover-effect border-2 border-emerald-200/50 hover:border-emerald-300/70 shadow-xl hover:shadow-2xl transition-all duration-300" style="background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(236, 253, 245, 0.95) 50%, rgba(209, 250, 229, 0.9) 100%);">
            <div class="p-6 section-header rounded-t-2xl" style="background: linear-gradient(135deg, rgba(16, 185, 129, 0.15) 0%, rgba(5, 150, 105, 0.1) 50%, rgba(4, 120, 87, 0.08) 100%); border-bottom: 2px solid rgba(16, 185, 129, 0.3);">
                <div class="flex items-center justify-between">
                    <h3 class="text-xl font-black bg-gradient-to-r from-emerald-700 via-green-600 to-teal-600 bg-clip-text text-transparent">
                        <i class="fas fa-money-bill-wave text-emerald-600 ml-2"></i>
                        المدفوعات الأخيرة
                    </h3>
                    <a href="#" class="text-sm font-bold text-emerald-600 hover:text-emerald-700 transition-colors inline-flex items-center gap-2">
                        عرض الكل
                        <i class="fas fa-arrow-left text-xs"></i>
                    </a>
                </div>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    @foreach($recent_payments as $payment)
                    <div class="list-item-card flex items-start gap-4 p-3 rounded-xl group border border-emerald-100/50 hover:border-emerald-200/70" style="background: linear-gradient(135deg, rgba(255, 255, 255, 0.95) 0%, rgba(236, 253, 245, 0.85) 100%);">
                        <div class="w-14 h-14 bg-gradient-to-br from-emerald-500 via-green-500 to-teal-600 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300">
                            <i class="fas fa-money-bill-wave text-white text-lg"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-emerald-900 truncate">{{ $payment->payment_number ?? 'غير محدد' }}</p>
                            <p class="text-xs text-emerald-700/70">{{ $payment->user->name ?? 'غير محدد' }}</p>
                            <p class="text-xs font-bold text-emerald-700">{{ number_format($payment->amount ?? 0, 2) }} ج.م</p>
                        </div>
                        <div class="text-right">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-700 border border-emerald-200">
                                مكتمل
                            </span>
                            <p class="text-xs text-emerald-600/60 mt-1">{{ $payment->paid_at ? $payment->paid_at->diffForHumans() : $payment->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif
    </div>
    @endif

    <!-- أزرار سريعة -->
    <div class="dashboard-card rounded-2xl p-6 card-hover-effect border-2 border-blue-200/50 hover:border-blue-300/70 shadow-xl hover:shadow-2xl transition-all duration-300" style="background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(240, 249, 255, 0.95) 50%, rgba(224, 242, 254, 0.9) 100%);">
        <div class="pb-4 mb-6 border-b-2 border-blue-200/50 flex items-center justify-between">
            <h3 class="text-xl font-black bg-gradient-to-r from-blue-700 via-blue-600 to-sky-600 bg-clip-text text-transparent">
                <i class="fas fa-bolt text-blue-600 ml-2"></i>
                إجراءات سريعة
            </h3>
            <p class="text-xs font-medium text-blue-700/70">روابط مباشرة للمهام اليومية بناءً على بيانات النظام الحالية</p>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-4">
            @foreach(($quickActions ?? []) as $action)
                <a href="{{ $action['route'] }}"
                   class="flex flex-col items-center gap-4 p-5 bg-gradient-to-br {{ $action['background'] }} rounded-2xl border-2 border-blue-200/30 hover:border-blue-300/50 shadow-lg hover:shadow-2xl transition-all duration-300 card-hover-effect group" style="background: linear-gradient(135deg, rgba(255, 255, 255, 0.95) 0%, rgba(240, 249, 255, 0.9) 100%);">
                    <div class="w-14 h-14 bg-gradient-to-br {{ $action['icon_background'] }} rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300" style="box-shadow: 0 8px 20px 0 rgba(59, 130, 246, 0.3);">
                        <i class="{{ $action['icon'] }} text-white text-xl"></i>
                    </div>
                    <div class="text-center space-y-2">
                        <p class="text-sm font-bold text-blue-900">{{ $action['title'] }}</p>
                        @php
                            $actionCount = $action['count'] ?? 0;
                        @endphp
                        <p class="text-3xl font-black {{ $action['count_class'] ?? 'bg-gradient-to-r from-blue-700 via-blue-600 to-sky-600 bg-clip-text text-transparent' }}">
                            {{ number_format($actionCount) }}
                        </p>
                        @if(!empty($action['meta']))
                            <p class="text-xs font-medium {{ $action['meta_class'] ?? 'text-blue-700/70' }}">{{ $action['meta'] }}</p>
                        @endif
                        <span class="inline-flex items-center justify-center gap-2 text-xs font-bold text-blue-600 group-hover:text-blue-700 transition-colors">
                            {{ $action['cta'] }}
                            <i class="fas fa-arrow-left text-[10px]"></i>
                        </span>
                    </div>
                </a>
            @endforeach
            @if(empty($quickActions))
                <div class="col-span-full text-center text-blue-600/60 text-sm py-4">
                    لا توجد مهام عاجلة حالياً.
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
