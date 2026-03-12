@extends('layouts.admin')

@section('title', 'تفاصيل الخصم - Mindlytics')
@section('header', 'تفاصيل الخصم')

@section('content')
<div class="p-3 sm:p-4 md:p-6 space-y-4 sm:space-y-6" style="background: #f8fafc; min-height: 100vh;">
    <section class="rounded-2xl bg-white/95 backdrop-blur border-2 border-slate-200/50 shadow-xl overflow-hidden">
        <div class="px-5 py-6 sm:px-8 lg:px-12 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-rose-500 to-red-600 flex items-center justify-center text-white shadow-lg">
                    <i class="fas fa-minus-circle text-xl"></i>
                </div>
                <div>
                    <h2 class="text-2xl font-black text-slate-900">{{ $employeeDeduction->deduction_number }}</h2>
                    <p class="text-sm text-slate-600 mt-1">{{ $employeeDeduction->title }}</p>
                </div>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('admin.employee-deductions.edit', $employeeDeduction) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded-xl font-semibold text-sm">
                    <i class="fas fa-edit"></i> تعديل
                </a>
                <a href="{{ route('admin.employee-deductions.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-semibold text-sm">
                    <i class="fas fa-arrow-right"></i> رجوع
                </a>
            </div>
        </div>
        <div class="px-5 py-6 sm:px-8 lg:px-12">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <p class="text-xs font-semibold text-slate-500 mb-1">الموظف</p>
                    <p class="text-sm font-semibold text-slate-900">{{ $employeeDeduction->employee->name ?? '—' }}</p>
                    @if($employeeDeduction->employee)
                        <p class="text-xs text-slate-500">{{ $employeeDeduction->employee->email }}</p>
                    @endif
                </div>
                <div>
                    <p class="text-xs font-semibold text-slate-500 mb-1">الاتفاقية</p>
                    @if($employeeDeduction->agreement)
                        <a href="{{ route('admin.employee-agreements.show', $employeeDeduction->agreement) }}" class="text-sm font-semibold text-rose-600 hover:underline">{{ $employeeDeduction->agreement->agreement_number }}</a>
                        <p class="text-xs text-slate-500">{{ $employeeDeduction->agreement->title }}</p>
                    @else
                        <p class="text-sm text-slate-500">—</p>
                    @endif
                </div>
                <div>
                    <p class="text-xs font-semibold text-slate-500 mb-1">نوع الخصم</p>
                    @php $typeLabels = ['tax' => 'ضريبة', 'insurance' => 'تأمين', 'loan' => 'قرض', 'penalty' => 'غرامة', 'other' => 'أخرى']; @endphp
                    <span class="inline-flex px-3 py-1 rounded-full text-sm font-semibold bg-slate-100 text-slate-800">{{ $typeLabels[$employeeDeduction->type] ?? $employeeDeduction->type }}</span>
                </div>
                <div>
                    <p class="text-xs font-semibold text-slate-500 mb-1">المبلغ</p>
                    <p class="text-xl font-bold text-rose-600">{{ number_format($employeeDeduction->amount, 2) }} ج.م</p>
                </div>
                <div>
                    <p class="text-xs font-semibold text-slate-500 mb-1">تاريخ الخصم</p>
                    <p class="text-sm font-semibold text-slate-900">{{ $employeeDeduction->deduction_date?->format('Y-m-d') }}</p>
                </div>
                <div>
                    <p class="text-xs font-semibold text-slate-500 mb-1">الحالة</p>
                    @if($employeeDeduction->status === 'pending')
                        <span class="px-3 py-1 rounded-full text-sm font-semibold bg-amber-100 text-amber-800">معلقة</span>
                    @elseif($employeeDeduction->status === 'applied')
                        <span class="px-3 py-1 rounded-full text-sm font-semibold bg-emerald-100 text-emerald-800">مطبقة</span>
                    @else
                        <span class="px-3 py-1 rounded-full text-sm font-semibold bg-slate-100 text-slate-800">ملغاة</span>
                    @endif
                </div>
            </div>
            @if($employeeDeduction->description)
                <div class="mt-6 pt-4 border-t border-slate-200">
                    <p class="text-xs font-semibold text-slate-500 mb-1">الوصف</p>
                    <p class="text-sm text-slate-700 whitespace-pre-line">{{ $employeeDeduction->description }}</p>
                </div>
            @endif
            @if($employeeDeduction->notes)
                <div class="mt-4">
                    <p class="text-xs font-semibold text-slate-500 mb-1">ملاحظات</p>
                    <p class="text-sm text-slate-700 whitespace-pre-line">{{ $employeeDeduction->notes }}</p>
                </div>
            @endif
            @if($employeeDeduction->creator)
                <div class="mt-4 pt-4 border-t border-slate-200 text-xs text-slate-500">
                    أنشئ بواسطة: {{ $employeeDeduction->creator->name }} — {{ $employeeDeduction->created_at?->format('Y-m-d H:i') }}
                </div>
            @endif
        </div>
    </section>
</div>
@endsection
