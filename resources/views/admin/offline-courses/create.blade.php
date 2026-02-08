@extends('layouts.admin')

@section('title', 'إضافة كورس أوفلاين')
@section('header', 'إضافة كورس أوفلاين')

@section('content')
<div class="space-y-6">
    <!-- الهيدر -->
    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">إضافة كورس أوفلاين جديد</h1>
                <p class="text-gray-600 mt-1">إنشاء كورس أوفلاين جديد في الأكاديمية</p>
            </div>
            <a href="{{ route('admin.offline-courses.index') }}" class="text-gray-600 hover:text-gray-900">
                <i class="fas fa-arrow-right mr-2"></i>العودة للقائمة
            </a>
        </div>
    </div>

    <form action="{{ route('admin.offline-courses.store') }}" method="POST" class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
        @csrf

        <div class="space-y-6">
            <!-- القسم الأساسي -->
            <div class="border-b border-gray-200 pb-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">المعلومات الأساسية</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- العنوان -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">عنوان الكورس *</label>
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

                    <!-- المدرب -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">المدرب المسؤول *</label>
                        <select name="instructor_id" required 
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                            <option value="">اختر المدرب</option>
                            @foreach($instructors as $instructor)
                                <option value="{{ $instructor->id }}" {{ old('instructor_id') == $instructor->id ? 'selected' : '' }}>{{ $instructor->name }}</option>
                            @endforeach
                        </select>
                        @error('instructor_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <!-- المكان -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">المكان</label>
                        <select name="location_id" 
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                            <option value="">اختر المكان</option>
                            @foreach($locations as $location)
                                <option value="{{ $location->id }}" {{ old('location_id') == $location->id ? 'selected' : '' }}>{{ $location->name }}</option>
                            @endforeach
                        </select>
                        <p class="text-xs text-gray-500 mt-1">أو</p>
                    </div>

                    <!-- الموقع (نص حر) -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">موقع الكورس (نص حر)</label>
                        <input type="text" name="location" value="{{ old('location') }}" placeholder="أو أدخل موقع مخصص" 
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                    </div>

                </div>
            </div>

            <!-- القسم التفاصيل -->
            <div class="border-b border-gray-200 pb-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">التفاصيل والمواعيد</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- تاريخ البدء -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">تاريخ البدء</label>
                        <input type="date" name="start_date" value="{{ old('start_date') }}" 
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                    </div>

                    <!-- تاريخ الانتهاء -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">تاريخ الانتهاء</label>
                        <input type="date" name="end_date" value="{{ old('end_date') }}" 
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                    </div>

                    <!-- عدد الساعات -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">عدد الساعات</label>
                        <input type="number" name="duration_hours" value="{{ old('duration_hours', 0) }}" min="0" 
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                    </div>

                    <!-- عدد الجلسات -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">عدد الجلسات</label>
                        <input type="number" name="sessions_count" value="{{ old('sessions_count', 0) }}" min="0" 
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                    </div>
                </div>
            </div>

            <!-- القسم المالي -->
            <div class="border-b border-gray-200 pb-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">المعلومات المالية</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- السعر -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">السعر</label>
                        <input type="number" name="price" value="{{ old('price', 0) }}" min="0" step="0.01" 
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                    </div>

                    <!-- الحد الأقصى للطلاب -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">الحد الأقصى للطلاب *</label>
                        <input type="number" name="max_students" value="{{ old('max_students', 20) }}" min="1" required 
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                        @error('max_students')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>

            <!-- القسم الإداري -->
            <div>
                <h2 class="text-lg font-semibold text-gray-900 mb-4">الإعدادات الإدارية</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
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

                    <!-- الملاحظات -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">ملاحظات إدارية</label>
                        <textarea name="notes" rows="3" 
                                  class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">{{ old('notes') }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-6 pt-6 border-t border-gray-200 flex items-center justify-end gap-4">
            <a href="{{ route('admin.offline-courses.index') }}" class="px-6 py-2.5 bg-gray-500 hover:bg-gray-600 text-white rounded-lg font-medium transition-colors">
                <i class="fas fa-times mr-2"></i>إلغاء
            </a>
            <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors">
                <i class="fas fa-save mr-2"></i>حفظ الكورس
            </button>
        </div>
    </form>
</div>
@endsection
