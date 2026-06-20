@php
    /** @var \App\Models\HrRubric|null $rubric */
    $rubric = $rubric ?? null;
    $criteria = old('criteria_json');
    if ($criteria === null) {
        $criteriaArr = $rubric?->criteria_json ?? ($defaultCriteria ?? []);
        $criteria = json_encode($criteriaArr, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }
@endphp

<div class="space-y-4">
    <div>
        <label class="block text-xs font-semibold text-slate-700 mb-1">اسم القالب *</label>
        <input name="name" value="{{ old('name', $rubric->name ?? '') }}" required class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm">
    </div>

    <label class="inline-flex items-center gap-2">
        <input type="checkbox" name="is_default" value="1" class="rounded border-slate-300 text-pink-600"
               @checked(old('is_default', $rubric->is_default ?? false))>
        <span class="text-sm font-semibold text-slate-800">تعيينه كافتراضي</span>
    </label>

    <div>
        <label class="block text-xs font-semibold text-slate-700 mb-1">criteria_json (JSON) *</label>
        <textarea name="criteria_json" rows="12" required class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm font-mono">{{ $criteria }}</textarea>
        <p class="text-[11px] text-slate-500 mt-1">
            مثال عنصر: <span class="font-mono">{"key":"skills","label":"المهارات","weight":1,"max":10}</span>
        </p>
    </div>
</div>

