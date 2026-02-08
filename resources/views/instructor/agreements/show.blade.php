@extends('layouts.app')

@section('title', 'تفاصيل الاتفاقية - Mindlytics')
@section('header', 'تفاصيل الاتفاقية')

@section('content')
<div class="p-3 sm:p-4 md:p-6 space-y-4 sm:space-y-6" style="background: #f8fafc; min-height: 100vh;">
    <!-- Header -->
    <section class="rounded-2xl bg-white/95 backdrop-blur border-2 border-slate-200/50 shadow-xl overflow-hidden">
        <div class="px-5 py-6 sm:px-8 lg:px-12 border-b border-slate-200 bg-gradient-to-r from-slate-50 to-white flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-[#2CA9BD] via-[#65DBE4] to-[#2CA9BD] flex items-center justify-center text-white shadow-lg">
                    <i class="fas fa-file-contract text-xl"></i>
                </div>
                <div>
                    <h2 class="text-2xl sm:text-3xl font-black text-slate-900">{{ $agreement->title }}</h2>
                    <p class="text-sm text-slate-600 mt-1">رقم الاتفاقية: <span class="font-semibold">{{ $agreement->agreement_number ?? 'N/A' }}</span></p>
                </div>
            </div>
            <a href="{{ route('instructor.agreements.index') }}" class="inline-flex items-center gap-2 rounded-xl border-2 border-slate-200 px-5 py-3 text-sm font-semibold text-slate-600 transition hover:bg-slate-50">
                <i class="fas fa-arrow-right"></i>
                رجوع
            </a>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 p-5 sm:p-8">
            <div class="dashboard-card rounded-xl border-2 border-emerald-200/50 bg-gradient-to-br from-emerald-50 to-white p-5 shadow-lg">
                <p class="text-xs font-semibold text-emerald-700 mb-2">إجمالي المدفوعات</p>
                <p class="text-2xl font-black text-emerald-700">{{ number_format($stats['total_earned'], 2) }} ج.م</p>
            </div>
            <div class="dashboard-card rounded-xl border-2 border-amber-200/50 bg-gradient-to-br from-amber-50 to-white p-5 shadow-lg">
                <p class="text-xs font-semibold text-amber-700 mb-2">معلق</p>
                <p class="text-2xl font-black text-amber-700">{{ number_format($stats['pending_amount'], 2) }} ج.م</p>
            </div>
            <div class="dashboard-card rounded-xl border-2 border-blue-200/50 bg-gradient-to-br from-blue-50 to-white p-5 shadow-lg">
                <p class="text-xs font-semibold text-blue-700 mb-2">إجمالي المدفوعات</p>
                <p class="text-2xl font-black text-blue-700">{{ $stats['total_payments'] }}</p>
            </div>
            <div class="dashboard-card rounded-xl border-2 border-green-200/50 bg-gradient-to-br from-green-50 to-white p-5 shadow-lg">
                <p class="text-xs font-semibold text-green-700 mb-2">مدفوع</p>
                <p class="text-2xl font-black text-green-700">{{ $stats['paid_payments'] }}</p>
            </div>
        </div>
    </section>

    <!-- Agreement Details -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6">
        <div class="lg:col-span-2 space-y-4 sm:space-y-6">
            <!-- Basic Info -->
            <section class="rounded-2xl bg-white/95 backdrop-blur border-2 border-slate-200/50 shadow-xl overflow-hidden">
                <div class="px-5 py-6 sm:px-8 lg:px-12 border-b border-slate-200 bg-gradient-to-r from-slate-50 to-white">
                    <h3 class="text-lg font-black text-slate-900 flex items-center gap-2">
                        <i class="fas fa-info-circle text-[#2CA9BD]"></i>
                        معلومات الاتفاقية
                    </h3>
                </div>
                <div class="px-5 py-6 sm:px-8 lg:px-12 space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="bg-gradient-to-br from-blue-50 to-white p-4 rounded-xl border-2 border-blue-100">
                            <p class="text-xs font-semibold text-blue-700 mb-1">نوع الاتفاقية</p>
                            <p class="text-sm font-black text-slate-900">
                                @if($agreement->type == 'course_price')
                                    سعر للكورس كاملاً
                                @elseif($agreement->type == 'hourly_rate')
                                    سعر للساعة المسجلة
                                @else
                                    راتب شهري
                                @endif
                            </p>
                        </div>
                        <div class="bg-gradient-to-br from-purple-50 to-white p-4 rounded-xl border-2 border-purple-100">
                            <p class="text-xs font-semibold text-purple-700 mb-1">السعر/المعدل</p>
                            <p class="text-sm font-black text-slate-900">{{ number_format($agreement->rate, 2) }} ج.م</p>
                        </div>
                        <div class="bg-gradient-to-br from-emerald-50 to-white p-4 rounded-xl border-2 border-emerald-100">
                            <p class="text-xs font-semibold text-emerald-700 mb-1">الحالة</p>
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold 
                                @if($agreement->status == 'active') bg-emerald-100 text-emerald-700 border-2 border-emerald-200
                                @elseif($agreement->status == 'draft') bg-gray-100 text-gray-700 border-2 border-gray-200
                                @elseif($agreement->status == 'suspended') bg-amber-100 text-amber-700 border-2 border-amber-200
                                @elseif($agreement->status == 'terminated') bg-rose-100 text-rose-700 border-2 border-rose-200
                                @else bg-blue-100 text-blue-700 border-2 border-blue-200
                                @endif">
                                @if($agreement->status == 'active') نشط
                                @elseif($agreement->status == 'draft') مسودة
                                @elseif($agreement->status == 'suspended') معلق
                                @elseif($agreement->status == 'terminated') منتهي
                                @else مكتمل
                                @endif
                            </span>
                        </div>
                        <div class="bg-gradient-to-br from-indigo-50 to-white p-4 rounded-xl border-2 border-indigo-100">
                            <p class="text-xs font-semibold text-indigo-700 mb-1">تاريخ البدء</p>
                            <p class="text-sm font-black text-slate-900">{{ $agreement->start_date ? $agreement->start_date->format('Y-m-d') : '-' }}</p>
                        </div>
                        @if($agreement->end_date)
                        <div class="bg-gradient-to-br from-orange-50 to-white p-4 rounded-xl border-2 border-orange-100">
                            <p class="text-xs font-semibold text-orange-700 mb-1">تاريخ الانتهاء</p>
                            <p class="text-sm font-black text-slate-900">{{ $agreement->end_date->format('Y-m-d') }}</p>
                        </div>
                        @endif
                    </div>
                    @if($agreement->description)
                    <div class="bg-gradient-to-br from-slate-50 to-white p-4 rounded-xl border-2 border-slate-100">
                        <p class="text-xs font-semibold text-slate-700 mb-2">الوصف</p>
                        <p class="text-sm text-slate-700 leading-relaxed">{{ $agreement->description }}</p>
                    </div>
                    @endif
                    @if($agreement->terms)
                    <div class="bg-gradient-to-br from-slate-50 to-white p-4 rounded-xl border-2 border-slate-100">
                        <p class="text-xs font-semibold text-slate-700 mb-2">شروط العقد</p>
                        <div class="text-sm text-slate-700 whitespace-pre-line leading-relaxed">{{ $agreement->terms }}</div>
                    </div>
                    @endif
                    @if($agreement->notes)
                    <div class="bg-gradient-to-br from-amber-50 to-white p-4 rounded-xl border-2 border-amber-100">
                        <p class="text-xs font-semibold text-amber-700 mb-2">ملاحظات</p>
                        <div class="text-sm text-amber-800 whitespace-pre-line leading-relaxed">{{ $agreement->notes }}</div>
                    </div>
                    @endif
                </div>
            </section>

            <!-- Payments -->
            <section class="rounded-2xl bg-white/95 backdrop-blur border-2 border-slate-200/50 shadow-xl overflow-hidden">
                <div class="px-5 py-6 sm:px-8 lg:px-12 border-b border-slate-200 bg-gradient-to-r from-slate-50 to-white">
                    <h3 class="text-lg font-black text-slate-900 flex items-center gap-2">
                        <i class="fas fa-receipt text-[#2CA9BD]"></i>
                        سجل المدفوعات
                    </h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-50">
                            <tr class="text-xs font-bold uppercase tracking-widest text-slate-700">
                                <th class="px-6 py-4 text-right">رقم الدفعة</th>
                                <th class="px-6 py-4 text-right">النوع</th>
                                <th class="px-6 py-4 text-right">المبلغ</th>
                                <th class="px-6 py-4 text-right">الحالة</th>
                                <th class="px-6 py-4 text-right">التاريخ</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 bg-white text-sm">
                            @forelse($agreement->payments as $payment)
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="px-6 py-4 font-semibold text-slate-900">{{ $payment->payment_number ?? 'N/A' }}</td>
                                    <td class="px-6 py-4">
                                        @php
                                            $typeLabels = [
                                                'course_completion' => 'إكمال كورس',
                                                'course_sale' => 'بيع كورس',
                                                'course_price' => 'سعر كورس',
                                                'hourly_teaching' => 'ساعة تدريس',
                                                'lecture_hour' => 'ساعة تدريس',
                                                'hourly_rate' => 'سعر ساعة',
                                                'monthly_salary' => 'راتب شهري',
                                                'bonus' => 'مكافأة',
                                                'other' => 'أخرى',
                                            ];
                                            $typeLabel = $typeLabels[$payment->type] ?? ($payment->type ?? 'غير محدد');
                                        @endphp
                                        <div>
                                            <span class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-bold bg-blue-100 text-blue-700 border border-blue-200">
                                                {{ $typeLabel }}
                                            </span>
                                            @if($payment->course)
                                                <p class="text-xs text-slate-500 mt-1">{{ $payment->course->title ?? '' }}</p>
                                            @endif
                                            @if($payment->lecture)
                                                <p class="text-xs text-slate-500 mt-1">{{ $payment->lecture->title ?? '' }}</p>
                                            @endif
                                            @if($payment->hours_count)
                                                <p class="text-xs text-slate-500 mt-1">{{ $payment->hours_count }} ساعة</p>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 font-black text-slate-900">{{ number_format($payment->amount, 2) }} ج.م</td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold 
                                            @if($payment->status == 'paid') bg-emerald-100 text-emerald-700 border-2 border-emerald-200
                                            @elseif($payment->status == 'approved') bg-amber-100 text-amber-700 border-2 border-amber-200
                                            @else bg-gray-100 text-gray-700 border-2 border-gray-200
                                            @endif">
                                            @if($payment->status == 'paid') مدفوع
                                            @elseif($payment->status == 'approved') معتمد
                                            @else قيد المراجعة
                                            @endif
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-xs text-slate-600">{{ $payment->created_at->format('Y-m-d') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center gap-4">
                                            <div class="w-16 h-16 bg-slate-100 rounded-2xl flex items-center justify-center">
                                                <i class="fas fa-receipt text-slate-400 text-2xl"></i>
                                            </div>
                                            <div>
                                                <p class="font-bold text-slate-900">لا توجد مدفوعات</p>
                                                <p class="text-sm text-slate-600 mt-1">لم يتم تسجيل أي مدفوعات بعد</p>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        </div>

        <!-- Sidebar -->
        <div class="space-y-4 sm:space-y-6">
            <section class="rounded-2xl bg-white/95 backdrop-blur border-2 border-slate-200/50 shadow-xl overflow-hidden">
                <div class="px-5 py-6 sm:px-8 lg:px-12 border-b border-slate-200 bg-gradient-to-r from-slate-50 to-white">
                    <h3 class="text-lg font-black text-slate-900 flex items-center gap-2">
                        <i class="fas fa-info-circle text-[#2CA9BD]"></i>
                        معلومات سريعة
                    </h3>
                </div>
                <div class="px-5 py-6 sm:px-8 lg:px-12 space-y-4">
                    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 p-4 rounded-xl border-2 border-blue-200">
                        <div class="flex items-center gap-2 mb-2">
                            <i class="fas fa-info-circle text-blue-600"></i>
                            <span class="text-sm font-bold text-blue-800">نصائح</span>
                        </div>
                        <ul class="mt-2 text-sm text-blue-700 space-y-1.5 font-medium">
                            <li>• يمكنك متابعة جميع المدفوعات هنا</li>
                            <li>• المدفوعات المعتمدة تظهر في "معلق"</li>
                            <li>• المدفوعات المكتملة تظهر في "إجمالي المدفوعات"</li>
                        </ul>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>
@endsection
