@php
    $active = $active ?? '';
@endphp
<nav class="flex flex-wrap gap-2 p-1.5 rounded-2xl bg-white border-2 border-slate-200/50 shadow-sm">
    <a href="{{ route('admin.whatsapp.index') }}"
       class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold transition-all {{ $active === 'dashboard' ? 'bg-emerald-600 text-white shadow-md' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
        <i class="fab fa-whatsapp"></i>
        لوحة الواتساب
    </a>
    <a href="{{ route('admin.whatsapp.send') }}"
       class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold transition-all {{ $active === 'send' ? 'bg-emerald-600 text-white shadow-md' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
        <i class="fas fa-paper-plane"></i>
        إرسال رسالة
    </a>
    <a href="{{ route('admin.whatsapp.messages') }}"
       class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold transition-all {{ $active === 'messages' ? 'bg-emerald-600 text-white shadow-md' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
        <i class="fas fa-list"></i>
        سجل الرسائل
    </a>
    <a href="{{ route('admin.whatsapp.settings') }}"
       class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold transition-all {{ $active === 'settings' ? 'bg-emerald-600 text-white shadow-md' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
        <i class="fas fa-plug"></i>
        إعدادات الربط
    </a>
</nav>
