@extends('layouts.app')

@section('title', 'حساب التحويل - Mindlytics')
@section('header', 'حساب التحويل')

@section('content')
<div class="p-3 sm:p-4 md:p-6 space-y-4 sm:space-y-6" style="background: #f8fafc; min-height: 100vh;">
    <section class="rounded-2xl bg-white/95 backdrop-blur border-2 border-slate-200/50 shadow-xl overflow-hidden">
        <div class="px-5 py-6 sm:px-8 border-b border-slate-200 bg-gradient-to-r from-indigo-50 to-white">
            <h2 class="text-xl sm:text-2xl font-black text-slate-900 flex items-center gap-2">
                <i class="fas fa-university text-indigo-600"></i>
                بيانات حساب التحويل
            </h2>
            <p class="text-sm text-slate-600 mt-2">أدخل بيانات الحساب البنكي أو التحويل التي تريد استلام مستحقاتك عليها. سيتم استخدامها عند تحويل المبالغ حسب الاتفاقيات.</p>
        </div>

        @if(session('success'))
        <div class="mx-5 mt-5 p-4 bg-green-50 border border-green-200 rounded-xl text-green-800 text-sm">
            <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
        </div>
        @endif

        <form action="{{ route('instructor.transfer-account.store') }}" method="POST" class="p-5 sm:p-8 space-y-5">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label for="bank_name" class="block text-sm font-semibold text-slate-700 mb-1">اسم البنك</label>
                    <input type="text" name="bank_name" id="bank_name" value="{{ old('bank_name', $detail->bank_name) }}"
                           class="w-full px-4 py-2.5 border border-slate-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                           placeholder="مثال: البنك الأهلي">
                    @error('bank_name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="account_holder_name" class="block text-sm font-semibold text-slate-700 mb-1">اسم صاحب الحساب</label>
                    <input type="text" name="account_holder_name" id="account_holder_name" value="{{ old('account_holder_name', $detail->account_holder_name) }}"
                           class="w-full px-4 py-2.5 border border-slate-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                           placeholder="الاسم كما في البطاقة">
                    @error('account_holder_name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="account_number" class="block text-sm font-semibold text-slate-700 mb-1">رقم الحساب</label>
                    <input type="text" name="account_number" id="account_number" value="{{ old('account_number', $detail->account_number) }}" dir="ltr"
                           class="w-full px-4 py-2.5 border border-slate-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                           placeholder="رقم الحساب البنكي">
                    @error('account_number')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="iban" class="block text-sm font-semibold text-slate-700 mb-1">الآيبان (IBAN)</label>
                    <input type="text" name="iban" id="iban" value="{{ old('iban', $detail->iban) }}" dir="ltr"
                           class="w-full px-4 py-2.5 border border-slate-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                           placeholder="EG...">
                    @error('iban')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="branch_name" class="block text-sm font-semibold text-slate-700 mb-1">الفرع</label>
                    <input type="text" name="branch_name" id="branch_name" value="{{ old('branch_name', $detail->branch_name) }}"
                           class="w-full px-4 py-2.5 border border-slate-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                           placeholder="اسم الفرع (اختياري)">
                    @error('branch_name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="swift_code" class="block text-sm font-semibold text-slate-700 mb-1">كود SWIFT</label>
                    <input type="text" name="swift_code" id="swift_code" value="{{ old('swift_code', $detail->swift_code) }}" dir="ltr"
                           class="w-full px-4 py-2.5 border border-slate-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                           placeholder="اختياري">
                    @error('swift_code')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
            </div>
            <div>
                <label for="notes" class="block text-sm font-semibold text-slate-700 mb-1">ملاحظات</label>
                <textarea name="notes" id="notes" rows="2" class="w-full px-4 py-2.5 border border-slate-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                          placeholder="أي تفاصيل إضافية للتحويل">{{ old('notes', $detail->notes) }}</textarea>
                @error('notes')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div class="pt-4">
                <button type="submit" class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold transition-colors">
                    <i class="fas fa-save mr-2"></i>حفظ بيانات التحويل
                </button>
            </div>
        </form>
    </section>
</div>
@endsection
