@extends('layouts.admin')

@section('title', 'إعدادات التقرير اليومي')
@section('header', 'إعدادات التقرير اليومي')

@section('content')
<div class="p-4 md:p-6 max-w-3xl">
    <a href="{{ route('admin.sales.daily-reports.index') }}" class="text-sm text-emerald-700 font-semibold mb-4 inline-block"><i class="fas fa-arrow-right ml-1"></i> التقارير اليومية</a>
    @if(session('success'))
        <div class="mb-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 text-sm font-semibold">{{ session('success') }}</div>
    @endif
    <div class="bg-white rounded-2xl border p-6">
        @include('admin.sales.daily-reports._settings_form', [
            'formAction' => route('admin.sales.daily-reports.settings.update'),
            'method' => 'PUT',
            'settings' => $settings,
        ])
    </div>
</div>
@endsection
