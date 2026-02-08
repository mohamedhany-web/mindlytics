@extends('layouts.admin')

@section('title', 'تعديل اتفاقية الموظف - Mindlytics')
@section('header', 'تعديل اتفاقية الموظف')

@section('content')
<div class="p-3 sm:p-4 md:p-6 space-y-4 sm:space-y-6" style="background: #f8fafc; min-height: 100vh;">
    <section class="rounded-2xl bg-white/95 backdrop-blur border-2 border-slate-200/50 shadow-xl overflow-hidden">
        <div class="px-5 py-6 sm:px-8 lg:px-12 border-b border-slate-200 bg-gradient-to-r from-slate-50 to-white">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-amber-500 via-amber-600 to-orange-600 flex items-center justify-center text-white shadow-lg">
                    <i class="fas fa-edit text-xl"></i>
                </div>
                <div>
                    <h2 class="text-2xl sm:text-3xl font-black text-slate-900">تعديل اتفاقية الموظف</h2>
                    <p class="text-sm text-slate-600 mt-1">تعديل اتفاقية: {{ $employeeAgreement->title }}</p>
                </div>
            </div>
        </div>
        <form method="POST" action="{{ route('admin.employee-agreements.update', $employeeAgreement) }}" class="px-5 py-6 sm:px-8 lg:px-12">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">الموظف <span class="text-red-500">*</span></label>
                    <select name="employee_id" required class="w-full rounded-xl border-2 border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-400 transition-all">
                        <option value="">اختر الموظف</option>
                        @forelse($employees as $employee)
                            <option value="{{ $employee->id }}" {{ old('employee_id', $employeeAgreement->employee_id) == $employee->id ? 'selected' : '' }}>
                                {{ $employee->name }} 
                                @if($employee->email)
                                    ({{ $employee->email }})
                                @endif
                            </option>
                        @empty
                            <option value="" disabled>لا يوجد موظفين متاحين</option>
                        @endforelse
                    </select>
                    @error('employee_id')
                        <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">عنوان الاتفاقية <span class="text-red-500">*</span></label>
                    <input type="text" name="title" value="{{ old('title', $employeeAgreement->title) }}" required class="w-full rounded-xl border-2 border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-400 transition-all" placeholder="مثال: اتفاقية عمل مع الموظف..." />
                    @error('title')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">الراتب (ج.م) <span class="text-red-500">*</span></label>
                    <input type="number" name="salary" step="0.01" min="0" value="{{ old('salary', $employeeAgreement->salary) }}" required class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-400 transition-all" placeholder="0.00" />
                    <p class="mt-1 text-xs text-slate-500">الراتب الشهري للموظف</p>
                    @error('salary')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">تاريخ البدء <span class="text-red-500">*</span></label>
                    <input type="date" name="start_date" value="{{ old('start_date', $employeeAgreement->start_date->format('Y-m-d')) }}" required class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-400 transition-all" />
                    @error('start_date')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">تاريخ الانتهاء</label>
                    <input type="date" name="end_date" value="{{ old('end_date', $employeeAgreement->end_date ? $employeeAgreement->end_date->format('Y-m-d') : '') }}" class="w-full rounded-xl border-2 border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-400 transition-all" />
                    @error('end_date')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">الحالة <span class="text-red-500">*</span></label>
                    <select name="status" required class="w-full rounded-xl border-2 border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-400 transition-all">
                        <option value="draft" {{ old('status', $employeeAgreement->status) == 'draft' ? 'selected' : '' }}>مسودة</option>
                        <option value="active" {{ old('status', $employeeAgreement->status) == 'active' ? 'selected' : '' }}>نشط</option>
                        <option value="suspended" {{ old('status', $employeeAgreement->status) == 'suspended' ? 'selected' : '' }}>معلق</option>
                        <option value="terminated" {{ old('status', $employeeAgreement->status) == 'terminated' ? 'selected' : '' }}>منتهي</option>
                        <option value="completed" {{ old('status', $employeeAgreement->status) == 'completed' ? 'selected' : '' }}>مكتمل</option>
                    </select>
                    @error('status')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-slate-700 mb-2">الوصف</label>
                    <textarea name="description" rows="3" class="w-full rounded-xl border-2 border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-400 transition-all" placeholder="وصف مختصر للاتفاقية...">{{ old('description', $employeeAgreement->description) }}</textarea>
                    @error('description')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-slate-700 mb-2">شروط العقد</label>
                    <textarea name="contract_terms" rows="5" class="w-full rounded-xl border-2 border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-400 transition-all" placeholder="شروط وأحكام العقد...">{{ old('contract_terms', $employeeAgreement->contract_terms) }}</textarea>
                    @error('contract_terms')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-slate-700 mb-2">بنود الاتفاقية</label>
                    <textarea name="agreement_terms" rows="5" class="w-full rounded-xl border-2 border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-400 transition-all" placeholder="بنود وأحكام الاتفاقية...">{{ old('agreement_terms', $employeeAgreement->agreement_terms) }}</textarea>
                    @error('agreement_terms')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-slate-700 mb-2">ملاحظات</label>
                    <textarea name="notes" rows="3" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-400 transition-all" placeholder="ملاحظات إضافية...">{{ old('notes', $employeeAgreement->notes) }}</textarea>
                    @error('notes')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 mt-6 pt-6 border-t border-slate-200">
                <a href="{{ route('admin.employee-agreements.show', $employeeAgreement) }}" class="inline-flex items-center gap-2 rounded-xl border-2 border-slate-200 px-5 py-3 text-sm font-semibold text-slate-600 transition hover:bg-slate-50">
                    إلغاء
                </a>
                <button type="submit" class="inline-flex items-center gap-2 rounded-2xl bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white shadow hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-all">
                    <i class="fas fa-save"></i>
                    حفظ التعديلات
                </button>
            </div>
        </form>
    </section>
</div>
@endsection
