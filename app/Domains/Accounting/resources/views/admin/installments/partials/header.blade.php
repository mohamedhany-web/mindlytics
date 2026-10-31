@php
    $title = $title ?? '';
    $description = $description ?? '';
    $icon = $icon ?? 'fa-percentage';
    $iconGradient = $iconGradient ?? 'from-violet-500 to-purple-600';
    $meta = $meta ?? null;
    $actions = $actions ?? [];
@endphp
<section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
    <div class="px-4 py-4 bg-slate-50 border-b border-slate-200 flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
        <div class="flex items-center gap-3">
            <div class="w-11 h-11 rounded-xl bg-gradient-to-br {{ $iconGradient }} flex items-center justify-center text-white shadow-md">
                <i class="fas {{ $icon }}"></i>
            </div>
            <div>
                <h2 class="text-xl font-black text-slate-900">{{ $title }}</h2>
                @if($description)
                    <p class="text-xs text-slate-600">{{ $description }}</p>
                @endif
                @if($meta)
                    <p class="text-[11px] text-slate-500 mt-1">{{ $meta }}</p>
                @endif
            </div>
        </div>
        @if($actions !== [])
            <div class="flex flex-wrap items-center gap-2">
                @foreach($actions as $action)
                    @php
                        $style = $action['style'] ?? 'secondary';
                        $classes = match ($style) {
                            'primary' => 'text-white bg-violet-600 hover:bg-violet-700 border-transparent',
                            'success' => 'text-white bg-emerald-600 hover:bg-emerald-700 border-transparent',
                            'warning' => 'text-slate-900 bg-amber-400 hover:bg-amber-300 border-transparent',
                            default => 'text-slate-700 border-slate-300 hover:bg-white',
                        };
                    @endphp
                    @if(!empty($action['route']) && Route::has($action['route']))
                        <a href="{{ route($action['route'], $action['params'] ?? []) }}"
                           class="inline-flex items-center gap-2 px-3 py-2 text-sm font-semibold rounded-xl border {{ $classes }}">
                            @if(!empty($action['icon']))
                                <i class="fas {{ $action['icon'] }} {{ $style === 'secondary' ? 'text-slate-500' : '' }}"></i>
                            @endif
                            {{ $action['label'] }}
                        </a>
                    @endif
                @endforeach
            </div>
        @endif
    </div>
</section>
