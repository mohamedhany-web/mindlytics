@php
    $gs = $groupSessions ?? collect();
    $selName = $name ?? 'offline_group_session_id';
    $selRequired = $required ?? false;
    $selValue = $value ?? null;
    $variant = $variant ?? 'default';
    $isModal = $variant === 'modal';
    $statusShort = [
        'scheduled' => 'مجدولة',
        'completed' => 'مكتملة',
        'cancelled' => 'ملغاة',
    ];
@endphp
@if($gs->isNotEmpty())
    <div class="{{ $isModal ? 'rounded-xl border border-slate-200 bg-slate-50/60 p-4 sm:p-5 shadow-sm' : '' }}">
        <label class="block font-semibold text-slate-800 mb-1.5 {{ $isModal ? 'text-xs sm:text-sm' : 'text-sm' }}">
            الجلسة التي ستُوصَّف هذه المحاضرة لها
            @if($selRequired)<span class="text-red-500">*</span>@endif
        </label>
        <p class="text-slate-600 {{ $isModal ? 'text-xs sm:text-sm mb-3 leading-relaxed' : 'text-xs mb-2' }}">
            @if($isModal)
                اختر جلسة من التقويم ({{ $gs->count() }} متاحة). المحتوى يصف ما ستقدّمه في تلك الجلسة.
            @else
                نفس الجلسات التي تظهر في تقويم المدرب بعد إنشائها من الإدارة.
            @endif
        </p>
        <select name="{{ $selName }}"
                class="w-full rounded-xl border border-slate-200 bg-white shadow-sm focus:border-slate-400 focus:ring-2 focus:ring-slate-200 {{ $isModal ? 'text-sm px-3 py-2.5 min-h-[2.75rem]' : 'text-sm px-4 py-2.5' }}"
                @if($selRequired) required @endif>
            <option value="">— اختر جلسة من القائمة —</option>
            @foreach($gs as $s)
                @php
                    $t = $s->title ?: 'جلسة';
                    $st = is_string($s->start_time) ? substr($s->start_time, 0, 5) : $s->start_time;
                    $et = is_string($s->end_time) ? substr($s->end_time, 0, 5) : $s->end_time;
                    $grp = $s->group->name ?? 'مجموعة';
                    $stLabel = $statusShort[$s->status] ?? $s->status;
                    $dur = (int) ($s->duration_minutes ?? 0);
                    $label = $s->session_date->format('Y/m/d').' · '.$st.'–'.$et.' · '.$dur.'د · '.$grp.' · '.$t.' ('.$stLabel.')';
                @endphp
                <option value="{{ $s->id }}" @selected((string) old($selName, $selValue) === (string) $s->id)>{{ $label }}</option>
            @endforeach
        </select>
        @unless($isModal)
            <p class="text-xs text-slate-500 mt-1">نفس الجلسات التي تظهر في تقويم المدرب بعد إنشائها من الإدارة.</p>
        @endunless
        @error($selName)<p class="text-red-500 {{ $isModal ? 'text-sm' : 'text-sm' }} mt-2">{{ $message }}</p>@enderror
    </div>
@else
    <div class="rounded-xl border border-amber-200 bg-amber-50/80 px-3 py-2 {{ $isModal ? 'text-sm p-4' : 'text-xs' }} text-amber-900">
        لا توجد جلسات مسجّلة للمجموعات بعد. عند إنشاء الجلسات من الإدارة ستظهر هنا وللمدرب في التقويم، ويمكنك حينها ربط كل محاضرة بجلسة.
    </div>
@endif
