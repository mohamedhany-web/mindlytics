@php
    $tabs = [
        'referrals' => ['label' => 'برامج الإحالة', 'route' => route('admin.referral-programs.index'), 'icon' => 'fa-gift'],
        'promo' => ['label' => 'أكواد الورش', 'route' => route('admin.workshop-promo-codes.index'), 'icon' => 'fa-ticket-alt'],
        'list' => ['label' => 'سجل الإحالات', 'route' => route('admin.referrals.index'), 'icon' => 'fa-users'],
    ];
@endphp
<nav class="flex flex-wrap gap-2 p-1 rounded-2xl bg-white border border-slate-200 shadow-sm">
    @foreach($tabs as $key => $tab)
        <a href="{{ $tab['route'] }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-bold transition-all {{ ($active ?? '') === $key ? 'bg-gradient-to-r from-sky-600 to-violet-600 text-white shadow-md' : 'text-slate-600 hover:bg-slate-50' }}">
            <i class="fas {{ $tab['icon'] }}"></i>
            {{ $tab['label'] }}
        </a>
    @endforeach
</nav>
