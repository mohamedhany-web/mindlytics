@extends('layouts.admin')

@section('title', $workshop->title)
@section('header', 'تفاصيل الورشة')

@section('content')
@php
    $stats = $stats ?? ['total' => 0, 'converted' => 0, 'pending_leads' => 0, 'checked_in' => 0, 'email_pending' => 0];
    $leadFilter = $leadFilter ?? 'all';
    $registeredCount = $stats['total'];
    $total = $workshop->max_seats ?: null;
    $remaining = $workshop->remaining_seats;
@endphp

<div class="space-y-5">
    @if(session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-900 font-medium flex items-center gap-2">
            <i class="fas fa-check-circle"></i><span>{{ session('success') }}</span>
        </div>
    @endif
    @if(session('error'))
        <div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-900 font-medium flex items-center gap-2">
            <i class="fas fa-exclamation-circle"></i><span>{{ session('error') }}</span>
        </div>
    @endif

    @php $transferSummary = session('workshop_transfer_summary'); @endphp
    @if(is_array($transferSummary) && (!empty($transferSummary['new']) || !empty($transferSummary['existing']) || !empty($transferSummary['already'])))
        <div class="rounded-xl border border-blue-200 bg-blue-50/50 px-4 py-3 text-sm text-blue-900">
            <strong>آخر ترحيل:</strong>
            {{ count($transferSummary['new'] ?? []) }} جدد،
            {{ count($transferSummary['existing'] ?? []) }} مربوطون،
            {{ count($transferSummary['already'] ?? []) }} متخطّى.
        </div>
    @endif

    {{-- Header --}}
    <section class="rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-5 py-5 sm:px-6 flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between border-b border-slate-100">
            <div class="space-y-2">
                <div class="flex flex-wrap items-center gap-2">
                    <h1 class="text-xl sm:text-2xl font-black text-slate-900">{{ $workshop->title }}</h1>
                    @if($workshop->is_active)
                        <span class="text-[10px] px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 font-bold">نشطة</span>
                    @else
                        <span class="text-[10px] px-2 py-0.5 rounded-full bg-slate-100 text-slate-600 font-bold">متوقفة</span>
                    @endif
                </div>
                <div class="flex flex-wrap gap-x-4 gap-y-1 text-xs text-slate-600">
                    @if($workshop->starts_at)
                        <span><i class="far fa-calendar ml-1"></i>{{ $workshop->starts_at->format('Y-m-d H:i') }}</span>
                    @endif
                    @if($total)
                        <span><i class="fas fa-chair ml-1"></i>{{ $registeredCount }}/{{ $total }} مقعد</span>
                    @endif
                    <span><i class="fas fa-users ml-1"></i>{{ $stats['total'] }} مسجّل</span>
                    <span class="text-amber-700"><i class="fas fa-hourglass-half ml-1"></i>{{ $stats['pending_leads'] }} للترحيل</span>
                    <span class="text-emerald-700"><i class="fas fa-check ml-1"></i>{{ $stats['converted'] }} مُرحّل</span>
                </div>
                <div class="flex flex-wrap gap-3 text-[11px]">
                    <a href="{{ route('public.workshops.show', $workshop->slug) }}" target="_blank" class="text-blue-600 hover:underline">رابط التسجيل</a>
                    <a href="{{ route('public.workshops.confirm.show', $workshop->slug) }}" target="_blank" class="text-indigo-600 hover:underline">رابط التأكيد</a>
                </div>
            </div>
            <div class="flex flex-wrap gap-2 shrink-0">
                <a href="{{ route('admin.workshops.index') }}" class="px-3 py-2 rounded-lg border border-slate-200 text-xs font-semibold text-slate-700 hover:bg-slate-50">
                    <i class="fas fa-arrow-right"></i> القائمة
                </a>
                <a href="{{ route('admin.workshops.edit', $workshop) }}" class="px-3 py-2 rounded-lg bg-amber-500 text-white text-xs font-semibold">تعديل</a>
                <a href="{{ route('admin.workshops.export', $workshop) }}" class="px-3 py-2 rounded-lg bg-emerald-600 text-white text-xs font-semibold">CSV</a>
                <a href="{{ route('admin.workshops.confirmations', $workshop) }}" class="px-3 py-2 rounded-lg bg-violet-600 text-white text-xs font-semibold">الحضور</a>
                <button type="button" @click="$dispatch('open-checkin-modal')" class="px-3 py-2 rounded-lg bg-indigo-600 text-white text-xs font-semibold">QR</button>
            </div>
        </div>

        {{-- إرسال واتساب --}}
        <div class="p-5 sm:p-6 border-b border-slate-100">
            @include('admin.workshops._workshop_messaging', ['workshop' => $workshop])
        </div>

        {{-- ترحيل للمبيعات --}}
        <details class="group border-b border-slate-100">
            <summary class="px-5 py-4 cursor-pointer hover:bg-slate-50 flex items-center justify-between gap-2 list-none">
                <span class="text-sm font-bold text-slate-900">
                    <i class="fas fa-right-left text-blue-600 ml-2"></i>
                    ترحيل للمبيعات
                    @if($stats['pending_leads'] > 0)
                        <span class="text-xs font-normal text-amber-700">({{ $stats['pending_leads'] }} جاهز)</span>
                    @endif
                </span>
                <i class="fas fa-chevron-down text-slate-400 group-open:rotate-180 transition-transform"></i>
            </summary>
            <div class="px-5 pb-5 pt-0">
                <form id="convert-to-leads-form" action="{{ route('admin.workshops.convert-to-leads', $workshop) }}" method="POST"
                      class="max-w-2xl space-y-3 rounded-xl border border-blue-100 bg-blue-50/30 p-4"
                      data-pending="{{ $stats['pending_leads'] }}">
                    @csrf
                    <p class="text-xs text-slate-600">يُرحَّل المسجّلون الجدد فقط — المُرحَّلون سابقاً لا يُعاد توزيعهم.</p>
                    <div>
                        <p class="text-xs font-bold text-slate-700 mb-2">موظفو المبيعات</p>
                        <div class="flex gap-2 mb-2 text-[11px]">
                            <button type="button" id="select-all-reps" class="text-blue-700 font-bold hover:underline">تحديد الكل</button>
                            <button type="button" id="clear-all-reps" class="text-slate-500 font-bold hover:underline">إلغاء</button>
                        </div>
                        <div id="convert-assigned-to-list" class="max-h-28 overflow-y-auto grid sm:grid-cols-2 gap-1 rounded-lg border border-slate-200 p-2 bg-white">
                            @foreach(($salesReps ?? collect()) as $rep)
                                <label class="flex items-center gap-2 text-xs cursor-pointer px-1 py-0.5">
                                    <input type="checkbox" name="assigned_to[]" value="{{ $rep->id }}" class="convert-rep-checkbox rounded text-blue-600">
                                    {{ $rep->name }}
                                </label>
                            @endforeach
                        </div>
                    </div>
                    <select name="sales_lead_group_id" id="convert-lead-group" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-xs bg-white">
                        <option value="">بدون مجموعة</option>
                    </select>
                    <button type="submit" @disabled($stats['pending_leads'] === 0)
                            class="w-full py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 disabled:opacity-50 text-white text-sm font-bold">
                        ترحيل الجدد ({{ $stats['pending_leads'] }})
                    </button>
                </form>
            </div>
        </details>

        {{-- جدول المسجلين --}}
        <div class="p-5 sm:p-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
                <h2 class="text-base font-black text-slate-900">
                    المسجّلون
                    <span class="text-sm font-normal text-slate-500">({{ $registrations->total() }})</span>
                </h2>
                <form method="GET" action="{{ route('admin.workshops.show', $workshop) }}" class="flex flex-wrap items-center gap-2">
                    <select name="lead_status" class="rounded-lg border border-slate-200 px-2 py-1.5 text-xs">
                        <option value="all" @selected($leadFilter === 'all')>كل الترحيل</option>
                        <option value="pending" @selected($leadFilter === 'pending')>انتظار الترحيل</option>
                        <option value="converted" @selected($leadFilter === 'converted')>مُرحّل</option>
                    </select>
                    <select name="attendance_mode" class="rounded-lg border border-slate-200 px-2 py-1.5 text-xs">
                        <option value="all" @selected(($filterMode ?? 'all') === 'all')>كل الحضور</option>
                        <option value="online" @selected(($filterMode ?? '') === 'online')>أونلاين</option>
                        <option value="offline" @selected(($filterMode ?? '') === 'offline')>حضوري</option>
                    </select>
                    <button type="submit" class="px-3 py-1.5 rounded-lg bg-slate-800 text-white text-xs font-bold">فلترة</button>
                </form>
            </div>

            <div class="overflow-x-auto rounded-xl border border-slate-200">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-3 py-3 text-right text-xs font-bold text-slate-500">#</th>
                            <th class="px-3 py-3 text-right text-xs font-bold text-slate-500">الاسم</th>
                            <th class="px-3 py-3 text-right text-xs font-bold text-slate-500">التواصل</th>
                            <th class="px-3 py-3 text-right text-xs font-bold text-slate-500">الترحيل</th>
                            <th class="px-3 py-3 text-right text-xs font-bold text-slate-500">الحضور</th>
                            <th class="px-3 py-3 text-right text-xs font-bold text-slate-500">التسجيل</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        @forelse($registrations as $reg)
                            @php $converted = $reg->isConvertedToLead(); @endphp
                            <tr class="{{ $converted ? 'bg-emerald-50/30' : 'hover:bg-slate-50/80' }}">
                                <td class="px-3 py-3 text-xs text-slate-500">{{ $reg->id }}</td>
                                <td class="px-3 py-3 font-semibold text-slate-900">{{ $reg->name }}</td>
                                <td class="px-3 py-3 text-xs text-slate-700">
                                    @if($reg->email)<div>{{ $reg->email }}</div>@endif
                                    @if($reg->phone)<div dir="ltr" class="text-left font-mono">{{ $reg->phone }}</div>@endif
                                    @if($reg->whatsapp_link_sent_at)
                                        <span class="text-[10px] text-green-700"><i class="fab fa-whatsapp"></i> {{ $reg->whatsapp_link_sent_at->format('m-d H:i') }}</span>
                                    @endif
                                </td>
                                <td class="px-3 py-3 text-xs">
                                    @if($converted)
                                        <span class="px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 text-[10px] font-bold">مُرحّل</span>
                                        @if($reg->salesLead)
                                            <a href="{{ route('admin.sales.leads.show', $reg->salesLead) }}" class="block text-[10px] text-blue-600 mt-1">Lead</a>
                                        @endif
                                    @else
                                        <span class="px-2 py-0.5 rounded-full bg-amber-100 text-amber-800 text-[10px] font-bold">جديد</span>
                                    @endif
                                </td>
                                <td class="px-3 py-3 text-xs">
                                    @php $mode = $reg->attendance_mode === 'offline' ? 'حضوري' : ($reg->attendance_mode === 'online' ? 'أونلاين' : '—'); @endphp
                                    {{ $mode }}
                                    @if($reg->checked_in_at)
                                        <div class="text-[10px] text-emerald-600"><i class="fas fa-check"></i> {{ $reg->checked_in_at->format('m-d H:i') }}</div>
                                    @endif
                                </td>
                                <td class="px-3 py-3 text-xs text-slate-500 whitespace-nowrap">{{ optional($reg->created_at)->format('Y-m-d H:i') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-slate-500">لا توجد تسجيلات.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($registrations->hasPages())
                <div class="mt-4">{{ $registrations->links() }}</div>
            @endif
        </div>
    </section>
</div>

{{-- مودال QR --}}
<div x-data="{ open: false, result: '', resultType: 'info' }"
     x-on:open-checkin-modal.window="open = true; result=''; resultType='info';"
     x-cloak x-show="open"
     class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/70 backdrop-blur-sm p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-xl max-h-[90vh] flex flex-col overflow-hidden">
        <div class="flex items-center justify-between px-5 py-3 border-b border-slate-200 bg-slate-50">
            <h3 class="text-sm font-bold text-slate-900"><i class="fas fa-qrcode text-indigo-600 ml-1"></i> التأكد من الحضور</h3>
            <button type="button" @click="open=false" class="p-2 rounded-xl text-slate-500 hover:bg-slate-200"><i class="fas fa-times"></i></button>
        </div>
        <div class="p-4 space-y-3">
            <div id="qr-reader" class="border border-slate-200 rounded-xl overflow-hidden"></div>
            <template x-if="result">
                <div class="text-xs px-3 py-2 rounded-xl" :class="resultType==='success' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-rose-50 text-rose-700 border border-rose-200'">
                    <span x-text="result"></span>
                </div>
            </template>
        </div>
    </div>
</div>

@push('scripts')
<script>
(function () {
    const allGroups = @json($salesLeadGroups ?? []);
    const groupSelect = document.getElementById('convert-lead-group');
    const repCheckboxes = document.querySelectorAll('.convert-rep-checkbox');
    const form = document.getElementById('convert-to-leads-form');

    function selectedRepIds() {
        return Array.from(repCheckboxes).filter(cb => cb.checked).map(cb => Number(cb.value));
    }

    function refreshLeadGroups() {
        if (!groupSelect) return;
        const repIds = selectedRepIds();
        groupSelect.innerHTML = '<option value="">بدون مجموعة</option>';
        if (repIds.length === 0) return;
        allGroups.forEach(function (g) {
            const members = (g.member_ids || []).map(Number);
            if (!repIds.every(id => members.includes(id))) return;
            const opt = document.createElement('option');
            opt.value = g.id;
            opt.textContent = g.name + (g.is_admin_managed ? ' (إدارة)' : '');
            groupSelect.appendChild(opt);
        });
    }

    repCheckboxes.forEach(cb => cb.addEventListener('change', refreshLeadGroups));
    document.getElementById('select-all-reps')?.addEventListener('click', () => {
        repCheckboxes.forEach(cb => { cb.checked = true; });
        refreshLeadGroups();
    });
    document.getElementById('clear-all-reps')?.addEventListener('click', () => {
        repCheckboxes.forEach(cb => { cb.checked = false; });
        refreshLeadGroups();
    });
    refreshLeadGroups();

    form?.addEventListener('submit', function (e) {
        if (selectedRepIds().length === 0) {
            e.preventDefault();
            alert('اختر موظف مبيعات واحد على الأقل.');
            return;
        }
        const pending = Number(form.dataset.pending || 0);
        if (pending === 0) {
            e.preventDefault();
            alert('لا يوجد مسجّلون جدد للترحيل.');
            return;
        }
        if (!confirm('ترحيل ' + pending + ' مسجّل جديد؟')) e.preventDefault();
    });
})();
</script>
<script src="https://unpkg.com/html5-qrcode@2.3.10/html5-qrcode.min.js"></script>
<script>
document.addEventListener('alpine:init', () => {
    window.addEventListener('open-checkin-modal', () => {
        setTimeout(() => {
            const elementId = 'qr-reader';
            if (!document.getElementById(elementId)) return;
            if (window.__qrScanner) {
                try { window.__qrScanner.stop().then(() => window.__qrScanner.clear()); } catch(e) {}
            }
            const qrScanner = new Html5Qrcode(elementId);
            window.__qrScanner = qrScanner;
            qrScanner.start({ facingMode: 'environment' }, { fps: 10, qrbox: 220 },
                async (decodedText) => {
                    try {
                        const res = await fetch("{{ route('admin.workshops.checkin', $workshop) }}", {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                            body: JSON.stringify({ token: decodedText }),
                        });
                        const data = await res.json();
                        const modal = document.querySelector('[x-on\\:open-checkin-modal]');
                        if (modal?.__x) {
                            modal.__x.$data.resultType = data.status || 'error';
                            modal.__x.$data.result = data.message || 'تمت المعالجة.';
                        }
                    } catch (e) { console.error(e); }
                }, () => {}
            ).catch(err => console.error(err));
        }, 150);
    });
});
</script>
@endpush
@endsection
