<section class="{{ $schSectionClass ?? 'rounded-2xl bg-white border-2 border-slate-200/50 shadow-xl overflow-hidden' }}">
    <div class="px-5 py-5 sm:px-6 border-b border-slate-200 bg-gradient-to-r from-violet-50/80 to-white flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-xl bg-gradient-to-br from-violet-500 to-purple-600 flex items-center justify-center text-white shadow-lg flex-shrink-0">
                <i class="{{ $icon ?? 'fas fa-graduation-cap' }} text-lg sm:text-xl"></i>
            </div>
            <div>
                <h2 class="text-xl sm:text-2xl font-black text-slate-900">{{ $title }}</h2>
                @if(!empty($subtitle))
                    <p class="text-sm text-slate-600 mt-0.5">{{ $subtitle }}</p>
                @endif
            </div>
        </div>
        @if(!empty($actions))
            <div class="flex flex-wrap items-center gap-2">
                {!! $actions !!}
            </div>
        @endif
    </div>
    @if(!empty($statCards) && is_array($statCards))
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-{{ min(count($statCards), 4) }} gap-3 p-4 sm:p-5">
            @foreach($statCards as $card)
                <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                    <div class="flex items-center justify-between gap-2">
                        <div class="min-w-0">
                            <p class="text-xs font-semibold text-slate-600 truncate">{{ $card['label'] }}</p>
                            <p class="text-xl font-black text-slate-900 truncate tabular-nums">{{ $card['value'] }}</p>
                        </div>
                        <div class="w-10 h-10 rounded-lg {{ $card['bg'] ?? 'bg-violet-100' }} flex items-center justify-center {{ $card['text'] ?? 'text-violet-600' }} flex-shrink-0">
                            <i class="{{ $card['icon'] ?? 'fas fa-chart-bar' }} text-sm"></i>
                        </div>
                    </div>
                    @if(!empty($card['description']))
                        <p class="text-[11px] text-slate-500 mt-1 truncate">{{ $card['description'] }}</p>
                    @endif
                </div>
            @endforeach
        </div>
    @endif
</section>
