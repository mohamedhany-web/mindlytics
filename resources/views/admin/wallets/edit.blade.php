@extends('layouts.admin')

@section('title', 'تعديل المحفظة')

@section('content')
<div class="px-4 py-8">
    <div class="max-w-5xl mx-auto space-y-8">
        <div class="bg-gradient-to-br from-sky-500 via-sky-600 to-indigo-600 rounded-3xl p-6 sm:p-8 shadow-xl text-white relative overflow-hidden">
            <div class="absolute inset-y-0 left-0 w-40 bg-white/10 blur-3xl pointer-events-none"></div>
            <div class="relative z-10 flex flex-col md:flex-row md:items-center md:justify-between gap-6">
                <div>
                    <p class="text-sm uppercase tracking-widest text-white/70 mb-3">تعديل المحفظة</p>
                    <h1 class="text-3xl sm:text-4xl font-bold">{{ $wallet->name ?? 'محفظة بدون اسم' }}</h1>
                    <p class="mt-3 text-white/80 flex items-center gap-2">
                        <i class="fas fa-user-circle"></i>
                        <span>{{ $wallet->user?->name ?? 'غير مرتبط بمستخدم' }}</span>
                    </p>
                </div>
                <div class="grid grid-cols-2 gap-4 text-right">
                    <div class="bg-white/15 backdrop-blur rounded-2xl px-4 py-3">
                        <p class="text-xs text-white/70">الرصيد الحالي</p>
                        <p class="text-xl font-semibold">{{ number_format($wallet->balance, 2) }} {{ $wallet->currency ?? 'ج.م' }}</p>
                    </div>
                    <div class="bg-white/10 backdrop-blur rounded-2xl px-4 py-3">
                        <p class="text-xs text-white/70">نوع المحفظة</p>
                        <p class="text-lg font-semibold">{{ $wallet->type_name ?? 'غير محدد' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-3xl shadow-xl border border-gray-100/60 overflow-hidden" x-data="{ walletType: '{{ old('type', $wallet->type) }}' }">
            <div class="border-b border-gray-100 px-6 sm:px-8 py-5 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <div>
                    <h2 class="text-xl font-semibold text-gray-900">بيانات المحفظة</h2>
                    <p class="text-sm text-gray-500">قم بتحديث معلومات المحفظة واحفظ التغييرات.</p>
                </div>
                <a href="{{ route('admin.wallets.show', $wallet) }}" class="inline-flex items-center gap-2 text-sm font-medium text-sky-600 hover:text-sky-700">
                    <i class="fas fa-arrow-right ml-1"></i>
                    العودة للتفاصيل
                </a>
            </div>

            <form action="{{ route('admin.wallets.update', $wallet) }}" method="POST" class="p-6 sm:p-8 space-y-10">
                @csrf
                @method('PUT')

                <section class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">اسم المحفظة *</label>
                            <input type="text" name="name" value="{{ old('name', $wallet->name) }}" required
                                   class="w-full rounded-2xl border border-gray-200 bg-white/60 px-4 py-3 text-gray-900 shadow-sm focus:border-sky-500 focus:ring-4 focus:ring-sky-500/20 transition">
                            @error('name')
                                <p class="mt-2 text-sm text-rose-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">نوع المحفظة *</label>
                            <select name="type" x-model="walletType" required
                                    class="w-full rounded-2xl border border-gray-200 bg-white/60 px-4 py-3 text-gray-900 shadow-sm focus:border-sky-500 focus:ring-4 focus:ring-sky-500/20 transition">
                                <option value="vodafone_cash">فودافون كاش</option>
                                <option value="instapay">إنستا باي</option>
                                <option value="bank_transfer">تحويل بنكي</option>
                                <option value="cash">كاش</option>
                                <option value="other">أخرى</option>
                            </select>
                            @error('type')
                                <p class="mt-2 text-sm text-rose-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">رقم الحساب/المحفظة</label>
                            <input type="text" name="account_number" value="{{ old('account_number', $wallet->account_number) }}"
                                   class="w-full rounded-2xl border border-gray-200 bg-white/60 px-4 py-3 text-gray-900 shadow-sm focus:border-sky-500 focus:ring-4 focus:ring-sky-500/20 transition">
                            @error('account_number')
                                <p class="mt-2 text-sm text-rose-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div x-show="walletType === 'bank_transfer'" x-cloak>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">اسم البنك</label>
                            <input type="text" name="bank_name" value="{{ old('bank_name', $wallet->bank_name) }}"
                                   class="w-full rounded-2xl border border-gray-200 bg-white/60 px-4 py-3 text-gray-900 shadow-sm focus:border-sky-500 focus:ring-4 focus:ring-sky-500/20 transition">
                            @error('bank_name')
                                <p class="mt-2 text-sm text-rose-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">اسم صاحب الحساب</label>
                            <input type="text" name="account_holder" value="{{ old('account_holder', $wallet->account_holder) }}"
                                   class="w-full rounded-2xl border border-gray-200 bg-white/60 px-4 py-3 text-gray-900 shadow-sm focus:border-sky-500 focus:ring-4 focus:ring-sky-500/20 transition">
                            @error('account_holder')
                                <p class="mt-2 text-sm text-rose-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">الرصيد الحالي</label>
                            <div class="relative">
                                <input type="number" name="balance" value="{{ old('balance', $wallet->balance) }}" step="0.01" min="0" readonly
                                       class="w-full rounded-2xl border border-gray-200 bg-gray-100 px-4 py-3 text-gray-900 shadow-sm cursor-not-allowed">
                                <span class="absolute inset-y-0 left-4 flex items-center text-xs text-gray-500">غير قابل للتعديل</span>
                            </div>
                            <p class="mt-2 text-xs text-gray-500">لتعديل الرصيد استخدم صفحة المعاملات.</p>
                        </div>
                    </div>
                </section>

                <section class="space-y-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">ملاحظات</label>
                        <textarea name="notes" rows="4"
                                  class="w-full rounded-2xl border border-gray-200 bg-white/60 px-4 py-3 text-gray-900 shadow-sm focus:border-sky-500 focus:ring-4 focus:ring-sky-500/20 transition">{{ old('notes', $wallet->notes) }}</textarea>
                        @error('notes')
                            <p class="mt-2 text-sm text-rose-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center gap-3 px-4 py-3 rounded-2xl bg-slate-100 border border-slate-200">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $wallet->is_active) ? 'checked' : '' }}
                               class="w-5 h-5 text-sky-600 border-gray-300 rounded focus:ring-sky-500">
                        <div>
                            <p class="text-sm font-semibold text-gray-800">تفعيل المحفظة</p>
                            <p class="text-xs text-gray-500">فعّل هذا الخيار للسماح باستخدام المحفظة.</p>
                        </div>
                    </div>
                </section>

                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 pt-4 border-t border-gray-100">
                    <a href="{{ route('admin.wallets.index') }}" class="inline-flex items-center justify-center gap-2 rounded-2xl border border-gray-200 px-5 py-3 text-sm font-semibold text-gray-700 hover:bg-gray-50 transition">
                        <i class="fas fa-arrow-right ml-1"></i>
                        إلغاء
                    </a>
                    <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-2xl bg-sky-600 hover:bg-sky-700 text-white px-6 py-3 text-sm font-semibold shadow-lg shadow-sky-500/20 transition">
                        <i class="fas fa-save ml-1"></i>
                        حفظ التغييرات
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
