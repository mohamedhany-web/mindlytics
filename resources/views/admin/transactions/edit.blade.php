@extends('layouts.admin')

@section('title', 'تعديل المعاملة')
@section('header', 'تعديل المعاملة')

@section('content')
@php
    $isReversed = $transaction->status === 'reversed';
    $metadata = is_array($transaction->metadata) ? $transaction->metadata : [];
    $wasRefunded = $isReversed || !empty($metadata['refunded_at']);
    $typeLabels = [
        'credit' => 'إيراد',
        'debit' => 'مصروف / استرداد',
    ];
@endphp
<div class="space-y-6">
    @if(session('error'))
        <div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">{{ session('error') }}</div>
    @endif

    @if($errors->any())
        <div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
        <h1 class="text-2xl font-bold text-gray-900 mb-2">تعديل المعاملة</h1>
        <p class="text-sm text-gray-500 mb-6">
            رقم المعاملة: {{ $transaction->transaction_number ?? ('#' . $transaction->id) }}
            — النوع الحالي: {{ $typeLabels[$transaction->type] ?? $transaction->type }}
        </p>

        @if($wasRefunded)
            <div class="mb-6 rounded-xl border border-orange-200 bg-orange-50 px-4 py-3 text-sm text-orange-900">
                <i class="fas fa-undo ml-1"></i>
                تم استرداد هذه المعاملة
                @if(!empty($metadata['refunded_at']))
                    في {{ \Illuminate\Support\Carbon::parse($metadata['refunded_at'])->format('Y-m-d H:i') }}
                @endif
            </div>
        @endif

        <form action="{{ route('admin.transactions.update', $transaction) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">العميل *</label>
                    <select name="user_id" required @disabled($wasRefunded) class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500 disabled:bg-gray-100">
                        @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ (int) old('user_id', $transaction->user_id) === (int) $user->id ? 'selected' : '' }}>{{ $user->name }} - {{ $user->phone }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">النوع *</label>
                    <select name="type" required @disabled($wasRefunded) class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500 disabled:bg-gray-100">
                        <option value="credit" {{ old('type', $transaction->type) === 'credit' ? 'selected' : '' }}>إيراد</option>
                        <option value="debit" {{ old('type', $transaction->type) === 'debit' ? 'selected' : '' }}>مصروف</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">المبلغ *</label>
                    <input type="number" name="amount" step="0.01" min="0" required value="{{ old('amount', $transaction->amount) }}"
                           @disabled($wasRefunded)
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500 disabled:bg-gray-100">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">الحالة *</label>
                    <select name="status" required @disabled($wasRefunded) class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500 disabled:bg-gray-100">
                        <option value="pending" {{ old('status', $transaction->status) === 'pending' ? 'selected' : '' }}>معلقة</option>
                        <option value="completed" {{ old('status', $transaction->status) === 'completed' ? 'selected' : '' }}>مكتملة</option>
                        <option value="cancelled" {{ old('status', $transaction->status) === 'cancelled' ? 'selected' : '' }}>ملغاة</option>
                        @if($canRefund)
                        <option value="reversed" {{ old('status', $transaction->status) === 'reversed' ? 'selected' : '' }}>مستردة (سحب من المحفظة + استرداد كامل)</option>
                        @endif
                    </select>
                    @if($canRefund)
                        <p class="text-xs text-orange-700 mt-1">اختيار «مستردة» يُنفّذ الاسترداد الكامل ويسحب المبلغ من المحفظة المرتبطة.</p>
                    @endif
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">الوصف</label>
                <textarea name="description" rows="3" @disabled($wasRefunded) class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500 disabled:bg-gray-100">{{ old('description', $transaction->description) }}</textarea>
            </div>

            @unless($wasRefunded)
            <div class="flex gap-4">
                <button type="submit" class="bg-gradient-to-r from-sky-600 to-sky-700 hover:from-sky-700 hover:to-sky-800 text-white px-6 py-3 rounded-lg font-medium transition-colors shadow-lg shadow-sky-500/30">
                    تحديث المعاملة
                </button>
                <a href="{{ route('admin.transactions.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-6 py-3 rounded-lg font-medium transition-colors">
                    إلغاء
                </a>
            </div>
            @endunless
        </form>
    </div>

    @if($canRefund)
    <div class="bg-white rounded-xl shadow-lg p-6 border border-orange-200">
        <h2 class="text-lg font-bold text-gray-900 mb-2">تنفيذ الاسترداد</h2>
        <p class="text-sm text-gray-600 mb-4">
            عند تنفيذ الاسترداد سيتم:
        </p>
        <ul class="text-sm text-gray-600 list-disc list-inside space-y-1 mb-6">
            <li>إرجاع المبلغ للعميل محاسبياً (معاملة استرداد جديدة)</li>
            <li>سحب المبلغ من المحفظة التي أُودع فيها (إن وُجدت)</li>
            <li>تحديث حالة الدفعة والفاتورة المرتبطة</li>
            <li>تحديث مبلغ التسجيل في الكورس الأوفلاين (إن وُجد)</li>
        </ul>

        <form action="{{ route('admin.transactions.refund', $transaction) }}" method="POST" class="space-y-4" onsubmit="return confirm('هل أنت متأكد من تنفيذ الاسترداد؟ سيتم سحب المبلغ من المحفظة المرتبطة.');">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">مبلغ الاسترداد</label>
                    <input type="number" name="amount" step="0.01" min="0.01" max="{{ $transaction->amount }}" value="{{ old('amount', $transaction->amount) }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                    <p class="text-xs text-gray-500 mt-1">الحد الأقصى: {{ number_format($transaction->amount, 2) }} ج.م</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">سبب / ملاحظات الاسترداد</label>
                    <input type="text" name="description" value="{{ old('description', 'استرداد معاملة: ' . ($transaction->transaction_number ?? $transaction->id)) }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                </div>
            </div>
            <button type="submit" class="inline-flex items-center gap-2 bg-gradient-to-r from-orange-600 to-orange-700 hover:from-orange-700 hover:to-orange-800 text-white px-6 py-3 rounded-lg font-medium transition-colors shadow-lg shadow-orange-500/30">
                <i class="fas fa-undo"></i>
                تنفيذ الاسترداد
            </button>
        </form>
    </div>
    @endif

    @if($needsWalletSync ?? false)
    @php
        $syncAmount = (float) (is_array($transaction->metadata) ? ($transaction->metadata['refund_amount'] ?? $transaction->amount) : $transaction->amount);
    @endphp
    <div id="wallet-sync-form" class="bg-amber-50 rounded-xl shadow-lg p-6 border-2 border-amber-400">
        <h2 class="text-lg font-black text-amber-900 mb-1 flex items-center gap-2">
            <i class="fas fa-wallet"></i>
            إعادة سحب مبلغ الاسترداد من المحفظة
        </h2>
        <p class="text-sm text-amber-800 mb-4">
            المعاملة مستردة لكن المبلغ لم يُسحب من المحفظة. المبلغ:
            <strong>{{ number_format($syncAmount, 2) }} ج.م</strong>
        </p>
        <form action="{{ route('admin.transactions.sync-wallet-withdrawal', $transaction) }}" method="POST" class="space-y-4"
              onsubmit="return confirm('تأكيد سحب المبلغ من المحفظة؟');">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-amber-900 mb-1">المحفظة / الحساب</label>
                    <select name="wallet_id" class="w-full px-4 py-3 border border-amber-300 rounded-lg bg-white focus:ring-2 focus:ring-amber-500">
                        <option value="">تلقائي (من سجل الإيداع)</option>
                        @foreach($academyWallets ?? [] as $wallet)
                            <option value="{{ $wallet->id }}" @selected((int) old('wallet_id', $suggestedWalletId ?? 0) === (int) $wallet->id)>
                                {{ $wallet->name ?? ('محفظة #' . $wallet->id) }}
                                — {{ \App\Models\Wallet::typeLabel($wallet->type) }}
                                ({{ number_format((float) $wallet->balance, 2) }} ج.م)
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-amber-900 mb-1">المبلغ</label>
                    <input type="number" name="amount" step="0.01" min="0.01" max="{{ $transaction->amount }}"
                           value="{{ old('amount', $syncAmount) }}"
                           class="w-full px-4 py-3 border border-amber-300 rounded-lg bg-white focus:ring-2 focus:ring-amber-500">
                </div>
            </div>
            <button type="submit" class="inline-flex items-center gap-2 bg-amber-600 hover:bg-amber-700 text-white px-6 py-3 rounded-lg font-bold">
                <i class="fas fa-hand-holding-usd"></i>
                تنفيذ السحب الآن
            </button>
        </form>
    </div>
    @endif
</div>
@endsection
