@php
    /** @var \App\Models\HrRubric|null $rubric */
    $rubric = $rubric ?? null;

    $criteriaRows = old('criteria');
    if (! is_array($criteriaRows) || $criteriaRows === []) {
        $existing = $rubric?->normalizedCriteria() ?? [];
        $criteriaRows = $existing !== [] ? $existing : ($defaultCriteria ?? \App\Models\HrRubric::defaultCriteriaTemplate());
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
        <div class="flex flex-wrap items-center justify-between gap-2 mb-3">
            <label class="{{ $hrLabelClass ?? 'block text-xs font-semibold text-slate-700' }}">معايير التقييم *</label>
            <button type="button" id="hr-add-criterion" class="{{ $hrBtnSecondary ?? 'inline-flex items-center gap-2 px-3 py-2 rounded-xl border border-slate-200 text-sm font-semibold' }} !py-1.5 !px-3 text-xs">
                <i class="fas fa-plus"></i>
                إضافة معيار
            </button>
        </div>

        <div id="hr-criteria-list" class="space-y-3">
            @foreach($criteriaRows as $i => $row)
                @php
                    $key = (string) ($row['key'] ?? '');
                    $label = (string) ($row['label'] ?? '');
                    $weight = (float) ($row['weight'] ?? 1);
                    $max = (float) ($row['max'] ?? 10);
                @endphp
                <div class="hr-criterion-row rounded-xl border-2 border-slate-200 bg-slate-50/80 p-4 space-y-3">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[11px] font-semibold text-slate-600 mb-1">المفتاح (key) *</label>
                            <input name="criteria[{{ $i }}][key]" value="{{ old('criteria.'.$i.'.key', $key) }}" required
                                   pattern="[a-z0-9_]+" title="حروف إنجليزية صغيرة وأرقام و _ فقط"
                                   class="{{ $hrInputClass ?? 'w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm' }} font-mono text-xs">
                        </div>
                        <div>
                            <label class="block text-[11px] font-semibold text-slate-600 mb-1">الاسم الظاهر *</label>
                            <input name="criteria[{{ $i }}][label]" value="{{ old('criteria.'.$i.'.label', $label) }}" required
                                   class="{{ $hrInputClass ?? 'w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm' }}">
                        </div>
                        <div>
                            <label class="block text-[11px] font-semibold text-slate-600 mb-1">الوزن</label>
                            <input type="number" name="criteria[{{ $i }}][weight]" value="{{ old('criteria.'.$i.'.weight', $weight) }}" step="0.1" min="0" required
                                   class="{{ $hrInputClass ?? 'w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm' }}">
                        </div>
                        <div>
                            <label class="block text-[11px] font-semibold text-slate-600 mb-1">الحد الأقصى</label>
                            <input type="number" name="criteria[{{ $i }}][max]" value="{{ old('criteria.'.$i.'.max', $max) }}" step="0.1" min="0.1" required
                                   class="{{ $hrInputClass ?? 'w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm' }}">
                        </div>
                    </div>
                    <div class="flex justify-end">
                        <button type="button" class="hr-remove-criterion text-xs font-semibold text-rose-600 hover:text-rose-700">
                            <i class="fas fa-trash-alt ml-1"></i>
                            حذف المعيار
                        </button>
                    </div>
                </div>
            @endforeach
        </div>

        <p class="text-[11px] text-slate-500 mt-2 rounded-lg bg-slate-50 border border-slate-200 px-3 py-2">
            <i class="fas fa-info-circle text-pink-600 ml-1"></i>
            المفتاح (key) يُستخدم داخلياً — استخدم حروف إنجليزية صغيرة مثل: <code class="font-mono text-[10px] bg-white px-1 rounded">skills</code>
        </p>
    </div>
</div>

<template id="hr-criterion-template">
    <div class="hr-criterion-row rounded-xl border-2 border-slate-200 bg-slate-50/80 p-4 space-y-3">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <div>
                <label class="block text-[11px] font-semibold text-slate-600 mb-1">المفتاح (key) *</label>
                <input name="criteria[__INDEX__][key]" required pattern="[a-z0-9_]+" title="حروف إنجليزية صغيرة وأرقام و _ فقط"
                       class="{{ $hrInputClass ?? 'w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm' }} font-mono text-xs">
            </div>
            <div>
                <label class="block text-[11px] font-semibold text-slate-600 mb-1">الاسم الظاهر *</label>
                <input name="criteria[__INDEX__][label]" required class="{{ $hrInputClass ?? 'w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm' }}">
            </div>
            <div>
                <label class="block text-[11px] font-semibold text-slate-600 mb-1">الوزن</label>
                <input type="number" name="criteria[__INDEX__][weight]" value="1" step="0.1" min="0" required class="{{ $hrInputClass ?? 'w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm' }}">
            </div>
            <div>
                <label class="block text-[11px] font-semibold text-slate-600 mb-1">الحد الأقصى</label>
                <input type="number" name="criteria[__INDEX__][max]" value="10" step="0.1" min="0.1" required class="{{ $hrInputClass ?? 'w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm' }}">
            </div>
        </div>
        <div class="flex justify-end">
            <button type="button" class="hr-remove-criterion text-xs font-semibold text-rose-600 hover:text-rose-700">
                <i class="fas fa-trash-alt ml-1"></i>
                حذف المعيار
            </button>
        </div>
    </div>
</template>

@push('scripts')
<script>
(function () {
    const list = document.getElementById('hr-criteria-list');
    const tpl = document.getElementById('hr-criterion-template');
    const addBtn = document.getElementById('hr-add-criterion');
    if (!list || !tpl || !addBtn) return;

    function reindex() {
        list.querySelectorAll('.hr-criterion-row').forEach(function (row, i) {
            row.querySelectorAll('[name^="criteria["]').forEach(function (input) {
                input.name = input.name.replace(/criteria\[\d+\]/, 'criteria[' + i + ']');
            });
        });
    }

    function bindRemove(row) {
        const btn = row.querySelector('.hr-remove-criterion');
        if (!btn) return;
        btn.addEventListener('click', function () {
            if (list.querySelectorAll('.hr-criterion-row').length <= 1) {
                alert('يجب أن يبقى معيار واحد على الأقل.');
                return;
            }
            row.remove();
            reindex();
        });
    }

    list.querySelectorAll('.hr-criterion-row').forEach(bindRemove);

    addBtn.addEventListener('click', function () {
        const index = list.querySelectorAll('.hr-criterion-row').length;
        const html = tpl.innerHTML.replace(/__INDEX__/g, String(index));
        const wrap = document.createElement('div');
        wrap.innerHTML = html.trim();
        const row = wrap.firstElementChild;
        list.appendChild(row);
        bindRemove(row);
    });
})();
</script>
@endpush
