@extends('layouts.admin')

@section('title', 'إضافة مهمة لمدرب - Mindlytics')
@section('header', 'مهام المدربين')

@section('content')
<div class="space-y-6 sm:space-y-10">
    <!-- رجوع -->
    <div>
        <a href="{{ route('admin.tasks.index') }}"
           class="inline-flex items-center gap-2 text-sky-600 hover:text-sky-800 font-semibold text-sm">
            <i class="fas fa-arrow-right"></i>
            رجوع لقائمة المهام
        </a>
    </div>

    <!-- نموذج إضافة مهمة -->
    <section class="rounded-3xl bg-white/95 backdrop-blur border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-5 py-6 sm:px-8 lg:px-12 border-b border-slate-200">
            <h2 class="text-2xl font-bold text-slate-900 flex items-center gap-3">
                <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-sky-100 text-sky-600">
                    <i class="fas fa-plus text-xl"></i>
                </span>
                إضافة مهمة جديدة لمدرب
            </h2>
            <p class="text-sm text-slate-500 mt-2">المهمة ستظهر في صفحة «المهام من الإدارة» لدى المدرب المختار.</p>
        </div>

        <form action="{{ route('admin.tasks.store') }}" method="POST" class="p-5 sm:p-8 lg:px-12">
            @csrf

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 sm:gap-8">
                <!-- المدرب -->
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">المدرب *</label>
                    <select name="user_id" required
                            class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-slate-900 focus:ring-2 focus:ring-sky-500 focus:border-sky-400 transition-all">
                        <option value="">اختر المدرب</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                {{ $user->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('user_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- عنوان المهمة -->
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">عنوان المهمة *</label>
                    <input type="text" name="title" value="{{ old('title') }}" required
                           class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-slate-900 focus:ring-2 focus:ring-sky-500 focus:border-sky-400 transition-all"
                           placeholder="مثال: إعداد محتوى الوحدة الثالثة">
                    @error('title')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- الوصف -->
            <div class="mt-6">
                <label class="block text-sm font-bold text-slate-700 mb-2">الوصف</label>
                <textarea name="description" rows="4"
                          class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-slate-900 focus:ring-2 focus:ring-sky-500 focus:border-sky-400 transition-all"
                          placeholder="تفاصيل إضافية عن المهمة...">{{ old('description') }}</textarea>
                @error('description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mt-6">
                <!-- الأولوية -->
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">الأولوية</label>
                    <select name="priority"
                            class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-slate-900 focus:ring-2 focus:ring-sky-500 focus:border-sky-400 transition-all">
                        <option value="low" {{ old('priority', 'medium') == 'low' ? 'selected' : '' }}>منخفضة</option>
                        <option value="medium" {{ old('priority', 'medium') == 'medium' ? 'selected' : '' }}>متوسطة</option>
                        <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>عالية</option>
                        <option value="urgent" {{ old('priority') == 'urgent' ? 'selected' : '' }}>عاجلة</option>
                    </select>
                </div>

                <!-- تاريخ الاستحقاق -->
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">تاريخ الاستحقاق</label>
                    <input type="datetime-local" name="due_date" value="{{ old('due_date') }}"
                           class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-slate-900 focus:ring-2 focus:ring-sky-500 focus:border-sky-400 transition-all">
                    @error('due_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex flex-wrap gap-4 justify-end mt-8 pt-6 border-t border-slate-200">
                <a href="{{ route('admin.tasks.index') }}"
                   class="inline-flex items-center gap-2 px-6 py-3 rounded-2xl border border-slate-300 text-slate-700 font-semibold hover:bg-slate-50 transition-all">
                    إلغاء
                </a>
                <button type="submit"
                        class="inline-flex items-center gap-2 px-6 py-3 rounded-2xl bg-sky-600 text-white font-semibold shadow hover:bg-sky-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-500 transition-all">
                    <i class="fas fa-save"></i>
                    حفظ المهمة
                </button>
            </div>
        </form>
    </section>
</div>
@endsection
