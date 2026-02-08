@extends('layouts.app')

@section('title', 'إضافة مورد - كورس أوفلاين')
@section('header', 'إضافة مورد')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <!-- هيدر الصفحة (عرض الصفحة) -->
    <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-5 sm:p-6 mb-6">
        <nav class="text-sm text-slate-500 mb-2">
            <a href="{{ route('instructor.offline-courses.index') }}" class="hover:text-amber-600 transition-colors">كورساتي الأوفلاين</a>
            <span class="mx-2">/</span>
            <a href="{{ route('instructor.offline-courses.show', $offlineCourse) }}" class="hover:text-amber-600 transition-colors">{{ $offlineCourse->title }}</a>
            <span class="mx-2">/</span>
            <a href="{{ route('instructor.offline-courses.resources.index', $offlineCourse) }}" class="hover:text-amber-600 transition-colors">الموارد</a>
            <span class="mx-2">/</span>
            <span class="text-slate-700 font-semibold">إضافة مورد</span>
        </nav>
        <div class="flex flex-wrap items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-sky-100 text-sky-600 flex items-center justify-center shrink-0">
                <i class="fas fa-file-alt text-lg"></i>
            </div>
            <div class="min-w-0 flex-1">
                <h1 class="text-xl sm:text-2xl font-bold text-slate-800">إضافة مورد (أوفلاين)</h1>
                <p class="text-sm text-slate-600 mt-0.5">رفع ملف واحد أو عدة ملفات (PDF، Word، صور، إلخ) أو إضافة رابط</p>
            </div>
        </div>
    </div>

    <!-- بطاقة النموذج -->
    <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-6 sm:p-8">
        <form action="{{ route('instructor.offline-courses.resources.store', $offlineCourse) }}" method="post" enctype="multipart/form-data">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">العنوان <span class="text-red-500">*</span></label>
                    <input type="text" name="title" value="{{ old('title') }}" required class="w-full rounded-xl border border-slate-200 px-4 py-2.5 focus:ring-2 focus:ring-sky-500 focus:border-sky-500" placeholder="مثال: ملخص الوحدة الأولى">
                    @error('title')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">الوصف</label>
                    <textarea name="description" rows="3" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 focus:ring-2 focus:ring-sky-500">{{ old('description') }}</textarea>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">نوع المورد <span class="text-red-500">*</span></label>
                    <select name="type" id="resourceType" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 focus:ring-2 focus:ring-sky-500">
                        <option value="file" {{ old('type', 'file') === 'file' ? 'selected' : '' }}>ملف مرفوع</option>
                        <option value="link" {{ old('type') === 'link' ? 'selected' : '' }}>رابط</option>
                    </select>
                </div>
                <div id="fileField" class="">
                    <label class="block text-sm font-semibold text-slate-700 mb-1">ملف واحد</label>
                    <input type="file" name="file" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 mb-2">
                    <label class="block text-sm font-semibold text-slate-700 mb-1 mt-3">أو عدة ملفات (PDF، Word، صور، فيديو، إلخ)</label>
                    <input type="file" name="files[]" multiple class="w-full rounded-xl border border-slate-200 px-4 py-2.5">
                    <p class="text-xs text-slate-500 mt-1">يمكنك اختيار أكثر من ملف. الحد الأقصى 50 ميجا لكل ملف.</p>
                    @error('file')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                    @error('files.*')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
                <div id="linkField" class="hidden">
                    <label class="block text-sm font-semibold text-slate-700 mb-1">رابط التحميل أو المورد</label>
                    <input type="url" name="url" value="{{ old('url') }}" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 focus:ring-2 focus:ring-sky-500" placeholder="https://...">
                    @error('url')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
                @if($groups->isNotEmpty())
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">لمجموعة محددة (اختياري)</label>
                        <select name="group_id" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 focus:ring-2 focus:ring-sky-500">
                            <option value="">كل الطلاب</option>
                            @foreach($groups as $g)
                                <option value="{{ $g->id }}" {{ old('group_id') == $g->id ? 'selected' : '' }}>{{ $g->name }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif
            </div>
            <div class="mt-6 flex gap-3">
                <button type="submit" class="px-4 py-2.5 bg-sky-600 text-white rounded-xl font-semibold hover:bg-sky-700">حفظ</button>
                <a href="{{ route('instructor.offline-courses.resources.index', $offlineCourse) }}" class="px-4 py-2.5 bg-slate-100 text-slate-700 rounded-xl font-semibold hover:bg-slate-200">إلغاء</a>
            </div>
        </form>
    </div>
</div>
@push('scripts')
<script>
document.getElementById('resourceType').addEventListener('change', function() {
    var type = this.value;
    document.getElementById('fileField').classList.toggle('hidden', type !== 'file');
    document.getElementById('linkField').classList.toggle('hidden', type !== 'link');
});
document.getElementById('resourceType').dispatchEvent(new Event('change'));
</script>
@endpush
@endsection
