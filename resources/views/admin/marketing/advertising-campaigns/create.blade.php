@extends('layouts.admin')
@section('title', 'حملة إعلانية جديدة')
@section('header', 'حملة إعلانية جديدة')
@section('content')
<div class="w-full max-w-4xl mx-auto space-y-6">
    <div class="rounded-3xl bg-white/95 backdrop-blur border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-5 py-6 sm:px-8 border-b border-slate-200">
            <h1 class="text-2xl font-bold text-slate-900">حملة إعلانية جديدة</h1>
            <p class="text-slate-500 mt-1">أدخل تفاصيل الحملة وتكلفتها وحدّد موظفي السيلز المسؤولين عنها.</p>
        </div>
        <form action="{{ route('admin.advertising-campaigns.store') }}" method="POST" class="p-5 sm:p-8">
            @csrf
            @include('admin.marketing.advertising-campaigns._form')

            <div class="mt-8 flex items-center gap-3">
                <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 bg-gradient-to-r from-sky-500 to-blue-600 hover:from-sky-600 hover:to-blue-700 text-white rounded-xl font-semibold shadow-lg shadow-sky-500/30 transition-all">
                    <i class="fas fa-check"></i> حفظ الحملة
                </button>
                <a href="{{ route('admin.advertising-campaigns.index') }}" class="px-6 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-semibold transition-all">إلغاء</a>
            </div>
        </form>
    </div>
</div>
@endsection
