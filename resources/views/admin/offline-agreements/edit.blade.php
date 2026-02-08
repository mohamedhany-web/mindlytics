@extends('layouts.admin')

@section('title', 'تعديل اتفاقية مدرب')
@section('header', 'تعديل اتفاقية مدرب')

@section('content')
<div class="space-y-6">
    <!-- الهيدر -->
    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">تعديل اتفاقية مدرب</h1>
                <p class="text-gray-600 mt-1">تحديث معلومات الاتفاقية</p>
            </div>
            <a href="{{ route('admin.offline-agreements.show', $agreement) }}" class="text-gray-600 hover:text-gray-900">
                <i class="fas fa-arrow-right mr-2"></i>العودة للتفاصيل
            </a>
        </div>
    </div>

    <form action="{{ route('admin.offline-agreements.update', $agreement) }}" method="POST" class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
        @csrf
        @method('PUT')

        <div class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- المدرب -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">المدرب *</label>
                    <select name="instructor_id" required 
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                        <option value="">اختر المدرب</option>
                        @foreach($instructors as $instructor)
                            <option value="{{ $instructor->id }}" {{ old('instructor_id', $agreement->instructor_id) == $instructor->id ? 'selected' : '' }}>{{ $instructor->name }}</option>
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
                            <option value="{{ $course->id }}" {{ old('offline_course_id', $agreement->offline_course_id) == $course->id ? 'selected' : '' }}>{{ $course->title }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- العنوان -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">عنوان الاتفاقية *</label>
                    <input type="text" name="title" value="{{ old('title', $agreement->title) }}" required 
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                    @error('title')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <!-- الوصف -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">الوصف</label>
                    <textarea name="description" rows="3" 
                              class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">{{ old('description', $agreement->description) }}</textarea>
                </div>

                <!-- تاريخ البدء -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">تاريخ البدء *</label>
                    <input type="date" name="start_date" value="{{ old('start_date', $agreement->start_date->format('Y-m-d')) }}" required 
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                    @error('start_date')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <!-- تاريخ الانتهاء -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">تاريخ الانتهاء</label>
                    <input type="date" name="end_date" value="{{ old('end_date', $agreement->end_date?->format('Y-m-d')) }}" 
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                </div>

                <!-- الراتب لكل جلسة -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">الراتب لكل جلسة *</label>
                    <input type="number" name="salary_per_session" value="{{ old('salary_per_session', $agreement->salary_per_session) }}" min="0" step="0.01" required 
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                    @error('salary_per_session')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <!-- عدد الجلسات -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">عدد الجلسات *</label>
                    <input type="number" name="sessions_count" value="{{ old('sessions_count', $agreement->sessions_count) }}" min="1" required 
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                    @error('sessions_count')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <!-- حالة الدفع -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">حالة الدفع *</label>
                    <select name="payment_status" required 
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                        <option value="pending" {{ old('payment_status', $agreement->payment_status) == 'pending' ? 'selected' : '' }}>معلق</option>
                        <option value="partial" {{ old('payment_status', $agreement->payment_status) == 'partial' ? 'selected' : '' }}>جزئي</option>
                        <option value="paid" {{ old('payment_status', $agreement->payment_status) == 'paid' ? 'selected' : '' }}>مدفوع</option>
                        <option value="overdue" {{ old('payment_status', $agreement->payment_status) == 'overdue' ? 'selected' : '' }}>متأخر</option>
                    </select>
                </div>

                <!-- الحالة -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">الحالة *</label>
                    <select name="status" required 
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                        <option value="draft" {{ old('status', $agreement->status) == 'draft' ? 'selected' : '' }}>مسودة</option>
                        <option value="active" {{ old('status', $agreement->status) == 'active' ? 'selected' : '' }}>نشط</option>
                        <option value="completed" {{ old('status', $agreement->status) == 'completed' ? 'selected' : '' }}>مكتمل</option>
                        <option value="cancelled" {{ old('status', $agreement->status) == 'cancelled' ? 'selected' : '' }}>ملغي</option>
                    </select>
                </div>

                <!-- الشروط -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">شروط الاتفاقية</label>
                    <textarea name="terms" rows="4" 
                              class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">{{ old('terms', $agreement->terms) }}</textarea>
                </div>

                <!-- الملاحظات -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">ملاحظات</label>
                    <textarea name="notes" rows="3" 
                              class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">{{ old('notes', $agreement->notes) }}</textarea>
                </div>
            </div>
        </div>

        <div class="mt-6 pt-6 border-t border-gray-200 flex items-center justify-end gap-4">
            <a href="{{ route('admin.offline-agreements.show', $agreement) }}" class="px-6 py-2.5 bg-gray-500 hover:bg-gray-600 text-white rounded-lg font-medium transition-colors">
                <i class="fas fa-times mr-2"></i>إلغاء
            </a>
            <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors">
                <i class="fas fa-save mr-2"></i>حفظ التغييرات
            </button>
        </div>
    </form>
</div>
@endsection
