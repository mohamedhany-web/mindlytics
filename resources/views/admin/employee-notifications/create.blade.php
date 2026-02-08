@extends('layouts.admin')

@section('title', 'إرسال إشعار للموظفين')
@section('header', 'إرسال إشعار للموظفين')

@section('content')
<div class="space-y-6">
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg">
            <i class="fas fa-check-circle ml-2"></i>
            {{ session('success') }}
        </div>
    @endif

    <!-- الهيدر -->
    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-6 py-5 bg-slate-50 border-b border-slate-200 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white shadow-md">
                    <i class="fas fa-paper-plane text-lg"></i>
                </div>
                <div>
                    <nav class="text-xs font-medium text-slate-500 flex flex-wrap items-center gap-2 mb-1">
                        <a href="{{ route('admin.dashboard') }}" class="text-blue-600 hover:text-blue-700">لوحة التحكم</a>
                        <span>/</span>
                        <a href="{{ route('admin.employee-notifications.index') }}" class="text-blue-600 hover:text-blue-700">إشعارات الموظفين</a>
                        <span>/</span>
                        <span class="text-slate-600">إرسال جديد</span>
                    </nav>
                    <h2 class="text-2xl font-black text-slate-900 mt-1">إنشاء إشعار للموظفين</h2>
                    <p class="text-sm text-slate-600 mt-1">صغ رسالة مخصصة واختر الموظفين المستهدفين.</p>
                </div>
            </div>
            <a href="{{ route('admin.employee-notifications.index') }}" class="inline-flex items-center gap-2 rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition-colors">
                <i class="fas fa-arrow-right"></i>
                العودة
            </a>
        </div>
    </section>

    @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.employee-notifications.store') }}" method="POST" class="space-y-6" id="notificationForm">
        @csrf
        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
            <div class="xl:col-span-2 space-y-6">
                <section class="rounded-xl bg-white border border-slate-200 shadow-lg overflow-hidden">
                    <div class="px-6 py-5 border-b border-slate-200 bg-slate-50">
                        <h3 class="text-lg font-black text-slate-900 mb-2 flex items-center gap-2">
                            <div class="w-10 h-10 rounded-lg bg-blue-100 flex items-center justify-center text-blue-600">
                                <i class="fas fa-edit text-lg"></i>
                            </div>
                            محتوى الإشعار
                        </h3>
                    </div>
                    <div class="p-6 space-y-5">
                        <div>
                            <label for="title" class="block text-xs font-semibold text-slate-700 mb-2">
                                عنوان الإشعار <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" name="title" id="title" value="{{ old('title', '') }}" required maxlength="255" 
                                   class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                   placeholder="مثال: اجتماع مهم" />
                            @error('title')
                                <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="message" class="block text-xs font-semibold text-slate-700 mb-2">
                                نص الإشعار <span class="text-rose-500">*</span>
                            </label>
                            <textarea name="message" id="message" rows="6" required maxlength="2000" 
                                      class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm leading-6 text-slate-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 resize-none" 
                                      placeholder="اكتب تفاصيل الإشعار...">{{ old('message', '') }}</textarea>
                            <p class="mt-1.5 text-xs text-slate-600">الحد الأقصى 2000 حرف.</p>
                            @error('message')
                                <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="priority" class="block text-xs font-semibold text-slate-700 mb-2">
                                الأولوية <span class="text-rose-500">*</span>
                            </label>
                            <select name="priority" id="priority" required 
                                    class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">اختر الأولوية</option>
                                @foreach ($priorities as $key => $priority)
                                    <option value="{{ $key }}" {{ old('priority', 'normal') == $key ? 'selected' : '' }}>
                                        {{ $priority }}
                                    </option>
                                @endforeach
                            </select>
                            @error('priority')
                                <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </section>
            </div>

            <div class="space-y-6">
                <section class="rounded-xl bg-white border border-slate-200 shadow-lg overflow-hidden">
                    <div class="px-6 py-5 border-b border-slate-200 bg-slate-50">
                        <h3 class="text-lg font-black text-slate-900 mb-2 flex items-center gap-2">
                            <div class="w-10 h-10 rounded-lg bg-green-100 flex items-center justify-center text-green-600">
                                <i class="fas fa-users text-lg"></i>
                            </div>
                            المستلمون
                        </h3>
                    </div>
                    <div class="p-6 space-y-5">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-2">
                                نوع الهدف <span class="text-rose-500">*</span>
                            </label>
                            <select name="target_type" id="target_type" required 
                                    class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">اختر الهدف</option>
                                <option value="all_employees" {{ old('target_type') == 'all_employees' ? 'selected' : '' }}>
                                    جميع الموظفين
                                </option>
                                <option value="specific_employee" {{ old('target_type') == 'specific_employee' ? 'selected' : '' }}>
                                    موظف محدد
                                </option>
                            </select>
                            @error('target_type')
                                <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>
                            @enderror
                        </div>
                        <div id="employee_select_container" style="display: none;">
                            <label for="employee_id" class="block text-xs font-semibold text-slate-700 mb-2">
                                الموظف <span class="text-rose-500">*</span>
                            </label>
                            <select name="employee_id" id="employee_id" 
                                    class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">اختر الموظف</option>
                                @foreach ($employees as $employee)
                                    <option value="{{ $employee->id }}" {{ old('employee_id') == $employee->id ? 'selected' : '' }}>
                                        {{ $employee->name }} 
                                        @if($employee->employee_code)
                                            ({{ $employee->employee_code }})
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                            @error('employee_id')
                                <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </section>

                <section class="rounded-xl bg-blue-50 border border-blue-200 p-6">
                    <h4 class="text-sm font-black text-blue-900 mb-3 flex items-center gap-2">
                        <i class="fas fa-info-circle"></i>
                        ملاحظات مهمة
                    </h4>
                    <ul class="space-y-2 text-xs text-blue-800">
                        <li class="flex items-start gap-2">
                            <i class="fas fa-check-circle mt-0.5"></i>
                            <span>سيتم إرسال الإشعار فوراً للموظفين المحددين</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <i class="fas fa-check-circle mt-0.5"></i>
                            <span>سيظهر الإشعار للموظف في popup عند فتح النظام</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <i class="fas fa-check-circle mt-0.5"></i>
                            <span>إذا أغلق الموظف الـ popup، سيظهر مرة أخرى حتى يقرأه</span>
                        </li>
                    </ul>
                </section>
            </div>
        </div>

        <div class="flex flex-col sm:flex-row gap-3 sm:justify-end">
            <a href="{{ route('admin.employee-notifications.index') }}" 
               class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-300 bg-white px-6 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition-colors">
                <i class="fas fa-times"></i>
                إلغاء
            </a>
            <button type="submit" id="submitBtn"
                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-blue-600 to-blue-500 hover:from-blue-700 hover:to-blue-600 px-6 py-3 text-sm font-semibold text-white shadow-md hover:shadow-lg transition-all">
                <i class="fas fa-paper-plane"></i>
                <span id="submitText">إرسال الإشعار</span>
                <span id="submitLoading" class="hidden">
                    <i class="fas fa-spinner fa-spin ml-2"></i>
                    جاري الإرسال...
                </span>
            </button>
        </div>
    </form>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('notificationForm');
        if (!form) return;
        
        form.addEventListener('submit', function(e) {
            const submitBtn = document.getElementById('submitBtn');
            const submitText = document.getElementById('submitText');
            const submitLoading = document.getElementById('submitLoading');
            
            // التحقق من صحة البيانات قبل الإرسال
            const title = document.getElementById('title').value.trim();
            const message = document.getElementById('message').value.trim();
            const priority = document.getElementById('priority').value;
            const targetType = document.getElementById('target_type').value;
            const employeeId = document.getElementById('employee_id').value;

            if (!title || !message || !priority || !targetType) {
                e.preventDefault();
                alert('يرجى ملء جميع الحقول المطلوبة');
                return false;
            }

            if (targetType === 'specific_employee' && !employeeId) {
                e.preventDefault();
                alert('يرجى اختيار موظف');
                return false;
            }

            // تعطيل الزر وإظهار التحميل
            if (submitBtn && submitText && submitLoading) {
                submitBtn.disabled = true;
                submitText.classList.add('hidden');
                submitLoading.classList.remove('hidden');
            }
        });
    });
    </script>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const targetType = document.getElementById('target_type');
    const employeeContainer = document.getElementById('employee_select_container');
    const employeeSelect = document.getElementById('employee_id');

    targetType.addEventListener('change', function() {
        if (this.value === 'specific_employee') {
            employeeContainer.style.display = 'block';
            employeeSelect.required = true;
        } else {
            employeeContainer.style.display = 'none';
            employeeSelect.required = false;
            employeeSelect.value = '';
        }
    });

    // Trigger on page load if value is set
    if (targetType.value === 'specific_employee') {
        employeeContainer.style.display = 'block';
        employeeSelect.required = true;
    }
});
</script>
@endsection
