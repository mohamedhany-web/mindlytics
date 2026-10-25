@extends('layouts.admin')

@section('title', 'إضافة مسابقة')
@section('header', 'إضافة مسابقة')

@section('content')
<div class="w-full">
    <form action="{{ route('admin.community.competitions.store') }}" method="POST" class="space-y-6">
        @csrf

        {{-- رأس الصفحة وأزرار الإجراء --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <p class="text-slate-600 text-sm">أضف مسابقة جديدة لمجتمع البيانات. سيظهر العنوان والوصف والتواريخ للمشاركين.</p>
            <div class="flex gap-3 flex-shrink-0">
                <a href="{{ route('admin.community.competitions.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-100 text-slate-700 font-bold hover:bg-slate-200 transition-colors">
                    <i class="fas fa-arrow-right"></i>
                    <span>العودة للقائمة</span>
                </a>
                <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-cyan-600 text-white font-bold hover:bg-cyan-700 transition-colors shadow-md hover:shadow-lg">
                    <i class="fas fa-save"></i>
                    <span>حفظ المسابقة</span>
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
            {{-- القسم الرئيسي: المحتوى --}}
            <div class="xl:col-span-2 space-y-6">
                {{-- معلومات أساسية --}}
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/70">
                        <h2 class="text-lg font-black text-slate-900 flex items-center gap-2">
                            <span class="w-9 h-9 rounded-xl bg-cyan-100 text-cyan-600 flex items-center justify-center"><i class="fas fa-info-circle"></i></span>
                            المعلومات الأساسية
                        </h2>
                    </div>
                    <div class="p-6 space-y-5">
                        <div>
                            <label for="title" class="block text-sm font-bold text-slate-700 mb-2">عنوان المسابقة <span class="text-red-500">*</span></label>
                            <input type="text" name="title" id="title" value="{{ old('title') }}" required
                                   placeholder="مثال: مسابقة تحليل البيانات 2025"
                                   class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-cyan-500 focus:ring-2 focus:ring-cyan-500/20 transition-colors">
                            @error('title')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="description" class="block text-sm font-bold text-slate-700 mb-2">الوصف</label>
                            <textarea name="description" id="description" rows="5" placeholder="وصف مختصر أو تفصيلي عن المسابقة وأهدافها..."
                                      class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-cyan-500 focus:ring-2 focus:ring-cyan-500/20 transition-colors resize-y min-h-[120px]">{{ old('description') }}</textarea>
                            @error('description')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </div>

                {{-- القواعد --}}
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/70">
                        <h2 class="text-lg font-black text-slate-900 flex items-center gap-2">
                            <span class="w-9 h-9 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center"><i class="fas fa-list-ol"></i></span>
                            القواعد والشروط
                        </h2>
                    </div>
                    <div class="p-6">
                        <label for="rules" class="block text-sm font-bold text-slate-700 mb-2">نص القواعد (اختياري)</label>
                        <textarea name="rules" id="rules" rows="4" placeholder="قواعد المسابقة، معايير التقييم، أو أي شروط مشاركة..."
                                  class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-cyan-500 focus:ring-2 focus:ring-cyan-500/20 transition-colors resize-y min-h-[100px]">{{ old('rules') }}</textarea>
                        @error('rules')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>

            {{-- الشريط الجانبي: التواريخ والحالة --}}
            <div class="space-y-6">
                {{-- التواريخ --}}
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/70">
                        <h2 class="text-lg font-black text-slate-900 flex items-center gap-2">
                            <span class="w-9 h-9 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center"><i class="fas fa-calendar-alt"></i></span>
                            التواريخ
                        </h2>
                    </div>
                    <div class="p-6 space-y-5">
                        <div>
                            <label for="start_at" class="block text-sm font-bold text-slate-700 mb-2">تاريخ ووقت البداية</label>
                            <input type="datetime-local" name="start_at" id="start_at" value="{{ old('start_at') }}"
                                   class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-cyan-500 focus:ring-2 focus:ring-cyan-500/20 transition-colors">
                            @error('start_at')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="end_at" class="block text-sm font-bold text-slate-700 mb-2">تاريخ ووقت النهاية</label>
                            <input type="datetime-local" name="end_at" id="end_at" value="{{ old('end_at') }}"
                                   class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-cyan-500 focus:ring-2 focus:ring-cyan-500/20 transition-colors">
                            @error('end_at')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </div>

                {{-- الحالة --}}
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/70">
                        <h2 class="text-lg font-black text-slate-900 flex items-center gap-2">
                            <span class="w-9 h-9 rounded-xl bg-green-100 text-green-600 flex items-center justify-center"><i class="fas fa-toggle-on"></i></span>
                            الحالة
                        </h2>
                    </div>
                    <div class="p-6">
                        <label class="flex items-center gap-3 p-4 rounded-xl border-2 border-slate-100 hover:border-cyan-100 cursor-pointer transition-colors has-[:checked]:border-cyan-200 has-[:checked]:bg-cyan-50/50">
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                                   class="w-5 h-5 rounded border-slate-300 text-cyan-600 focus:ring-cyan-500">
                            <span class="font-bold text-slate-800">مسابقة نشطة</span>
                            <span class="text-slate-500 text-sm">تظهر للمستخدمين في المجتمع</span>
                        </label>
                        @error('is_active')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- أزرار أسفل الصفحة (للموبايل) --}}
        <div class="flex flex-wrap gap-3 pt-2 xl:hidden">
            <button type="submit" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-cyan-600 text-white font-bold hover:bg-cyan-700 transition-colors shadow-md">
                <i class="fas fa-save"></i>
                <span>حفظ المسابقة</span>
            </button>
            <a href="{{ route('admin.community.competitions.index') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-slate-100 text-slate-700 font-bold hover:bg-slate-200 transition-colors">إلغاء</a>
        </div>
    </form>
</div>
@endsection
