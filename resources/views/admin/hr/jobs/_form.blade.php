@php
    /** @var \App\Models\HrJobPosting|null $job */
    $job = $job ?? null;
@endphp

<div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-5">
    <div class="md:col-span-2">
        <label class="{{ $hrLabelClass ?? 'block text-xs font-semibold text-slate-700 mb-1.5' }}">عنوان الوظيفة *</label>
        <input name="title" value="{{ old('title', $job->title ?? '') }}" required class="{{ $hrInputClass ?? 'w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm' }}">
    </div>
    <div>
        <label class="{{ $hrLabelClass ?? 'block text-xs font-semibold text-slate-700 mb-1.5' }}">القسم</label>
        <input name="department" value="{{ old('department', $job->department ?? '') }}" class="{{ $hrInputClass ?? 'w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm' }}">
    </div>
    <div>
        <label class="{{ $hrLabelClass ?? 'block text-xs font-semibold text-slate-700 mb-1.5' }}">المكان</label>
        <input name="location" value="{{ old('location', $job->location ?? '') }}" class="{{ $hrInputClass ?? 'w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm' }}">
    </div>
    <div>
        <label class="{{ $hrLabelClass ?? 'block text-xs font-semibold text-slate-700 mb-1.5' }}">نوع التوظيف</label>
        <input name="employment_type" value="{{ old('employment_type', $job->employment_type ?? '') }}" placeholder="دوام كامل / جزئي / عن بُعد…" class="{{ $hrInputClass ?? 'w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm' }}">
    </div>
    <div class="flex items-center gap-3 pt-2 md:pt-6">
        <input type="checkbox" name="is_published" value="1" id="is_published" class="rounded border-slate-300 text-pink-600 focus:ring-pink-500/20"
               @checked(old('is_published', $job->is_published ?? false))>
        <label for="is_published" class="text-sm font-semibold text-slate-800">نشر الوظيفة في صفحة التوظيف</label>
    </div>
    <div class="md:col-span-2">
        <label class="{{ $hrLabelClass ?? 'block text-xs font-semibold text-slate-700 mb-1.5' }}">الوصف</label>
        <textarea name="description" rows="6" class="{{ $hrTextareaClass ?? 'w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm resize-y min-h-[100px]' }}">{{ old('description', $job->description ?? '') }}</textarea>
    </div>
    <div class="md:col-span-2">
        <label class="{{ $hrLabelClass ?? 'block text-xs font-semibold text-slate-700 mb-1.5' }}">المتطلبات</label>
        <textarea name="requirements" rows="5" class="{{ $hrTextareaClass ?? 'w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm resize-y min-h-[100px]' }}">{{ old('requirements', $job->requirements ?? '') }}</textarea>
    </div>
</div>
