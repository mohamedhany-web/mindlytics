@extends('layouts.admin')

@section('title', $employee_addition->addition_number)
@section('header', 'إضافة — ' . $employee_addition->title)

@section('content')
<div class="max-w-2xl space-y-4">
    <a href="{{ route('admin.employee-additions.index') }}" class="text-sm text-slate-600"><i class="fas fa-arrow-right ml-1"></i> العودة</a>
    <div class="rounded-2xl bg-white border p-6 space-y-3">
        <p><strong>الرقم:</strong> {{ $employee_addition->addition_number }}</p>
        <p><strong>الموظف:</strong> {{ $employee_addition->employee->name ?? '—' }}</p>
        <p><strong>المبلغ:</strong> <span class="text-emerald-700 font-bold">{{ number_format($employee_addition->amount, 2) }} ج.م</span></p>
        <p><strong>النوع:</strong> {{ \App\Models\EmployeeSalaryAddition::typeLabels()[$employee_addition->type] ?? '' }}</p>
        <p><strong>التاريخ:</strong> {{ $employee_addition->addition_date->format('Y-m-d') }}</p>
        <p><strong>الحالة:</strong> {{ $employee_addition->status }}</p>
        @if($employee_addition->description)<p class="whitespace-pre-wrap">{{ $employee_addition->description }}</p>@endif
        <div class="flex flex-wrap gap-2 pt-2">
            <a href="{{ route('admin.employee-additions.edit', $employee_addition) }}" class="px-4 py-2 bg-slate-800 text-white rounded-lg text-sm font-semibold">تعديل</a>
        </div>
    </div>
</div>
@endsection
