@extends('layouts.admin')

@section('title', 'إنشاء مصروف جديد')
@section('header', 'إنشاء مصروف جديد')

@section('content')
<div class="p-6 bg-gray-50 min-h-screen">
    <!-- Header Section -->
    <div class="mb-8">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">
                    <i class="fas fa-receipt text-sky-600 ml-3"></i>
                    {{ __('إنشاء مصروف جديد') }}
                </h1>
                <p class="text-gray-600">{{ __('إضافة مصروف جديد للموافقة عليه') }}</p>
            </div>
            <a href="{{ route('admin.expenses.index') }}" 
               class="bg-gray-600 hover:bg-gray-700 text-white px-5 py-2.5 rounded-lg font-medium transition-colors flex items-center gap-2">
                <i class="fas fa-arrow-right"></i>
                <span>{{ __('العودة') }}</span>
            </a>
        </div>
    </div>

    <!-- Form Section -->
    <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-200">
        <form action="{{ route('admin.expenses.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- العنوان -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">
                        <i class="fas fa-heading text-sky-600 ml-2"></i>
                        {{ __('العنوان') }} *
                    </label>
                    <input type="text" name="title" value="{{ old('title') }}" required 
                           class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-sky-500 focus:border-sky-500 transition-all"
                           placeholder="{{ __('مثال: شراء معدات للقاعة') }}">
                    @error('title')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- الفئة -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">
                        <i class="fas fa-tags text-sky-600 ml-2"></i>
                        {{ __('الفئة') }} *
                    </label>
                    <select name="category" required 
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-sky-500 focus:border-sky-500 transition-all">
                        <option value="">{{ __('اختر الفئة') }}</option>
                        <option value="operational" {{ old('category') == 'operational' ? 'selected' : '' }}>{{ __('تشغيلي') }}</option>
                        <option value="marketing" {{ old('category') == 'marketing' ? 'selected' : '' }}>{{ __('تسويق') }}</option>
                        <option value="salaries" {{ old('category') == 'salaries' ? 'selected' : '' }}>{{ __('رواتب') }}</option>
                        <option value="utilities" {{ old('category') == 'utilities' ? 'selected' : '' }}>{{ __('مرافق') }}</option>
                        <option value="equipment" {{ old('category') == 'equipment' ? 'selected' : '' }}>{{ __('معدات') }}</option>
                        <option value="maintenance" {{ old('category') == 'maintenance' ? 'selected' : '' }}>{{ __('صيانة') }}</option>
                        <option value="other" {{ old('category') == 'other' ? 'selected' : '' }}>{{ __('أخرى') }}</option>
                    </select>
                    @error('category')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- المبلغ -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">
                        <i class="fas fa-money-bill-wave text-sky-600 ml-2"></i>
                        {{ __('المبلغ') }} * (ج.م)
                    </label>
                    <input type="number" name="amount" step="0.01" min="0.01" value="{{ old('amount') }}" required 
                           class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-sky-500 focus:border-sky-500 transition-all"
                           placeholder="0.00">
                    @error('amount')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- تاريخ المصروف -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">
                        <i class="fas fa-calendar-alt text-sky-600 ml-2"></i>
                        {{ __('تاريخ المصروف') }} *
                    </label>
                    <input type="date" name="expense_date" value="{{ old('expense_date', date('Y-m-d')) }}" required 
                           class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-sky-500 focus:border-sky-500 transition-all">
                    @error('expense_date')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- طريقة الدفع -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">
                        <i class="fas fa-credit-card text-sky-600 ml-2"></i>
                        {{ __('طريقة الدفع') }} *
                    </label>
                    <select name="payment_method" id="payment_method" required 
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-sky-500 focus:border-sky-500 transition-all">
                        <option value="">{{ __('اختر طريقة الدفع') }}</option>
                        <option value="cash" {{ old('payment_method') == 'cash' ? 'selected' : '' }}>{{ __('نقدي') }}</option>
                        <option value="bank_transfer" {{ old('payment_method') == 'bank_transfer' ? 'selected' : '' }}>{{ __('تحويل بنكي') }}</option>
                        <option value="card" {{ old('payment_method') == 'card' ? 'selected' : '' }}>{{ __('بطاقة') }}</option>
                        <option value="wallet" {{ old('payment_method') == 'wallet' ? 'selected' : '' }}>{{ __('محفظة إلكترونية') }}</option>
                        <option value="other" {{ old('payment_method') == 'other' ? 'selected' : '' }}>{{ __('أخرى') }}</option>
                    </select>
                    @error('payment_method')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- المحفظة الإلكترونية -->
                <div id="wallet_field" style="display: none;">
                    <label class="block text-sm font-bold text-gray-700 mb-2">
                        <i class="fas fa-wallet text-sky-600 ml-2"></i>
                        {{ __('المحفظة الإلكترونية') }}
                    </label>
                    <select name="wallet_id" id="wallet_id"
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-sky-500 focus:border-sky-500 transition-all">
                        <option value="">{{ __('اختر محفظة') }}</option>
                        @foreach($wallets as $wallet)
                            <option value="{{ $wallet->id }}" {{ old('wallet_id') == $wallet->id ? 'selected' : '' }}>
                                {{ $wallet->name }} ({{ $wallet->type_name }})
                            </option>
                        @endforeach
                    </select>
                    @error('wallet_id')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- رقم المرجع -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-hashtag text-sky-600 ml-2"></i>
                        {{ __('رقم المرجع') }} (اختياري)
                    </label>
                    <input type="text" name="reference_number" value="{{ old('reference_number') }}" 
                           class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-sky-500 focus:border-sky-500 transition-all"
                           placeholder="{{ __('رقم الفاتورة، رقم الشيك، إلخ') }}">
                    @error('reference_number')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- المرفق -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">
                        <i class="fas fa-paperclip text-sky-600 ml-2"></i>
                        {{ __('صورة الفاتورة/الإيصال') }} (اختياري)
                    </label>
                    <input type="file" name="attachment" accept="image/*" 
                           class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-sky-500 focus:border-sky-500 transition-all">
                    <p class="text-xs text-gray-500 mt-1">{{ __('يُسمح بالصور فقط (JPEG, PNG, JPG) - الحد الأقصى 2 ميجابايت') }}</p>
                    @error('attachment')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- الوصف -->
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">
                    <i class="fas fa-align-right text-sky-600 ml-2"></i>
                    {{ __('الوصف') }} (اختياري)
                </label>
                <textarea name="description" rows="3" 
                          class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-sky-500 focus:border-sky-500 transition-all"
                          placeholder="{{ __('وصف تفصيلي للمصروف...') }}">{{ old('description') }}</textarea>
                @error('description')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- الملاحظات -->
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">
                    <i class="fas fa-sticky-note text-sky-600 ml-2"></i>
                    {{ __('ملاحظات') }} (اختياري)
                </label>
                <textarea name="notes" rows="2" 
                          class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-sky-500 focus:border-sky-500 transition-all"
                          placeholder="{{ __('ملاحظات إضافية...') }}">{{ old('notes') }}</textarea>
                @error('notes')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Buttons -->
            <div class="flex flex-wrap gap-4 pt-4 border-t border-gray-200">
                <button type="submit" 
                        class="bg-gradient-to-l from-sky-600 to-sky-500 hover:from-sky-700 hover:to-sky-600 text-white px-6 py-3 rounded-xl font-medium transition-all duration-200 shadow-md hover:shadow-lg flex items-center gap-2">
                    <i class="fas fa-save"></i>
                    <span>{{ __('إنشاء المصروف') }}</span>
                </button>
                <a href="{{ route('admin.expenses.index') }}" 
                   class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-6 py-3 rounded-xl font-medium transition-colors flex items-center gap-2">
                    <i class="fas fa-times"></i>
                    <span>{{ __('إلغاء') }}</span>
                </a>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const paymentMethod = document.getElementById('payment_method');
    const walletField = document.getElementById('wallet_field');
    const walletId = document.getElementById('wallet_id');

    paymentMethod.addEventListener('change', function() {
        if (this.value === 'wallet' || this.value === 'bank_transfer') {
            walletField.style.display = 'block';
            if (this.value === 'wallet') {
                walletId.setAttribute('required', 'required');
            } else {
                walletId.removeAttribute('required');
            }
        } else {
            walletField.style.display = 'none';
            walletId.removeAttribute('required');
            walletId.value = '';
        }
    });

    // Check on page load if payment method is already selected
    if (paymentMethod.value === 'wallet' || paymentMethod.value === 'bank_transfer') {
        walletField.style.display = 'block';
        if (paymentMethod.value === 'wallet') {
            walletId.setAttribute('required', 'required');
        }
    }
});
</script>
@endpush
@endsection

