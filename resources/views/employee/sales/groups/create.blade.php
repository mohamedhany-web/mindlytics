@extends('layouts.employee')

@section('title', 'مجموعة جديدة')
@section('header', 'مجموعة جديدة')

@section('content')
@include('employee.sales.groups._styles')

<div class="space-y-4">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h2 class="text-xl font-bold text-slate-900">مجموعة جديدة</h2>
            <p class="text-sm text-slate-500">بعد الإنشاء يمكنك إضافة عملاء أو اختيارها عند تسجيل عميل جديد</p>
        </div>
        <a href="{{ route('employee.sales.groups.index') }}" class="text-sm text-slate-600 hover:text-slate-900"><i class="fas fa-arrow-right ml-1"></i> المجموعات</a>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-12 gap-6">
        <form method="post" action="{{ route('employee.sales.groups.store') }}" class="xl:col-span-8 sales-panel p-5 sm:p-6 space-y-4">
            @csrf
            @if($errors->any())
                <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                    <ul class="list-disc list-inside">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                </div>
            @endif
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">اسم المجموعة <span class="text-red-600">*</span></label>
                <input type="text" name="name" value="{{ old('name') }}" required placeholder="مثال: عملاء أكتوبر — كورسات أونلاين" class="px-3 py-2.5">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">وصف <span class="text-slate-400">(اختياري)</span></label>
                <textarea name="description" rows="3" placeholder="ملاحظة قصيرة عن هذه المجموعة…" class="px-3 py-2.5 text-sm">{{ old('description') }}</textarea>
            </div>
            <div class="flex flex-wrap gap-2 pt-2 border-t border-slate-100">
                <button type="submit" class="px-5 py-2.5 bg-slate-800 hover:bg-slate-900 text-white rounded-lg text-sm font-semibold">إنشاء المجموعة</button>
                <a href="{{ route('employee.sales.groups.index') }}" class="px-5 py-2.5 text-sm text-slate-600">إلغاء</a>
            </div>
        </form>
        <aside class="xl:col-span-4 space-y-4">
            <div class="sales-panel p-4 text-sm text-slate-600 space-y-2">
                <p class="font-semibold text-slate-800">نصائح</p>
                <p>استخدم أسماء واضحة: «متابعة أسبوع 12»، «Leads فيسبوك»…</p>
                <p>عند <a href="{{ route('employee.sales.leads.create') }}" class="text-slate-800 underline">تسجيل عميل</a> اختر المجموعة مباشرة.</p>
            </div>
        </aside>
    </div>
</div>
@endsection
