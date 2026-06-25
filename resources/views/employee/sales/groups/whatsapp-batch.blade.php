@extends('layouts.employee')

@section('title', 'متابعة واتساب — '.$group->name)
@section('header', 'متابعة إرسال واتساب')

@section('content')
@include('employee.sales.groups._styles')

<div class="space-y-4" id="wa-batch-page"
     data-status-url="{{ route('employee.sales.groups.whatsapp-batches.status', [$group, $batch]) }}">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h2 class="text-lg font-bold text-slate-900">{{ $batch->title ?: ('دفعة #'.$batch->id) }}</h2>
            <p class="text-sm text-slate-500">مجموعة: {{ $group->name }}</p>
        </div>
        <a href="{{ route('employee.sales.groups.show', $group) }}" class="px-4 py-2 text-sm border border-slate-200 rounded-lg">رجوع للمجموعة</a>
    </div>

    @if(session('success'))
        <div class="rounded-lg border border-emerald-200 bg-emerald-50 text-emerald-800 px-4 py-2 text-sm">{{ session('success') }}</div>
    @endif

    <div class="sales-panel p-5 space-y-4">
        <div class="flex flex-wrap items-center justify-between gap-2">
            <span id="batch-status-badge" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold border
                @if($batch->status === 'processing') bg-sky-100 text-sky-800 border-sky-200
                @elseif($batch->status === 'completed') bg-emerald-100 text-emerald-800 border-emerald-200
                @else bg-amber-100 text-amber-800 border-amber-200 @endif">
                <i id="batch-status-icon" class="fas {{ $batch->status === 'processing' ? 'fa-spinner fa-spin' : 'fa-info-circle' }}"></i>
                <span id="batch-status-label">{{ $batch->statusLabel() }}</span>
            </span>
            <span class="text-sm font-bold tabular-nums"><span id="batch-progress-text">{{ $batch->progressPercent() }}</span>%</span>
        </div>
        <div class="h-2.5 rounded-full bg-slate-200 overflow-hidden">
            <div id="batch-progress-bar" class="h-full bg-emerald-500 transition-all duration-500" style="width: {{ $batch->progressPercent() }}%"></div>
        </div>
        <div class="grid grid-cols-3 gap-2 text-center text-xs">
            <div class="rounded-lg bg-emerald-50 border border-emerald-200 py-2">
                <p class="text-emerald-800 font-bold text-lg tabular-nums" id="stat-sent">{{ $batch->sent_count }}</p>
                <p class="text-emerald-700/80">نجح</p>
            </div>
            <div class="rounded-lg bg-rose-50 border border-rose-200 py-2">
                <p class="text-rose-800 font-bold text-lg tabular-nums" id="stat-failed">{{ $batch->failed_count }}</p>
                <p class="text-rose-700/80">فشل</p>
            </div>
            <div class="rounded-lg bg-amber-50 border border-amber-200 py-2">
                <p class="text-amber-800 font-bold text-lg tabular-nums" id="stat-pending">{{ $batch->pendingCount() }}</p>
                <p class="text-amber-700/80">متبقي</p>
            </div>
        </div>
        <p class="text-[11px] text-slate-500">الإرسال يعمل في الخلفية — تُحدَّث الصفحة كل 3 ثوانٍ.</p>
    </div>

    <div class="sales-panel overflow-hidden">
        <div class="px-4 py-3 border-b border-slate-200 font-bold text-sm">تفاصيل المستلمين</div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-xs text-slate-600">
                    <tr>
                        <th class="text-right p-3">الاسم</th>
                        <th class="text-right p-3">الهاتف</th>
                        <th class="text-right p-3">الحالة</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($items as $item)
                        <tr>
                            <td class="p-3">{{ $item->recipient_name }}</td>
                            <td class="p-3 font-mono text-xs dir-ltr text-right">{{ $item->phone }}</td>
                            <td class="p-3">
                                <span class="text-xs font-semibold
                                    @if($item->status === 'sent') text-emerald-700
                                    @elseif($item->status === 'failed') text-rose-700
                                    @else text-amber-700 @endif">
                                    {{ $item->statusLabel() }}
                                </span>
                                @if($item->error_message)
                                    <p class="text-[10px] text-rose-600 mt-0.5">{{ Str::limit($item->error_message, 80) }}</p>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if($items->hasPages())
            <div class="p-3">{{ $items->links() }}</div>
        @endif
    </div>
</div>

@push('scripts')
<script>
(function () {
    const root = document.getElementById('wa-batch-page');
    if (!root) return;
    const url = root.dataset.statusUrl;
    let timer = null;

    async function poll() {
        try {
            const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
            const data = await res.json();
            document.getElementById('batch-progress-text').textContent = data.progress ?? 0;
            document.getElementById('batch-progress-bar').style.width = (data.progress ?? 0) + '%';
            document.getElementById('stat-sent').textContent = data.sent ?? 0;
            document.getElementById('stat-failed').textContent = data.failed ?? 0;
            document.getElementById('stat-pending').textContent = data.pending ?? 0;
            document.getElementById('batch-status-label').textContent = data.status_label ?? '';
            if (data.finished) {
                clearInterval(timer);
                const icon = document.getElementById('batch-status-icon');
                if (icon) icon.className = 'fas fa-check-circle';
            }
        } catch (_) {}
    }

    timer = setInterval(poll, 3000);
    poll();
})();
</script>
@endpush
@endsection
