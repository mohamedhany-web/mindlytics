@extends('layouts.admin')

@section('title', 'تعديل إضافة خارجية')
@section('header', 'تعديل إضافة — ' . $employee_addition->addition_number)

@section('content')
<div class="max-w-2xl">
    <a href="{{ route('admin.employee-additions.show', $employee_addition) }}" class="inline-flex items-center gap-1 text-sm text-slate-600 mb-4 hover:text-slate-900">
        <i class="fas fa-arrow-right"></i> العودة للتفاصيل
    </a>

    <form method="post" action="{{ route('admin.employee-additions.update', $employee_addition) }}" class="rounded-2xl bg-white border p-6 space-y-4">
        @csrf
        @method('PUT')

        <div class="rounded-xl bg-slate-50 border border-slate-200 px-4 py-3 text-sm">
            <span class="text-slate-500">الموظف:</span>
            <span class="font-semibold text-slate-900">{{ $employee_addition->employee->name ?? '—' }}</span>
        </div>

        <div>
            <label class="block text-sm font-semibold mb-1">العنوان *</label>
            <input type="text" name="title" value="{{ old('title', $employee_addition->title) }}" required class="w-full rounded-xl border px-3 py-2 text-sm">
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-semibold mb-1">المبلغ *</label>
                <input type="number" name="amount" step="0.01" min="0.01" value="{{ old('amount', $employee_addition->amount) }}" required class="w-full rounded-xl border px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-sm font-semibold mb-1">النوع</label>
                <select name="type" class="w-full rounded-xl border px-3 py-2 text-sm">
                    @foreach(\App\Models\EmployeeSalaryAddition::typeLabels() as $k => $label)
                        <option value="{{ $k }}" @selected(old('type', $employee_addition->type) === $k)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-semibold mb-1">التاريخ</label>
                <input type="date" name="addition_date" value="{{ old('addition_date', $employee_addition->addition_date->format('Y-m-d')) }}" required class="w-full rounded-xl border px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-sm font-semibold mb-1">الحالة</label>
                <select name="status" class="w-full rounded-xl border px-3 py-2 text-sm">
                    <option value="applied" @selected(old('status', $employee_addition->status) === 'applied')>مطبقة</option>
                    <option value="pending" @selected(old('status', $employee_addition->status) === 'pending')>معلقة</option>
                    <option value="cancelled" @selected(old('status', $employee_addition->status) === 'cancelled')>ملغاة</option>
                </select>
            </div>
        </div>

        <div>
            <label class="block text-sm font-semibold mb-1">الوصف</label>
            <textarea name="description" rows="2" class="w-full rounded-xl border px-3 py-2 text-sm">{{ old('description', $employee_addition->description) }}</textarea>
        </div>

        <div>
            <label class="block text-sm font-semibold mb-1">ملاحظات داخلية</label>
            <textarea name="notes" rows="2" class="w-full rounded-xl border px-3 py-2 text-sm">{{ old('notes', $employee_addition->notes) }}</textarea>
        </div>

        <div class="flex flex-wrap gap-3">
            <button type="submit" class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-semibold text-sm">حفظ التعديلات</button>
            <a href="{{ route('admin.employee-additions.show', $employee_addition) }}" class="px-6 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-800 rounded-xl font-semibold text-sm">إلغاء</a>
        </div>
    </form>
</div>
@endsection
