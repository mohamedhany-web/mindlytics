@extends('layouts.admin')

@section('title', 'إدارة الموظفين')
@section('header', 'إدارة الموظفين')

@section('content')
<div class="space-y-6">
    <!-- الهيدر -->
    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">إدارة الموظفين</h1>
                <p class="text-gray-600 mt-1">إدارة وتنظيم الموظفين في الأكاديمية</p>
            </div>
            <a href="{{ route('admin.employees.create') }}" 
               class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                <i class="fas fa-plus mr-2"></i>
                إضافة موظف جديد
            </a>
        </div>

        <!-- الفلاتر -->
        <div class="mt-6 pt-6 border-t border-gray-200">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-1">البحث</label>
                    <input type="text" name="search" id="search" value="{{ request('search') }}" 
                           placeholder="البحث في الموظفين..."
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div>
                    <label for="job_id" class="block text-sm font-medium text-gray-700 mb-1">الوظيفة</label>
                    <select name="job_id" id="job_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">جميع الوظائف</option>
                        @foreach($jobs as $job)
                            <option value="{{ $job->id }}" {{ request('job_id') == $job->id ? 'selected' : '' }}>{{ $job->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">الحالة</label>
                    <select name="status" id="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">جميع الحالات</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>نشط</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>غير نشط</option>
                        <option value="terminated" {{ request('status') == 'terminated' ? 'selected' : '' }}>منتهي الخدمة</option>
                    </select>
                </div>

                <div class="flex items-end gap-2">
                    <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                        <i class="fas fa-search mr-2"></i>بحث
                    </button>
                    @if(request()->hasAny(['search', 'job_id', 'status']))
                        <a href="{{ route('admin.employees.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                            <i class="fas fa-times"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- الإحصائيات -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="dashboard-card rounded-2xl p-5 sm:p-6 card-hover-effect relative overflow-hidden group border-2 border-blue-200/50 hover:border-blue-300/70 shadow-xl hover:shadow-2xl transition-all duration-300" style="background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(240, 249, 255, 0.95) 50%, rgba(224, 242, 254, 0.9) 100%);">
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-sm font-semibold text-gray-600 mb-1">إجمالي الموظفين</p>
                        <p class="text-3xl font-black text-gray-900">{{ $stats['total'] }}</p>
                    </div>
                    <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center text-white shadow-lg">
                        <i class="fas fa-users text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="dashboard-card rounded-2xl p-5 sm:p-6 card-hover-effect relative overflow-hidden group border-2 border-green-200/50 hover:border-green-300/70 shadow-xl hover:shadow-2xl transition-all duration-300" style="background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(240, 253, 250, 0.95) 50%, rgba(209, 250, 229, 0.9) 100%);">
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-sm font-semibold text-gray-600 mb-1">نشط</p>
                        <p class="text-3xl font-black text-green-700">{{ $stats['active'] }}</p>
                    </div>
                    <div class="w-16 h-16 bg-gradient-to-br from-green-500 to-green-600 rounded-2xl flex items-center justify-center text-white shadow-lg">
                        <i class="fas fa-user-check text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="dashboard-card rounded-2xl p-5 sm:p-6 card-hover-effect relative overflow-hidden group border-2 border-gray-200/50 hover:border-gray-300/70 shadow-xl hover:shadow-2xl transition-all duration-300" style="background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(249, 250, 251, 0.95) 50%, rgba(243, 244, 246, 0.9) 100%);">
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-sm font-semibold text-gray-600 mb-1">غير نشط</p>
                        <p class="text-3xl font-black text-gray-800">{{ $stats['inactive'] }}</p>
                    </div>
                    <div class="w-16 h-16 bg-gradient-to-br from-gray-500 to-gray-600 rounded-2xl flex items-center justify-center text-white shadow-lg">
                        <i class="fas fa-user-slash text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="dashboard-card rounded-2xl p-5 sm:p-6 card-hover-effect relative overflow-hidden group border-2 border-red-200/50 hover:border-red-300/70 shadow-xl hover:shadow-2xl transition-all duration-300" style="background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(254, 242, 242, 0.95) 50%, rgba(254, 226, 226, 0.9) 100%);">
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-sm font-semibold text-gray-600 mb-1">منتهي الخدمة</p>
                        <p class="text-3xl font-black text-red-700">{{ $stats['terminated'] }}</p>
                    </div>
                    <div class="w-16 h-16 bg-gradient-to-br from-red-500 to-red-600 rounded-2xl flex items-center justify-center text-white shadow-lg">
                        <i class="fas fa-user-times text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- قائمة الموظفين -->
    @if($employees->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($employees as $employee)
            <div class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition-shadow duration-300 border border-gray-200">
                <!-- هيدر البطاقة -->
                <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-emerald-50 to-emerald-100/50">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900 truncate">{{ $employee->name }}</h3>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $employee->is_active && !$employee->termination_date ? 'bg-green-100 text-green-800' : ($employee->termination_date ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800') }}">
                            @if($employee->termination_date) منتهي الخدمة
                            @elseif($employee->is_active) نشط
                            @else غير نشط
                            @endif
                        </span>
                    </div>
                </div>

                <!-- محتوى البطاقة -->
                <div class="px-6 py-4">
                    <div class="space-y-2">
                        @if($employee->employee_code)
                        <div class="flex items-center text-sm">
                            <i class="fas fa-hashtag text-gray-400 w-4 ml-2"></i>
                            <span class="text-gray-600">الرمز:</span>
                            <span class="text-gray-900 mr-2 font-medium">{{ $employee->employee_code }}</span>
                        </div>
                        @endif

                        @if($employee->employeeJob)
                        <div class="flex items-center text-sm">
                            <i class="fas fa-briefcase text-gray-400 w-4 ml-2"></i>
                            <span class="text-gray-600">الوظيفة:</span>
                            <span class="text-gray-900 mr-2 font-medium">{{ $employee->employeeJob->name }}</span>
                        </div>
                        @endif

                        @if($employee->hire_date)
                        <div class="flex items-center text-sm">
                            <i class="fas fa-calendar-check text-gray-400 w-4 ml-2"></i>
                            <span class="text-gray-600">تاريخ التوظيف:</span>
                            <span class="text-gray-900 mr-2 font-medium">{{ $employee->hire_date->format('Y-m-d') }}</span>
                        </div>
                        @endif

                        @if($employee->email)
                        <div class="flex items-center text-sm">
                            <i class="fas fa-envelope text-gray-400 w-4 ml-2"></i>
                            <span class="text-gray-600">البريد:</span>
                            <span class="text-gray-900 mr-2 font-medium truncate">{{ $employee->email }}</span>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- أزرار الإجراءات -->
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex items-center justify-end gap-2">
                    <a href="{{ route('admin.employees.show', $employee) }}" 
                       class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-sm rounded-lg font-medium transition-colors">
                        <i class="fas fa-eye mr-1"></i>عرض
                    </a>
                    <a href="{{ route('admin.employees.edit', $employee) }}" 
                       class="px-3 py-1.5 bg-yellow-600 hover:bg-yellow-700 text-white text-sm rounded-lg font-medium transition-colors">
                        <i class="fas fa-edit mr-1"></i>تعديل
                    </a>
                </div>
            </div>
            @endforeach
        </div>
    @else
        <div class="bg-white rounded-xl shadow-lg p-12 text-center border border-gray-200">
            <div class="w-20 h-20 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-user-tie text-3xl text-gray-400"></i>
            </div>
            <p class="text-lg font-semibold text-gray-700 mb-2">لا يوجد موظفين</p>
            <p class="text-sm text-gray-600 mb-6">ابدأ بإضافة موظف جديد</p>
            <a href="{{ route('admin.employees.create') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors">
                <i class="fas fa-plus"></i>
                <span>إضافة موظف</span>
            </a>
        </div>
    @endif

    <!-- Pagination -->
    @if($employees->hasPages())
    <div class="bg-white rounded-xl shadow-lg p-4 border border-gray-200">
        {{ $employees->links() }}
    </div>
    @endif
</div>
@endsection
