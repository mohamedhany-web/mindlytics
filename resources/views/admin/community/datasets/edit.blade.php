@extends('layouts.admin')

@section('title', 'تعديل مجموعة البيانات')
@section('header', 'تعديل مجموعة البيانات')

@section('content')
<div class="w-full">
    <form action="{{ route('admin.community.datasets.update', $dataset) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')

        {{-- رأس الصفحة وأزرار الإجراء --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <p class="text-slate-600 text-sm">تعديل بيانات مجموعة البيانات. التغييرات تنعكس فوراً في واجهة المجتمع.</p>
            <div class="flex gap-3 flex-shrink-0">
                <a href="{{ route('admin.community.datasets.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-100 text-slate-700 font-bold hover:bg-slate-200 transition-colors">
                    <i class="fas fa-arrow-right"></i>
                    <span>العودة للقائمة</span>
                </a>
                <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-blue-600 text-white font-bold hover:bg-blue-700 transition-colors shadow-md hover:shadow-lg">
                    <i class="fas fa-save"></i>
                    <span>حفظ التعديلات</span>
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
            {{-- القسم الرئيسي --}}
            <div class="xl:col-span-2 space-y-6">
                {{-- المعلومات الأساسية --}}
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/70">
                        <h2 class="text-lg font-black text-slate-900 flex items-center gap-2">
                            <span class="w-9 h-9 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center"><i class="fas fa-info-circle"></i></span>
                            المعلومات الأساسية
                        </h2>
                    </div>
                    <div class="p-6 space-y-5">
                        <div>
                            <label for="title" class="block text-sm font-bold text-slate-700 mb-2">عنوان مجموعة البيانات <span class="text-red-500">*</span></label>
                            <input type="text" name="title" id="title" value="{{ old('title', $dataset->title) }}" required
                                   placeholder="مثال: مجموعة بيانات المبيعات 2024"
                                   class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-colors">
                            @error('title')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="description" class="block text-sm font-bold text-slate-700 mb-2">الوصف</label>
                            <textarea name="description" id="description" rows="5" placeholder="وصف المجموعة، المصدر، الأعمدة أو طريقة الاستخدام..."
                                      class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-colors resize-y min-h-[120px]">{{ old('description', $dataset->description) }}</textarea>
                            @error('description')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </div>

                {{-- رفع ملف (Excel / CSV) --}}
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/70">
                        <h2 class="text-lg font-black text-slate-900 flex items-center gap-2">
                            <span class="w-9 h-9 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center"><i class="fas fa-file-excel"></i></span>
                            رفع ملف البيانات
                        </h2>
                    </div>
                    <div class="p-6 space-y-5">
                        @if($dataset->file_path)
                            <p class="text-sm text-slate-600">الملف الحالي: <span class="font-bold">{{ basename($dataset->file_path) }}</span> @if($dataset->file_size)({{ $dataset->file_size }})@endif</p>
                        @endif
                        <div>
                            <label for="file" class="block text-sm font-bold text-slate-700 mb-2">استبدال الملف (اختياري)</label>
                            <input type="file" name="file" id="file" accept=".xlsx,.xls,.csv"
                                   class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-colors file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-blue-50 file:text-blue-700 file:font-bold">
                            <p class="mt-1.5 text-xs text-slate-500">الحد الأقصى 100 MB. الامتدادات: xlsx, xls, csv.</p>
                            @error('file')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </div>

                {{-- رابط التحميل والمعلومات --}}
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/70">
                        <h2 class="text-lg font-black text-slate-900 flex items-center gap-2">
                            <span class="w-9 h-9 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center"><i class="fas fa-link"></i></span>
                            رابط التحميل والمعلومات
                        </h2>
                    </div>
                    <div class="p-6 space-y-5">
                        <div>
                            <label for="file_url" class="block text-sm font-bold text-slate-700 mb-2">رابط التحميل (URL)</label>
                            <input type="url" name="file_url" id="file_url" value="{{ old('file_url', $dataset->file_url) }}"
                                   placeholder="https://example.com/dataset.csv أو رابط Google Drive..."
                                   class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-colors">
                            @error('file_url')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="file_size" class="block text-sm font-bold text-slate-700 mb-2">الحجم (اختياري — يُحدّث تلقائياً عند رفع ملف جديد)</label>
                            <input type="text" name="file_size" id="file_size" value="{{ old('file_size', $dataset->file_size) }}"
                                   placeholder="مثال: 10 MB أو 2.5 GB"
                                   class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-colors">
                            @error('file_size')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- الشريط الجانبي: الحالة --}}
            <div class="space-y-6">
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/70">
                        <h2 class="text-lg font-black text-slate-900 flex items-center gap-2">
                            <span class="w-9 h-9 rounded-xl bg-green-100 text-green-600 flex items-center justify-center"><i class="fas fa-toggle-on"></i></span>
                            الحالة
                        </h2>
                    </div>
                    <div class="p-6">
                        <label class="flex items-center gap-3 p-4 rounded-xl border-2 border-slate-100 hover:border-blue-100 cursor-pointer transition-colors has-[:checked]:border-blue-200 has-[:checked]:bg-blue-50/50">
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $dataset->is_active) ? 'checked' : '' }}
                                   class="w-5 h-5 rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                            <span class="font-bold text-slate-800">مجموعة نشطة</span>
                            <span class="text-slate-500 text-sm">تظهر للمستخدمين في المجتمع</span>
                        </label>
                        @error('is_active')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- أزرار أسفل الصفحة (للموبايل) --}}
        <div class="flex flex-wrap gap-3 pt-2 xl:hidden">
            <button type="submit" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-blue-600 text-white font-bold hover:bg-blue-700 transition-colors shadow-md">
                <i class="fas fa-save"></i>
                <span>حفظ التعديلات</span>
            </button>
            <a href="{{ route('admin.community.datasets.index') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-slate-100 text-slate-700 font-bold hover:bg-slate-200 transition-colors">إلغاء</a>
        </div>
    </form>
</div>
@endsection
