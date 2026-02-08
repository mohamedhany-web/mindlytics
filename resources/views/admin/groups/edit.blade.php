@extends('layouts.admin')

@section('title', 'تعديل المجموعة')

@section('content')
<div class="w-full max-w-full px-4 py-6 space-y-6">
    <!-- هيدر الصفحة -->
    <div class="bg-gradient-to-l from-indigo-600 via-blue-600 to-cyan-500 rounded-2xl p-6 text-white shadow-lg">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="min-w-0">
                <nav class="text-sm text-white/80 mb-2">
                    <a href="{{ route('admin.dashboard') }}" class="hover:text-white">لوحة التحكم</a>
                    <span class="mx-2">/</span>
                    <a href="{{ route('admin.groups.index') }}" class="hover:text-white">المجموعات</a>
                    <span class="mx-2">/</span>
                    <span class="text-white truncate">{{ Str::limit($group->name, 30) }}</span>
                    <span class="mx-2">/</span>
                    <span class="text-white">تعديل</span>
                </nav>
                <h1 class="text-xl sm:text-2xl font-bold mt-1">تعديل المجموعة</h1>
                <p class="text-sm text-white/90 mt-1 truncate">{{ $group->name }}</p>
            </div>
            <div class="flex flex-wrap gap-2 flex-shrink-0">
                <a href="{{ route('admin.groups.show', $group) }}" class="inline-flex items-center gap-2 bg-white/20 hover:bg-white/30 text-white px-4 py-2.5 rounded-xl font-medium transition-colors border border-white/30">
                    <i class="fas fa-eye"></i> عرض
                </a>
                <a href="{{ route('admin.groups.index') }}" class="inline-flex items-center gap-2 bg-white/20 hover:bg-white/30 text-white px-4 py-2.5 rounded-xl font-medium transition-colors border border-white/30">
                    <i class="fas fa-arrow-right"></i> رجوع
                </a>
            </div>
        </div>
    </div>

    <!-- نموذج التعديل -->
    <div class="bg-white rounded-2xl border border-gray-200 shadow-lg overflow-hidden">
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
            <h4 class="text-lg font-bold text-gray-900">بيانات المجموعة</h4>
        </div>
        <form action="{{ route('admin.groups.update', $group) }}" method="POST" class="p-6 sm:p-8">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">الكورس <span class="text-red-500">*</span></label>
                    <select name="course_id" required class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                        @foreach($courses as $course)
                            <option value="{{ $course->id }}" {{ old('course_id', $group->course_id) == $course->id ? 'selected' : '' }}>{{ $course->title }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">اسم المجموعة <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $group->name) }}" required
                           class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">الوصف</label>
                    <textarea name="description" rows="3" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">{{ old('description', $group->description) }}</textarea>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">قائد المجموعة</label>
                    <select name="leader_id" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                        <option value="">لا يوجد</option>
                        @foreach($students as $student)
                            <option value="{{ $student->id }}" {{ old('leader_id', $group->leader_id) == $student->id ? 'selected' : '' }}>{{ $student->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">الحد الأقصى للأعضاء <span class="text-red-500">*</span></label>
                    <input type="number" name="max_members" value="{{ old('max_members', $group->max_members) }}" min="2" max="50" required
                           class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">الحالة <span class="text-red-500">*</span></label>
                    <select name="status" required class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                        <option value="active" {{ old('status', $group->status) == 'active' ? 'selected' : '' }}>نشط</option>
                        <option value="inactive" {{ old('status', $group->status) == 'inactive' ? 'selected' : '' }}>غير نشط</option>
                        <option value="archived" {{ old('status', $group->status) == 'archived' ? 'selected' : '' }}>مؤرشف</option>
                    </select>
                </div>
            </div>

            <div class="flex flex-wrap items-center justify-end gap-3 mt-8 pt-6 border-t border-gray-200">
                <a href="{{ route('admin.groups.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-xl transition-colors border border-gray-200">إلغاء</a>
                <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-xl transition-colors shadow-lg shadow-indigo-600/20">
                    <i class="fas fa-save"></i> حفظ التغييرات
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
