@php
    $active = $active ?? '';
    $items = [
        ['key' => 'dashboard', 'route' => 'admin.accounting.installments', 'label' => 'لوحة التقسيط', 'icon' => 'fa-tachometer-alt'],
        ['key' => 'plans', 'route' => 'admin.installments.plans.index', 'label' => 'خطط التقسيط', 'icon' => 'fa-layer-group'],
        ['key' => 'agreements', 'route' => 'admin.installments.agreements.index', 'label' => 'الاتفاقيات', 'icon' => 'fa-handshake'],
        ['key' => 'manual-booking', 'route' => 'admin.installments.agreements.manual-booking', 'label' => 'حجز + تقسيط', 'icon' => 'fa-user-plus'],
    ];
@endphp
<section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
    <div class="px-4 py-3 bg-slate-50 border-b border-slate-200 flex flex-wrap items-center gap-2">
        <a href="{{ route('admin.accounting.hub') }}" class="inline-flex items-center gap-1.5 px-2.5 py-1.5 text-[11px] font-bold text-slate-600 rounded-lg border border-slate-200 hover:bg-white">
            <i class="fas fa-calculator text-sky-600"></i>
            مركز المحاسبة
        </a>
        <span class="text-slate-300 hidden sm:inline">|</span>
        @foreach($items as $item)
            @continue(! Route::has($item['route']))
            @php $isActive = $active === $item['key']; @endphp
            <a href="{{ route($item['route']) }}"
               class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold rounded-xl border transition-colors
                      {{ $isActive ? 'text-violet-800 border-violet-300 bg-violet-50 shadow-sm' : 'text-slate-700 border-slate-200 hover:bg-slate-50' }}">
                <i class="fas {{ $item['icon'] }} {{ $isActive ? 'text-violet-600' : 'text-slate-500' }}"></i>
                {{ $item['label'] }}
            </a>
        @endforeach
    </div>
</section>
