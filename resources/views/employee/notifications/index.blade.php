@extends('layouts.employee')

@section('title', 'الإشعارات')
@section('header', 'الإشعارات')

@section('content')
<div class="space-y-6">
    <!-- الهيدر والإحصائيات -->
    <div class="bg-white shadow-lg rounded-xl border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div>
                    <h3 class="text-lg font-black text-gray-900">الإشعارات</h3>
                    <p class="text-sm text-gray-600">آخر التحديثات والرسائل المهمة</p>
                </div>
                <div class="flex items-center gap-2">
                    @if($stats['unread'] > 0)
                        <button onclick="markAllAsRead()" 
                                class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                            <i class="fas fa-check ml-2"></i>
                            تحديد الكل كمقروء
                        </button>
                    @endif
                </div>
            </div>
        </div>

        <!-- إحصائيات سريعة -->
        <div class="p-6">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="text-center bg-blue-50 rounded-xl p-4 border-2 border-blue-200">
                    <div class="text-3xl font-black text-blue-600">{{ $stats['total'] }}</div>
                    <div class="text-sm text-gray-600 font-medium mt-1">إجمالي الإشعارات</div>
                </div>
                <div class="text-center bg-red-50 rounded-xl p-4 border-2 border-red-200">
                    <div class="text-3xl font-black text-red-600">{{ $stats['unread'] }}</div>
                    <div class="text-sm text-gray-600 font-medium mt-1">غير مقروءة</div>
                </div>
                <div class="text-center bg-green-50 rounded-xl p-4 border-2 border-green-200">
                    <div class="text-3xl font-black text-green-600">{{ $stats['today'] }}</div>
                    <div class="text-sm text-gray-600 font-medium mt-1">اليوم</div>
                </div>
                <div class="text-center bg-yellow-50 rounded-xl p-4 border-2 border-yellow-200">
                    <div class="text-3xl font-black text-yellow-600">{{ $stats['urgent'] }}</div>
                    <div class="text-sm text-gray-600 font-medium mt-1">عاجلة</div>
                </div>
            </div>
        </div>
    </div>

    <!-- الفلاتر -->
    <div class="bg-white shadow-lg rounded-xl border border-gray-200 p-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label for="type" class="block text-sm font-bold text-gray-700 mb-2">نوع الإشعار</label>
                <select name="type" id="type" class="w-full px-3 py-2 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">جميع الأنواع</option>
                    @foreach($notificationTypes as $key => $type)
                        <option value="{{ $key }}" {{ request('type') == $key ? 'selected' : '' }}>{{ $type }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="status" class="block text-sm font-bold text-gray-700 mb-2">الحالة</label>
                <select name="status" id="status" class="w-full px-3 py-2 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">جميع الحالات</option>
                    <option value="unread" {{ request('status') == 'unread' ? 'selected' : '' }}>غير مقروءة</option>
                    <option value="read" {{ request('status') == 'read' ? 'selected' : '' }}>مقروءة</option>
                </select>
            </div>

            <div>
                <label for="priority" class="block text-sm font-bold text-gray-700 mb-2">الأولوية</label>
                <select name="priority" id="priority" class="w-full px-3 py-2 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">جميع الأولويات</option>
                    @foreach($priorities as $key => $priority)
                        <option value="{{ $key }}" {{ request('priority') == $key ? 'selected' : '' }}>{{ $priority }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-end">
                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-xl font-bold transition-colors">
                    <i class="fas fa-filter ml-2"></i>
                    فلترة
                </button>
            </div>
        </form>
    </div>

    <!-- قائمة الإشعارات -->
    @if($notifications->count() > 0)
        <div class="space-y-4">
            @foreach($notifications as $notification)
            <div class="bg-white shadow-lg rounded-xl border-2 {{ $notification->is_read ? 'border-gray-200' : 'border-blue-300 bg-blue-50' }} overflow-hidden transition-all hover:shadow-xl">
                <div class="p-6">
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex-1">
                            <div class="flex items-center gap-3 mb-2">
                                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white">
                                    <i class="fas fa-{{ $notification->type === 'task' ? 'tasks' : ($notification->type === 'leave' ? 'calendar' : 'bell') }}"></i>
                                </div>
                                <div class="flex-1">
                                    <h4 class="text-lg font-black text-gray-900 mb-1">{{ $notification->title }}</h4>
                                    <p class="text-sm text-gray-600">{{ $notification->message }}</p>
                                </div>
                                @if(!$notification->is_read)
                                    <span class="bg-red-500 text-white text-xs font-bold px-2 py-1 rounded-full">جديد</span>
                                @endif
                            </div>
                            
                            <div class="flex items-center gap-4 mt-4 text-xs text-gray-500">
                                <span class="flex items-center gap-1">
                                    <i class="fas fa-clock"></i>
                                    {{ $notification->created_at->diffForHumans() }}
                                </span>
                                @if($notification->sender)
                                    <span class="flex items-center gap-1">
                                        <i class="fas fa-user"></i>
                                        {{ $notification->sender->name }}
                                    </span>
                                @endif
                                @if($notification->priority)
                                    <span class="px-2 py-1 rounded-full bg-{{ $notification->priority === 'urgent' ? 'red' : ($notification->priority === 'high' ? 'orange' : 'yellow') }}-100 text-{{ $notification->priority === 'urgent' ? 'red' : ($notification->priority === 'high' ? 'orange' : 'yellow') }}-800">
                                        {{ $notification->priority === 'urgent' ? 'عاجل' : ($notification->priority === 'high' ? 'عالي' : 'متوسط') }}
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="flex flex-col gap-2">
                            <a href="{{ route('employee.notifications.show', $notification) }}" 
                               class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm rounded-lg font-medium transition-colors">
                                <i class="fas fa-eye ml-2"></i>
                                عرض
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-6">
            {{ $notifications->links() }}
        </div>
    @else
        <div class="bg-white shadow-lg rounded-xl border border-gray-200 p-16 text-center">
            <div class="flex flex-col items-center gap-4">
                <div class="w-20 h-20 bg-blue-50 rounded-2xl flex items-center justify-center">
                    <i class="fas fa-bell text-4xl text-blue-500"></i>
                </div>
                <div>
                    <p class="font-black text-gray-900 text-xl mb-2">لا توجد إشعارات</p>
                    <p class="text-sm text-gray-600">سيتم إشعارك عند وجود تحديثات جديدة</p>
                </div>
            </div>
        </div>
    @endif
</div>

<script>
function markAllAsRead() {
    fetch('{{ route("employee.notifications.mark-all-read") }}', {
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
    });
}
</script>
@endsection
