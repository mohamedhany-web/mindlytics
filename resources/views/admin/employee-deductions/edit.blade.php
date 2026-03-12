@extends('layouts.admin')

@section('title', 'تعديل خصم الموظف - Mindlytics')
@section('header', 'تعديل خصم الموظف')

@section('content')
<div class="p-3 sm:p-4 md:p-6 space-y-4 sm:space-y-6" style="background: #f8fafc; min-height: 100vh;">
    <section class="rounded-2xl bg-white/95 backdrop-blur border-2 border-slate-200/50 shadow-xl overflow-hidden">
        <div class="px-5 py-6 sm:px-8 lg:px-12 border-b border-slate-200 bg-gradient-to-r from-slate-50 to-white">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-rose-500 to-red-600 flex items-center justify-center text-white shadow-lg">
                    <i class="fas fa-edit text-xl"></i>
                </div>
                <div>
                    <h2 class="text-2xl sm:text-3xl font-black text-slate-900">تعديل الخصم: {{ $employeeDeduction->deduction_number }}</h2>
                    <p class="text-sm text-slate-600 mt-1">تعديل بيانات الخصم المسجل للموظف</p>
                </div>
            </div>
        </div>
        @if($errors->any())
            <div class="mx-5 mt-4 p-4 rounded-xl bg-amber-50 border border-amber-200 text-amber-800">
                <p class="font-semibold mb-1"><i class="fas fa-exclamation-triangle ml-1"></i>يرجى تصحيح الأخطاء التالية:</p>
                <ul class="list-disc list-inside text-sm">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <form method="POST" action="{{ route('admin.employee-deductions.update', $employeeDeduction) }}" class="px-5 py-6 sm:px-8 lg:px-12">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">الموظف <span class="text-red-500">*</span></label>
                    <select name="employee_id" required class="w-full rounded-xl border-2 border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 focus:ring-2 focus:ring-rose-500 focus:border-rose-400">
                        @foreach($employees as $emp)
                            <option value="{{ $emp->id }}" {{ old('employee_id', $employeeDeduction->employee_id) == $emp->id ? 'selected' : '' }}>{{ $emp->name }} @if($emp->email)({{ $emp->email }})@endif</option>
                        @endforeach
                    </select>
                    @error('employee_id')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">الاتفاقية (اختياري)</label>
                    <select name="agreement_id" class="w-full rounded-xl border-2 border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 focus:ring-2 focus:ring-rose-500 focus:border-rose-400">
                        <option value="">— بدون ربط باتفاقية —</option>
                        @foreach($agreements as $agr)
                            <option value="{{ $agr->id }}" {{ old('agreement_id', $employeeDeduction->agreement_id) == $agr->id ? 'selected' : '' }}>{{ $agr->agreement_number }} — {{ $agr->employee->name ?? '' }}</option>
                        @endforeach
                    </select>
                    @error('agreement_id')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-slate-700 mb-2">عنوان الخصم <span class="text-red-500">*</span></label>
                    <input type="text" name="title" value="{{ old('title', $employeeDeduction->title) }}" required class="w-full rounded-xl border-2 border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 focus:ring-2 focus:ring-rose-500 focus:border-rose-400" />
                    @error('title')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-slate-700 mb-2">الوصف (اختياري)</label>
                    <textarea name="description" rows="2" class="w-full rounded-xl border-2 border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 focus:ring-2 focus:ring-rose-500 focus:border-rose-400">{{ old('description', $employeeDeduction->description) }}</textarea>
                    @error('description')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">المبلغ (ج.م) <span class="text-red-500">*</span></label>
                    <input type="number" name="amount" step="0.01" min="0.01" value="{{ old('amount', $employeeDeduction->amount) }}" required class="w-full rounded-xl border-2 border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 focus:ring-2 focus:ring-rose-500 focus:border-rose-400" />
                    @error('amount')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">نوع الخصم <span class="text-red-500">*</span></label>
                    <select name="type" required class="w-full rounded-xl border-2 border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 focus:ring-2 focus:ring-rose-500 focus:border-rose-400">
                        <option value="tax" {{ old('type', $employeeDeduction->type) == 'tax' ? 'selected' : '' }}>ضريبة</option>
                        <option value="insurance" {{ old('type', $employeeDeduction->type) == 'insurance' ? 'selected' : '' }}>تأمين</option>
                        <option value="loan" {{ old('type', $employeeDeduction->type) == 'loan' ? 'selected' : '' }}>قرض</option>
                        <option value="penalty" {{ old('type', $employeeDeduction->type) == 'penalty' ? 'selected' : '' }}>غرامة</option>
                        <option value="other" {{ old('type', $employeeDeduction->type) == 'other' ? 'selected' : '' }}>أخرى</option>
                    </select>
                    @error('type')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">تاريخ الخصم <span class="text-red-500">*</span></label>
                    <input type="date" name="deduction_date" value="{{ old('deduction_date', $employeeDeduction->deduction_date?->format('Y-m-d')) }}" required class="w-full rounded-xl border-2 border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 focus:ring-2 focus:ring-rose-500 focus:border-rose-400" />
                    @error('deduction_date')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">الحالة <span class="text-red-500">*</span></label>
                    <select name="status" required class="w-full rounded-xl border-2 border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 focus:ring-2 focus:ring-rose-500 focus:border-rose-400">
                        <option value="pending" {{ old('status', $employeeDeduction->status) == 'pending' ? 'selected' : '' }}>معلقة</option>
                        <option value="applied" {{ old('status', $employeeDeduction->status) == 'applied' ? 'selected' : '' }}>مطبقة</option>
                        <option value="cancelled" {{ old('status', $employeeDeduction->status) == 'cancelled' ? 'selected' : '' }}>ملغاة</option>
                    </select>
                    @error('status')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-slate-700 mb-2">ملاحظات (اختياري)</label>
                    <textarea name="notes" rows="2" class="w-full rounded-xl border-2 border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 focus:ring-2 focus:ring-rose-500 focus:border-rose-400">{{ old('notes', $employeeDeduction->notes) }}</textarea>
                    @error('notes')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>
            <div class="mt-8 pt-6 border-t border-slate-200 flex flex-wrap gap-3">
                <button type="submit" class="px-5 py-2.5 bg-rose-600 hover:bg-rose-700 text-white rounded-xl font-semibold text-sm shadow-lg transition-all">
                    <i class="fas fa-save ml-1"></i> حفظ التعديلات
                </button>
                <a href="{{ route('admin.employee-deductions.show', $employeeDeduction) }}" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-semibold text-sm transition-all">عرض</a>
                <a href="{{ route('admin.employee-deductions.index') }}" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-semibold text-sm transition-all">إلغاء</a>
            </div>
        </form>
    </section>
</div>
@endsection
