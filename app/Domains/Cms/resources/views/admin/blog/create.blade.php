@extends('layouts.admin')

@section('title', 'إضافة مقال جديد - Mindlytics')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">إضافة مقال جديد</h1>
                    <p class="mt-2 text-gray-600">إنشاء مقال جديد للمدونة</p>
                </div>
                <div>
                    <a href="{{ route('admin.blog.index') }}" 
                       class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                        <i class="fas fa-arrow-right mr-2"></i>
                        العودة للقائمة
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <form action="{{ route('admin.blog.store') }}" method="POST" enctype="multipart/form-data" class="bg-white shadow rounded-lg p-6">
            @csrf

            <div class="space-y-6">
                <!-- العنوان -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">العنوان *</label>
                    <input type="text" name="title" value="{{ old('title') }}" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md bg-white text-gray-900">
                    @error('title')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- الرابط (Slug) -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">الرابط (اختياري)</label>
                    <input type="text" name="slug" value="{{ old('slug') }}"
                           placeholder="سيتم إنشاؤه تلقائياً من العنوان"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md bg-white text-gray-900">
                    @error('slug')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- الملخص -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">الملخص</label>
                    <textarea name="excerpt" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-md bg-white text-gray-900">{{ old('excerpt') }}</textarea>
                    @error('excerpt')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- المحتوى -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">المحتوى *</label>
                    <textarea name="content" id="content" rows="15" required
                              class="w-full px-3 py-2 border border-gray-300 rounded-md bg-white text-gray-900">{{ old('content') }}</textarea>
                    @error('content')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- الصورة المميزة -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">الصورة المميزة</label>
                    <input type="file" name="featured_image" accept="image/*"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md bg-white text-gray-900">
                    @error('featured_image')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- الحالة -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">الحالة *</label>
                    <select name="status" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md bg-white text-gray-900">
                        <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>مسودة</option>
                        <option value="published" {{ old('status') == 'published' ? 'selected' : '' }}>منشور</option>
                    </select>
                    @error('status')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- مميز -->
                <div class="flex items-center">
                    <input type="checkbox" name="is_featured" value="1" {{ old('is_featured') ? 'checked' : '' }}
                           class="rounded border-gray-300 text-sky-600 focus:ring-sky-500">
                    <label class="mr-2 text-sm font-medium text-gray-700">مقال مميز</label>
                </div>

                <!-- Tags -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">الوسوم (افصل بينها بفواصل)</label>
                    <input type="text" name="tags_input" value="{{ old('tags_input') }}"
                           placeholder="مثال: برمجة, تطوير, Laravel"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md bg-white text-gray-900">
                    <p class="mt-1 text-xs text-gray-500">سيتم تحويلها تلقائياً إلى مصفوفة</p>
                </div>

                <!-- Meta Title -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">عنوان SEO</label>
                    <input type="text" name="meta_title" value="{{ old('meta_title') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md bg-white text-gray-900">
                </div>

                <!-- Meta Description -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">وصف SEO</label>
                    <textarea name="meta_description" rows="2"
                              class="w-full px-3 py-2 border border-gray-300 rounded-md bg-white text-gray-900">{{ old('meta_description') }}</textarea>
                </div>
            </div>

            <div class="mt-6 flex justify-end space-x-3 space-x-reverse">
                <a href="{{ route('admin.blog.index') }}" 
                   class="px-6 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                    إلغاء
                </a>
                <button type="submit" 
                        class="px-6 py-2 bg-sky-600 text-white rounded-md hover:bg-sky-700">
                    حفظ المقال
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

