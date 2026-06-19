@php
    $heroTitle = $heroTitle ?? 'مركز المبيعات';
    $heroSubtitle = $heroSubtitle ?? '';
    $heroIcon = $heroIcon ?? 'fa-chart-line';
    $heroIconFrom = $heroIconFrom ?? 'emerald-500';
    $heroIconTo = $heroIconTo ?? 'teal-600';
    $backUrl = $backUrl ?? null;
    $backLabel = $backLabel ?? 'مركز المبيعات';
@endphp
<div class="welcome-section dashboard-card relative overflow-hidden">
    <div class="relative z-10 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div class="flex items-start gap-4">
            <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center text-white shadow-lg shrink-0">
                <i class="fas {{ $heroIcon }} text-2xl"></i>
            </div>
            <div>
                @if($backUrl)
                    <a href="{{ $backUrl }}" class="text-sm text-emerald-700 font-semibold hover:underline mb-1 inline-flex items-center gap-1">
                        <i class="fas fa-arrow-right text-xs"></i> {{ $backLabel }}
                    </a>
                @endif
                <h2 class="text-2xl sm:text-3xl font-black text-gray-900">{{ $heroTitle }}</h2>
                @if($heroSubtitle)
                    <p class="text-gray-600 text-sm sm:text-base mt-1 font-medium">{{ $heroSubtitle }}</p>
                @endif
            </div>
        </div>
        @isset($heroActions)
            <div class="flex flex-wrap gap-2 shrink-0">{!! $heroActions !!}</div>
        @endisset
    </div>
</div>
