@extends('layouts.admin')

@section('title', 'إرسال واتساب - Mindlytics')
@section('header', 'قسم الواتساب')

@section('content')
<div class="space-y-6">
    @include('admin.whatsapp._nav', ['active' => 'send'])

    @if(session('error'))
        <div class="rounded-xl bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 text-sm">{{ session('error') }}</div>
    @endif

    <section class="rounded-3xl bg-white border border-slate-200 shadow-sm p-6 max-w-2xl">
        <h3 class="text-lg font-bold text-slate-900 mb-4">إرسال رسالة واتساب</h3>
        <form method="POST" action="{{ route('admin.whatsapp.send.post') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">رقم الهاتف</label>
                <input type="text" name="phone" value="{{ old('phone') }}" required
                       placeholder="01012345678 أو 201012345678"
                       class="w-full rounded-xl border-slate-300 focus:border-emerald-500 focus:ring-emerald-500">
                @error('phone')<p class="text-rose-600 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">الرسالة</label>
                <textarea name="message" rows="6" required
                          class="w-full rounded-xl border-slate-300 focus:border-emerald-500 focus:ring-emerald-500">{{ old('message') }}</textarea>
                @error('message')<p class="text-rose-600 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-emerald-600 text-white font-semibold hover:bg-emerald-700">
                <i class="fab fa-whatsapp"></i>
                إرسال
            </button>
        </form>
    </section>
</div>
@endsection
