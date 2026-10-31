@php $active = $active ?? ''; @endphp
<nav class="flex flex-wrap gap-2 p-1.5 rounded-2xl bg-white border border-slate-200 shadow-sm">
    <a href="{{ route('admin.investment.dashboard') }}"
       class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold transition-all {{ $active === 'dashboard' ? 'bg-amber-600 text-white shadow-md' : 'text-slate-600 hover:bg-amber-50 hover:text-amber-900' }}">
        <i class="fas fa-tachometer-alt"></i><span>لوحة الاستثمار</span>
    </a>
    <a href="{{ route('admin.investment.plans.index') }}"
       class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold transition-all {{ $active === 'plans' ? 'bg-amber-600 text-white shadow-md' : 'text-slate-600 hover:bg-amber-50 hover:text-amber-900' }}">
        <i class="fas fa-chart-pie"></i><span>الخطط الاستثمارية</span>
    </a>
    <a href="{{ route('admin.investment.inquiries.index') }}"
       class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold transition-all {{ $active === 'inquiries' ? 'bg-amber-600 text-white shadow-md' : 'text-slate-600 hover:bg-amber-50 hover:text-amber-900' }}">
        <i class="fas fa-handshake"></i><span>طلبات المستثمرين</span>
    </a>
    <a href="{{ route('admin.investment.policies.edit') }}"
       class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold transition-all {{ $active === 'policies' ? 'bg-amber-600 text-white shadow-md' : 'text-slate-600 hover:bg-amber-50 hover:text-amber-900' }}">
        <i class="fas fa-gavel"></i><span>الإطار القانوني</span>
    </a>
    <a href="{{ route('investment.index') }}" target="_blank" rel="noopener"
       class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold text-slate-600 hover:bg-slate-50 border border-dashed border-slate-300">
        <i class="fas fa-external-link-alt"></i><span>الصفحة العامة</span>
    </a>
</nav>
