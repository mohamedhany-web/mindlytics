@php
    $plan = $plan ?? null;
    $processSteps = old('process_steps', $plan?->process_steps ?? [['title' => '', 'description' => '']]);
    if (! is_array($processSteps) || $processSteps === []) {
        $processSteps = [['title' => '', 'description' => '']];
    }
@endphp

<div class="space-y-5">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="md:col-span-2">
            <label class="{{ $invLabelClass }}">عنوان الخطة *</label>
            <input type="text" name="title" value="{{ old('title', $plan?->title) }}" required class="{{ $invInputClass }}">
        </div>
        <div class="md:col-span-2">
            <label class="{{ $invLabelClass }}">وصف مختصر</label>
            <input type="text" name="short_description" value="{{ old('short_description', $plan?->short_description) }}" maxlength="500" class="{{ $invInputClass }}">
        </div>
        <div class="md:col-span-2">
            <label class="{{ $invLabelClass }}">تفاصيل الخطة</label>
            <textarea name="description" rows="5" class="{{ $invTextareaClass }}">{{ old('description', $plan?->description) }}</textarea>
        </div>
        <div>
            <label class="{{ $invLabelClass }}">نوع الاستثمار *</label>
            <select name="plan_type" required class="{{ $invSelectClass }}">
                @foreach($planTypes as $value => $label)
                    <option value="{{ $value }}" @selected(old('plan_type', $plan?->plan_type) === $value)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="{{ $invLabelClass }}">نموذج العائد *</label>
            <select name="return_model" required class="{{ $invSelectClass }}">
                @foreach($returnModels as $value => $label)
                    <option value="{{ $value }}" @selected(old('return_model', $plan?->return_model) === $value)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="{{ $invLabelClass }}">الحد الأدنى *</label>
            <input type="number" step="0.01" min="0" name="min_investment" value="{{ old('min_investment', $plan?->min_investment ?? 0) }}" required class="{{ $invInputClass }}">
        </div>
        <div>
            <label class="{{ $invLabelClass }}">الحد الأقصى</label>
            <input type="number" step="0.01" min="0" name="max_investment" value="{{ old('max_investment', $plan?->max_investment) }}" class="{{ $invInputClass }}">
        </div>
        <div>
            <label class="{{ $invLabelClass }}">الهدف التمويلي</label>
            <input type="number" step="0.01" min="0" name="target_amount" value="{{ old('target_amount', $plan?->target_amount) }}" class="{{ $invInputClass }}">
        </div>
        <div>
            <label class="{{ $invLabelClass }}">العملة</label>
            <input type="text" name="currency" value="{{ old('currency', $plan?->currency ?? 'EGP') }}" maxlength="3" class="{{ $invInputClass }} dir-ltr">
        </div>
        <div>
            <label class="{{ $invLabelClass }}">المدة (بالأشهر)</label>
            <input type="number" min="1" name="duration_months" value="{{ old('duration_months', $plan?->duration_months) }}" class="{{ $invInputClass }}">
        </div>
        <div>
            <label class="{{ $invLabelClass }}">مستوى المخاطر *</label>
            <select name="risk_level" required class="{{ $invSelectClass }}">
                @foreach($riskLevels as $value => $label)
                    <option value="{{ $value }}" @selected(old('risk_level', $plan?->risk_level ?? 'medium') === $value)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="{{ $invLabelClass }}">العائد المتوقع (% — من)</label>
            <input type="number" step="0.01" min="0" name="expected_return_min" value="{{ old('expected_return_min', $plan?->expected_return_min) }}" class="{{ $invInputClass }}">
        </div>
        <div>
            <label class="{{ $invLabelClass }}">العائد المتوقع (% — إلى)</label>
            <input type="number" step="0.01" min="0" name="expected_return_max" value="{{ old('expected_return_max', $plan?->expected_return_max) }}" class="{{ $invInputClass }}">
        </div>
        <div>
            <label class="{{ $invLabelClass }}">بداية العرض</label>
            <input type="datetime-local" name="starts_at" value="{{ old('starts_at', $plan?->starts_at?->format('Y-m-d\TH:i')) }}" class="{{ $invInputClass }}">
        </div>
        <div>
            <label class="{{ $invLabelClass }}">نهاية العرض</label>
            <input type="datetime-local" name="ends_at" value="{{ old('ends_at', $plan?->ends_at?->format('Y-m-d\TH:i')) }}" class="{{ $invInputClass }}">
        </div>
        <div>
            <label class="{{ $invLabelClass }}">ترتيب العرض</label>
            <input type="number" min="0" name="sort_order" value="{{ old('sort_order', $plan?->sort_order ?? 0) }}" class="{{ $invInputClass }}">
        </div>
    </div>

    <div>
        <label class="{{ $invLabelClass }}">شروط الأهلية</label>
        <textarea name="eligibility_criteria" rows="3" class="{{ $invTextareaClass }}">{{ old('eligibility_criteria', $plan?->eligibility_criteria) }}</textarea>
    </div>
    <div>
        <label class="{{ $invLabelClass }}">المزايا للمستثمر</label>
        <textarea name="benefits" rows="3" class="{{ $invTextareaClass }}">{{ old('benefits', $plan?->benefits) }}</textarea>
    </div>
    <div>
        <label class="{{ $invLabelClass }}">ملخص الشروط</label>
        <textarea name="terms_summary" rows="3" class="{{ $invTextareaClass }}">{{ old('terms_summary', $plan?->terms_summary) }}</textarea>
    </div>
    <div>
        <label class="{{ $invLabelClass }}">ملاحظات قانونية خاصة بالخطة</label>
        <textarea name="legal_notes" rows="3" class="{{ $invTextareaClass }}">{{ old('legal_notes', $plan?->legal_notes) }}</textarea>
    </div>

    <div>
        <label class="{{ $invLabelClass }}">خطوات التنفيذ (اختياري)</label>
        <div class="space-y-3">
            @foreach($processSteps as $i => $step)
                <div class="grid grid-cols-1 md:grid-cols-2 gap-2 p-3 rounded-xl bg-amber-50/50 border border-amber-100">
                    <input type="text" name="process_steps[{{ $i }}][title]" value="{{ $step['title'] ?? '' }}" placeholder="عنوان الخطوة" class="{{ $invInputClass }}">
                    <input type="text" name="process_steps[{{ $i }}][description]" value="{{ $step['description'] ?? '' }}" placeholder="الوصف" class="{{ $invInputClass }}">
                </div>
            @endforeach
        </div>
    </div>

    <div class="flex flex-wrap gap-4">
        <label class="inline-flex items-center gap-2">
            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $plan?->is_active ?? true) ? 'checked' : '' }} class="rounded border-slate-300 text-amber-600">
            <span class="text-sm text-slate-700 font-medium">الخطة نشطة</span>
        </label>
        <label class="inline-flex items-center gap-2">
            <input type="checkbox" name="is_featured" value="1" {{ old('is_featured', $plan?->is_featured ?? false) ? 'checked' : '' }} class="rounded border-slate-300 text-amber-600">
            <span class="text-sm text-slate-700 font-medium">خطة مميزة</span>
        </label>
    </div>
</div>
