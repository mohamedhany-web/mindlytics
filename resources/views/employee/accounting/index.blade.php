@extends('layouts.employee')

@section('title', 'الراتب والمحاسبة')
@section('header', 'الراتب والمحاسبة')

@section('content')
<div class="space-y-6">
    @if(!$activeAgreement)
        <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-6 text-center">
            <i class="fas fa-exclamation-triangle text-yellow-600 text-4xl mb-4"></i>
            <h3 class="text-lg font-semibold text-yellow-900 mb-2">لا توجد اتفاقية نشطة</h3>
            <p class="text-yellow-700">لم يتم تعيين اتفاقية عمل نشطة لك حتى الآن</p>
        </div>
    @else
        <!-- معلومات الراتب الأساسية -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="dashboard-card rounded-2xl p-6 card-hover-effect border-2 border-green-200/50 hover:border-green-300/70 shadow-xl hover:shadow-2xl transition-all duration-300" style="background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(240, 253, 250, 0.95) 50%, rgba(209, 250, 229, 0.9) 100%);">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-semibold text-gray-600 mb-1">الراتب الأساسي</p>
                        <p class="text-3xl font-black text-green-700">{{ number_format($stats['base_salary'], 2) }} ج.م</p>
                    </div>
                    <div class="w-16 h-16 bg-gradient-to-br from-green-500 to-green-600 rounded-2xl flex items-center justify-center text-white shadow-lg">
                        <i class="fas fa-money-bill-wave text-2xl"></i>
                    </div>
                </div>
            </div>

            <div class="dashboard-card rounded-2xl p-6 card-hover-effect border-2 border-red-200/50 hover:border-red-300/70 shadow-xl hover:shadow-2xl transition-all duration-300" style="background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(254, 242, 242, 0.95) 50%, rgba(254, 226, 226, 0.9) 100%);">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-semibold text-gray-600 mb-1">خصومات الشهر الحالي</p>
                        <p class="text-3xl font-black text-red-700">{{ number_format($stats['current_month_deductions'], 2) }} ج.م</p>
                    </div>
                    <div class="w-16 h-16 bg-gradient-to-br from-red-500 to-red-600 rounded-2xl flex items-center justify-center text-white shadow-lg">
                        <i class="fas fa-minus-circle text-2xl"></i>
                    </div>
                </div>
            </div>

            <div class="dashboard-card rounded-2xl p-6 card-hover-effect border-2 border-blue-200/50 hover:border-blue-300/70 shadow-xl hover:shadow-2xl transition-all duration-300" style="background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(240, 249, 255, 0.95) 50%, rgba(224, 242, 254, 0.9) 100%);">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-semibold text-gray-600 mb-1">صافي الراتب</p>
                        <p class="text-3xl font-black text-blue-700">{{ number_format($stats['net_salary'], 2) }} ج.م</p>
                    </div>
                    <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center text-white shadow-lg">
                        <i class="fas fa-calculator text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- معلومات الاستحقاق -->
        <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
            <h2 class="text-xl font-bold text-gray-900 mb-4">معلومات الاستحقاق</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <p class="text-sm font-semibold text-gray-600 mb-2">تاريخ الاستحقاق القادم</p>
                    <p class="text-2xl font-bold text-gray-900">
                        @if($stats['next_payment_date'])
                            {{ $stats['next_payment_date']->format('Y-m-d') }}
                        @else
                            غير محدد
                        @endif
                    </p>
                </div>
                <div>
                    <p class="text-sm font-semibold text-gray-600 mb-2">إجمالي المدفوعات</p>
                    <p class="text-2xl font-bold text-green-600">{{ number_format($stats['total_paid'], 2) }} ج.م</p>
                </div>
            </div>
        </div>

        <!-- الدفعات القادمة -->
        @if($upcomingPayments->count() > 0)
        <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
            <h2 class="text-xl font-bold text-gray-900 mb-4">الدفعات القادمة</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">رقم الدفعة</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">تاريخ الاستحقاق</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الراتب الأساسي</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الخصومات</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">صافي الراتب</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الحالة</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($upcomingPayments as $payment)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $payment->payment_number }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $payment->payment_date->format('Y-m-d') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ number_format($payment->base_salary, 2) }} ج.م</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-red-600">{{ number_format($payment->total_deductions, 2) }} ج.م</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-green-600">{{ number_format($payment->net_salary, 2) }} ج.م</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">معلقة</span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        <!-- الخصومات الأخيرة -->
        @if($recentDeductions->count() > 0)
        <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
            <h2 class="text-xl font-bold text-gray-900 mb-4">الخصومات الأخيرة</h2>
            <div class="space-y-3">
                @foreach($recentDeductions as $deduction)
                <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition-colors">
                    <div class="flex items-center justify-between">
                        <div>
                            <h4 class="font-semibold text-gray-900">{{ $deduction->title }}</h4>
                            <p class="text-sm text-gray-600 mt-1">{{ $deduction->description }}</p>
                            <p class="text-xs text-gray-500 mt-1">{{ $deduction->deduction_date->format('Y-m-d') }}</p>
                        </div>
                        <div class="text-left">
                            <p class="text-lg font-bold text-red-600">-{{ number_format($deduction->amount, 2) }} ج.م</p>
                            <span class="px-2 py-1 text-xs font-semibold rounded-full
                                @if($deduction->type === 'tax') bg-blue-100 text-blue-800
                                @elseif($deduction->type === 'insurance') bg-purple-100 text-purple-800
                                @elseif($deduction->type === 'loan') bg-orange-100 text-orange-800
                                @elseif($deduction->type === 'penalty') bg-red-100 text-red-800
                                @else bg-gray-100 text-gray-800
                                @endif">
                                @if($deduction->type === 'tax') ضريبة
                                @elseif($deduction->type === 'insurance') تأمين
                                @elseif($deduction->type === 'loan') قرض
                                @elseif($deduction->type === 'penalty') غرامة
                                @else أخرى
                                @endif
                            </span>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- رابط الاتفاقية -->
        <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">اتفاقية العمل</h3>
                    <p class="text-sm text-gray-600 mt-1">رقم الاتفاقية: {{ $activeAgreement->agreement_number }}</p>
                </div>
                <a href="{{ route('employee.agreements.show', $activeAgreement) }}" class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors">
                    <i class="fas fa-file-contract mr-2"></i>عرض الاتفاقية
                </a>
            </div>
        </div>
    @endif
</div>
@endsection
