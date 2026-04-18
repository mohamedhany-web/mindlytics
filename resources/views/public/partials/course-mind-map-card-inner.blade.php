@if($isFirst)
    <span class="inline-block text-xs font-bold text-emerald-800 bg-emerald-50 border border-emerald-200/80 rounded-lg px-2.5 py-1 mb-2">{{ __('public.roadmap_start') }}</span>
@elseif($isLast)
    <span class="inline-block text-xs font-bold text-violet-800 bg-violet-50 border border-violet-200/80 rounded-lg px-2.5 py-1 mb-2">{{ __('public.roadmap_end') }}</span>
@else
    <span class="inline-block text-xs font-bold text-sky-800 bg-sky-50 border border-sky-200/80 rounded-lg px-2.5 py-1 mb-2">{{ __('public.roadmap_step', ['n' => $index]) }}</span>
@endif
<h2 class="text-lg md:text-xl font-black text-slate-900 leading-snug">{{ $step['title'] ?? '' }}</h2>
@if(!empty($step['description']))
    <p class="mt-2.5 text-slate-600 text-sm md:text-base leading-relaxed whitespace-pre-line">{{ $step['description'] }}</p>
@endif
