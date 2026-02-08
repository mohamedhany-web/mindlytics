@extends('layouts.app')

@section('title', 'المهام - Mindlytics')
@section('header', 'المهام')

@push('styles')
<style>
    .task-card {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        background: linear-gradient(to bottom, #ffffff 0%, #f8fafc 100%);
        border: 2px solid rgba(44, 169, 189, 0.1);
        position: relative;
        overflow: hidden;
    }

    .task-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, #2CA9BD, #65DBE4, #2CA9BD);
        background-size: 200% 100%;
        animation: gradientFlow 3s ease infinite;
    }

    .task-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 20px 40px rgba(44, 169, 189, 0.15), 0 0 20px rgba(101, 219, 228, 0.1);
        border-color: rgba(44, 169, 189, 0.3);
    }

    .task-card:hover::before {
        animation-duration: 1s;
    }

    .stats-card {
        background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
        border: 2px solid rgba(44, 169, 189, 0.1);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
    }

    .stats-card::before {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        width: 100px;
        height: 100px;
        background: radial-gradient(circle, rgba(44, 169, 189, 0.1) 0%, transparent 70%);
        border-radius: 50%;
        transform: translate(30%, -30%);
    }

    .stats-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 24px rgba(44, 169, 189, 0.15);
        border-color: rgba(44, 169, 189, 0.3);
    }

    @keyframes gradientFlow {
        0%, 100% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
    }

    .priority-badge {
        font-weight: bold;
    }

    .priority-low { background: linear-gradient(135deg, #10b981, #059669); }
    .priority-medium { background: linear-gradient(135deg, #3b82f6, #2563eb); }
    .priority-high { background: linear-gradient(135deg, #f59e0b, #d97706); }
    .priority-urgent { background: linear-gradient(135deg, #ef4444, #dc2626); }
</style>
@endpush

@section('content')
<div class="space-y-6">
    <!-- الهيدر المحسن -->
    <div class="bg-gradient-to-r from-[#2CA9BD]/10 via-[#65DBE4]/10 to-[#2CA9BD]/10 rounded-2xl p-6 border-2 border-[#2CA9BD]/20 shadow-lg">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-black text-[#1C2C39] mb-2">المهام</h1>
                <p class="text-sm sm:text-base text-[#1F3A56] font-medium">إدارة مهامك الشخصية</p>
            </div>
            <a href="{{ route('instructor.tasks.create') }}" 
               class="inline-flex items-center gap-2 bg-gradient-to-r from-[#2CA9BD] to-[#65DBE4] hover:from-[#1F3A56] hover:to-[#2CA9BD] text-white px-6 py-3 rounded-xl font-bold shadow-lg shadow-[#2CA9BD]/30 hover:shadow-xl transition-all duration-300 transform hover:scale-105">
                <i class="fas fa-plus"></i>
                <span>إضافة مهمة جديدة</span>
            </a>
        </div>
    </div>

    <!-- الإحصائيات المحسنة -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
        <div class="stats-card rounded-2xl p-5 sm:p-6">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-xs sm:text-sm font-semibold text-[#1F3A56] mb-2 uppercase tracking-wide">إجمالي المهام</p>
                    <p class="text-3xl sm:text-4xl font-black text-[#2CA9BD] leading-none">{{ $stats['total'] ?? 0 }}</p>
                </div>
                <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-2xl bg-gradient-to-br from-[#2CA9BD] to-[#65DBE4] flex items-center justify-center text-white shadow-lg">
                    <i class="fas fa-check-square text-xl sm:text-2xl"></i>
                </div>
            </div>
        </div>
        <div class="stats-card rounded-2xl p-5 sm:p-6">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-xs sm:text-sm font-semibold text-[#1F3A56] mb-2 uppercase tracking-wide">معلقة</p>
                    <p class="text-3xl sm:text-4xl font-black text-[#FFD34E] leading-none">{{ $stats['pending'] ?? 0 }}</p>
                </div>
                <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-2xl bg-gradient-to-br from-[#FFD34E] to-amber-500 flex items-center justify-center text-white shadow-lg">
                    <i class="fas fa-clock text-xl sm:text-2xl"></i>
                </div>
            </div>
        </div>
        <div class="stats-card rounded-2xl p-5 sm:p-6">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-xs sm:text-sm font-semibold text-[#1F3A56] mb-2 uppercase tracking-wide">قيد التنفيذ</p>
                    <p class="text-3xl sm:text-4xl font-black text-blue-600 leading-none">{{ $stats['in_progress'] ?? 0 }}</p>
                </div>
                <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-2xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white shadow-lg">
                    <i class="fas fa-spinner text-xl sm:text-2xl"></i>
                </div>
            </div>
        </div>
        <div class="stats-card rounded-2xl p-5 sm:p-6">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-xs sm:text-sm font-semibold text-[#1F3A56] mb-2 uppercase tracking-wide">مكتملة</p>
                    <p class="text-3xl sm:text-4xl font-black text-green-600 leading-none">{{ $stats['completed'] ?? 0 }}</p>
                </div>
                <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-2xl bg-gradient-to-br from-green-500 to-emerald-600 flex items-center justify-center text-white shadow-lg">
                    <i class="fas fa-check-double text-xl sm:text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- الفلاتر المحسنة -->
    <div class="bg-white rounded-2xl shadow-lg p-5 sm:p-6 border-2 border-[#2CA9BD]/10">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label for="search" class="block text-sm font-bold text-[#1C2C39] mb-2">البحث</label>
                <input type="text" name="search" id="search" value="{{ request('search') }}" 
                       placeholder="البحث في المهام..."
                       class="w-full px-4 py-3 border-2 border-[#2CA9BD]/20 rounded-xl bg-white text-[#1C2C39] font-medium focus:border-[#2CA9BD] focus:ring-4 focus:ring-[#2CA9BD]/20 transition-all">
            </div>

            <div>
                <label for="status" class="block text-sm font-bold text-[#1C2C39] mb-2">الحالة</label>
                <select name="status" id="status" class="w-full px-4 py-3 border-2 border-[#2CA9BD]/20 rounded-xl bg-white text-[#1C2C39] font-medium focus:border-[#2CA9BD] focus:ring-4 focus:ring-[#2CA9BD]/20 transition-all">
                    <option value="">جميع الحالات</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>معلقة</option>
                    <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>قيد التنفيذ</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>مكتملة</option>
                </select>
            </div>

            <div class="flex items-end gap-2">
                <div class="flex-1">
                    <label for="priority" class="block text-sm font-bold text-[#1C2C39] mb-2">الأولوية</label>
                    <select name="priority" id="priority" class="w-full px-4 py-3 border-2 border-[#2CA9BD]/20 rounded-xl bg-white text-[#1C2C39] font-medium focus:border-[#2CA9BD] focus:ring-4 focus:ring-[#2CA9BD]/20 transition-all">
                        <option value="">جميع الأولويات</option>
                        <option value="low" {{ request('priority') == 'low' ? 'selected' : '' }}>منخفضة</option>
                        <option value="medium" {{ request('priority') == 'medium' ? 'selected' : '' }}>متوسطة</option>
                        <option value="high" {{ request('priority') == 'high' ? 'selected' : '' }}>عالية</option>
                        <option value="urgent" {{ request('priority') == 'urgent' ? 'selected' : '' }}>عاجلة</option>
                    </select>
                </div>
                <button type="submit" class="px-4 py-3 bg-gradient-to-r from-[#2CA9BD] to-[#65DBE4] hover:from-[#1F3A56] hover:to-[#2CA9BD] text-white rounded-xl font-bold shadow-lg shadow-[#2CA9BD]/30 hover:shadow-xl transition-all duration-300 transform hover:scale-105">
                    <i class="fas fa-search"></i>
                </button>
                @if(request()->anyFilled(['search', 'status', 'priority']))
                    <a href="{{ route('instructor.tasks.index') }}" class="px-4 py-3 bg-gray-500 hover:bg-gray-600 text-white rounded-xl font-bold transition-all duration-300">
                        <i class="fas fa-times"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- قائمة المهام المحسنة -->
    @if($tasks->count() > 0)
        <div class="grid grid-cols-1 gap-5 sm:gap-6">
            @foreach($tasks as $task)
            <div class="task-card rounded-2xl overflow-hidden">
                <div class="p-5 sm:p-6">
                    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                        <div class="flex-1">
                            <div class="flex items-center gap-3 mb-3">
                                <h3 class="text-lg sm:text-xl font-black text-[#1C2C39] flex-1">{{ $task->title }}</h3>
                                <div class="flex items-center gap-2">
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold text-white shadow-md priority-badge priority-{{ $task->priority }}">
                                        @if($task->priority == 'low')
                                            <i class="fas fa-arrow-down"></i>
                                            منخفضة
                                        @elseif($task->priority == 'medium')
                                            <i class="fas fa-minus"></i>
                                            متوسطة
                                        @elseif($task->priority == 'high')
                                            <i class="fas fa-arrow-up"></i>
                                            عالية
                                        @else
                                            <i class="fas fa-exclamation"></i>
                                            عاجلة
                                        @endif
                                    </span>
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold shadow-md
                                        @if($task->status == 'completed') bg-gradient-to-r from-green-500 to-emerald-600 text-white
                                        @elseif($task->status == 'in_progress') bg-gradient-to-r from-blue-500 to-indigo-600 text-white
                                        @else bg-gradient-to-r from-[#FFD34E] to-amber-500 text-white
                                        @endif">
                                        @if($task->status == 'completed')
                                            <i class="fas fa-check-double"></i>
                                            مكتملة
                                        @elseif($task->status == 'in_progress')
                                            <i class="fas fa-spinner"></i>
                                            قيد التنفيذ
                                        @else
                                            <i class="fas fa-clock"></i>
                                            معلقة
                                        @endif
                                    </span>
                                </div>
                            </div>
                            
                            @if($task->description)
                            <p class="text-sm text-[#1F3A56] mb-4 font-medium p-3 bg-gradient-to-r from-[#2CA9BD]/5 to-[#65DBE4]/5 rounded-xl border border-[#2CA9BD]/10 line-clamp-2">{{ $task->description }}</p>
                            @endif
                            
                            <div class="space-y-2.5 text-sm">
                                @if($task->relatedCourse)
                                <div class="flex items-center gap-2">
                                    <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-[#2CA9BD]/10 to-[#65DBE4]/10 flex items-center justify-center flex-shrink-0">
                                        <i class="fas fa-book text-[#2CA9BD] text-xs"></i>
                                    </div>
                                    <span class="text-[#1F3A56] font-medium">الكورس:</span>
                                    <span class="text-[#1C2C39] font-bold mr-2">{{ $task->relatedCourse->title ?? 'غير محدد' }}</span>
                                </div>
                                @endif
                                @if($task->relatedLecture)
                                <div class="flex items-center gap-2">
                                    <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-purple-500/10 to-indigo-500/10 flex items-center justify-center flex-shrink-0">
                                        <i class="fas fa-chalkboard-teacher text-purple-600 text-xs"></i>
                                    </div>
                                    <span class="text-[#1F3A56] font-medium">المحاضرة:</span>
                                    <span class="text-[#1C2C39] font-bold mr-2">{{ $task->relatedLecture->title ?? 'غير محدد' }}</span>
                                </div>
                                @endif
                                @if($task->due_date)
                                <div class="flex items-center gap-2">
                                    <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-red-500/10 to-rose-500/10 flex items-center justify-center flex-shrink-0">
                                        <i class="fas fa-calendar-alt text-red-600 text-xs"></i>
                                    </div>
                                    <span class="text-[#1F3A56] font-medium">تاريخ الاستحقاق:</span>
                                    <span class="text-[#1C2C39] font-bold mr-2">{{ $task->due_date->format('Y/m/d H:i') }}</span>
                                    @if($task->due_date->isPast() && $task->status != 'completed')
                                    <span class="mr-2 text-red-600 font-bold">(متأخرة)</span>
                                    @endif
                                </div>
                                @endif
                            </div>
                        </div>
                        
                        <div class="flex items-center gap-2">
                            <a href="{{ route('instructor.tasks.show', $task) }}" 
                               class="inline-flex items-center gap-2 bg-gradient-to-r from-[#2CA9BD] to-[#65DBE4] hover:from-[#1F3A56] hover:to-[#2CA9BD] text-white px-5 py-3 rounded-xl font-bold shadow-lg shadow-[#2CA9BD]/30 hover:shadow-xl transition-all duration-300 transform hover:scale-105">
                                <i class="fas fa-eye"></i>
                                <span>عرض التفاصيل</span>
                            </a>
                            <a href="{{ route('instructor.tasks.edit', $task) }}" 
                               class="px-4 py-3 bg-gray-500 hover:bg-gray-600 text-white rounded-xl font-bold transition-all duration-300 transform hover:scale-105">
                                <i class="fas fa-edit"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-6 flex justify-center">
            <div class="bg-white rounded-2xl p-4 shadow-lg border-2 border-[#2CA9BD]/10">
                {{ $tasks->links() }}
            </div>
        </div>
    @else
        <!-- لا توجد مهام - محسن -->
        <div class="bg-gradient-to-r from-[#2CA9BD]/5 to-[#65DBE4]/5 rounded-2xl p-12 sm:p-16 text-center border-2 border-dashed border-[#2CA9BD]/20">
            <div class="w-32 h-32 bg-gradient-to-br from-[#2CA9BD]/10 to-[#65DBE4]/10 rounded-3xl flex items-center justify-center mx-auto mb-6 shadow-lg">
                <i class="fas fa-check-square text-5xl text-[#2CA9BD]"></i>
            </div>
            <h3 class="text-2xl sm:text-3xl font-black text-[#1C2C39] mb-3">لا توجد مهام</h3>
            <p class="text-base sm:text-lg text-[#1F3A56] mb-8 max-w-md mx-auto font-medium">لم يتم إنشاء أي مهام بعد</p>
            <a href="{{ route('instructor.tasks.create') }}" 
               class="inline-flex items-center gap-2 px-8 py-4 bg-gradient-to-r from-[#2CA9BD] to-[#65DBE4] hover:from-[#1F3A56] hover:to-[#2CA9BD] text-white font-bold rounded-xl shadow-lg shadow-[#2CA9BD]/30 hover:shadow-xl transition-all duration-300 transform hover:scale-105">
                <i class="fas fa-plus"></i>
                <span>إضافة مهمة جديدة</span>
            </a>
        </div>
    @endif
</div>
@endsection
