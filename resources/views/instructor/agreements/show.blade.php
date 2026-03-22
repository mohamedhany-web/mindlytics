@extends('layouts.app')

@section('title', __('instructor.agreement_details_title') . ' - Mindlytics')
@section('header', __('instructor.agreement_details_title'))

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <section class="rounded-2xl bg-white/95 backdrop-blur border-2 border-slate-200/50 shadow-xl overflow-hidden">
        <div class="px-5 py-6 sm:px-8 lg:px-12 border-b border-slate-200 bg-gradient-to-r from-slate-50 to-white flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-[#2CA9BD] via-[#65DBE4] to-[#2CA9BD] flex items-center justify-center text-white shadow-lg">
                    <i class="fas fa-file-contract text-xl"></i>
                </div>
                <div>
                    <h2 class="text-2xl sm:text-3xl font-black text-slate-900">{{ $agreement->title }}</h2>
                    <p class="text-sm text-slate-600 mt-1">{{ __('instructor.agreement_number') }}: <span class="font-semibold">{{ $agreement->agreement_number ?? 'N/A' }}</span></p>
                </div>
            </div>
            <a href="{{ route('instructor.agreements.index') }}" class="inline-flex items-center gap-2 rounded-xl border-2 border-slate-200 px-5 py-3 text-sm font-semibold text-slate-600 transition hover:bg-slate-50">
                <i class="fas fa-arrow-right"></i>
                {{ __('instructor.back') }}
            </a>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 p-5 sm:p-8">
            <div class="dashboard-card rounded-xl border-2 border-emerald-200/50 bg-gradient-to-br from-emerald-50 to-white p-5 shadow-lg">
                <p class="text-xs font-semibold text-emerald-700 mb-2">{{ __('instructor.total_payments') }}</p>
                <p class="text-2xl font-black text-emerald-700">{{ number_format($stats['total_earned'], 2) }} {{ __('public.currency_egp') }}</p>
            </div>
            <div class="dashboard-card rounded-xl border-2 border-amber-200/50 bg-gradient-to-br from-amber-50 to-white p-5 shadow-lg">
                <p class="text-xs font-semibold text-amber-700 mb-2">{{ __('instructor.pending') }}</p>
                <p class="text-2xl font-black text-amber-700">{{ number_format($stats['pending_amount'], 2) }} {{ __('public.currency_egp') }}</p>
            </div>
            <div class="dashboard-card rounded-xl border-2 border-blue-200/50 bg-gradient-to-br from-blue-50 to-white p-5 shadow-lg">
                <p class="text-xs font-semibold text-blue-700 mb-2">{{ __('instructor.total_payments') }}</p>
                <p class="text-2xl font-black text-blue-700">{{ $stats['total_payments'] }}</p>
            </div>
            <div class="dashboard-card rounded-xl border-2 border-green-200/50 bg-gradient-to-br from-green-50 to-white p-5 shadow-lg">
                <p class="text-xs font-semibold text-green-700 mb-2">{{ __('instructor.paid') }}</p>
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
                        {{ __('instructor.agreement_info') }}
                    </h3>
                </div>
                <div class="px-5 py-6 sm:px-8 lg:px-12 space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="bg-gradient-to-br from-blue-50 to-white p-4 rounded-xl border-2 border-blue-100">
                            <p class="text-xs font-semibold text-blue-700 mb-1">{{ __('instructor.agreement_type_label') }}</p>
                            <p class="text-sm font-black text-slate-900">
                                @if(($agreement->billing_type ?? '') === 'course_percentage')
                                    نسبة من الكورس
                                    @if($agreement->advancedCourse)
                                        <span class="block text-xs font-normal text-slate-600 mt-1">{{ $agreement->advancedCourse->title }}</span>
                                    @endif
                                @elseif($agreement->type == 'course_price')
                                    {{ __('instructor.course_price_full') }}
                                @elseif($agreement->type == 'hourly_rate')
                                    {{ __('instructor.hourly_rate_recorded') }}
                                @else
                                    {{ __('instructor.monthly_salary') }}
                                @endif
                            </p>
                        </div>
                        <div class="bg-gradient-to-br from-purple-50 to-white p-4 rounded-xl border-2 border-purple-100">
                            <p class="text-xs font-semibold text-purple-700 mb-1">{{ (($agreement->billing_type ?? '') === 'course_percentage') ? 'نسبة المدرب' : __('instructor.rate') }}</p>
                            <p class="text-sm font-black text-slate-900">
                                @if(($agreement->billing_type ?? '') === 'course_percentage')
                                    {{ number_format($agreement->course_percentage ?? 0, 2) }}%
                                @else
                                    {{ number_format($agreement->rate, 2) }} {{ __('public.currency_egp') }}
                                @endif
                            </p>
                        </div>
                        <div class="bg-gradient-to-br from-emerald-50 to-white p-4 rounded-xl border-2 border-emerald-100">
                            <p class="text-xs font-semibold text-emerald-700 mb-1">{{ __('common.status') }}</p>
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold 
                                @if($agreement->status == 'active') bg-emerald-100 text-emerald-700 border-2 border-emerald-200
                                @elseif($agreement->status == 'draft') bg-gray-100 text-gray-700 border-2 border-gray-200
                                @elseif($agreement->status == 'suspended') bg-amber-100 text-amber-700 border-2 border-amber-200
                                @elseif($agreement->status == 'terminated') bg-rose-100 text-rose-700 border-2 border-rose-200
                                @else bg-blue-100 text-blue-700 border-2 border-blue-200
                                @endif">
                                @if($agreement->status == 'active') {{ __('instructor.active_status') }}
                                @elseif($agreement->status == 'draft') {{ __('instructor.draft') }}
                                @elseif($agreement->status == 'suspended') {{ __('instructor.suspended') }}
                                @elseif($agreement->status == 'terminated') {{ __('instructor.terminated') }}
                                @else {{ __('instructor.agreement_completed') }}
                                @endif
                            </span>
                        </div>
                        <div class="bg-gradient-to-br from-indigo-50 to-white p-4 rounded-xl border-2 border-indigo-100">
                            <p class="text-xs font-semibold text-indigo-700 mb-1">{{ __('instructor.start_date') }}</p>
                            <p class="text-sm font-black text-slate-900">{{ $agreement->start_date ? $agreement->start_date->format('Y-m-d') : '-' }}</p>
                        </div>
                        @if($agreement->end_date)
                        <div class="bg-gradient-to-br from-orange-50 to-white p-4 rounded-xl border-2 border-orange-100">
                            <p class="text-xs font-semibold text-orange-700 mb-1">{{ __('instructor.end_date') }}</p>
                            <p class="text-sm font-black text-slate-900">{{ $agreement->end_date->format('Y-m-d') }}</p>
                        </div>
                        @endif
                    </div>
                    @if($agreement->description)
                    <div class="bg-gradient-to-br from-slate-50 to-white p-4 rounded-xl border-2 border-slate-100">
                        <p class="text-xs font-semibold text-slate-700 mb-2">{{ __('instructor.description') }}</p>
                        <p class="text-sm text-slate-700 leading-relaxed">{{ $agreement->description }}</p>
                    </div>
                    @endif
                    @if($agreement->terms)
                    <div class="bg-gradient-to-br from-slate-50 to-white p-4 rounded-xl border-2 border-slate-100">
                        <p class="text-xs font-semibold text-slate-700 mb-2">{{ __('instructor.contract_terms') }}</p>
                        <div class="text-sm text-slate-700 whitespace-pre-line leading-relaxed">{{ $agreement->terms }}</div>
                    </div>
                    @endif
                    @if($agreement->notes)
                    <div class="bg-gradient-to-br from-amber-50 to-white p-4 rounded-xl border-2 border-amber-100">
                        <p class="text-xs font-semibold text-amber-700 mb-2">{{ __('instructor.notes') }}</p>
                        <div class="text-sm text-amber-800 whitespace-pre-line leading-relaxed">{{ $agreement->notes }}</div>
                    </div>
                    @endif
                </div>
            </section>

            @if(($agreement->billing_type ?? '') === 'course_percentage')
            {{-- تفعيلات الطلاب وحصتي من كل شراء — يظهر عند اتفاقية "نسبة من الكورس" فقط --}}
            @php
                $activationPayments = $agreement->payments->where('type', 'course_activation');
            @endphp
            <section class="rounded-2xl bg-white/95 backdrop-blur border-2 border-blue-200/50 shadow-xl overflow-hidden">
                <div class="px-5 py-6 sm:px-8 lg:px-12 border-b border-slate-200 bg-gradient-to-r from-blue-50 to-white flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <h3 class="text-lg font-black text-slate-900 flex items-center gap-2">
                            <i class="fas fa-user-graduate text-blue-600"></i>
                            تفعيلات الطلاب وحصتي من كل شراء
                        </h3>
                        <p class="text-sm text-slate-600 mt-1">عند كل تفعيل لطالب في الكورس تظهر نسبتك من مبلغ الشراء وحصتك.</p>
                    </div>
                    <a href="{{ route('instructor.agreements.export-activations', $agreement) }}"
                       class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl font-bold text-sm bg-emerald-600 hover:bg-emerald-700 text-white shadow-lg shadow-emerald-500/25 transition-all duration-200 border-2 border-emerald-500/30 whitespace-nowrap">
                        <i class="fas fa-file-excel"></i>
                        تصدير إلى Excel
                    </a>
                </div>
                @if($activationPayments->isNotEmpty())
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-50">
                            <tr class="text-xs font-bold uppercase tracking-widest text-slate-700">
                                <th class="px-6 py-4 text-right">التاريخ</th>
                                <th class="px-6 py-4 text-right">الطالب</th>
                                <th class="px-6 py-4 text-right">مبلغ الشراء (ج.م)</th>
                                <th class="px-6 py-4 text-right">نسبتي</th>
                                <th class="px-6 py-4 text-right">حصتي (ج.م)</th>
                                <th class="px-6 py-4 text-right">الحالة</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 bg-white text-sm">
                            @foreach($activationPayments as $p)
                            <tr class="hover:bg-slate-50">
                                <td class="px-6 py-4 text-slate-600">{{ $p->created_at?->format('Y-m-d') ?? '—' }}</td>
                                <td class="px-6 py-4 font-medium text-slate-900">{{ $p->enrollment?->student?->name ?? '—' }}</td>
                                <td class="px-6 py-4">{{ $p->enrollment ? number_format($p->enrollment->final_price ?? 0, 2) : '—' }}</td>
                                <td class="px-6 py-4 font-semibold text-blue-700">{{ number_format($agreement->course_percentage ?? 0, 2) }}%</td>
                                <td class="px-6 py-4 font-black text-slate-900">{{ number_format($p->amount, 2) }} ج.م</td>
                                <td class="px-6 py-4">
                                    @if($p->status === 'paid')
                                        <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-700">مدفوع</span>
                                    @elseif($p->status === 'approved')
                                        <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-700">موافق عليه</span>
                                    @else
                                        <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-700">قيد المراجعة</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="px-5 py-4 sm:px-8 lg:px-12 bg-gradient-to-r from-blue-50 to-white border-t-2 border-blue-100">
                    <p class="text-base font-black text-slate-900">
                        إجمالي أرباحي من هذه الاتفاقية: <span class="text-blue-700">{{ number_format($activationPayments->sum('amount'), 2) }} ج.م</span>
                    </p>
                </div>
                @else
                <div class="px-5 py-8 sm:px-8 lg:px-12 text-center text-slate-500">
                    <i class="fas fa-user-graduate text-4xl text-slate-300 mb-3"></i>
                    <p class="font-medium">لا توجد تفعيلات للطلاب حتى الآن.</p>
                    <p class="text-sm mt-1">عند تفعيل أي طالب في هذا الكورس ستظهر هنا نسبتك وحصتك تلقائياً.</p>
                </div>
                @endif
            </section>
            @endif

            <!-- Payments -->
            <section class="rounded-2xl bg-white/95 backdrop-blur border-2 border-slate-200/50 shadow-xl overflow-hidden">
                <div class="px-5 py-6 sm:px-8 lg:px-12 border-b border-slate-200 bg-gradient-to-r from-slate-50 to-white">
                    <h3 class="text-lg font-black text-slate-900 flex items-center gap-2">
                        <i class="fas fa-receipt text-[#2CA9BD]"></i>
                        {{ __('instructor.payments_log') }}
                    </h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-50">
                            <tr class="text-xs font-bold uppercase tracking-widest text-slate-700">
                                <th class="px-6 py-4 text-right">{{ __('instructor.payment_number') }}</th>
                                <th class="px-6 py-4 text-right">{{ __('instructor.type') }}</th>
                                <th class="px-6 py-4 text-right">{{ __('instructor.amount') }}</th>
                                <th class="px-6 py-4 text-right">{{ __('common.status') }}</th>
                                <th class="px-6 py-4 text-right">{{ __('common.date') }}</th>
                                <th class="px-6 py-4 text-right">{{ __('instructor.transfer_receipt') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 bg-white text-sm">
                            @forelse($agreement->payments as $payment)
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="px-6 py-4 font-semibold text-slate-900">{{ $payment->payment_number ?? 'N/A' }}</td>
                                    <td class="px-6 py-4">
                                        @php
                                            $typeLabels = [
                                                'course_completion' => __('instructor.course_price_full'),
                                                'course_sale' => __('instructor.course_price'),
                                                'course_price' => __('instructor.course_price'),
                                                'hourly_teaching' => __('instructor.hourly_rate_recorded'),
                                                'lecture_hour' => __('instructor.hourly_rate_recorded'),
                                                'hourly_rate' => __('instructor.hourly_rate'),
                                                'monthly_salary' => __('instructor.monthly_salary'),
                                                'bonus' => __('instructor.bonus'),
                                                'other' => __('instructor.other'),
                                                'course_activation' => 'نسبة من تفعيل الطالب',
                                            ];
                                            $typeLabel = $typeLabels[$payment->type] ?? ($payment->type ?? __('instructor.not_specified'));
                                        @endphp
                                        <div>
                                            <span class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-bold bg-blue-100 text-blue-700 border border-blue-200">
                                                {{ $typeLabel }}
                                            </span>
                                            @if($payment->type === 'course_activation' && $payment->enrollment)
                                                <p class="text-xs text-slate-500 mt-1">الطالب: {{ $payment->enrollment->student->name ?? '—' }}</p>
                                                <p class="text-xs text-slate-500 mt-1">مبلغ التفعيل: {{ number_format($payment->enrollment->final_price ?? 0, 2) }} ج.م → حصتك {{ number_format($payment->amount, 2) }} ج.م</p>
                                            @endif
                                            @if($payment->course)
                                                <p class="text-xs text-slate-500 mt-1">{{ $payment->course->title ?? '' }}</p>
                                            @endif
                                            @if($payment->lecture)
                                                <p class="text-xs text-slate-500 mt-1">{{ $payment->lecture->title ?? '' }}</p>
                                            @endif
                                            @if($payment->hours_count)
                                                <p class="text-xs text-slate-500 mt-1">{{ $payment->hours_count }} {{ __('instructor.hour') }}</p>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 font-black text-slate-900">{{ number_format($payment->amount, 2) }} {{ __('public.currency_egp') }}</td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold 
                                            @if($payment->status == 'paid') bg-emerald-100 text-emerald-700 border-2 border-emerald-200
                                            @elseif($payment->status == 'approved') bg-amber-100 text-amber-700 border-2 border-amber-200
                                            @else bg-gray-100 text-gray-700 border-2 border-gray-200
                                            @endif">
                                            @if($payment->status == 'paid') {{ __('instructor.received') }}
                                            @elseif($payment->status == 'approved') {{ __('instructor.approved') }}
                                            @else {{ __('instructor.pending_review') }}
                                            @endif
                                        </span>
                                        @if($payment->status == 'paid')
                                            <p class="text-xs text-emerald-600 mt-1">{{ __('instructor.amount_transferred_info') }}</p>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-xs text-slate-600">{{ $payment->created_at->format('Y-m-d') }}</td>
                                    <td class="px-6 py-4">
                                        @if($payment->status == 'paid' && $payment->transfer_receipt_path)
                                            <a href="{{ asset('storage/' . $payment->transfer_receipt_path) }}" target="_blank" class="inline-flex items-center gap-1 px-2.5 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-xs font-semibold">
                                                <i class="fas fa-receipt"></i>
                                                {{ __('instructor.download_receipt') }}
                                            </a>
                                        @else
                                            —
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center gap-4">
                                            <div class="w-16 h-16 bg-slate-100 rounded-2xl flex items-center justify-center">
                                                <i class="fas fa-receipt text-slate-400 text-2xl"></i>
                                            </div>
                                            <div>
                                                <p class="font-bold text-slate-900">{{ __('instructor.no_payments') }}</p>
                                                <p class="text-sm text-slate-600 mt-1">{{ __('instructor.no_payments_yet') }}</p>
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
                        {{ __('instructor.quick_tips') }}
                    </h3>
                </div>
                <div class="px-5 py-6 sm:px-8 lg:px-12 space-y-4">
                    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 p-4 rounded-xl border-2 border-blue-200">
                        <div class="flex items-center gap-2 mb-2">
                            <i class="fas fa-info-circle text-blue-600"></i>
                            <span class="text-sm font-bold text-blue-800">{{ __('instructor.tips') }}</span>
                        </div>
                        <ul class="mt-2 text-sm text-blue-700 space-y-1.5 font-medium">
                            <li>• {{ __('instructor.tips_follow_payments') }}</li>
                            <li>• {{ __('instructor.tips_pending') }}</li>
                            <li>• {{ __('instructor.tips_completed') }}</li>
                        </ul>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>
@endsection
