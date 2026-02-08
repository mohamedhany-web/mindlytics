@extends('layouts.admin')

@section('title', 'إضافة مهمة جديدة')
@section('header', 'إضافة مهمة جديدة')

@section('content')
<div class="p-3 sm:p-4 md:p-6 space-y-4 sm:space-y-6" style="background: #f8fafc; min-height: 100vh;">
    <!-- الهيدر -->
    <div class="dashboard-card rounded-2xl card-hover-effect border-2 border-blue-200/50 hover:border-blue-300/70 shadow-xl hover:shadow-2xl transition-all duration-300 overflow-hidden" style="background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(240, 249, 255, 0.95) 50%, rgba(224, 242, 254, 0.9) 100%);">
        <div class="px-4 py-6 sm:px-8 sm:py-8 relative overflow-hidden">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                <div class="space-y-4">
                    <span class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-blue-100 text-blue-700 text-sm font-semibold">
                        <i class="fas fa-tasks"></i>
                        إضافة مهمة جديدة
                    </span>
                    <h1 class="text-3xl font-black text-gray-900 leading-tight">تعيين مهمة جديدة لموظف</h1>
                    <p class="text-gray-600 text-lg">
                        قم بتعيين مهمة جديدة لأحد الموظفين مع تحديد الأولوية والموعد النهائي
                    </p>
                </div>
                <a href="{{ route('admin.employee-tasks.index') }}" class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl bg-gray-500 hover:bg-gray-600 text-white text-sm font-bold shadow-lg hover:shadow-xl transition-all duration-300 w-full sm:w-auto">
                    <i class="fas fa-arrow-right"></i>
                    العودة للقائمة
                </a>
            </div>
        </div>
    </div>

    <!-- نموذج الإضافة -->
    <div class="dashboard-card rounded-2xl card-hover-effect border-2 border-gray-200/50 hover:border-blue-300/70 shadow-xl hover:shadow-2xl transition-all duration-300 overflow-hidden" style="background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(248, 250, 252, 0.95) 100%);">
        <form action="{{ route('admin.employee-tasks.store') }}" method="POST" class="p-6 sm:p-8 space-y-8">
            @csrf

            <!-- معلومات المهمة -->
            <div class="space-y-6">
                <h2 class="text-xl font-bold text-gray-900 border-b border-gray-200 pb-3">معلومات المهمة</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label for="employee_id" class="block text-sm font-semibold text-gray-700 mb-2">
                            الموظف <span class="text-red-500">*</span>
                        </label>
                        <select name="employee_id" id="employee_id" required
                                class="w-full rounded-2xl border border-gray-200 bg-white/70 px-4 py-3 text-gray-900 shadow-sm focus:border-blue-500 focus:ring-4 focus:ring-blue-500/20 transition">
                            <option value="">اختر الموظف</option>
                            @foreach($employees as $employee)
                                <option value="{{ $employee->id }}" {{ old('employee_id') == $employee->id ? 'selected' : '' }}>
                                    {{ $employee->name }} 
                                    @if($employee->employee_code)
                                        ({{ $employee->employee_code }})
                                    @endif
                                    @if($employee->employeeJob)
                                        - {{ $employee->employeeJob->name }}
                                    @endif
                                </option>
                            @endforeach
                        </select>
                        @error('employee_id')
                            <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label for="title" class="block text-sm font-semibold text-gray-700 mb-2">
                            عنوان المهمة <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="title" id="title" value="{{ old('title') }}" required
                               class="w-full rounded-2xl border border-gray-200 bg-white/70 px-4 py-3 text-gray-900 shadow-sm focus:border-blue-500 focus:ring-4 focus:ring-blue-500/20 transition"
                               placeholder="مثال: إعداد محتوى كورس Laravel">
                        @error('title')
                            <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label for="description" class="block text-sm font-semibold text-gray-700 mb-2">
                            وصف المهمة
                        </label>
                        <textarea name="description" id="description" rows="4"
                                  class="w-full rounded-2xl border border-gray-200 bg-white/70 px-4 py-3 text-gray-900 shadow-sm focus:border-blue-500 focus:ring-4 focus:ring-blue-500/20 transition"
                                  placeholder="وصف تفصيلي للمهمة المطلوبة...">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="priority" class="block text-sm font-semibold text-gray-700 mb-2">
                            الأولوية <span class="text-red-500">*</span>
                        </label>
                        <select name="priority" id="priority" required
                                class="w-full rounded-2xl border border-gray-200 bg-white/70 px-4 py-3 text-gray-900 shadow-sm focus:border-blue-500 focus:ring-4 focus:ring-blue-500/20 transition">
                            <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }}>منخفضة</option>
                            <option value="medium" {{ old('priority') == 'medium' ? 'selected' : '' }}>متوسطة</option>
                            <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>عالية</option>
                            <option value="urgent" {{ old('priority') == 'urgent' ? 'selected' : '' }}>عاجلة</option>
                        </select>
                        @error('priority')
                            <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="deadline" class="block text-sm font-semibold text-gray-700 mb-2">
                            الموعد النهائي
                        </label>
                        <input type="date" name="deadline" id="deadline" value="{{ old('deadline') }}"
                               min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                               class="w-full rounded-2xl border border-gray-200 bg-white/70 px-4 py-3 text-gray-900 shadow-sm focus:border-blue-500 focus:ring-4 focus:ring-blue-500/20 transition">
                        @error('deadline')
                            <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label for="notes" class="block text-sm font-semibold text-gray-700 mb-2">
                            ملاحظات إضافية
                        </label>
                        <textarea name="notes" id="notes" rows="3"
                                  class="w-full rounded-2xl border border-gray-200 bg-white/70 px-4 py-3 text-gray-900 shadow-sm focus:border-blue-500 focus:ring-4 focus:ring-blue-500/20 transition"
                                  placeholder="أي ملاحظات أو تعليمات إضافية...">{{ old('notes') }}</textarea>
                        @error('notes')
                            <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- أزرار الإجراءات -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pt-6 border-t border-gray-100">
                <span class="text-xs text-gray-500">
                    سيتم تعيين المهمة بحالة "معلقة" ويمكن للموظف البدء فيها لاحقاً
                </span>
                <div class="flex flex-col md:flex-row md:items-center gap-3">
                    <a href="{{ route('admin.employee-tasks.index') }}" class="inline-flex items-center justify-center gap-2 rounded-xl bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 text-sm font-bold shadow-lg hover:shadow-xl transition-all duration-300">
                        <i class="fas fa-times"></i>
                        إلغاء
                    </a>
                    <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-blue-600 via-blue-500 to-blue-600 hover:from-blue-700 hover:via-blue-600 hover:to-blue-700 text-white px-6 py-3 text-sm font-bold shadow-lg shadow-blue-600/30 hover:shadow-xl hover:shadow-blue-600/40 hover:-translate-y-0.5 transition-all duration-300">
                        <i class="fas fa-save"></i>
                        حفظ المهمة
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
