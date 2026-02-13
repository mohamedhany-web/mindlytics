@extends('layouts.admin')

@section('title', 'إضافة محفظة جديدة - Mindlytics')
@section('header', 'إضافة محفظة جديدة')

@section('content')
<div class="space-y-6">
    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-4 py-4 sm:px-6 bg-slate-50 border-b border-slate-200 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-violet-500 to-violet-600 flex items-center justify-center text-white shadow-md">
                    <i class="fas fa-wallet"></i>
                </div>
                <div>
                    <h2 class="text-xl font-black text-slate-900">إضافة محفظة جديدة</h2>
                    <p class="text-xs text-slate-600">إعداد محفظة للدفع أو التحويل (فودافون كاش، إنستا باي، تحويل بنكي، إلخ).</p>
                </div>
            </div>
            <a href="{{ route('admin.wallets.index') }}" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-slate-700 bg-white border border-slate-300 rounded-xl hover:bg-slate-50 transition-colors">
                <i class="fas fa-arrow-right"></i>
                العودة للمحافظ
            </a>
        </div>

        <form action="{{ route('admin.wallets.store') }}" method="POST" class="p-4 sm:p-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">اسم المحفظة <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" required maxlength="255"
                           class="w-full px-3 py-2.5 border border-slate-300 rounded-xl text-slate-900 placeholder-slate-400 focus:ring-2 focus:ring-violet-500 focus:border-violet-500 transition-colors"
                           placeholder="مثال: فودافون كاش - 01000000000">
                    @error('name')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">نوع المحفظة <span class="text-rose-500">*</span></label>
                    <select name="type" id="wallet-type" required
                            class="w-full px-3 py-2.5 border border-slate-300 rounded-xl text-slate-900 focus:ring-2 focus:ring-violet-500 focus:border-violet-500 transition-colors">
                        <option value="">اختر النوع</option>
                        <option value="vodafone_cash" {{ old('type') == 'vodafone_cash' ? 'selected' : '' }}>فودافون كاش</option>
                        <option value="instapay" {{ old('type') == 'instapay' ? 'selected' : '' }}>إنستا باي</option>
                        <option value="bank_transfer" {{ old('type') == 'bank_transfer' ? 'selected' : '' }}>تحويل بنكي</option>
                        <option value="cash" {{ old('type') == 'cash' ? 'selected' : '' }}>كاش</option>
                        <option value="other" {{ old('type') == 'other' ? 'selected' : '' }}>أخرى</option>
                    </select>
                    @error('type')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">رقم الحساب / المحفظة</label>
                    <input type="text" name="account_number" value="{{ old('account_number') }}" maxlength="100"
                           class="w-full px-3 py-2.5 border border-slate-300 rounded-xl text-slate-900 placeholder-slate-400 focus:ring-2 focus:ring-violet-500 focus:border-violet-500 transition-colors"
                           placeholder="مثال: 01000000000">
                    @error('account_number')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div id="bank-name-field" class="hidden">
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">اسم البنك</label>
                    <input type="text" name="bank_name" value="{{ old('bank_name') }}" maxlength="100"
                           class="w-full px-3 py-2.5 border border-slate-300 rounded-xl text-slate-900 placeholder-slate-400 focus:ring-2 focus:ring-violet-500 focus:border-violet-500 transition-colors"
                           placeholder="مثال: البنك الأهلي">
                    @error('bank_name')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">اسم صاحب الحساب</label>
                    <input type="text" name="account_holder" value="{{ old('account_holder') }}" maxlength="255"
                           class="w-full px-3 py-2.5 border border-slate-300 rounded-xl text-slate-900 placeholder-slate-400 focus:ring-2 focus:ring-violet-500 focus:border-violet-500 transition-colors"
                           placeholder="الاسم كما يظهر في الحساب">
                    @error('account_holder')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">الرصيد الابتدائي (ج.م)</label>
                    <input type="number" name="balance" value="{{ old('balance', 0) }}" step="0.01" min="0"
                           class="w-full px-3 py-2.5 border border-slate-300 rounded-xl text-slate-900 focus:ring-2 focus:ring-violet-500 focus:border-violet-500 transition-colors"
                           placeholder="0">
                    @error('balance')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mt-4 sm:mt-6">
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">ملاحظات</label>
                <textarea name="notes" rows="3" maxlength="1000"
                          class="w-full px-3 py-2.5 border border-slate-300 rounded-xl text-slate-900 placeholder-slate-400 focus:ring-2 focus:ring-violet-500 focus:border-violet-500 transition-colors resize-none"
                          placeholder="أي تفاصيل إضافية عن المحفظة">{{ old('notes') }}</textarea>
                @error('notes')
                    <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="mt-4 sm:mt-6 flex items-center gap-2">
                <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                       class="w-4 h-4 text-violet-600 border-slate-300 rounded focus:ring-violet-500">
                <label for="is_active" class="text-sm font-medium text-slate-700">المحفظة نشطة</label>
            </div>

            <div class="mt-6 pt-4 border-t border-slate-200 flex flex-wrap items-center gap-3 justify-end">
                <a href="{{ route('admin.wallets.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-semibold text-slate-700 bg-white border border-slate-300 rounded-xl hover:bg-slate-50 transition-colors">
                    إلغاء
                </a>
                <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-semibold text-white bg-gradient-to-r from-violet-600 to-violet-500 rounded-xl shadow hover:from-violet-700 hover:to-violet-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-violet-500 transition-all">
                    <i class="fas fa-save"></i>
                    حفظ المحفظة
                </button>
            </div>
        </form>
    </section>
</div>

<script>
(function() {
    var typeSelect = document.getElementById('wallet-type');
    var bankField = document.getElementById('bank-name-field');
    if (!typeSelect || !bankField) return;
    function toggle() {
        if (typeSelect.value === 'bank_transfer') {
            bankField.classList.remove('hidden');
        } else {
            bankField.classList.add('hidden');
        }
    }
    typeSelect.addEventListener('change', toggle);
    toggle();
})();
</script>
@endsection
