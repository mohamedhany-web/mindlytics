@php
    /** @var \App\Models\HrJobPosting|null $job */
    $job = $job ?? null;
    $educationLevels = config('hr.education_levels', []);
    $skillsValue = old('required_skills');
    if ($skillsValue === null && $job) {
        $skillsValue = implode(', ', $job->normalizedRequiredSkills());
    }
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
    <div>
        <label class="{{ $hrLabelClass ?? 'block text-xs font-semibold text-slate-700 mb-1.5' }}">حالة الوظيفة *</label>
        <select name="status" required class="{{ $hrSelectClass ?? 'w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm' }}">
            @foreach(\App\Models\HrJobPosting::STATUSES as $k => $label)
                <option value="{{ $k }}" @selected(old('status', $job->status ?? 'open') === $k)>{{ $label }}</option>
            @endforeach
        </select>
    </div>
    <div class="flex items-center gap-3 pt-2 md:pt-6">
        <input type="checkbox" name="is_published" value="1" id="is_published" class="rounded border-slate-300 text-pink-600 focus:ring-pink-500/20"
               @checked(old('is_published', $job->is_published ?? false))>
        <label for="is_published" class="text-sm font-semibold text-slate-800">نشر الوظيفة في صفحة التوظيف</label>
    </div>

    <div class="md:col-span-2 rounded-xl border-2 border-pink-100 bg-pink-50/40 p-4 space-y-4">
        <h4 class="text-sm font-black text-slate-900 flex items-center gap-2">
            <i class="fas fa-calculator text-pink-600"></i>
            معايير التقييم التلقائي (Rule-Based ATS)
        </h4>
        <div>
            <label class="{{ $hrLabelClass ?? 'block text-xs font-semibold text-slate-700 mb-1.5' }}">المهارات المطلوبة</label>
            <textarea name="required_skills" rows="2" placeholder="Excel, SQL, Power BI, Python — افصل بفاصلة"
                      class="{{ $hrTextareaClass ?? 'w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm resize-y' }}">{{ $skillsValue }}</textarea>
            <p class="text-[11px] text-slate-500 mt-1">تُستخدم لمطابقة مهارات السيرة — وزنها 60% من النتيجة.</p>
        </div>
        <div class="grid sm:grid-cols-2 gap-4">
            <div>
                <label class="{{ $hrLabelClass ?? 'block text-xs font-semibold text-slate-700 mb-1.5' }}">سنوات الخبرة المطلوبة</label>
                <input type="number" name="required_experience" min="0" max="50" step="1"
                       value="{{ old('required_experience', $job->required_experience ?? '') }}"
                       class="{{ $hrInputClass ?? 'w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm' }}">
                <p class="text-[11px] text-slate-500 mt-1">وزنها 30% من النتيجة.</p>
            </div>
            <div>
                <label class="{{ $hrLabelClass ?? 'block text-xs font-semibold text-slate-700 mb-1.5' }}">المؤهل الدراسي المطلوب</label>
                <select name="required_education" class="{{ $hrSelectClass ?? 'w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm' }}">
                    <option value="">— غير محدد —</option>
                    @foreach($educationLevels as $k => $meta)
                        <option value="{{ $k }}" @selected(old('required_education', $job->required_education ?? '') === $k)>{{ $meta['label'] ?? $k }}</option>
                    @endforeach
                </select>
                <p class="text-[11px] text-slate-500 mt-1">وزنها 10% من النتيجة.</p>
            </div>
        </div>
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
