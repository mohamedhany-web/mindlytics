@extends('layouts.admin')

@section('title', 'إضافة كورس — لوحة المبيعات')
@section('header', 'إضافة كورس')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="rounded-3xl bg-white border border-slate-200 shadow-lg p-6 sm:p-8">
        <div class="mb-6">
            <a href="{{ route('admin.sales-course-board.index') }}" class="text-sm text-slate-500 hover:text-slate-800"><i class="fas fa-arrow-right ml-1"></i> رجوع للقائمة</a>
            <h1 class="text-2xl font-bold text-slate-900 mt-2">كورس جديد</h1>
        </div>

        <form method="POST" action="{{ route('admin.sales-course-board.store') }}" class="space-y-4">
            @csrf
            @include('admin.sales-course-board._form', ['entry' => $entry, 'courses' => $courses])
            <div class="pt-4">
                <button type="submit" class="px-6 py-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-bold">حفظ</button>
            </div>
        </form>
    </div>
</div>
@endsection
