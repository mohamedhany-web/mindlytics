@extends('layouts.app')

@section('title', 'طلب حجز — ' . $offlineCourse->title)
@section('header', 'طلب حجز كورس أوفلاين')

@section('content')
<div class="w-full px-4 sm:px-6 lg:px-8 py-6 max-w-2xl mx-auto space-y-6">
    <div class="bg-white rounded-xl p-5 border border-gray-200 shadow-sm">
        <a href="{{ route('student.offline-courses.booking.catalog') }}" class="text-sm text-purple-600 hover:text-purple-800 font-medium">
            <i class="fas fa-arrow-right ml-1"></i> العودة لقائمة الحجز
        </a>
        <h1 class="text-xl font-bold text-gray-900 mt-3">{{ $offlineCourse->title }}</h1>
        <p class="text-gray-600 mt-1">السعر: <span class="font-bold text-gray-900">{{ number_format((float) $offlineCourse->price, 2) }} ج.م</span></p>
    </div>

    <form action="{{ route('student.offline-courses.booking.store', $offlineCourse) }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-xl p-6 border border-gray-200 shadow-sm space-y-5" x-data="{ method: '{{ old('payment_method', 'bank_transfer') }}' }">
        @csrf

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">طريقة التحويل *</label>
            <select name="payment_method" x-model="method" required class="w-full rounded-lg border-gray-300 focus:ring-purple-500 focus:border-purple-500">
                <option value="bank_transfer">تحويل بنكي / تعليمات عامة</option>
                @if($wallets->isNotEmpty())
                    <option value="wallet">محفظة إلكترونية (فودافون كاش / إنستاباي / …)</option>
                @endif
            </select>
            @error('payment_method')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
        </div>

        @if($wallets->isNotEmpty())
            <div class="space-y-2" x-show="method === 'wallet' || method === 'bank_transfer'" x-cloak>
                <label class="block text-sm font-medium text-gray-700">
                    حساب التحويل / المحفظة
                    <span class="text-red-600" x-show="method === 'wallet'">*</span>
                    <span class="text-gray-400 font-normal text-xs" x-show="method === 'bank_transfer'">(اختياري للتحويل البنكي)</span>
                </label>
                <select name="wallet_id" class="w-full rounded-lg border-gray-300 focus:ring-purple-500 focus:border-purple-500" x-bind:required="method === 'wallet'">
                    <option value="">— اختر —</option>
                    @foreach($wallets as $w)
                        <option value="{{ $w->id }}" @selected(old('wallet_id') == $w->id)>
                            {{ \App\Models\Wallet::typeLabel($w->type) }}
                            @if($w->name) — {{ $w->name }} @endif
                            @if($w->account_number) ({{ $w->account_number }}) @endif
                        </option>
                    @endforeach
                </select>
                @error('wallet_id')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
        @endif

        <div x-show="method === 'bank_transfer'" x-cloak class="rounded-lg bg-slate-50 border border-slate-200 p-3 text-sm text-slate-700">
            بعد التحويل ارفع إيصالاً واضحاً. يمكنك عند التحويل البنكي اختيار حساب المنصة من القائمة أعلاه إن وُجد.
        </div>

        @if((float) $offlineCourse->price > 0)
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">صورة إيصال التحويل *</label>
                <input type="file" name="payment_proof" accept="image/jpeg,image/png,image/jpg" required class="block w-full text-sm text-gray-600">
                <p class="text-xs text-gray-500 mt-1">صورة واضحة، بحد أقصى 2 ميجابايت (jpg, png).</p>
                @error('payment_proof')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
        @else
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">صورة إيصال (اختياري)</label>
                <input type="file" name="payment_proof" accept="image/jpeg,image/png,image/jpg" class="block w-full text-sm text-gray-600">
                @error('payment_proof')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
        @endif

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">ملاحظات للطالب (اختياري)</label>
            <textarea name="student_notes" rows="3" class="w-full rounded-lg border-gray-300 focus:ring-purple-500 focus:border-purple-500" placeholder="اسمك كما يظهر في التحويل، أو أي تفاصيل تساعد المراجعة">{{ old('student_notes') }}</textarea>
            @error('student_notes')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
        </div>

        @error('error')
            <p class="text-red-600 text-sm">{{ $message }}</p>
        @enderror

        <button type="submit" class="w-full py-3 rounded-xl bg-purple-600 text-white font-bold hover:bg-purple-700 transition-colors">
            إرسال طلب الحجز
        </button>
    </form>
</div>
@endsection
