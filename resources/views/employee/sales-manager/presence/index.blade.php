@extends('layouts.employee')

@section('title', 'مراقبة تواجد الفريق')
@section('header', 'مراقبة تواجد الفريق — صارم')

@section('content')
<div class="space-y-6" id="presence-monitor" data-poll-url="{{ route('employee.sales-manager.presence.poll') }}">
    @if(session('success'))
        <div class="rounded-lg bg-emerald-50 border border-emerald-200 px-4 py-3 text-sm text-emerald-900">{{ session('success') }}</div>
    @endif

    <div class="bg-rose-50 border border-rose-200 rounded-xl p-4 text-sm text-rose-950">
        <p class="font-bold"><i class="fas fa-shield-alt ml-2"></i> نظام رقابة صارم</p>
        <p class="mt-1">يجب على موظف المبيعات إبقاء النظام مفتوحاً ومُسجّل الدخول طوال الدوام. بدون نبضة لمدة {{ (int) config('employee_presence.away_threshold_seconds', 120) / 60 }} د → «بعيد». بدون نبضة {{ (int) config('employee_presence.offline_threshold_seconds', 300) / 60 }} د → مخالفة. انتهاء الجلسة = خروج من النظام.</p>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-5 gap-3" id="presence-stats">
        @foreach([
            ['key' => 'online', 'label' => 'متصل', 'color' => 'emerald'],
            ['key' => 'away', 'label' => 'بعيد', 'color' => 'amber'],
            ['key' => 'offline', 'label' => 'غير متصل', 'color' => 'rose'],
            ['key' => 'not_clocked_in', 'label' => 'لم يحضر', 'color' => 'slate'],
            ['key' => 'completed', 'label' => 'أنهى', 'color' => 'blue'],
        ] as $s)
            <div class="bg-white rounded-xl border p-4">
                <p class="text-xs text-slate-500">{{ $s['label'] }}</p>
                <p class="text-2xl font-bold text-{{ $s['color'] }}-700" data-stat="{{ $s['key'] }}">{{ $stats[$s['key']] ?? 0 }}</p>
            </div>
        @endforeach
    </div>

    <div class="bg-white rounded-xl border overflow-hidden">
        <div class="px-5 py-4 border-b flex justify-between items-center">
            <h2 class="font-bold text-slate-900">حالة الفريق الآن</h2>
            <span class="text-xs text-slate-500" id="presence-updated">آخر تحديث: {{ now()->format('H:i:s') }}</span>
        </div>
        <div class="divide-y" id="presence-board">
            @foreach($board as $member)
                @include('employee.sales-manager.presence._member_row', ['member' => $member])
            @endforeach
        </div>
    </div>

    <div class="bg-white rounded-xl border overflow-hidden">
        <div class="px-5 py-4 border-b flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <h2 class="font-bold text-slate-900">مخالفات الانقطاع — {{ $date->format('Y-m-d') }}</h2>
            <form method="GET" class="flex gap-2">
                <input type="date" name="date" value="{{ $date->format('Y-m-d') }}" class="px-3 py-2 border rounded-lg text-sm">
                <button class="px-3 py-2 bg-slate-800 text-white rounded-lg text-sm">عرض</button>
            </form>
        </div>
        <p class="px-5 py-2 text-xs text-slate-500">إجمالي: {{ $violationStats['total'] }} — مفتوحة الآن: {{ $violationStats['open'] }}</p>
        <table class="min-w-full text-sm">
            <thead class="bg-slate-50"><tr>
                <th class="px-4 py-3 text-right">الموظف</th>
                <th class="px-4 py-3 text-right">بدء الانقطاع</th>
                <th class="px-4 py-3 text-right">المدة</th>
                <th class="px-4 py-3 text-right">السبب</th>
                <th class="px-4 py-3 text-right">الحالة</th>
                <th class="px-4 py-3"></th>
            </tr></thead>
            <tbody class="divide-y">
                @forelse($violations as $v)
                    <tr>
                        <td class="px-4 py-3 font-medium">{{ $v->user->name ?? '—' }}</td>
                        <td class="px-4 py-3">{{ $v->started_at->format('H:i:s') }}</td>
                        <td class="px-4 py-3">{{ $v->ended_at ? gmdate('H:i:s', $v->duration_seconds) : 'جاري...' }}</td>
                        <td class="px-4 py-3">{{ $v->reason === 'session_expired' ? 'جلسة منتهية' : 'بدون نشاط' }}</td>
                        <td class="px-4 py-3">{{ $v->isOpen() ? 'مفتوحة' : 'مغلقة' }}</td>
                        <td class="px-4 py-3">
                            @if(! $v->acknowledged_at)
                                <form method="POST" action="{{ route('employee.sales-manager.presence.acknowledge', $v) }}">
                                    @csrf
                                    <button class="text-xs text-emerald-700 font-semibold">تمت المراجعة</button>
                                </form>
                            @else
                                <span class="text-xs text-slate-400">✓</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-4 py-10 text-center text-slate-500">لا مخالفات في هذا اليوم.</td></tr>
                @endforelse
            </tbody>
        </table>
        @if($violations->hasPages())<div class="px-4 py-3">{{ $violations->links() }}</div>@endif
    </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
    const root = document.getElementById('presence-monitor');
    if (!root) return;
    const pollUrl = root.dataset.pollUrl;
    const colorMap = { emerald: 'text-emerald-700', amber: 'text-amber-700', rose: 'text-rose-700', slate: 'text-slate-700', blue: 'text-blue-700' };

    function renderBoard(board) {
        const container = document.getElementById('presence-board');
        if (!container) return;
        container.innerHTML = board.map(m => {
            const dot = m.status === 'online' ? 'bg-emerald-500 animate-pulse' : (m.status === 'away' ? 'bg-amber-500' : 'bg-rose-500');
            const viol = m.open_violation ? `<span class="text-xs text-rose-700 font-semibold">انقطاع من ${m.open_violation.started_at}</span>` : '';
            return `<div class="px-5 py-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                <div class="flex items-center gap-3">
                    <span class="w-3 h-3 rounded-full ${dot}"></span>
                    <div>
                        <p class="font-semibold text-slate-900">${m.name}</p>
                        <p class="text-xs text-slate-500">${m.status_label}</p>
                    </div>
                </div>
                <div class="text-xs text-slate-600 text-left sm:text-right space-y-1">
                    <p>آخر نشاط: ${m.last_seen_human || '—'}</p>
                    <p>حضور: ${m.clock_in_at || '—'} · جلسة: ${m.session_active ? 'نشطة' : 'منتهية'}</p>
                    <p>مخالفات اليوم: ${m.violations_today} · offline: ${m.offline_minutes_today} د</p>
                    ${viol}
                </div>
            </div>`;
        }).join('');
    }

    setInterval(() => {
        fetch(pollUrl, { headers: { 'Accept': 'application/json' } })
            .then(r => r.json())
            .then(data => {
                if (!data.success) return;
                renderBoard(data.board || []);
                Object.entries(data.stats || {}).forEach(([k, v]) => {
                    const el = document.querySelector(`[data-stat="${k}"]`);
                    if (el) el.textContent = v;
                });
                const upd = document.getElementById('presence-updated');
                if (upd) upd.textContent = 'آخر تحديث: ' + new Date().toLocaleTimeString('ar-EG');
            }).catch(() => {});
    }, 30000);
})();
</script>
@endpush
