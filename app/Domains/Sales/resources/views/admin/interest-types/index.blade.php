@extends('layouts.admin')

@section('title', 'أنواع اهتمام العملاء')
@section('header', 'المبيعات — أنواع الاهتمام')

@section('content')
@php
    $inputClass = 'w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500';
@endphp
<div class="space-y-6">
    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-4 py-4 bg-slate-50 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h2 class="text-xl font-black text-slate-900">أنواع اهتمام العملاء</h2>
                <p class="text-xs text-slate-600">كورسات / دبلومات / ورش / B2B — أساس توزيع الـ leads على المتخصصين.</p>
            </div>
            <a href="{{ route('admin.sales.specialties.index') }}" class="inline-flex items-center gap-2 px-3 py-2 text-sm font-semibold rounded-xl border border-slate-300 hover:bg-white">تخصصات الموظفين</a>
        </div>
        <div class="grid grid-cols-2 xl:grid-cols-4 gap-3 p-4">
            <div class="rounded-xl border p-3"><p class="text-xs text-slate-500">الإجمالي</p><p class="text-lg font-black">{{ $stats['total'] }}</p></div>
            <div class="rounded-xl border p-3"><p class="text-xs text-slate-500">نشط</p><p class="text-lg font-black text-emerald-700">{{ $stats['active'] }}</p></div>
            <div class="rounded-xl border p-3"><p class="text-xs text-slate-500">عملاء</p><p class="text-lg font-black">{{ number_format($stats['leads_total']) }}</p></div>
            <div class="rounded-xl border p-3"><p class="text-xs text-slate-500">تخصصات مربوطة</p><p class="text-lg font-black">{{ number_format($stats['specialists']) }}</p></div>
        </div>
    </section>

    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg p-4">
        <h3 class="font-black text-slate-900 mb-3">إضافة نوع</h3>
        <form method="post" action="{{ route('admin.sales.interest-types.store') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-3 items-end">
            @csrf
            <div class="lg:col-span-2">
                <label class="text-xs font-semibold">الاسم بالعربي *</label>
                <input type="text" name="name_ar" required class="{{ $inputClass }}">
            </div>
            <div>
                <label class="text-xs font-semibold">English</label>
                <input type="text" name="name_en" class="{{ $inputClass }}">
            </div>
            <div>
                <label class="text-xs font-semibold">اللون</label>
                <input type="color" name="color" value="#059669" class="h-10 w-full rounded-xl border">
            </div>
            <div>
                <label class="text-xs font-semibold">الترتيب</label>
                <input type="number" name="sort_order" value="0" class="{{ $inputClass }}">
            </div>
            <button class="rounded-xl bg-emerald-600 text-white px-4 py-2.5 text-sm font-semibold">إضافة</button>
        </form>
    </section>

    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <table class="min-w-full text-sm">
            <thead class="bg-slate-50 border-b">
                <tr>
                    <th class="px-4 py-3 text-right">النوع</th>
                    <th class="px-4 py-3 text-center">عملاء</th>
                    <th class="px-4 py-3 text-center">متخصصون</th>
                    <th class="px-4 py-3 text-center">الحالة</th>
                    <th class="px-4 py-3 text-center">إجراءات</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @foreach($types as $type)
                    <tr>
                        <td class="px-4 py-3">
                            <form method="post" action="{{ route('admin.sales.interest-types.update', $type) }}" class="flex flex-wrap items-center gap-2">
                                @csrf @method('PUT')
                                <span class="w-3 h-3 rounded-full" style="background:{{ $type->color }}"></span>
                                <input type="text" name="name_ar" value="{{ $type->name_ar }}" class="rounded-lg border px-2 py-1 text-sm w-36">
                                <input type="text" name="name_en" value="{{ $type->name_en }}" class="rounded-lg border px-2 py-1 text-sm w-28" placeholder="EN">
                                <input type="color" name="color" value="{{ $type->color }}" class="h-8 w-10 rounded border">
                                <input type="number" name="sort_order" value="{{ $type->sort_order }}" class="rounded-lg border px-2 py-1 w-16 text-sm">
                                <label class="text-xs flex items-center gap-1"><input type="checkbox" name="is_active" value="1" @checked($type->is_active)> نشط</label>
                                <button class="text-xs font-semibold text-sky-700">حفظ</button>
                            </form>
                        </td>
                        <td class="px-4 py-3 text-center tabular-nums">{{ $type->leads_count }}</td>
                        <td class="px-4 py-3 text-center tabular-nums">{{ $type->specialists_count }}</td>
                        <td class="px-4 py-3 text-center">{{ $type->is_active ? 'نشط' : 'معطّل' }}</td>
                        <td class="px-4 py-3 text-center">
                            <form method="post" action="{{ route('admin.sales.interest-types.destroy', $type) }}" onsubmit="return confirm('حذف؟');">
                                @csrf @method('DELETE')
                                <button class="text-xs text-rose-600 font-semibold">حذف</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </section>
</div>
@endsection
