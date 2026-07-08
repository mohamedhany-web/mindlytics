@extends('layouts.admin')

@section('title', 'متابعة دفعة واتساب #' . $batch->id . ' - Mindlytics')
@section('header', 'قسم الواتساب')

@section('content')
<div class="p-3 sm:p-4 md:p-6 space-y-4 sm:space-y-6" style="background: #f8fafc; min-height: 100vh;">
    @include('admin.whatsapp._alerts')

    @php
        $failedItemsCount = (int) $batch->items()->where('status', 'failed')->count();
        $pendingItemsCount = $batch->pendingCount();
        $autoDriveBatch = ! $batch->isFinished() || $pendingItemsCount > 0;
        $backUrl = $workshop
            ? route('admin.workshops.show', $workshop)
            : ($salesGroup ?? null
                ? route('admin.sales.groups.show', $salesGroup)
                : route('admin.whatsapp.batches.index'));
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

    @if(($batch->isPausedForBridge() || !($connectionMeta['can_send'] ?? false)) && !$batch->isFinished())
        <div id="connection-blocked-banner" class="rounded-xl border-2 border-rose-300 bg-rose-50 p-4 sm:p-5 space-y-3">
            <div class="flex flex-wrap items-start gap-3">
                <div class="w-10 h-10 rounded-xl bg-rose-100 flex items-center justify-center shrink-0">
                    <i class="fab fa-meta text-rose-600 text-xl"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <h3 class="font-bold text-rose-900">WhatsApp Business غير جاهز — لن تُرسل أي رسالة</h3>
                    <p class="text-sm text-rose-800 mt-1">
                        الحالة: <strong>{{ $connectionMeta['label'] ?? 'غير متصل' }}</strong>
                    </p>
                    @if($batch->meta['connection_blocked_reason'] ?? $batch->meta['bridge_blocked_reason'] ?? null)
                        <p class="text-xs text-rose-700 mt-1">{{ $batch->meta['connection_blocked_reason'] ?? $batch->meta['bridge_blocked_reason'] }}</p>
                    @endif
                    <p class="text-xs text-rose-700 mt-2">بعد إكمال الربط من صفحة الإعدادات، ستُستأنف الدفعة تلقائياً.</p>
                </div>
            </div>
            <a href="{{ route('admin.whatsapp.settings') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold">
                <i class="fas fa-plug"></i>
                إعدادات ربط Meta
            </a>
        </div>
    @endif

    {{-- Progress --}}
    <section class="{{ $waSectionClass }}" id="batch-progress-panel">
        <div class="p-5 space-y-4">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div class="flex items-center gap-2">
                    <span id="batch-status-badge" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold border
                        @if($batch->status === 'processing') bg-sky-100 text-sky-800 border-sky-200
                        @elseif($batch->status === 'completed') bg-emerald-100 text-emerald-800 border-emerald-200
                        @elseif($batch->status === 'paused') bg-rose-100 text-rose-800 border-rose-200
                        @elseif($batch->status === 'cancelled') bg-slate-200 text-slate-800 border-slate-300
                        @else bg-amber-100 text-amber-800 border-amber-200 @endif">
                        <i id="batch-status-icon" class="fas {{ $batch->status === 'processing' ? 'fa-spinner fa-spin' : 'fa-info-circle' }}"></i>
                        <span id="batch-status-label">{{ $batch->statusLabel() }}</span>
                    </span>
                    @if(!$batch->isFinished())
                        <span class="text-xs text-slate-500">الإرسال يعمل تلقائياً — تُعالَج الدفعة من هذه الصفحة ومن طابور whatsapp كل دقيقة</span>
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
            @if(!$batch->isFinished() && $pendingItemsCount > 0)
                <div class="flex flex-wrap items-center gap-3 px-5 pb-5">
                    <form method="POST" action="{{ route('admin.whatsapp.batches.cancel', $batch) }}"
                          onsubmit="return confirm('إيقاف الإرسال؟\n\nلن تُرسل الرسائل المتبقية ({{ $pendingItemsCount }}). الرسائل التي أُرسلت بالفعل تبقى كما هي.');">
                        @csrf
                        <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold bg-rose-600 hover:bg-rose-700 text-white">
                            <i class="fas fa-stop"></i>
                            إيقاف الإرسال
                        </button>
                    </form>
                    @if($failedItemsCount > 0)
                        <form method="POST" action="{{ route('admin.whatsapp.batches.retry', $batch) }}">
                            @csrf
                            <button type="submit" class="{{ $waBtnPrimary }} !text-sm">
                                <i class="fas fa-redo"></i>
                                إعادة إرسال الفاشلة فقط ({{ $failedItemsCount }})
                            </button>
                        </form>
                    @endif
                    <form method="POST" action="{{ route('admin.whatsapp.batches.retry', $batch) }}">
                        @csrf
                        <button type="submit" class="{{ $waBtnSecondary }} !text-sm">
                            <i class="fas fa-play"></i>
                            متابعة المعلّقة ({{ $pendingItemsCount }})
                        </button>
                    </form>
                    <p class="text-[11px] text-slate-500 w-full">الرسائل المرسلة بنجاح لا تُعاد. إذا بقيت «في الانتظار» أكثر من دقيقة، جرّب «متابعة المعلّقة».</p>
                </div>
            @elseif($failedItemsCount > 0 && $batch->status !== 'cancelled')
                <div class="flex flex-wrap items-center gap-3 px-5 pb-5 rounded-xl bg-rose-50 border border-rose-200">
                    <p class="text-sm text-rose-800 font-semibold w-full sm:w-auto">
                        <i class="fas fa-exclamation-circle"></i>
                        {{ $failedItemsCount }} رسالة فشل إرسالها — يمكنك إعادة إرسالها فقط دون المساس بالرسائل الناجحة.
                    </p>
                    <form method="POST" action="{{ route('admin.whatsapp.batches.retry', $batch) }}">
                        @csrf
                        <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold bg-rose-600 hover:bg-rose-700 text-white">
                            <i class="fas fa-redo"></i>
                            إعادة إرسال كل الفاشلة ({{ $failedItemsCount }})
                        </button>
                    </form>
                    <p class="text-[11px] text-rose-700/80 w-full">أو أعد إرسال رسالة واحدة من عمود «إجراء» في الجدول أدناه.</p>
                </div>
            @endif
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
                        <th class="px-4 py-3 text-right text-xs font-semibold text-slate-500">إجراء</th>
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
                            <td class="px-4 py-3 text-xs">
                                @if($item->status === 'failed' && $batch->status !== 'cancelled')
                                    <form method="POST" action="{{ route('admin.whatsapp.batches.items.retry', [$batch, $item]) }}" class="inline">
                                        @csrf
                                        <button type="submit" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-[11px] font-semibold bg-rose-600 hover:bg-rose-700 text-white"
                                                title="إعادة إرسال هذه الرسالة فقط">
                                            <i class="fas fa-redo text-[10px]"></i>
                                            إعادة الإرسال
                                        </button>
                                    </form>
                                @else
                                    <span class="text-slate-300">—</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-slate-500">لا توجد عناصر.</td>
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

@if($autoDriveBatch)
<script>
(function () {
    const statusUrl = @json(route('admin.whatsapp.batches.status', $batch));
    const processUrl = @json(route('admin.whatsapp.batches.process', $batch));
    const csrf = @json(csrf_token());
    let pollTimer = null;
    let processing = false;

    function badgeHtml(status, label) {
        const map = {
            sent: 'bg-emerald-100 text-emerald-800 border-emerald-200',
            failed: 'bg-rose-100 text-rose-800 border-rose-200',
            pending: 'bg-amber-100 text-amber-800 border-amber-200',
        };
        const cls = map[status] || 'bg-slate-100 text-slate-700 border-slate-200';
        return '<span class="inline-flex px-2.5 py-1 rounded-full text-xs font-bold border ' + cls + '">' + label + '</span>';
    }

    function applyStatus(data) {
        document.getElementById('stat-sent').textContent = data.sent;
        document.getElementById('stat-failed').textContent = data.failed;
        document.getElementById('stat-pending').textContent = data.pending;
        document.getElementById('batch-progress-text').textContent = data.progress;
        document.getElementById('batch-progress-bar').style.width = data.progress + '%';
        document.getElementById('batch-status-label').textContent = data.status_label;

        const banner = document.getElementById('connection-blocked-banner');
        if (banner && data.bridge && data.bridge.can_send && !data.paused_for_bridge) {
            banner.remove();
        }

        if (data.finished) {
            clearInterval(pollTimer);
            document.getElementById('batch-status-icon').className = 'fas fa-check-circle';
            location.reload();
        }
    }

    async function poll() {
        try {
            const res = await fetch(statusUrl, { headers: { 'Accept': 'application/json' } });
            if (!res.ok) return;
            applyStatus(await res.json());
        } catch (e) {}
    }

    async function driveProcess() {
        if (processing) return;
        processing = true;
        try {
            const res = await fetch(processUrl, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrf,
                    'X-Requested-With': 'XMLHttpRequest',
                },
            });
            if (res.ok) {
                const data = await res.json();
                if (data.finished !== undefined) {
                    applyStatus(data);
                }
            }
        } catch (e) {
            /* fallback to cron queue */
        } finally {
            processing = false;
        }
    }

    pollTimer = setInterval(poll, 4000);
    setInterval(driveProcess, 12000);
    poll();
    driveProcess();
})();
</script>
@endif
@endsection
