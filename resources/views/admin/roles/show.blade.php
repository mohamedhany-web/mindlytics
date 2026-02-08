@extends('layouts.admin')

@section('title', 'تفاصيل الدور')
@section('header', 'تفاصيل الدور')

@section('content')
<div class="space-y-6">
    <!-- معلومات الدور -->
    <div class="bg-white rounded-xl shadow-lg border border-gray-200">
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">{{ $role->display_name }}</h3>
                    <p class="text-sm text-gray-500 mt-1">{{ $role->name }}</p>
                </div>
                <div class="flex items-center gap-3">
                    @if($role->is_system)
                        <span class="px-3 py-1 text-xs font-medium bg-blue-100 text-blue-800 rounded-full">
                            دور نظامي
                        </span>
                    @endif
                    <a href="{{ route('admin.roles.edit', $role) }}" 
                       class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200">
                        <i class="fas fa-edit mr-2"></i>
                        تعديل
                    </a>
                    <a href="{{ route('admin.roles.index') }}" 
                       class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors duration-200">
                        <i class="fas fa-arrow-right mr-2"></i>
                        العودة
                    </a>
                </div>
            </div>
        </div>
        <div class="p-6">
            <dl class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <dt class="text-sm font-medium text-gray-500">الاسم</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $role->name }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">الاسم المعروض</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $role->display_name }}</dd>
                </div>
                @if($role->description)
                <div class="md:col-span-2">
                    <dt class="text-sm font-medium text-gray-500">الوصف</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $role->description }}</dd>
                </div>
                @endif
                <div>
                    <dt class="text-sm font-medium text-gray-500">عدد الصلاحيات</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $role->permissions->count() }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">عدد المستخدمين</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $role->users->count() }}</dd>
                </div>
            </dl>
        </div>
    </div>

    <!-- الصلاحيات -->
    <div class="bg-white rounded-xl shadow-lg border border-gray-200">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">صلاحيات الدور</h3>
        </div>
        <div class="p-6">
            @if($role->permissions->count() > 0)
                <form method="POST" action="{{ route('admin.roles.update-permissions', $role) }}">
                    @csrf
                    <div class="border border-gray-300 rounded-lg p-4 max-h-96 overflow-y-auto">
                        @foreach($permissions as $group => $groupPermissions)
                            <div class="mb-6 last:mb-0">
                                <h4 class="text-sm font-semibold text-gray-900 mb-3 pb-2 border-b border-gray-200">
                                    {{ $group ?? 'عام' }}
                                </h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                                    @foreach($groupPermissions as $permission)
                                        <label class="flex items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer {{ in_array($permission->id, $role->permissions->pluck('id')->toArray()) ? 'bg-blue-50 border-blue-300 : '' }}">
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
                    </div>
                    <div class="mt-4 flex justify-end">
                        <button type="submit" 
                                class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200">
                            <i class="fas fa-save mr-2"></i>
                            حفظ التغييرات
                        </button>
                    </div>
                </form>
            @else
                <div class="text-center py-12">
                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-key text-gray-400 text-2xl"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">لا توجد صلاحيات</h3>
                    <p class="text-gray-500 mb-4">هذا الدور لا يحتوي على أي صلاحيات</p>
                    <a href="{{ route('admin.roles.edit', $role) }}" 
                       class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200">
                        <i class="fas fa-edit mr-2"></i>
                        إضافة صلاحيات
                    </a>
                </div>
            @endif
        </div>
    </div>

    <!-- المستخدمون -->
    @if($role->users->count() > 0)
    <div class="bg-white rounded-xl shadow-lg border border-gray-200">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">المستخدمون المرتبطون بهذا الدور</h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($role->users as $user)
                    <div class="p-4 border border-gray-200 rounded-lg hover:bg-gray-50">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white font-bold">
                                {{ substr($user->name, 0, 1) }}
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ $user->name }}</p>
                                <p class="text-xs text-gray-500">{{ $user->email ?? $user->phone }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
