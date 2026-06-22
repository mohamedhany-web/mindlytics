@extends('layouts.admin')

@section('title', 'متابعة دفعة واتساب #' . $batch->id . ' - Mindlytics')
@section('header', 'قسم الواتساب')

@section('content')
<div class="p-3 sm:p-4 md:p-6 space-y-4 sm:space-y-6" style="background: #f8fafc; min-height: 100vh;">
    @include('admin.whatsapp._alerts')
    @include('admin.whatsapp._nav', ['active' => 'batches'])

    @php
        $backUrl = $workshop
            ? route('admin.workshops.show', $workshop)
            : route('admin.whatsapp.batches.index');
    @endphp

    @include('admin.whatsapp._page-header', [
        'title' => $batch->title ?: ('دفعة #' . $batch->id),
        'subtitle' => 'متابعة حية — يتحدّث تلقائياً كل 3 ثوانٍ أثناء الإرسال.',
        'icon' => 'fas fa-tasks',
        'actions' => '<a href="' . $backUrl . '" class="' . $waBtnSecondary . '"><i class="fas fa-arrow-right"></i> رجوع</a>',
        'statCards' => [
            ['label' => 'إجمالي', 'value' => $batch->total_count, 'icon' => 'fas fa-users', 'bg' => 'bg-slate-100', 'text' => 'text-slate-600'],
            ['label' => 'تم الإرسال', 'value' => $batch->sent_count, 'icon' => 'fas fa-check-circle', 'bg' => 'bg-emerald-100', 'text' => 'text-emerald-600'],
            ['label' => 'فشل', 'value' => $batch->failed_count, 'icon' => 'fas fa-times-circle', 'bg' => 'bg-rose-100', 'text' => 'text-rose-600'],
            ['label' => 'متبقي', 'value' => $batch->pendingCount(), 'icon' => 'fas fa-clock', 'bg' => 'bg-amber-100', 'text' => 'text-amber-600'],
        ],
    ])

    {{-- Progress --}}
    <section class="{{ $waSectionClass }}" id="batch-progress-panel">
        <div class="p-5 space-y-4">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div class="flex items-center gap-2">
                    <span id="batch-status-badge" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold border
                        @if($batch->status === 'processing') bg-sky-100 text-sky-800 border-sky-200
                        @elseif($batch->status === 'completed') bg-emerald-100 text-emerald-800 border-emerald-200
                        @else bg-amber-100 text-amber-800 border-amber-200 @endif">
                        <i id="batch-status-icon" class="fas {{ $batch->status === 'processing' ? 'fa-spinner fa-spin' : 'fa-info-circle' }}"></i>
                        <span id="batch-status-label">{{ $batch->statusLabel() }}</span>
                    </span>
                    @if(!$batch->isFinished())
                        <span class="text-xs text-slate-500">الإرسال يعمل في الخلفية — يمكنك إغلاق الصفحة</span>
                    @endif
                </div>
                <p class="text-sm font-bold text-slate-700 tabular-nums"><span id="batch-progress-text">{{ $batch->progressPercent() }}</span>%</p>
            </div>
            <div class="h-3 rounded-full bg-slate-200 overflow-hidden">
                <div id="batch-progress-bar" class="h-full bg-gradient-to-r from-emerald-500 to-green-400 transition-all duration-500" style="width: {{ $batch->progressPercent() }}%"></div>
            </div>
            <div class="grid grid-cols-3 gap-3 text-center text-xs">
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
        </div>
    </section>

    {{-- Filters --}}
    <section class="{{ $waSectionClass }}">
        <div class="px-5 py-4 border-b border-slate-200 flex flex-wrap items-center justify-between gap-3">
            <h3 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                <i class="fas fa-list-check text-emerald-600"></i>
                تفاصيل المستلمين
            </h3>
            <div class="flex flex-wrap gap-2">
                @foreach(['all' => 'الكل', 'sent' => 'تم الإرسال', 'failed' => 'فشل', 'pending' => 'في الانتظار'] as $key => $label)
                    <a href="{{ route('admin.whatsapp.batches.show', ['batch' => $batch, 'filter' => $key === 'all' ? null : $key]) }}"
                       class="px-3 py-1.5 rounded-lg text-xs font-semibold border transition-all {{ ($filter ?? 'all') === $key ? 'bg-emerald-600 text-white border-emerald-600' : 'bg-white text-slate-600 border-slate-200 hover:border-emerald-300' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-slate-500">#</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-slate-500">الاسم</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-slate-500">الهاتف</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-slate-500">الحالة</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-slate-500">ملاحظة</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white" id="batch-items-tbody">
                    @forelse($items as $item)
                        <tr class="hover:bg-slate-50/80" data-item-id="{{ $item->id }}">
                            <td class="px-4 py-3 text-sm text-slate-500">{{ $item->sort_order + 1 }}</td>
                            <td class="px-4 py-3 text-sm font-semibold text-slate-900">{{ $item->recipient_name ?: '—' }}</td>
                            <td class="px-4 py-3 text-sm font-mono text-slate-700">{{ $item->phone }}</td>
                            <td class="px-4 py-3 item-status-cell">
                                @include('admin.whatsapp.batches._status-badge', ['status' => $item->status, 'label' => $item->statusLabel()])
                            </td>
                            <td class="px-4 py-3 text-xs text-slate-600 item-error-cell">
                                @if($item->status === 'sent' && $item->sent_at)
                                    <span class="text-emerald-700">{{ $item->sent_at->format('Y-m-d H:i') }}</span>
                                @elseif($item->error_message)
                                    <span class="text-rose-700" title="{{ $item->error_message }}">{{ Str::limit($item->error_message, 60) }}</span>
                                @else
                                    <span class="text-slate-400">—</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-slate-500">لا توجد عناصر.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($items->hasPages())
            <div class="px-5 py-4 border-t border-slate-200">{{ $items->links() }}</div>
        @endif
    </section>
</div>

@if(!$batch->isFinished())
<script>
(function () {
    const statusUrl = @json(route('admin.whatsapp.batches.status', $batch));
    let pollTimer = null;

    function badgeHtml(status, label) {
        const map = {
            sent: 'bg-emerald-100 text-emerald-800 border-emerald-200',
            failed: 'bg-rose-100 text-rose-800 border-rose-200',
            pending: 'bg-amber-100 text-amber-800 border-amber-200',
        };
        const cls = map[status] || 'bg-slate-100 text-slate-700 border-slate-200';
        return '<span class="inline-flex px-2.5 py-1 rounded-full text-xs font-bold border ' + cls + '">' + label + '</span>';
    }

    async function poll() {
        try {
            const res = await fetch(statusUrl, { headers: { 'Accept': 'application/json' } });
            if (!res.ok) return;
            const data = await res.json();

            document.getElementById('stat-sent').textContent = data.sent;
            document.getElementById('stat-failed').textContent = data.failed;
            document.getElementById('stat-pending').textContent = data.pending;
            document.getElementById('batch-progress-text').textContent = data.progress;
            document.getElementById('batch-progress-bar').style.width = data.progress + '%';
            document.getElementById('batch-status-label').textContent = data.status_label;

            if (data.finished) {
                clearInterval(pollTimer);
                document.getElementById('batch-status-icon').className = 'fas fa-check-circle';
                location.reload();
            }
        } catch (e) {}
    }

    pollTimer = setInterval(poll, 3000);
    poll();
})();
</script>
@endif
@endsection
