@extends('layouts.admin')

@section('title', 'المهام - Mindlytics')
@section('header', 'المهام')

@section('content')
@php
    $statCards = [
        [
            'label' => 'إجمالي المهام',
            'value' => number_format($stats['total'] ?? 0),
            'icon' => 'fas fa-list-check',
            'color' => 'text-sky-500 bg-sky-100/70',
            'description' => 'كل المهام المسجلة',
        ],
        [
            'label' => 'في الانتظار',
            'value' => number_format($stats['pending'] ?? 0),
            'icon' => 'fas fa-clock',
            'color' => 'text-amber-500 bg-amber-100/70',
            'description' => 'مهام قيد الانتظار',
        ],
        [
            'label' => 'قيد التنفيذ',
            'value' => number_format($stats['in_progress'] ?? 0),
            'icon' => 'fas fa-spinner',
            'color' => 'text-blue-500 bg-blue-100/70',
            'description' => 'مهام قيد التنفيذ',
        ],
        [
            'label' => 'مكتملة',
            'value' => number_format($stats['completed'] ?? 0),
            'icon' => 'fas fa-check-circle',
            'color' => 'text-emerald-500 bg-emerald-100/70',
            'description' => 'مهام مكتملة',
        ],
    ];
    
    $priorityBadges = [
        'urgent' => ['label' => 'عاجلة', 'classes' => 'bg-rose-100 text-rose-700'],
        'high' => ['label' => 'عالية', 'classes' => 'bg-amber-100 text-amber-700'],
        'medium' => ['label' => 'متوسطة', 'classes' => 'bg-sky-100 text-sky-700'],
        'low' => ['label' => 'منخفضة', 'classes' => 'bg-slate-100 text-slate-700'],
    ];
    
    $statusBadges = [
        'completed' => ['label' => 'مكتملة', 'classes' => 'bg-emerald-100 text-emerald-700'],
        'in_progress' => ['label' => 'قيد التنفيذ', 'classes' => 'bg-blue-100 text-blue-700'],
        'pending' => ['label' => 'في الانتظار', 'classes' => 'bg-amber-100 text-amber-700'],
        'cancelled' => ['label' => 'ملغاة', 'classes' => 'bg-rose-100 text-rose-700'],
    ];
@endphp

<div class="space-y-6 sm:space-y-10">
    <!-- Header Section -->
    <section class="rounded-3xl bg-white/95 backdrop-blur border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-5 py-6 sm:px-8 lg:px-12 border-b border-slate-200 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-2xl font-bold text-slate-900">لوحة إدارة المهام</h2>
                <p class="text-sm text-slate-500 mt-2">إدارة المهام الشخصية والمرتبطة بالكورسات</p>
            </div>
            <a href="{{ route('admin.tasks.create') }}" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-white bg-sky-600 rounded-xl shadow hover:bg-sky-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-500 transition-all">
                <i class="fas fa-plus"></i>
                إضافة مهمة جديدة
            </a>
        </div>
        
        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 p-5 sm:p-8">
            @foreach ($statCards as $card)
                <div class="rounded-2xl border border-slate-200 bg-white/70 p-5 flex flex-col gap-4 card-hover-effect">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-widest text-slate-500">{{ $card['label'] }}</p>
                            <p class="mt-3 text-2xl font-bold text-slate-900">{{ $card['value'] }}</p>
                        </div>
                        <span class="flex h-12 w-12 items-center justify-center rounded-2xl {{ $card['color'] }}">
                            <i class="{{ $card['icon'] }} text-xl"></i>
                        </span>
                    </div>
                    <p class="text-xs text-slate-500">{{ $card['description'] }}</p>
                </div>
            @endforeach
        </div>
    </section>

    <!-- Filters Section -->
    <section class="rounded-3xl bg-white/95 backdrop-blur border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-5 py-6 sm:px-8 lg:px-12 border-b border-slate-200">
            <h3 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                <i class="fas fa-filter text-sky-600"></i>
                البحث والفلترة
            </h3>
        </div>
        <div class="px-5 py-6 sm:px-8 lg:px-12">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-2">البحث</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-3 flex items-center text-slate-400">
                            <i class="fas fa-search"></i>
                        </span>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="ابحث في المهام..."
                               class="w-full rounded-2xl border border-slate-200 bg-white/80 px-4 py-2.5 pr-10 text-sm text-slate-900 focus:ring-2 focus:ring-sky-500 focus:border-sky-400 transition-all">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-2">الحالة</label>
                    <select name="status" class="w-full rounded-2xl border border-slate-200 bg-white/80 px-4 py-2.5 text-sm text-slate-900 focus:ring-2 focus:ring-sky-500 focus:border-sky-400 transition-all">
                        <option value="">جميع الحالات</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>في الانتظار</option>
                        <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>قيد التنفيذ</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>مكتملة</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>ملغاة</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-2">الأولوية</label>
                    <select name="priority" class="w-full rounded-2xl border border-slate-200 bg-white/80 px-4 py-2.5 text-sm text-slate-900 focus:ring-2 focus:ring-sky-500 focus:border-sky-400 transition-all">
                        <option value="">جميع الأولويات</option>
                        <option value="low" {{ request('priority') == 'low' ? 'selected' : '' }}>منخفضة</option>
                        <option value="medium" {{ request('priority') == 'medium' ? 'selected' : '' }}>متوسطة</option>
                        <option value="high" {{ request('priority') == 'high' ? 'selected' : '' }}>عالية</option>
                        <option value="urgent" {{ request('priority') == 'urgent' ? 'selected' : '' }}>عاجلة</option>
                    </select>
                </div>
                <div class="flex items-end">
                    <button type="submit" class="w-full inline-flex items-center justify-center gap-2 rounded-2xl bg-sky-600 px-4 py-2.5 text-sm font-semibold text-white shadow hover:bg-sky-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-500 transition-all">
                        <i class="fas fa-search"></i>
                        بحث
                    </button>
                </div>
            </form>
        </div>
    </section>

    <!-- Tasks List -->
    <section class="rounded-3xl bg-white/95 backdrop-blur border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-5 py-6 sm:px-8 lg:px-12 border-b border-slate-200">
            <h3 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                <i class="fas fa-list text-sky-600"></i>
                قائمة المهام
            </h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr class="text-xs font-semibold uppercase tracking-widest text-slate-500">
                        <th class="px-6 py-4 text-right">المهمة</th>
                        <th class="px-6 py-4 text-right">المستخدم</th>
                        <th class="px-6 py-4 text-right">الأولوية</th>
                        <th class="px-6 py-4 text-right">الحالة</th>
                        <th class="px-6 py-4 text-right">تاريخ الاستحقاق</th>
                        <th class="px-6 py-4 text-center">الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-slate-200">
                    @forelse($tasks as $task)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-slate-900">{{ $task->title }}</div>
                            @if($task->description)
                                <div class="text-xs text-slate-500 mt-1">{{ Str::limit($task->description, 50) }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-700">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 bg-gradient-to-br from-sky-500 to-blue-600 rounded-lg flex items-center justify-center text-white font-bold text-xs">
                                    {{ mb_substr($task->user->name, 0, 1, 'UTF-8') }}
                                </div>
                                <span>{{ $task->user->name }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php $priorityBadge = $priorityBadges[$task->priority] ?? null; @endphp
                            @if($priorityBadge)
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold {{ $priorityBadge['classes'] }}">
                                    <span class="h-2 w-2 rounded-full bg-current"></span>
                                    {{ $priorityBadge['label'] }}
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php $statusBadge = $statusBadges[$task->status] ?? null; @endphp
                            @if($statusBadge)
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold {{ $statusBadge['classes'] }}">
                                    <span class="h-2 w-2 rounded-full bg-current"></span>
                                    {{ $statusBadge['label'] }}
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-700">
                            @if($task->due_date)
                                <div class="font-medium">{{ $task->due_date->format('Y-m-d') }}</div>
                                @if($task->due_date->isPast() && $task->status != 'completed')
                                    <span class="text-xs text-rose-600">متأخرة</span>
                                @endif
                            @else
                                <span class="text-slate-400">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('admin.tasks.show', $task) }}" 
                                   class="w-9 h-9 flex items-center justify-center bg-sky-50 hover:bg-sky-100 text-sky-600 rounded-xl transition-colors"
                                   title="عرض التفاصيل">
                                    <i class="fas fa-eye text-sm"></i>
                                </a>
                                <a href="{{ route('admin.tasks.edit', $task) }}" 
                                   class="w-9 h-9 flex items-center justify-center bg-blue-50 hover:bg-blue-100 text-blue-600 rounded-xl transition-colors"
                                   title="تعديل">
                                    <i class="fas fa-edit text-sm"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-slate-500">
                            <div class="flex flex-col items-center gap-4">
                                <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-list-check text-slate-400 text-2xl"></i>
                                </div>
                                <p class="font-medium">لا توجد مهام</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($tasks->hasPages())
        <div class="px-6 py-4 border-t border-slate-200 bg-slate-50">
            {{ $tasks->appends(request()->query())->links() }}
        </div>
        @endif
    </section>
</div>
@endsection
