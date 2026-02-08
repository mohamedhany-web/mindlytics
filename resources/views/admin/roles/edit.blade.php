@extends('layouts.admin')

@section('title', 'تعديل الدور')
@section('header', 'تعديل الدور')

@section('content')
<div class="space-y-6">
    <div class="bg-white rounded-xl shadow-lg border border-gray-200">
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">معلومات الدور</h3>
                    <p class="text-sm text-gray-500 mt-1">تعديل معلومات الدور: {{ $role->display_name }}</p>
                </div>
                <a href="{{ route('admin.roles.index') }}" 
                   class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors duration-200">
                    <i class="fas fa-arrow-right mr-2"></i>
                    العودة للقائمة
                </a>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.roles.update', $role) }}">
            @csrf
            @method('PUT')
            <div class="p-6 space-y-6">
                <!-- اسم الدور -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                        اسم الدور <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" id="name" value="{{ old('name', $role->name) }}" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="مثال: content_manager"
                           {{ $role->is_system ? 'readonly' : '' }}>
                    @if($role->is_system)
                        <p class="mt-1 text-xs text-blue-600">
                            <i class="fas fa-info-circle mr-1"></i>
                            هذا دور نظامي ولا يمكن تعديل اسمه
                        </p>
                    @endif
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- الاسم المعروض -->
                <div>
                    <label for="display_name" class="block text-sm font-medium text-gray-700 mb-2">
                        الاسم المعروض <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="display_name" id="display_name" value="{{ old('display_name', $role->display_name) }}" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="مثال: مدير المحتوى">
                    @error('display_name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- الوصف -->
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                        الوصف
                    </label>
                    <textarea name="description" id="description" rows="3"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                              placeholder="وصف مختصر للدور (اختياري)">{{ old('description', $role->description) }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- الصلاحيات -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-3">
                        الصلاحيات
                    </label>
                    <div class="border border-gray-300 rounded-lg p-4 max-h-96 overflow-y-auto">
                        @if($permissions->count() > 0)
                            @foreach($permissions as $group => $groupPermissions)
                                <div class="mb-6 last:mb-0">
                                    <h4 class="text-sm font-semibold text-gray-900 mb-3 pb-2 border-b border-gray-200">
                                        {{ $group ?? 'عام' }}
                                    </h4>
                                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                                        @foreach($groupPermissions as $permission)
                                            <label class="flex items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                                                <input type="checkbox" name="permissions[]" value="{{ $permission->id }}"
                                                       {{ in_array($permission->id, $role->permissions->pluck('id')->toArray()) ? 'checked' : '' }}
                                                       class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                                <div class="mr-3">
                                                    <span class="text-sm font-medium text-gray-900">{{ $permission->display_name }}</span>
                                                    @if($permission->description)
                                                        <p class="text-xs text-gray-500">{{ $permission->description }}</p>
                                                    @endif
                                                </div>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <p class="text-sm text-gray-500 text-center py-4">
                                لا توجد صلاحيات متاحة. <a href="{{ route('admin.permissions.create') }}" class="text-blue-600 hover:underline">أضف صلاحيات جديدة</a>
                            </p>
                        @endif
                    </div>
                    @error('permissions.*')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- أزرار الإجراءات -->
                <div class="flex items-center justify-end gap-4 pt-4 border-t border-gray-200">
                    <a href="{{ route('admin.roles.index') }}" 
                       class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors duration-200">
                        إلغاء
                    </a>
                    <button type="submit" 
                            class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200">
                        <i class="fas fa-save mr-2"></i>
                        حفظ التغييرات
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
