@extends('layouts.admin')

@section('title', 'إدارة المهام')
@section('header', 'إدارة المهام')

@section('content')
<div class="space-y-6">
    <!-- الهيدر -->
    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">إدارة المهام</h1>
                <p class="text-gray-600 mt-1">إدارة وتنظيم مهام الموظفين</p>
            </div>
            <a href="{{ route('admin.employee-tasks.create') }}" 
               class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                <i class="fas fa-plus mr-2"></i>
                إضافة مهمة جديدة
            </a>
        </div>

        <!-- الفلاتر -->
        <div class="mt-6 pt-6 border-t border-gray-200">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-1">البحث</label>
                    <input type="text" name="search" id="search" value="{{ request('search') }}" 
                           placeholder="البحث في المهام..."
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div>
                    <label for="employee_id" class="block text-sm font-medium text-gray-700 mb-1">الموظف</label>
                    <select name="employee_id" id="employee_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">جميع الموظفين</option>
                        @foreach($employees as $emp)
                            <option value="{{ $emp->id }}" {{ request('employee_id') == $emp->id ? 'selected' : '' }}>{{ $emp->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">الحالة</label>
                    <select name="status" id="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">جميع الحالات</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>معلقة</option>
                        <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>قيد التنفيذ</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>مكتملة</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>ملغاة</option>
                    </select>
                </div>

                <div class="flex items-end gap-2">
                    <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                        <i class="fas fa-search mr-2"></i>بحث
                    </button>
                    @if(request()->hasAny(['search', 'employee_id', 'status', 'priority']))
                        <a href="{{ route('admin.employee-tasks.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                            <i class="fas fa-times"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- الإحصائيات -->
    <div class="grid grid-cols-1 md:grid-cols-5 gap-6">
        <div class="dashboard-card rounded-2xl p-5 sm:p-6 card-hover-effect relative overflow-hidden group border-2 border-blue-200/50 hover:border-blue-300/70 shadow-xl hover:shadow-2xl transition-all duration-300" style="background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(240, 249, 255, 0.95) 50%, rgba(224, 242, 254, 0.9) 100%);">
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-sm font-semibold text-gray-600 mb-1">إجمالي</p>
                        <p class="text-3xl font-black text-gray-900">{{ $stats['total'] }}</p>
                    </div>
                    <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center text-white shadow-lg">
                        <i class="fas fa-tasks text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="dashboard-card rounded-2xl p-5 sm:p-6 card-hover-effect relative overflow-hidden group border-2 border-yellow-200/50 hover:border-yellow-300/70 shadow-xl hover:shadow-2xl transition-all duration-300" style="background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(255, 251, 235, 0.95) 50%, rgba(254, 243, 199, 0.9) 100%);">
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-sm font-semibold text-gray-600 mb-1">معلقة</p>
                        <p class="text-3xl font-black text-yellow-700">{{ $stats['pending'] }}</p>
                    </div>
                    <div class="w-16 h-16 bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-2xl flex items-center justify-center text-white shadow-lg">
                        <i class="fas fa-clock text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="dashboard-card rounded-2xl p-5 sm:p-6 card-hover-effect relative overflow-hidden group border-2 border-blue-200/50 hover:border-blue-300/70 shadow-xl hover:shadow-2xl transition-all duration-300" style="background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(240, 249, 255, 0.95) 50%, rgba(224, 242, 254, 0.9) 100%);">
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-sm font-semibold text-gray-600 mb-1">قيد التنفيذ</p>
                        <p class="text-3xl font-black text-blue-700">{{ $stats['in_progress'] }}</p>
                    </div>
                    <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center text-white shadow-lg">
                        <i class="fas fa-spinner text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="dashboard-card rounded-2xl p-5 sm:p-6 card-hover-effect relative overflow-hidden group border-2 border-green-200/50 hover:border-green-300/70 shadow-xl hover:shadow-2xl transition-all duration-300" style="background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(240, 253, 250, 0.95) 50%, rgba(209, 250, 229, 0.9) 100%);">
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-sm font-semibold text-gray-600 mb-1">مكتملة</p>
                        <p class="text-3xl font-black text-green-700">{{ $stats['completed'] }}</p>
                    </div>
                    <div class="w-16 h-16 bg-gradient-to-br from-green-500 to-green-600 rounded-2xl flex items-center justify-center text-white shadow-lg">
                        <i class="fas fa-check-circle text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="dashboard-card rounded-2xl p-5 sm:p-6 card-hover-effect relative overflow-hidden group border-2 border-red-200/50 hover:border-red-300/70 shadow-xl hover:shadow-2xl transition-all duration-300" style="background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(254, 242, 242, 0.95) 50%, rgba(254, 226, 226, 0.9) 100%);">
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-sm font-semibold text-gray-600 mb-1">متأخرة</p>
                        <p class="text-3xl font-black text-red-700">{{ $stats['overdue'] }}</p>
                    </div>
                    <div class="w-16 h-16 bg-gradient-to-br from-red-500 to-red-600 rounded-2xl flex items-center justify-center text-white shadow-lg">
                        <i class="fas fa-exclamation-triangle text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- قائمة المهام -->
    <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
        @if($tasks->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">المهمة</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الموظف</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الأولوية</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الموعد</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الحالة</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($tasks as $task)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">{{ $task->title }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $task->employee->name }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full
                                    @if($task->priority === 'urgent') bg-red-100 text-red-800
                                    @elseif($task->priority === 'high') bg-orange-100 text-orange-800
                                    @elseif($task->priority === 'medium') bg-yellow-100 text-yellow-800
                                    @else bg-gray-100 text-gray-800
                                    @endif">
                                    @if($task->priority === 'urgent') عاجل
                                    @elseif($task->priority === 'high') عالي
                                    @elseif($task->priority === 'medium') متوسط
                                    @else منخفض
                                    @endif
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($task->deadline)
                                    <div class="text-sm {{ $task->deadline < now() && !in_array($task->status, ['completed', 'cancelled']) ? 'text-red-600 font-semibold' : 'text-gray-900' }}">
                                        {{ $task->deadline->format('Y-m-d') }}
                                    </div>
                                @else
                                    <span class="text-sm text-gray-400">غير محدد</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full
                                    @if($task->status === 'completed') bg-green-100 text-green-800
                                    @elseif($task->status === 'in_progress') bg-blue-100 text-blue-800
                                    @elseif($task->status === 'pending') bg-yellow-100 text-yellow-800
                                    @else bg-gray-100 text-gray-800
                                    @endif">
                                    @if($task->status === 'completed') مكتملة
                                    @elseif($task->status === 'in_progress') قيد التنفيذ
                                    @elseif($task->status === 'pending') معلقة
                                    @else {{ $task->status }}
                                    @endif
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="{{ route('admin.employee-tasks.show', $task) }}" class="text-blue-600 hover:text-blue-900">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $tasks->links() }}
            </div>
        @else
            <div class="p-12 text-center">
                <p class="text-gray-600">لا توجد مهام</p>
            </div>
        @endif
    </div>
</div>
@endsection
