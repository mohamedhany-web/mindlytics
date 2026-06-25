@php
    $badge = match($status) {
        'sent' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
        'failed' => 'bg-rose-100 text-rose-800 border-rose-200',
        'pending' => 'bg-amber-100 text-amber-800 border-amber-200',
        default => 'bg-slate-100 text-slate-700 border-slate-200',
    };
@endphp
<span class="inline-flex px-2.5 py-1 rounded-full text-xs font-bold border {{ $badge }}">{{ $label }}</span>
