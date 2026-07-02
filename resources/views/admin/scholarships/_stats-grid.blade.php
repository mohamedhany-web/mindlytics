@php $cards = $cards ?? []; @endphp
@if(count($cards) > 0)
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-{{ min(count($cards), 4) }} gap-4 sm:gap-6">
    @foreach($cards as $stat)
        <div class="rounded-2xl p-5 sm:p-6 relative overflow-hidden border border-slate-200 bg-white shadow-md hover:shadow-lg transition-all duration-200 w-full">
            <div class="flex items-center justify-between mb-3">
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-slate-700 mb-2">{{ $stat['label'] }}</p>
                    <p class="text-3xl sm:text-4xl font-black text-slate-900">{{ $stat['value'] }}</p>
                </div>
                <div class="w-14 h-14 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center shadow-md flex-shrink-0 mr-3 sm:mr-0">
                    <i class="{{ $stat['icon'] ?? 'fas fa-chart-bar' }} text-white text-xl"></i>
                </div>
            </div>
            @if(!empty($stat['description']))
                <p class="text-xs font-medium text-slate-600">{{ $stat['description'] }}</p>
            @endif
        </div>
    @endforeach
</div>
@endif
