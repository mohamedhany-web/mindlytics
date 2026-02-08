@extends('layouts.admin')

@section('title', 'إشعارات الموظفين')
@section('header', 'إشعارات الموظفين')

@section('content')
@php
    $priorities = \App\Models\Notification::getPriorities();
@endphp
<div class="space-y-6">
    <!-- الهيدر -->
    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-6 py-5 bg-slate-50 border-b border-slate-200 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white shadow-md">
                    <i class="fas fa-user-tie text-lg"></i>
                </div>
                <div>
                    <h2 class="text-2xl font-black text-slate-900">إشعارات الموظفين</h2>
                    <p class="text-sm text-slate-600 mt-1">إرسال إشعارات مخصصة للموظفين ومتابعة حالة القراءة.</p>
                </div>
            </div>
            <a href="{{ route('admin.employee-notifications.create') }}" class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-blue-600 to-blue-500 hover:from-blue-700 hover:to-blue-600 px-4 py-2.5 text-sm font-semibold text-white shadow-md hover:shadow-lg transition-all duration-200">
                <i class="fas fa-paper-plane"></i>
                إرسال إشعار جديد
            </a>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 p-6">
            <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex-1">
                        <p class="text-xs font-semibold text-slate-600 mb-1">إجمالي الإشعارات</p>
                        <p class="text-2xl font-black text-slate-900">{{ $stats['total'] }}</p>
                    </div>
                    <div class="w-12 h-12 rounded-lg bg-blue-100 flex items-center justify-center text-blue-600 shadow-sm">
                        <i class="fas fa-bell text-lg"></i>
                    </div>
                </div>
            </div>
            <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex-1">
                        <p class="text-xs font-semibold text-slate-600 mb-1">غير المقروء</p>
                        <p class="text-2xl font-black text-slate-900">{{ $stats['unread'] }}</p>
                    </div>
                    <div class="w-12 h-12 rounded-lg bg-amber-100 flex items-center justify-center text-amber-600 shadow-sm">
                        <i class="fas fa-envelope-open-text text-lg"></i>
                    </div>
                </div>
            </div>
            <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex-1">
                        <p class="text-xs font-semibold text-slate-600 mb-1">أُرسلت اليوم</p>
                        <p class="text-2xl font-black text-slate-900">{{ $stats['today'] }}</p>
                    </div>
                    <div class="w-12 h-12 rounded-lg bg-purple-100 flex items-center justify-center text-purple-600 shadow-sm">
                        <i class="fas fa-calendar-day text-lg"></i>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- الفلاتر -->
    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-200 bg-slate-50">
            <h3 class="text-lg font-black text-slate-900 mb-2 flex items-center gap-2">
                <i class="fas fa-filter text-blue-600"></i>
                البحث والفلترة
            </h3>
        </div>
        <div class="p-6">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-2">الحالة</label>
                    <select name="status" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">جميع الحالات</option>
                        <option value="unread" {{ request('status') == 'unread' ? 'selected' : '' }}>غير مقروءة</option>
                        <option value="read" {{ request('status') == 'read' ? 'selected' : '' }}>مقروءة</option>
                    </select>
                </div>
                <div class="flex items-end">
                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2.5 rounded-xl font-semibold transition-colors">
                        <i class="fas fa-filter ml-2"></i>
                        فلترة
                    </button>
                </div>
            </form>
        </div>
    </section>

    <!-- قائمة الإشعارات -->
    @if($notifications->count() > 0)
        <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
            <div class="divide-y divide-slate-200">
                @foreach($notifications as $notification)
                <div class="p-6 hover:bg-slate-50 transition-colors">
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex-1">
                            <div class="flex items-center gap-3 mb-2">
                                <h3 class="text-lg font-black text-slate-900">{{ $notification->title }}</h3>
                                <span class="px-2 py-1 text-xs font-semibold rounded-full
                                    @if($notification->priority === 'urgent') bg-red-100 text-red-800
                                    @elseif($notification->priority === 'high') bg-orange-100 text-orange-800
                                    @elseif($notification->priority === 'normal') bg-blue-100 text-blue-800
                                    @else bg-gray-100 text-gray-800
                                    @endif">
                                    {{ $priorities[$notification->priority] ?? $notification->priority }}
                                </span>
                                @if(!$notification->is_read)
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-amber-100 text-amber-800">غير مقروء</span>
                                @endif
                            </div>
                            <p class="text-sm text-slate-600 mb-3">{{ Str::limit($notification->message, 150) }}</p>
                            <div class="flex items-center gap-4 text-xs text-slate-500">
                                <span class="flex items-center gap-1">
                                    <i class="fas fa-user"></i>
                                    {{ $notification->user->name ?? 'غير محدد' }}
                                </span>
                                <span class="flex items-center gap-1">
                                    <i class="fas fa-clock"></i>
                                    {{ $notification->created_at->diffForHumans() }}
                                </span>
                            </div>
                        </div>
                        <a href="{{ route('admin.employee-notifications.show', $notification) }}" 
                           class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm rounded-xl font-semibold transition-colors whitespace-nowrap">
                            <i class="fas fa-eye ml-2"></i>
                            عرض
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
            <div class="px-6 py-4 border-t border-slate-200">
                {{ $notifications->links() }}
            </div>
        </section>
    @else
        <section class="rounded-2xl bg-white border border-slate-200 shadow-lg p-16 text-center">
            <div class="flex flex-col items-center gap-4">
                <div class="w-20 h-20 bg-blue-50 rounded-2xl flex items-center justify-center">
                    <i class="fas fa-bell text-4xl text-blue-500"></i>
                </div>
                <div>
                    <p class="font-black text-gray-900 text-xl mb-2">لا توجد إشعارات</p>
                    <p class="text-sm text-gray-600">لم يتم إرسال أي إشعارات للموظفين بعد</p>
                </div>
                <a href="{{ route('admin.employee-notifications.create') }}" 
                   class="mt-4 inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-xl font-semibold transition-colors">
                    <i class="fas fa-paper-plane"></i>
                    إرسال إشعار جديد
                </a>
            </div>
        </section>
    @endif
</div>
@endsection
