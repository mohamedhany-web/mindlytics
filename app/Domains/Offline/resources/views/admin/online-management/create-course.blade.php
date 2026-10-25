@extends('layouts.admin')

@section('title', 'كورس أونلاين فقط — جديد')
@section('header', 'إنشاء كورس أونلاين فقط')

@section('content')
<div class="space-y-6">
    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">كورس أونلاين فقط — جديد</h1>
                <p class="text-gray-600 mt-1">يُنشئ كورساً بنفس بنية الأوفلاين مع علامة أونلاين فقط ومجموعة افتراضية للحجز الأونلاين</p>
            </div>
            <a href="{{ route('admin.online-management.index') }}" class="text-gray-600 hover:text-gray-900">
                <i class="fas fa-arrow-right mr-2"></i>العودة للقائمة
            </a>
        </div>
    </div>

    <form action="{{ route('admin.online-management.courses.store') }}" method="post" class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
        @csrf
        @if($errors->has('error'))
            <div class="mb-6 rounded-lg bg-red-50 text-red-800 text-sm px-4 py-3 border border-red-100">{{ $errors->first('error') }}</div>
        @endif

        <div class="space-y-6">
            <div class="border-b border-gray-200 pb-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">المعلومات الأساسية</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">عنوان الكورس *</label>
                        <input type="text" name="title" value="{{ old('title') }}" required
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                        @error('title')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">الوصف</label>
                        <textarea name="description" rows="3" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">{{ old('description') }}</textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">المدرب *</label>
                        @if($instructors->isEmpty())
                            <div class="rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900">
                                لا يوجد مدربون نشطون. أنشئ حساب مدرب أو فعّل مدرباً موجوداً ثم عد إلى هذه الصفحة.
                            </div>
                        @else
                            <select name="instructor_id" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                                <option value="">اختر المدرب</option>
                                @foreach($instructors as $ins)
                                    <option value="{{ $ins->id }}" @selected(old('instructor_id') == $ins->id)>{{ $ins->name }}</option>
                                @endforeach
                            </select>
                        @endif
                        @error('instructor_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">حالة الكورس *</label>
                        <select name="status" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                            <option value="active" @selected(old('status', 'active') === 'active')>نشط</option>
                            <option value="draft" @selected(old('status') === 'draft')>مسودة</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="border-b border-gray-200 pb-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">السعر والمجموعة الأونلاين</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">السعر (ج.م)</label>
                        <input type="number" name="price" value="{{ old('price', 0) }}" min="0" step="0.01"
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                        @error('price')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">سعة أونلاين (طالب) *</label>
                        <input type="number" name="max_students_online" value="{{ old('max_students_online', 30) }}" min="1" max="500" required
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                        @error('max_students_online')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">اسم المجموعة الافتراضية (اختياري)</label>
                        <input type="text" name="group_name" value="{{ old('group_name') }}" placeholder="مثال: الدفعة الأولى — أونلاين"
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                    </div>
                </div>
            </div>

            <div class="flex flex-wrap gap-3">
                <button type="submit" class="inline-flex items-center gap-2 px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors">
                    <i class="fas fa-save"></i>
                    حفظ وإنشاء المجموعة
                </button>
                <a href="{{ route('admin.online-management.index') }}" class="inline-flex items-center px-5 py-3 border border-gray-300 text-gray-700 rounded-lg font-medium hover:bg-gray-50 transition-colors">إلغاء</a>
            </div>
        </div>
    </form>
</div>
@endsection
