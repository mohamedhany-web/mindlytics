@extends('layouts.admin')

@section('title', 'إضافة سؤال جديد - Mindlytics')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">إضافة سؤال جديد</h1>
                    <p class="mt-2 text-gray-600">إضافة سؤال جديد للأسئلة الشائعة</p>
                </div>
                <div>
                    <a href="{{ route('admin.faq.index') }}" 
                       class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                        <i class="fas fa-arrow-right mr-2"></i>
                        العودة للقائمة
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <form action="{{ route('admin.faq.store') }}" method="POST" class="bg-white shadow rounded-lg p-6">
            @csrf

            <div class="space-y-6">
                <!-- السؤال -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">السؤال *</label>
                    <input type="text" name="question" value="{{ old('question') }}" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md bg-white text-gray-900">
                    @error('question')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- الإجابة -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">الإجابة *</label>
                    <textarea name="answer" rows="6" required
                              class="w-full px-3 py-2 border border-gray-300 rounded-md bg-white text-gray-900">{{ old('answer') }}</textarea>
                    @error('answer')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- الفئة -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">الفئة</label>
                    <input type="text" name="category" value="{{ old('category') }}" list="categories"
                           placeholder="أدخل فئة جديدة أو اختر من القائمة"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md bg-white text-gray-900">
                    <datalist id="categories">
                        @foreach($categories as $category)
                        <option value="{{ $category }}">
                        @endforeach
                    </datalist>
                    @error('category')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- الترتيب -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">الترتيب</label>
                    <input type="number" name="order" value="{{ old('order', 0) }}" min="0"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md bg-white text-gray-900">
                    @error('order')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- الحالة -->
                <div class="flex items-center">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                           class="rounded border-gray-300 text-sky-600 focus:ring-sky-500">
                    <label class="mr-2 text-sm font-medium text-gray-700">نشط</label>
                </div>
            </div>

            <div class="mt-6 flex justify-end space-x-3 space-x-reverse">
                <a href="{{ route('admin.faq.index') }}" 
                   class="px-6 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                    إلغاء
                </a>
                <button type="submit" 
                        class="px-6 py-2 bg-sky-600 text-white rounded-md hover:bg-sky-700">
                    حفظ السؤال
                </button>
            </div>
        </form>
    </div>
</div>
@endsection



