@extends('layouts.admin')

@section('title', 'إضافة محفظة جديدة')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-xl shadow-lg p-6">
            <h1 class="text-2xl font-bold text-gray-900 mb-6">إضافة محفظة جديدة</h1>

            <form action="{{ route('admin.wallets.store') }}" method="POST">
                @csrf
                
                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">اسم المحفظة *</label>
                        <input type="text" name="name" value="{{ old('name') }}" required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500"
                               placeholder="مثال: فودافون كاش - رقم 01000000000">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">نوع المحفظة *</label>
                        <select name="type" id="wallet-type" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                            <option value="">اختر النوع</option>
                            <option value="vodafone_cash" {{ old('type') == 'vodafone_cash' ? 'selected' : '' }}>فودافون كاش</option>
                            <option value="instapay" {{ old('type') == 'instapay' ? 'selected' : '' }}>إنستا باي</option>
                            <option value="bank_transfer" {{ old('type') == 'bank_transfer' ? 'selected' : '' }}>تحويل بنكي</option>
                            <option value="cash" {{ old('type') == 'cash' ? 'selected' : '' }}>كاش</option>
                            <option value="other" {{ old('type') == 'other' ? 'selected' : '' }}>أخرى</option>
                        </select>
                        @error('type')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">رقم الحساب/المحفظة</label>
                        <input type="text" name="account_number" value="{{ old('account_number') }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500"
                               placeholder="مثال: 01000000000">
                        @error('account_number')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div id="bank-name-field" style="display: none;">
                        <label class="block text-sm font-medium text-gray-700 mb-2">اسم البنك</label>
                        <input type="text" name="bank_name" value="{{ old('bank_name') }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500"
                               placeholder="مثال: البنك الأهلي">
                        @error('bank_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">اسم صاحب الحساب</label>
                        <input type="text" name="account_holder" value="{{ old('account_holder') }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                        @error('account_holder')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">الرصيد الابتدائي</label>
                        <input type="number" name="balance" value="{{ old('balance', 0) }}" step="0.01" min="0"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                        @error('balance')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">ملاحظات</label>
                        <textarea name="notes" rows="3"
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500">{{ old('notes') }}</textarea>
                        @error('notes')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                               class="w-4 h-4 text-sky-600 border-gray-300 rounded focus:ring-sky-500">
                        <label class="mr-2 block text-sm text-gray-700">المحفظة نشطة</label>
                    </div>

                    <div class="flex justify-end space-x-4 space-x-reverse">
                        <a href="{{ route('admin.wallets.index') }}" class="btn-secondary">
                            إلغاء
                        </a>
                        <button type="submit" class="btn-primary">
                            <i class="fas fa-save ml-2"></i>
                            حفظ
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('wallet-type').addEventListener('change', function() {
    const bankNameField = document.getElementById('bank-name-field');
    if (this.value === 'bank_transfer') {
        bankNameField.style.display = 'block';
    } else {
        bankNameField.style.display = 'none';
    }
});
</script>
@endsection
