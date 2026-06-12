@php
    $lead = $lead ?? null;
    $inputClass = 'w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500';
@endphp

@if(isset($salesReps))
<section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
    <div class="px-4 py-3 border-b border-slate-200 bg-slate-50">
        <h3 class="text-base font-black text-slate-900 flex items-center gap-2">
            <i class="fas fa-user-tie text-violet-600"></i>
            الإسناد
        </h3>
        <p class="text-xs text-slate-600 mt-0.5">اختر موظف المبيعات المسؤول عن هذا Lead.</p>
    </div>
    <div class="p-4 sm:p-6">
        <div class="max-w-xl">
            <label class="block text-xs font-semibold text-slate-700 mb-1">مسند إلى (موظف مبيعات) <span class="text-rose-600">*</span></label>
            <select name="assigned_to" required class="{{ $inputClass }}">
                @foreach($salesReps as $rep)
                    <option value="{{ $rep->id }}" @selected(old('assigned_to', $lead->assigned_to ?? '') == $rep->id)>{{ $rep->name }}</option>
                @endforeach
            </select>
            @error('assigned_to')<p class="text-rose-600 text-xs mt-1">{{ $message }}</p>@enderror
        </div>
    </div>
</section>
@endif

<section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
    <div class="px-4 py-3 border-b border-slate-200 bg-slate-50">
        <h3 class="text-base font-black text-slate-900 flex items-center gap-2">
            <i class="fas fa-address-card text-sky-600"></i>
            بيانات العميل والصفقة
        </h3>
        <p class="text-xs text-slate-600 mt-0.5">معلومات التواصل، المرحلة، الأولوية، والمتابعة.</p>
    </div>
    <div class="p-4 sm:p-6">
        @include('admin.sales.leads._lead_fields_inner', ['lead' => $lead])
    </div>
</section>

@if(isset($lead))
<section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
    <div class="px-4 py-3 border-b border-amber-200 bg-amber-50/70">
        <h3 class="text-base font-black text-slate-900 flex items-center gap-2">
            <i class="fas fa-star text-amber-500"></i>
            رضا العميل (CSAT)
        </h3>
        <p class="text-xs text-slate-600 mt-0.5">بعد إغلاق الصفقة — يُستخدم في مؤشرات الجودة الشهرية.</p>
    </div>
    <div class="p-4 sm:p-6">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 max-w-2xl">
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">التقييم (1–5)</label>
                <select name="csat_rating" class="{{ $inputClass }}">
                    <option value="">— لا يوجد —</option>
                    @for($i = 5; $i >= 1; $i--)
                        <option value="{{ $i }}" @selected((string) old('csat_rating', $lead->csat_rating ?? '') === (string) $i)>{{ $i }}</option>
                    @endfor
                </select>
                @error('csat_rating')<p class="text-rose-600 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div class="sm:col-span-2">
                <label class="block text-xs font-semibold text-slate-700 mb-1">ملاحظة (اختياري)</label>
                <textarea name="csat_comment" rows="2" class="{{ $inputClass }}">{{ old('csat_comment', $lead->csat_comment) }}</textarea>
            </div>
        </div>
        @if($lead->csat_recorded_at)
            <p class="text-xs text-slate-500 mt-3">آخر تسجيل: {{ $lead->csat_recorded_at->format('Y-m-d H:i') }}</p>
        @endif
    </div>
</section>
@endif
