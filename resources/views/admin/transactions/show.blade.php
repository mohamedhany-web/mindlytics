@extends('layouts.admin')

@section('title', 'تفاصيل المعاملة المالية - Mindlytics')
@section('header', 'تفاصيل المعاملة المالية')

@section('content')
@php
    $statusBadges = [
        'completed' => ['label' => 'مكتملة', 'classes' => 'bg-emerald-100 text-emerald-700'],
        'pending' => ['label' => 'معلقة', 'classes' => 'bg-amber-100 text-amber-700'],
        'failed' => ['label' => 'فاشلة', 'classes' => 'bg-rose-100 text-rose-700'],
        'cancelled' => ['label' => 'ملغاة', 'classes' => 'bg-slate-100 text-slate-700'],
        'reversed' => ['label' => 'مستردة', 'classes' => 'bg-orange-100 text-orange-700'],
    ];

    $typeBadges = [
        'income' => ['label' => 'إيراد', 'classes' => 'bg-emerald-100 text-emerald-700', 'icon' => 'fas fa-arrow-down'],
        'expense' => ['label' => 'مصروف', 'classes' => 'bg-rose-100 text-rose-700', 'icon' => 'fas fa-arrow-up'],
        'transfer' => ['label' => 'تحويل', 'classes' => 'bg-blue-100 text-blue-700', 'icon' => 'fas fa-exchange-alt'],
        'refund' => ['label' => 'استرداد', 'classes' => 'bg-orange-100 text-orange-700', 'icon' => 'fas fa-undo'],
    ];
@endphp

<div class="space-y-10">
    @if(session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">{{ session('error') }}</div>
    @endif
    @if($errors->any())
        <div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">
            <ul class="list-disc list-inside space-y-1">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    @if($needsWalletSync ?? false)
        <div class="rounded-2xl border-2 border-amber-400 bg-amber-50 px-5 py-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <p class="text-base font-black text-amber-900 flex items-center gap-2">
                    <i class="fas fa-exclamation-triangle"></i>
                    استرداد بدون سحب من المحفظة
                </p>
                <p class="text-sm text-amber-800 mt-1">
                    المعاملة مُعلَّمة كمستردة لكن المبلغ لم يُخصم بعد من المحفظة/الحساب. استخدم الزر لإتمام السحب.
                </p>
            </div>
            <button type="button" onclick="document.getElementById('wallet-sync-form')?.scrollIntoView({behavior:'smooth'})"
                    class="inline-flex items-center justify-center gap-2 bg-amber-600 hover:bg-amber-700 text-white px-5 py-2.5 rounded-xl text-sm font-bold whitespace-nowrap">
                <i class="fas fa-wallet"></i>
                سحب الفلوس من المحفظة
            </button>
        </div>
    @endif

    <section class="rounded-3xl bg-white/95 backdrop-blur border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-5 py-6 sm:px-8 lg:px-12 flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-widest text-slate-400">معاملة رقم</p>
                <h2 class="mt-1 text-2xl font-bold text-slate-900">#{{ $transaction->transaction_number ?? $transaction->id }}</h2>
                <p class="text-sm text-slate-500 mt-1">أُنشئت في {{ $transaction->created_at->format('Y-m-d H:i') }}</p>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                @if($needsWalletSync ?? false)
                    <a href="#wallet-sync-form" class="inline-flex items-center gap-2 rounded-2xl bg-amber-600 hover:bg-amber-700 px-4 py-2.5 text-sm font-bold text-white shadow-lg shadow-amber-500/30 transition">
                        <i class="fas fa-wallet"></i>
                        سحب من المحفظة
                    </a>
                @endif
                <a href="{{ route('admin.transactions.edit', $transaction) }}" class="inline-flex items-center gap-2 rounded-2xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-600 transition hover:border-sky-300 hover:text-sky-600">
                    <i class="fas fa-edit"></i>
                    تعديل المعاملة
                </a>
                <a href="{{ route('admin.transactions.index') }}" class="inline-flex items-center gap-2 rounded-2xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-slate-50">
                    <i class="fas fa-arrow-right"></i>
                    العودة
                </a>
            </div>
        </div>

        <div class="px-5 py-6 sm:px-8 lg:px-12 space-y-6">
            <!-- معلومات المعاملة الرئيسية -->
            <div class="rounded-2xl border border-slate-200 bg-white/85 p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-bold text-slate-900">معلومات المعاملة</h3>
                    <div class="flex items-center gap-3">
                        @php
                            $typeMeta = $typeBadges[$transaction->type] ?? null;
                            $statusMeta = $statusBadges[$transaction->status] ?? null;
                        @endphp
                        @if($typeMeta)
                            <span class="inline-flex items-center gap-2 rounded-full px-3 py-1 text-xs font-semibold {{ $typeMeta['classes'] }}">
                                <i class="{{ $typeMeta['icon'] }} text-[10px]"></i>
                                {{ $typeMeta['label'] }}
                            </span>
                        @endif
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
                            <span class="text-2xl font-black {{ $transaction->type == 'income' ? 'text-emerald-600' : ($transaction->type == 'expense' ? 'text-rose-600' : 'text-slate-900') }}">
                                {{ $transaction->type == 'expense' ? '-' : '+' }}{{ number_format($transaction->amount, 2) }} ج.م
                            </span>
                        </div>
                        <div class="flex items-center justify-between py-2 border-b border-slate-100">
                            <span class="text-sm text-slate-500">العملة</span>
                            <span class="text-sm font-semibold text-slate-900">{{ $transaction->currency ?? 'EGP' }}</span>
                        </div>
                        <div class="flex items-center justify-between py-2 border-b border-slate-100">
                            <span class="text-sm text-slate-500">الفئة</span>
                            <span class="text-sm font-semibold text-slate-900">{{ $transaction->category ?? 'غير محدد' }}</span>
                        </div>
                    </div>
                    <div class="space-y-4">
                        <div class="flex items-center justify-between py-2 border-b border-slate-100">
                            <span class="text-sm text-slate-500">تاريخ الإنشاء</span>
                            <span class="text-sm font-semibold text-slate-900">{{ $transaction->created_at->format('Y-m-d H:i') }}</span>
                        </div>
                        <div class="flex items-center justify-between py-2 border-b border-slate-100">
                            <span class="text-sm text-slate-500">آخر تحديث</span>
                            <span class="text-sm font-semibold text-slate-900">{{ $transaction->updated_at->format('Y-m-d H:i') }}</span>
                        </div>
                        @if($transaction->createdBy)
                        <div class="flex items-center justify-between py-2 border-b border-slate-100">
                            <span class="text-sm text-slate-500">أنشأها</span>
                            <span class="text-sm font-semibold text-slate-900">{{ $transaction->createdBy->name }}</span>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- معلومات العميل -->
            @if($transaction->user)
            <div class="rounded-2xl border border-slate-200 bg-slate-50/60 p-6">
                <h3 class="text-base font-semibold text-slate-900 mb-4">بيانات العميل</h3>
                <dl class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    <div class="flex justify-between gap-6">
                        <dt class="text-slate-500">الاسم</dt>
                        <dd class="font-semibold text-slate-900">{{ $transaction->user->name ?? 'غير معروف' }}</dd>
                    </div>
                    <div class="flex justify-between gap-6">
                        <dt class="text-slate-500">رقم الهاتف</dt>
                        <dd class="font-semibold text-slate-900">{{ $transaction->user->phone ?? '—' }}</dd>
                    </div>
                    @if($transaction->user->email)
                    <div class="flex justify-between gap-6">
                        <dt class="text-slate-500">البريد الإلكتروني</dt>
                        <dd class="font-semibold text-slate-900">{{ $transaction->user->email }}</dd>
                    </div>
                    @endif
                    <div class="flex justify-between gap-6">
                        <dt class="text-slate-500">الدور</dt>
                        <dd class="font-semibold text-slate-900">
                            @if($transaction->user->role == 'student') طالب
                            @elseif($transaction->user->role == 'instructor') مدرب
                            @elseif($transaction->user->role == 'admin') إداري
                            @else {{ $transaction->user->role }}
                            @endif
                        </dd>
                    </div>
                </dl>
            </div>
            @endif

            <!-- الروابط المرتبطة -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @if($transaction->payment)
                <div class="rounded-2xl border border-slate-200 bg-white/85 p-6">
                    <h3 class="text-base font-semibold text-slate-900 mb-4">الدفعة المرتبطة</h3>
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-slate-500">رقم الدفعة</span>
                            <a href="{{ route('admin.payments.show', $transaction->payment) }}" class="text-sm font-semibold text-sky-600 hover:text-sky-700 transition-colors">
                                {{ $transaction->payment->payment_number }}
                                <i class="fas fa-external-link-alt text-xs mr-1"></i>
                            </a>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-slate-500">المبلغ</span>
                            <span class="text-sm font-semibold text-slate-900">{{ number_format($transaction->payment->amount, 2) }} ج.م</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-slate-500">طريقة الدفع</span>
                            <span class="text-sm font-semibold text-slate-900">{{ $transaction->payment->payment_method ?? '—' }}</span>
                        </div>
                    </div>
                </div>
                @endif

                @if($transaction->invoice)
                <div class="rounded-2xl border border-slate-200 bg-white/85 p-6">
                    <h3 class="text-base font-semibold text-slate-900 mb-4">الفاتورة المرتبطة</h3>
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-slate-500">رقم الفاتورة</span>
                            <a href="{{ route('admin.invoices.show', $transaction->invoice) }}" class="text-sm font-semibold text-sky-600 hover:text-sky-700 transition-colors">
                                {{ $transaction->invoice->invoice_number }}
                                <i class="fas fa-external-link-alt text-xs mr-1"></i>
                            </a>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-slate-500">المبلغ الإجمالي</span>
                            <span class="text-sm font-semibold text-slate-900">{{ number_format($transaction->invoice->total_amount, 2) }} ج.م</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-slate-500">الحالة</span>
                            <span class="text-xs font-semibold px-2.5 py-1 rounded-full
                                @if($transaction->invoice->status == 'paid') bg-emerald-100 text-emerald-700
                                @elseif($transaction->invoice->status == 'pending') bg-amber-100 text-amber-700
                                @else bg-rose-100 text-rose-700
                                @endif">
                                {{ $transaction->invoice->status == 'paid' ? 'مدفوعة' : ($transaction->invoice->status == 'pending' ? 'معلقة' : 'متأخرة') }}
                            </span>
                        </div>
                    </div>
                </div>
                @endif
            </div>

            <!-- الوصف -->
            @if($transaction->description)
            <div class="rounded-2xl border border-slate-200 bg-white/85 p-6">
                <h3 class="text-base font-semibold text-slate-900 mb-3">الوصف</h3>
                <p class="text-sm text-slate-600 leading-relaxed">{{ $transaction->description }}</p>
            </div>
            @endif

            <!-- البيانات الإضافية -->
            @if($transaction->metadata && is_array($transaction->metadata) && count($transaction->metadata) > 0)
            <div class="rounded-2xl border border-slate-200 bg-white/85 p-6">
                <h3 class="text-base font-semibold text-slate-900 mb-4">البيانات الإضافية</h3>
                <dl class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    @foreach($transaction->metadata as $key => $value)
                    <div class="flex justify-between gap-6">
                        <dt class="text-slate-500">{{ $key }}</dt>
                        <dd class="font-semibold text-slate-900">{{ is_array($value) ? json_encode($value, JSON_UNESCAPED_UNICODE) : $value }}</dd>
                    </div>
                    @endforeach
                </dl>
            </div>
            @endif
        </div>
    </section>

    @if($needsWalletSync ?? false)
    @php
        $syncAmount = (float) (is_array($transaction->metadata) ? ($transaction->metadata['refund_amount'] ?? $transaction->amount) : $transaction->amount);
    @endphp
    <section id="wallet-sync-form" class="rounded-3xl bg-amber-50 border-2 border-amber-400 shadow-lg p-6 sm:p-8">
        <h3 class="text-lg font-black text-amber-900 mb-1 flex items-center gap-2">
            <i class="fas fa-wallet"></i>
            إعادة سحب مبلغ الاسترداد من المحفظة
        </h3>
        <p class="text-sm text-amber-800 mb-5">
            اضغط لتنفيذ السحب الذي لم يتم عند الاسترداد. المبلغ المستهدف:
            <strong class="tabular-nums">{{ number_format($syncAmount, 2) }} ج.م</strong>
        </p>
        <form action="{{ route('admin.transactions.sync-wallet-withdrawal', $transaction) }}" method="POST" class="space-y-4"
              onsubmit="return confirm('تأكيد سحب {{ number_format($syncAmount, 2) }} ج.م من المحفظة المحددة؟');">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-amber-900 mb-1">المحفظة / الحساب</label>
                    <select name="wallet_id" class="w-full rounded-xl border border-amber-300 bg-white px-4 py-2.5 text-sm focus:ring-2 focus:ring-amber-500">
                        <option value="">تلقائي (من سجل الإيداع)</option>
                        @foreach($academyWallets ?? [] as $wallet)
                            <option value="{{ $wallet->id }}" @selected((int) old('wallet_id', $suggestedWalletId ?? 0) === (int) $wallet->id)>
                                {{ $wallet->name ?? ('محفظة #' . $wallet->id) }}
                                — {{ \App\Models\Wallet::typeLabel($wallet->type) }}
                                (رصيد: {{ number_format((float) $wallet->balance, 2) }} ج.م)
                            </option>
                        @endforeach
                    </select>
                    <p class="text-xs text-amber-700 mt-1">إذا لم يُكتشف الحساب تلقائياً، اختر المحفظة التي نزل فيها المبلغ.</p>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-amber-900 mb-1">المبلغ المراد سحبه</label>
                    <input type="number" name="amount" step="0.01" min="0.01" max="{{ $transaction->amount }}"
                           value="{{ old('amount', $syncAmount) }}"
                           class="w-full rounded-xl border border-amber-300 bg-white px-4 py-2.5 text-sm focus:ring-2 focus:ring-amber-500">
                </div>
            </div>
            <button type="submit" class="inline-flex items-center gap-2 bg-amber-600 hover:bg-amber-700 text-white px-6 py-3 rounded-xl text-sm font-bold shadow-lg shadow-amber-500/30">
                <i class="fas fa-hand-holding-usd"></i>
                تنفيذ السحب الآن
            </button>
        </form>
    </section>
    @endif

    @if($canRefund ?? false)
    <section class="rounded-3xl bg-white border border-orange-200 shadow-lg p-6 sm:p-8">
        <h3 class="text-lg font-bold text-slate-900 mb-2">تنفيذ الاسترداد</h3>
        <p class="text-sm text-slate-600 mb-4">
            عند الاسترداد يُسحب المبلغ من المحفظة/الحساب الذي أُودع فيه، وتُنشأ معاملة استرداد، ويُحدَّث سجل الدفعة والفاتورة.
        </p>
        <form action="{{ route('admin.transactions.refund', $transaction) }}" method="POST" class="space-y-4" onsubmit="return confirm('تأكيد الاسترداد؟ سيتم سحب المبلغ من المحفظة المرتبطة.');">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">مبلغ الاسترداد</label>
                    <input type="number" name="amount" step="0.01" min="0.01" max="{{ $transaction->amount }}" value="{{ $transaction->amount }}"
                           class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-orange-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">سبب / ملاحظات</label>
                    <input type="text" name="description" value="استرداد معاملة: {{ $transaction->transaction_number ?? $transaction->id }}"
                           class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-orange-500">
                </div>
            </div>
            <button type="submit" class="inline-flex items-center gap-2 bg-orange-600 hover:bg-orange-700 text-white px-5 py-2.5 rounded-xl text-sm font-semibold">
                <i class="fas fa-undo"></i>
                تنفيذ الاسترداد
            </button>
        </form>
    </section>
    @endif
</div>
@endsection

