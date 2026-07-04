@if(session('success'))
    <div class="rounded-xl bg-emerald-50 border-2 border-emerald-200 text-emerald-800 px-5 py-4 flex items-center gap-3 shadow-sm">
        <i class="fas fa-check-circle text-emerald-600 text-xl"></i>
        <span class="font-semibold text-sm">{{ session('success') }}</span>
    </div>
@endif

@if(session('warning'))
    <div class="rounded-xl bg-amber-50 border-2 border-amber-200 text-amber-900 px-5 py-4 flex items-center gap-3 shadow-sm">
        <i class="fas fa-exclamation-triangle text-amber-600 text-xl"></i>
        <span class="font-semibold text-sm">{{ session('warning') }}</span>
    </div>
@endif

@if(session('error'))
    <div class="rounded-xl bg-rose-50 border-2 border-rose-200 text-rose-800 px-5 py-4 flex items-center gap-3 shadow-sm">
        <i class="fas fa-exclamation-circle text-rose-600 text-xl"></i>
        <span class="font-semibold text-sm">{{ session('error') }}</span>
    </div>
@endif

@if(isset($errors) && $errors->any())
    <div class="rounded-xl border-2 border-rose-200 bg-rose-50 text-rose-800 px-5 py-4 text-sm">
        <p class="font-semibold mb-1 flex items-center gap-2"><i class="fas fa-exclamation-circle"></i> يوجد أخطاء:</p>
        <ul class="list-disc list-inside space-y-0.5 mr-1">
            @foreach($errors->all() as $e)
                <li>{{ $e }}</li>
            @endforeach
        </ul>
    </div>
@endif
