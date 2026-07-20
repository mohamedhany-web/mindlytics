{{-- Course picker + expected value autofill. Expects: $lead, $coursesCatalogUrl, optional $inputClass/$labelClass --}}
@php
    $lead = $lead ?? null;
    $inputClass = $inputClass ?? 'w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500';
    $labelClass = $labelClass ?? 'block text-sm font-medium text-gray-700 mb-1';
    $courseType = old('course_type', $lead?->course_type ?? '');
    $courseRefId = old('course_ref_id', $lead?->linkedCourseId() ?? '');
    $coursesCatalogUrl = $coursesCatalogUrl ?? route('employee.sales.courses.index');
@endphp
<div>
    <label class="{{ $labelClass }}">نوع الكورس</label>
    <select name="course_type" id="lead_course_type" class="{{ $inputClass }}">
        <option value="">— بدون —</option>
        @foreach(\App\Models\SalesLead::COURSE_TYPES as $k => $label)
            <option value="{{ $k }}" @selected($courseType === $k)>{{ $label }}</option>
        @endforeach
    </select>
    @error('course_type')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
</div>
<div>
    <label class="{{ $labelClass }}">الكورس</label>
    <select name="course_ref_id" id="lead_course_ref" class="{{ $inputClass }}" data-selected="{{ $courseRefId }}">
        <option value="">— اختر —</option>
    </select>
    <p class="text-xs text-slate-500 mt-1" id="lead_course_price_hint"></p>
    @error('course_ref_id')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
</div>

@once
@push('scripts')
<script>
(function () {
    const typeSel = document.getElementById('lead_course_type');
    const courseSel = document.getElementById('lead_course_ref');
    const priceHint = document.getElementById('lead_course_price_hint');
    const valueInput = document.querySelector('input[name="expected_value"]');
    if (!typeSel || !courseSel) return;

    const catalogUrl = @json($coursesCatalogUrl);
    const cache = {};
    let userEditedValue = false;

    if (valueInput) {
        valueInput.addEventListener('input', () => { userEditedValue = true; });
    }

    async function loadCourses(preserveSelected) {
        const type = typeSel.value;
        const selected = preserveSelected ? (courseSel.dataset.selected || courseSel.value) : '';
        courseSel.innerHTML = '<option value="">— اختر —</option>';
        if (!type) {
            if (priceHint) priceHint.textContent = '';
            return;
        }
        try {
            if (!cache[type]) {
                const res = await fetch(catalogUrl + '?type=' + encodeURIComponent(type), {
                    headers: { 'Accept': 'application/json' }
                });
                const json = await res.json();
                cache[type] = json.data || [];
            }
            cache[type].forEach(c => {
                const opt = document.createElement('option');
                opt.value = c.id;
                opt.textContent = c.title + ' — ' + Number(c.price).toFixed(2) + ' ج.م';
                opt.dataset.price = c.price;
                if (String(selected) === String(c.id)) opt.selected = true;
                courseSel.appendChild(opt);
            });
            syncPrice(false);
        } catch (e) {
            courseSel.innerHTML = '<option value="">تعذّر التحميل</option>';
        }
    }

    function syncPrice(fromUserSelect) {
        const opt = courseSel.selectedOptions[0];
        const price = opt && opt.dataset.price !== undefined ? Number(opt.dataset.price) : null;
        if (priceHint) {
            priceHint.textContent = price !== null && !Number.isNaN(price)
                ? ('سعر الكورس في النظام: ' + price.toFixed(2) + ' ج.م')
                : '';
        }
        if (valueInput && price !== null && !Number.isNaN(price) && (fromUserSelect || !userEditedValue)) {
            if (fromUserSelect || valueInput.value === '' || valueInput.value === '0') {
                valueInput.value = price.toFixed(2);
                userEditedValue = false;
            }
        }
    }

    typeSel.addEventListener('change', () => {
        courseSel.dataset.selected = '';
        loadCourses(false);
    });
    courseSel.addEventListener('change', () => syncPrice(true));
    loadCourses(true);
})();
</script>
@endpush
@endonce
