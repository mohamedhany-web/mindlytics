@extends('layouts.app')

@section('title', __('instructor.tasks_from_management') . ' - Mindlytics')
@section('header', __('instructor.tasks_from_management'))

@section('content')
<div class="space-y-6">
    <!-- الهيدر -->
    <div class="rounded-2xl p-5 sm:p-6 bg-white border border-slate-200 shadow-sm">
        <h1 class="text-2xl sm:text-3xl font-bold text-slate-800 mb-1">{{ __('instructor.tasks_from_management') }}</h1>
        <p class="text-sm text-slate-500">{{ __('instructor.tasks_assigned_by_management') }}</p>
    </div>

    <!-- الإحصائيات -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="rounded-2xl p-5 bg-white border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1">{{ __('instructor.total_tasks') }}</p>
                <p class="text-2xl sm:text-3xl font-bold text-slate-800">{{ $stats['total'] ?? 0 }}</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-sky-50 flex items-center justify-center">
                <i class="fas fa-check-square text-sky-600 text-lg"></i>
            </div>
        </div>
        <div class="rounded-2xl p-5 bg-white border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1">{{ __('instructor.pending') }}</p>
                <p class="text-2xl sm:text-3xl font-bold text-slate-800">{{ $stats['pending'] ?? 0 }}</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-amber-50 flex items-center justify-center">
                <i class="fas fa-clock text-amber-600 text-lg"></i>
            </div>
        </div>
        <div class="rounded-2xl p-5 bg-white border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1">{{ __('instructor.in_progress') }}</p>
                <p class="text-2xl sm:text-3xl font-bold text-slate-800">{{ $stats['in_progress'] ?? 0 }}</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center">
                <i class="fas fa-spinner text-blue-600 text-lg"></i>
            </div>
        </div>
        <div class="rounded-2xl p-5 bg-white border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1">{{ __('instructor.completed_attempts') }}</p>
                <p class="text-2xl sm:text-3xl font-bold text-slate-800">{{ $stats['completed'] ?? 0 }}</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-50 flex items-center justify-center">
                <i class="fas fa-check-double text-emerald-600 text-lg"></i>
            </div>
        </div>
    </div>

    <!-- الفلاتر -->
    <div class="bg-white rounded-2xl p-5 sm:p-6 border border-slate-200 shadow-sm">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label for="search" class="block text-sm font-semibold text-slate-700 mb-2">{{ __('common.search') }}</label>
                <input type="text" name="search" id="search" value="{{ request('search') }}"
                       placeholder="{{ __('instructor.search_in_tasks') }}"
                       class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-slate-800 focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 transition-colors">
            </div>
            <div>
                <label for="status" class="block text-sm font-semibold text-slate-700 mb-2">{{ __('common.status') }}</label>
                <select name="status" id="status" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-slate-800 focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 transition-colors">
                    <option value="">{{ __('instructor.all_statuses') }}</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>{{ __('instructor.pending') }}</option>
                    <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>{{ __('instructor.in_progress') }}</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>{{ __('instructor.completed_attempts') }}</option>
                </select>
            </div>
            <div>
                <label for="priority" class="block text-sm font-semibold text-slate-700 mb-2">{{ __('instructor.priority') }}</label>
                <select name="priority" id="priority" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-slate-800 focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 transition-colors">
                    <option value="">{{ __('instructor.all_priorities') }}</option>
                    <option value="low" {{ request('priority') == 'low' ? 'selected' : '' }}>{{ __('instructor.low') }}</option>
                    <option value="medium" {{ request('priority') == 'medium' ? 'selected' : '' }}>{{ __('instructor.medium') }}</option>
                    <option value="high" {{ request('priority') == 'high' ? 'selected' : '' }}>{{ __('instructor.high') }}</option>
                    <option value="urgent" {{ request('priority') == 'urgent' ? 'selected' : '' }}>{{ __('instructor.urgent') }}</option>
                </select>
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 inline-flex items-center justify-center gap-2 bg-sky-500 hover:bg-sky-600 text-white px-5 py-2.5 rounded-xl font-semibold transition-colors">
                    <i class="fas fa-search"></i>
                    <span>{{ __('common.search') }}</span>
                </button>
                @if(request()->anyFilled(['search', 'status', 'priority']))
                    <a href="{{ route('instructor.tasks.index') }}" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-semibold transition-colors inline-flex items-center justify-center">
                        <i class="fas fa-times"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- قائمة المهام -->
    @if($tasks->count() > 0)
        <div class="space-y-4">
            @foreach($tasks as $task)
                <div class="rounded-xl bg-white border border-slate-200 shadow-sm hover:border-sky-300 hover:shadow-md transition-all p-5">
                    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                        <div class="flex-1 min-w-0">
                            <div class="flex flex-wrap items-center gap-2 mb-2">
                                <h3 class="text-lg font-bold text-slate-800">{{ $task->title }}</h3>
                                @if($task->assigner)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-semibold bg-slate-100 text-slate-600">{{ __('instructor.from_management') }}</span>
                                @endif
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-semibold
                                    @if($task->priority == 'urgent') bg-rose-100 text-rose-700
                                    @elseif($task->priority == 'high') bg-amber-100 text-amber-700
                                    @elseif($task->priority == 'medium') bg-sky-100 text-sky-700
                                    @else bg-slate-100 text-slate-600
                                    @endif">
                                    @if($task->priority == 'urgent') {{ __('instructor.urgent') }}
                                    @elseif($task->priority == 'high') {{ __('instructor.high') }}
                                    @elseif($task->priority == 'medium') {{ __('instructor.medium') }}
                                    @else {{ __('instructor.low') }}
                                    @endif
                                </span>
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-semibold
                                    @if($task->status == 'completed') bg-emerald-100 text-emerald-700
                                    @elseif($task->status == 'in_progress') bg-blue-100 text-blue-700
                                    @else bg-amber-100 text-amber-700
                                    @endif">
                                    @if($task->status == 'completed') {{ __('instructor.completed_attempts') }}
                                    @elseif($task->status == 'in_progress') {{ __('instructor.in_progress') }}
                                    @else {{ __('instructor.pending') }}
                                    @endif
                                </span>
                            </div>
                            @if($task->description)
                                <p class="text-sm text-slate-600 mb-3 line-clamp-2">{{ $task->description }}</p>
                            @endif
                            <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-sm text-slate-500">
                                @if($task->relatedCourse)
                                    <span><i class="fas fa-book text-sky-500 ml-1"></i> {{ $task->relatedCourse->title ?? '—' }}</span>
                                @endif
                                @if($task->relatedLecture)
                                    <span><i class="fas fa-chalkboard-teacher text-violet-500 ml-1"></i> {{ $task->relatedLecture->title ?? '—' }}</span>
                                @endif
                                @if($task->due_date)
                                    <span><i class="fas fa-calendar text-slate-400 ml-1"></i> {{ $task->due_date->format('Y/m/d') }}</span>
                                    @if($task->due_date->isPast() && $task->status != 'completed')
                                        <span class="text-rose-600 font-semibold">{{ __('instructor.late') }}</span>
                                    @endif
                                @endif
                            </div>
                        </div>
                        <div class="flex items-center gap-2 shrink-0">
                            <a href="{{ route('instructor.tasks.show', $task) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-sky-500 hover:bg-sky-600 text-white rounded-xl font-semibold text-sm transition-colors">
                                <i class="fas fa-eye"></i>
                                {{ __('instructor.view_and_submit') }}
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        @if($tasks->hasPages())
            <div class="mt-6">
                {{ $tasks->appends(request()->query())->links() }}
            </div>
        @endif
    @else
        <div class="rounded-2xl p-12 bg-white border border-slate-200 shadow-sm text-center">
            <div class="w-16 h-16 rounded-2xl bg-slate-100 flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-check-square text-3xl text-slate-400"></i>
            </div>
            <h3 class="text-xl font-bold text-slate-800 mb-2">{{ __('instructor.no_tasks_from_management') }}</h3>
            <p class="text-sm text-slate-500 max-w-md mx-auto">{{ __('instructor.no_tasks_description') }}</p>
        </div>
    @endif
</div>
@endsection
