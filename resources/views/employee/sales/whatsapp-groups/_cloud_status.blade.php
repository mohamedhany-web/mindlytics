@php $connected = (bool) ($cloud['connected'] ?? false); @endphp
<div class="flex flex-wrap items-center justify-between gap-3 sales-panel px-4 py-3">
    <div class="flex flex-wrap items-center gap-3">
        <span class="wa-cloud-pill {{ $connected ? 'wa-cloud-pill--ok' : 'wa-cloud-pill--warn' }}">
            <i class="fab fa-whatsapp"></i>
            {{ $connected ? ($cloud['label'] ?? 'Meta Cloud متصل') : 'Meta Cloud غير جاهز' }}
        </span>
        @if($connected)
            <span class="text-xs text-slate-500">مجموعات واتساب عبر Meta Cloud API</span>
        @else
            <span class="text-xs text-amber-800">{{ $cloud['error'] ?? 'أكمل الربط من إعدادات الواتساب' }}</span>
        @endif
    </div>
    @if(($settingsUrl ?? null) && ! $connected)
        <a href="{{ $settingsUrl }}" class="text-xs font-semibold text-sky-700 hover:underline">إعدادات الربط ←</a>
    @endif
</div>
