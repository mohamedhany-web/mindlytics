@php
    $b = $resolvedBranch ?? auth()->user()?->branch;
@endphp
<div class="flex flex-col h-full bg-gradient-to-b from-emerald-950 via-slate-900 to-slate-900 shadow-2xl border-l border-emerald-800/40" style="margin: 0 !important; padding: 0 !important;">
    <div class="p-6 border-b border-emerald-800/40 bg-slate-900/90 flex-shrink-0">
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-xl bg-emerald-600/30 flex items-center justify-center text-emerald-300">
                <i class="fas fa-building text-xl"></i>
            </div>
            <div class="min-w-0">
                <h2 class="text-lg font-black text-white truncate">{{ $b->name ?? 'الفرع' }}</h2>
                <p class="text-xs text-emerald-200/80 font-semibold">لوحة مدير الفرع</p>
            </div>
        </div>
    </div>
    <nav class="flex-1 p-4 overflow-y-auto space-y-1">
        <a href="{{ route('branch.office.dashboard') }}"
           class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-200 hover:bg-white/10 transition {{ request()->routeIs('branch.office.dashboard') ? 'bg-emerald-600/40 text-white font-semibold' : '' }}">
            <i class="fas fa-chart-pie w-5 text-emerald-400"></i>
            <span>لوحة الفرع</span>
        </a>
        <a href="{{ route('branch.office.users') }}"
           class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-200 hover:bg-white/10 transition {{ request()->routeIs('branch.office.users') ? 'bg-emerald-600/40 text-white font-semibold' : '' }}">
            <i class="fas fa-users w-5 text-emerald-400"></i>
            <span>مستخدمو الفرع</span>
        </a>
        <div class="pt-6 mt-6 border-t border-white/10">
            <a href="{{ url('/') }}" target="_blank" rel="noopener"
               class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-300 hover:bg-white/5 text-sm">
                <i class="fas fa-external-link-alt w-5 text-slate-400"></i>
                <span>الموقع العام</span>
            </a>
            <form method="POST" action="{{ route('logout') }}" class="mt-1">
                @csrf
                <button type="submit" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-rose-200 hover:bg-rose-900/30 text-sm text-start">
                    <i class="fas fa-sign-out-alt w-5"></i>
                    <span>تسجيل الخروج</span>
                </button>
            </form>
        </div>
    </nav>
</div>
