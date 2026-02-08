@extends('layouts.app')

@section('title', 'تعديل مورد - كورس أوفلاين')
@section('header', 'تعديل مورد')

@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">
    <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-5">
        <nav class="text-sm text-slate-500 mb-2">
            <a href="{{ route('instructor.offline-courses.index') }}" class="hover:text-amber-600">كورساتي الأوفلاين</a>
            <span class="mx-2">/</span>
            <a href="{{ route('instructor.offline-courses.resources.index', $offlineCourse) }}" class="hover:text-amber-600">الموارد</a>
            <span class="mx-2">/</span>
            <span class="text-slate-700 font-semibold">تعديل</span>
        </nav>
        <h1 class="text-xl font-bold text-slate-800">تعديل المورد</h1>
    </div>

    <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-6">
        <form action="{{ route('instructor.offline-courses.resources.update', [$offlineCourse, $resource]) }}" method="post" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">العنوان <span class="text-red-500">*</span></label>
                    <input type="text" name="title" value="{{ old('title', $resource->title) }}" required class="w-full rounded-xl border border-slate-200 px-4 py-2.5 focus:ring-2 focus:ring-sky-500">
                    @error('title')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">الوصف</label>
                    <textarea name="description" rows="3" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 focus:ring-2 focus:ring-sky-500">{{ old('description', $resource->description) }}</textarea>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">نوع المورد</label>
                    <select name="type" id="resourceType" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 focus:ring-2 focus:ring-sky-500">
                        <option value="file" {{ old('type', $resource->type) === 'file' ? 'selected' : '' }}>ملف مرفوع</option>
                        <option value="link" {{ old('type', $resource->type) === 'link' ? 'selected' : '' }}>رابط</option>
                    </select>
                </div>
                <div id="fileField" class="">
                    @php $allFiles = $resource->getAllFiles(); @endphp
                    @if(count($allFiles) > 0)
                        <label class="block text-sm font-semibold text-slate-700 mb-1">الملفات الحالية ({{ count($allFiles) }})</label>
                        <ul class="text-sm text-slate-600 mb-3 space-y-1">
                            @foreach($allFiles as $f)
                                <li><i class="fas fa-file ml-1"></i> {{ $f['name'] ?? 'ملف' }}</li>
                            @endforeach
                        </ul>
                    @endif
                    <label class="block text-sm font-semibold text-slate-700 mb-1">إضافة ملف جديد أو عدة ملفات (اختياري)</label>
                    <input type="file" name="file" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 mb-2">
                    <input type="file" name="files[]" multiple class="w-full rounded-xl border border-slate-200 px-4 py-2.5">
                    <p class="text-xs text-slate-500 mt-1">الملفات الجديدة تُضاف للموجود. الحد الأقصى 50 ميجا لكل ملف.</p>
                    @error('file')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                    @error('files.*')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
                <div id="linkField" class="">
                    <label class="block text-sm font-semibold text-slate-700 mb-1">الرابط</label>
                    <input type="url" name="url" value="{{ old('url', $resource->url) }}" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 focus:ring-2 focus:ring-sky-500">
                    @error('url')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
                @if($groups->isNotEmpty())
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">لمجموعة محددة</label>
                        <select name="group_id" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 focus:ring-2 focus:ring-sky-500">
                            <option value="">كل الطلاب</option>
                            @foreach($groups as $g)
                                <option value="{{ $g->id }}" {{ old('group_id', $resource->group_id) == $g->id ? 'selected' : '' }}>{{ $g->name }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif
                <div>
                    <label class="inline-flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $resource->is_active) ? 'checked' : '' }} class="rounded border-slate-300">
                        <span class="text-sm font-semibold text-slate-700">نشط (يظهر للطلاب)</span>
                    </label>
                </div>
            </div>
            <div class="mt-6 flex gap-3">
                <button type="submit" class="px-4 py-2.5 bg-sky-600 text-white rounded-xl font-semibold hover:bg-sky-700">حفظ</button>
                <a href="{{ route('instructor.offline-courses.resources.index', $offlineCourse) }}" class="px-4 py-2.5 bg-slate-100 text-slate-700 rounded-xl font-semibold hover:bg-slate-200">إلغاء</a>
            </div>
        </form>
    </div>
</div>
<script>
document.getElementById('resourceType').addEventListener('change', function() {
    var type = this.value;
    document.getElementById('fileField').classList.toggle('hidden', type !== 'file');
    document.getElementById('linkField').classList.toggle('hidden', type !== 'link');
});
document.getElementById('resourceType').dispatchEvent(new Event('change'));
</script>
@endsection
