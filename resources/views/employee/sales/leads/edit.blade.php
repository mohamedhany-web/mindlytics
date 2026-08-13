@extends('layouts.employee')

@section('title', 'تعديل '.$lead->name)
@section('header', 'تعديل بيانات العميل')

@push('styles')
@include('employee.sales._styles')
<style>
    .sales-hub .dashboard-card,
    .sales-hub .panel-card {
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
    }
    .sales-hub .dashboard-card::before { display: none; }
    .sales-hub .dashboard-card:hover { transform: none; box-shadow: 0 2px 8px rgba(15, 23, 42, 0.06); }
    .sales-hub .panel-card-head {
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
    }
</style>
@endpush

@section('content')
<div class="space-y-5 sales-hub pb-8">
    <div class="dashboard-card flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div class="min-w-0">
            <a href="{{ route('employee.sales.leads.show', $lead) }}" class="text-sm text-teal-700 font-semibold hover:underline inline-flex items-center gap-1 mb-1">
                <i class="fas fa-arrow-right text-xs"></i> العودة لتفاصيل العميل
            </a>
            <h2 class="text-2xl font-bold text-slate-900">تعديل بيانات {{ $lead->name }}</h2>
            <p class="text-sm text-slate-500 mt-1">هنا تعدّل البيانات فقط. نقل المرحلة من صفحة التفاصيل عبر الـ Pipeline.</p>
        </div>
        <div class="flex flex-wrap items-center gap-2 shrink-0">
            <span class="px-2.5 py-1 rounded-full bg-teal-100 text-teal-800 text-xs font-bold">{{ \App\Models\SalesLead::stageLabel($lead->stage) }}</span>
            <a href="{{ route('employee.sales.leads.show', $lead) }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-slate-800 hover:bg-slate-900 text-white text-sm font-semibold">
                فتح الـ Pipeline
            </a>
        </div>
    </div>

    @if($errors->any())
        <div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-900">
            <p class="font-bold mb-2"><i class="fas fa-exclamation-circle ml-1"></i> لم يتم الحفظ — راجع الحقول:</p>
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="post" action="{{ route('employee.sales.leads.update', $lead) }}" class="grid grid-cols-1 xl:grid-cols-12 gap-5 items-start">
        @csrf
        @method('PUT')

        <div class="xl:col-span-9 space-y-5">
            @include('employee.sales._lead_fields', ['lead' => $lead, 'groups' => $groups ?? collect()])

            <div class="flex flex-wrap gap-2">
                <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 rounded-lg bg-teal-600 hover:bg-teal-700 text-white text-sm font-bold">
                    حفظ التعديلات
                </button>
                <a href="{{ route('employee.sales.leads.show', $lead) }}" class="inline-flex items-center px-6 py-2.5 rounded-lg border border-slate-200 bg-white text-slate-700 text-sm font-semibold hover:bg-slate-50">إلغاء</a>
            </div>
        </div>

        <aside class="xl:col-span-3 space-y-4 xl:sticky xl:top-20">
            <div class="panel-card p-4 text-sm space-y-2">
                <p class="font-bold text-slate-800">المرحلة الحالية</p>
                <p class="text-teal-800 font-semibold">{{ \App\Models\SalesLead::stageLabel($lead->stage) }}</p>
                <p class="text-slate-500 text-xs leading-relaxed">لتغيير المرحلة استخدم أزرار الـ Pipeline في صفحة التفاصيل، مش من هنا.</p>
            </div>
            <div class="panel-card p-4 text-sm space-y-2">
                <p class="text-slate-500">الهاتف</p>
                <p class="font-semibold text-slate-900 break-all">{{ $lead->phone ?: '—' }}</p>
                <p class="text-slate-500 pt-2">آخر تواصل</p>
                <p class="font-semibold text-slate-900">{{ $lead->last_contacted_at?->format('Y-m-d H:i') ?? '—' }}</p>
            </div>
        </aside>
    </form>
</div>
@endsection
