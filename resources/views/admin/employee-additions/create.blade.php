@extends('layouts.admin')

@section('title', 'إضافة خارجية')
@section('header', 'إضافة خارجية لموظف')

@section('content')
<div class="max-w-2xl">
    <form method="post" action="{{ route('admin.employee-additions.store') }}" class="rounded-2xl bg-white border p-6 space-y-4">
        @csrf
        <div>
            <label class="block text-sm font-semibold mb-1">الموظف *</label>
            <select name="employee_id" required class="w-full rounded-xl border px-3 py-2 text-sm">
                @foreach($employees as $emp)
                    <option value="{{ $emp->id }}" @selected(old('employee_id') == $emp->id)>{{ $emp->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-semibold mb-1">العنوان *</label>
            <input type="text" name="title" value="{{ old('title') }}" required class="w-full rounded-xl border px-3 py-2 text-sm" placeholder="مثال: مكافأة أداء">
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-semibold mb-1">المبلغ *</label>
                <input type="number" name="amount" step="0.01" min="0.01" value="{{ old('amount') }}" required class="w-full rounded-xl border px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-sm font-semibold mb-1">النوع</label>
                <select name="type" class="w-full rounded-xl border px-3 py-2 text-sm">
                    @foreach(\App\Models\EmployeeSalaryAddition::typeLabels() as $k => $label)
                        <option value="{{ $k }}" @selected(old('type', 'bonus') === $k)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-semibold mb-1">التاريخ</label>
                <input type="date" name="addition_date" value="{{ old('addition_date', today()->toDateString()) }}" required class="w-full rounded-xl border px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-sm font-semibold mb-1">الحالة</label>
                <select name="status" class="w-full rounded-xl border px-3 py-2 text-sm">
                    <option value="applied" @selected(old('status', 'applied') === 'applied')>مطبقة فوراً</option>
                    <option value="pending">معلقة</option>
                </select>
            </div>
        </div>
        <div>
            <label class="block text-sm font-semibold mb-1">الوصف</label>
            <textarea name="description" rows="2" class="w-full rounded-xl border px-3 py-2 text-sm">{{ old('description') }}</textarea>
        </div>
        <button type="submit" class="px-6 py-2.5 bg-emerald-600 text-white rounded-xl font-semibold text-sm">حفظ الإضافة</button>
    </form>
</div>
@endsection
