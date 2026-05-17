@extends('layouts.admin')

@section('title', 'تفاصيل التقرير اليومي')
@section('header', 'تفاصيل التقرير اليومي')

@section('content')
<div class="p-4 md:p-6 space-y-6 max-w-5xl">
    <a href="{{ route('admin.sales.daily-reports.index') }}" class="text-sm text-emerald-700 font-semibold"><i class="fas fa-arrow-right ml-1"></i> العودة</a>

    <div class="bg-white rounded-2xl border p-6">
        <p class="text-sm text-slate-500">{{ $report->report_date->format('Y-m-d') }} — {{ $report->user->name ?? '' }}</p>
        <p class="mt-1 font-bold @if($report->isSubmitted()) text-emerald-700 @else text-amber-700 @endif">
            {{ $report->isSubmitted() ? 'مسلّم' : 'مسودة' }}
            @if($report->autoDeduction)
                <span class="text-rose-600 text-sm mr-2">| خصم: {{ $report->autoDeduction->deduction_number }} ({{ number_format($report->autoDeduction->amount, 2) }} ج.م)</span>
            @endif
        </p>
    </div>

    <div class="grid md:grid-cols-2 gap-6">
        <section class="bg-white rounded-2xl border p-5">
            <h3 class="font-bold mb-3">نشاط اليوم</h3>
            <dl class="text-sm space-y-1">
                <div class="flex justify-between"><dt>ردود رسائل</dt><dd class="font-bold">{{ $report->messages_replied }}</dd></div>
                <div class="flex justify-between"><dt>مؤهلون</dt><dd class="font-bold">{{ $report->leads_qualified }}</dd></div>
                <div class="flex justify-between"><dt>حجوزات</dt><dd class="font-bold">{{ $report->bookings_from_leads }}</dd></div>
            </dl>
            @if($report->activity_notes)<p class="mt-3 text-xs text-slate-600 whitespace-pre-wrap">{{ $report->activity_notes }}</p>@endif
        </section>
        <section class="bg-white rounded-2xl border p-5">
            <h3 class="font-bold mb-3">الإنتاجية</h3>
            <dl class="text-sm space-y-1">
                <div class="flex justify-between"><dt>أرقام</dt><dd class="font-bold">{{ $report->numbers_worked }}</dd></div>
                <div class="flex justify-between"><dt>متابعات</dt><dd class="font-bold">{{ $report->followups_done }}</dd></div>
                <div class="flex justify-between"><dt>مكالمات / اجتماعات / ردود</dt><dd class="font-bold">{{ $report->calls_made }} / {{ $report->meetings_held }} / {{ $report->calls_answered }}</dd></div>
            </dl>
            @if($report->productivity_notes)<p class="mt-3 text-xs text-slate-600 whitespace-pre-wrap">{{ $report->productivity_notes }}</p>@endif
        </section>
    </div>

    <section class="bg-white rounded-2xl border overflow-hidden">
        <h3 class="px-5 py-3 font-bold border-b bg-slate-50">المكالمات والاجتماعات (حالة العميل والمشاكل)</h3>
        <div class="divide-y">
            @forelse($report->contacts as $c)
                <div class="p-5 text-sm">
                    <p class="font-bold">{{ $c->interactionTypeLabel() }} — {{ $c->contact_name ?: '—' }} — {{ $c->contact_phone }}</p>
                    @if($c->lead)<p class="text-xs text-emerald-700">Lead: {{ $c->lead->name }}</p>@endif
                    <p class="mt-2"><span class="font-semibold text-slate-600">الحالة:</span> {{ $c->client_status }}</p>
                    <p class="mt-1"><span class="font-semibold text-rose-700">المشاكل:</span> {{ $c->client_problems }}</p>
                </div>
            @empty
                <p class="p-5 text-slate-500">لا توجد سجلات تواصل</p>
            @endforelse
        </div>
    </section>
</div>
@endsection
