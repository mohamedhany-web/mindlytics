@extends('layouts.admin')

@section('title', 'تفاصيل الدفعة - Mindlytics')
@section('header', 'تفاصيل الدفعة')

@section('content')
@php
    $statusBadges = [
        'completed' => ['label' => 'مكتملة', 'classes' => 'bg-emerald-100 text-emerald-700'],
        'pending' => ['label' => 'معلقة', 'classes' => 'bg-amber-100 text-amber-700'],
        'processing' => ['label' => 'قيد المعالجة', 'classes' => 'bg-blue-100 text-blue-700'],
        'failed' => ['label' => 'فاشلة', 'classes' => 'bg-rose-100 text-rose-700'],
        'cancelled' => ['label' => 'ملغاة', 'classes' => 'bg-slate-100 text-slate-700'],
        'refunded' => ['label' => 'مستردة', 'classes' => 'bg-orange-100 text-orange-700'],
    ];
    $paymentMethodLabels = [
        'cash' => ['label' => 'نقدي', 'icon' => 'fas fa-money-bill'],
        'card' => ['label' => 'بطاقة', 'icon' => 'fas fa-credit-card'],
        'bank_transfer' => ['label' => 'تحويل بنكي', 'icon' => 'fas fa-university'],
        'online' => ['label' => 'دفع إلكتروني', 'icon' => 'fas fa-globe'],
        'wallet' => ['label' => 'محفظة', 'icon' => 'fas fa-wallet'],
        'other' => ['label' => 'أخرى', 'icon' => 'fas fa-ellipsis-h'],
    ];
@endphp

<div class="space-y-10">
    <section class="rounded-3xl bg-white/95 backdrop-blur border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-5 py-6 sm:px-8 lg:px-12 flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-widest text-slate-400">دفعة رقم</p>
                <h2 class="mt-1 text-2xl font-bold text-slate-900">#{{ $payment->payment_number }}</h2>
                <p class="text-sm text-slate-500 mt-1">أُنشئت في {{ $payment->created_at->format('Y-m-d H:i') }}</p>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('admin.payments.edit', $payment) }}" class="inline-flex items-center gap-2 rounded-2xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-600 transition hover:border-sky-300 hover:text-sky-600">
                    <i class="fas fa-edit"></i>
                    تعديل الدفعة
                </a>
                <a href="{{ route('admin.payments.index') }}" class="inline-flex items-center gap-2 rounded-2xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-slate-50">
                    <i class="fas fa-arrow-right"></i>
                    العودة
                </a>
            </div>
        </div>

        <div class="px-5 py-6 sm:px-8 lg:px-12 space-y-6">
            <!-- معلومات الدفعة الرئيسية -->
            <div class="rounded-2xl border border-slate-200 bg-white/85 p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-bold text-slate-900">معلومات الدفعة</h3>
                    <div class="flex items-center gap-3">
                        @php
                            $statusMeta = $statusBadges[$payment->status] ?? null;
                        @endphp
                        @if($statusMeta)
                            <span class="inline-flex items-center gap-2 rounded-full px-3 py-1 text-xs font-semibold {{ $statusMeta['classes'] }}">
                                <span class="h-2 w-2 rounded-full bg-current"></span>
                                {{ $statusMeta['label'] }}
                            </span>
                        @endif
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-4">
                        <div class="flex items-center justify-between py-2 border-b border-slate-100">
                            <span class="text-sm text-slate-500">المبلغ</span>
                            <span class="text-2xl font-black text-emerald-600">
                                {{ number_format($payment->amount, 2) }} ج.م
                            </span>
                        </div>
                        <div class="flex items-center justify-between py-2 border-b border-slate-100">
                            <span class="text-sm text-slate-500">العملة</span>
                            <span class="text-sm font-semibold text-slate-900">{{ $payment->currency ?? 'EGP' }}</span>
                        </div>
                        <div class="flex items-center justify-between py-2 border-b border-slate-100">
                            <span class="text-sm text-slate-500">طريقة الدفع</span>
                            <span class="inline-flex items-center gap-2 rounded-full px-3 py-1 text-xs font-semibold bg-slate-100 text-slate-700">
                                <i class="{{ $paymentMethodLabels[$payment->payment_method]['icon'] ?? 'fas fa-ellipsis-h' }} text-[10px]"></i>
                                {{ $paymentMethodLabels[$payment->payment_method]['label'] ?? $payment->payment_method }}
                            </span>
                        </div>
                    </div>
                    <div class="space-y-4">
                        <div class="flex items-center justify-between py-2 border-b border-slate-100">
                            <span class="text-sm text-slate-500">تاريخ الإنشاء</span>
                            <span class="text-sm font-semibold text-slate-900">{{ $payment->created_at->format('Y-m-d H:i') }}</span>
                        </div>
                        @if($payment->paid_at)
                        <div class="flex items-center justify-between py-2 border-b border-slate-100">
                            <span class="text-sm text-slate-500">تاريخ الدفع</span>
                            <span class="text-sm font-semibold text-slate-900">{{ $payment->paid_at->format('Y-m-d H:i') }}</span>
                        </div>
                        @endif
                        @if($payment->processedBy)
                        <div class="flex items-center justify-between py-2 border-b border-slate-100">
                            <span class="text-sm text-slate-500">معالج بواسطة</span>
                            <span class="text-sm font-semibold text-slate-900">{{ $payment->processedBy->name }}</span>
                        </div>
                        @endif
                        @if($payment->payment_gateway)
                        <div class="flex items-center justify-between py-2 border-b border-slate-100">
                            <span class="text-sm text-slate-500">بوابة الدفع</span>
                            <span class="text-sm font-semibold text-slate-900">{{ $payment->payment_gateway }}</span>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- معلومات العميل -->
            @if($payment->user)
            <div class="rounded-2xl border border-slate-200 bg-slate-50/60 p-6">
                <h3 class="text-base font-semibold text-slate-900 mb-4">بيانات العميل</h3>
                <dl class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    <div class="flex justify-between gap-6">
                        <dt class="text-slate-500">الاسم</dt>
                        <dd class="font-semibold text-slate-900">{{ $payment->user->name ?? 'غير معروف' }}</dd>
                    </div>
                    <div class="flex justify-between gap-6">
                        <dt class="text-slate-500">رقم الهاتف</dt>
                        <dd class="font-semibold text-slate-900">{{ $payment->user->phone ?? '—' }}</dd>
                    </div>
                    @if($payment->user->email)
                    <div class="flex justify-between gap-6">
                        <dt class="text-slate-500">البريد الإلكتروني</dt>
                        <dd class="font-semibold text-slate-900">{{ $payment->user->email }}</dd>
                    </div>
                    @endif
                    <div class="flex justify-between gap-6">
                        <dt class="text-slate-500">الدور</dt>
                        <dd class="font-semibold text-slate-900">
                            @if($payment->user->role == 'student') طالب
                            @elseif($payment->user->role == 'instructor') مدرب
                            @elseif($payment->user->role == 'admin') إداري
                            @else {{ $payment->user->role }}
                            @endif
                        </dd>
                    </div>
                </dl>
            </div>
            @endif

            <!-- الروابط المرتبطة -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @if($payment->invoice)
                <div class="rounded-2xl border border-slate-200 bg-white/85 p-6">
                    <h3 class="text-base font-semibold text-slate-900 mb-4">الفاتورة المرتبطة</h3>
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-slate-500">رقم الفاتورة</span>
                            <a href="{{ route('admin.invoices.show', $payment->invoice) }}" class="text-sm font-semibold text-sky-600 hover:text-sky-700 transition-colors">
                                {{ $payment->invoice->invoice_number }}
                                <i class="fas fa-external-link-alt text-xs mr-1"></i>
                            </a>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-slate-500">المبلغ الإجمالي</span>
                            <span class="text-sm font-semibold text-slate-900">{{ number_format($payment->invoice->total_amount, 2) }} ج.م</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-slate-500">الحالة</span>
                            <span class="text-xs font-semibold px-2.5 py-1 rounded-full
                                @if($payment->invoice->status == 'paid') bg-emerald-100 text-emerald-700
                                @elseif($payment->invoice->status == 'pending') bg-amber-100 text-amber-700
                                @else bg-rose-100 text-rose-700
                                @endif">
                                {{ $payment->invoice->status == 'paid' ? 'مدفوعة' : ($payment->invoice->status == 'pending' ? 'معلقة' : 'متأخرة') }}
                            </span>
                        </div>
                    </div>
                </div>
                @endif

                @if($payment->transactions && $payment->transactions->count() > 0)
                <div class="rounded-2xl border border-slate-200 bg-white/85 p-6">
                    <h3 class="text-base font-semibold text-slate-900 mb-4">المعاملات المرتبطة</h3>
                    <div class="space-y-2">
                        @foreach($payment->transactions->take(3) as $transaction)
                        <div class="flex items-center justify-between py-2 border-b border-slate-100 last:border-0">
                            <a href="{{ route('admin.transactions.show', $transaction) }}" class="text-sm font-semibold text-sky-600 hover:text-sky-700 transition-colors">
                                {{ $transaction->transaction_number ?? 'N/A' }}
                            </a>
                            <span class="text-sm text-slate-500">{{ number_format($transaction->amount, 2) }} ج.م</span>
                        </div>
                        @endforeach
                        @if($payment->transactions->count() > 3)
                        <p class="text-xs text-slate-500 mt-2">و {{ $payment->transactions->count() - 3 }} معاملة أخرى</p>
                        @endif
                    </div>
                </div>
                @endif
            </div>

            <!-- معلومات إضافية -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @if($payment->reference_number)
                <div class="rounded-2xl border border-slate-200 bg-white/85 p-6">
                    <h3 class="text-base font-semibold text-slate-900 mb-3">رقم المرجع</h3>
                    <p class="text-sm font-mono text-slate-600">{{ $payment->reference_number }}</p>
                </div>
                @endif

                @if($payment->transaction_id)
                <div class="rounded-2xl border border-slate-200 bg-white/85 p-6">
                    <h3 class="text-base font-semibold text-slate-900 mb-3">رقم المعاملة</h3>
                    <p class="text-sm font-mono text-slate-600">{{ $payment->transaction_id }}</p>
                </div>
                @endif
            </div>

            <!-- الملاحظات -->
            @if($payment->notes)
            <div class="rounded-2xl border border-slate-200 bg-white/85 p-6">
                <h3 class="text-base font-semibold text-slate-900 mb-3">ملاحظات</h3>
                <p class="text-sm text-slate-600 leading-relaxed">{{ $payment->notes }}</p>
            </div>
            @endif
        </div>
    </section>
</div>
@endsection

