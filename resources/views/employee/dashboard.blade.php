@extends('layouts.employee')

@section('title', 'لوحة تحكم الموظف')
@section('header', 'لوحة تحكم الموظف')

@push('styles')
<style>
    .dashboard-card {
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(240, 249, 255, 0.95) 50%, rgba(224, 242, 254, 0.9) 100%);
        border-radius: 20px;
        padding: 24px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
        border: 2px solid rgba(44, 169, 189, 0.2);
        box-shadow: 0 4px 16px rgba(44, 169, 189, 0.1);
    }

    .dashboard-card::before {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        width: 120px;
        height: 120px;
        background: linear-gradient(135deg, rgba(44, 169, 189, 0.15) 0%, transparent 100%);
        border-radius: 50%;
        transform: translate(30%, -30%);
    }

    .dashboard-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 32px rgba(44, 169, 189, 0.2);
        border-color: rgba(44, 169, 189, 0.4);
    }

    .welcome-section {
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(240, 249, 255, 0.95) 50%, rgba(224, 242, 254, 0.9) 100%);
        border-radius: 20px;
        padding: 32px 40px;
        position: relative;
        overflow: hidden;
        box-shadow: 0 4px 16px rgba(44, 169, 189, 0.1);
        border: 2px solid rgba(44, 169, 189, 0.2);
    }

    .welcome-section::before {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        width: 120px;
        height: 120px;
        background: linear-gradient(135deg, rgba(44, 169, 189, 0.15) 0%, transparent 100%);
        border-radius: 50%;
        transform: translate(30%, -30%);
    }

    .task-card {
        transition: all 0.2s;
        border: 1px solid rgba(226, 232, 240, 0.8);
    }

    .task-card:hover {
        transform: translateX(-4px);
        background: linear-gradient(to right, rgba(44, 169, 189, 0.05), transparent);
        border-color: rgba(44, 169, 189, 0.3);
        box-shadow: 0 4px 12px rgba(44, 169, 189, 0.1);
    }
</style>
@endpush

@section('content')
<div class="space-y-6">
    <!-- ترحيب شخصي -->
    <div class="welcome-section dashboard-card relative overflow-hidden">
        <div class="relative z-10">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div>
                    <h2 class="text-2xl sm:text-3xl md:text-4xl font-black mb-2 text-gray-900">مرحباً، {{ $user->name }}</h2>
                    <p class="text-gray-600 text-base sm:text-lg font-medium">إليك نظرة عامة على مهامك ونشاطك اليوم</p>
                    @if($user->employeeJob)
                        <p class="text-gray-500 text-sm mt-2 flex items-center gap-2">
                            <i class="fas fa-briefcase"></i>
                            <span>{{ $user->employeeJob->name }}</span>
                            @if($user->employee_code)
                                <span class="mr-2">({{ $user->employee_code }})</span>
                            @endif
                        </p>
                    @endif
                </div>
                <div class="w-20 h-20 bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center text-white shadow-lg">
                    <i class="fas fa-user-tie text-4xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- إحصائيات سريعة -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 sm:gap-6">
        <div class="dashboard-card rounded-2xl p-5 sm:p-6 card-hover-effect relative overflow-hidden group border-2 border-blue-200/50 hover:border-blue-300/70 shadow-xl hover:shadow-2xl transition-all duration-300" style="background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(240, 249, 255, 0.95) 50%, rgba(224, 242, 254, 0.9) 100%);">
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-sm font-semibold text-gray-600 mb-1">إجمالي المهام</p>
                        <p class="text-3xl font-black text-gray-900">{{ $stats['total_tasks'] }}</p>
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
                        <p class="text-3xl font-black text-yellow-700">{{ $stats['pending_tasks'] }}</p>
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
                        <p class="text-3xl font-black text-blue-700">{{ $stats['in_progress_tasks'] }}</p>
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
                        <p class="text-3xl font-black text-green-700">{{ $stats['completed_tasks'] }}</p>
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
                        <p class="text-3xl font-black text-red-700">{{ $stats['overdue_tasks'] }}</p>
                    </div>
                    <div class="w-16 h-16 bg-gradient-to-br from-red-500 to-red-600 rounded-2xl flex items-center justify-center text-white shadow-lg">
                        <i class="fas fa-exclamation-triangle text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- المهام الأخيرة -->
    <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-200 bg-gray-50 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white shadow-md">
                    <i class="fas fa-tasks text-lg"></i>
                </div>
                <div>
                    <h3 class="text-lg font-black text-gray-900">المهام الأخيرة</h3>
                    <p class="text-xs text-gray-600 font-medium mt-1">آخر 10 مهام مخصصة لك</p>
                </div>
            </div>
            <a href="{{ route('employee.tasks.index') }}" 
               class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                <i class="fas fa-list mr-2"></i>
                عرض جميع المهام
            </a>
        </div>

        <div class="divide-y divide-gray-200">
            @forelse($tasks as $task)
            <div class="task-card px-6 py-4 hover:bg-gray-50 transition-all">
                <div class="flex items-start justify-between gap-4">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-3 mb-2">
                            <h4 class="text-base font-bold text-gray-900">{{ $task->title }}</h4>
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
                        </div>
                        @if($task->description)
                            <p class="text-sm text-gray-600 mb-2 line-clamp-2">{{ Str::limit($task->description, 100) }}</p>
                        @endif
                        <div class="flex items-center gap-4 text-xs text-gray-500 flex-wrap">
                            @if($task->assigner)
                                <span class="flex items-center gap-1">
                                    <i class="fas fa-user-tie"></i>
                                    {{ $task->assigner->name }}
                                </span>
                            @endif
                            @if($task->deadline)
                                <span class="flex items-center gap-1 {{ $task->deadline < now() && !in_array($task->status, ['completed', 'cancelled']) ? 'text-red-600 font-semibold' : '' }}">
                                    <i class="fas fa-calendar-alt"></i>
                                    {{ $task->deadline->format('Y-m-d') }}
                                </span>
                            @endif
                            @if($task->progress !== null)
                                <span class="flex items-center gap-1">
                                    <i class="fas fa-chart-line"></i>
                                    {{ $task->progress }}%
                                </span>
                            @endif
                        </div>
                    </div>
                    <a href="{{ route('employee.tasks.show', $task) }}" 
                       class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm rounded-lg font-medium transition-colors whitespace-nowrap">
                        <i class="fas fa-eye mr-2"></i>عرض
                    </a>
                </div>
            </div>
            @empty
            <div class="px-6 py-16 text-center">
                <div class="flex flex-col items-center gap-4">
                    <div class="w-16 h-16 bg-blue-50 rounded-2xl flex items-center justify-center">
                        <i class="fas fa-tasks text-3xl text-blue-600"></i>
                    </div>
                    <div>
                        <p class="font-bold text-gray-900 text-lg mb-1">لا توجد مهام حالياً</p>
                        <p class="text-sm text-gray-600 font-medium">سيتم إشعارك عند تعيين مهام جديدة لك</p>
                    </div>
                </div>
            </div>
            @endforelse
        </div>
    </div>

    <!-- إجراءات سريعة -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <a href="{{ route('employee.tasks.index') }}" 
           class="group rounded-xl border border-gray-200 bg-white p-6 hover:border-blue-300 hover:shadow-md transition-all duration-200">
            <div class="flex items-center justify-between mb-3">
                <div class="w-12 h-12 rounded-xl bg-blue-100 flex items-center justify-center text-blue-600 shadow-sm">
                    <i class="fas fa-tasks text-lg"></i>
                </div>
            </div>
            <h4 class="text-sm font-bold text-gray-900 mb-2">عرض جميع المهام</h4>
            <p class="text-xs text-gray-600 font-medium leading-relaxed">إدارة ومتابعة جميع مهامك في مكان واحد</p>
        </a>

        @if($user->employeeJob)
        <div class="rounded-xl border border-gray-200 bg-white p-6">
            <div class="flex items-center justify-between mb-3">
                <div class="w-12 h-12 rounded-xl bg-emerald-100 flex items-center justify-center text-emerald-600 shadow-sm">
                    <i class="fas fa-briefcase text-lg"></i>
                </div>
            </div>
            <h4 class="text-sm font-bold text-gray-900 mb-2">معلومات الوظيفة</h4>
            <p class="text-xs text-gray-600 font-medium leading-relaxed">
                <strong>{{ $user->employeeJob->name }}</strong>
                @if($user->employee_code)
                    <br>الرمز: {{ $user->employee_code }}
                @endif
            </p>
        </div>
        @endif

        <div class="rounded-xl border border-gray-200 bg-white p-6">
            <div class="flex items-center justify-between mb-3">
                <div class="w-12 h-12 rounded-xl bg-purple-100 flex items-center justify-center text-purple-600 shadow-sm">
                    <i class="fas fa-chart-bar text-lg"></i>
                </div>
            </div>
            <h4 class="text-sm font-bold text-gray-900 mb-2">إحصائيات الأداء</h4>
            <p class="text-xs text-gray-600 font-medium leading-relaxed">
                معدل الإنجاز: 
                <strong class="text-green-600">
                    {{ $stats['total_tasks'] > 0 ? round(($stats['completed_tasks'] / $stats['total_tasks']) * 100, 1) : 0 }}%
                </strong>
            </p>
        </div>
    </div>
</div>
@endsection
