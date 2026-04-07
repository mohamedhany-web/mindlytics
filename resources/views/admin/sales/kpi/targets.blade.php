@extends('layouts.admin')

@section('title', 'أهداف KPIs المبيعات')
@section('header', 'أهداف KPIs المبيعات')

@php
    $labels = [
        'leads_daily' => 'Leads جديدة / يوم',
        'leads_weekly' => 'Leads جديدة / أسبوع',
        'deals_weekly' => 'صفقات مغلقة / أسبوع',
        'revenue_monthly' => 'إيراد شهري (ج.م) — قيمة متوقعة مكتملة',
        'calls_daily' => 'مكالمات / يوم',
        'meetings_daily' => 'اجتماعات أو ديمو / يوم',
        'followups_daily' => 'متابعات مسجّلة / يوم',
        'response_minutes_max' => 'أقصى متوسط أول رد (دقيقة)',
        'closing_ratio_pct_min' => 'أدنى نسبة إغلاق won/(won+lost) %',
        'csat_min' => 'أدنى متوسط CSAT (1–5)',
        'loss_ratio_max_pct' => 'أقصى نسبة خسارة مقبولة %',
        'open_opportunities_min' => 'أدنى فرص مفتوحة في الأنبوب',
        'sales_cycle_max_days' => 'أقصى متوسط دورة بيع (يوم)',
        'crm_activities_daily_min' => 'أدنى أنشطة CRM / يوم',
        'data_fresh_open_pct_min' => 'أدنى % فرص محدّثة خلال 7 أيام',
        'engagement_days_pct_min' => 'أدنى % أيام بتفاعل مسجّل',
        'conversion_pct_target' => 'هدف نسبة تحويل % (شهري)',
    ];
@endphp

@section('content')
<div class="w-full p-3 sm:p-6 space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <a href="{{ route('admin.sales.kpi.index') }}" class="inline-flex items-center gap-2 text-sm text-emerald-700 hover:text-emerald-800 hover:underline">
            <i class="fas fa-arrow-right"></i>
            لوحة المراقبة
        </a>
        <div class="text-xs sm:text-sm text-slate-500">
            إدارة أهداف فريق المبيعات
        </div>
    </div>

    @if(session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-emerald-900 text-sm font-medium">{{ session('success') }}</div>
    @endif

    @if($salesReps->isEmpty())
        <p class="text-gray-600">لا يوجد موظفو مبيعات.</p>
    @else
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 sm:p-6 lg:p-8">
        <form method="get" action="{{ route('admin.sales.kpi.targets') }}" class="grid grid-cols-1 md:grid-cols-3 xl:grid-cols-5 gap-4 items-end mb-8 pb-6 border-b border-slate-100">
            <div class="xl:col-span-2">
                <label class="block text-xs font-semibold text-slate-600 mb-1">الموظف</label>
                <select name="user_id" class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200 focus:border-emerald-500">
                    @foreach($salesReps as $sr)
                        <option value="{{ $sr->id }}" @selected((int) $userId === (int) $sr->id)>{{ $sr->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">الشهر</label>
                <input type="month" name="year_month" value="{{ $yearMonth }}" class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200 focus:border-emerald-500">
            </div>
            <button type="submit" class="md:col-span-1 xl:col-span-1 w-full md:w-auto px-4 py-2.5 bg-slate-800 hover:bg-slate-900 text-white rounded-xl text-sm font-bold transition-colors">عرض البيانات</button>
        </form>

        <form method="post" action="{{ route('admin.sales.kpi.targets.update') }}">
            @csrf
            @method('PUT')
            <input type="hidden" name="user_id" value="{{ $userId }}">
            <input type="hidden" name="year_month" value="{{ $yearMonth }}">

            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                @foreach($labels as $key => $label)
                    <div class="rounded-xl border border-slate-200 bg-slate-50/50 p-3">
                        <label class="block text-sm font-semibold text-slate-700 mb-2" for="t_{{ $key }}">{{ $label }}</label>
                        <input type="number" step="any" name="{{ $key }}" id="t_{{ $key }}" value="{{ old($key, $targets[$key] ?? '') }}"
                               class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200 focus:border-emerald-500">
                    </div>
                @endforeach
            </div>

            <div class="mt-8 flex flex-wrap gap-3 border-t border-slate-100 pt-5">
                <button type="submit" class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-bold transition-colors">حفظ الأهداف</button>
            </div>
        </form>
    </div>
    @endif
</div>
@endsection
