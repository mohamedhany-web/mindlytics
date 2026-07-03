@php $cards = $cards ?? []; @endphp
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
    @foreach($cards as $card)
        <div class="rounded-2xl p-5 sm:p-6 relative overflow-hidden border border-slate-200 bg-white shadow-md hover:shadow-lg transition-all duration-200 w-full">
            <div class="flex items-center justify-between mb-3">
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-slate-700 mb-1">{{ $card['label'] }}</p>
                    <p class="text-3xl font-black text-slate-900 tabular-nums">{{ $card['value'] }}</p>
                </div>
                <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-amber-500 to-orange-600 flex items-center justify-center text-white shadow-md flex-shrink-0">
                    <i class="{{ $card['icon'] ?? 'fas fa-chart-line' }}"></i>
                </div>
            </div>
            @if(!empty($card['description']))
                <p class="text-xs text-slate-600 font-medium">{{ $card['description'] }}</p>
            @endif
        </div>
    @endforeach
</div>
