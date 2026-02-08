@extends('layouts.admin')

@section('title', 'تعديل المجموعة المهارية')
@section('header', 'تعديل المجموعة المهارية')

@section('content')
<div class="space-y-6">
    <!-- معلومات المادة -->
    <div class="bg-white rounded-xl shadow-lg border border-gray-200">
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">تعديل المجموعة المهارية</h3>
                <a href="{{ route('admin.academic-subjects.index') }}" 
                   class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors duration-200">
                    <i class="fas fa-arrow-right mr-2"></i>
                    العودة للقائمة
                </a>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.academic-subjects.update', $academicSubject) }}" class="p-6">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- المسار التعليمي -->
                <div>
                    <label for="academic_year_id" class="block text-sm font-medium text-gray-700 mb-2">
                        المسار التعليمي <span class="text-red-500">*</span>
                    </label>
                    <select name="academic_year_id" id="academic_year_id" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">اختر المسار التعليمي</option>
                        @foreach($academicYears as $year)
                            <option value="{{ $year->id }}" {{ old('academic_year_id', $academicSubject->academic_year_id) == $year->id ? 'selected' : '' }}>
                                {{ $year->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('academic_year_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- اسم المجموعة -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                        اسم المجموعة المهارية <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" id="name" value="{{ old('name', $academicSubject->name) }}" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="مثال: Frontend Development">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- رمز المجموعة -->
                <div>
                    <label for="code" class="block text-sm font-medium text-gray-700 mb-2">
                        رمز المجموعة <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="code" id="code" value="{{ old('code', $academicSubject->code) }}" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="مثال: FE-FOUND أو BACKEND-101">
                    @error('code')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- اللون -->
                <div>
                    <label for="color" class="block text-sm font-medium text-gray-700 mb-2">
                        اللون <span class="text-red-500">*</span>
                    </label>
                    <input type="color" name="color" id="color" value="{{ old('color', $academicSubject->color ?? '#3B82F6') }}" required
                           class="w-full h-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    @error('color')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- الأيقونة -->
                <div>
                    <label for="icon" class="block text-sm font-medium text-gray-700 mb-2">
                        الأيقونة <span class="text-red-500">*</span>
                    </label>
                    <select name="icon" id="icon" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="fas fa-calculator" {{ old('icon', $academicSubject->icon) == 'fas fa-calculator' ? 'selected' : '' }}>🧮 آلة حاسبة (رياضيات)</option>
                        <option value="fas fa-atom" {{ old('icon', $academicSubject->icon) == 'fas fa-atom' ? 'selected' : '' }}>⚛️ ذرة (علوم)</option>
                        <option value="fas fa-book-open" {{ old('icon', $academicSubject->icon) == 'fas fa-book-open' ? 'selected' : '' }}>📖 كتاب مفتوح</option>
                        <option value="fas fa-language" {{ old('icon', $academicSubject->icon) == 'fas fa-language' ? 'selected' : '' }}>🌐 لغات</option>
                        <option value="fas fa-history" {{ old('icon', $academicSubject->icon) == 'fas fa-history' ? 'selected' : '' }}>📜 تاريخ</option>
                        <option value="fas fa-globe" {{ old('icon', $academicSubject->icon) == 'fas fa-globe' ? 'selected' : '' }}>🌍 جغرافيا</option>
                        <option value="fas fa-palette" {{ old('icon', $academicSubject->icon) == 'fas fa-palette' ? 'selected' : '' }}>🎨 فنون</option>
                        <option value="fas fa-music" {{ old('icon', $academicSubject->icon) == 'fas fa-music' ? 'selected' : '' }}>🎵 موسيقى</option>
                        <option value="fas fa-running" {{ old('icon', $academicSubject->icon) == 'fas fa-running' ? 'selected' : '' }}>🏃 رياضة</option>
                        <option value="fas fa-laptop-code" {{ old('icon', $academicSubject->icon) == 'fas fa-laptop-code' ? 'selected' : '' }}>💻 حاسوب</option>
                    </select>
                    @error('icon')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- ترتيب العرض -->
                <div>
                    <label for="order" class="block text-sm font-medium text-gray-700 mb-2">
                        ترتيب العرض
                    </label>
                    <input type="number" name="order" id="order" value="{{ old('order', $academicSubject->order ?? 1) }}" min="1"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="1">
                    @error('order')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- الوصف -->
            <div class="mt-6">
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                    الوصف
                </label>
                <textarea name="description" id="description" rows="3"
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                          placeholder="وصف مختصر للمجموعة المهارية (اختياري)">{{ old('description', $academicSubject->description) }}</textarea>
                @error('description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- حالة النشاط -->
            <div class="mt-6">
                <div class="flex items-center">
                    <input type="checkbox" name="is_active" id="is_active" value="1" 
                           {{ old('is_active', $academicSubject->is_active) ? 'checked' : '' }}
                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                    <label for="is_active" class="mr-2 block text-sm text-gray-700">
                        المجموعة نشطة
                    </label>
                </div>
                <p class="mt-1 text-xs text-gray-500">
                    يمكن إضافة كورسات للمجموعات النشطة فقط
                </p>
            </div>

            <!-- أزرار الإجراءات -->
            <div class="mt-8 pt-6 border-t border-gray-200">
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <form action="{{ route('admin.academic-subjects.destroy', $academicSubject) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف هذه المجموعة؟ سيتم فقد أي ربط يدوي للكورسات المرتبطة.');" class="inline-flex">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors duration-200">
                            <i class="fas fa-trash"></i>
                            حذف المجموعة
                        </button>
                    </form>
                    <a href="{{ route('admin.academic-subjects.index') }}" 
                        class="inline-flex items-center gap-2 px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors duration-200">
                        إلغاء
                    </a>
                    <button type="submit" 
                            class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200">
                        <i class="fas fa-save"></i>
                        حفظ التعديلات
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection