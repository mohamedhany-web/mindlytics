@extends('layouts.admin')

@section('title', 'إضافة دفعة جديدة')
@section('header', 'إضافة دفعة جديدة')

@section('content')
<div class="space-y-6">
    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
        <h1 class="text-2xl font-bold text-gray-900 mb-6">إضافة دفعة جديدة</h1>
        
        <form action="{{ route('admin.payments.store') }}" method="POST" class="space-y-6" id="payment-create-form">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">الفاتورة *</label>
                    <select name="invoice_id" id="payment-invoice-select" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                        @if($invoices->isEmpty())
                            <option value="" disabled selected>لا توجد فواتير مستحقة حاليًا</option>
                        @else
                            <option value="">اختر الفاتورة</option>
                            @foreach($invoices as $invoice)
                                @php
                                    $isCompany = $invoice->isCompanyClient();
                                    $clientLabel = $invoice->clientDisplayName();
                                @endphp
                                <option value="{{ $invoice->id }}"
                                        data-user-id="{{ $invoice->user_id ?? '' }}"
                                        data-client-type="{{ $isCompany ? 'company' : 'student' }}"
                                        data-client-label="{{ e($clientLabel) }}"
                                        data-remaining="{{ $invoice->remaining_amount }}">
                                    {{ $invoice->invoice_number }}
                                    · {{ $clientLabel }}{{ $isCompany ? ' (شركة)' : '' }}
                                    · متبقي {{ number_format($invoice->remaining_amount, 2) }} ج.م
                                </option>
                            @endforeach
                        @endif
                    </select>
                    @error('invoice_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    @if($invoices->isEmpty())
                        <p class="mt-2 text-xs text-amber-600">لا توجد فواتير بحاجة إلى دفع في الوقت الحالي.</p>
                    @endif
                </div>

                <div id="payment-student-block" class="md:col-span-2 space-y-2">
                    <label for="payment-client-search" class="block text-sm font-medium text-gray-700 mb-2">بحث عن طالب (اختياري)</label>
                    <input type="search"
                           id="payment-client-search"
                           autocomplete="off"
                           placeholder="البريد الإلكتروني، الاسم، أو الهاتف…"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500 placeholder:text-gray-400">
                    <label for="payment-user-select" class="block text-sm font-medium text-gray-700 mb-2">الطالب</label>
                    <select name="user_id" id="payment-user-select" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                        <option value="">يُحدَّد تلقائياً من الفاتورة</option>
                        @foreach($users as $user)
                        @php
                            $searchBlob = \Illuminate\Support\Str::lower(trim(implode(' ', array_filter([
                                $user->name ?? '',
                                $user->email ?? '',
                                $user->phone ?? '',
                            ]))));
                        @endphp
                        <option value="{{ $user->id }}" data-search="{{ e($searchBlob) }}">
                            {{ $user->name }} — {{ $user->email }}@if(!empty($user->phone)) — {{ $user->phone }}@endif
                        </option>
                        @endforeach
                    </select>
                    @error('user_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div id="payment-company-hint" class="md:col-span-2 hidden rounded-xl border border-indigo-100 bg-indigo-50 px-4 py-3 text-sm text-indigo-900">
                    <i class="fas fa-building ml-1"></i>
                    فاتورة جهة خارجية: <strong id="payment-company-name-label"></strong> — لا يلزم اختيار طالب.
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">المبلغ *</label>
                    <input type="number" name="amount" id="payment-amount" step="0.01" min="0" required value="{{ old('amount') }}" 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                    @error('amount')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">طريقة الدفع *</label>
                    <select name="payment_method" id="payment-method-select" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                        <option value="cash" {{ old('payment_method', 'cash') === 'cash' ? 'selected' : '' }}>نقدي</option>
                        <option value="card" {{ old('payment_method') === 'card' ? 'selected' : '' }}>بطاقة</option>
                        <option value="bank_transfer" {{ old('payment_method') === 'bank_transfer' ? 'selected' : '' }}>تحويل بنكي</option>
                        <option value="online" {{ old('payment_method') === 'online' ? 'selected' : '' }}>دفع إلكتروني</option>
                        <option value="wallet" {{ old('payment_method') === 'wallet' ? 'selected' : '' }}>محفظة</option>
                        <option value="other" {{ old('payment_method') === 'other' ? 'selected' : '' }}>أخرى</option>
                    </select>
                </div>

                <div id="payment-wallet-row" class="md:col-span-2 rounded-xl border border-sky-100 bg-sky-50/50 p-4 space-y-2" style="display: none;">
                    <label for="payment-wallet-select" class="block text-sm font-medium text-gray-800">المحفظة التي استلمت الدفعة *</label>
                    <p class="text-xs text-gray-600">يظهر <strong>الرصيد الحالي</strong> لكل محفظة؛ عند الحفظ يُضاف مبلغ الدفعة إلى رصيد المحفظة المختارة وتُسجَّل حركة إيداع.</p>
                    <select name="wallet_id" id="payment-wallet-select"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500 @error('wallet_id') border-red-500 @enderror">
                        <option value="">{{ $wallets->isEmpty() ? 'لا توجد محافظ مفعّلة' : 'اختر المحفظة' }}</option>
                        @foreach($wallets as $wallet)
                            <option value="{{ $wallet->id }}"
                                    data-balance="{{ $wallet->balance }}"
                                    {{ (string) old('wallet_id') === (string) $wallet->id ? 'selected' : '' }}>
                                {{ $wallet->name }} — {{ \App\Models\Wallet::typeLabel($wallet->type) }} — الرصيد {{ number_format($wallet->balance, 2) }} ج.م
                            </option>
                        @endforeach
                    </select>
                    @error('wallet_id')
                        <p class="text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p id="payment-wallet-balance-hint" class="hidden text-sm font-medium text-sky-800"></p>
                    @if($wallets->isEmpty())
                        <p class="text-sm text-amber-700">
                            لا توجد محافظ مفعّلة. أنشئ محفظة من
                            <a href="{{ route('admin.wallets.index') }}" class="font-semibold text-sky-700 underline">إدارة المحافظ</a>.
                        </p>
                    @endif
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">ملاحظات</label>
                <textarea name="notes" rows="3" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500">{{ old('notes') }}</textarea>
            </div>

            <div class="flex gap-4">
                <button type="submit" class="bg-gradient-to-r from-sky-600 to-sky-700 hover:from-sky-700 hover:to-sky-800 text-white px-6 py-3 rounded-lg font-medium transition-colors shadow-lg shadow-sky-500/30">
                    إضافة الدفعة
                </button>
                <a href="{{ route('admin.payments.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-6 py-3 rounded-lg font-medium transition-colors">
                    إلغاء
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    var search = document.getElementById('payment-client-search');
    var select = document.getElementById('payment-user-select');
    var invoiceSelect = document.getElementById('payment-invoice-select');
    var studentBlock = document.getElementById('payment-student-block');
    var companyHint = document.getElementById('payment-company-hint');
    var companyLabel = document.getElementById('payment-company-name-label');
    var amountInput = document.getElementById('payment-amount');

    var pool = select ? Array.prototype.slice.call(select.options, 1).map(function (o) {
        return {
            v: o.value,
            t: o.text,
            s: (o.getAttribute('data-search') || o.text || '').toLowerCase()
        };
    }) : [];

    function applyFilter() {
        if (!search || !select) return;
        var q = search.value.trim().toLowerCase().replace(/\s+/g, ' ');
        var prev = select.value;

        while (select.options.length > 1) {
            select.remove(1);
        }

        pool.forEach(function (p) {
            if (!q || p.s.indexOf(q) !== -1) {
                var opt = document.createElement('option');
                opt.value = p.v;
                opt.textContent = p.t;
                opt.setAttribute('data-search', p.s);
                select.appendChild(opt);
            }
        });

        var stillThere = Array.prototype.some.call(select.options, function (o) {
            return o.value === prev;
        });
        select.value = stillThere ? prev : '';
    }

    if (search) search.addEventListener('input', applyFilter);

    function syncInvoiceClient() {
        if (!invoiceSelect) return;
        var opt = invoiceSelect.options[invoiceSelect.selectedIndex];
        if (!opt || !opt.value) {
            if (studentBlock) studentBlock.classList.remove('hidden');
            if (companyHint) companyHint.classList.add('hidden');
            return;
        }
        var type = opt.getAttribute('data-client-type') || 'student';
        var userId = opt.getAttribute('data-user-id') || '';
        var label = opt.getAttribute('data-client-label') || '';
        var remaining = opt.getAttribute('data-remaining');

        if (type === 'company') {
            if (studentBlock) studentBlock.classList.add('hidden');
            if (companyHint) companyHint.classList.remove('hidden');
            if (companyLabel) companyLabel.textContent = label;
            if (select) select.value = '';
        } else {
            if (studentBlock) studentBlock.classList.remove('hidden');
            if (companyHint) companyHint.classList.add('hidden');
            if (select && userId) select.value = userId;
        }

        if (amountInput && remaining && (!amountInput.value || amountInput.dataset.autofilled === '1')) {
            amountInput.value = parseFloat(remaining).toFixed(2);
            amountInput.dataset.autofilled = '1';
        }
    }

    if (invoiceSelect) {
        invoiceSelect.addEventListener('change', syncInvoiceClient);
        syncInvoiceClient();
    }
    if (amountInput) {
        amountInput.addEventListener('input', function () {
            amountInput.dataset.autofilled = '0';
        });
    }

    var methodSelect = document.getElementById('payment-method-select');
    var walletRow = document.getElementById('payment-wallet-row');
    var walletSelect = document.getElementById('payment-wallet-select');
    var walletHint = document.getElementById('payment-wallet-balance-hint');

    function updateWalletBalanceHint() {
        if (!walletSelect || !walletHint) return;
        var opt = walletSelect.options[walletSelect.selectedIndex];
        if (!opt || !opt.value) {
            walletHint.classList.add('hidden');
            walletHint.textContent = '';
            return;
        }
        var bal = opt.getAttribute('data-balance');
        if (bal === null || bal === '') {
            walletHint.classList.add('hidden');
            return;
        }
        var n = parseFloat(bal, 10);
        if (isNaN(n)) n = 0;
        walletHint.textContent = 'الرصيد الحالي في هذه المحفظة (قبل إضافة هذه الدفعة): ' + n.toLocaleString('ar-EG', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' ج.م';
        walletHint.classList.remove('hidden');
    }

    function syncWalletRow() {
        if (!methodSelect || !walletRow || !walletSelect) return;
        var isWallet = methodSelect.value === 'wallet';
        walletRow.style.display = isWallet ? '' : 'none';
        walletSelect.disabled = !isWallet;
        walletSelect.required = isWallet;
        if (!isWallet) {
            walletSelect.value = '';
            if (walletHint) {
                walletHint.classList.add('hidden');
                walletHint.textContent = '';
            }
        } else {
            updateWalletBalanceHint();
        }
    }

    if (methodSelect) {
        methodSelect.addEventListener('change', syncWalletRow);
        if (walletSelect) {
            walletSelect.addEventListener('change', updateWalletBalanceHint);
        }
        syncWalletRow();
    }
});
</script>
@endpush
