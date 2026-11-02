{{-- تابات قسم الإرشاد المهني في الإدارة --}}
<div class="flex flex-wrap items-center justify-between gap-3">
    <div>
        <h1 class="text-2xl font-black text-slate-900">الإرشاد المهني</h1>
        <p class="text-sm text-slate-600 mt-1">جلسات مجانية بنساعد فيها الطالب يعرف احتياجاته ويختار طريقه لسوق العمل. الطلاب بيحجزوا من أي صفحة في الموقع.</p>
    </div>
    <nav class="flex gap-2" aria-label="أقسام الإرشاد">
        <a href="{{ route('admin.advising.bookings') }}" class="px-4 py-2 rounded-lg text-sm font-bold border {{ request()->routeIs('admin.advising.bookings') ? 'bg-slate-900 text-white border-slate-900' : 'bg-white text-slate-700 border-slate-300 hover:bg-slate-50' }}">الحجوزات</a>
        <a href="{{ route('admin.advising.availability') }}" class="px-4 py-2 rounded-lg text-sm font-bold border {{ request()->routeIs('admin.advising.availability') ? 'bg-slate-900 text-white border-slate-900' : 'bg-white text-slate-700 border-slate-300 hover:bg-slate-50' }}">مواعيد التوافر</a>
        <a href="{{ route('advising.show') }}" target="_blank" class="px-4 py-2 rounded-lg text-sm font-bold border bg-white text-slate-700 border-slate-300 hover:bg-slate-50">صفحة الحجز ↗</a>
    </nav>
</div>
