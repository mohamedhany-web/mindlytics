@extends('layouts.admin')

@section('title', 'مواعيد العمل')
@section('header', 'مواعيد العمل')

@section('content')
<div class="space-y-6">
    <!-- الهيدر -->
    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">مواعيد العمل</h1>
                <p class="text-gray-600 mt-1">تحديد أوقات الدوام وساعات العمل لجميع أنواع الموظفين</p>
            </div>
            <a href="{{ route('admin.work-schedules.create') }}"
               class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                <i class="fas fa-plus mr-2"></i>
                إضافة موعد جديد
            </a>
        </div>

        <!-- الفلاتر -->
        <div class="mt-6 pt-6 border-t border-gray-200">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-1">البحث</label>
                    <input type="text" name="search" id="search" value="{{ request('search') }}"
                           placeholder="البحث في مواعيد العمل..."
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">الحالة</label>
                    <select name="status" id="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">جميع الحالات</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>نشط</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>موقوف</option>
                    </select>
                </div>

                <div class="flex items-end gap-2">
                    <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                        <i class="fas fa-search mr-2"></i>بحث
                    </button>
                    @if(request()->hasAny(['search', 'status']))
                        <a href="{{ route('admin.work-schedules.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                            <i class="fas fa-times"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- الإحصائيات -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="dashboard-card rounded-2xl p-5 sm:p-6 card-hover-effect relative overflow-hidden group border-2 border-blue-200/50 hover:border-blue-300/70 shadow-xl hover:shadow-2xl transition-all duration-300" style="background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(240, 249, 255, 0.95) 50%, rgba(224, 242, 254, 0.9) 100%);">
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-sm font-semibold text-gray-600 mb-1">إجمالي المواعيد</p>
                        <p class="text-3xl font-black text-gray-900">{{ $stats['total'] }}</p>
                    </div>
                    <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center text-white shadow-lg">
                        <i class="fas fa-clock text-2xl"></i>
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
                        <i class="fas fa-check-circle text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="dashboard-card rounded-2xl p-5 sm:p-6 card-hover-effect relative overflow-hidden group border-2 border-gray-200/50 hover:border-gray-300/70 shadow-xl hover:shadow-2xl transition-all duration-300" style="background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(249, 250, 251, 0.95) 50%, rgba(243, 244, 246, 0.9) 100%);">
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-sm font-semibold text-gray-600 mb-1">موقوف</p>
                        <p class="text-3xl font-black text-gray-800">{{ $stats['inactive'] }}</p>
                    </div>
                    <div class="w-16 h-16 bg-gradient-to-br from-gray-500 to-gray-600 rounded-2xl flex items-center justify-center text-white shadow-lg">
                        <i class="fas fa-pause-circle text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="dashboard-card rounded-2xl p-5 sm:p-6 card-hover-effect relative overflow-hidden group border-2 border-indigo-200/50 hover:border-indigo-300/70 shadow-xl hover:shadow-2xl transition-all duration-300" style="background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(238, 242, 255, 0.95) 50%, rgba(224, 231, 255, 0.9) 100%);">
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-sm font-semibold text-gray-600 mb-1">موظفين مرتبطين</p>
                        <p class="text-3xl font-black text-indigo-700">{{ $stats['assigned_employees'] }}</p>
                    </div>
                    <div class="w-16 h-16 bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-2xl flex items-center justify-center text-white shadow-lg">
                        <i class="fas fa-user-clock text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- قائمة المواعيد -->
    @if($schedules->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($schedules as $schedule)
            <div class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition-shadow duration-300 border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-sky-50 to-indigo-100/50">
                    <div class="flex items-center justify-between gap-2">
                        <h3 class="text-lg font-semibold text-gray-900 truncate">{{ $schedule->name }}</h3>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium shrink-0 {{ $schedule->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                            {{ $schedule->is_active ? 'نشط' : 'موقوف' }}
                        </span>
                    </div>
                </div>

                <div class="px-6 py-4">
                    <div class="space-y-2">
                        <div class="flex items-center text-sm">
                            <i class="fas fa-clock text-gray-400 w-4 ml-2"></i>
                            <span class="text-gray-600">الوقت:</span>
                            <span class="text-gray-900 mr-2 font-medium tabular-nums">{{ $schedule->timeRangeLabel() }}</span>
                        </div>

                        <div class="flex items-center text-sm">
                            <i class="fas fa-hourglass-half text-gray-400 w-4 ml-2"></i>
                            <span class="text-gray-600">الساعات المطلوبة:</span>
                            <span class="text-gray-900 mr-2 font-medium">{{ $schedule->required_hours }} ساعة</span>
                        </div>

                        <div class="flex items-start text-sm">
                            <i class="fas fa-calendar-week text-gray-400 w-4 ml-2 mt-0.5"></i>
                            <span class="text-gray-600 shrink-0">أيام العمل:</span>
                            <span class="text-gray-900 mr-2 font-medium leading-relaxed">{{ $schedule->workDaysLabel() }}</span>
                        </div>

                        <div class="flex items-center text-sm">
                            <i class="fas fa-users text-gray-400 w-4 ml-2"></i>
                            <span class="text-gray-600">الموظفين:</span>
                            <span class="text-gray-900 mr-2 font-medium">{{ $schedule->employees_count }} موظف</span>
                        </div>

                        @if($schedule->grace_minutes)
                        <div class="flex items-center text-sm">
                            <i class="fas fa-stopwatch text-gray-400 w-4 ml-2"></i>
                            <span class="text-gray-600">سماح التأخير:</span>
                            <span class="text-gray-900 mr-2 font-medium">{{ $schedule->grace_minutes }} دقيقة</span>
                        </div>
                        @endif

                        @if($schedule->description)
                        <div class="flex items-start text-sm pt-1">
                            <i class="fas fa-sticky-note text-gray-400 w-4 ml-2 mt-0.5"></i>
                            <span class="text-gray-600 line-clamp-2">{{ $schedule->description }}</span>
                        </div>
                        @endif
                    </div>
                </div>

                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex items-center justify-end gap-2">
                    <a href="{{ route('admin.work-schedules.edit', $schedule) }}"
                       class="px-3 py-1.5 bg-yellow-600 hover:bg-yellow-700 text-white text-sm rounded-lg font-medium transition-colors">
                        <i class="fas fa-edit mr-1"></i>تعديل
                    </a>
                    <form action="{{ route('admin.work-schedules.destroy', $schedule) }}" method="POST"
                          onsubmit="return confirm('هل أنت متأكد من حذف موعد العمل؟');">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white text-sm rounded-lg font-medium transition-colors {{ $schedule->employees_count > 0 ? 'opacity-50 cursor-not-allowed' : '' }}"
                                @disabled($schedule->employees_count > 0)
                                title="{{ $schedule->employees_count > 0 ? 'لا يمكن الحذف — مرتبط بموظفين' : 'حذف' }}">
                            <i class="fas fa-trash mr-1"></i>حذف
                        </button>
                    </form>
                </div>
            </div>
            @endforeach
        </div>
    @else
        <div class="bg-white rounded-xl shadow-lg p-12 text-center border border-gray-200">
            <div class="w-20 h-20 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-clock text-3xl text-gray-400"></i>
            </div>
            <p class="text-lg font-semibold text-gray-700 mb-2">لا توجد مواعيد عمل</p>
            <p class="text-sm text-gray-600 mb-6">ابدأ بإضافة موعد دوام جديد للموظفين</p>
            <a href="{{ route('admin.work-schedules.create') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors">
                <i class="fas fa-plus"></i>
                <span>إضافة موعد</span>
            </a>
        </div>
    @endif

    @if($schedules->hasPages())
    <div class="bg-white rounded-xl shadow-lg p-4 border border-gray-200">
        {{ $schedules->links() }}
    </div>
    @endif
</div>
@endsection
