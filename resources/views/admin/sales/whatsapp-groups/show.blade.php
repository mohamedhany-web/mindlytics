@extends('layouts.admin')

@section('title', $whatsappGroup->subject)
@section('header', 'مجموعة واتساب: '.$whatsappGroup->subject)

@section('content')
@include('employee.sales.whatsapp-groups._styles')

@php $r = fn($name, ...$p) => route('admin.sales.whatsapp-groups.'.$name, ...$p); @endphp

<div class="space-y-4">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <div class="flex flex-wrap items-center gap-2">
                <h2 class="text-xl font-bold text-slate-900">{{ $whatsappGroup->subject }}</h2>
                <span class="text-[10px] px-2 py-0.5 rounded-md font-semibold {{ $whatsappGroup->isActive() ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-600' }}">{{ $whatsappGroup->statusLabel() }}</span>
            </div>
            @if($whatsappGroup->description)
                <p class="text-sm text-slate-500 mt-1">{{ $whatsappGroup->description }}</p>
            @endif
        </div>
        <a href="{{ $r('index') }}" class="btn-wa-secondary">← المجموعات</a>
    </div>

    @if(session('success'))<div class="rounded-lg border border-emerald-200 bg-emerald-50 text-emerald-800 px-4 py-2 text-sm">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="rounded-lg border border-rose-200 bg-rose-50 text-rose-800 px-4 py-2 text-sm">{{ session('error') }}</div>@endif

    @include('employee.sales.whatsapp-groups._show_body', compact('r', 'whatsappGroup', 'inviteTemplates', 'availableLeads', 'crmGroups'))
</div>
@endsection
