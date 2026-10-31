@if(session('success'))
    <div class="rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 px-5 py-4 flex items-center gap-3 shadow-sm">
        <i class="fas fa-check-circle text-emerald-600"></i>
        <span class="font-semibold">{{ session('success') }}</span>
    </div>
@endif
@if(session('error'))
    <div class="rounded-xl bg-red-50 border border-red-200 text-red-800 px-5 py-4 flex items-center gap-3 shadow-sm">
        <i class="fas fa-exclamation-circle text-red-600"></i>
        <span class="font-semibold">{{ session('error') }}</span>
    </div>
@endif
