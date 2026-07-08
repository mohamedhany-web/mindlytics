@extends('layouts.employee')

@section('title', 'طلبات واتساب')
@section('header', 'طلبات واتساب الجديدة')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="rounded-2xl border border-emerald-200 bg-gradient-to-l from-emerald-50 via-white to-teal-50/40 p-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-lg font-black text-slate-900 flex items-center gap-2">
                    <i class="fab fa-whatsapp text-emerald-600"></i>
                    طابور الطلبات
                </h2>
                <p class="text-sm text-slate-600 mt-1 leading-relaxed">
                    محادثات واردة من أرقام غير مسجّلة كـ leads. اضغط «قبول» لتصبح العميل مسنداً إليك وتفتح المحادثة.
                </p>
            </div>
            <div class="shrink-0 text-center sm:text-left">
                <p class="text-3xl font-black text-emerald-700 tabular-nums">{{ $conversations->total() }}</p>
                <p class="text-xs text-slate-500">طلب في الانتظار</p>
            </div>
        </div>
    </div>

    @if(! $queueEnabled)
        <div class="rounded-xl border border-amber-200 bg-amber-50 px-5 py-4 text-sm text-amber-900">
            طابور الانتظار غير مفعّل حالياً. راجع إعدادات الواتساب (<code class="text-xs">WHATSAPP_ASSIGNMENT_STRATEGY=manual_queue</code>).
        </div>
    @endif

    <div class="space-y-3" id="wa-queue-list">
        @forelse($conversations as $conversation)
            <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm hover:border-emerald-200 transition-colors">
                <div class="flex flex-col sm:flex-row sm:items-start gap-4">
                    <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0 text-xl">
                        <i class="fab fa-whatsapp"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex flex-wrap items-center gap-2 mb-1">
                            <h3 class="font-bold text-slate-900">{{ $conversation->displayName() }}</h3>
                            <span class="text-xs text-slate-500 tabular-nums dir-ltr">{{ $conversation->formattedPhone() }}</span>
                        </div>
                        <p class="text-sm text-slate-600 line-clamp-2 mb-2">
                            {{ $conversation->last_message_preview ?: '—' }}
                        </p>
                        <p class="text-xs text-slate-400">
                            @if($conversation->last_message_at)
                                {{ $conversation->last_message_at->diffForHumans() }}
                            @endif
                            @if($conversation->unread_count > 0)
                                · <span class="text-emerald-700 font-semibold">{{ $conversation->unread_count }} غير مقروء</span>
                            @endif
                        </p>
                    </div>
                    <form method="POST" action="{{ route('employee.sales.whatsapp.queue.accept', $conversation) }}" class="shrink-0">
                        @csrf
                        <button type="submit"
                                class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold transition-colors shadow-sm">
                            <i class="fas fa-check"></i>
                            قبول
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="rounded-xl border border-dashed border-slate-200 bg-white px-6 py-16 text-center">
                <div class="w-14 h-14 mx-auto mb-4 rounded-2xl bg-slate-50 flex items-center justify-center">
                    <i class="fas fa-inbox text-2xl text-slate-400"></i>
                </div>
                <p class="font-bold text-slate-900 mb-1">لا توجد طلبات حالياً</p>
                <p class="text-sm text-slate-500">ستظهر هنا المحادثات الواردة من أرقام جديدة</p>
            </div>
        @endforelse
    </div>

    @if($conversations->hasPages())
        <div>{{ $conversations->links() }}</div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    setInterval(() => {
        if (document.hidden) return;
        fetch('{{ route('employee.sales.whatsapp.queue.count') }}', {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        })
            .then(r => r.json())
            .then(data => {
                const badge = document.getElementById('wa-queue-badge');
                if (!badge) return;
                const count = data.count || 0;
                badge.textContent = count;
                badge.classList.toggle('hidden', count === 0);
            })
            .catch(() => {});
    }, 15000);
</script>
@endpush
