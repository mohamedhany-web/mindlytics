@php
    $icon = $icon ?? 'fas fa-list';
    $title = $title ?? '';
    $subtitle = $subtitle ?? null;
    $actions = $actions ?? null;
@endphp
<div class="px-6 py-5 border-b border-slate-200 bg-slate-50 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-amber-500 to-orange-600 flex items-center justify-center text-white shadow-md flex-shrink-0">
            <i class="{{ $icon }} text-lg"></i>
        </div>
        <div>
            <h3 class="text-lg font-black text-slate-900">{{ $title }}</h3>
            @if($subtitle)
                <p class="text-xs text-slate-600 font-medium mt-1">{!! $subtitle !!}</p>
            @endif
        </div>
    </div>
    @if(!empty($actions))
        <div class="flex flex-wrap items-center gap-2 shrink-0">{!! $actions !!}</div>
    @endif
</div>
