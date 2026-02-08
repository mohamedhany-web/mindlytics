@extends('layouts.admin')

@section('title', 'الرقابة والجودة')
@section('header', 'الرقابة والجودة')

@section('content')
<div class="space-y-6">
    <!-- الهيدر -->
    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
        <div class="flex items-center gap-4">
            <div class="w-16 h-16 bg-gradient-to-br from-red-500 to-red-600 rounded-2xl flex items-center justify-center text-white shadow-lg">
                <i class="fas fa-shield-alt text-2xl"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">لوحة الرقابة والجودة</h1>
                <p class="text-gray-600 mt-1">متابعة ورصد جميع العمليات في النظام</p>
            </div>
        </div>
    </div>

    <!-- إحصائيات الطلاب -->
    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
        <h2 class="text-xl font-bold text-gray-900 mb-4">إحصائيات الطلاب</h2>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="dashboard-card rounded-2xl p-5 card-hover-effect relative overflow-hidden group border-2 border-blue-200/50 shadow-xl" style="background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(240, 249, 255, 0.95) 50%, rgba(224, 242, 254, 0.9) 100%);">
                <div class="relative z-10">
                    <p class="text-sm font-semibold text-gray-600 mb-1">إجمالي الطلاب</p>
                    <p class="text-3xl font-black text-gray-900">{{ $studentStats['total'] }}</p>
                </div>
            </div>
            <div class="dashboard-card rounded-2xl p-5 card-hover-effect relative overflow-hidden group border-2 border-green-200/50 shadow-xl" style="background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(240, 253, 250, 0.95) 50%, rgba(209, 250, 229, 0.9) 100%);">
                <div class="relative z-10">
                    <p class="text-sm font-semibold text-gray-600 mb-1">نشط</p>
                    <p class="text-3xl font-black text-green-700">{{ $studentStats['active'] }}</p>
                </div>
            </div>
            <div class="dashboard-card rounded-2xl p-5 card-hover-effect relative overflow-hidden group border-2 border-purple-200/50 shadow-xl" style="background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(250, 245, 255, 0.95) 50%, rgba(243, 232, 255, 0.9) 100%);">
                <div class="relative z-10">
                    <p class="text-sm font-semibold text-gray-600 mb-1">تسجيلات هذا الشهر</p>
                    <p class="text-3xl font-black text-purple-700">{{ $studentStats['enrollments_this_month'] }}</p>
                </div>
            </div>
            <div class="dashboard-card rounded-2xl p-5 card-hover-effect relative overflow-hidden group border-2 border-blue-200/50 shadow-xl" style="background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(240, 249, 255, 0.95) 50%, rgba(224, 242, 254, 0.9) 100%);">
                <div class="relative z-10">
                    <p class="text-sm font-semibold text-gray-600 mb-1">مسجلون حديثاً</p>
                    <p class="text-3xl font-black text-blue-700">{{ $studentStats['recent_registrations'] }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- إحصائيات المدربين -->
    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
        <h2 class="text-xl font-bold text-gray-900 mb-4">إحصائيات المدربين</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="dashboard-card rounded-2xl p-5 card-hover-effect relative overflow-hidden group border-2 border-indigo-200/50 shadow-xl" style="background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(238, 242, 255, 0.95) 50%, rgba(224, 231, 255, 0.9) 100%);">
                <div class="relative z-10">
                    <p class="text-sm font-semibold text-gray-600 mb-1">إجمالي المدربين</p>
                    <p class="text-3xl font-black text-indigo-700">{{ $instructorStats['total'] }}</p>
                </div>
            </div>
            <div class="dashboard-card rounded-2xl p-5 card-hover-effect relative overflow-hidden group border-2 border-green-200/50 shadow-xl" style="background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(240, 253, 250, 0.95) 50%, rgba(209, 250, 229, 0.9) 100%);">
                <div class="relative z-10">
                    <p class="text-sm font-semibold text-gray-600 mb-1">نشط</p>
                    <p class="text-3xl font-black text-green-700">{{ $instructorStats['active'] }}</p>
                </div>
            </div>
            <div class="dashboard-card rounded-2xl p-5 card-hover-effect relative overflow-hidden group border-2 border-purple-200/50 shadow-xl" style="background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(250, 245, 255, 0.95) 50%, rgba(243, 232, 255, 0.9) 100%);">
                <div class="relative z-10">
                    <p class="text-sm font-semibold text-gray-600 mb-1">مع اتفاقيات</p>
                    <p class="text-3xl font-black text-purple-700">{{ $instructorStats['with_agreements'] }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- إحصائيات الموظفين -->
    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
        <h2 class="text-xl font-bold text-gray-900 mb-4">إحصائيات الموظفين</h2>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="dashboard-card rounded-2xl p-5 card-hover-effect relative overflow-hidden group border-2 border-emerald-200/50 shadow-xl" style="background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(236, 253, 245, 0.95) 50%, rgba(209, 250, 229, 0.9) 100%);">
                <div class="relative z-10">
                    <p class="text-sm font-semibold text-gray-600 mb-1">إجمالي الموظفين</p>
                    <p class="text-3xl font-black text-emerald-700">{{ $employeeStats['total'] }}</p>
                </div>
            </div>
            <div class="dashboard-card rounded-2xl p-5 card-hover-effect relative overflow-hidden group border-2 border-green-200/50 shadow-xl" style="background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(240, 253, 250, 0.95) 50%, rgba(209, 250, 229, 0.9) 100%);">
                <div class="relative z-10">
                    <p class="text-sm font-semibold text-gray-600 mb-1">نشط</p>
                    <p class="text-3xl font-black text-green-700">{{ $employeeStats['active'] }}</p>
                </div>
            </div>
            <div class="dashboard-card rounded-2xl p-5 card-hover-effect relative overflow-hidden group border-2 border-yellow-200/50 shadow-xl" style="background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(255, 251, 235, 0.95) 50%, rgba(254, 243, 199, 0.9) 100%);">
                <div class="relative z-10">
                    <p class="text-sm font-semibold text-gray-600 mb-1">مهام معلقة</p>
                    <p class="text-3xl font-black text-yellow-700">{{ $employeeStats['pending_tasks'] }}</p>
                </div>
            </div>
            <div class="dashboard-card rounded-2xl p-5 card-hover-effect relative overflow-hidden group border-2 border-red-200/50 shadow-xl" style="background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(254, 242, 242, 0.95) 50%, rgba(254, 226, 226, 0.9) 100%);">
                <div class="relative z-10">
                    <p class="text-sm font-semibold text-gray-600 mb-1">مهام متأخرة</p>
                    <p class="text-3xl font-black text-red-700">{{ $employeeStats['overdue_tasks'] }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- العمليات المعلقة -->
    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
        <h2 class="text-xl font-bold text-gray-900 mb-4">العمليات المعلقة</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="border border-yellow-200 rounded-lg p-4 bg-yellow-50">
                <p class="text-sm text-gray-600 mb-1">تسجيلات أونلاين معلقة</p>
                <p class="text-2xl font-bold text-yellow-700">{{ $pendingOperations['pending_enrollments'] }}</p>
            </div>
            <div class="border border-yellow-200 rounded-lg p-4 bg-yellow-50">
                <p class="text-sm text-gray-600 mb-1">تسجيلات أوفلاين معلقة</p>
                <p class="text-2xl font-bold text-yellow-700">{{ $pendingOperations['pending_offline_enrollments'] }}</p>
            </div>
            <div class="border border-yellow-200 rounded-lg p-4 bg-yellow-50">
                <p class="text-sm text-gray-600 mb-1">مهام موظفين معلقة</p>
                <p class="text-2xl font-bold text-yellow-700">{{ $pendingOperations['pending_tasks'] }}</p>
            </div>
        </div>
    </div>

    <!-- النشاطات الأخيرة -->
    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
        <h2 class="text-xl font-bold text-gray-900 mb-4">النشاطات الأخيرة</h2>
        <div class="space-y-3">
            @foreach($recentActivities as $activity)
                <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition-colors">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="font-semibold text-gray-900">{{ $activity->description }}</p>
                            <p class="text-sm text-gray-600">
                                {{ $activity->user->name ?? 'نظام' }} - {{ $activity->created_at->diffForHumans() }}
                            </p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
