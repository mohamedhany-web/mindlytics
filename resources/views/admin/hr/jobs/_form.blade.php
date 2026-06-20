@php
    /** @var \App\Models\HrJobPosting|null $job */
    $job = $job ?? null;
@endphp

<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div class="md:col-span-2">
        <label class="block text-xs font-semibold text-slate-700 mb-1">عنوان الوظيفة *</label>
        <input name="title" value="{{ old('title', $job->title ?? '') }}" required class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm">
    </div>
    <div>
        <label class="block text-xs font-semibold text-slate-700 mb-1">القسم</label>
        <input name="department" value="{{ old('department', $job->department ?? '') }}" class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm">
    </div>
    <div>
        <label class="block text-xs font-semibold text-slate-700 mb-1">المكان</label>
        <input name="location" value="{{ old('location', $job->location ?? '') }}" class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm">
    </div>
    <div>
        <label class="block text-xs font-semibold text-slate-700 mb-1">نوع التوظيف</label>
        <input name="employment_type" value="{{ old('employment_type', $job->employment_type ?? '') }}" placeholder="Full-time / Part-time ..." class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm">
    </div>
    <div class="flex items-center gap-2 pt-6">
        <input type="checkbox" name="is_published" value="1" class="rounded border-slate-300 text-pink-600"
               @checked(old('is_published', $job->is_published ?? false))>
        <label class="text-sm font-semibold text-slate-800">نشر الوظيفة</label>
    </div>
    <div class="md:col-span-2">
        <label class="block text-xs font-semibold text-slate-700 mb-1">الوصف</label>
        <textarea name="description" rows="6" class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm">{{ old('description', $job->description ?? '') }}</textarea>
    </div>
    <div class="md:col-span-2">
        <label class="block text-xs font-semibold text-slate-700 mb-1">المتطلبات</label>
        <textarea name="requirements" rows="5" class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm">{{ old('requirements', $job->requirements ?? '') }}</textarea>
    </div>
</div>

