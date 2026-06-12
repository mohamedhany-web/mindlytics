@php
    $stats = $stats ?? [];
    $columns = $columns ?? 'grid-cols-1 sm:grid-cols-2 xl:grid-cols-4';
    $cardThemes = [
        'emerald' => ['border' => 'border-emerald-200/70', 'bg' => 'from-white via-white to-emerald-50/60', 'label' => 'text-emerald-800/80', 'value' => 'from-emerald-700 to-teal-600', 'icon' => 'from-emerald-500 to-teal-600', 'desc' => 'text-emerald-700/70'],
        'amber'   => ['border' => 'border-amber-200/70', 'bg' => 'from-white via-white to-amber-50/60', 'label' => 'text-amber-800/80', 'value' => 'from-amber-700 to-orange-600', 'icon' => 'from-amber-500 to-orange-500', 'desc' => 'text-amber-700/70'],
        'sky'     => ['border' => 'border-sky-200/70', 'bg' => 'from-white via-white to-sky-50/60', 'label' => 'text-sky-800/80', 'value' => 'from-sky-700 to-blue-600', 'icon' => 'from-sky-500 to-blue-600', 'desc' => 'text-sky-700/70'],
        'rose'    => ['border' => 'border-rose-200/70', 'bg' => 'from-white via-white to-rose-50/60', 'label' => 'text-rose-800/80', 'value' => 'from-rose-700 to-red-600', 'icon' => 'from-rose-500 to-red-500', 'desc' => 'text-rose-700/70'],
        'violet'  => ['border' => 'border-violet-200/70', 'bg' => 'from-white via-white to-violet-50/60', 'label' => 'text-violet-800/80', 'value' => 'from-violet-700 to-purple-600', 'icon' => 'from-violet-500 to-purple-600', 'desc' => 'text-violet-700/70'],
        'indigo'  => ['border' => 'border-indigo-200/70', 'bg' => 'from-white via-white to-indigo-50/60', 'label' => 'text-indigo-800/80', 'value' => 'from-indigo-700 to-violet-600', 'icon' => 'from-indigo-500 to-violet-600', 'desc' => 'text-indigo-700/70'],
        'teal'    => ['border' => 'border-teal-200/70', 'bg' => 'from-white via-white to-teal-50/60', 'label' => 'text-teal-800/80', 'value' => 'from-teal-700 to-emerald-600', 'icon' => 'from-teal-500 to-emerald-600', 'desc' => 'text-teal-700/70'],
        'green'   => ['border' => 'border-green-200/70', 'bg' => 'from-white via-white to-green-50/60', 'label' => 'text-green-800/80', 'value' => 'from-green-700 to-emerald-600', 'icon' => 'from-green-500 to-emerald-600', 'desc' => 'text-green-700/70'],
        'orange'  => ['border' => 'border-orange-200/70', 'bg' => 'from-white via-white to-orange-50/60', 'label' => 'text-orange-800/80', 'value' => 'from-orange-700 to-amber-600', 'icon' => 'from-orange-500 to-amber-500', 'desc' => 'text-orange-700/70'],
        'purple'  => ['border' => 'border-purple-200/70', 'bg' => 'from-white via-white to-purple-50/60', 'label' => 'text-purple-800/80', 'value' => 'from-purple-700 to-violet-600', 'icon' => 'from-purple-500 to-violet-600', 'desc' => 'text-purple-700/70'],
    ];
@endphp
@if($stats !== [])
<div class="grid {{ $columns }} gap-4">
    @foreach($stats as $card)
        @php $theme = $cardThemes[$card['theme'] ?? 'sky'] ?? $cardThemes['sky']; @endphp
        <div class="dashboard-stat-card rounded-2xl border-2 {{ $theme['border'] }} bg-gradient-to-br {{ $theme['bg'] }} p-5 shadow-lg">
            <div class="flex items-center justify-between gap-3 mb-3">
                <div class="min-w-0 flex-1">
                    <p class="text-sm font-bold {{ $theme['label'] }} mb-1">{{ $card['label'] }}</p>
                    <p class="text-2xl font-black bg-gradient-to-r {{ $theme['value'] }} bg-clip-text text-transparent tabular-nums">{{ $card['value'] }}</p>
                </div>
                @if(!empty($card['icon']))
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br {{ $theme['icon'] }} flex items-center justify-center text-white shadow-md flex-shrink-0">
                        <i class="{{ $card['icon'] }} text-sm"></i>
                    </div>
                @endif
            </div>
            @if(!empty($card['desc']))
                <p class="text-xs font-medium {{ $theme['desc'] }} truncate">{{ $card['desc'] }}</p>
            @endif
        </div>
    @endforeach
</div>
@endif
