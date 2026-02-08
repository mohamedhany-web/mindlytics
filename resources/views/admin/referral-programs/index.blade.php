@extends('layouts.admin')

@section('title', 'برامج الإحالات - Mindlytics')

@section('content')
<div class="p-6 bg-gray-50 min-h-screen">
    <!-- Header Section -->
    <div class="mb-8">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">
                    <i class="fas fa-gift text-sky-600 ml-3"></i>
                    برامج الإحالات
                </h1>
                <p class="text-gray-600">إدارة برامج الإحالات والخصومات</p>
            </div>
            <a href="{{ route('admin.referral-programs.create') }}" 
               class="bg-gradient-to-l from-sky-600 to-sky-500 hover:from-sky-700 hover:to-sky-600 text-white px-5 py-2.5 rounded-lg font-medium transition-all duration-200 shadow-md hover:shadow-lg flex items-center gap-2">
                <i class="fas fa-plus"></i>
                <span>برنامج جديد</span>
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 p-6 border-r-4 border-sky-500">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-gray-500 text-sm font-medium mb-1">إجمالي البرامج</p>
                    <p class="text-3xl font-bold text-gray-900">{{ number_format($stats['total']) }}</p>
                </div>
                <div class="w-16 h-16 bg-gradient-to-br from-sky-500 to-sky-600 rounded-xl flex items-center justify-center shadow-lg">
                    <i class="fas fa-list text-white text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 p-6 border-r-4 border-emerald-500">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-gray-500 text-sm font-medium mb-1">البرامج النشطة</p>
                    <p class="text-3xl font-bold text-gray-900">{{ number_format($stats['active']) }}</p>
                </div>
                <div class="w-16 h-16 bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-xl flex items-center justify-center shadow-lg">
                    <i class="fas fa-check-circle text-white text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 p-6 border-r-4 border-amber-500">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-gray-500 text-sm font-medium mb-1">البرامج المعطلة</p>
                    <p class="text-3xl font-bold text-gray-900">{{ number_format($stats['inactive']) }}</p>
                </div>
                <div class="w-16 h-16 bg-gradient-to-br from-amber-500 to-amber-600 rounded-xl flex items-center justify-center shadow-lg">
                    <i class="fas fa-times-circle text-white text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Programs List -->
    @if($programs->count() > 0)
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-200">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">اسم البرنامج</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">خصم المحال</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">مكافأة المحيل</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">مدة الصلاحية</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الحالة</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($programs as $program)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div>
                                <div class="text-sm font-medium text-gray-900">{{ $program->name }}</div>
                                @if($program->description)
                                <div class="text-sm text-gray-500">{{ Str::limit($program->description, 50) }}</div>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm font-medium text-gray-900">
                                @if($program->discount_type == 'percentage')
                                    {{ number_format($program->discount_value, 0) }}%
                                @else
                                    {{ number_format($program->discount_value, 2) }} ج.م
                                @endif
                            </span>
                            @if($program->maximum_discount)
                            <div class="text-xs text-gray-500">حد أقصى: {{ number_format($program->maximum_discount, 2) }} ج.م</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($program->referrer_reward_value)
                            <span class="text-sm font-medium text-gray-900">
                                @if($program->referrer_reward_type == 'percentage')
                                    {{ number_format($program->referrer_reward_value, 0) }}%
                                @elseif($program->referrer_reward_type == 'points')
                                    {{ number_format($program->referrer_reward_value, 0) }} نقطة
                                @else
                                    {{ number_format($program->referrer_reward_value, 2) }} ج.م
                                @endif
                            </span>
                            @else
                            <span class="text-xs text-gray-400">لا توجد مكافأة</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $program->discount_valid_days }} يوم
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                @if($program->is_active && $program->isValid()) bg-emerald-100 text-emerald-800
                                @else bg-gray-100 text-gray-800
                                @endif">
                                @if($program->is_active && $program->isValid()) نشط
                                @else معطل
                                @endif
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('admin.referral-programs.show', $program) }}" 
                                   class="text-sky-600 hover:text-sky-900">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.referral-programs.edit', $program) }}" 
                                   class="text-amber-600 hover:text-amber-900">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.referral-programs.destroy', $program) }}" 
                                      method="POST" 
                                      onsubmit="return confirm('هل أنت متأكد من حذف هذا البرنامج؟');"
                                      class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $programs->links() }}
        </div>
    </div>
    @else
    <div class="bg-white rounded-2xl shadow-lg p-12 text-center border border-gray-200">
        <i class="fas fa-gift text-gray-400 text-6xl mb-4"></i>
        <p class="text-gray-600 text-lg mb-2">لا توجد برامج إحالات</p>
        <p class="text-gray-400 text-sm mb-6">ابدأ بإنشاء برنامج إحالات جديد</p>
        <a href="{{ route('admin.referral-programs.create') }}" 
           class="inline-flex items-center gap-2 bg-gradient-to-r from-sky-600 to-sky-700 hover:from-sky-700 hover:to-sky-800 text-white px-6 py-3 rounded-lg font-medium transition-colors shadow-lg shadow-sky-500/30">
            <i class="fas fa-plus"></i>
            إنشاء برنامج جديد
        </a>
    </div>
    @endif
</div>
@endsection
