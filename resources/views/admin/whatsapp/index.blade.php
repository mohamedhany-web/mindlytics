@extends('layouts.admin')

@section('title', 'لوحة الواتساب — Meta Cloud API')
@section('header', 'قسم الواتساب')

@section('content')
@php
    $isConnected = (bool) ($connectionMeta['can_send'] ?? false);
@endphp

<div class="p-3 sm:p-4 md:p-6 space-y-4 sm:space-y-6" style="background: #f8fafc; min-height: 100vh;">
    @include('admin.whatsapp._alerts')
    @include('admin.whatsapp._nav', ['active' => 'dashboard'])

    @include('admin.whatsapp._page-header', [
        'title' => 'WhatsApp Business — Meta Cloud API',
        'subtitle' => 'إرسال رسمي وموثق عبر Meta — بدون Bridge أو QR.',
        'icon' => 'fab fa-whatsapp',
        'actions' => '
            <a href="' . route('admin.whatsapp.inbox') . '" class="' . $waBtnPrimary . '"><i class="fas fa-inbox"></i> المحادثات</a>
            <a href="' . route('admin.whatsapp.settings') . '" class="' . $waBtnSecondary . '"><i class="fas fa-plug"></i> إعدادات الربط</a>
            <a href="' . route('admin.whatsapp.send') . '" class="' . $waBtnSecondary . '"><i class="fas fa-paper-plane"></i> إرسال</a>
        ',
        'statCards' => [
            ['label' => 'إجمالي الرسائل', 'value' => number_format($stats['total'] ?? 0), 'icon' => 'fas fa-comments', 'bg' => 'bg-sky-100', 'text' => 'text-sky-600'],
            ['label' => 'مرسلة اليوم', 'value' => number_format($stats['sent_today'] ?? 0), 'icon' => 'fas fa-paper-plane', 'bg' => 'bg-emerald-100', 'text' => 'text-emerald-600'],
            ['label' => 'فاشلة', 'value' => number_format($stats['failed'] ?? 0), 'icon' => 'fas fa-times-circle', 'bg' => 'bg-rose-100', 'text' => 'text-rose-600'],
            ['label' => 'حالة Meta', 'value' => $isConnected ? 'متصل' : 'غير مربوط', 'icon' => 'fab fa-meta', 'bg' => 'bg-violet-100', 'text' => 'text-violet-600'],
        ],
    ])

    @if(!$isConnected)
        <div class="rounded-2xl border-2 border-amber-300 bg-amber-50 p-5 flex flex-wrap items-center justify-between gap-4">
            <div>
                <h3 class="font-bold text-amber-900">WhatsApp Business غير مربوط بعد</h3>
                <p class="text-sm text-amber-800 mt-1">أكمل إعداد Meta من صفحة الإعدادات: App ID و App Secret و Access Token و Phone Number ID.</p>
            </div>
            <a href="{{ route('admin.whatsapp.settings') }}" class="{{ $waBtnPrimary }}">بدء الربط</a>
        </div>
    @else
        <section class="{{ $waSectionClass }}">
            <div class="p-5 grid sm:grid-cols-2 lg:grid-cols-4 gap-4 text-sm">
                <div class="rounded-xl border border-emerald-200 bg-emerald-50/50 p-4">
                    <p class="text-xs text-emerald-700">الرقم</p>
                    <p class="font-bold text-emerald-900">{{ $connectionMeta['display_phone'] ?? $connection?->display_phone_number ?? '—' }}</p>
                </div>
                <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                    <p class="text-xs text-slate-500">الاسم</p>
                    <p class="font-bold">{{ $connectionMeta['display_name'] ?? $connection?->verified_display_name ?? '—' }}</p>
                </div>
                <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                    <p class="text-xs text-slate-500">Phone Number ID</p>
                    <p class="font-mono text-xs break-all">{{ $connection?->phone_number_id ?? ($connectionMeta['phone_number_id'] ?? '—') }}</p>
                </div>
                <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                    <p class="text-xs text-slate-500">WABA ID</p>
                    <p class="font-mono text-xs break-all">{{ $connection?->waba_id ?? ($connectionMeta['business_account_id'] ?? '—') }}</p>
                </div>
            </div>
        </section>
    @endif

    <section class="{{ $waSectionClass }}">
        <div class="px-5 py-4 border-b border-slate-200">
            <h3 class="font-bold text-slate-900">حدود الإرسال اليوم</h3>
        </div>
        <div class="p-5 text-sm text-slate-700">
            اليوم: <strong>{{ $pacingStats['day'] ?? 0 }}/{{ $pacingStats['max_day'] ?? 0 }}</strong>
            · هذه الساعة: <strong>{{ $pacingStats['hour'] ?? 0 }}/{{ $pacingStats['max_hour'] ?? 0 }}</strong>
        </div>
    </section>
</div>
@endsection
