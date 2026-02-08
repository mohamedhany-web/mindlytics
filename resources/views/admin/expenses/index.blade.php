@extends('layouts.admin')

@section('title', 'إدارة المصروفات')
@section('header', 'إدارة المصروفات')

@section('content')
<div class="space-y-6">
    <!-- الهيدر -->
    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-6 py-5 bg-slate-50 border-b border-slate-200 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white shadow-md">
                    <i class="fas fa-receipt text-lg"></i>
                </div>
                <div>
                    <h2 class="text-2xl font-black text-slate-900">لوحة إدارة المصروفات</h2>
                    <p class="text-sm text-slate-600 mt-1">إدارة وتتبع جميع المصروفات والمدفوعات.</p>
                </div>
            </div>
            <a href="{{ route('admin.expenses.create') }}" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-white bg-gradient-to-r from-blue-600 to-blue-500 rounded-xl shadow hover:from-blue-700 hover:to-blue-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200">
                <i class="fas fa-plus"></i>
                مصروف جديد
            </a>
        </div>
    </section>

    <!-- Statistics Cards -->
    @if(isset($stats))
    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 p-6">
            <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm hover:shadow-md transition-shadow duration-200">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex-1">
                        <p class="text-xs font-semibold text-slate-600 mb-1">{{ htmlspecialchars('إجمالي المصروفات') }}</p>
                        <p class="text-2xl font-black text-slate-900">{{ number_format($stats['total']) }}</p>
                    </div>
                    <div class="w-12 h-12 rounded-lg bg-blue-100 text-blue-600 shadow-sm flex items-center justify-center">
                        <i class="fas fa-receipt text-lg"></i>
                    </div>
                </div>
            </div>
            <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm hover:shadow-md transition-shadow duration-200">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex-1">
                        <p class="text-xs font-semibold text-slate-600 mb-1">{{ htmlspecialchars('قيد المراجعة') }}</p>
                        <p class="text-2xl font-black text-amber-600">{{ number_format($stats['pending']) }}</p>
                    </div>
                    <div class="w-12 h-12 rounded-lg bg-amber-100 text-amber-600 shadow-sm flex items-center justify-center">
                        <i class="fas fa-hourglass-half text-lg"></i>
                    </div>
                </div>
            </div>
            <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm hover:shadow-md transition-shadow duration-200">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex-1">
                        <p class="text-xs font-semibold text-slate-600 mb-1">{{ htmlspecialchars('موافق عليها') }}</p>
                        <p class="text-2xl font-black text-emerald-600">{{ number_format($stats['approved']) }}</p>
                    </div>
                    <div class="w-12 h-12 rounded-lg bg-emerald-100 text-emerald-600 shadow-sm flex items-center justify-center">
                        <i class="fas fa-check-circle text-lg"></i>
                    </div>
                </div>
            </div>
            <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm hover:shadow-md transition-shadow duration-200">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex-1">
                        <p class="text-xs font-semibold text-slate-600 mb-1">{{ htmlspecialchars('إجمالي المبلغ') }}</p>
                        <p class="text-2xl font-black text-slate-900">{{ number_format($stats['total_amount'], 2) }} ج.م</p>
                    </div>
                    <div class="w-12 h-12 rounded-lg bg-purple-100 text-purple-600 shadow-sm flex items-center justify-center">
                        <i class="fas fa-money-bill-wave text-lg"></i>
                    </div>
                </div>
            </div>
        </div>
    </section>
    @endif

    <!-- Filter Section -->
    <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-200 mb-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('البحث') }}</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('رقم المصروف، العنوان، المرجع...') }}" 
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('الحالة') }}</label>
                <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                    <option value="">{{ __('جميع الحالات') }}</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>{{ __('قيد المراجعة') }}</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>{{ __('موافق عليها') }}</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>{{ __('مرفوضة') }}</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('الفئة') }}</label>
                <select name="category" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                    <option value="">{{ __('جميع الفئات') }}</option>
                    @foreach(['operational' => 'تشغيلي', 'marketing' => 'تسويق', 'salaries' => 'رواتب', 'utilities' => 'مرافق', 'equipment' => 'معدات', 'maintenance' => 'صيانة', 'other' => 'أخرى'] as $key => $label)
                        <option value="{{ $key }}" {{ request('category') == $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="bg-sky-600 hover:bg-sky-700 text-white px-6 py-2 rounded-lg font-medium transition-colors flex-1">
                    <i class="fas fa-search ml-2"></i>
                    {{ __('بحث') }}
                </button>
                <a href="{{ route('admin.expenses.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg transition-colors">
                    <i class="fas fa-redo"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Expenses Table -->
    @if(isset($expenses) && $expenses->count() > 0)
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-200">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">{{ __('رقم المصروف') }}</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">{{ __('العنوان') }}</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">{{ __('الفئة') }}</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">{{ __('المبلغ') }}</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">{{ __('التاريخ') }}</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">{{ __('الحالة') }}</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">{{ __('الإجراءات') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($expenses as $expense)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $expense->expense_number }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $expense->title }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $expense->category_label }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ number_format($expense->amount, 2) }} ج.م</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $expense->expense_date->format('Y-m-d') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                @if($expense->status == 'approved') bg-green-100 text-green-800
                                @elseif($expense->status == 'pending') bg-yellow-100 text-yellow-800
                                @else bg-red-100 text-red-800
                                @endif">
                                {{ $expense->status_text }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('admin.expenses.show', $expense) }}" class="text-sky-600 hover:text-sky-900">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @if($expense->status == 'pending')
                                <form action="{{ route('admin.expenses.approve', $expense) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="text-green-600 hover:text-green-900" onclick="return confirm('هل أنت متأكد من الموافقة على هذا المصروف؟')">
                                        <i class="fas fa-check"></i>
                                    </button>
                                </form>
                                <form action="{{ route('admin.expenses.reject', $expense) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('هل أنت متأكد من رفض هذا المصروف؟')">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $expenses->links() }}
        </div>
    </div>
    @else
    <div class="bg-white rounded-2xl shadow-lg p-12 text-center border border-gray-200">
        <i class="fas fa-receipt text-gray-400 text-6xl mb-4"></i>
        <p class="text-gray-600 text-lg">{{ __('لا توجد مصروفات') }}</p>
    </div>
    @endif
</div>
@endsection

