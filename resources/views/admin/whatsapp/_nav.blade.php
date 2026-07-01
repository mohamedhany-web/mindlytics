@php
    $navItems = [
        'dashboard' => ['route' => 'admin.whatsapp.index', 'icon' => 'fas fa-tachometer-alt', 'label' => 'لوحة الواتساب'],
        'settings' => ['route' => 'admin.whatsapp.settings', 'icon' => 'fas fa-plug', 'label' => 'ربط Meta'],
        'send' => ['route' => 'admin.whatsapp.send', 'icon' => 'fas fa-paper-plane', 'label' => 'إرسال رسالة'],
        'messages' => ['route' => 'admin.whatsapp.messages', 'icon' => 'fas fa-list', 'label' => 'سجل الرسائل'],
        'inbox' => ['route' => 'admin.whatsapp.inbox', 'icon' => 'fas fa-inbox', 'label' => 'المحادثات'],
        'batches' => ['route' => 'admin.whatsapp.batches.index', 'icon' => 'fas fa-layer-group', 'label' => 'دفعات الإرسال'],
    ];
@endphp
<nav class="flex flex-wrap gap-2 p-1.5 rounded-2xl bg-white border-2 border-slate-200/50 shadow-sm">
    @foreach($navItems as $key => $item)
        <a href="{{ route($item['route']) }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold transition-all {{ ($active ?? '') === $key ? 'bg-gradient-to-r from-emerald-600 to-green-500 text-white shadow-md shadow-emerald-500/25' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
            <i class="{{ $item['icon'] }}"></i>
            {{ $item['label'] }}
        </a>
    @endforeach
</nav>
