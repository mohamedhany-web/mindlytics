@php
    /** @var \App\Models\HrRubric|null $rubric */
    $rubric = $rubric ?? null;
    $criteria = old('criteria_json');
    if ($criteria === null) {
        $criteriaArr = $rubric?->criteria_json ?? ($defaultCriteria ?? []);
        $criteria = json_encode($criteriaArr, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }
@endphp

<div class="space-y-5">
    <div>
        <label class="{{ $hrLabelClass ?? 'block text-xs font-semibold text-slate-700 mb-1.5' }}">اسم القالب *</label>
        <input name="name" value="{{ old('name', $rubric->name ?? '') }}" required class="{{ $hrInputClass ?? 'w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm' }}">
    </div>

    <label class="inline-flex items-center gap-3 rounded-xl border-2 border-slate-200 bg-slate-50/50 px-4 py-3 cursor-pointer">
        <input type="checkbox" name="is_default" value="1" class="rounded border-slate-300 text-pink-600 focus:ring-pink-500/20"
               @checked(old('is_default', $rubric->is_default ?? false))>
        <span class="text-sm font-semibold text-slate-800">تعيينه كقالب افتراضي للتقييم</span>
    </label>

    <div>
        <label class="{{ $hrLabelClass ?? 'block text-xs font-semibold text-slate-700 mb-1.5' }}">معايير التقييم (JSON) *</label>
        <textarea name="criteria_json" rows="14" required class="{{ ($hrTextareaClass ?? 'w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm resize-y') }} font-mono text-xs leading-relaxed">{{ $criteria }}</textarea>
        <p class="text-[11px] text-slate-500 mt-2 rounded-lg bg-slate-50 border border-slate-200 px-3 py-2">
            <i class="fas fa-info-circle text-pink-600 ml-1"></i>
            مثال عنصر: <code class="font-mono text-[10px] bg-white px-1 rounded">{"key":"skills","label":"المهارات","weight":1,"max":10}</code>
        </p>
    </div>
</div>
