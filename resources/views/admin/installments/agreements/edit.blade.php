@extends('layouts.admin')

@section('title', 'تعديل اتفاقية التقسيط')
@section('header', 'تعديل اتفاقية التقسيط')

@section('content')
@php
    $agreement = $agreement ?? null;
    $plans = $plans ?? collect();
@endphp
<div class="container mx-auto px-4 py-8 space-y-8">
    <div class="bg-gradient-to-br from-amber-500 via-amber-600 to-sky-600 rounded-3xl shadow-xl text-white p-8 relative overflow-hidden">
        <div class="absolute inset-y-0 right-0 w-1/3 pointer-events-none opacity-20">
            <div class="w-full h-full bg-[url('https://www.transparenttextures.com/patterns/cubes.png')]"></div>
        </div>
        <div class="relative flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
            <div>
                <div class="flex items-center gap-3 flex-wrap">
                    <h1 class="text-3xl font-black tracking-tight">{{ $agreement->student->name ?? 'طالب غير معروف' }}</h1>
                    <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold bg-white/20">
                        <i class="fas fa-pen text-xs"></i>
                        تحديث تفاصيل الاتفاقية
                    </span>
                </div>
                <p class="mt-3 text-white/80 max-w-2xl">
                    يمكنك تغيير حالة الاتفاقية أو نقلها إلى خطة أخرى، بالإضافة إلى إضافة ملاحظات إدارية.
                </p>
            </div>
            <div class="flex flex-wrap gap-3 justify-end">
                <a href="{{ route('admin.installments.agreements.show', $agreement) }}" class="inline-flex items-center gap-2 px-5 py-3 rounded-2xl bg-white text-amber-700 font-semibold shadow-lg hover:shadow-xl transition-all">
                    <i class="fas fa-arrow-right"></i>
                    العودة للتفاصيل
                </a>
            </div>
        </div>
    </div>

    <div class="max-w-3xl mx-auto bg-white rounded-3xl shadow-lg border border-gray-100 p-8 space-y-6">
        <form action="{{ route('admin.installments.agreements.update', $agreement) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                    <label class="block text-sm font-semibold text-gray-700">الخطة المرتبطة</label>
                    <select name="installment_plan_id" disabled class="w-full px-4 py-3 rounded-2xl border border-gray-200 bg-gray-100 text-gray-500">
                        <option>{{ $agreement->plan->name ?? 'خطة عامة' }}</option>
                    </select>
                </div>
                <div class="space-y-2">
                    <label class="block text-sm font-semibold text-gray-700">حالة الاتفاقية *</label>
                    <select name="status" class="w-full px-4 py-3 rounded-2xl border border-gray-200 bg-gray-50 text-gray-900 focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                        @foreach($statuses as $value => $label)
                            <option value="{{ $value }}" {{ old('status', $agreement->status) === $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('status')
                        <p class="mt-2 text-sm text-rose-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="space-y-2">
                <label class="block text-sm font-semibold text-gray-700">ملاحظات إدارية</label>
                <textarea name="notes" rows="4" class="w-full px-4 py-3 rounded-2xl border border-gray-200 bg-gray-50 text-gray-900 focus:ring-2 focus:ring-amber-500 focus:border-amber-500">{{ old('notes', $agreement->notes) }}</textarea>
            </div>

            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('admin.installments.agreements.show', $agreement) }}" class="px-4 py-2 rounded-xl border border-gray-200 text-gray-600 hover:bg-gray-100">إلغاء</a>
                <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 bg-amber-600 hover:bg-amber-700 text-white rounded-xl shadow">
                    <i class="fas fa-save"></i>
                    تحديث الاتفاقية
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
