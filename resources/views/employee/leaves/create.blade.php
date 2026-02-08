@extends('layouts.employee')

@section('title', 'طلب إجازة جديد')
@section('header', 'طلب إجازة جديد')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-6">
        <form action="{{ route('employee.leaves.store') }}" method="POST">
            @csrf

            <div class="space-y-6">
                <!-- نوع الإجازة -->
                <div>
                    <label for="type" class="block text-sm font-medium text-gray-700 mb-2">
                        نوع الإجازة <span class="text-red-500">*</span>
                    </label>
                    <select name="type" id="type" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('type') border-red-500 @enderror">
                        <option value="">اختر نوع الإجازة</option>
                        <option value="annual" {{ old('type') == 'annual' ? 'selected' : '' }}>سنوية</option>
                        <option value="sick" {{ old('type') == 'sick' ? 'selected' : '' }}>مرضية</option>
                        <option value="emergency" {{ old('type') == 'emergency' ? 'selected' : '' }}>طارئة</option>
                        <option value="unpaid" {{ old('type') == 'unpaid' ? 'selected' : '' }}>بدون راتب</option>
                        <option value="other" {{ old('type') == 'other' ? 'selected' : '' }}>أخرى</option>
                    </select>
                    @error('type')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- من تاريخ -->
                <div>
                    <label for="start_date" class="block text-sm font-medium text-gray-700 mb-2">
                        من تاريخ <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="start_date" id="start_date" required
                           value="{{ old('start_date') }}"
                           min="{{ date('Y-m-d') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('start_date') border-red-500 @enderror">
                    @error('start_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- إلى تاريخ -->
                <div>
                    <label for="end_date" class="block text-sm font-medium text-gray-700 mb-2">
                        إلى تاريخ <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="end_date" id="end_date" required
                           value="{{ old('end_date') }}"
                           min="{{ date('Y-m-d') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('end_date') border-red-500 @enderror">
                    @error('end_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- السبب -->
                <div>
                    <label for="reason" class="block text-sm font-medium text-gray-700 mb-2">
                        سبب الإجازة <span class="text-red-500">*</span>
                    </label>
                    <textarea name="reason" id="reason" rows="5" required
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('reason') border-red-500 @enderror"
                              placeholder="اكتب سبب طلب الإجازة...">{{ old('reason') }}</textarea>
                    @error('reason')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- الأزرار -->
                <div class="flex items-center justify-end gap-4 pt-4 border-t border-gray-200">
                    <a href="{{ route('employee.leaves.index') }}" 
                       class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                        إلغاء
                    </a>
                    <button type="submit" 
                            class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors">
                        <i class="fas fa-paper-plane mr-2"></i>
                        تقديم الطلب
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    // تحديث الحد الأدنى لتاريخ النهاية عند تغيير تاريخ البداية
    document.getElementById('start_date').addEventListener('change', function() {
        const startDate = this.value;
        const endDateInput = document.getElementById('end_date');
        if (startDate) {
            endDateInput.min = startDate;
            if (endDateInput.value && endDateInput.value < startDate) {
                endDateInput.value = startDate;
            }
        }
    });
</script>
@endpush
@endsection
