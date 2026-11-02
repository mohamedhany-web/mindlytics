@extends('sales::crm.admin')

@section('title', 'إضافة توصيف دبلومة')
@section('header', 'توصيف دبلومة جديدة')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="rounded-3xl bg-white border border-slate-200 shadow-lg p-6 sm:p-8">
        <div class="mb-6">
            <a href="{{ route('admin.sales-diploma-board.index') }}" class="text-sm text-slate-500 hover:text-slate-800"><i class="fas fa-arrow-right ml-1"></i> رجوع للقائمة</a>
            <h1 class="text-2xl font-bold text-slate-900 mt-2">دبلومة جديدة</h1>
            <p class="text-slate-500 text-sm mt-1">نفس بيانات توصيف الكورس + تاريخ البداية وطرق الحجز والمحاضرات المجانية</p>
        </div>


        <form method="POST" action="{{ route('admin.sales-diploma-board.store') }}" class="space-y-4">
            @csrf
            @include('sales::admin.diploma-board._form', ['entry' => $entry, 'paths' => $paths])
            <div class="pt-4">
                <button type="submit" class="px-6 py-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-bold">حفظ</button>
            </div>
        </form>
    </div>
</div>
@endsection
