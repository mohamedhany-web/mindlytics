@extends('layouts.admin')

@section('title', 'مجموعة واتساب جديدة')
@section('header', 'مجموعة واتساب جديدة')

@section('content')
@include('employee.sales.whatsapp-groups._styles')

@php
    $r = fn($name, ...$p) => route('admin.sales.whatsapp-groups.'.$name, ...$p);
    $settingsUrl = route('admin.whatsapp.settings');
@endphp

<div class="space-y-4 max-w-3xl">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h2 class="text-xl font-bold text-slate-900">مجموعة واتساب جديدة</h2>
            <p class="text-sm text-slate-500 mt-0.5">تُنشأ عبر Meta Cloud — الدعوات تُرسل بقالب Group Invite</p>
        </div>
        <a href="{{ $r('index') }}" class="btn-wa-secondary">← المجموعات</a>
    </div>

    @if(session('error'))<div class="rounded-lg border border-rose-200 bg-rose-50 text-rose-800 px-4 py-2 text-sm">{{ session('error') }}</div>@endif

    @include('employee.sales.whatsapp-groups._cloud_status', ['cloud' => $cloud, 'settingsUrl' => $settingsUrl])

    @include('employee.sales.whatsapp-groups._form_create', compact('r', 'crmGroups', 'prefillCrmGroupId', 'prefillParticipants', 'inviteTemplates'))
</div>
@endsection
