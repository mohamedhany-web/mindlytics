@extends('layouts.employee')

@section('title', 'مجموعات واتساب')
@section('header', 'مجموعات واتساب')

@section('content')
@include('employee.sales.whatsapp-groups._styles')

@php
    $r = fn($name, ...$p) => route('employee.sales.whatsapp-groups.'.$name, ...$p);
    $settingsUrl = null;
@endphp

<div class="space-y-4">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h2 class="text-xl font-bold text-slate-900">مجموعات واتساب</h2>
            <p class="text-sm text-slate-500 mt-0.5">إنشاء مجموعات Meta Cloud وإرسال دعوات للعملاء</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('employee.sales.dashboard') }}" class="btn-wa-secondary">مركز المبيعات</a>
            <a href="{{ $r('create') }}" class="btn-wa-primary">
                <i class="fas fa-plus"></i> مجموعة جديدة
            </a>
        </div>
    </div>

    @if(session('success'))<div class="rounded-lg border border-emerald-200 bg-emerald-50 text-emerald-800 px-4 py-2 text-sm">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="rounded-lg border border-rose-200 bg-rose-50 text-rose-800 px-4 py-2 text-sm">{{ session('error') }}</div>@endif

    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
        <div class="stat-card">
            <p class="text-xs text-slate-500">المجموعات</p>
            <p class="text-2xl font-bold text-slate-900 tabular-nums">{{ $stats['total'] ?? 0 }}</p>
        </div>
        <div class="stat-card">
            <p class="text-xs text-slate-500">نشطة</p>
            <p class="text-2xl font-bold text-emerald-700 tabular-nums">{{ $stats['active'] ?? 0 }}</p>
        </div>
        <div class="stat-card">
            <p class="text-xs text-slate-500">المدعوون</p>
            <p class="text-2xl font-bold text-slate-900 tabular-nums">{{ $stats['participants'] ?? 0 }}</p>
        </div>
        <div class="stat-card">
            <p class="text-xs text-slate-500">Meta Cloud</p>
            <p class="text-sm font-bold {{ ($cloud['connected'] ?? false) ? 'text-emerald-700' : 'text-amber-700' }} mt-1">
                {{ ($cloud['connected'] ?? false) ? 'متصل' : 'غير جاهز' }}
            </p>
        </div>
    </div>

    @include('employee.sales.whatsapp-groups._cloud_status', ['cloud' => $cloud, 'settingsUrl' => $settingsUrl])

    @include('employee.sales.whatsapp-groups._list', ['groups' => $groups, 'r' => $r])
</div>
@endsection
