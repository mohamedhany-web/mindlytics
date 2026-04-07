@php
    $nodes = $nodes ?? [];
    $depth = $depth ?? 0;
@endphp
<ul class="space-y-1 {{ $depth > 0 ? 'mr-3 sm:mr-5 mt-2 border-r-2 border-slate-200/90 pr-3 sm:pr-4' : '' }}">
    @foreach($nodes as $node)
        @php
            $hasChildren = !empty($node['children']);
            $type = $node['type'] ?? '';
            $typeIcon = match ($type) {
                'asset' => 'fa-coins text-amber-600',
                'liability' => 'fa-arrow-down text-rose-600',
                'equity' => 'fa-balance-scale text-violet-600',
                'revenue' => 'fa-chart-line text-emerald-600',
                'expense' => 'fa-fire text-orange-600',
                default => 'fa-circle text-slate-400',
            };
            $nodeIcon = $node['icon'] ?? null;
        @endphp
        <li class="rounded-xl border border-transparent hover:border-slate-200 hover:bg-white/80 transition-colors" x-data="{ open: {{ $depth < 1 ? 'true' : 'false' }} }">
            <div class="flex flex-wrap items-start gap-2 py-2 px-2 sm:px-3 rounded-xl bg-white/40">
                @if($hasChildren)
                    <button type="button" @click="open = !open" class="mt-0.5 flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-slate-200/80 text-slate-700 hover:bg-slate-300 transition-colors" aria-expanded="true" :aria-expanded="open">
                        <i class="fas text-[10px]" :class="open ? 'fa-chevron-down' : 'fa-chevron-left'"></i>
                    </button>
                @else
                    <span class="mt-0.5 flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-slate-100 text-slate-400">
                        <i class="fas fa-minus text-[8px]"></i>
                    </span>
                @endif
                <div class="min-w-0 flex-1">
                    <div class="flex flex-wrap items-center gap-2 text-sm">
                        <span class="inline-flex items-center rounded-lg bg-indigo-50 px-2 py-0.5 font-mono text-[11px] font-bold text-indigo-700 border border-indigo-100">{{ $node['code'] ?? '' }}</span>
                        <i class="fas {{ $nodeIcon ?? $typeIcon }} text-xs opacity-90"></i>
                        <span class="font-bold text-slate-900">{{ $node['name'] ?? '' }}</span>
                        @if($type)
                            <span class="rounded-md bg-slate-100 px-1.5 py-0.5 text-[10px] font-semibold text-slate-600">
                                @if($type === 'asset') أصول
                                @elseif($type === 'liability') خصوم
                                @elseif($type === 'equity') حقوق ملكية
                                @elseif($type === 'revenue') إيرادات
                                @elseif($type === 'expense') مصروفات
                                @else {{ $type }}
                                @endif
                            </span>
                        @endif
                        @if(!empty($node['route']) && Route::has($node['route']))
                            <a href="{{ route($node['route']) }}" class="inline-flex items-center gap-1 rounded-full bg-sky-50 px-2.5 py-0.5 text-[11px] font-bold text-sky-700 hover:bg-sky-100 border border-sky-100">
                                <i class="fas fa-external-link-alt text-[9px]"></i>
                                فتح في النظام
                            </a>
                        @endif
                    </div>
                    @if(!empty($node['description']))
                        <p class="mt-1 text-xs text-slate-500 leading-relaxed pr-1">{{ $node['description'] }}</p>
                    @endif
                </div>
            </div>
            @if($hasChildren)
                <div x-show="open" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" class="pb-1">
                    @include('admin.accounting.partials.chart-node', ['nodes' => $node['children'], 'depth' => $depth + 1])
                </div>
            @endif
        </li>
    @endforeach
</ul>
