@if(session('success'))
    <div class="rounded-2xl bg-emerald-50 border border-emerald-200 px-5 py-4 text-emerald-800 text-sm font-medium flex items-center gap-2">
        <i class="fas fa-check-circle text-emerald-600"></i>
        <span>{{ session('success') }}</span>
    </div>
@endif
@if(session('error'))
    <div class="rounded-2xl bg-rose-50 border border-rose-200 px-5 py-4 text-rose-800 text-sm font-medium flex items-center gap-2">
        <i class="fas fa-exclamation-circle text-rose-600"></i>
        <span>{{ session('error') }}</span>
    </div>
@endif
@if(isset($errors) && $errors->any())
    <div class="rounded-2xl border border-rose-200 bg-rose-50 text-rose-800 px-5 py-4 text-sm">
        <p class="font-semibold mb-1 flex items-center gap-2"><i class="fas fa-exclamation-circle"></i> يوجد أخطاء:</p>
        <ul class="list-disc list-inside space-y-0.5 mr-1">
            @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
        </ul>
    </div>
@endif
