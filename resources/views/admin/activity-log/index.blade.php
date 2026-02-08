@extends('layouts.admin')

@section('title', 'سجل النشاطات - Mindlytics')
@section('header', 'سجل النشاطات')

@section('content')
@php
    $statCards = [
        [
            'label' => 'إجمالي النشاطات',
            'value' => number_format($stats['total'] ?? 0),
            'icon' => 'fas fa-history',
            'color' => 'text-sky-500 bg-sky-100/70',
            'description' => 'جميع النشاطات المسجلة',
        ],
        [
            'label' => 'نشاطات اليوم',
            'value' => number_format($stats['today'] ?? 0),
            'icon' => 'fas fa-calendar-day',
            'color' => 'text-emerald-500 bg-emerald-100/70',
            'description' => 'تم تسجيلها اليوم',
        ],
        [
            'label' => 'هذا الأسبوع',
            'value' => number_format($stats['this_week'] ?? 0),
            'icon' => 'fas fa-calendar-week',
            'color' => 'text-purple-500 bg-purple-100/70',
            'description' => 'خلال الأسبوع الحالي',
        ],
        [
            'label' => 'هذا الشهر',
            'value' => number_format($stats['this_month'] ?? 0),
            'icon' => 'fas fa-calendar-alt',
            'color' => 'text-amber-500 bg-amber-100/70',
            'description' => 'خلال الشهر الحالي',
        ],
    ];
@endphp

<div class="space-y-6 sm:space-y-10">
    <!-- Header Section -->
    <section class="rounded-3xl bg-white/95 backdrop-blur border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-5 py-6 sm:px-8 lg:px-12 border-b border-slate-200 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-2xl font-bold text-slate-900">سجل النشاطات</h2>
                <p class="text-sm text-slate-500 mt-2">راقب كل العمليات التي تمت داخل المنصة</p>
            </div>
            <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                <button @click="open = !open" 
                        class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-white bg-rose-600 rounded-xl shadow hover:bg-rose-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-rose-500 transition-all">
                    <i class="fas fa-trash"></i>
                    <span>مسح السجلات</span>
                    <i class="fas fa-chevron-down text-xs transition-transform duration-200" :class="open ? 'rotate-180' : ''"></i>
                </button>
                <div x-show="open" 
                     x-transition:enter="transition ease-out duration-100"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-75"
                     x-transition:leave-start="opacity-100 scale-100"
                     x-transition:leave-end="opacity-0 scale-95"
                     x-cloak
                     class="absolute left-0 mt-2 w-64 bg-white rounded-2xl shadow-2xl border border-slate-200 z-50 overflow-hidden"
                     style="display: none;">
                    <div class="p-2">
                        <button onclick="clearActivityLog('filtered')" 
                                class="w-full text-right px-4 py-3 text-sm text-slate-700 hover:bg-rose-50 rounded-xl transition-colors flex items-center justify-between">
                            <span class="flex items-center gap-2">
                                <i class="fas fa-filter text-slate-400"></i>
                                مسح المطابقة للفلتر
                            </span>
                            <i class="fas fa-chevron-left text-xs text-slate-400"></i>
                        </button>
                        <button onclick="clearActivityLog('old')" 
                                class="w-full text-right px-4 py-3 text-sm text-slate-700 hover:bg-rose-50 rounded-xl transition-colors flex items-center justify-between">
                            <span class="flex items-center gap-2">
                                <i class="fas fa-calendar text-slate-400"></i>
                                مسح أقدم من 3 أشهر
                            </span>
                            <i class="fas fa-chevron-left text-xs text-slate-400"></i>
                        </button>
                        <button onclick="clearActivityLog('older')" 
                                class="w-full text-right px-4 py-3 text-sm text-slate-700 hover:bg-rose-50 rounded-xl transition-colors flex items-center justify-between">
                            <span class="flex items-center gap-2">
                                <i class="fas fa-calendar-alt text-slate-400"></i>
                                مسح أقدم من 6 أشهر
                            </span>
                            <i class="fas fa-chevron-left text-xs text-slate-400"></i>
                        </button>
                        <hr class="my-2 border-slate-200">
                        <button onclick="clearActivityLog('all')" 
                                class="w-full text-right px-4 py-3 text-sm text-rose-600 hover:bg-rose-50 rounded-xl transition-colors flex items-center justify-between font-semibold">
                            <span class="flex items-center gap-2">
                                <i class="fas fa-exclamation-triangle text-rose-500"></i>
                                مسح جميع السجلات
                            </span>
                            <i class="fas fa-chevron-left text-xs text-rose-400"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Statistics Cards -->
        @if(isset($stats))
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
        @endif
    </section>

    <!-- Search and Filters -->
    <section class="rounded-3xl bg-white/95 backdrop-blur border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-5 py-6 sm:px-8 lg:px-12 border-b border-slate-200">
            <h3 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                <i class="fas fa-filter text-sky-600"></i>
                فلترة وبحث النشاطات
            </h3>
        </div>
        <div class="px-5 py-6 sm:px-8 lg:px-12">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-5 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-2">البحث</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-3 flex items-center text-slate-400">
                            <i class="fas fa-search"></i>
                        </span>
                        <input type="text" name="search" value="{{ request('search') }}" 
                               placeholder="البحث في النشاطات..."
                               class="w-full rounded-2xl border border-slate-200 bg-white/80 px-4 py-2.5 pr-10 text-sm text-slate-900 focus:ring-2 focus:ring-sky-500 focus:border-sky-400 transition-all">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-2">نوع النشاط</label>
                    <select name="type" class="w-full rounded-2xl border border-slate-200 bg-white/80 px-4 py-2.5 text-sm text-slate-900 focus:ring-2 focus:ring-sky-500 focus:border-sky-400 transition-all">
                        <option value="">جميع الأنواع</option>
                        <option value="create" {{ request('type') == 'create' ? 'selected' : '' }}>إنشاء</option>
                        <option value="update" {{ request('type') == 'update' ? 'selected' : '' }}>تحديث</option>
                        <option value="delete" {{ request('type') == 'delete' ? 'selected' : '' }}>حذف</option>
                        <option value="login" {{ request('type') == 'login' ? 'selected' : '' }}>تسجيل دخول</option>
                        <option value="logout" {{ request('type') == 'logout' ? 'selected' : '' }}>تسجيل خروج</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-2">من تاريخ</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}" 
                           class="w-full rounded-2xl border border-slate-200 bg-white/80 px-4 py-2.5 text-sm text-slate-900 focus:ring-2 focus:ring-sky-500 focus:border-sky-400 transition-all" />
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-2">إلى تاريخ</label>
                    <input type="date" name="date_to" value="{{ request('date_to') }}" 
                           class="w-full rounded-2xl border border-slate-200 bg-white/80 px-4 py-2.5 text-sm text-slate-900 focus:ring-2 focus:ring-sky-500 focus:border-sky-400 transition-all" />
                </div>
                <div class="flex items-end gap-2">
                    <button type="submit" 
                            class="flex-1 inline-flex items-center justify-center gap-2 rounded-2xl bg-sky-600 px-4 py-2.5 text-sm font-semibold text-white shadow hover:bg-sky-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-500 transition-all">
                        <i class="fas fa-search"></i>
                        <span>بحث</span>
                    </button>
                    @if(request()->anyFilled(['search', 'type', 'date_from', 'date_to']))
                    <a href="{{ route('admin.activity-log') }}" 
                       class="px-4 py-2.5 bg-slate-200 hover:bg-slate-300 text-slate-700 rounded-2xl font-semibold transition-colors"
                       title="مسح الفلتر">
                        <i class="fas fa-times"></i>
                    </a>
                    @endif
                </div>
            </form>
        </div>
    </section>

    <!-- Activities List -->
    <section class="rounded-3xl bg-white/95 backdrop-blur border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-5 py-6 sm:px-8 lg:px-12 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h3 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                    <i class="fas fa-list text-sky-600"></i>
                    العمليات المسجلة
                </h3>
                <p class="text-sm text-slate-500 mt-1">
                    <span class="font-semibold text-sky-600">{{ $activities->total() }}</span> عملية تم تسجيلها
                </p>
            </div>
            <div class="flex items-center gap-2 text-sm text-slate-500">
                <i class="fas fa-clock"></i>
                <span>آخر تحديث: {{ now()->format('H:i') }}</span>
            </div>
        </div>

        @if ($activities->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr class="text-xs font-semibold uppercase tracking-widest text-slate-500">
                            <th class="px-6 py-4 text-right">
                                <i class="fas fa-user ml-2 text-sky-500"></i>
                                المستخدم
                            </th>
                            <th class="px-6 py-4 text-right">
                                <i class="fas fa-tag ml-2 text-sky-500"></i>
                                النوع
                            </th>
                            <th class="px-6 py-4 text-right">
                                <i class="fas fa-comment ml-2 text-sky-500"></i>
                                الوصف
                            </th>
                            <th class="px-6 py-4 text-right">
                                <i class="fas fa-clock ml-2 text-sky-500"></i>
                                الوقت
                            </th>
                            <th class="px-6 py-4 text-center">
                                <i class="fas fa-cog ml-2 text-sky-500"></i>
                                الإجراءات
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 bg-white/80 text-sm text-slate-700">
                        @foreach ($activities as $activity)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 bg-gradient-to-br from-sky-500 to-blue-600 rounded-xl flex items-center justify-center text-white font-bold text-sm shadow-md">
                                            {{ mb_substr($activity->user->name ?? 'غ', 0, 1, 'UTF-8') }}
                                        </div>
                                        <div class="space-y-1">
                                            <p class="font-semibold text-slate-900">{{ $activity->user->name ?? 'مستخدم غير معروف' }}</p>
                                            <p class="text-xs text-slate-500">{{ $activity->user->email ?? '—' }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    @php
                                        $actionType = $activity->action;
                                        $isCreate = str_contains($actionType, 'create') || str_contains($actionType, 'created');
                                        $isUpdate = str_contains($actionType, 'update') || str_contains($actionType, 'updated') || str_contains($actionType, 'changed');
                                        $isDelete = str_contains($actionType, 'delete') || str_contains($actionType, 'deleted');
                                        $isLogin = str_contains($actionType, 'login') && !str_contains($actionType, 'logout');
                                        $isLogout = str_contains($actionType, 'logout');
                                        
                                        if ($isCreate) {
                                            $badgeClasses = 'bg-emerald-100 text-emerald-700';
                                            $typeLabel = 'إنشاء';
                                        } elseif ($isUpdate) {
                                            $badgeClasses = 'bg-sky-100 text-sky-700';
                                            $typeLabel = 'تحديث';
                                        } elseif ($isDelete) {
                                            $badgeClasses = 'bg-rose-100 text-rose-700';
                                            $typeLabel = 'حذف';
                                        } elseif ($isLogin) {
                                            $badgeClasses = 'bg-purple-100 text-purple-700';
                                            $typeLabel = 'تسجيل دخول';
                                        } elseif ($isLogout) {
                                            $badgeClasses = 'bg-slate-100 text-slate-700';
                                            $typeLabel = 'تسجيل خروج';
                                        } else {
                                            $badgeClasses = 'bg-amber-100 text-amber-700';
                                            $typeLabel = 'نشاط آخر';
                                        }
                                    @endphp
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold {{ $badgeClasses }}">
                                        <span class="h-2 w-2 rounded-full bg-current"></span>
                                        {{ $typeLabel }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="font-medium text-slate-900">
                                        {{ $activity->description ?: ($activity->action_description ?? $activity->action) }}
                                    </p>
                                    @if ($activity->model_type && $activity->model_id)
                                        <p class="text-xs text-slate-500 mt-1">
                                            <i class="fas fa-link text-xs ml-1"></i>
                                            {{ class_basename($activity->model_type) }} #{{ $activity->model_id }}
                                        </p>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-xs text-slate-500">
                                    <p class="font-medium">{{ $activity->created_at->format('Y-m-d') }}</p>
                                    <p>{{ $activity->created_at->format('H:i:s') }}</p>
                                </td>
                                <td class="px-6 py-4">
                                    <a href="{{ route('admin.activity-log.show', $activity) }}" 
                                       class="inline-flex items-center gap-2 rounded-xl border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-600 transition hover:border-sky-300 hover:text-sky-600">
                                        <i class="fas fa-eye"></i>
                                        عرض التفاصيل
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if ($activities->hasPages())
                <div class="px-6 py-4 bg-slate-50 border-t border-slate-200">
                    {{ $activities->appends(request()->query())->links() }}
                </div>
            @endif
        @else
            <div class="p-12 text-center">
                <div class="w-24 h-24 mx-auto mb-6 bg-gradient-to-br from-slate-100 to-slate-200 rounded-full flex items-center justify-center">
                    <i class="fas fa-history text-slate-400 text-5xl"></i>
                </div>
                <h3 class="text-xl font-bold text-slate-900 mb-2">
                    لا توجد نشاطات
                </h3>
                <p class="text-slate-600 mb-6 max-w-md mx-auto">
                    لا توجد نشاطات مطابقة للمعايير الحالية
                </p>
            </div>
        @endif
    </section>
</div>

@push('scripts')
<script>
function clearActivityLog(type) {
    let confirmMessage = '';
    
    switch(type) {
        case 'all':
            confirmMessage = 'هل أنت متأكد من مسح جميع السجلات؟\n\n⚠️ تحذير: هذا الإجراء لا يمكن التراجع عنه!';
            break;
        case 'old':
            confirmMessage = 'هل أنت متأكد من مسح السجلات الأقدم من 3 أشهر؟';
            break;
        case 'older':
            confirmMessage = 'هل أنت متأكد من مسح السجلات الأقدم من 6 أشهر؟';
            break;
        case 'filtered':
            confirmMessage = 'هل أنت متأكد من مسح السجلات المطابقة للفلتر الحالي؟';
            break;
    }
    
    if (confirm(confirmMessage)) {
        const loadingToast = document.createElement('div');
        loadingToast.className = 'fixed top-4 left-1/2 transform -translate-x-1/2 bg-blue-600 text-white px-6 py-3 rounded-xl shadow-lg z-50 flex items-center gap-2';
        loadingToast.innerHTML = '<i class="fas fa-spinner fa-spin"></i> جاري مسح السجلات...';
        document.body.appendChild(loadingToast);
        
        const formData = new FormData();
        formData.append('delete_type', type);
        formData.append('_token', '{{ csrf_token() }}');
        
        if (type === 'filtered') {
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.has('type')) formData.append('type', urlParams.get('type'));
            if (urlParams.has('user_id')) formData.append('user_id', urlParams.get('user_id'));
            if (urlParams.has('date_from')) formData.append('date_from', urlParams.get('date_from'));
            if (urlParams.has('date_to')) formData.append('date_to', urlParams.get('date_to'));
            if (urlParams.has('search')) formData.append('search', urlParams.get('search'));
        }
        
        fetch('{{ route('admin.activity-log.destroy') }}', {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            document.body.removeChild(loadingToast);
            
            if (data.success) {
                const successToast = document.createElement('div');
                successToast.className = 'fixed top-4 left-1/2 transform -translate-x-1/2 bg-emerald-600 text-white px-6 py-3 rounded-xl shadow-lg z-50 flex items-center gap-2';
                successToast.innerHTML = '<i class="fas fa-check-circle"></i> ' + data.message;
                document.body.appendChild(successToast);
                
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
            } else {
                const errorToast = document.createElement('div');
                errorToast.className = 'fixed top-4 left-1/2 transform -translate-x-1/2 bg-rose-600 text-white px-6 py-3 rounded-xl shadow-lg z-50 flex items-center gap-2';
                errorToast.innerHTML = '<i class="fas fa-exclamation-circle"></i> ' + data.message;
                document.body.appendChild(errorToast);
                
                setTimeout(() => {
                    if (document.body.contains(errorToast)) {
                        document.body.removeChild(errorToast);
                    }
                }, 5000);
            }
        })
        .catch(error => {
            document.body.removeChild(loadingToast);
            
            const errorToast = document.createElement('div');
            errorToast.className = 'fixed top-4 left-1/2 transform -translate-x-1/2 bg-rose-600 text-white px-6 py-3 rounded-xl shadow-lg z-50 flex items-center gap-2';
            errorToast.innerHTML = '<i class="fas fa-exclamation-circle"></i> حدث خطأ: ' + error.message;
            document.body.appendChild(errorToast);
            
            setTimeout(() => {
                if (document.body.contains(errorToast)) {
                    document.body.removeChild(errorToast);
                }
            }, 5000);
        });
    }
}
</script>
@endpush
@endsection
