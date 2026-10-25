@php
    $v = $variant ?? 'default';
    $isModal = $v === 'modal';
    $fname = $name ?? 'offline_attendee_mindmap';
    $fval = $value ?? null;
@endphp
<div class="rounded-xl border border-slate-200 bg-slate-50/60 p-4 sm:p-5 shadow-sm {{ $isModal ? '' : 'border-dashed border-slate-300 bg-white' }}">
    <div class="flex flex-wrap items-start gap-3 mb-3">
        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-600">
            <i class="fas fa-diagram-project text-base"></i>
        </div>
        <div class="min-w-0 flex-1">
            <p class="font-bold text-slate-800 {{ $isModal ? 'text-sm sm:text-base' : 'text-sm' }}">خريطة ذهنية للحضور الأوفلاين</p>
            <p class="text-slate-600 {{ $isModal ? 'text-xs sm:text-sm mt-1' : 'text-xs mt-0.5' }} leading-relaxed">
                لطلاب الحضور الشخصي: سطر رئيسي ثم أسطر بمسافتين أو تاب للفرع، أو بادئة <code class="text-[11px] bg-white border border-slate-200 px-1 rounded">- </code> لكل فكرة.
            </p>
        </div>
    </div>
    <textarea name="{{ $fname }}"
              rows="{{ $isModal ? 6 : 5 }}"
              class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 font-mono text-sm leading-relaxed text-slate-800 shadow-sm focus:border-slate-400 focus:ring-2 focus:ring-slate-200 placeholder:text-slate-400"
              placeholder="الجلسة — الحضور الأوفلاين&#10;  - الترحيب والتجهيز&#10;  - المحتوى الأساسي&#10;    - جزء نظري&#10;    - جزء عملي&#10;  - الختام والواجب">{{ old($fname, $fval) }}</textarea>
    @error($fname)<p class="text-red-600 text-sm mt-2 font-medium">{{ $message }}</p>@enderror
</div>
