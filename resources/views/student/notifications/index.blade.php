@extends('layouts.app')

@section('title', 'الإشعارات')
@section('header', 'الإشعارات')

@section('content')
<div class="w-full px-4 sm:px-6 lg:px-8 py-6 space-y-6">
    <!-- الهيدر والإحصائيات -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-4 sm:px-5 py-4 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <h1 class="text-xl sm:text-2xl font-bold text-gray-900">الإشعارات</h1>
            <div class="flex items-center gap-2">
                @if($stats['unread'] > 0)
                <button onclick="markAllAsRead()" class="inline-flex items-center gap-2 bg-emerald-500 hover:bg-emerald-600 text-white px-4 py-2 rounded-lg text-sm font-semibold transition-colors">
                    <i class="fas fa-check ml-2"></i> تحديد الكل كمقروء
                </button>
                @endif
                <button onclick="cleanup()" class="inline-flex items-center gap-2 bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm font-semibold transition-colors">
                    <i class="fas fa-broom ml-2"></i> تنظيف
                </button>
            </div>
        </div>
        <div class="p-4 sm:p-5">
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4">
                <div class="py-3 px-4 bg-gray-50 rounded-xl border border-gray-100 text-center">
                    <p class="text-xl sm:text-2xl font-bold text-gray-900">{{ $stats['total'] }}</p>
                    <p class="text-xs font-medium text-gray-500">إجمالي الإشعارات</p>
                </div>
                <div class="py-3 px-4 bg-sky-50 rounded-xl border border-sky-100 text-center">
                    <p class="text-xl sm:text-2xl font-bold text-sky-600">{{ $stats['unread'] }}</p>
                    <p class="text-xs font-medium text-gray-500">غير مقروءة</p>
                </div>
                <div class="py-3 px-4 bg-amber-50 rounded-xl border border-amber-100 text-center">
                    <p class="text-xl sm:text-2xl font-bold text-amber-600">{{ $stats['today'] }}</p>
                    <p class="text-xs font-medium text-gray-500">اليوم</p>
                </div>
                <div class="py-3 px-4 bg-red-50 rounded-xl border border-red-100 text-center">
                    <p class="text-xl sm:text-2xl font-bold text-red-600">{{ $stats['urgent'] }}</p>
                    <p class="text-xs font-medium text-gray-500">عاجلة</p>
                </div>
            </div>
        </div>
    </div>

    <!-- الفلاتر -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 sm:p-5">
        <form method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div>
                <label for="type" class="block text-sm font-medium text-gray-700 mb-1">نوع الإشعار</label>
                <select name="type" id="type" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm">
                    <option value="">جميع الأنواع</option>
                    @foreach($notificationTypes as $key => $type)
                        <option value="{{ $key }}" {{ request('type') == $key ? 'selected' : '' }}>{{ $type }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">الحالة</label>
                <select name="status" id="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm">
                    <option value="">جميع الحالات</option>
                    <option value="unread" {{ request('status') == 'unread' ? 'selected' : '' }}>غير مقروءة</option>
                    <option value="read" {{ request('status') == 'read' ? 'selected' : '' }}>مقروءة</option>
                </select>
            </div>

            <div>
                <label for="priority" class="block text-sm font-medium text-gray-700 mb-1">الأولوية</label>
                <select name="priority" id="priority" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm">
                    <option value="">جميع الأولويات</option>
                    @foreach($priorities as $key => $priority)
                        <option value="{{ $key }}" {{ request('priority') == $key ? 'selected' : '' }}>{{ $priority }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-end">
                <button type="submit" class="w-full bg-sky-500 hover:bg-sky-600 text-white px-4 py-2.5 rounded-lg text-sm font-semibold transition-colors">
                    <i class="fas fa-filter ml-2"></i>
                    فلترة
                </button>
            </div>
        </form>
    </div>

    <!-- قائمة الإشعارات -->
    @if($notifications->count() > 0)
        <div class="space-y-3">
            @foreach($notifications as $notification)
            <div class="bg-white rounded-xl border {{ !$notification->is_read ? 'border-sky-200 border-r-4 border-r-sky-500' : 'border-gray-200' }} shadow-sm overflow-hidden hover:shadow-md transition-shadow">
                <div class="p-6">
                    <div class="flex items-start justify-between">
                        <div class="flex items-start gap-4 flex-1 flex-row-reverse">
                            <!-- أيقونة الإشعار -->
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 rounded-full flex items-center justify-center
                                    @if($notification->type_color == 'blue') bg-sky-100
                                    @elseif($notification->type_color == 'green') bg-emerald-100
                                    @elseif($notification->type_color == 'yellow') bg-amber-100
                                    @elseif($notification->type_color == 'red') bg-red-100
                                    @elseif($notification->type_color == 'purple') bg-violet-100
                                    @elseif($notification->type_color == 'orange') bg-amber-100
                                    @else bg-gray-100
                                    @endif">
                                    <i class="{{ $notification->type_icon }} 
                                        @if($notification->type_color == 'blue') text-sky-600
                                        @elseif($notification->type_color == 'green') text-emerald-600
                                        @elseif($notification->type_color == 'yellow') text-amber-600
                                        @elseif($notification->type_color == 'red') text-red-600
                                        @elseif($notification->type_color == 'purple') text-violet-600
                                        @elseif($notification->type_color == 'orange') text-amber-600
                                        @else text-gray-600
                                        @endif"></i>
                                </div>
                            </div>
                            
                            <!-- محتوى الإشعار -->
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-3 mb-2">
                                    <h3 class="text-lg font-medium text-gray-900">{{ $notification->title }}</h3>
                                    
                                    @if($notification->priority !== 'normal')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            @if($notification->priority_color == 'red') bg-red-100 text-red-800
                                            @elseif($notification->priority_color == 'yellow') bg-amber-100 text-amber-800
                                            @else bg-gray-100 text-gray-800
                                            @endif">
                                            {{ $priorities[$notification->priority] ?? $notification->priority }}
                                        </span>
                                    @endif

                                    @if(!$notification->is_read)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-semibold bg-sky-100 text-sky-800">
                                            <i class="fas fa-circle text-[6px] ml-1"></i> جديد
                                        </span>
                                    @endif
                                </div>
                                
                                <p class="text-gray-600 mb-3">{{ $notification->message }}</p>
                                
                                <div class="flex items-center gap-6 text-sm text-gray-500">
                                    <span class="flex items-center">
                                        <i class="fas fa-user ml-1"></i>
                                        من: {{ $notification->sender->name ?? 'النظام' }}
                                    </span>
                                    
                                    <span class="flex items-center">
                                        <i class="fas fa-clock ml-1"></i>
                                        {{ $notification->created_at->diffForHumans() }}
                                    </span>

                                    @if($notification->expires_at)
                                        <span class="flex items-center">
                                            <i class="fas fa-hourglass-end ml-1"></i>
                                            ينتهي {{ $notification->expires_at->diffForHumans() }}
                                        </span>
                                    @endif
                                </div>

                                @if($notification->action_url && $notification->action_text)
                                    <div class="mt-4">
                                        <a href="{{ route('notifications.go', $notification) }}" class="inline-flex items-center gap-2 text-sky-600 hover:text-sky-700 text-sm font-semibold transition-colors">
                                            {{ $notification->action_text }} <i class="fas fa-external-link-alt text-xs"></i>
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="flex items-center gap-2 flex-shrink-0">
                            @if(!$notification->is_read)
                            <button onclick="markAsRead({{ $notification->id }})" class="p-2 text-emerald-600 hover:bg-emerald-50 rounded-lg transition-colors" title="تحديد كمقروء"><i class="fas fa-check"></i></button>
                            @endif
                            <a href="{{ route('notifications.show', $notification) }}" class="p-2 text-sky-600 hover:bg-sky-50 rounded-lg transition-colors" title="عرض"><i class="fas fa-eye"></i></a>
                            <button onclick="deleteNotification({{ $notification->id }})" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="حذف"><i class="fas fa-trash"></i></button>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        @if($notifications->hasPages())
        <div class="flex justify-center mt-6">{{ $notifications->appends(request()->query())->links() }}</div>
        @endif
    @else
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-10 sm:p-12 text-center">
            <div class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-4 text-gray-400">
                <i class="fas fa-bell-slash text-2xl"></i>
            </div>
            <h3 class="text-lg font-bold text-gray-900 mb-2">لا توجد إشعارات</h3>
            <p class="text-sm text-gray-500">ستظهر هنا آخر التحديثات والرسائل المهمة</p>
        </div>
    @endif
</div>

@push('scripts')
<script>
function markAsRead(notificationId) {
    fetch(`/notifications/${notificationId}/mark-read`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}

function markAllAsRead() {
    if (confirm('هل تريد تحديد جميع الإشعارات كمقروءة؟')) {
        fetch('/notifications/mark-all-read', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                location.reload();
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    }
}

function deleteNotification(notificationId) {
    if (confirm('هل تريد حذف هذا الإشعار؟')) {
        fetch(`/notifications/${notificationId}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    }
}

function cleanup() {
    if (confirm('هل تريد حذف الإشعارات المقروءة الأقدم من 30 يوم؟')) {
        fetch('/notifications/cleanup', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                location.reload();
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    }
}
</script>
@endpush
@endsection