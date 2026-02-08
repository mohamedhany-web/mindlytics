@extends('layouts.admin')

@section('title', 'تفاصيل اتفاقية الموظف - Mindlytics')
@section('header', 'تفاصيل اتفاقية الموظف')

@section('content')
<div class="p-3 sm:p-4 md:p-6 space-y-4 sm:space-y-6" style="background: #f8fafc; min-height: 100vh;">
    <!-- Header -->
    <section class="rounded-3xl bg-white/95 backdrop-blur border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-5 py-6 sm:px-8 lg:px-12 border-b border-slate-200 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-2xl font-bold text-slate-900 flex items-center gap-3">
                    <i class="fas fa-file-contract text-emerald-600"></i>
                    {{ $employeeAgreement->title }}
                </h2>
                <p class="text-sm text-slate-500 mt-2">رقم الاتفاقية: <span class="font-semibold">{{ $employeeAgreement->agreement_number }}</span></p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.employee-agreements.edit', $employeeAgreement) }}" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-white bg-amber-600 rounded-xl shadow hover:bg-amber-700 transition-all">
                    <i class="fas fa-edit"></i>
                    تعديل
                </a>
                <a href="{{ route('admin.employee-agreements.index') }}" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-slate-600 bg-slate-100 rounded-xl hover:bg-slate-200 transition-all">
                    <i class="fas fa-arrow-right"></i>
                    رجوع
                </a>
            </div>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 p-5 sm:p-8">
            <div class="rounded-2xl border border-slate-200 bg-white/70 p-5">
                <p class="text-xs font-semibold text-slate-500 mb-2">الراتب الأساسي</p>
                <p class="text-2xl font-bold text-slate-900">{{ number_format($employeeAgreement->salary, 2) }} ج.م</p>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-white/70 p-5">
                <p class="text-xs font-semibold text-slate-500 mb-2">إجمالي الخصومات</p>
                <p class="text-2xl font-bold text-red-600">{{ number_format($stats['total_deductions'], 2) }} ج.م</p>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-white/70 p-5">
                <p class="text-xs font-semibold text-slate-500 mb-2">إجمالي المدفوعات</p>
                <p class="text-2xl font-bold text-emerald-600">{{ number_format($stats['total_payments'], 2) }} ج.م</p>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-white/70 p-5">
                <p class="text-xs font-semibold text-slate-500 mb-2">الدفعات المعلقة</p>
                <p class="text-2xl font-bold text-amber-600">{{ $stats['pending_payments'] }}</p>
            </div>
        </div>
    </section>

    <!-- Agreement Details -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6">
        <div class="lg:col-span-2 space-y-4 sm:space-y-6">
            <!-- Basic Info -->
            <section class="rounded-3xl bg-white/95 backdrop-blur border border-slate-200 shadow-lg overflow-hidden">
                <div class="px-5 py-6 sm:px-8 lg:px-12 border-b border-slate-200">
                    <h3 class="text-lg font-bold text-slate-900">معلومات الاتفاقية</h3>
                </div>
                <div class="px-5 py-6 sm:px-8 lg:px-12 space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-xs font-semibold text-slate-500 mb-1">الموظف</p>
                            <p class="text-sm font-semibold text-slate-900">{{ $employeeAgreement->employee->name }}</p>
                            <p class="text-xs text-slate-500">{{ $employeeAgreement->employee->email }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-slate-500 mb-1">الراتب</p>
                            <p class="text-sm font-semibold text-slate-900">{{ number_format($employeeAgreement->salary, 2) }} ج.م</p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-slate-500 mb-1">الحالة</p>
                            @php
                                $statusBadges = [
                                    'draft' => ['label' => 'مسودة', 'classes' => 'bg-slate-100 text-slate-700'],
                                    'active' => ['label' => 'نشط', 'classes' => 'bg-emerald-100 text-emerald-700'],
                                    'suspended' => ['label' => 'معلق', 'classes' => 'bg-amber-100 text-amber-700'],
                                    'terminated' => ['label' => 'منتهي', 'classes' => 'bg-rose-100 text-rose-700'],
                                    'completed' => ['label' => 'مكتمل', 'classes' => 'bg-blue-100 text-blue-700'],
                                ];
                                $status = $statusBadges[$employeeAgreement->status] ?? ['label' => $employeeAgreement->status, 'classes' => 'bg-slate-100 text-slate-700'];
                            @endphp
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold {{ $status['classes'] }}">
                                {{ $status['label'] }}
                            </span>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-slate-500 mb-1">تاريخ البدء</p>
                            <p class="text-sm font-semibold text-slate-900">{{ $employeeAgreement->start_date->format('Y-m-d') }}</p>
                        </div>
                        @if($employeeAgreement->end_date)
                        <div>
                            <p class="text-xs font-semibold text-slate-500 mb-1">تاريخ الانتهاء</p>
                            <p class="text-sm font-semibold text-slate-900">{{ $employeeAgreement->end_date->format('Y-m-d') }}</p>
                        </div>
                        @endif
                    </div>
                    @if($employeeAgreement->description)
                    <div>
                        <p class="text-xs font-semibold text-slate-500 mb-1">الوصف</p>
                        <p class="text-sm text-slate-700">{{ $employeeAgreement->description }}</p>
                    </div>
                    @endif
                    @if($employeeAgreement->contract_terms)
                    <div>
                        <p class="text-xs font-semibold text-slate-500 mb-1">شروط العقد</p>
                        <div class="text-sm text-slate-700 whitespace-pre-line">{{ $employeeAgreement->contract_terms }}</div>
                    </div>
                    @endif
                    @if($employeeAgreement->agreement_terms)
                    <div>
                        <p class="text-xs font-semibold text-slate-500 mb-1">بنود الاتفاقية</p>
                        <div class="text-sm text-slate-700 whitespace-pre-line">{{ $employeeAgreement->agreement_terms }}</div>
                    </div>
                    @endif
                    @if($employeeAgreement->notes)
                    <div>
                        <p class="text-xs font-semibold text-slate-500 mb-1">ملاحظات</p>
                        <div class="text-sm text-slate-700 whitespace-pre-line">{{ $employeeAgreement->notes }}</div>
                    </div>
                    @endif
                </div>
            </section>

            <!-- Deductions -->
            @if($employeeAgreement->deductions->count() > 0)
            <section class="rounded-3xl bg-white/95 backdrop-blur border border-slate-200 shadow-lg overflow-hidden">
                <div class="px-5 py-6 sm:px-8 lg:px-12 border-b border-slate-200">
                    <h3 class="text-lg font-bold text-slate-900">الخصومات</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase">رقم الخصم</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase">العنوان</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase">النوع</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase">المبلغ</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase">التاريخ</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-slate-200">
                            @foreach($employeeAgreement->deductions as $deduction)
                            <tr class="hover:bg-slate-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900">{{ $deduction->deduction_number }}</td>
                                <td class="px-6 py-4 text-sm text-slate-900">{{ $deduction->title }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-slate-100 text-slate-800">
                                        @if($deduction->type === 'tax') ضريبة
                                        @elseif($deduction->type === 'insurance') تأمين
                                        @elseif($deduction->type === 'loan') قرض
                                        @elseif($deduction->type === 'penalty') غرامة
                                        @else أخرى
                                        @endif
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-red-600">{{ number_format($deduction->amount, 2) }} ج.م</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900">{{ $deduction->deduction_date->format('Y-m-d') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </section>
            @endif

            <!-- Payments -->
            @if($employeeAgreement->payments->count() > 0)
            <section class="rounded-3xl bg-white/95 backdrop-blur border border-slate-200 shadow-lg overflow-hidden">
                <div class="px-5 py-6 sm:px-8 lg:px-12 border-b border-slate-200">
                    <h3 class="text-lg font-bold text-slate-900">سجل المدفوعات</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase">رقم الدفعة</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase">تاريخ الاستحقاق</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase">الراتب الأساسي</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase">الخصومات</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase">صافي الراتب</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase">الحالة</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-slate-200">
                            @foreach($employeeAgreement->payments as $payment)
                            <tr class="hover:bg-slate-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-slate-900">{{ $payment->payment_number }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900">{{ $payment->payment_date->format('Y-m-d') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900">{{ number_format($payment->base_salary, 2) }} ج.م</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-red-600">{{ number_format($payment->total_deductions, 2) }} ج.م</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-emerald-600">{{ number_format($payment->net_salary, 2) }} ج.م</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full
                                        @if($payment->status === 'paid') bg-emerald-100 text-emerald-800
                                        @elseif($payment->status === 'pending') bg-amber-100 text-amber-800
                                        @elseif($payment->status === 'overdue') bg-rose-100 text-rose-800
                                        @else bg-slate-100 text-slate-800
                                        @endif">
                                        @if($payment->status === 'paid') مدفوعة
                                        @elseif($payment->status === 'pending') معلقة
                                        @elseif($payment->status === 'overdue') متأخرة
                                        @else ملغاة
                                        @endif
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </section>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-4 sm:space-y-6">
            <section class="rounded-3xl bg-white/95 backdrop-blur border border-slate-200 shadow-lg overflow-hidden">
                <div class="px-5 py-6 sm:px-8 lg:px-12 border-b border-slate-200">
                    <h3 class="text-lg font-bold text-slate-900">معلومات إضافية</h3>
                </div>
                <div class="px-5 py-6 sm:px-8 lg:px-12 space-y-4">
                    <div>
                        <p class="text-xs font-semibold text-slate-500 mb-1">منشئ الاتفاقية</p>
                        <p class="text-sm font-semibold text-slate-900">{{ $employeeAgreement->creator->name ?? 'غير محدد' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-slate-500 mb-1">تاريخ الإنشاء</p>
                        <p class="text-sm text-slate-700">{{ $employeeAgreement->created_at->format('Y-m-d H:i') }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-slate-500 mb-1">آخر تحديث</p>
                        <p class="text-sm text-slate-700">{{ $employeeAgreement->updated_at->format('Y-m-d H:i') }}</p>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>
@endsection
