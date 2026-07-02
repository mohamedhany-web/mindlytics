@php
    $icon = $icon ?? 'fas fa-award';
    $subtitle = $subtitle ?? null;
@endphp
<div class="bg-gradient-to-r from-slate-50 to-white rounded-2xl p-6 border border-slate-200 shadow-lg">
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white shadow-md flex-shrink-0">
                <i class="{{ $icon }} text-xl"></i>
            </div>
            <div>
                <h1 class="text-2xl sm:text-3xl font-black text-slate-900 mb-1">{{ $title }}</h1>
                @if($subtitle)
                    <p class="text-sm sm:text-base text-slate-600 font-medium">{{ $subtitle }}</p>
                @endif
            </div>
        </div>
        @if(!empty($actions))
            <div class="flex flex-wrap items-center gap-2 shrink-0">
                {!! $actions !!}
            </div>
        @endif
    </div>
</div>
