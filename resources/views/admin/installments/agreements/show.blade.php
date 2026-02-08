@extends('layouts.admin')

@section('title', 'تفاصيل اتفاقية التقسيط')
@section('header', 'تفاصيل اتفاقية التقسيط')

@section('content')
@php
    $agreement = $agreement ?? null;
    $plan = $agreement?->plan;
    $student = $agreement?->student;
    $course = $agreement?->course;
    $payments = $agreement?->payments ?? collect();
    $pendingPayments = $payments->where('status', \App\Models\InstallmentPayment::STATUS_PENDING)->sortBy('due_date');
    $nextPayment = $pendingPayments->first();
@endphp
<div class="container mx-auto px-4 py-8 space-y-8">
    <div class="bg-gradient-to-br from-emerald-500 via-emerald-600 to-sky-600 rounded-3xl shadow-xl text-white p-8 relative overflow-hidden">
        <div class="absolute inset-y-0 right-0 w-1/3 pointer-events-none opacity-20">
            <div class="w-full h-full bg-[url('https://www.transparenttextures.com/patterns/cubes.png')]"></div>
        </div>
        <div class="relative flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
            <div>
                <div class="flex items-center gap-3 flex-wrap">
                    <h1 class="text-3xl font-black tracking-tight">{{ $student->name ?? 'طالب غير معروف' }}</h1>
                    <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold
                        @class([
                            'bg-emerald-100 text-emerald-700' => $agreement->status === \App\Models\InstallmentAgreement::STATUS_ACTIVE,
                            'bg-amber-100 text-amber-700' => $agreement->status === \App\Models\InstallmentAgreement::STATUS_OVERDUE,
                            'bg-purple-100 text-purple-700' => $agreement->status === \App\Models\InstallmentAgreement::STATUS_COMPLETED,
                            'bg-rose-100 text-rose-700' => $agreement->status === \App\Models\InstallmentAgreement::STATUS_CANCELLED,
                            'bg-white/20 text-white' => !in_array($agreement->status, [\App\Models\InstallmentAgreement::STATUS_ACTIVE, \App\Models\InstallmentAgreement::STATUS_OVERDUE, \App\Models\InstallmentAgreement::STATUS_COMPLETED, \App\Models\InstallmentAgreement::STATUS_CANCELLED])
                        ])">
                        <span class="w-2 h-2 rounded-full bg-current/60"></span>
                        {{ $statuses[$agreement->status] ?? $agreement->status }}
                    </span>
                </div>
                <p class="mt-3 text-white/80 max-w-2xl">
                    الكورس: {{ $course->title ?? 'خطة عامة' }} — بدأت في {{ optional($agreement->start_date)->format('Y-m-d') }}. تتبع أدناه جدول الأقساط والمبالغ المستحقة.
                </p>
            </div>
            <div class="flex flex-wrap gap-3 justify-end">
                <a href="{{ route('admin.installments.agreements.edit', $agreement) }}" class="inline-flex items-center gap-2 px-5 py-3 rounded-2xl bg-white text-emerald-700 font-semibold shadow-lg hover:shadow-xl transition-all">
                    <i class="fas fa-edit"></i>
                    تعديل الاتفاقية
                </a>
                <a href="{{ route('admin.installments.agreements.index') }}" class="inline-flex items-center gap-2 px-5 py-3 rounded-2xl bg-white/20 text-white font-semibold border border-white/30 hover:bg-white/30 transition-all">
                    <i class="fas fa-arrow-right"></i>
                    العودة للقائمة
                </a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
        <div class="rounded-2xl bg-white shadow-lg border border-sky-100 p-6">
            <p class="text-sm font-semibold text-sky-500">إجمالي الاتفاقية</p>
            <p class="mt-2 text-3xl font-black text-gray-900">{{ number_format($agreement->total_amount ?? 0, 2) }} ج.م</p>
            <p class="text-xs text-gray-500 mt-3">القيمة الكاملة التي سيتم سدادها عبر الخطة.</p>
        </div>
        <div class="rounded-2xl bg-white shadow-lg border border-emerald-100 p-6">
            <p class="text-sm font-semibold text-emerald-500">الدفعة المقدمة</p>
            <p class="mt-2 text-3xl font-black text-gray-900">{{ number_format($agreement->deposit_amount ?? 0, 2) }} ج.م</p>
            <p class="text-xs text-gray-500 mt-3">تم تحصيلها عند توقيع الاتفاقية.</p>
        </div>
        <div class="rounded-2xl bg-white shadow-lg border border-amber-100.dark:border-gray-700 p-6">
            <p class="text-sm font-semibold text-amber-500">الأقساط المتبقية</p>
            <p class="mt-2 text-3xl font-black text-gray-900">{{ $pendingPayments->count() }}</p>
            <p class="text-xs text-gray-500 mt-3">القيمة التالية: {{ optional($nextPayment)->amount ? number_format($nextPayment->amount, 2) . ' ج.م' : '—' }}</p>
        </div>
        <div class="rounded-2xl bg-white shadow-lg border border-purple-100 p-6">
            <p class="text-sm font-semibold text-purple-500">القسط القادم</p>
            <p class="mt-2 text-3xl font-black text-gray-900">{{ optional($nextPayment)->due_date?->format('Y-m-d') ?? '—' }}</p>
            <p class="text-xs text-gray-500 mt-3">عدد الأقساط الكلي: {{ $agreement->installments_count }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <div class="xl:col-span-2 space-y-6">
            <div class="bg-white.dark:bg-gray-900 rounded-2xl shadow-lg border border-gray-100 p-6 space-y-6">
                <h2 class="text-lg font-black text-gray-900">تفاصيل الطالب والكورس</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-sm">
                    <div>
                        <p class="text-xs text-gray-500 uppercase">اسم الطالب</p>
                        <p class="mt-2 text-base font-semibold text-gray-900">{{ $student->name ?? 'غير متوفر' }}</p>
                        <p class="text-xs text-gray-500">{{ $student->phone ?? 'بدون هاتف' }} · {{ $student->email ?? 'بدون بريد' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase">الكورس</p>
                        <p class="mt-2 text-base font-semibold text-gray-900">{{ $course->title ?? 'خطة عامة' }}</p>
                        <p class="text-xs text-gray-500">سعر الكورس: {{ number_format($course->price ?? 0, 2) }} ج.م</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase">الخطة</p>
                        <p class="mt-2 text-base font-semibold text-gray-900">{{ $plan->name ?? 'غير محددة' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500.dark:text-gray-400 uppercase">دورية الأقساط</p>
                        <p class="mt-2 text-base font-semibold text-gray-900">كل {{ $plan->frequency_interval ?? '—' }} {{ ($plan && $frequencyUnits[$plan->frequency_unit] ?? $plan->frequency_unit ?? '-') }}</p>
                        <p class="text-xs text-gray-500">فترة السماح: {{ $plan->grace_period_days ?? 0 }} يوم</p>
                    </div>
                </div>
                @if($agreement->notes)
                    <div class="bg-gray-50 rounded-2xl px-4 py-3 text-xs text-gray-600 leading-relaxed">
                        <strong class="text-sm text-gray-900">ملاحظات الاتفاقية:</strong>
                        <p class="mt-2">{{ $agreement->notes }}</p>
                    </div>
                @endif
            </div>

            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 space-y-4">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-black text-gray-900">جدول الأقساط</h2>
                    <span class="inline-flex items-center gap-2 text-xs font-semibold px-3 py-1 rounded-full bg-sky-100 text-sky-700">
                        <i class="fas fa-stream text-xs"></i>
                        {{ $payments->count() }} دفعات
                    </span>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm text-gray-700 divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-right">#</th>
                                <th class="px-4 py-3 text-right">تاريخ الاستحقاق</th>
                                <th class="px-4 py-3 text-right">المبلغ</th>
                                <th class="px-4 py-3 text-right">الحالة</th>
                                <th class="px-4 py-3 text-right">ملاحظات</th>
                                <th class="px-4 py-3"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($payments as $payment)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-4 py-3 font-semibold">{{ $payment->sequence_number }}</td>
                                    <td class="px-4 py-3">{{ optional($payment->due_date)->format('Y-m-d') }}</td>
                                    <td class="px-4 py-3 font-semibold text-gray-900">{{ number_format($payment->amount ?? 0, 2) }} ج.م</td>
                                    <td class="px-4 py-3">
                                        <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold
                                            @class([
                                                'bg-emerald-100 text-emerald-700' => $payment->status === \App\Models\InstallmentPayment::STATUS_PAID,
                                                'bg-amber-100 text-amber-700' => $payment->status === \App\Models\InstallmentPayment::STATUS_OVERDUE,
                                                'bg-rose-100 text-rose-700' => $payment->status === \App\Models\InstallmentPayment::STATUS_SKIPPED,
                                                'bg-gray-100 text-gray-700' => $payment->status === \App\Models\InstallmentPayment::STATUS_PENDING,
                                            ])">
                                            {{ $paymentStatuses[$payment->status] ?? $payment->status }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">{{ $payment->notes ?? '—' }}</td>
                                    <td class="px-4 py-3 text-left">
                                        <form action="{{ route('admin.installments.agreements.mark-payment', $payment) }}" method="POST" class="inline-flex gap-2">
                                            @csrf
                                            <input type="hidden" name="status" value="{{ $payment->status === \App\Models\InstallmentPayment::STATUS_PAID ? \App\Models\InstallmentPayment::STATUS_PENDING : \App\Models\InstallmentPayment::STATUS_PAID }}">
                                            <button type="submit" class="text-xs px-3 py-1 rounded-xl bg-sky-100 text-sky-600 hover:bg-sky-200">
                                                {{ $payment->status === \App\Models\InstallmentPayment::STATUS_PAID ? 'تعيين كقيد الانتظار' : 'وضع علامة كمدفوع' }}
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-10 text-center text-gray-500 text-sm">لم يتم توليد جدول أقساط بعد.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="bg-white rounded-3xl shadow-lg border border-gray-100 p-6">
                <h2 class="text-lg font-black text-gray-900 mb-4">نظرة سريعة</h2>
                <ul class="space-y-3 text-sm text-gray-600">
                    <li class="flex items-start gap-2">
                        <i class="fas fa-wallet mt-1 text-emerald-500"></i>
                        مجموع ما تم دفعه حتى الآن: {{ number_format($payments->where('status', \App\Models\InstallmentPayment::STATUS_PAID)->sum('amount'), 2) }} ج.م
                    </li>
                    <li class="flex items-start gap-2">
                        <i class="fas fa-balance-scale mt-1 text-emerald-500"></i>
                        المبلغ المتبقي: {{ number_format(($agreement->total_amount ?? 0) - $payments->where('status', \App\Models\InstallmentPayment::STATUS_PAID)->sum('amount'), 2) }} ج.م
                    </li>
                    <li class="flex items-start gap-2">
                        <i class="fas fa-exclamation-triangle mt-1 text-emerald-500"></i>
                        الأقساط المتأخرة: {{ $payments->where('status', \App\Models\InstallmentPayment::STATUS_OVERDUE)->count() }} قسط
                    </li>
                </ul>
            </div>

            <div class="bg-white rounded-3xl shadow-lg border border-gray-100 p-6">
                <h2 class="text-lg font-black text-gray-900 mb-4">إجراءات إضافية</h2>
                <div class="space-y-3">
                    <form action="{{ route('admin.installments.agreements.destroy', $agreement) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من إلغاء الاتفاقية؟');" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="flex items-center justify-between w-full px-4 py-3 rounded-2xl border border-rose-100 bg-rose-50/70 text-rose-600 hover:border-rose-200 transition-all">
                            <span class="font-semibold text-sm">إلغاء الاتفاقية</span>
                            <i class="fas fa-ban text-xs"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
