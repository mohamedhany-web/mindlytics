@extends('layouts.admin')

@section('title', 'تعديل اتفاقية التقسيط')
@section('header', 'تعديل اتفاقية التقسيط')

@section('content')
@php
    $agreement = $agreement ?? null;
    $plans = $plans ?? collect();
@endphp
<div class="space-y-6">
    @include('admin.installments.partials.header', [
        'title' => $agreement->student->name ?? 'تعديل اتفاقية',
        'description' => 'تغيير حالة الاتفاقية أو نقلها لخطة أخرى وإضافة ملاحظات.',
        'icon' => 'fa-edit',
        'iconGradient' => 'from-amber-500 to-orange-500',
        'actions' => [
            ['route' => 'admin.installments.agreements.show', 'label' => 'التفاصيل', 'icon' => 'fa-eye', 'params' => [$agreement]],
            ['route' => 'admin.installments.agreements.index', 'label' => 'الاتفاقيات', 'icon' => 'fa-list'],
        ],
    ])
    @include('admin.installments.partials.nav', ['active' => 'agreements'])

    <section class="max-w-3xl mx-auto rounded-2xl bg-white border border-slate-200 shadow-lg p-6 sm:p-8">
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
    </section>
</div>
@endsection
