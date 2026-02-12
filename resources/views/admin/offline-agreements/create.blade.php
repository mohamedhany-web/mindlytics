@extends('layouts.admin')

@section('title', 'إضافة اتفاقية مدرب')
@section('header', 'إضافة اتفاقية مدرب')

@section('content')
<div class="space-y-6">
    <!-- الهيدر -->
    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">إضافة اتفاقية مدرب جديدة</h1>
                <p class="text-gray-600 mt-1">إنشاء اتفاقية جديدة مع مدرب للكورسات الأوفلاين</p>
            </div>
            <a href="{{ route('admin.offline-agreements.index') }}" class="text-gray-600 hover:text-gray-900">
                <i class="fas fa-arrow-right mr-2"></i>العودة للقائمة
            </a>
        </div>
    </div>

    <form action="{{ route('admin.offline-agreements.store') }}" method="POST" class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
        @csrf

        <div class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- المدرب -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">المدرب *</label>
                    <select name="instructor_id" required 
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                        <option value="">اختر المدرب</option>
                        @foreach($instructors as $instructor)
                            <option value="{{ $instructor->id }}" {{ old('instructor_id') == $instructor->id ? 'selected' : '' }}>{{ $instructor->name }}</option>
                        @endforeach
                    </select>
                    @error('instructor_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <!-- الكورس الأوفلاين -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">الكورس الأوفلاين</label>
                    <select name="offline_course_id" 
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                        <option value="">اختر الكورس (اختياري)</option>
                        @foreach($offlineCourses as $course)
                            <option value="{{ $course->id }}" {{ old('offline_course_id') == $course->id ? 'selected' : '' }}>{{ $course->title }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- نوع الاتفاقية (أوبشن العقد) -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">نوع الاتفاقية *</label>
                    <select name="billing_type" id="billing_type" required 
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                        <option value="per_session" {{ old('billing_type', 'per_session') == 'per_session' ? 'selected' : '' }}>بالجلسة</option>
                        <option value="monthly" {{ old('billing_type') == 'monthly' ? 'selected' : '' }}>راتب شهري</option>
                        <option value="full_course" {{ old('billing_type') == 'full_course' ? 'selected' : '' }}>باكورس كامل</option>
                    </select>
                    <p class="text-xs text-gray-500 mt-1">حدد طريقة احتساب التعويض: لكل جلسة، أو راتب شهري، أو مبلغ إجمالي للكورس بالكامل.</p>
                    @error('billing_type')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <!-- العنوان -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">عنوان الاتفاقية *</label>
                    <input type="text" name="title" value="{{ old('title') }}" required 
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                    @error('title')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <!-- الوصف -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">الوصف</label>
                    <textarea name="description" rows="3" 
                              class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">{{ old('description') }}</textarea>
                </div>

                <!-- تاريخ البدء -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">تاريخ البدء *</label>
                    <input type="date" name="start_date" value="{{ old('start_date') }}" required 
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                    @error('start_date')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <!-- تاريخ الانتهاء -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">تاريخ الانتهاء</label>
                    <input type="date" name="end_date" value="{{ old('end_date') }}" 
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                </div>

                <!-- حقول نوع: بالجلسة -->
                <div id="billing_per_session_fields" class="md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-6 billing-type-fields">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">الراتب لكل جلسة (ج.م) *</label>
                        <input type="number" name="salary_per_session" id="salary_per_session" value="{{ old('salary_per_session', 0) }}" min="0" step="0.01" 
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                        @error('salary_per_session')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">عدد الجلسات *</label>
                        <input type="number" name="sessions_count" id="sessions_count" value="{{ old('sessions_count', 0) }}" min="0" step="1" 
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                        @error('sessions_count')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>

                <!-- حقول نوع: راتب شهري -->
                <div id="billing_monthly_fields" class="md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-6 billing-type-fields" style="display: none;">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">الراتب الشهري (ج.م) *</label>
                        <input type="number" name="monthly_amount" id="monthly_amount" value="{{ old('monthly_amount') }}" min="0" step="0.01" 
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                        @error('monthly_amount')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">عدد الأشهر *</label>
                        <input type="number" name="months_count" id="months_count" value="{{ old('months_count') }}" min="1" step="1" 
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                        @error('months_count')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>

                <!-- حقول نوع: باكورس كامل -->
                <div id="billing_full_course_fields" class="md:col-span-2 billing-type-fields" style="display: none;">
                    <label class="block text-sm font-medium text-gray-700 mb-2">إجمالي قيمة الكورس (ج.م) *</label>
                    <input type="number" name="total_amount" id="total_amount_input" value="{{ old('total_amount') }}" min="0" step="0.01" 
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                    <p class="text-xs text-gray-500 mt-1">مبلغ ثابت يُدفع مقابل تنفيذ الكورس بالكامل.</p>
                    @error('total_amount')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <!-- الحالة -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">الحالة *</label>
                    <select name="status" required 
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                        <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>مسودة</option>
                        <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>نشط</option>
                        <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>مكتمل</option>
                        <option value="cancelled" {{ old('status') == 'cancelled' ? 'selected' : '' }}>ملغي</option>
                    </select>
                </div>

                <!-- الشروط -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">شروط الاتفاقية</label>
                    <textarea name="terms" rows="4" 
                              class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">{{ old('terms') }}</textarea>
                </div>

                <!-- الملاحظات -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">ملاحظات</label>
                    <textarea name="notes" rows="3" 
                              class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">{{ old('notes') }}</textarea>
                </div>
            </div>
        </div>

        <div class="mt-6 pt-6 border-t border-gray-200 flex items-center justify-end gap-4">
            <a href="{{ route('admin.offline-agreements.index') }}" class="px-6 py-2.5 bg-gray-500 hover:bg-gray-600 text-white rounded-lg font-medium transition-colors">
                <i class="fas fa-times mr-2"></i>إلغاء
            </a>
            <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors">
                <i class="fas fa-save mr-2"></i>حفظ الاتفاقية
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    var billingType = document.getElementById('billing_type');
    var perSession = document.getElementById('billing_per_session_fields');
    var monthly = document.getElementById('billing_monthly_fields');
    var fullCourse = document.getElementById('billing_full_course_fields');
    var salaryPerSession = document.getElementById('salary_per_session');
    var sessionsCount = document.getElementById('sessions_count');
    var monthlyAmount = document.getElementById('monthly_amount');
    var monthsCount = document.getElementById('months_count');
    var totalAmountInput = document.getElementById('total_amount_input');

    function toggleBillingFields() {
        var v = billingType.value;
        perSession.style.display = v === 'per_session' ? 'grid' : 'none';
        monthly.style.display = v === 'monthly' ? 'grid' : 'none';
        fullCourse.style.display = v === 'full_course' ? 'block' : 'none';

        salaryPerSession.required = (v === 'per_session');
        sessionsCount.required = (v === 'per_session');
        monthlyAmount.required = (v === 'monthly');
        monthsCount.required = (v === 'monthly');
        totalAmountInput.required = (v === 'full_course');
    }

    billingType.addEventListener('change', toggleBillingFields);
    toggleBillingFields();
});
</script>
@endpush
@endsection
