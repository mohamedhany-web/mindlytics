@extends('layouts.admin')

@section('title', 'مجموعات واتساب')
@section('header', 'مجموعات واتساب — المبيعات')

@section('content')
@include('employee.sales.whatsapp-groups._styles')

@php
    $r = fn($name, ...$p) => route('admin.sales.whatsapp-groups.'.$name, ...$p);
    $settingsUrl = route('admin.whatsapp.settings');
@endphp

<div class="space-y-4">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h2 class="text-xl font-bold text-slate-900">مجموعات واتساب</h2>
            <p class="text-sm text-slate-500 mt-0.5">إنشاء وإدارة مجموعات Meta Cloud من المنصة</p>
        </div>
        <a href="{{ $r('create') }}" class="btn-wa-primary">
            <i class="fas fa-plus"></i> مجموعة جديدة
        </a>
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

    @if($groups->isEmpty())
        <div class="sales-panel p-10 text-center">
            <i class="fab fa-whatsapp text-3xl text-slate-300 mb-3"></i>
            <p class="text-slate-600 mb-4">لا توجد مجموعات واتساب</p>
            <a href="{{ $r('create') }}" class="btn-wa-primary">إنشاء مجموعة</a>
        </div>
    @else
        <div class="sales-panel overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 border-b border-slate-100">
                    <tr>
                        <th class="text-right p-3 font-semibold text-slate-600">المجموعة</th>
                        <th class="text-right p-3 font-semibold text-slate-600">CRM</th>
                        <th class="text-right p-3 font-semibold text-slate-600">مدعوون</th>
                        <th class="text-right p-3 font-semibold text-slate-600">الحالة</th>
                        <th class="text-right p-3 font-semibold text-slate-600">أنشأها</th>
                        <th class="p-3"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($groups as $group)
                        <tr class="border-t border-slate-100 hover:bg-slate-50/50">
                            <td class="p-3">
                                <p class="font-semibold text-slate-900">{{ $group->subject }}</p>
                                @if($group->description)
                                    <p class="text-xs text-slate-500 truncate max-w-xs">{{ $group->description }}</p>
                                @endif
                            </td>
                            <td class="p-3 text-xs text-slate-600">{{ $group->salesLeadGroup?->name ?? '—' }}</td>
                            <td class="p-3 tabular-nums">{{ $group->participants_count }}</td>
                            <td class="p-3">
                                <span class="text-[10px] px-2 py-0.5 rounded-md font-semibold {{ $group->isActive() ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-600' }}">{{ $group->statusLabel() }}</span>
                            </td>
                            <td class="p-3 text-xs text-slate-600">{{ $group->creator?->name ?? '—' }}</td>
                            <td class="p-3 text-left">
                                <a href="{{ $r('show', $group) }}" class="text-sky-700 font-semibold text-sm hover:underline">إدارة</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            @if($groups->hasPages())
                <div class="p-3 border-t border-slate-100">{{ $groups->links() }}</div>
            @endif
        </div>
    @endif
</div>
@endsection
