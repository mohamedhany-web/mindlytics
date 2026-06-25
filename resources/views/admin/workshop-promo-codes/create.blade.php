@extends('layouts.admin')

@section('title', 'إنشاء كود ورشة')
@section('header', 'إنشاء كود ورشة')

@section('content')
<div class="p-3 sm:p-6 space-y-6" style="background:#f8fafc;min-height:100vh;">
    @include('admin.marketing._tabs', ['active' => 'promo'])
    <div class="max-w-4xl mx-auto rounded-2xl bg-white border border-slate-200 shadow-sm p-6 sm:p-8">
        <h1 class="text-xl font-black text-slate-900 mb-6">كود خصم جديد مرتبط بورشة</h1>
        <form action="{{ route('admin.workshop-promo-codes.store') }}" method="POST" class="space-y-6">
            @include('admin.workshop-promo-codes._form')
            <div class="flex gap-3 pt-4 border-t border-slate-100">
                <button type="submit" class="px-6 py-3 rounded-xl bg-violet-600 text-white font-bold hover:bg-violet-700">حفظ الكود</button>
                <a href="{{ route('admin.workshop-promo-codes.index') }}" class="px-6 py-3 rounded-xl bg-slate-100 text-slate-700 font-semibold">إلغاء</a>
            </div>
        </form>
    </div>
</div>
@endsection
