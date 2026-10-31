@extends('layouts.admin')

@section('title', 'إعدادات التقرير اليومي')
@section('header', 'إعدادات التقرير اليومي للموظفين')

@section('content')
<div class="max-w-2xl">
    <a href="{{ route('admin.employee-daily-reports.index') }}" class="text-sm text-slate-600 mb-4 inline-block"><i class="fas fa-arrow-right ml-1"></i> العودة</a>
    @if(session('success'))<div class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 text-emerald-800 px-4 py-3 text-sm">{{ session('success') }}</div>@endif
    <form method="post" action="{{ route('admin.employee-daily-reports.settings.update') }}" class="rounded-2xl bg-white border p-6 space-y-4">
        @csrf @method('PUT')
        <label class="flex items-center gap-2"><input type="checkbox" name="enabled" value="1" class="rounded" @checked($settings['enabled'] ?? true)> تفعيل التقارير اليومية</label>
        <label class="flex items-center gap-2"><input type="checkbox" name="penalty_enabled" value="1" class="rounded" @checked($settings['penalty_enabled'] ?? true)> تفعيل الغرامة التلقائية</label>
        <label class="flex items-center gap-2"><input type="checkbox" name="exclude_sales_employees" value="1" class="rounded" @checked($settings['exclude_sales_employees'] ?? true)> استثناء موظفي المبيعات (لديهم نظام منفصل)</label>
        <div>
            <label class="block text-sm font-semibold mb-1">مبلغ الغرامة (ج.م)</label>
            <input type="number" name="penalty_amount" step="0.01" min="0" value="{{ $settings['penalty_amount'] ?? 50 }}" class="w-full rounded-xl border px-3 py-2 text-sm">
        </div>
        <div>
            <label class="block text-sm font-semibold mb-1">سريان الغرامة اعتباراً من</label>
            <input type="date" name="penalty_effective_from" value="{{ $settings['penalty_effective_from'] ?? '' }}" class="w-full rounded-xl border px-3 py-2 text-sm">
            <p class="text-xs text-slate-500 mt-1">اتركه فارغاً للاعتماد على تاريخ تعيين كل موظف. لن تُحتسب غرامة عن أي يوم قبل هذا التاريخ.</p>
        </div>
        <button type="submit" class="px-6 py-2.5 bg-sky-600 text-white rounded-xl font-semibold text-sm">حفظ</button>
    </form>
</div>
@endsection
