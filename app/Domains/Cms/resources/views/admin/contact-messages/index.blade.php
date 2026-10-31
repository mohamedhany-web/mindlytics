@extends('layouts.admin')

@section('title', 'إدارة رسائل التواصل - Mindlytics')

@section('content')
<div class="p-6 bg-gray-50 min-h-screen">
    <!-- Header Section -->
    <div class="mb-8">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">
                    <i class="fas fa-envelope-open-text text-sky-600 ml-3"></i>
                    {{ __('إدارة رسائل التواصل') }}
                </h1>
                <p class="text-gray-600">{{ __('عرض وإدارة رسائل التواصل من الزوار') }}</p>
                @if($stats['unread'] > 0)
                <div class="mt-3 inline-flex items-center gap-2 px-4 py-2 bg-red-50 border border-red-200 rounded-lg">
                    <i class="fas fa-exclamation-circle text-red-600"></i>
                    <span class="text-sm font-semibold text-red-700">
                        لديك <span class="font-bold">{{ $stats['unread'] }}</span> رسالة غير مقروءة
                    </span>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 p-6 border-r-4 border-sky-500">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-gray-500 text-sm font-medium mb-1">{{ __('إجمالي الرسائل') }}</p>
                    <p class="text-3xl font-bold text-gray-900">{{ number_format($stats['total']) }}</p>
                    <p class="text-xs text-gray-400 mt-2">
                        <i class="fas fa-envelope text-sky-500 ml-1"></i>
                        جميع الرسائل
                    </p>
                </div>
                <div class="w-16 h-16 bg-gradient-to-br from-sky-500 to-sky-600 rounded-xl flex items-center justify-center shadow-lg">
                    <i class="fas fa-inbox text-white text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 p-6 border-r-4 border-red-500">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-gray-500 text-sm font-medium mb-1">{{ __('غير مقروءة') }}</p>
                    <p class="text-3xl font-bold text-gray-900">{{ number_format($stats['unread']) }}</p>
                    <p class="text-xs text-gray-400 mt-2">
                        <i class="fas fa-circle text-red-500 ml-1"></i>
                        تحتاج للمراجعة
                    </p>
                </div>
                <div class="w-16 h-16 bg-gradient-to-br from-red-500 to-red-600 rounded-xl flex items-center justify-center shadow-lg">
                    <i class="fas fa-envelope text-white text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 p-6 border-r-4 border-emerald-500">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-gray-500 text-sm font-medium mb-1">{{ __('مقروءة') }}</p>
                    <p class="text-3xl font-bold text-gray-900">{{ number_format($stats['read']) }}</p>
                    <p class="text-xs text-gray-400 mt-2">
                        <i class="fas fa-check-circle text-emerald-500 ml-1"></i>
                        تم المراجعة
                    </p>
                </div>
                <div class="w-16 h-16 bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-xl flex items-center justify-center shadow-lg">
                    <i class="fas fa-check-double text-white text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 p-6 border-r-4 border-amber-500">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-gray-500 text-sm font-medium mb-1">{{ __('رسائل اليوم') }}</p>
                    <p class="text-3xl font-bold text-gray-900">{{ number_format($stats['today']) }}</p>
                    <p class="text-xs text-gray-400 mt-2">
                        <i class="fas fa-calendar-day text-amber-500 ml-1"></i>
                        تم الاستلام اليوم
                    </p>
                </div>
                <div class="w-16 h-16 bg-gradient-to-br from-amber-500 to-amber-600 rounded-xl flex items-center justify-center shadow-lg">
                    <i class="fas fa-calendar text-white text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Search and Filters -->
    <div class="bg-white rounded-2xl shadow-lg p-6 mb-6 border border-gray-200">
        <div class="flex items-center mb-4 pb-4 border-b border-gray-200">
            <i class="fas fa-filter text-sky-600 ml-3"></i>
            <h3 class="text-lg font-semibold text-gray-900">{{ __('فلترة وبحث الرسائل') }}</h3>
        </div>
        <form method="GET" action="{{ route('admin.contact-messages.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-search text-gray-400 ml-1"></i>
                    {{ __('البحث') }}
                </label>
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" 
                           placeholder="{{ __('الاسم، البريد الإلكتروني، أو الموضوع...') }}"
                           class="w-full px-4 py-2.5 pr-10 border border-gray-300 rounded-lg bg-white text-gray-900 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 transition-all">
                    <i class="fas fa-search absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-info-circle text-gray-400 ml-1"></i>
                    {{ __('الحالة') }}
                </label>
                <select name="status" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg bg-white text-gray-900 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 transition-all">
                    <option value="">{{ __('جميع الرسائل') }}</option>
                    <option value="unread" {{ request('status') == 'unread' ? 'selected' : '' }}>{{ __('غير مقروءة') }}</option>
                    <option value="read" {{ request('status') == 'read' ? 'selected' : '' }}>{{ __('مقروءة') }}</option>
                </select>
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" 
                        class="flex-1 bg-gradient-to-l from-sky-600 to-sky-500 hover:from-sky-700 hover:to-sky-600 text-white px-4 py-2.5 rounded-lg font-medium transition-all duration-200 shadow-md hover:shadow-lg flex items-center justify-center gap-2">
                    <i class="fas fa-search"></i>
                    <span>{{ __('بحث') }}</span>
                </button>
                @if(request()->anyFilled(['search', 'status']))
                <a href="{{ route('admin.contact-messages.index') }}" 
                   class="px-4 py-2.5 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg font-medium transition-colors"
                   title="{{ __('مسح الفلتر') }}">
                    <i class="fas fa-times"></i>
                </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Messages List -->
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-200">
        <div class="p-6 border-b border-gray-200 bg-gradient-to-r from-sky-50 to-blue-50">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-xl font-bold text-gray-900 flex items-center gap-2">
                        <i class="fas fa-list text-sky-600"></i>
                        {{ __('سجل الرسائل') }}
                    </h3>
                    <p class="text-sm text-gray-500 mt-1">
                        <span class="font-semibold text-sky-600">{{ $messages->total() }}</span> رسالة
                    </p>
                </div>
                <div class="flex items-center gap-2 text-sm text-gray-500">
                    <i class="fas fa-clock"></i>
                    <span>آخر تحديث: {{ now()->format('H:i') }}</span>
                </div>
            </div>
        </div>

        @if($messages->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                <i class="fas fa-user ml-2 text-sky-500"></i>
                                {{ __('المرسل') }}
                            </th>
                            <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                <i class="fas fa-comment ml-2 text-sky-500"></i>
                                {{ __('الموضوع') }}
                            </th>
                            <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                <i class="fas fa-info-circle ml-2 text-sky-500"></i>
                                {{ __('الحالة') }}
                            </th>
                            <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                <i class="fas fa-clock ml-2 text-sky-500"></i>
                                {{ __('تاريخ الإرسال') }}
                            </th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                <i class="fas fa-cog ml-2 text-sky-500"></i>
                                {{ __('الإجراءات') }}
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($messages as $message)
                            <tr class="hover:bg-gradient-to-r hover:from-sky-50 hover:to-blue-50 transition-all duration-200 {{ !$message->read_at ? 'bg-sky-50/50' : '' }}">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="w-12 h-12 bg-gradient-to-br from-sky-500 to-blue-600 rounded-xl flex items-center justify-center shadow-md">
                                            <span class="text-white font-bold text-lg">
                                                {{ mb_substr($message->name, 0, 1, 'UTF-8') }}
                                            </span>
                                        </div>
                                        <div class="mr-4">
                                            <div class="text-sm font-semibold text-gray-900">
                                                {{ $message->name }}
                                            </div>
                                            <div class="text-xs text-gray-500 flex items-center gap-1 mt-1">
                                                <i class="fas fa-envelope text-gray-400 text-xs"></i>
                                                <span>{{ $message->email }}</span>
                                            </div>
                                            @if($message->phone)
                                            <div class="text-xs text-gray-500 flex items-center gap-1 mt-1">
                                                <i class="fas fa-phone text-gray-400 text-xs"></i>
                                                <span>{{ $message->phone }}</span>
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-semibold text-gray-900 mb-1">
                                        {{ $message->subject }}
                                    </div>
                                    <div class="text-sm text-gray-600 max-w-md">
                                        <div class="line-clamp-2">
                                            {{ Str::limit($message->message, 100) }}
                                        </div>
                                        @if(strlen($message->message) > 100)
                                        <a href="{{ route('admin.contact-messages.show', $message) }}" class="text-xs text-sky-600 hover:text-sky-800 mt-1 inline-block">
                                            قراءة المزيد...
                                        </a>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($message->read_at)
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold shadow-sm bg-emerald-100 text-emerald-800">
                                        <i class="fas fa-check-circle"></i>
                                        <span>مقروءة</span>
                                    </span>
                                    @else
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold shadow-sm bg-red-100 text-red-800">
                                        <i class="fas fa-circle text-xs"></i>
                                        <span>غير مقروءة</span>
                                    </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900 font-medium">
                                        {{ $message->created_at->format('d/m/Y') }}
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        {{ $message->created_at->format('H:i') }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('admin.contact-messages.show', $message) }}" 
                                           class="w-9 h-9 flex items-center justify-center bg-blue-50 hover:bg-blue-100 text-blue-600 rounded-lg transition-colors"
                                           title="{{ __('عرض التفاصيل') }}">
                                            <i class="fas fa-eye text-sm"></i>
                                        </a>
                                        @if($message->read_at)
                                        <form action="{{ route('admin.contact-messages.mark-as-unread', $message) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" 
                                                    class="w-9 h-9 flex items-center justify-center bg-amber-50 hover:bg-amber-100 text-amber-600 rounded-lg transition-colors"
                                                    title="{{ __('تحديد كغير مقروءة') }}">
                                                <i class="fas fa-envelope text-sm"></i>
                                            </button>
                                        </form>
                                        @else
                                        <form action="{{ route('admin.contact-messages.mark-as-read', $message) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" 
                                                    class="w-9 h-9 flex items-center justify-center bg-emerald-50 hover:bg-emerald-100 text-emerald-600 rounded-lg transition-colors"
                                                    title="{{ __('تحديد كمقروءة') }}">
                                                <i class="fas fa-check text-sm"></i>
                                            </button>
                                        </form>
                                        @endif
                                        <form action="{{ route('admin.contact-messages.destroy', $message) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="w-9 h-9 flex items-center justify-center bg-red-50 hover:bg-red-100 text-red-600 rounded-lg transition-colors"
                                                    title="{{ __('حذف') }}"
                                                    onclick="return confirm('هل أنت متأكد من حذف هذه الرسالة؟')">
                                                <i class="fas fa-trash text-sm"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                {{ $messages->withQueryString()->links() }}
            </div>
        @else
            <div class="p-12 text-center">
                <div class="w-24 h-24 mx-auto mb-6 bg-gradient-to-br from-gray-100 to-gray-200 rounded-full flex items-center justify-center">
                    <i class="fas fa-inbox text-gray-400 text-5xl"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">
                    {{ __('لا توجد رسائل') }}
                </h3>
                <p class="text-gray-600 mb-6 max-w-md mx-auto">
                    {{ __('لم يتم استلام أي رسائل تواصل بعد') }}
                </p>
            </div>
        @endif
    </div>
</div>

@push('styles')
<style>
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
</style>
@endpush
@endsection



