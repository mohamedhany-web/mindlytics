@extends('layouts.admin')

@section('title', 'تعديل مكان')
@section('header', 'تعديل مكان')

@section('content')
<div class="space-y-6">
    <!-- الهيدر -->
    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">تعديل مكان</h1>
                <p class="text-gray-600 mt-1">تحديث معلومات المكان</p>
            </div>
            <a href="{{ route('admin.offline-locations.show', $offlineLocation) }}" class="text-gray-600 hover:text-gray-900">
                <i class="fas fa-arrow-right mr-2"></i>العودة للتفاصيل
            </a>
        </div>
    </div>

    <form action="{{ route('admin.offline-locations.update', $offlineLocation) }}" method="POST" class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- اسم المكان -->
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">اسم المكان *</label>
                <input type="text" name="name" value="{{ old('name', $offlineLocation->name) }}" required 
                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <!-- العنوان -->
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">العنوان</label>
                <textarea name="address" rows="2" 
                          class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">{{ old('address', $offlineLocation->address) }}</textarea>
            </div>

            <!-- المدينة -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">المدينة</label>
                <input type="text" name="city" value="{{ old('city', $offlineLocation->city) }}" 
                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
            </div>

            <!-- رقم الهاتف -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">رقم الهاتف</label>
                <input type="text" name="phone" value="{{ old('phone', $offlineLocation->phone) }}" 
                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
            </div>

            <!-- السعة -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">السعة</label>
                <input type="number" name="capacity" value="{{ old('capacity', $offlineLocation->capacity) }}" min="0" 
                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
            </div>

            <!-- الحالة -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">الحالة</label>
                <select name="is_active" 
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                    <option value="1" {{ old('is_active', $offlineLocation->is_active) ? 'selected' : '' }}>نشط</option>
                    <option value="0" {{ !old('is_active', $offlineLocation->is_active) ? 'selected' : '' }}>غير نشط</option>
                </select>
            </div>

            <!-- الوصف -->
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">الوصف</label>
                <textarea name="description" rows="3" 
                          class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">{{ old('description', $offlineLocation->description) }}</textarea>
            </div>
        </div>

        <div class="mt-6 pt-6 border-t border-gray-200 flex items-center justify-end gap-4">
            <a href="{{ route('admin.offline-locations.show', $offlineLocation) }}" class="px-6 py-2.5 bg-gray-500 hover:bg-gray-600 text-white rounded-lg font-medium transition-colors">
                <i class="fas fa-times mr-2"></i>إلغاء
            </a>
            <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors">
                <i class="fas fa-save mr-2"></i>حفظ التغييرات
            </button>
        </div>
    </form>
</div>
@endsection
