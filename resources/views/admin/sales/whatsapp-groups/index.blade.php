@extends('layouts.admin')

@section('title', 'مجموعات واتساب')
@section('header', 'مجموعات واتساب — المبيعات')

@section('content')
@include('employee.sales.whatsapp-groups._styles')

@php $r = fn($name, ...$p) => route('admin.sales.whatsapp-groups.'.$name, ...$p); @endphp

<div class="p-4 md:p-6 space-y-4">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h2 class="text-xl font-bold text-slate-900">مجموعات واتساب</h2>
            <p class="text-sm text-slate-500 mt-0.5">إنشاء وإدارة مجموعات واتساب الحقيقية من المنصة</p>
        </div>
        <a href="{{ $r('create') }}" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-sm font-semibold">
            <i class="fas fa-plus ml-1"></i> مجموعة جديدة
        </a>
    </div>

    @if(session('success'))<div class="rounded-lg border border-emerald-200 bg-emerald-50 text-emerald-800 px-4 py-2 text-sm">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="rounded-lg border border-rose-200 bg-rose-50 text-rose-800 px-4 py-2 text-sm">{{ session('error') }}</div>@endif

    <div class="sales-panel p-4 text-sm {{ ($bridge['connected'] ?? false) ? 'border-emerald-200 bg-emerald-50/50' : 'border-amber-200 bg-amber-50/50' }}">
        <p class="font-bold mb-1"><i class="fab fa-whatsapp text-emerald-600 ml-1"></i> جلسة الجسر</p>
        <p>{{ ($bridge['connected'] ?? false) ? 'متصل' : ($bridge['error'] ?? 'غير متصل') }}</p>
    </div>

    @include('employee.sales.whatsapp-groups._list', ['groups' => $groups, 'r' => $r])
</div>
@endsection
