@extends('layouts.student-dashboard')

@section('title', 'تفاصيل الفاتورة')
@section('header', 'تفاصيل الفاتورة')

@section('content')
@php
    $wallets = $wallets ?? collect();
    $platformPaymentMode = $platformPaymentMode ?? 'kashier';
@endphp
<div class="w-full max-w-5xl mx-auto space-y-6 px-2 sm:px-0">
    @if(session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 text-emerald-900 px-4 py-3 text-sm font-semibold">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="rounded-xl border border-red-200 bg-red-50 text-red-900 px-4 py-3 text-sm font-semibold">{{ session('error') }}</div>
    @endif
    @if(session('info'))
        <div class="rounded-xl border border-amber-200 bg-amber-50 text-amber-900 px-4 py-3 text-sm font-semibold">{{ session('info') }}</div>
    @endif
    @if($errors->any())
        <div class="rounded-xl border border-red-200 bg-red-50 text-red-800 px-4 py-3 text-sm">
            <ul class="list-disc list-inside">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
        <div class="mb-6">
            <a href="{{ route('student.invoices.index') }}" class="text-sky-600 hover:text-sky-900 mb-4 inline-block">
                <i class="fas fa-arrow-right mr-2"></i>رجوع إلى الفواتير
            </a>
            <h1 class="text-2xl font-bold text-gray-900">فاتورة #{{ $invoice->invoice_number }}</h1>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <h3 class="text-lg font-bold text-gray-900 mb-4">معلومات الفاتورة</h3>
                <div class="space-y-2 text-sm">
                    <div><span class="text-gray-600">النوع:</span> <span class="font-medium text-gray-900 mr-2">{{ $invoice->type }}</span></div>
                    <div><span class="text-gray-600">الحالة:</span> 
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            @if($invoice->status == 'paid') bg-green-100 text-green-800
                            @elseif($invoice->status == 'pending') bg-yellow-100 text-yellow-800
                            @else bg-red-100 text-red-800
                            @endif mr-2">
                            {{ $invoice->status == 'paid' ? 'مدفوعة' : ($invoice->status == 'pending' ? 'معلقة' : 'متأخرة') }}
                        </span>
                    </div>
                    <div><span class="text-gray-600">تاريخ الاستحقاق:</span> <span class="font-medium text-gray-900 mr-2">{{ $invoice->due_date ? $invoice->due_date->format('Y-m-d') : '-' }}</span></div>
                </div>
            </div>
            <div>
                <h3 class="text-lg font-bold text-gray-900 mb-4">تفاصيل المبلغ</h3>
                <div class="space-y-2">
                    <div class="flex justify-between">
                        <span class="text-gray-600">المبلغ الفرعي:</span>
                        <span class="font-medium text-gray-900">{{ number_format($invoice->subtotal, 2) }} ج.م</span>
                    </div>
                    @if($invoice->tax_amount > 0)
                    <div class="flex justify-between">
                        <span class="text-gray-600">الضريبة:</span>
                        <span class="font-medium text-gray-900">{{ number_format($invoice->tax_amount, 2) }} ج.م</span>
                    </div>
                    @endif
                    @if($invoice->discount_amount > 0)
                    <div class="flex justify-between">
                        <span class="text-gray-600">الخصم:</span>
                        <span class="font-medium text-red-600">-{{ number_format($invoice->discount_amount, 2) }} ج.م</span>
                    </div>
                    @endif
                    <div class="flex justify-between text-lg font-bold border-t border-gray-200 pt-2 mt-2">
                        <span class="text-gray-900">المبلغ الإجمالي:</span>
                        <span class="text-sky-600">{{ number_format($invoice->total_amount, 2) }} ج.م</span>
                    </div>
                </div>
            </div>
        </div>

        @if($invoice->payments && $invoice->payments->count() > 0)
        <div class="border-t border-gray-200 pt-6 mt-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4">المدفوعات</h3>
            <div class="space-y-2">
                @foreach($invoice->payments as $payment)
                <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg gap-3">
                    <div>
                        <div class="font-medium text-gray-900">{{ $payment->payment_number }}</div>
                        <div class="text-sm text-gray-600">{{ $payment->paid_at ? $payment->paid_at->format('Y-m-d') : '—' }}</div>
                        <span class="inline-block mt-1 text-xs px-2 py-0.5 rounded-full {{ $payment->status === 'completed' ? 'bg-green-100 text-green-800' : ($payment->status === 'pending' ? 'bg-amber-100 text-amber-900' : 'bg-gray-200 text-gray-700') }}">{{ $payment->status }}</span>
                    </div>
                    <div class="font-medium text-green-600 tabular-nums">{{ number_format($payment->amount, 2) }} ج.م</div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        @if($invoice->description)
        <div class="border-t border-gray-200 pt-6 mt-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4">الوصف</h3>
            <p class="text-gray-600">{{ $invoice->description }}</p>
        </div>
        @endif

        @if(! $invoice->isPaid() && (float) $invoice->remaining_amount > 0)
            <div class="border-t border-gray-200 pt-8 mt-8">
                <h3 class="text-lg font-bold text-gray-900 mb-2">سداد الفاتورة</h3>
                <p class="text-sm text-gray-600 mb-4">المتبقي: <span class="font-black text-sky-700 tabular-nums">{{ number_format($invoice->remaining_amount, 2) }} ج.م</span></p>

                @if($platformPaymentMode === 'manual')
                    @if($wallets->isEmpty())
                        <p class="text-sm text-amber-800 bg-amber-50 border border-amber-200 rounded-lg p-4">لا تتوفر بيانات تحويل حالياً. تواصل مع الإدارة.</p>
                    @else
                        <div class="mb-4 space-y-2">
                            @foreach($wallets as $w)
                                <div class="text-sm p-3 bg-slate-50 rounded-lg border border-slate-200">
                                    <span class="font-bold text-gray-900">{{ $w->name }}</span>
                                    <span class="text-gray-500"> — {{ \App\Models\Wallet::typeLabel($w->type) }}</span>
                                    @if($w->account_number)
                                        <div class="font-mono text-gray-800 mt-1">{{ $w->account_number }}</div>
                                    @endif
                                    @if($w->notes)
                                        <p class="text-gray-600 text-xs mt-1">{{ $w->notes }}</p>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                        <form method="post" action="{{ route('student.invoices.payment-proof', $invoice) }}" enctype="multipart/form-data" class="space-y-4 max-w-xl">
                            @csrf
                            <div>
                                <label class="block text-sm font-bold text-gray-800 mb-1">المبلغ المراد تسجيله</label>
                                <input type="number" name="amount" step="0.01" min="0.01" max="{{ $invoice->remaining_amount }}" value="{{ old('amount', $invoice->remaining_amount) }}" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-800 mb-1">طريقة الدفع</label>
                                <select name="payment_method" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm" required>
                                    <option value="bank_transfer" @selected(old('payment_method') === 'bank_transfer')>تحويل بنكي</option>
                                    <option value="wallet" @selected(old('payment_method') === 'wallet')>محفظة إلكترونية</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-800 mb-1">المحفظة (عند اختيار محفظة)</label>
                                <select name="wallet_id" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                                    <option value="">—</option>
                                    @foreach($wallets as $w)
                                        <option value="{{ $w->id }}" @selected((string) old('wallet_id') === (string) $w->id)>{{ $w->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-800 mb-1">صورة الإيصال</label>
                                <input type="file" name="payment_proof" accept="image/jpeg,image/png,image/jpg" required class="block w-full text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-800 mb-1">ملاحظات</label>
                                <textarea name="notes" rows="2" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">{{ old('notes') }}</textarea>
                            </div>
                            <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-sky-600 text-white text-sm font-bold hover:bg-sky-700">إرسال الإيصال للمراجعة</button>
                        </form>
                    @endif
                @elseif($platformPaymentMode === 'fawaterak')
                    <div class="rounded-xl border-2 border-dashed border-indigo-200 bg-indigo-50 p-6 text-center text-indigo-950">
                        <i class="fas fa-receipt text-2xl mb-2"></i>
                        <p class="font-bold">فواتيرك — الكورسات</p>
                        <p class="text-sm mt-2 text-indigo-900/90">سداد فواتير الطلاب عبر واجهة فواتيرك مرتبط حالياً بمسار شراء الكورس من صفحة إتمام الطلب. للتحويل اليدوي يمكن للإدارة تفعيل «دفع يدوي» من إعدادات النظام.</p>
                    </div>
                @else
                    <div class="rounded-xl border border-sky-200 bg-sky-50 p-4 text-sm text-sky-900">
                        الدفع الإلكتروني للفواتير عبر البوابة يتم عادة من قبل الإدارة أو عبر روابط الدفع عند توفرها. إن احتجت سداداً يدوياً، يمكن للإدارة تفعيل وضع «دفع يدوي» من إعدادات النظام.
                    </div>
                @endif
            </div>
        @endif
    </div>
</div>
@endsection

